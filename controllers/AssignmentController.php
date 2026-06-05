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
            $_SESSION['error'] = 'Vui lòng chọn file để nộp.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }
        $file = $_FILES['submission_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Lỗi upload file lên máy chủ. Mã lỗi: ' . $file['error'];
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }
        if ($file['size'] > 50 * 1024 * 1024) {
            $_SESSION['error'] = 'File quá lớn. Tối đa 50MB.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        $uploadSuccess = false;
        $fileDriveUrl = null;
        $fileDriveId = null;
        $driveErrorMsg = '';

        // Kiem tra cau hinh va folder ID Google Drive, sau do upload
        try {
            if (empty($folder_id)) {
                throw new Exception('Bài tập này chưa có Google Drive Folder ID. Giáo viên chưa cấu hình thư mục lưu bài nộp.');
            }

            require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';
            $creds = GoogleDriveHelper::loadCredentials();

            $result = GoogleDriveHelper::uploadFile($file['tmp_name'], $file['name'], $folder_id, $creds);
            $fileDriveUrl = $result['url'];
            $fileDriveId = $result['id'];
            $uploadSuccess = true;
        } catch (Exception $e) {
            $errMsg = $e->getMessage();
            if (strpos($errMsg, 'openssl') !== false) {
                $driveErrorMsg = 'Server không hỗ trợ OpenSSL. Liên hệ hosting để bật openssl.';
            } elseif (strpos($errMsg, 'HTTP 403') !== false || strpos($errMsg, '403') !== false) {
                $driveErrorMsg = 'Lỗi 403: Google Drive từ chối truy cập. Tài khoản Google (Service Account/OAuth) chưa được phân quyền vào thư mục này. Giáo viên cần chia sẻ thư mục Drive và cấp quyền Editor cho tài khoản.';
            } elseif (strpos($errMsg, 'HTTP 404') !== false || strpos($errMsg, '404') !== false) {
                $driveErrorMsg = 'Lỗi 404: Không tìm thấy thư mục Google Drive (Folder ID bị sai hoặc thư mục đã bị xóa).';
            } elseif (strpos($errMsg, 'curl') !== false || strpos($errMsg, 'cURL') !== false) {
                $driveErrorMsg = 'Lỗi kết nối mạng: Không thể kết nối từ máy chủ đến Google API.';
            } else {
                $driveErrorMsg = 'Lỗi Google Drive: ' . $errMsg;
            }
        }

        // Luu / cap nhat submission vao DB
        $exists = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
        $exists->execute([$assignment_id, $_SESSION['user_id']]);
        $row = $exists->fetch();

        if ($uploadSuccess) {
            // Tải lên thành công: lưu thông tin file Drive và xóa thông tin lỗi nếu có
            if ($row) {
                $db->prepare("UPDATE assignment_submissions SET file_name=?, file_drive_url=?, file_drive_id=?, content=NULL, status='pending', submitted_at=NOW(), score=NULL, feedback=NULL WHERE id=?")
                   ->execute([$file['name'], $fileDriveUrl, $fileDriveId, $row['id']]);
            } else {
                $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, file_name, file_drive_url, file_drive_id, content, status) VALUES (?,?,?,?,?,NULL,'pending')")
                   ->execute([$assignment_id, $_SESSION['user_id'], $file['name'], $fileDriveUrl, $fileDriveId]);
            }
            $_SESSION['success'] = 'Đã nộp file thành công lên Google Drive! Giáo viên sẽ chấm điểm sớm.';
        } else {
            // Tải lên thất bại: đánh dấu lỗi trong DB để báo cho cả học viên và giáo viên biết
            $errorDetail = 'Lỗi tải tệp tin "' . $file['name'] . '" lên Google Drive. Nguyên nhân: ' . $driveErrorMsg;
            if ($row) {
                $db->prepare("UPDATE assignment_submissions SET file_name=?, file_drive_url=NULL, file_drive_id='error', content=?, status='pending', submitted_at=NOW(), score=NULL, feedback=NULL WHERE id=?")
                   ->execute([$file['name'], $errorDetail, $row['id']]);
            } else {
                $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, file_name, file_drive_url, file_drive_id, content, status) VALUES (?,?,?,NULL,'error',?,'pending')")
                   ->execute([$assignment_id, $_SESSION['user_id'], $file['name'], $errorDetail]);
            }
            $_SESSION['error'] = 'Nộp file thất bại! Không thể upload lên Google Drive. Lỗi đã được ghi nhận và gửi đến giáo viên để khắc phục. Vui lòng thử lại sau.';
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
