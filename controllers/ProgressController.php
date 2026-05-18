<?php
class ProgressController extends Controller {
    // Bang ket qua ca nhan (can dang nhap)
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) { $this->redirect('/login'); return; }
        $db = Database::getInstance()->getConnection();
        $sid = $_SESSION['user_id'];

        // Cac khoa hoc dang theo hoc
        $courses = $db->prepare("SELECT c.*, e.status as enroll_status, (SELECT COUNT(*) FROM course_lessons cl2 JOIN course_chapters cc2 ON cl2.chapter_id=cc2.id JOIN course_parts cp2 ON cc2.part_id=cp2.id WHERE cp2.course_id=c.id) as total_lessons, (SELECT COUNT(*) FROM course_progress WHERE student_id=? AND lesson_id IN (SELECT cl3.id FROM course_lessons cl3 JOIN course_chapters cc3 ON cl3.chapter_id=cc3.id JOIN course_parts cp3 ON cc3.part_id=cp3.id WHERE cp3.course_id=c.id) AND is_completed=1) as done_lessons FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.student_id=? AND e.status='active'");
        $courses->execute([$sid, $sid]); $courses = $courses->fetchAll();

        // Ket qua quiz
        $quizResults = $db->prepare("SELECT qa.*, q.title as quiz_title, cl.title as lesson_title FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id=q.id JOIN course_lessons cl ON q.lesson_id=cl.id WHERE qa.student_id=? AND qa.submitted_at IS NOT NULL ORDER BY qa.submitted_at DESC LIMIT 20");
        $quizResults->execute([$sid]); $quizResults = $quizResults->fetchAll();

        // Ket qua bai tap
        $asgResults = $db->prepare("SELECT s.*, a.title as asgn_title, a.max_score, a.type, cl.title as lesson_title FROM assignment_submissions s JOIN assignments a ON s.assignment_id=a.id JOIN course_lessons cl ON a.lesson_id=cl.id WHERE s.student_id=? ORDER BY s.submitted_at DESC LIMIT 20");
        $asgResults->execute([$sid]); $asgResults = $asgResults->fetchAll();

        $this->render('progress/dashboard', ['title' => 'Ket qua hoc tap', 'courses' => $courses, 'quizResults' => $quizResults, 'asgResults' => $asgResults], 'main');
    }

    // Tra cuu bang so dien thoai (khong can dang nhap)
    public function lookup() {
        $this->render('progress/lookup', ['title' => 'Tra cuu ket qua hoc tap'], 'main');
    }

    public function lookupResult() {
        $phone = trim($_POST['phone'] ?? '');
        if (empty($phone)) { $_SESSION['error'] = 'Vui long nhap so dien thoai.'; $this->redirect('/progress/lookup'); return; }

        // Rate limiting don gian qua session
        if (!isset($_SESSION['lookup_count'])) $_SESSION['lookup_count'] = 0;
        if (!isset($_SESSION['lookup_reset'])) $_SESSION['lookup_reset'] = time();
        if (time() - $_SESSION['lookup_reset'] > 60) { $_SESSION['lookup_count'] = 0; $_SESSION['lookup_reset'] = time(); }
        $_SESSION['lookup_count']++;
        if ($_SESSION['lookup_count'] > 10) { $_SESSION['error'] = 'Ban tra cuu qua nhieu. Vui long cho 1 phut.'; $this->redirect('/progress/lookup'); return; }

        $db = Database::getInstance()->getConnection();
        $user = $db->prepare("SELECT id, full_name, phone FROM users WHERE phone=?"); $user->execute([$phone]); $user = $user->fetch();
        if (!$user) { $_SESSION['error'] = 'Khong tim thay hoc vien voi so dien thoai nay.'; $this->redirect('/progress/lookup'); return; }

        $sid = $user['id'];
        $quizResults = $db->prepare("SELECT qa.score, qa.max_score, qa.passed, qa.submitted_at, q.title as quiz_title, cl.title as lesson_title, c.title as course_title FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id=q.id JOIN course_lessons cl ON q.lesson_id=cl.id JOIN course_chapters cc ON cl.chapter_id=cc.id JOIN course_parts cp ON cc.part_id=cp.id JOIN courses c ON cp.course_id=c.id WHERE qa.student_id=? AND qa.submitted_at IS NOT NULL ORDER BY qa.submitted_at DESC");
        $quizResults->execute([$sid]); $quizResults = $quizResults->fetchAll();

        $asgResults = $db->prepare("SELECT s.score, s.feedback, s.status, s.submitted_at, a.title as asgn_title, a.max_score, a.type, cl.title as lesson_title, c.title as course_title FROM assignment_submissions s JOIN assignments a ON s.assignment_id=a.id JOIN course_lessons cl ON a.lesson_id=cl.id JOIN course_chapters cc ON cl.chapter_id=cc.id JOIN course_parts cp ON cc.part_id=cp.id JOIN courses c ON cp.course_id=c.id WHERE s.student_id=? ORDER BY s.submitted_at DESC");
        $asgResults->execute([$sid]); $asgResults = $asgResults->fetchAll();

        $this->render('progress/lookup_result', ['title' => 'Ket qua: '.$user['full_name'], 'user' => $user, 'quizResults' => $quizResults, 'asgResults' => $asgResults], 'main');
    }
}
