<?php
class AdminQuizController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin','admin'])) {
            $this->redirect('/login');
        }
    }

    // Danh sach quiz theo bai hoc
    public function index() {
        $lesson_id = $_GET['lesson_id'] ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();

        $lesson = $db->prepare("SELECT cl.*, cc.title as chapter_title, c.id as course_id, c.title as course_title
            FROM course_lessons cl
            JOIN course_chapters cc ON cl.chapter_id = cc.id
            JOIN course_parts cp ON cc.part_id = cp.id
            JOIN courses c ON cp.course_id = c.id
            WHERE cl.id = ?");
        $lesson->execute([$lesson_id]);
        $lesson = $lesson->fetch();

        $stmt = $db->prepare("SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY id DESC");
        $stmt->execute([$lesson_id]);
        $quizzes = $stmt->fetchAll();

        $this->render('admin/quizzes/index', [
            'title'     => 'Quan ly Trac nghiem',
            'lesson'    => $lesson,
            'quizzes'   => $quizzes,
            'course_id' => $course_id,
        ], 'admin');
    }

    // Tao quiz moi
    public function create() {
        $lesson_id = $_GET['lesson_id'] ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        $this->render('admin/quizzes/form', [
            'title'     => 'Tao De Trac Nghiem',
            'lesson_id' => $lesson_id,
            'course_id' => $course_id,
            'quiz'      => null,
        ], 'admin');
    }

    public function store() {
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("INSERT INTO quizzes (lesson_id, title, description, time_limit_minutes, pass_score, max_attempts, shuffle_questions, shuffle_options)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $lesson_id,
            $_POST['title'] ?? 'De trac nghiem',
            $_POST['description'] ?? '',
            (int)($_POST['time_limit_minutes'] ?? 0),
            (float)($_POST['pass_score'] ?? 50),
            (int)($_POST['max_attempts'] ?? 0),
            isset($_POST['shuffle_questions']) ? 1 : 0,
            isset($_POST['shuffle_options']) ? 1 : 0,
        ]);
        $quiz_id = $db->lastInsertId();

        // Them vao lesson_items de hien thi trong bai hoc
        $itemStmt = $db->prepare("INSERT INTO lesson_items (lesson_id, type, content) VALUES (?, 'quiz', ?)");
        $itemStmt->execute([$lesson_id, $quiz_id]);

        $_SESSION['success'] = 'Tao de trac nghiem thanh cong! Hay them cau hoi vao de.';
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    // Trang quan ly cau hoi cua mot quiz
    public function questions() {
        $quiz_id   = $_GET['quiz_id']   ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();

        $quiz = $db->prepare("SELECT q.*, cl.title as lesson_title FROM quizzes q JOIN course_lessons cl ON q.lesson_id = cl.id WHERE q.id = ?");
        $quiz->execute([$quiz_id]);
        $quiz = $quiz->fetch();
        if (!$quiz) { $this->redirect('/admin/courses'); return; }

        // Cau hoi da trong de
        $inQuiz = $db->prepare("SELECT qq.id as qq_id, qq.sort_order, qb.id as qb_id, qb.question_text FROM quiz_questions qq JOIN question_bank qb ON qq.bank_question_id = qb.id WHERE qq.quiz_id = ? ORDER BY qq.sort_order ASC");
        $inQuiz->execute([$quiz_id]);
        $inQuizQuestions = $inQuiz->fetchAll();

        // Ngan hang cau hoi cua khoa hoc nay (chua trong de)
        $inQuizIds = array_column($inQuizQuestions, 'qb_id');
        $course_id_q = $db->prepare("SELECT cp.course_id FROM course_lessons cl JOIN course_chapters cc ON cl.chapter_id = cc.id JOIN course_parts cp ON cc.part_id = cp.id WHERE cl.id = ?");
        $course_id_q->execute([$quiz['lesson_id']]);
        $cRow = $course_id_q->fetch();
        $cid  = $cRow ? $cRow['course_id'] : 0;

        $bankStmt = $db->prepare("SELECT qb.*, (SELECT COUNT(*) FROM question_bank_options WHERE question_id = qb.id) as opt_count FROM question_bank qb WHERE qb.course_id = ? ORDER BY qb.id DESC");
        $bankStmt->execute([$cid]);
        $bankAll = $bankStmt->fetchAll();
        $bankQuestions = array_filter($bankAll, fn($q) => !in_array($q['id'], $inQuizIds));

        $this->render('admin/quizzes/questions', [
            'title'          => 'Quan ly Cau hoi: ' . $quiz['title'],
            'quiz'           => $quiz,
            'inQuizQuestions'=> $inQuizQuestions,
            'bankQuestions'  => array_values($bankQuestions),
            'course_id'      => $course_id,
            'cid'            => $cid,
        ], 'admin');
    }

    // Them cau hoi moi vao ngan hang VA vao de luon
    public function storeQuestion() {
        $quiz_id   = $_POST['quiz_id']   ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $cid       = $_POST['cid']       ?? 0;
        $db = Database::getInstance()->getConnection();

        $qStmt = $db->prepare("INSERT INTO question_bank (course_id, question_text, created_by) VALUES (?, ?, ?)");
        $qStmt->execute([$cid, $_POST['question_text'] ?? '', $_SESSION['user_id']]);
        $qid = $db->lastInsertId();

        // Them cac dap an
        $options   = $_POST['options']   ?? [];
        $correct   = $_POST['correct']   ?? 0;
        foreach ($options as $i => $opt) {
            if (trim($opt) === '') continue;
            $oStmt = $db->prepare("INSERT INTO question_bank_options (question_id, option_text, is_correct, sort_order) VALUES (?, ?, ?, ?)");
            $oStmt->execute([$qid, $opt, ($i == $correct) ? 1 : 0, $i]);
        }

        // Them vao de
        $order = $db->query("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = $quiz_id")->fetchColumn();
        $qqStmt = $db->prepare("INSERT INTO quiz_questions (quiz_id, bank_question_id, sort_order) VALUES (?, ?, ?)");
        $qqStmt->execute([$quiz_id, $qid, $order]);

        $_SESSION['success'] = 'Da them cau hoi moi vao de.';
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    // Them cau hoi tu ngan hang vao de
    public function addFromBank() {
        $quiz_id   = $_POST['quiz_id']   ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $qb_ids    = $_POST['qb_ids']    ?? [];
        $db = Database::getInstance()->getConnection();

        $order = (int)$db->query("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = $quiz_id")->fetchColumn();
        foreach ($qb_ids as $qbid) {
            $exists = $db->prepare("SELECT id FROM quiz_questions WHERE quiz_id = ? AND bank_question_id = ?");
            $exists->execute([$quiz_id, $qbid]);
            if ($exists->fetch()) continue;
            $s = $db->prepare("INSERT INTO quiz_questions (quiz_id, bank_question_id, sort_order) VALUES (?, ?, ?)");
            $s->execute([$quiz_id, $qbid, $order++]);
        }
        $_SESSION['success'] = 'Da them ' . count($qb_ids) . ' cau hoi tu ngan hang.';
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    // Xoa cau hoi khoi de (khong xoa khoi ngan hang)
    public function removeQuestion() {
        $qq_id     = $_POST['qq_id']     ?? 0;
        $quiz_id   = $_POST['quiz_id']   ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $db->prepare("DELETE FROM quiz_questions WHERE id = ?")->execute([$qq_id]);
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    // Xoa quiz
    public function delete() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        // Xoa lesson_item tuong ung
        $db->prepare("DELETE FROM lesson_items WHERE type='quiz' AND content = ?")->execute([$id]);
        $db->prepare("DELETE FROM quizzes WHERE id = ?")->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    // Xem ket qua cua hoc vien
    public function results() {
        $quiz_id   = $_GET['quiz_id']   ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();

        $quiz = $db->prepare("SELECT * FROM quizzes WHERE id = ?");
        $quiz->execute([$quiz_id]);
        $quiz = $quiz->fetch();

        $attempts = $db->prepare("SELECT qa.*, u.full_name, u.phone FROM quiz_attempts qa JOIN users u ON qa.student_id = u.id WHERE qa.quiz_id = ? AND qa.submitted_at IS NOT NULL ORDER BY qa.submitted_at DESC");
        $attempts->execute([$quiz_id]);
        $attempts = $attempts->fetchAll();

        $this->render('admin/quizzes/results', [
            'title'     => 'Ket qua: ' . ($quiz['title'] ?? ''),
            'quiz'      => $quiz,
            'attempts'  => $attempts,
            'course_id' => $course_id,
        ], 'admin');
    }
}
