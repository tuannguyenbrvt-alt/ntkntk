<?php
// check_quizzes.php
require 'config/config.php';
require 'config/database.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== RECENT QUIZZES IN SYSTEM ===\n";
    $stmtRecent = $db->prepare("SELECT q.id, q.lesson_id, q.title, (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as count FROM quizzes q ORDER BY q.id DESC LIMIT 15");
    $stmtRecent->execute();
    $recents = $stmtRecent->fetchAll();
    foreach ($recents as $r) {
        echo "Quiz ID: {$r['id']} | Lesson ID: {$r['lesson_id']} | Title: {$r['title']} | Question Count: {$r['count']}\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
