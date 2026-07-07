<?php
// check_quizzes.php
require 'config/config.php';
require 'config/database.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== DISSECTING QUIZ 120 ===\n";
    
    // 1. Get quiz questions rows
    $stmtQQ = $db->prepare("SELECT * FROM quiz_questions WHERE quiz_id = 120 ORDER BY sort_order ASC");
    $stmtQQ->execute();
    $qqRows = $stmtQQ->fetchAll();
    echo "Total rows in quiz_questions for quiz 120: " . count($qqRows) . "\n";
    
    // 2. Try the join query
    $stmtJoin = $db->prepare("
        SELECT qq.id as qq_id, qb.id as qb_id, qb.question_text, qb.question_type 
        FROM quiz_questions qq 
        JOIN question_bank qb ON qq.bank_question_id=qb.id 
        WHERE qq.quiz_id=120 
        ORDER BY qq.sort_order ASC
    ");
    $stmtJoin->execute();
    $joinRows = $stmtJoin->fetchAll();
    echo "Total questions returned by JOIN query: " . count($joinRows) . "\n";
    
    // 3. Dump the questions to verify escaping
    foreach ($joinRows as $idx => $row) {
        echo ($idx + 1) . ". QB ID: {$row['qb_id']} | Text: " . htmlspecialchars($row['question_text']) . "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
