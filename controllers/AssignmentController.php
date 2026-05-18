<?php
class AssignmentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui long dang nhap.';
            $this->redirect('/login');
        }
    }

    // Hoc vien nop bai tu luan
    public function submitEssay() {
        $assignment_id = (int)($_POST['assignment_id'] ?? 0);
        $course_id     = (int)($_POST['course_id']     ?? 0);
        $lesson_id     = (int)($_POST['lesson_id']     ?? 0);
        $content       = trim($_POST['content'] ?? '');
        $db = Database::getInstance()->getConnection();

        if (empty($content)) {
            $_SESSION['error'] = 'Vui long nhap noi dung bai lam.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        $exists = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
        $exists->execute([$assignment_id, $_SESSION['user_id']]);
        $row = $exists->fetch();

        if ($row) {
            $db->prepare("UPDATE assignment_submissions SET content=?, status='pending', submitted_at=NOW(), score=NULL, feedback=NULL WHERE id=?")
               ->execute([$content, $row['id']]);
        } else {
            $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, content) VALUES (?,?,?)")
               ->execute([$assignment_id, $_SESSION['user_id'], $content]);
        }

        $_SESSION['success'] = 'Da nop bai thanh cong! Giao vien se cham diem som.';
        $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
    }

    // Hoc vien nop file - luu local truoc, upload Drive sau neu co cau hinh
    public function submitFile() {
        $assignment_id = (int)($_POST['assignment_id'] ?? 0);
        $course_id     = (int)($_POST['course_id']     ?? 0);
        $lesson_id     = (int)($_POST['lesson_id']     ?? 0);
        $folder_id     = trim($_POST['drive_folder_id'] ?? '');
        $db = Database::getInstance()->getConnection();

        // Kiem tra file upload
        if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['error'] = 'Vui long chon file de nop.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }
        $file = $_FILES['submission_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Loi upload file. Ma loi: ' . $file['error'];
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }
        if ($file['size'] > 50 * 1024 * 1024) {
            $_SESSION['error'] = 'File qua lon. Toi da 50MB.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        // --- Buoc 1: Luu file local truoc (dam bao khong mat bai) ---
        $uploadDir = ROOT_PATH . '/uploads/submissions/';
        if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0755, true); }

        $safeExt   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $localName = 'sub_' . $_SESSION['user_id'] . '_' . $assignment_id . '_' . time() . '.' . $safeExt;
        $localPath = $uploadDir . $localName;
        $localUrl  = null;
        $driveUrl  = null;
        $driveId   = null;

        if (move_uploaded_file($file['tmp_name'], $localPath)) {
            $localUrl = 'uploads/submissions/' . $localName; // Duong dan tuong doi
        } else {
            $_SESSION['error'] = 'Khong the luu file. Vui long thu lai.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        // --- Buoc 2: Thu upload len Google Drive (neu co cau hinh) ---
        $driveConfigFile = ROOT_PATH . '/config/google-service-account.json';
        if (!empty($folder_id) && file_exists($driveConfigFile)) {
            try {
                require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';
                $sa = file_get_contents($driveConfigFile);
                $driveResult = GoogleDriveHelper::uploadFile($localPath, $file['name'], $folder_id, $sa);
                $driveUrl = $driveResult['url'];
                $driveId  = $driveResult['id'];
                // Xoa file local sau khi upload Drive thanh cong
                @unlink($localPath);
                $localUrl = null;
            } catch (Exception $e) {
                // Drive that bai - giu file local, khong loi voi hoc vien
                // Ghi log de admin biet
                error_log('[GoogleDrive] Upload that bai cho assignment #' . $assignment_id . ': ' . $e->getMessage());
            }
        }

        // --- Buoc 3: Luu submission vao DB (du co Drive hay khong) ---
        $exists = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
        $exists->execute([$assignment_id, $_SESSION['user_id']]);
        $row = $exists->fetch();

        // Xac dinh URL hien thi: uu tien Drive, fallback local
        $displayUrl  = $driveUrl  ?? ($localUrl  ? (defined('APP_URL') ? APP_URL . '/' . $localUrl : $localUrl) : null);
        $displayId   = $driveId   ?? $localUrl;  // Luu local path lam ID de tim lai

        if ($row) {
            $db->prepare("UPDATE assignment_submissions SET file_name=?, file_drive_url=?, file_drive_id=?, status='pending', submitted_at=NOW(), score=NULL, feedback=NULL WHERE id=?")
               ->execute([$file['name'], $displayUrl, $displayId, $row['id']]);
        } else {
            $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, file_name, file_drive_url, file_drive_id) VALUES (?,?,?,?,?)")
               ->execute([$assignment_id, $_SESSION['user_id'], $file['name'], $displayUrl, $displayId]);
        }

        if ($driveUrl) {
            $_SESSION['success'] = 'Da nop file thanh cong len Google Drive!';
        } else {
            $_SESSION['success'] = 'Da nop file thanh cong! (Luu tren server)';
        }
        $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
    }

    // Hoc vien xem ket qua bai tap cua minh
    public function result() {
        $assignment_id = (int)($_GET['assignment_id'] ?? 0);
        $course_id     = (int)($_GET['course_id']     ?? 0);
        $db = Database::getInstance()->getConnection();

        $aStmt = $db->prepare("SELECT * FROM assignments WHERE id=?");
        $aStmt->execute([$assignment_id]);
        $asgn = $aStmt->fetch();

        if (!$asgn) { $this->redirect('/'); return; }

        $sStmt = $db->prepare("SELECT s.*, u_g.full_name as grader_name FROM assignment_submissions s LEFT JOIN users u_g ON s.graded_by=u_g.id WHERE s.assignment_id=? AND s.student_id=?");
        $sStmt->execute([$assignment_id, $_SESSION['user_id']]);
        $sub = $sStmt->fetch();

        $this->render('assignment/result', array(
            'title'      => 'Ket qua bai tap',
            'assignment' => $asgn,
            'submission' => $sub,
            'course_id'  => $course_id,
        ), 'main');
    }
}
