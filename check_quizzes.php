<?php
// check_quizzes.php
require 'config/config.php';
require 'config/database.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== LESSON ITEMS FOR LESSON 284 ===\n";
    $stmtItems = $db->prepare("SELECT * FROM lesson_items WHERE lesson_id = 284 ORDER BY sort_order ASC");
    $stmtItems->execute();
    $items = $stmtItems->fetchAll();
    foreach ($items as $item) {
        echo "Item ID: {$item['id']} | Type: {$item['type']} | Content: {$item['content']}\n";
    }
    
    echo "\n=== QUIZZES IN DB FOR LESSON 284 ===\n";
    $stmtQ = $db->prepare("SELECT id, title, (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as count FROM quizzes q WHERE lesson_id = 284");
    $stmtQ->execute();
    $quizzes = $stmtQ->fetchAll();
    foreach ($quizzes as $q) {
        echo "Quiz ID: {$q['id']} | Title: {$q['title']} | Question Count: {$q['count']}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
