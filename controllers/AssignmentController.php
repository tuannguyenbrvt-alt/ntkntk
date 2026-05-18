<?php
class AssignmentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) { $_SESSION['error'] = 'Vui long dang nhap.'; $this->redirect('/login'); }
    }

    // Hoc vien nop bai tu luan
    public function submitEssay() {
        $assignment_id = $_POST['assignment_id'] ?? 0;
        $course_id     = $_POST['course_id']     ?? 0;
        $content       = trim($_POST['content']  ?? '');
        $db = Database::getInstance()->getConnection();

        if (empty($content)) { $_SESSION['error'] = 'Vui long nhap noi dung bai lam.'; $this->redirect('/learning?course_id='.$course_id.'&lesson_id='.$_POST['lesson_id']); return; }

        // Kiem tra da nop chua (neu da nop thi cap nhat)
        $exists = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
        $exists->execute([$assignment_id, $_SESSION['user_id']]); $row = $exists->fetch();

        if ($row) {
            $db->prepare("UPDATE assignment_submissions SET content=?, status='pending', submitted_at=NOW(), score=NULL, feedback=NULL WHERE id=?")->execute([$content, $row['id']]);
        } else {
            $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, content) VALUES (?,?,?)")->execute([$assignment_id, $_SESSION['user_id'], $content]);
        }
        $_SESSION['success'] = 'Da nop bai thanh cong! Giao vien se cham diem som.';
        $this->redirect('/assignment/result?assignment_id='.$assignment_id.'&course_id='.$course_id);
    }

    // Hoc vien nop file (Google Drive)
    public function submitFile() {
        $assignment_id = $_POST['assignment_id'] ?? 0;
        $course_id     = $_POST['course_id']     ?? 0;
        $lesson_id     = $_POST['lesson_id']     ?? 0;
        $folder_id     = $_POST['drive_folder_id'] ?? '';
        $db = Database::getInstance()->getConnection();

        if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['error'] = 'Vui long chon file de nop.';
            $this->redirect('/learning?course_id='.$course_id.'&lesson_id='.$lesson_id); return;
        }
        $file = $_FILES['submission_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) { $_SESSION['error'] = 'Loi upload file. Ma loi: '.$file['error']; $this->redirect('/learning?course_id='.$course_id.'&lesson_id='.$lesson_id); return; }
        if ($file['size'] > 50 * 1024 * 1024) { $_SESSION['error'] = 'File qua lon. Toi da 50MB.'; $this->redirect('/learning?course_id='.$course_id.'&lesson_id='.$lesson_id); return; }

        // Upload len Google Drive
        require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';
        try {
            $sa = GoogleDriveHelper::loadServiceAccount();
            $result = GoogleDriveHelper::uploadFile($file['tmp_name'], $file['name'], $folder_id, $sa);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Loi upload len Google Drive: ' . $e->getMessage();
            $this->redirect('/learning?course_id='.$course_id.'&lesson_id='.$lesson_id); return;
        }

        $exists = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
        $exists->execute([$assignment_id, $_SESSION['user_id']]); $row = $exists->fetch();

        if ($row) {
            $db->prepare("UPDATE assignment_submissions SET file_name=?, file_drive_url=?, file_drive_id=?, status='pending', submitted_at=NOW(), score=NULL, feedback=NULL WHERE id=?")->execute([$file['name'], $result['url'], $result['id'], $row['id']]);
        } else {
            $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, file_name, file_drive_url, file_drive_id) VALUES (?,?,?,?,?)")->execute([$assignment_id, $_SESSION['user_id'], $file['name'], $result['url'], $result['id']]);
        }
        $_SESSION['success'] = 'Da nop file thanh cong! Giao vien se cham diem soon.';
        $this->redirect('/assignment/result?assignment_id='.$assignment_id.'&course_id='.$course_id);
    }

    // Hoc vien xem ket qua
    public function result() {
        $assignment_id = $_GET['assignment_id'] ?? 0;
        $course_id     = $_GET['course_id']     ?? 0;
        $db = Database::getInstance()->getConnection();

        $asgn = $db->prepare("SELECT * FROM assignments WHERE id=?"); $asgn->execute([$assignment_id]); $asgn = $asgn->fetch();
        $sub  = $db->prepare("SELECT s.*, u_g.full_name as grader_name FROM assignment_submissions s LEFT JOIN users u_g ON s.graded_by = u_g.id WHERE s.assignment_id=? AND s.student_id=?");
        $sub->execute([$assignment_id, $_SESSION['user_id']]); $sub = $sub->fetch();

        $this->render('assignment/result', ['title' => 'Ket qua bai tap', 'assignment' => $asgn, 'submission' => $sub, 'course_id' => $course_id], 'main');
    }
}
