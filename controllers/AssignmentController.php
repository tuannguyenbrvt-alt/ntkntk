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

        // Kiem tra du lieu upload
        if (!isset($_FILES['submission_files']) || !is_array($_FILES['submission_files']['name'])) {
            $_SESSION['error'] = 'Vui lòng chọn ít nhất một file để nộp.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        $files = [];
        $count = count($_FILES['submission_files']['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['submission_files']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $files[] = [
                    'name' => $_FILES['submission_files']['name'][$i],
                    'type' => $_FILES['submission_files']['type'][$i],
                    'tmp_name' => $_FILES['submission_files']['tmp_name'][$i],
                    'error' => $_FILES['submission_files']['error'][$i],
                    'size' => $_FILES['submission_files']['size'][$i]
                ];
            }
        }

        if (empty($files)) {
            $_SESSION['error'] = 'Vui lòng chọn file để nộp.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        // Kiem tra loi va kich thuoc truoc khi upload bat ky file nao
        foreach ($files as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Lỗi upload file ' . htmlspecialchars($file['name']) . '. Mã lỗi: ' . $file['error'];
                $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
                return;
            }
            if ($file['size'] > 50 * 1024 * 1024) {
                $_SESSION['error'] = 'File ' . htmlspecialchars($file['name']) . ' quá lớn. Tối đa 50MB.';
                $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
                return;
            }
        }

        // Lay hoac tao submission
        $exists = $db->prepare("SELECT id FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
        $exists->execute([$assignment_id, $_SESSION['user_id']]);
        $row = $exists->fetch();

        if ($row) {
            $submission_id = $row['id'];
            // Reset trang thai ve pending khi co file moi duoc nop
            $db->prepare("UPDATE assignment_submissions SET status='pending', submitted_at=NOW(), score=NULL, feedback=NULL, graded_at=NULL, graded_by=NULL WHERE id=?")
               ->execute([$submission_id]);
        } else {
            $db->prepare("INSERT INTO assignment_submissions (assignment_id, student_id, content, status) VALUES (?,?,NULL,'pending')")
               ->execute([$assignment_id, $_SESSION['user_id']]);
            $submission_id = $db->lastInsertId();
        }

        $creds = null;
        try {
            require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';
            $creds = GoogleDriveHelper::loadCredentials();
        } catch (Exception $e) {
            // Se log loi trong qua trinh upload
        }

        $uploadedCount = 0;
        $failedCount = 0;

        foreach ($files as $file) {
            $uploadSuccess = false;
            $fileDriveUrl = null;
            $fileDriveId = null;
            $driveErrorMsg = '';

            try {
                if (empty($folder_id)) {
                    throw new Exception('Bài tập này chưa có Google Drive Folder ID. Giáo viên chưa cấu hình thư mục lưu bài nộp.');
                }
                if (!$creds) {
                    throw new Exception('Chưa cấu hình Google Drive.');
                }

                $result = GoogleDriveHelper::uploadFile($file['tmp_name'], $file['name'], $folder_id, $creds);
                $fileDriveUrl = $result['url'];
                $fileDriveId = $result['id'];
                $uploadSuccess = true;
            } catch (Exception $e) {
                $errMsg = $e->getMessage();
                if (strpos($errMsg, 'openssl') !== false) {
                    $driveErrorMsg = 'Server không hỗ trợ OpenSSL.';
                } elseif (strpos($errMsg, '403') !== false) {
                    $driveErrorMsg = 'Lỗi 403: Google Drive từ chối truy cập. Tài khoản Google chưa được phân quyền vào thư mục này.';
                } elseif (strpos($errMsg, '404') !== false) {
                    $driveErrorMsg = 'Lỗi 404: Không tìm thấy thư mục Google Drive (Folder ID sai hoặc thư mục đã bị xóa).';
                } elseif (strpos($errMsg, 'curl') !== false || strpos($errMsg, 'cURL') !== false) {
                    $driveErrorMsg = 'Lỗi kết nối mạng: Không thể kết nối đến Google API.';
                } else {
                    $driveErrorMsg = 'Lỗi Google Drive: ' . $errMsg;
                }
            }

            if ($uploadSuccess) {
                $db->prepare("INSERT INTO assignment_submission_files (submission_id, file_name, file_drive_url, file_drive_id, content, status) VALUES (?,?,?,?,NULL,'pending')")
                   ->execute([$submission_id, $file['name'], $fileDriveUrl, $fileDriveId]);
                $uploadedCount++;
            } else {
                $errorDetail = 'Lỗi tải tệp tin "' . $file['name'] . '" lên Google Drive. Nguyên nhân: ' . $driveErrorMsg;
                $db->prepare("INSERT INTO assignment_submission_files (submission_id, file_name, file_drive_url, file_drive_id, content, status) VALUES (?,?,NULL,'error',?,'pending')")
                   ->execute([$submission_id, $file['name'], $errorDetail]);
                $failedCount++;
            }
        }

        if ($failedCount === 0) {
            $_SESSION['success'] = 'Đã nộp thành công ' . $uploadedCount . ' file bài làm! Giáo viên sẽ chấm điểm sớm.';
        } elseif ($uploadedCount === 0) {
            $_SESSION['error'] = 'Nộp file thất bại! Không thể upload lên Google Drive. Lỗi đã được ghi nhận.';
        } else {
            $_SESSION['warning'] = 'Đã tải lên thành công ' . $uploadedCount . ' file, nhưng có ' . $failedCount . ' file gặp lỗi upload.';
        }

        $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
    }

    // Hoc vien tu xoa file nop cua minh neu chua duoc cham
    public function deleteFile() {
        $file_id   = (int)($_POST['file_id']   ?? 0);
        $course_id = (int)($_POST['course_id'] ?? 0);
        $lesson_id = (int)($_POST['lesson_id'] ?? 0);
        $db = Database::getInstance()->getConnection();

        // Kiem tra xem file co thuoc ve dung hoc vien hien tai va chua duoc cham khong
        $stmt = $db->prepare("
            SELECT f.*, s.student_id 
            FROM assignment_submission_files f
            JOIN assignment_submissions s ON f.submission_id = s.id
            WHERE f.id = ? AND s.student_id = ?
        ");
        $stmt->execute([$file_id, $_SESSION['user_id']]);
        $file = $stmt->fetch();

        if (!$file) {
            $_SESSION['error'] = 'Không tìm thấy file bài làm hoặc bạn không có quyền xóa.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        if ($file['status'] === 'graded') {
            $_SESSION['error'] = 'Không thể xóa file đã được giáo viên chấm điểm.';
            $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
            return;
        }

        // Xoa file tren Google Drive neu co
        if (!empty($file['file_drive_id']) && $file['file_drive_id'] !== 'error') {
            try {
                require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';
                $creds = GoogleDriveHelper::loadCredentials();
                GoogleDriveHelper::deleteFile($file['file_drive_id'], $creds);
            } catch (Exception $e) {
                error_log("Failed to delete Google Drive file: " . $e->getMessage());
            }
        }

        // Xoa trong DB
        $db->prepare("DELETE FROM assignment_submission_files WHERE id = ?")->execute([$file_id]);

        // Cap nhat lai trang thai cua submission tong
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM assignment_submission_files WHERE submission_id = ?");
        $checkStmt->execute([$file['submission_id']]);
        $remainingFiles = (int)$checkStmt->fetchColumn();

        if ($remainingFiles === 0) {
            $db->prepare("UPDATE assignment_submissions SET status='pending', score=NULL, feedback=NULL WHERE id=?")
               ->execute([$file['submission_id']]);
        } else {
            $pendingStmt = $db->prepare("SELECT COUNT(*) FROM assignment_submission_files WHERE submission_id = ? AND status = 'pending'");
            $pendingStmt->execute([$file['submission_id']]);
            $pendingFiles = (int)$pendingStmt->fetchColumn();
            if ($pendingFiles === 0) {
                // Tinh lai diem trung binh
                $avgStmt = $db->prepare("SELECT AVG(score) FROM assignment_submission_files WHERE submission_id = ? AND score IS NOT NULL");
                $avgStmt->execute([$file['submission_id']]);
                $avgScore = $avgStmt->fetchColumn();
                $db->prepare("UPDATE assignment_submissions SET status='graded', score=? WHERE id=?")
                   ->execute([$avgScore !== null ? (float)$avgScore : null, $file['submission_id']]);
            } else {
                $db->prepare("UPDATE assignment_submissions SET status='pending', score=NULL WHERE id=?")
                   ->execute([$file['submission_id']]);
            }
        }

        $_SESSION['success'] = 'Đã xóa file bài làm thành công.';
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

        $subFiles = [];
        if ($sub && $asgn['type'] === 'file') {
            $sfStmt = $db->prepare("SELECT * FROM assignment_submission_files WHERE submission_id = ? ORDER BY created_at ASC");
            $sfStmt->execute([$sub['id']]);
            $subFiles = $sfStmt->fetchAll();
        }

        $this->render('assignment/result', array(
            'title'      => 'Ket qua bai tap',
            'assignment' => $asgn,
            'submission' => $sub,
            'subFiles'   => $subFiles,
            'course_id'  => $course_id,
        ), 'main');
    }
}
