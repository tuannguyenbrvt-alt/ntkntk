<?php
class AdminStudentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $search = trim($_GET['q'] ?? '');
        $db = Database::getInstance()->getConnection();
        if ($search !== '') {
            $stmt = $db->prepare("SELECT u.*, (SELECT COUNT(*) FROM enrollments WHERE student_id = u.id AND status = 'active') as active_courses FROM users u WHERE u.role = 'student' AND (u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?) ORDER BY u.created_at DESC");
            $stmt->execute(['%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);
        } else {
            $stmt = $db->query("SELECT u.*, (SELECT COUNT(*) FROM enrollments WHERE student_id = u.id AND status = 'active') as active_courses FROM users u WHERE u.role = 'student' ORDER BY u.created_at DESC");
        }
        $students = $stmt->fetchAll();

        $this->render('admin/students/index', [
            'title' => 'Quản lý Học viên',
            'students' => $students,
            'search' => $search
        ], 'admin');
    }

    public function show() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
        $stmt->execute([$id]);
        $student = $stmt->fetch();

        if (!$student) {
            $this->redirect('/admin/students');
        }

        $stmtCourses = $db->prepare("SELECT c.title, c.price, e.status, e.price_paid, e.created_at as enrolled_at, e.tx_code FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? ORDER BY e.created_at DESC");
        $stmtCourses->execute([$id]);
        $enrollments = $stmtCourses->fetchAll();

        $totalPaid = 0;
        foreach ($enrollments as $en) {
            if ($en['status'] == 'active') {
                $totalPaid += $en['price_paid'];
            }
        }

        // --- TẢI CHI TIẾT KẾT QUẢ HỌC TẬP ---
        
        // 1. Tiến độ khóa học đang kích hoạt
        $stmtActiveCourses = $db->prepare("
            SELECT c.id, c.title, e.status as enroll_status, 
                (SELECT COUNT(*) FROM course_lessons cl2 JOIN course_chapters cc2 ON cl2.chapter_id=cc2.id JOIN course_parts cp2 ON cc2.part_id=cp2.id WHERE cp2.course_id=c.id) as total_lessons, 
                (SELECT COUNT(*) FROM course_progress WHERE student_id=? AND lesson_id IN (SELECT cl3.id FROM course_lessons cl3 JOIN course_chapters cc3 ON cl3.chapter_id=cc3.id JOIN course_parts cp3 ON cc3.part_id=cp3.id WHERE cp3.course_id=c.id) AND is_completed=1) as done_lessons 
            FROM enrollments e 
            JOIN courses c ON e.course_id=c.id 
            WHERE e.student_id=? AND e.status='active'
        ");
        $stmtActiveCourses->execute([$id, $id]);
        $activeCourses = $stmtActiveCourses->fetchAll();

        // 2. Thống kê Trắc nghiệm
        $quizStatsStmt = $db->prepare("
            SELECT 
                COUNT(DISTINCT q.id) as total_quizzes,
                COUNT(DISTINCT qa.quiz_id) as attempted_quizzes,
                SUM(CASE WHEN qa.passed = 1 THEN 1 ELSE 0 END) as passed_quizzes,
                SUM(qa.score) as total_quiz_score
            FROM enrollments e
            JOIN course_parts cp ON e.course_id = cp.course_id
            JOIN course_chapters cc ON cp.id = cc.part_id
            JOIN course_lessons cl ON cc.id = cl.chapter_id
            JOIN quizzes q ON cl.id = q.lesson_id
            LEFT JOIN (
                SELECT quiz_id, MAX(score) as score, MAX(passed) as passed 
                FROM quiz_attempts 
                WHERE student_id = ? AND submitted_at IS NOT NULL 
                GROUP BY quiz_id
            ) qa ON q.id = qa.quiz_id
            WHERE e.student_id = ? AND e.status = 'active'
        ");
        $quizStatsStmt->execute([$id, $id]);
        $quizStats = $quizStatsStmt->fetch();
        if (!$quizStats) {
            $quizStats = [
                'total_quizzes' => 0,
                'attempted_quizzes' => 0,
                'passed_quizzes' => 0,
                'total_quiz_score' => 0
            ];
        }

        // 3. Thống kê Bài tập (Tự luận & Nộp file)
        $asgStatsStmt = $db->prepare("
            SELECT 
                a.type as asgn_type,
                COUNT(DISTINCT a.id) as total_asgns,
                COUNT(DISTINCT s.id) as submitted_asgns,
                SUM(CASE WHEN s.status = 'graded' THEN 1 ELSE 0 END) as graded_asgns,
                SUM(CASE WHEN s.status = 'graded' THEN s.score ELSE 0 END) as total_score,
                SUM(a.max_score) as total_max_score
            FROM enrollments e
            JOIN course_parts cp ON e.course_id = cp.course_id
            JOIN course_chapters cc ON cp.id = cc.part_id
            JOIN course_lessons cl ON cc.id = cl.chapter_id
            JOIN assignments a ON cl.id = a.lesson_id
            LEFT JOIN assignment_submissions s ON a.id = s.assignment_id AND s.student_id = ?
            WHERE e.student_id = ? AND e.status = 'active'
            GROUP BY a.type
        ");
        $asgStatsStmt->execute([$id, $id]);
        $asgStatsRaw = $asgStatsStmt->fetchAll();
        
        $asgStats = [
            'essay' => ['total' => 0, 'submitted' => 0, 'graded' => 0, 'score' => 0, 'max_score' => 0],
            'file'  => ['total' => 0, 'submitted' => 0, 'graded' => 0, 'score' => 0, 'max_score' => 0]
        ];
        foreach ($asgStatsRaw as $row) {
            $type = $row['asgn_type'];
            if (isset($asgStats[$type])) {
                $asgStats[$type] = [
                    'total' => (int)$row['total_asgns'],
                    'submitted' => (int)$row['submitted_asgns'],
                    'graded' => (int)$row['graded_asgns'],
                    'score' => (float)$row['total_score'],
                    'max_score' => (float)$row['total_max_score']
                ];
            }
        }

        // 4. Danh sách các bài Trắc nghiệm đã làm
        $stmtQuizResults = $db->prepare("
            SELECT qa.id as attempt_id, qa.score, qa.max_score, qa.passed, qa.submitted_at, 
                   q.title as quiz_title, cl.title as lesson_title, c.title as course_title 
            FROM quiz_attempts qa 
            JOIN quizzes q ON qa.quiz_id=q.id 
            JOIN course_lessons cl ON q.lesson_id=cl.id 
            JOIN course_chapters cc ON cl.chapter_id=cc.id 
            JOIN course_parts cp ON cc.part_id=cp.id 
            JOIN courses c ON cp.course_id=c.id 
            WHERE qa.student_id=? AND qa.submitted_at IS NOT NULL 
            ORDER BY qa.submitted_at DESC
        ");
        $stmtQuizResults->execute([$id]);
        $quizResults = $stmtQuizResults->fetchAll();

        // 5. Danh sách các bài tập đã nộp
        $stmtAsgResults = $db->prepare("
            SELECT s.id as submission_id, s.assignment_id, s.content, s.file_name, s.file_drive_url, s.score, s.feedback, s.status, s.submitted_at, s.graded_at, 
                   a.title as asgn_title, a.max_score, a.type, cl.title as lesson_title, c.title as course_title,
                   u.full_name as grader_name
            FROM assignment_submissions s 
            JOIN assignments a ON s.assignment_id=a.id 
            JOIN course_lessons cl ON a.lesson_id=cl.id 
            JOIN course_chapters cc ON cl.chapter_id=cc.id 
            JOIN course_parts cp ON cc.part_id=cp.id 
            JOIN courses c ON cp.course_id=c.id 
            LEFT JOIN users u ON s.graded_by = u.id
            WHERE s.student_id=? 
            ORDER BY s.submitted_at DESC
        ");
        $stmtAsgResults->execute([$id]);
        $asgResults = $stmtAsgResults->fetchAll();

        $this->render('admin/students/show', [
            'title' => 'Chi tiết Hồ sơ Học viên',
            'student' => $student,
            'enrollments' => $enrollments,
            'totalPaid' => $totalPaid,
            'activeCourses' => $activeCourses,
            'quizStats' => $quizStats,
            'asgStats' => $asgStats,
            'quizResults' => $quizResults,
            'asgResults' => $asgResults
        ], 'admin');
    }

    public function quizAttempt() {
        $attempt_id = (int)($_GET['attempt_id'] ?? 0);
        $db = Database::getInstance()->getConnection();

        // Xác thực lượt thi
        $aStmt = $db->prepare("
            SELECT qa.*, q.title as quiz_title, q.pass_score, q.lesson_id, u.full_name as student_name, u.id as student_id
            FROM quiz_attempts qa 
            JOIN quizzes q ON qa.quiz_id=q.id 
            JOIN users u ON qa.student_id = u.id
            WHERE qa.id=? AND qa.submitted_at IS NOT NULL
        ");
        $aStmt->execute([$attempt_id]);
        $attempt = $aStmt->fetch();

        if (!$attempt) {
            $_SESSION['error'] = 'Lượt làm bài trắc nghiệm không tồn tại hoặc chưa nộp.';
            $this->redirect('/admin/students');
            return;
        }

        // Lấy chi tiết các câu trả lời của attempt này
        $qStmt = $db->prepare("
            SELECT DISTINCT qb.id, qb.question_text, qb.question_type, qq.sort_order 
             FROM quiz_attempt_answers qaa
             JOIN question_bank qb ON qaa.bank_question_id = qb.id
             LEFT JOIN quiz_questions qq ON qq.bank_question_id = qb.id AND qq.quiz_id = ?
             WHERE qaa.attempt_id = ?
             ORDER BY qq.sort_order ASC, qb.id ASC
        ");
        $qStmt->execute([$attempt['quiz_id'], $attempt_id]);
        $questions = $qStmt->fetchAll();

        $resultDetails = [];
        foreach ($questions as $q) {
            $qb_id = (int)$q['id'];
            
            // Lấy các phương án lựa chọn trong câu hỏi
            $oStmt = $db->prepare("SELECT id, option_text, is_correct FROM question_bank_options WHERE question_id = ? ORDER BY sort_order ASC, id ASC");
            $oStmt->execute([$qb_id]);
            $options = $oStmt->fetchAll();
            
            // Lấy các phương án mà học sinh đã chọn
            $sStmt = $db->prepare("SELECT selected_option_id, is_correct FROM quiz_attempt_answers WHERE attempt_id = ? AND bank_question_id = ?");
            $sStmt->execute([$attempt_id, $qb_id]);
            $selectedRows = $sStmt->fetchAll();
            
            $selectedOptionIds = [];
            $questionCorrect = 0;
            foreach ($selectedRows as $sr) {
                if ($sr['selected_option_id'] !== null) {
                    $selectedOptionIds[] = (int)$sr['selected_option_id'];
                }
                if ($sr['is_correct']) {
                    $questionCorrect = 1;
                }
            }
            
            $resultDetails[] = [
                'id'                  => $qb_id,
                'question_text'       => $q['question_text'],
                'question_type'       => $q['question_type'] ?? 'single',
                'options'             => $options,
                'selected_option_ids' => $selectedOptionIds,
                'is_correct'          => $questionCorrect
            ];
        }

        $this->render('admin/students/quiz_attempt', [
            'title'         => 'Chi tiết làm bài: ' . $attempt['student_name'],
            'attempt'       => $attempt,
            'resultDetails' => $resultDetails
        ], 'admin');
    }

    public function update() {
        $id = $_POST['id'] ?? 0;
        $full_name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $profession = $_POST['profession'] ?? '';
        $address = $_POST['address'] ?? '';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, profession = ?, address = ? WHERE id = ?");
        $stmt->execute([$full_name, $phone, $profession, $address, $id]);
        
        $_SESSION['success'] = 'Cập nhật hồ sơ học viên thành công.';
        $this->redirect('/admin/students/show?id=' . $id);
    }
}
