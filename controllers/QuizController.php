<?php
class QuizController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) { $_SESSION['error'] = 'Vui long dang nhap.'; $this->redirect('/login'); }
    }

    // Bat dau hoac tiep tuc lam bai
    public function take() {
        $quiz_id = $_GET['quiz_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $quiz = $db->prepare("SELECT * FROM quizzes WHERE id = ?"); $quiz->execute([$quiz_id]); $quiz = $quiz->fetch();
        if (!$quiz) { $_SESSION['error'] = 'De thi khong ton tai.'; $this->redirect('/'); return; }

        // Kiem tra so lan lam bai
        if ($quiz['max_attempts'] > 0) {
            $cnt = (int)$db->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id=? AND student_id=? AND submitted_at IS NOT NULL")->execute([$quiz_id, $_SESSION['user_id']]) ? $db->query("SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id=$quiz_id AND student_id=".$_SESSION['user_id']." AND submitted_at IS NOT NULL")->fetchColumn() : 0;
            if ($cnt >= $quiz['max_attempts']) { $_SESSION['error'] = 'Ban da het luot lam bai.'; $this->redirect('/learning?course_id=0&lesson_id='.$quiz['lesson_id']); return; }
        }

        // Tim attempt dang lam do (chua submitted)
        $attempt = $db->prepare("SELECT * FROM quiz_attempts WHERE quiz_id=? AND student_id=? AND submitted_at IS NULL ORDER BY id DESC LIMIT 1");
        $attempt->execute([$quiz_id, $_SESSION['user_id']]); $attempt = $attempt->fetch();

        if (!$attempt) {
            $seed = rand(1000, 9999999);
            $ins  = $db->prepare("INSERT INTO quiz_attempts (quiz_id, student_id, shuffle_seed) VALUES (?,?,?)");
            $ins->execute([$quiz_id, $_SESSION['user_id'], $seed]);
            $attempt = $db->prepare("SELECT * FROM quiz_attempts WHERE id=?")->execute([$db->lastInsertId()]) ? null : null;
            $attempt = $db->query("SELECT * FROM quiz_attempts WHERE id=".$db->lastInsertId())->fetch();
            if (!$attempt) {
                $attempt = $db->query("SELECT * FROM quiz_attempts WHERE quiz_id=$quiz_id AND student_id=".$_SESSION['user_id']." ORDER BY id DESC LIMIT 1")->fetch();
            }
        }

        // Lay cau hoi va tron
        $qs = $db->prepare("SELECT qq.id as qq_id, qb.id as qb_id, qb.question_text FROM quiz_questions qq JOIN question_bank qb ON qq.bank_question_id = qb.id WHERE qq.quiz_id = ? ORDER BY qq.sort_order ASC");
        $qs->execute([$quiz_id]); $questions = $qs->fetchAll();

        if ($quiz['shuffle_questions']) {
            srand($attempt['shuffle_seed']); shuffle($questions); srand();
        }

        foreach ($questions as &$q) {
            $opts = $db->prepare("SELECT * FROM question_bank_options WHERE question_id=? ORDER BY sort_order ASC");
            $opts->execute([$q['qb_id']]); $q['options'] = $opts->fetchAll();
            if ($quiz['shuffle_options']) { srand($attempt['shuffle_seed'] + $q['qb_id']); shuffle($q['options']); srand(); }
        }
        unset($q);

        // Cau tra loi da luu (neu reload)
        $saved = $db->prepare("SELECT bank_question_id, selected_option_id FROM quiz_attempt_answers WHERE attempt_id=?");
        $saved->execute([$attempt['id']]); $savedMap = [];
        foreach ($saved->fetchAll() as $r) $savedMap[$r['bank_question_id']] = $r['selected_option_id'];

        $this->render('quiz/take', ['title' => $quiz['title'], 'quiz' => $quiz, 'attempt' => $attempt, 'questions' => $questions, 'savedMap' => $savedMap], 'main');
    }

    // Nop bai
    public function submit() {
        $attempt_id = $_POST['attempt_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $attempt = $db->query("SELECT * FROM quiz_attempts WHERE id=$attempt_id AND student_id=".$_SESSION['user_id'])->fetch();
        if (!$attempt || $attempt['submitted_at']) { $this->redirect('/'); return; }

        $quiz = $db->query("SELECT * FROM quizzes WHERE id=".$attempt['quiz_id'])->fetch();
        $answers = $_POST['answers'] ?? [];

        // Xoa cau tra loi cu (neu co)
        $db->prepare("DELETE FROM quiz_attempt_answers WHERE attempt_id=?")->execute([$attempt_id]);

        $score = 0; $max = 0;
        foreach ($answers as $qb_id => $opt_id) {
            $correct = $db->query("SELECT is_correct FROM question_bank_options WHERE id=$opt_id AND question_id=$qb_id")->fetchColumn();
            $isCorrect = (int)(bool)$correct;
            $db->prepare("INSERT INTO quiz_attempt_answers (attempt_id, bank_question_id, selected_option_id, is_correct) VALUES (?,?,?,?)")->execute([$attempt_id, $qb_id, $opt_id, $isCorrect]);
            $score += $isCorrect; $max++;
        }
        // Dem cau khong tra loi
        $totalQ = (int)$db->query("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id=".$attempt['quiz_id'])->fetchColumn();
        $max    = $totalQ;
        $pct    = $max > 0 ? round($score / $max * 100, 2) : 0;
        $passed = $pct >= $quiz['pass_score'] ? 1 : 0;

        $db->prepare("UPDATE quiz_attempts SET score=?, max_score=?, passed=?, submitted_at=NOW() WHERE id=?")->execute([$pct, 100, $passed, $attempt_id]);
        $this->redirect('/quiz/result?attempt_id='.$attempt_id);
    }

    // Ket qua
    public function result() {
        $attempt_id = $_GET['attempt_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $attempt = $db->query("SELECT qa.*, q.title as quiz_title, q.pass_score, q.lesson_id FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id = q.id WHERE qa.id=$attempt_id AND qa.student_id=".$_SESSION['user_id'])->fetch();
        if (!$attempt) { $this->redirect('/'); return; }

        $answers = $db->prepare("SELECT qaa.*, qb.question_text, qbo_sel.option_text as selected_text, qbo_cor.option_text as correct_text FROM quiz_attempt_answers qaa JOIN question_bank qb ON qaa.bank_question_id = qb.id LEFT JOIN question_bank_options qbo_sel ON qaa.selected_option_id = qbo_sel.id LEFT JOIN question_bank_options qbo_cor ON qbo_cor.question_id = qb.id AND qbo_cor.is_correct = 1 WHERE qaa.attempt_id = ?");
        $answers->execute([$attempt_id]); $answers = $answers->fetchAll();

        // Tim course_id tu lesson
        $cRow = $db->query("SELECT cp.course_id FROM course_lessons cl JOIN course_chapters cc ON cl.chapter_id=cc.id JOIN course_parts cp ON cc.part_id=cp.id WHERE cl.id=".$attempt['lesson_id'])->fetch();

        $this->render('quiz/result', ['title' => 'Ket qua: '.$attempt['quiz_title'], 'attempt' => $attempt, 'answers' => $answers, 'course_id' => $cRow ? $cRow['course_id'] : 0], 'main');
    }
}
