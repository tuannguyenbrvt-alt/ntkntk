<?php
require 'config/config.php';
require 'config/database.php';
$db = Database::getInstance()->getConnection();
$sid = 1;
try {
    $courses = $db->prepare("SELECT c.*, e.status as enroll_status, (SELECT COUNT(*) FROM course_lessons cl2 JOIN course_chapters cc2 ON cl2.chapter_id=cc2.id JOIN course_parts cp2 ON cc2.part_id=cp2.id WHERE cp2.course_id=c.id) as total_lessons, (SELECT COUNT(*) FROM course_progress WHERE student_id=? AND lesson_id IN (SELECT cl3.id FROM course_lessons cl3 JOIN course_chapters cc3 ON cl3.chapter_id=cc3.id JOIN course_parts cp3 ON cc3.part_id=cp3.id WHERE cp3.course_id=c.id) AND is_completed=1) as done_lessons FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.student_id=? AND e.status='active'");
    $courses->execute([$sid, $sid]);
    echo "Courses OK\n";

    $quizResults = $db->prepare("SELECT qa.*, q.title as quiz_title, cl.title as lesson_title FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id=q.id JOIN course_lessons cl ON q.lesson_id=cl.id WHERE qa.student_id=? AND qa.submitted_at IS NOT NULL ORDER BY qa.submitted_at DESC LIMIT 20");
    $quizResults->execute([$sid]);
    echo "Quiz OK\n";

    $asgResults = $db->prepare("SELECT s.*, a.title as asgn_title, a.max_score, a.type, cl.title as lesson_title FROM assignment_submissions s JOIN assignments a ON s.assignment_id=a.id JOIN course_lessons cl ON a.lesson_id=cl.id WHERE s.student_id=? ORDER BY s.submitted_at DESC LIMIT 20");
    $asgResults->execute([$sid]);
    echo "Asg OK\n";
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
