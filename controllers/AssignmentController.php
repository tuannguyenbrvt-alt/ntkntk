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

    // Hoc vien nop file - CHI upload len Google Drive
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

        // Kiem tra cau hinh Google Drive
        try {
            require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';
            $creds = GoogleDriveHelper::loadCredentials();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        if (empty($folder_id)) {
            $_SESSION['error'] = 'Bai tap nay chua co Google Drive Folder ID. Vui long lien he giao vien.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        // Upload len Google Drive
        try {
            $result = GoogleDriveHelper::uploadFile($file['tmp_name'], $file['name'], $folder_id, $creds);
        } catch (Exception $e) {
            // Hien thi loi cu the de debug
            $errMsg = $e->getMessage();
            if (strpos($errMsg, 'openssl') !== false) {
                $_SESSION['error'] = 'Loi: Server khong ho tro OpenSSL. Lien he hosting de bat openssl.';
            } elseif (strpos($errMsg, 'HTTP 403') !== false || strpos($errMsg, '403') !== false) {
                $_SESSION['error'] = 'Loi 403: Service Account chua co quyen vao thu muc Drive. Hay chia se thu muc voi email Service Account va cap quyen Editor.';
            } elseif (strpos($errMsg, 'HTTP 404') !== false || strpos($errMsg, '404') !== false) {
                $_SESSION['error'] = 'Loi 404: Khong tim thay thu muc Drive (Folder ID sai). Kiem tra lai Folder ID.';
            } elseif (strpos($errMsg, 'curl') !== false || strpos($errMsg, 'cURL') !== false) {
                $_SESSION['error'] = 'Loi ket noi mang. Server khong the ket noi Google API. Kiem tra lien ket internet cua hosting.';
            } else {
                $_SESSION['error'] = 'Loi upload Google Drive: ' . $errMsg;
            }
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        // Luu submission vao DB
        $exists = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
        $exists->execute([$assignment_id, $_SESSION['user_id']]);
        $row = $exists->fetch();

        if ($row) {
            $db->prepare("UPDATE assignment_submissions SET file_name=?, file_drive_url=?, file_drive_id=?, status='pending', submitted_at=NOW(), score=NULL, feedback=NULL WHERE id=?")
               ->execute([$file['name'], $result['url'], $result['id'], $row['id']]);
        } else {
            $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, file_name, file_drive_url, file_drive_id) VALUES (?,?,?,?,?)")
               ->execute([$assignment_id, $_SESSION['user_id'], $file['name'], $result['url'], $result['id']]);
        }

        $_SESSION['success'] = 'Da nop file thanh cong len Google Drive! Giao vien se cham diem som.';
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
