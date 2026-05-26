<?php
class AdminQuizController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin','admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $lesson_id = $_GET['lesson_id'] ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $l = $db->prepare("SELECT cl.id, cl.title FROM course_lessons cl WHERE cl.id = ?");
        $l->execute([$lesson_id]); $lesson = $l->fetch();
        $stmt = $db->prepare("SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY id DESC");
        $stmt->execute([$lesson_id]); $quizzes = $stmt->fetchAll();
        $this->render('admin/quizzes/index', ['title'=>'Quan ly Trac nghiem','lesson'=>$lesson,'quizzes'=>$quizzes,'course_id'=>$course_id], 'admin');
    }

    public function create() {
        $lesson_id = $_GET['lesson_id'] ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        $this->render('admin/quizzes/form', ['title'=>'Tao De Trac Nghiem','lesson_id'=>$lesson_id,'course_id'=>$course_id,'quiz'=>null], 'admin');
    }

    public function store() {
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO quizzes (lesson_id, title, description, time_limit_minutes, pass_score, max_attempts, shuffle_questions, shuffle_options) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $lesson_id, $_POST['title'] ?? 'De trac nghiem', $_POST['description'] ?? '',
            (int)($_POST['time_limit_minutes'] ?? 0), (float)($_POST['pass_score'] ?? 50),
            (int)($_POST['max_attempts'] ?? 0),
            isset($_POST['shuffle_questions']) ? 1 : 0,
            isset($_POST['shuffle_options']) ? 1 : 0,
        ]);
        $quiz_id = $db->lastInsertId();
        // Them vao lesson_items
        $db->prepare("INSERT INTO lesson_items (lesson_id, type, content) VALUES (?, 'quiz', ?)")->execute([$lesson_id, $quiz_id]);
        $_SESSION['success'] = 'Tao de trac nghiem thanh cong!';
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    public function questions() {
        $quiz_id   = $_GET['quiz_id']   ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $q = $db->prepare("SELECT q.*, cl.title as lesson_title FROM quizzes q JOIN course_lessons cl ON q.lesson_id = cl.id WHERE q.id = ?");
        $q->execute([$quiz_id]); $quiz = $q->fetch();
        if (!$quiz) { $this->redirect('/admin/courses'); return; }

        $iq = $db->prepare("SELECT qq.id as qq_id, qq.sort_order, qb.id as qb_id, qb.question_text FROM quiz_questions qq JOIN question_bank qb ON qq.bank_question_id = qb.id WHERE qq.quiz_id = ? ORDER BY qq.sort_order ASC");
        $iq->execute([$quiz_id]); $inQuizQuestions = $iq->fetchAll();
        foreach ($inQuizQuestions as &$inq) {
            $optStmt = $db->prepare("SELECT * FROM question_bank_options WHERE question_id = ? ORDER BY sort_order ASC, id ASC");
            $optStmt->execute([$inq['qb_id']]);
            $inq['options'] = $optStmt->fetchAll();
        }
        $inQuizIds = array_column($inQuizQuestions, 'qb_id');

        // Tim course_id
        $cr = $db->prepare("SELECT cp.course_id FROM course_lessons cl JOIN course_chapters cc ON cl.chapter_id=cc.id JOIN course_parts cp ON cc.part_id=cp.id WHERE cl.id=?");
        $cr->execute([$quiz['lesson_id']]); $cRow = $cr->fetch();
        $cid = $cRow ? $cRow['course_id'] : 0;

        $bk = $db->prepare("SELECT qb.id, qb.question_text FROM question_bank qb WHERE qb.course_id = ? ORDER BY qb.id DESC");
        $bk->execute([$cid]); $bankAll = $bk->fetchAll();

        // Loc cau hoi chua trong de (khong dung fn())
        $bankQuestions = array();
        foreach ($bankAll as $bq) {
            if (!in_array($bq['id'], $inQuizIds)) {
                $bankQuestions[] = $bq;
            }
        }

        $this->render('admin/quizzes/questions', [
            'title'           => 'Quan ly Cau hoi: ' . $quiz['title'],
            'quiz'            => $quiz,
            'inQuizQuestions' => $inQuizQuestions,
            'bankQuestions'   => $bankQuestions,
            'course_id'       => $course_id,
            'cid'             => $cid,
        ], 'admin');
    }

    public function storeQuestion() {
        $quiz_id   = $_POST['quiz_id']   ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $cid       = $_POST['cid']       ?? 0;
        $db = Database::getInstance()->getConnection();
        $qStmt = $db->prepare("INSERT INTO question_bank (course_id, question_text, created_by) VALUES (?, ?, ?)");
        $qStmt->execute([$cid, $_POST['question_text'] ?? '', $_SESSION['user_id']]);
        $qid = $db->lastInsertId();
        $options = $_POST['options'] ?? array();
        $correct = (int)($_POST['correct'] ?? 0);
        foreach ($options as $i => $opt) {
            if (trim($opt) === '') continue;
            $db->prepare("INSERT INTO question_bank_options (question_id, option_text, is_correct, sort_order) VALUES (?, ?, ?, ?)")->execute([$qid, $opt, ($i == $correct) ? 1 : 0, $i]);
        }
        $order = (int)$db->query("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id=" . (int)$quiz_id)->fetchColumn();
        $db->prepare("INSERT INTO quiz_questions (quiz_id, bank_question_id, sort_order) VALUES (?, ?, ?)")->execute([$quiz_id, $qid, $order]);
        $_SESSION['success'] = 'Da them cau hoi moi vao de.';
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    public function updateQuestion() {
        $quiz_id   = $_POST['quiz_id']   ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $qb_id     = $_POST['qb_id']     ?? 0;
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("UPDATE question_bank SET question_text = ? WHERE id = ?");
        $stmt->execute([$_POST['question_text'] ?? '', $qb_id]);
        
        $optStmt = $db->prepare("SELECT id FROM question_bank_options WHERE question_id = ? ORDER BY sort_order ASC, id ASC");
        $optStmt->execute([$qb_id]);
        $existingOptions = $optStmt->fetchAll();
        
        $options = $_POST['options'] ?? array();
        $correct = (int)($_POST['correct'] ?? 0);
        
        foreach ($options as $i => $opt) {
            $is_correct = ($i == $correct) ? 1 : 0;
            if (isset($existingOptions[$i])) {
                $db->prepare("UPDATE question_bank_options SET option_text = ?, is_correct = ? WHERE id = ?")
                   ->execute([$opt, $is_correct, $existingOptions[$i]['id']]);
            } else {
                $db->prepare("INSERT INTO question_bank_options (question_id, option_text, is_correct, sort_order) VALUES (?, ?, ?, ?)")
                   ->execute([$qb_id, $opt, $is_correct, $i]);
            }
        }
        
        if (count($existingOptions) > count($options)) {
            for ($i = count($options); $i < count($existingOptions); $i++) {
                $db->prepare("DELETE FROM question_bank_options WHERE id = ?")
                   ->execute([$existingOptions[$i]['id']]);
            }
        }
        
        $_SESSION['success'] = 'Cap nhat cau hoi thanh cong!';
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    public function addFromBank() {
        $quiz_id   = $_POST['quiz_id']   ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $qb_ids    = $_POST['qb_ids']    ?? array();
        $db = Database::getInstance()->getConnection();
        $order = (int)$db->query("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id=" . (int)$quiz_id)->fetchColumn();
        foreach ($qb_ids as $qbid) {
            $ex = $db->prepare("SELECT id FROM quiz_questions WHERE quiz_id=? AND bank_question_id=?");
            $ex->execute([$quiz_id, $qbid]);
            if ($ex->fetch()) continue;
            $db->prepare("INSERT INTO quiz_questions (quiz_id, bank_question_id, sort_order) VALUES (?, ?, ?)")->execute([$quiz_id, $qbid, $order++]);
        }
        $_SESSION['success'] = 'Da them cau hoi tu ngan hang.';
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    public function removeQuestion() {
        $qq_id     = $_POST['qq_id']     ?? 0;
        $quiz_id   = $_POST['quiz_id']   ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $db->prepare("DELETE FROM quiz_questions WHERE id=?")->execute([$qq_id]);
        $this->redirect('/admin/quizzes/questions?quiz_id=' . $quiz_id . '&course_id=' . $course_id);
    }

    public function delete() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $db->prepare("DELETE FROM lesson_items WHERE type='quiz' AND content=?")->execute([$id]);
        $db->prepare("DELETE FROM quizzes WHERE id=?")->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function results() {
        $quiz_id   = $_GET['quiz_id']   ?? 0;
        $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $q = $db->prepare("SELECT * FROM quizzes WHERE id=?"); $q->execute([$quiz_id]); $quiz = $q->fetch();
        $a = $db->prepare("SELECT qa.*, u.full_name, u.phone FROM quiz_attempts qa JOIN users u ON qa.student_id=u.id WHERE qa.quiz_id=? AND qa.submitted_at IS NOT NULL ORDER BY qa.submitted_at DESC");
        $a->execute([$quiz_id]); $attempts = $a->fetchAll();
        $this->render('admin/quizzes/results', ['title'=>'Ket qua: '.($quiz['title']??''),'quiz'=>$quiz,'attempts'=>$attempts,'course_id'=>$course_id], 'admin');
    }
}
