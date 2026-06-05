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
        $folder_id = trim($_POST['drive_folder_id'] ?? '');
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO assignments (lesson_id, title, description, type, max_score, due_date, drive_folder_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$lesson_id, $_POST['title'] ?? 'Bai tap', $_POST['description'] ?? '', $type, (float)($_POST['max_score'] ?? 10), !empty($_POST['due_date']) ? $_POST['due_date'] : null, $folder_id ?: null]);
        $asgn_id = $db->lastInsertId();
        $itemType = ($type === 'essay') ? 'assignment_essay' : 'assignment_file';
        $db->prepare("INSERT INTO lesson_items (lesson_id, type, content) VALUES (?, ?, ?)")->execute([$lesson_id, $itemType, $asgn_id]);
        $_SESSION['success'] = 'Da tao bai tap thanh cong!';
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function update() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title     = $_POST['title']     ?? 'Bai tap';
        $desc      = $_POST['description'] ?? '';
        $type      = in_array($_POST['type'] ?? '', ['essay','file']) ? $_POST['type'] : 'essay';
        $max_score = (float)($_POST['max_score'] ?? 10);
        $due_date  = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $folder_id = trim($_POST['drive_folder_id'] ?? '');
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("UPDATE assignments SET title = ?, description = ?, type = ?, max_score = ?, due_date = ?, drive_folder_id = ? WHERE id = ?");
        $stmt->execute([$title, $desc, $type, $max_score, $due_date, $folder_id ?: null, $id]);
        
        $itemType = ($type === 'essay') ? 'assignment_essay' : 'assignment_file';
        $db->prepare("UPDATE lesson_items SET type = ? WHERE (type = 'assignment_essay' OR type = 'assignment_file') AND content = ?")
           ->execute([$itemType, $id]);
        
        $_SESSION['success'] = 'Cap nhat bai tap thanh cong!';
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
        // Neu den tu trang pending, tra ve pending
        if (!empty($_POST['from_pending'])) {
            $this->redirect('/admin/assignments/pending');
        } else {
            $this->redirect('/admin/assignments/submissions?assignment_id='.$assignment_id.'&course_id='.$course_id);
        }
    }

    public function pending() {
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];
        $role   = $_SESSION['role'];

        $query = "
            SELECT s.*, 
                   u.full_name as student_name, 
                   a.title as assignment_title, 
                   a.type as assignment_type,
                   c.title as course_title,
                   c.id as course_id
            FROM assignment_submissions s
            JOIN users u ON s.student_id = u.id
            JOIN assignments a ON s.assignment_id = a.id
            JOIN course_lessons cl ON a.lesson_id = cl.id
            JOIN course_chapters cc ON cl.chapter_id = cc.id
            JOIN course_parts cp ON cc.part_id = cp.id
            JOIN courses c ON cp.course_id = c.id
            WHERE s.status = 'pending'
        ";

        if ($role !== 'super_admin') {
            $query .= " AND c.author_id = ?";
        }
        $query .= " ORDER BY s.submitted_at ASC";

        $stmt = $db->prepare($query);
        if ($role !== 'super_admin') {
            $stmt->execute([$userId]);
        } else {
            $stmt->execute();
        }
        $pendingSubs = $stmt->fetchAll();

        $this->render('admin/assignments/pending', ['title' => 'Chấm bài tập', 'pendingSubs' => $pendingSubs], 'admin');
    }

    public function setupDrive() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin','admin'])) {
            $this->redirect('/login');
            return;
        }

        $_SESSION['oauth_purpose'] = 'drive_setup';

        $params = [
            'client_id'       => GOOGLE_CLIENT_ID,
            'redirect_uri'    => GOOGLE_REDIRECT_URI,
            'response_type'   => 'code',
            'scope'           => 'https://www.googleapis.com/auth/drive',
            'access_type'     => 'offline',
            'prompt'          => 'consent',
            'state'           => bin2hex(random_bytes(16))
        ];

        $_SESSION['oauth2state'] = $params['state'];
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        $this->redirect($url);
    }
}
