<?php
// check_quizzes.php
require 'config/config.php';
require 'config/database.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== DISSECTING QUIZ 119 ===\n";
    
    // 1. Get quiz questions rows
    $stmtQQ = $db->prepare("SELECT * FROM quiz_questions WHERE quiz_id = 119 ORDER BY sort_order ASC");
    $stmtQQ->execute();
    $qqRows = $stmtQQ->fetchAll();
    echo "Total rows in quiz_questions for quiz 119: " . count($qqRows) . "\n";
    
    // 2. Try the join query used by QuizController
    $stmtJoin = $db->prepare("
        SELECT qq.id as qq_id, qb.id as qb_id, qb.question_text, qb.question_type 
        FROM quiz_questions qq 
        JOIN question_bank qb ON qq.bank_question_id=qb.id 
        WHERE qq.quiz_id=119 
        ORDER BY qq.sort_order ASC
    ");
    $stmtJoin->execute();
    $joinRows = $stmtJoin->fetchAll();
    echo "Total questions returned by JOIN query: " . count($joinRows) . "\n";
    
    // 3. See if there are any orphaned quiz_questions (where bank_question_id does not exist in question_bank)
    $stmtOrphan = $db->prepare("
        SELECT qq.id, qq.bank_question_id 
        FROM quiz_questions qq 
        LEFT JOIN question_bank qb ON qq.bank_question_id=qb.id 
        WHERE qq.quiz_id=119 AND qb.id IS NULL
    ");
    $stmtOrphan->execute();
    $orphans = $stmtOrphan->fetchAll();
    echo "Orphaned quiz_questions: " . count($orphans) . "\n";
    
    // 4. Dump the questions to see if any are duplicate or weird
    foreach ($joinRows as $idx => $row) {
        echo ($idx + 1) . ". QB ID: {$row['qb_id']} | Text: " . mb_substr($row['question_text'], 0, 50) . "...\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
