<?php
class ProgressController extends Controller {
    // Bảng kết quả cá nhân (cần đăng nhập)
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) { $this->redirect('/login'); return; }
        $db = Database::getInstance()->getConnection();
        $sid = $_SESSION['user_id'];

        // Các khóa học đang theo học
        $courses = $db->prepare("SELECT c.*, e.status as enroll_status, (SELECT COUNT(*) FROM course_lessons cl2 JOIN course_chapters cc2 ON cl2.chapter_id=cc2.id JOIN course_parts cp2 ON cc2.part_id=cp2.id WHERE cp2.course_id=c.id) as total_lessons, (SELECT COUNT(*) FROM course_progress WHERE student_id=? AND lesson_id IN (SELECT cl3.id FROM course_lessons cl3 JOIN course_chapters cc3 ON cl3.chapter_id=cc3.id JOIN course_parts cp3 ON cc3.part_id=cp3.id WHERE cp3.course_id=c.id) AND is_completed=1) as done_lessons FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.student_id=? AND e.status='active'");
        $courses->execute([$sid, $sid]); $courses = $courses->fetchAll();

        // Kết quả quiz
        $quizResults = $db->prepare("SELECT qa.*, q.title as quiz_title, cl.title as lesson_title FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id=q.id JOIN course_lessons cl ON q.lesson_id=cl.id WHERE qa.student_id=? AND qa.submitted_at IS NOT NULL ORDER BY qa.submitted_at DESC LIMIT 20");
        $quizResults->execute([$sid]); $quizResults = $quizResults->fetchAll();

        // Kết quả bài tập
        $asgResults = $db->prepare("SELECT s.*, a.title as asgn_title, a.max_score, a.type, a.id as assignment_id, cl.title as lesson_title FROM assignment_submissions s JOIN assignments a ON s.assignment_id=a.id JOIN course_lessons cl ON a.lesson_id=cl.id WHERE s.student_id=? ORDER BY s.submitted_at DESC LIMIT 20");
        $asgResults->execute([$sid]); $asgResults = $asgResults->fetchAll();

        // Thống kê Trắc nghiệm
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
        $quizStatsStmt->execute([$sid, $sid]);
        $quizStats = $quizStatsStmt->fetch() ?? [
            'total_quizzes' => 0,
            'attempted_quizzes' => 0,
            'passed_quizzes' => 0,
            'total_quiz_score' => 0
        ];

        // Thống kê Bài tập (Tự luận & Nộp file)
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
        $asgStatsStmt->execute([$sid, $sid]);
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

        $this->render('progress/dashboard', [
            'title' => 'Kết quả học tập', 
            'courses' => $courses, 
            'quizResults' => $quizResults, 
            'asgResults' => $asgResults,
            'quizStats' => $quizStats,
            'asgStats' => $asgStats
        ], 'main');
    }

    // Tra cứu bằng Số điện thoại + Tên đăng nhập (không cần đăng nhập)
    public function lookup() {
        $this->render('progress/lookup', ['title' => 'Tra cứu kết quả học tập'], 'main');
    }

    public function lookupResult() {
        $username = trim($_POST['username'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        
        if (empty($username) || empty($phone)) { 
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ Tên đăng nhập và Số điện thoại.'; 
            $this->redirect('/progress/lookup'); 
            return; 
        }

        // Rate limiting đơn giản qua session
        if (!isset($_SESSION['lookup_count'])) $_SESSION['lookup_count'] = 0;
        if (!isset($_SESSION['lookup_reset'])) $_SESSION['lookup_reset'] = time();
        if (time() - $_SESSION['lookup_reset'] > 60) { $_SESSION['lookup_count'] = 0; $_SESSION['lookup_reset'] = time(); }
        $_SESSION['lookup_count']++;
        if ($_SESSION['lookup_count'] > 10) { $_SESSION['error'] = 'Bạn tra cứu quá nhiều. Vui lòng chờ 1 phút.'; $this->redirect('/progress/lookup'); return; }

        $db = Database::getInstance()->getConnection();
        
        // Xác thực đồng thời cả Username và Số điện thoại
        $user = $db->prepare("SELECT id, full_name, phone, username FROM users WHERE username = ? AND phone = ?"); 
        $user->execute([$username, $phone]); 
        $user = $user->fetch();
        if (!$user) { 
            $_SESSION['error'] = 'Không tìm thấy thông tin học viên khớp với Tên đăng nhập và Số điện thoại này.'; 
            $this->redirect('/progress/lookup'); 
            return; 
        }

        $sid = $user['id'];

        // Thống kê Trắc nghiệm cho học viên được tra cứu
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
        $quizStatsStmt->execute([$sid, $sid]);
        $quizStats = $quizStatsStmt->fetch() ?? [
            'total_quizzes' => 0,
            'attempted_quizzes' => 0,
            'passed_quizzes' => 0,
            'total_quiz_score' => 0
        ];

        // Thống kê Bài tập cho học viên được tra cứu
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
        $asgStatsStmt->execute([$sid, $sid]);
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

        $quizResults = $db->prepare("SELECT qa.score, qa.max_score, qa.passed, qa.submitted_at, q.title as quiz_title, cl.title as lesson_title, c.title as course_title FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id=q.id JOIN course_lessons cl ON q.lesson_id=cl.id JOIN course_chapters cc ON cl.chapter_id=cc.id JOIN course_parts cp ON cc.part_id=cp.id JOIN courses c ON cp.course_id=c.id WHERE qa.student_id=? AND qa.submitted_at IS NOT NULL ORDER BY qa.submitted_at DESC");
        $quizResults->execute([$sid]); $quizResults = $quizResults->fetchAll();

        $asgResults = $db->prepare("SELECT s.score, s.feedback, s.status, s.submitted_at, a.title as asgn_title, a.max_score, a.type, cl.title as lesson_title, c.title as course_title FROM assignment_submissions s JOIN assignments a ON s.assignment_id=a.id JOIN course_lessons cl ON a.lesson_id=cl.id JOIN course_chapters cc ON cl.chapter_id=cc.id JOIN course_parts cp ON cc.part_id=cp.id JOIN courses c ON cp.course_id=c.id WHERE s.student_id=? ORDER BY s.submitted_at DESC");
        $asgResults->execute([$sid]); $asgResults = $asgResults->fetchAll();

        $this->render('progress/lookup_result', [
            'title'       => 'Kết quả: ' . $user['full_name'], 
            'user'        => $user, 
            'quizResults' => $quizResults, 
            'asgResults'  => $asgResults,
            'quizStats'   => $quizStats,
            'asgStats'    => $asgStats
        ], 'main');
    }
}
