<?php
// controllers/QuizApiController.php

class QuizApiController extends Controller {
    
    /**
     * Xác thực API key qua header HTTP_X_API_KEY hoặc tham số truyền lên
     */
    private function validateApiKey($inputData) {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? ($inputData['api_key'] ?? '');
        if (empty($apiKey) || $apiKey !== API_SECRET_KEY) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Xác thực không hợp lệ. API Key không chính xác.'
            ]);
            exit;
        }
    }

    /**
     * Lấy danh sách bài học của một khóa học
     * GET /api/courses/lessons?course_id=XX
     */
    public function getLessons() {
        header('Content-Type: application/json; charset=utf-8');
        
        $this->validateApiKey($_GET);

        $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        if ($courseId <= 0) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Thiếu hoặc sai định dạng course_id.'
            ]);
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            
            // Kiểm tra xem khóa học có tồn tại không
            $stmtCheck = $db->prepare("SELECT id, title FROM courses WHERE id = ?");
            $stmtCheck->execute([$courseId]);
            $course = $stmtCheck->fetch();
            if (!$course) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => "Không tìm thấy khóa học có ID: $courseId"
                ]);
                return;
            }

            // Lấy toàn bộ bài học có phân cấp Part > Chapter > Lesson
            $stmt = $db->prepare("
                SELECT cl.id, cl.title, cc.title as chapter_title, cp.title as part_title
                FROM course_lessons cl
                JOIN course_chapters cc ON cl.chapter_id = cc.id
                JOIN course_parts cp ON cc.part_id = cp.id
                WHERE cp.course_id = ?
                ORDER BY cp.sort_order, cp.id, cc.sort_order, cc.id, cl.sort_order, cl.id
            ");
            $stmt->execute([$courseId]);
            $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'course_title' => $course['title'],
                'lessons' => $lessons
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Lỗi server khi lấy dữ liệu bài học: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Đồng bộ đề trắc nghiệm và câu hỏi vào một bài học cụ thể
     * POST /api/quizzes/create
     */
    public function create() {
        header('Content-Type: application/json; charset=utf-8');

        // Hỗ trợ đọc dữ liệu JSON
        $inputData = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true) ?? [];
        } else {
            $inputData = $_POST;
        }

        $this->validateApiKey($inputData);

        // Lấy và kiểm tra các tham số đầu vào
        $courseId = isset($inputData['course_id']) ? (int)$inputData['course_id'] : 0;
        $lessonId = isset($inputData['lesson_id']) ? (int)$inputData['lesson_id'] : 0;
        $title = isset($inputData['title']) ? trim($inputData['title']) : '';
        $questions = isset($inputData['questions']) ? $inputData['questions'] : [];

        if ($courseId <= 0 || $lessonId <= 0 || empty($title) || empty($questions) || !is_array($questions)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Thiếu hoặc dữ liệu đầu vào không hợp lệ (course_id, lesson_id, title, questions).'
            ]);
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // 1. Kiểm tra bài học có thuộc khóa học không để bảo toàn cấu trúc dữ liệu
            $stmtCheck = $db->prepare("
                SELECT cl.id 
                FROM course_lessons cl
                JOIN course_chapters cc ON cl.chapter_id = cc.id
                JOIN course_parts cp ON cc.part_id = cp.id
                WHERE cl.id = ? AND cp.course_id = ?
            ");
            $stmtCheck->execute([$lessonId, $courseId]);
            if (!$stmtCheck->fetch()) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => "Bài học ID $lessonId không thuộc khóa học ID $courseId."
                ]);
                return;
            }

            // 2. Tìm hoặc tạo AI User tác giả
            $authorUsername = 'N_M_T_AI';
            $authorId = null;
            $stmtUser = $db->prepare("SELECT id FROM users WHERE username = ?");
            $stmtUser->execute([$authorUsername]);
            $user = $stmtUser->fetch();

            if ($user) {
                $authorId = $user['id'];
            } else {
                $randomPassword = bin2hex(random_bytes(8));
                $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
                $email = 'nmt_ai@ntkntk.com';
                $fullName = 'Trợ Lý Viết Bài AI';
                $role = 'admin';

                $stmtCreateUser = $db->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
                $stmtCreateUser->execute([$authorUsername, $hashedPassword, $email, $fullName, $role]);
                $authorId = $db->lastInsertId();
            }

            // 3. Thực thi Database Transaction để đảm bảo tính toàn vẹn tuyệt đối
            $db->beginTransaction();

            // 3.1. Lưu đề thi vào bảng `quizzes`
            $stmtQuiz = $db->prepare("
                INSERT INTO quizzes (lesson_id, title, description, time_limit_minutes, pass_score, max_attempts, shuffle_questions, shuffle_options) 
                VALUES (?, ?, ?, 0, 50.00, 0, 1, 1)
            ");
            $stmtQuiz->execute([$lessonId, $title, 'Đề thi tự động tải lên từ file Word']);
            $quizId = $db->lastInsertId();

            // 3.2. Đưa đề thi vào mục nội dung bài học `lesson_items`
            // Tính toán sort_order cho nội dung mới ở cuối bài
            $stmtOrder = $db->prepare("SELECT COALESCE(MAX(sort_order), 0) FROM lesson_items WHERE lesson_id = ?");
            $stmtOrder->execute([$lessonId]);
            $maxOrder = (int)$stmtOrder->fetchColumn();
            $newOrder = $maxOrder + 1;

            $stmtLessonItem = $db->prepare("
                INSERT INTO lesson_items (lesson_id, type, content, sort_order) 
                VALUES (?, 'quiz', ?, ?)
            ");
            $stmtLessonItem->execute([$lessonId, $quizId, $newOrder]);

            // 3.3. Duyệt và lưu từng câu hỏi
            foreach ($questions as $qIdx => $q) {
                $qText = isset($q['text']) ? trim($q['text']) : '';
                $options = isset($q['options']) ? $q['options'] : [];
                $correctIndex = isset($q['correct_index']) ? (int)$q['correct_index'] : -1;

                if (empty($qText) || empty($options) || $correctIndex < 0 || $correctIndex >= count($options)) {
                    // Nếu một câu hỏi bị lỗi cấu trúc, rollback toàn bộ
                    throw new Exception("Lỗi dữ liệu câu hỏi số " . ($qIdx + 1) . " (thiếu nội dung, đáp án hoặc phương án lựa chọn).");
                }

                // 3.3.1. Ghi câu hỏi vào ngân hàng câu hỏi `question_bank`
                $stmtQB = $db->prepare("
                    INSERT INTO question_bank (course_id, question_text, question_type, created_by) 
                    VALUES (?, ?, 'single', ?)
                ");
                $stmtQB->execute([$courseId, $qText, $authorId]);
                $bankQuestionId = $db->lastInsertId();

                // 3.3.2. Ghi các lựa chọn đáp án vào `question_bank_options`
                foreach ($options as $oIdx => $oText) {
                    $isCorrect = ($oIdx === $correctIndex) ? 1 : 0;
                    $stmtOpt = $db->prepare("
                        INSERT INTO question_bank_options (question_id, option_text, is_correct, sort_order) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmtOpt->execute([$bankQuestionId, trim($oText), $isCorrect, $oIdx]);
                }

                // 3.3.3. Ánh xạ câu hỏi ngân hàng vào đề thi `quiz_questions`
                $stmtQQ = $db->prepare("
                    INSERT INTO quiz_questions (quiz_id, bank_question_id, sort_order) 
                    VALUES (?, ?, ?)
                ");
                $stmtQQ->execute([$quizId, $bankQuestionId, $qIdx]);
            }

            // Cam kết tất cả ghi vào DB
            $db->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'Đề trắc nghiệm cùng bộ câu hỏi đã được đồng bộ thành công!',
                'quiz_id' => $quizId,
                'title' => $title,
                'question_count' => count($questions)
            ]);

        } catch (Exception $e) {
            // Rollback ngay lập tức nếu xảy ra bất kỳ lỗi gì
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Gặp lỗi trong quá trình ghi cơ sở dữ liệu: ' . $e->getMessage()
            ]);
        }
    }
}
