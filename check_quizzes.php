<?php
// check_quizzes.php
require 'config/config.php';
require 'config/database.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== CLEANING UP QUIZ 119 ===\n";
    
    // 1. Delete from lesson_items
    $stmt1 = $db->prepare("DELETE FROM lesson_items WHERE type = 'quiz' AND content = '119'");
    $stmt1->execute();
    echo "Deleted from lesson_items: " . $stmt1->rowCount() . " rows.\n";
    
    // 2. Delete from quiz_questions
    $stmt2 = $db->prepare("DELETE FROM quiz_questions WHERE quiz_id = 119");
    $stmt2->execute();
    echo "Deleted from quiz_questions: " . $stmt2->rowCount() . " rows.\n";
    
    // 3. Delete from quizzes
    $stmt3 = $db->prepare("DELETE FROM quizzes WHERE id = 119");
    $stmt3->execute();
    echo "Deleted from quizzes: " . $stmt3->rowCount() . " rows.\n";
    
    echo "\n=== REMAINING QUIZZES FOR LESSON 283 ===\n";
    $stmtQuizzes = $db->prepare("SELECT * FROM quizzes WHERE lesson_id = 283 ORDER BY id DESC");
    $stmtQuizzes->execute();
    $quizzes = $stmtQuizzes->fetchAll();
    foreach ($quizzes as $quiz) {
        echo "Quiz ID: {$quiz['id']} | Title: {$quiz['title']}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
