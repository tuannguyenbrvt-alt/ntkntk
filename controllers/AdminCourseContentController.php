<?php
class AdminCourseContentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function storePart() {
        $course_id = $_POST['course_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO course_parts (course_id, title) VALUES (?, ?)");
        $stmt->execute([$course_id, $title]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function updatePart() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        if ($title) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE course_parts SET title = ? WHERE id = ?");
            $stmt->execute([$title, $id]);
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function deletePart() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM course_parts WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function storeChapter() {
        $part_id = $_POST['part_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO course_chapters (part_id, title) VALUES (?, ?)");
        $stmt->execute([$part_id, $title]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function updateChapter() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        if ($title) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE course_chapters SET title = ? WHERE id = ?");
            $stmt->execute([$title, $id]);
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function deleteChapter() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM course_chapters WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function storeLesson() {
        $chapter_id = $_POST['chapter_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $is_free_preview = isset($_POST['is_free_preview']) ? 1 : 0;
        $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO course_lessons (chapter_id, title, is_free_preview, allow_comments) VALUES (?, ?, ?, ?)");
        $stmt->execute([$chapter_id, $title, $is_free_preview, $allow_comments]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function updateLesson() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $is_free_preview = isset($_POST['is_free_preview']) ? 1 : 0;
        $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;
        if ($title) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE course_lessons SET title = ?, is_free_preview = ?, allow_comments = ? WHERE id = ?");
            $stmt->execute([$title, $is_free_preview, $allow_comments, $id]);
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function deleteLesson() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM course_lessons WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function storeItem() {
        $lesson_id = $_POST['lesson_id'] ?? $_POST['pdf_lesson_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $type      = $_POST['type']      ?? 'text';
        $content   = '';

        if ($type === 'pdf') {
            if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] === UPLOAD_ERR_NO_FILE) {
                $_SESSION['error'] = 'Vui long chon file PDF.';
                $this->redirect('/admin/courses/builder?id=' . $course_id);
                return;
            }
            $file = $_FILES['pdf_file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Loi upload file. Ma loi: ' . $file['error'];
                $this->redirect('/admin/courses/builder?id=' . $course_id);
                return;
            }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $_SESSION['error'] = 'Chi chap nhan file PDF.';
                $this->redirect('/admin/courses/builder?id=' . $course_id);
                return;
            }
            $uploadDir = ROOT_PATH . '/uploads/course_pdfs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = uniqid() . '-' . time() . '.pdf';
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $content = 'uploads/course_pdfs/' . $filename;
            } else {
                $_SESSION['error'] = 'Loi luu file PDF len may chu.';
                $this->redirect('/admin/courses/builder?id=' . $course_id);
                return;
            }
        } else {
            $content = $_POST['content'] ?? '';
        }

        $db   = Database::getInstance()->getConnection();
        
        $orderQuery = $db->prepare("SELECT IFNULL(MAX(sort_order), -1) + 1 FROM lesson_items WHERE lesson_id = ?");
        $orderQuery->execute([$lesson_id]);
        $nextOrder = (int)$orderQuery->fetchColumn();
        
        $stmt = $db->prepare("INSERT INTO lesson_items (lesson_id, type, content, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lesson_id, $type, $content, $nextOrder]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function updateItem() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $type      = $_POST['type']      ?? 'text';
        $content   = '';
        
        $db = Database::getInstance()->getConnection();
        
        if ($type === 'pdf') {
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['pdf_file'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if ($ext !== 'pdf') {
                    $_SESSION['error'] = 'Chi chap nhan file PDF.';
                    $this->redirect('/admin/courses/builder?id=' . $course_id);
                    return;
                }
                $uploadDir = ROOT_PATH . '/uploads/course_pdfs/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename = uniqid() . '-' . time() . '.pdf';
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $s = $db->prepare("SELECT content FROM lesson_items WHERE id = ?");
                    $s->execute([$id]);
                    $old = $s->fetchColumn();
                    if ($old && file_exists(ROOT_PATH . '/' . $old)) {
                        @unlink(ROOT_PATH . '/' . $old);
                    }
                    $content = 'uploads/course_pdfs/' . $filename;
                } else {
                    $_SESSION['error'] = 'Loi luu file PDF len may chu.';
                    $this->redirect('/admin/courses/builder?id=' . $course_id);
                    return;
                }
            } else {
                $s = $db->prepare("SELECT content FROM lesson_items WHERE id = ?");
                $s->execute([$id]);
                $content = $s->fetchColumn();
            }
        } else {
            $content = $_POST['content'] ?? '';
        }
        
        $stmt = $db->prepare("UPDATE lesson_items SET content = ? WHERE id = ?");
        $stmt->execute([$content, $id]);
        
        $_SESSION['success'] = 'Cap nhat noi dung thanh cong!';
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function deleteItem() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db        = Database::getInstance()->getConnection();
        $s = $db->prepare("SELECT type, content FROM lesson_items WHERE id = ?");
        $s->execute([$id]);
        $item = $s->fetch();
        if ($item && $item['type'] === 'pdf' && file_exists(ROOT_PATH . '/' . $item['content'])) {
            @unlink(ROOT_PATH . '/' . $item['content']);
        }
        $stmt = $db->prepare("DELETE FROM lesson_items WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function reorderItem() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $direction = $_POST['direction'] ?? '';
        
        $db = Database::getInstance()->getConnection();
        
        $s = $db->prepare("SELECT lesson_id, sort_order FROM lesson_items WHERE id = ?");
        $s->execute([$id]);
        $curr = $s->fetch();
        if (!$curr) {
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        
        $lesson_id = $curr['lesson_id'];
        
        $all = $db->prepare("SELECT id FROM lesson_items WHERE lesson_id = ? ORDER BY sort_order ASC, id ASC");
        $all->execute([$lesson_id]);
        $rows = $all->fetchAll();
        
        $curr_index = -1;
        foreach ($rows as $index => $row) {
            $db->prepare("UPDATE lesson_items SET sort_order = ? WHERE id = ?")->execute([$index, $row['id']]);
            if ($row['id'] == $id) {
                $curr_index = $index;
            }
        }
        
        if ($direction === 'up' && $curr_index > 0) {
            $target_index = $curr_index - 1;
            $target_id = $rows[$target_index]['id'];
            
            $db->prepare("UPDATE lesson_items SET sort_order = ? WHERE id = ?")->execute([$target_index, $id]);
            $db->prepare("UPDATE lesson_items SET sort_order = ? WHERE id = ?")->execute([$curr_index, $target_id]);
        } elseif ($direction === 'down' && $curr_index < count($rows) - 1) {
            $target_index = $curr_index + 1;
            $target_id = $rows[$target_index]['id'];
            
            $db->prepare("UPDATE lesson_items SET sort_order = ? WHERE id = ?")->execute([$target_index, $id]);
            $db->prepare("UPDATE lesson_items SET sort_order = ? WHERE id = ?")->execute([$curr_index, $target_id]);
        }
        
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    // -- ATTACHMENTS --
    public function storeAttachment() {
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;

        if (!isset($_FILES['attachment_file']) || $_FILES['attachment_file']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['error'] = 'Vui long chon file dinh kem.';
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        $file = $_FILES['attachment_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Loi upload. Ma loi: ' . $file['error'];
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        $allowed = ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','txt','mp3','mp4','png','jpg','jpeg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = 'Dinh dang file khong duoc phep.';
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        $uploadDir = ROOT_PATH . '/uploads/attachments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = uniqid() . '-' . time() . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $_SESSION['error'] = 'Loi luu file len may chu.';
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        // Tinh kich thuoc file
        $bytes = $file['size'];
        if ($bytes >= 1048576)      $size = round($bytes/1048576, 1) . ' MB';
        elseif ($bytes >= 1024)     $size = round($bytes/1024, 1) . ' KB';
        else                        $size = $bytes . ' B';

        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO lesson_attachments (lesson_id, name, file_path, file_size) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lesson_id, $file['name'], 'uploads/attachments/' . $filename, $size]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function deleteAttachment() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db        = Database::getInstance()->getConnection();
        $s         = $db->prepare("SELECT file_path FROM lesson_attachments WHERE id = ?");
        $s->execute([$id]);
        $row = $s->fetch();
        if ($row && file_exists(ROOT_PATH . '/' . $row['file_path'])) {
            @unlink(ROOT_PATH . '/' . $row['file_path']);
        }
        $stmt = $db->prepare("DELETE FROM lesson_attachments WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function reorderPart() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $direction = $_POST['direction'] ?? '';
        
        $db = Database::getInstance()->getConnection();
        
        $all = $db->prepare("SELECT id FROM course_parts WHERE course_id = ? ORDER BY sort_order ASC, id ASC");
        $all->execute([$course_id]);
        $rows = $all->fetchAll();
        
        $curr_index = -1;
        foreach ($rows as $index => $row) {
            $db->prepare("UPDATE course_parts SET sort_order = ? WHERE id = ?")->execute([$index, $row['id']]);
            if ($row['id'] == $id) {
                $curr_index = $index;
            }
        }
        
        if ($direction === 'up' && $curr_index > 0) {
            $target_index = $curr_index - 1;
            $target_id = $rows[$target_index]['id'];
            
            $db->prepare("UPDATE course_parts SET sort_order = ? WHERE id = ?")->execute([$target_index, $id]);
            $db->prepare("UPDATE course_parts SET sort_order = ? WHERE id = ?")->execute([$curr_index, $target_id]);
        } elseif ($direction === 'down' && $curr_index < count($rows) - 1) {
            $target_index = $curr_index + 1;
            $target_id = $rows[$target_index]['id'];
            
            $db->prepare("UPDATE course_parts SET sort_order = ? WHERE id = ?")->execute([$target_index, $id]);
            $db->prepare("UPDATE course_parts SET sort_order = ? WHERE id = ?")->execute([$curr_index, $target_id]);
        }
        
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function reorderChapter() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $direction = $_POST['direction'] ?? '';
        
        $db = Database::getInstance()->getConnection();
        
        $s = $db->prepare("SELECT part_id FROM course_chapters WHERE id = ?");
        $s->execute([$id]);
        $part_id = $s->fetchColumn();
        if (!$part_id) {
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        
        $all = $db->prepare("SELECT id FROM course_chapters WHERE part_id = ? ORDER BY sort_order ASC, id ASC");
        $all->execute([$part_id]);
        $rows = $all->fetchAll();
        
        $curr_index = -1;
        foreach ($rows as $index => $row) {
            $db->prepare("UPDATE course_chapters SET sort_order = ? WHERE id = ?")->execute([$index, $row['id']]);
            if ($row['id'] == $id) {
                $curr_index = $index;
            }
        }
        
        if ($direction === 'up' && $curr_index > 0) {
            $target_index = $curr_index - 1;
            $target_id = $rows[$target_index]['id'];
            
            $db->prepare("UPDATE course_chapters SET sort_order = ? WHERE id = ?")->execute([$target_index, $id]);
            $db->prepare("UPDATE course_chapters SET sort_order = ? WHERE id = ?")->execute([$curr_index, $target_id]);
        } elseif ($direction === 'down' && $curr_index < count($rows) - 1) {
            $target_index = $curr_index + 1;
            $target_id = $rows[$target_index]['id'];
            
            $db->prepare("UPDATE course_chapters SET sort_order = ? WHERE id = ?")->execute([$target_index, $id]);
            $db->prepare("UPDATE course_chapters SET sort_order = ? WHERE id = ?")->execute([$curr_index, $target_id]);
        }
        
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function reorderLesson() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $direction = $_POST['direction'] ?? '';
        
        $db = Database::getInstance()->getConnection();
        
        $s = $db->prepare("SELECT chapter_id FROM course_lessons WHERE id = ?");
        $s->execute([$id]);
        $chapter_id = $s->fetchColumn();
        if (!$chapter_id) {
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        
        $all = $db->prepare("SELECT id FROM course_lessons WHERE chapter_id = ? ORDER BY sort_order ASC, id ASC");
        $all->execute([$chapter_id]);
        $rows = $all->fetchAll();
        
        $curr_index = -1;
        foreach ($rows as $index => $row) {
            $db->prepare("UPDATE course_lessons SET sort_order = ? WHERE id = ?")->execute([$index, $row['id']]);
            if ($row['id'] == $id) {
                $curr_index = $index;
            }
        }
        
        if ($direction === 'up' && $curr_index > 0) {
            $target_index = $curr_index - 1;
            $target_id = $rows[$target_index]['id'];
            
            $db->prepare("UPDATE course_lessons SET sort_order = ? WHERE id = ?")->execute([$target_index, $id]);
            $db->prepare("UPDATE course_lessons SET sort_order = ? WHERE id = ?")->execute([$curr_index, $target_id]);
        } elseif ($direction === 'down' && $curr_index < count($rows) - 1) {
            $target_index = $curr_index + 1;
            $target_id = $rows[$target_index]['id'];
            
            $db->prepare("UPDATE course_lessons SET sort_order = ? WHERE id = ?")->execute([$target_index, $id]);
            $db->prepare("UPDATE course_lessons SET sort_order = ? WHERE id = ?")->execute([$curr_index, $target_id]);
        }
        
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    // API Lấy cấu trúc khóa học (Phần -> Chương -> Bài học) dưới dạng JSON
    public function getCourseStructure() {
        header('Content-Type: application/json');
        $course_id = (int)($_GET['course_id'] ?? 0);
        if (!$course_id) {
            echo json_encode(['ok' => false, 'error' => 'Khóa học không hợp lệ']);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Lấy danh sách phần
        $stmtParts = $db->prepare("SELECT id, title FROM course_parts WHERE course_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtParts->execute([$course_id]);
        $parts = $stmtParts->fetchAll();

        foreach ($parts as &$part) {
            // Lấy danh sách chương của phần
            $stmtChapters = $db->prepare("SELECT id, title FROM course_chapters WHERE part_id = ? ORDER BY sort_order ASC, id ASC");
            $stmtChapters->execute([$part['id']]);
            $part['chapters'] = $stmtChapters->fetchAll();

            foreach ($part['chapters'] as &$chapter) {
                // Lấy danh sách bài học của chương
                $stmtLessons = $db->prepare("SELECT id, title FROM course_lessons WHERE chapter_id = ? ORDER BY sort_order ASC, id ASC");
                $stmtLessons->execute([$chapter['id']]);
                $chapter['lessons'] = $stmtLessons->fetchAll();
            }
        }

        echo json_encode([
            'ok' => true,
            'parts' => $parts
        ]);
    }

    // Thực hiện nhân bản bài học và các tài nguyên đi kèm
    private function cloneLesson($db, $old_lesson_id, $new_chapter_id, $new_course_id) {
        $stmt = $db->prepare("SELECT * FROM course_lessons WHERE id = ?");
        $stmt->execute([$old_lesson_id]);
        $oldLesson = $stmt->fetch();
        if (!$oldLesson) return false;

        $orderQuery = $db->prepare("SELECT IFNULL(MAX(sort_order), -1) + 1 FROM course_lessons WHERE chapter_id = ?");
        $orderQuery->execute([$new_chapter_id]);
        $nextOrder = (int)$orderQuery->fetchColumn();

        $stmtInsert = $db->prepare("INSERT INTO course_lessons (chapter_id, title, is_free_preview, allow_comments, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->execute([
            $new_chapter_id,
            $oldLesson['title'],
            $oldLesson['is_free_preview'],
            $oldLesson['allow_comments'] ?? 0,
            $nextOrder
        ]);
        $new_lesson_id = $db->lastInsertId();

        // Sao chép lesson_items
        $stmtItems = $db->prepare("SELECT * FROM lesson_items WHERE lesson_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtItems->execute([$old_lesson_id]);
        $items = $stmtItems->fetchAll();
        foreach ($items as $item) {
            $stmtInsItem = $db->prepare("INSERT INTO lesson_items (lesson_id, type, content, sort_order) VALUES (?, ?, ?, ?)");
            $stmtInsItem->execute([
                $new_lesson_id,
                $item['type'],
                $item['content'],
                $item['sort_order']
            ]);
        }

        // Sao chép lesson_attachments
        $stmtAttach = $db->prepare("SELECT * FROM lesson_attachments WHERE lesson_id = ? ORDER BY id ASC");
        $stmtAttach->execute([$old_lesson_id]);
        $attachments = $stmtAttach->fetchAll();
        foreach ($attachments as $attach) {
            $stmtInsAttach = $db->prepare("INSERT INTO lesson_attachments (lesson_id, name, file_path, file_size) VALUES (?, ?, ?, ?)");
            $stmtInsAttach->execute([
                $new_lesson_id,
                $attach['name'],
                $attach['file_path'],
                $attach['file_size']
            ]);
        }

        // Sao chép quizzes và liên kết ngân hàng câu hỏi độc lập
        $stmtQuizzes = $db->prepare("SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY id ASC");
        $stmtQuizzes->execute([$old_lesson_id]);
        $quizzes = $stmtQuizzes->fetchAll();
        foreach ($quizzes as $quiz) {
            $stmtInsQuiz = $db->prepare("INSERT INTO quizzes (lesson_id, title, description, time_limit_minutes, pass_score, max_attempts, shuffle_questions, shuffle_options) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmtInsQuiz->execute([
                $new_lesson_id,
                $quiz['title'],
                $quiz['description'],
                $quiz['time_limit_minutes'],
                $quiz['pass_score'],
                $quiz['max_attempts'],
                $quiz['shuffle_questions'],
                $quiz['shuffle_options']
            ]);
            $new_quiz_id = $db->lastInsertId();

            // Sao chép câu hỏi trong đề trắc nghiệm
            $stmtQQ = $db->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY sort_order ASC, id ASC");
            $stmtQQ->execute([$quiz['id']]);
            $quizQuestions = $stmtQQ->fetchAll();

            foreach ($quizQuestions as $qq) {
                // Sao chép câu hỏi gốc sang ngân hàng câu hỏi của khóa học đích
                $stmtQB = $db->prepare("SELECT * FROM question_bank WHERE id = ?");
                $stmtQB->execute([$qq['bank_question_id']]);
                $oldQB = $stmtQB->fetch();

                if ($oldQB) {
                    $stmtInsQB = $db->prepare("INSERT INTO question_bank (course_id, question_text, question_type, created_by) VALUES (?, ?, ?, ?)");
                    $stmtInsQB->execute([
                        $new_course_id,
                        $oldQB['question_text'],
                        $oldQB['question_type'],
                        $_SESSION['user_id']
                    ]);
                    $new_bank_question_id = $db->lastInsertId();

                    // Sao chép đáp án câu hỏi
                    $stmtQBO = $db->prepare("SELECT * FROM question_bank_options WHERE question_id = ? ORDER BY sort_order ASC, id ASC");
                    $stmtQBO->execute([$qq['bank_question_id']]);
                    $options = $stmtQBO->fetchAll();
                    foreach ($options as $opt) {
                        $stmtInsQBO = $db->prepare("INSERT INTO question_bank_options (question_id, option_text, is_correct, sort_order) VALUES (?, ?, ?, ?)");
                        $stmtInsQBO->execute([
                            $new_bank_question_id,
                            $opt['option_text'],
                            $opt['is_correct'],
                            $opt['sort_order']
                        ]);
                    }

                    // Đăng ký câu hỏi vào đề trắc nghiệm mới
                    $stmtInsQQ = $db->prepare("INSERT INTO quiz_questions (quiz_id, bank_question_id, sort_order) VALUES (?, ?, ?)");
                    $stmtInsQQ->execute([
                        $new_quiz_id,
                        $new_bank_question_id,
                        $qq['sort_order']
                    ]);
                }
            }
        }

        // Sao chép assignments
        $stmtAsgn = $db->prepare("SELECT * FROM assignments WHERE lesson_id = ? ORDER BY id ASC");
        $stmtAsgn->execute([$old_lesson_id]);
        $assignments = $stmtAsgn->fetchAll();
        foreach ($assignments as $asgn) {
            $stmtInsAsgn = $db->prepare("INSERT INTO assignments (lesson_id, title, description, type, max_score, due_date, drive_folder_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtInsAsgn->execute([
                $new_lesson_id,
                $asgn['title'],
                $asgn['description'],
                $asgn['type'],
                $asgn['max_score'],
                $asgn['due_date'],
                $asgn['drive_folder_id'] ?? null
            ]);
        }

        return $new_lesson_id;
    }

    // Thực hiện nhân bản chương và các bài học trong đó
    private function cloneChapter($db, $old_chapter_id, $new_part_id, $new_course_id) {
        $stmt = $db->prepare("SELECT * FROM course_chapters WHERE id = ?");
        $stmt->execute([$old_chapter_id]);
        $oldChapter = $stmt->fetch();
        if (!$oldChapter) return false;

        $orderQuery = $db->prepare("SELECT IFNULL(MAX(sort_order), -1) + 1 FROM course_chapters WHERE part_id = ?");
        $orderQuery->execute([$new_part_id]);
        $nextOrder = (int)$orderQuery->fetchColumn();

        $stmtInsert = $db->prepare("INSERT INTO course_chapters (part_id, title, sort_order) VALUES (?, ?, ?)");
        $stmtInsert->execute([
            $new_part_id,
            $oldChapter['title'],
            $nextOrder
        ]);
        $new_chapter_id = $db->lastInsertId();

        $stmtLessons = $db->prepare("SELECT id FROM course_lessons WHERE chapter_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtLessons->execute([$old_chapter_id]);
        $lessons = $stmtLessons->fetchAll();
        foreach ($lessons as $lesson) {
            $this->cloneLesson($db, $lesson['id'], $new_chapter_id, $new_course_id);
        }

        return $new_chapter_id;
    }

    // Thực hiện nhân bản phần và các chương trong đó
    private function clonePart($db, $old_part_id, $new_course_id) {
        $stmt = $db->prepare("SELECT * FROM course_parts WHERE id = ?");
        $stmt->execute([$old_part_id]);
        $oldPart = $stmt->fetch();
        if (!$oldPart) return false;

        $orderQuery = $db->prepare("SELECT IFNULL(MAX(sort_order), -1) + 1 FROM course_parts WHERE course_id = ?");
        $orderQuery->execute([$new_course_id]);
        $nextOrder = (int)$orderQuery->fetchColumn();

        $stmtInsert = $db->prepare("INSERT INTO course_parts (course_id, title, sort_order) VALUES (?, ?, ?)");
        $stmtInsert->execute([
            $new_course_id,
            $oldPart['title'],
            $nextOrder
        ]);
        $new_part_id = $db->lastInsertId();

        $stmtChapters = $db->prepare("SELECT id FROM course_chapters WHERE part_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtChapters->execute([$old_part_id]);
        $chapters = $stmtChapters->fetchAll();
        foreach ($chapters as $chapter) {
            $this->cloneChapter($db, $chapter['id'], $new_part_id, $new_course_id);
        }

        return $new_part_id;
    }

    // Handler POST Sao chép Phần
    public function importPart() {
        $old_part_id = (int)($_POST['source_part_id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);

        if ($old_part_id > 0 && $course_id > 0) {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                $this->clonePart($db, $old_part_id, $course_id);
                $db->commit();
                $_SESSION['success'] = 'Sao chép phần thành công!';
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = 'Lỗi sao chép phần: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    // Handler POST Sao chép Chương
    public function importChapter() {
        $old_chapter_id = (int)($_POST['source_chapter_id'] ?? 0);
        $part_id = (int)($_POST['part_id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);

        if ($old_chapter_id > 0 && $part_id > 0 && $course_id > 0) {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                $this->cloneChapter($db, $old_chapter_id, $part_id, $course_id);
                $db->commit();
                $_SESSION['success'] = 'Sao chép chương thành công!';
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = 'Lỗi sao chép chương: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    // Handler POST Sao chép Bài học
    public function importLesson() {
        $old_lesson_id = (int)($_POST['source_lesson_id'] ?? 0);
        $chapter_id = (int)($_POST['chapter_id'] ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);

        if ($old_lesson_id > 0 && $chapter_id > 0 && $course_id > 0) {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                $this->cloneLesson($db, $old_lesson_id, $chapter_id, $course_id);
                $db->commit();
                $_SESSION['success'] = 'Sao chép bài học thành công!';
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = 'Lỗi sao chép bài học: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
}
