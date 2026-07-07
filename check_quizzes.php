<?php
// check_quizzes.php
require 'config/config.php';
require 'config/database.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== LESSON ITEMS FOR LESSON 283 ===\n";
    $stmtItems = $db->prepare("SELECT * FROM lesson_items WHERE lesson_id = 283 ORDER BY sort_order ASC");
    $stmtItems->execute();
    $items = $stmtItems->fetchAll();
    foreach ($items as $item) {
        echo "Item ID: {$item['id']} | Type: {$item['type']} | Content: {$item['content']} | Sort Order: {$item['sort_order']}\n";
    }
    
    echo "\n=== QUIZZES FOR LESSON 283 ===\n";
    $stmtQuizzes = $db->prepare("SELECT * FROM quizzes WHERE lesson_id = 283 ORDER BY id DESC");
    $stmtQuizzes->execute();
    $quizzes = $stmtQuizzes->fetchAll();
    foreach ($quizzes as $quiz) {
        $stmtCount = $db->prepare("SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = ?");
        $stmtCount->execute([$quiz['id']]);
        $count = $stmtCount->fetchColumn();
        echo "Quiz ID: {$quiz['id']} | Title: {$quiz['title']} | Question Count in DB: $count | Created At: {$quiz['created_at']}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
