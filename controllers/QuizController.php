<?php
class QuizController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui long dang nhap de lam bai.';
            $this->redirect('/login');
        }
    }

    // Bat dau hoac tiep tuc lam bai
    public function take() {
        $quiz_id = (int)($_GET['quiz_id'] ?? 0);
        $db = Database::getInstance()->getConnection();

        // Lay thong tin de thi
        $qStmt = $db->prepare("SELECT * FROM quizzes WHERE id=?");
        $qStmt->execute([$quiz_id]);
        $quiz = $qStmt->fetch();

        if (!$quiz) {
            $_SESSION['error'] = 'De thi khong ton tai.';
            $this->redirect('/');
            return;
        }

        // Kiem tra so lan lam bai
        if ($quiz['max_attempts'] > 0) {
            $cntStmt = $db->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id=? AND student_id=? AND submitted_at IS NOT NULL");
            $cntStmt->execute([$quiz_id, $_SESSION['user_id']]);
            $cnt = (int)$cntStmt->fetchColumn();
            if ($cnt >= $quiz['max_attempts']) {
                $_SESSION['error'] = 'Ban da het luot lam bai.';
                $this->redirect('/');
                return;
            }
        }

        // Tim attempt dang lam do (chua submitted)
        $aStmt = $db->prepare("SELECT * FROM quiz_attempts WHERE quiz_id=? AND student_id=? AND submitted_at IS NULL ORDER BY id DESC LIMIT 1");
        $aStmt->execute([$quiz_id, $_SESSION['user_id']]);
        $attempt = $aStmt->fetch();

        // Neu chua co attempt, tao moi
        if (!$attempt) {
            $seed = rand(100000, 9999999);
            $ins = $db->prepare("INSERT INTO quiz_attempts (quiz_id, student_id, shuffle_seed) VALUES (?,?,?)");
            $ins->execute([$quiz_id, $_SESSION['user_id'], $seed]);
            $new_id = (int)$db->lastInsertId();  // Luu lastInsertId ngay lap tuc

            $naStmt = $db->prepare("SELECT * FROM quiz_attempts WHERE id=?");
            $naStmt->execute([$new_id]);
            $attempt = $naStmt->fetch();
        }

        if (!$attempt) {
            $_SESSION['error'] = 'Khong the tao luot lam bai. Vui long thu lai.';
            $this->redirect('/');
            return;
        }

        // Lay danh sach cau hoi cua de
        $qs = $db->prepare("SELECT qq.id as qq_id, qb.id as qb_id, qb.question_text FROM quiz_questions qq JOIN question_bank qb ON qq.bank_question_id=qb.id WHERE qq.quiz_id=? ORDER BY qq.sort_order ASC");
        $qs->execute([$quiz_id]);
        $questions = $qs->fetchAll();

        // Dao thu tu cau hoi theo seed
        if ($quiz['shuffle_questions'] && count($questions) > 0) {
            srand((int)$attempt['shuffle_seed']);
            shuffle($questions);
            srand();
        }

        // Lay cac phuong an cho tung cau
        foreach ($questions as &$q) {
            $opts = $db->prepare("SELECT * FROM question_bank_options WHERE question_id=? ORDER BY sort_order ASC");
            $opts->execute([$q['qb_id']]);
            $q['options'] = $opts->fetchAll();
            if ($quiz['shuffle_options'] && count($q['options']) > 0) {
                srand((int)$attempt['shuffle_seed'] + (int)$q['qb_id']);
                shuffle($q['options']);
                srand();
            }
        }
        unset($q);

        // Cau tra loi da luu truoc do (neu reload trang)
        $savedStmt = $db->prepare("SELECT bank_question_id, selected_option_id FROM quiz_attempt_answers WHERE attempt_id=?");
        $savedStmt->execute([$attempt['id']]);
        $savedMap = array();
        foreach ($savedStmt->fetchAll() as $row) {
            $savedMap[$row['bank_question_id']] = $row['selected_option_id'];
        }

        $this->render('quiz/take', array(
            'title'     => $quiz['title'],
            'quiz'      => $quiz,
            'attempt'   => $attempt,
            'questions' => $questions,
            'savedMap'  => $savedMap,
        ), 'main');
    }

    // Nop bai va cham diem tu dong
    public function submit() {
        $attempt_id = (int)($_POST['attempt_id'] ?? 0);
        if ($attempt_id <= 0) { $this->redirect('/'); return; }

        $db = Database::getInstance()->getConnection();

        // Xac thuc attempt thuoc ve hoc vien nay
        $aStmt = $db->prepare("SELECT * FROM quiz_attempts WHERE id=? AND student_id=?");
        $aStmt->execute([$attempt_id, $_SESSION['user_id']]);
        $attempt = $aStmt->fetch();

        if (!$attempt) { $this->redirect('/'); return; }
        if ($attempt['submitted_at']) {
            // Da nop roi, chuyen thang den ket qua
            $this->redirect('/quiz/result?attempt_id=' . $attempt_id);
            return;
        }

        // Lay thong tin de thi
        $qStmt = $db->prepare("SELECT * FROM quizzes WHERE id=?");
        $qStmt->execute([$attempt['quiz_id']]);
        $quiz = $qStmt->fetch();

        if (!$quiz) { $this->redirect('/'); return; }

        $answers = $_POST['answers'] ?? array();

        // Xoa cau tra loi cu neu co (lam lai)
        $db->prepare("DELETE FROM quiz_attempt_answers WHERE attempt_id=?")->execute([$attempt_id]);

        // Luu tung cau tra loi va tinh diem
        $score = 0;
        foreach ($answers as $qb_id => $opt_id) {
            $qb_id  = (int)$qb_id;
            $opt_id = (int)$opt_id;
            if ($qb_id <= 0 || $opt_id <= 0) continue;

            // Kiem tra dap an co dung khong
            $cStmt = $db->prepare("SELECT is_correct FROM question_bank_options WHERE id=? AND question_id=?");
            $cStmt->execute([$opt_id, $qb_id]);
            $row = $cStmt->fetch();
            $is_correct = ($row && $row['is_correct']) ? 1 : 0;

            $db->prepare("INSERT INTO quiz_attempt_answers (attempt_id, bank_question_id, selected_option_id, is_correct) VALUES (?,?,?,?)")
               ->execute([$attempt_id, $qb_id, $opt_id, $is_correct]);
            $score += $is_correct;
        }

        // Tinh tong so cau trong de
        $totalStmt = $db->prepare("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id=?");
        $totalStmt->execute([$attempt['quiz_id']]);
        $totalQ = (int)$totalStmt->fetchColumn();

        // Tinh phan tram va ket qua
        $pct    = $totalQ > 0 ? round($score / $totalQ * 100, 2) : 0;
        $passed = ($pct >= (float)$quiz['pass_score']) ? 1 : 0;

        // Cap nhat ket qua vao DB
        $db->prepare("UPDATE quiz_attempts SET score=?, max_score=100, passed=?, submitted_at=NOW() WHERE id=?")
           ->execute([$pct, $passed, $attempt_id]);

        $this->redirect('/quiz/result?attempt_id=' . $attempt_id);
    }

    // Hien thi ket qua sau khi nop bai
    public function result() {
        $attempt_id = (int)($_GET['attempt_id'] ?? 0);
        $db = Database::getInstance()->getConnection();

        $aStmt = $db->prepare("SELECT qa.*, q.title as quiz_title, q.pass_score, q.lesson_id FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id=q.id WHERE qa.id=? AND qa.student_id=?");
        $aStmt->execute([$attempt_id, $_SESSION['user_id']]);
        $attempt = $aStmt->fetch();

        if (!$attempt) { $this->redirect('/'); return; }

        // Chi tiet tung cau tra loi kem dap an dung
        $ansStmt = $db->prepare(
            "SELECT qaa.bank_question_id, qaa.selected_option_id, qaa.is_correct,
                    qb.question_text,
                    qbo_sel.option_text as selected_text,
                    (SELECT option_text FROM question_bank_options WHERE question_id=qb.id AND is_correct=1 LIMIT 1) as correct_text
             FROM quiz_attempt_answers qaa
             JOIN question_bank qb ON qaa.bank_question_id=qb.id
             LEFT JOIN question_bank_options qbo_sel ON qaa.selected_option_id=qbo_sel.id
             WHERE qaa.attempt_id=?"
        );
        $ansStmt->execute([$attempt_id]);
        $answers = $ansStmt->fetchAll();

        // Lay course_id de co the quay ve bai hoc
        $crStmt = $db->prepare("SELECT cp.course_id FROM course_lessons cl JOIN course_chapters cc ON cl.chapter_id=cc.id JOIN course_parts cp ON cc.part_id=cp.id WHERE cl.id=?");
        $crStmt->execute([$attempt['lesson_id']]);
        $cRow = $crStmt->fetch();

        $this->render('quiz/result', array(
            'title'     => 'Ket qua: ' . $attempt['quiz_title'],
            'attempt'   => $attempt,
            'answers'   => $answers,
            'course_id' => $cRow ? $cRow['course_id'] : 0,
        ), 'main');
    }
}
