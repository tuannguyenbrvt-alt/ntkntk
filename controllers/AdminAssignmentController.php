<?php
class AdminAssignmentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin','admin'])) {
            $this->redirect('/login');
        }
    }

    public function store() {
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $type      = in_array($_POST['type'] ?? '', ['essay','file']) ? $_POST['type'] : 'essay';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO assignments (lesson_id, title, description, type, max_score, due_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$lesson_id, $_POST['title'] ?? 'Bai tap', $_POST['description'] ?? '', $type, (float)($_POST['max_score'] ?? 10), !empty($_POST['due_date']) ? $_POST['due_date'] : null]);
        $asgn_id = $db->lastInsertId();
        $itemType = ($type === 'essay') ? 'assignment_essay' : 'assignment_file';
        $db->prepare("INSERT INTO lesson_items (lesson_id, type, content) VALUES (?, ?, ?)")->execute([$lesson_id, $itemType, $asgn_id]);
        $_SESSION['success'] = 'Da tao bai tap thanh cong!';
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function delete() {
        $id = $_POST['id'] ?? 0; $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $a = $db->prepare("SELECT type FROM assignments WHERE id = ?"); $a->execute([$id]); $row = $a->fetch();
        if ($row) $db->prepare("DELETE FROM lesson_items WHERE type = ? AND content = ?")->execute(['assignment_' . $row['type'], $id]);
        $db->prepare("DELETE FROM assignments WHERE id = ?")->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function submissions() {
        $assignment_id = $_GET['assignment_id'] ?? 0; $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $asgn = $db->prepare("SELECT * FROM assignments WHERE id = ?"); $asgn->execute([$assignment_id]); $asgn = $asgn->fetch();
        if (!$asgn) { $this->redirect('/admin/courses'); return; }
        $subs = $db->prepare("SELECT s.*, u.full_name, u.phone FROM assignment_submissions s JOIN users u ON s.student_id = u.id WHERE s.assignment_id = ? ORDER BY s.submitted_at DESC");
        $subs->execute([$assignment_id]); $submissions = $subs->fetchAll();
        $this->render('admin/assignments/submissions', ['title' => 'Bai nop: '.$asgn['title'], 'assignment' => $asgn, 'submissions' => $submissions, 'course_id' => $course_id], 'admin');
    }

    public function grade() {
        $sub_id = $_GET['sub_id'] ?? 0; $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $sub = $db->prepare("SELECT s.*, u.full_name, u.phone, a.title as asgn_title, a.max_score, a.type, a.id as assignment_id FROM assignment_submissions s JOIN users u ON s.student_id = u.id JOIN assignments a ON s.assignment_id = a.id WHERE s.id = ?");
        $sub->execute([$sub_id]); $sub = $sub->fetch();
        if (!$sub) { $this->redirect('/admin/courses'); return; }
        $this->render('admin/assignments/grade', ['title' => 'Cham diem: '.$sub['full_name'], 'sub' => $sub, 'course_id' => $course_id], 'admin');
    }

    public function storeGrade() {
        $sub_id = $_POST['sub_id'] ?? 0; $assignment_id = $_POST['assignment_id'] ?? 0; $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $db->prepare("UPDATE assignment_submissions SET score=?, feedback=?, status='graded', graded_at=NOW(), graded_by=? WHERE id=?")->execute([(float)($_POST['score'] ?? 0), $_POST['feedback'] ?? '', $_SESSION['user_id'], $sub_id]);
        $_SESSION['success'] = 'Da cham diem thanh cong!';
        $this->redirect('/admin/assignments/submissions?assignment_id='.$assignment_id.'&course_id='.$course_id);
    }
}
