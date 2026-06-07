<?php
require_once ROOT_PATH . '/helpers/UploadHelper.php';
require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';

class ChatController extends Controller {

    // Lấy thông tin lớp học và các thread chat hiện tại của học viên
    public function getActiveThreads() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['ok' => false, 'error' => 'Chưa đăng nhập']);
            return;
        }

        $student_id = $_SESSION['user_id'];
        $db = Database::getInstance()->getConnection();

        // Lấy danh sách khóa học đang theo học để hiển thị lựa chọn chat với giáo viên khóa đó
        $stmtCourses = $db->prepare("
            SELECT c.id as course_id, c.title as course_title, u.id as teacher_id, u.full_name as teacher_name
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            JOIN users u ON c.author_id = u.id
            WHERE e.student_id = ? AND e.status = 'active'
        ");
        $stmtCourses->execute([$student_id]);
        $courses = $stmtCourses->fetchAll();

        // Lấy các thread chat đã tạo của học viên
        $stmtThreads = $db->prepare("
            SELECT t.*, c.title as course_title, u.full_name as teacher_name
            FROM chat_threads t
            LEFT JOIN courses c ON t.course_id = c.id
            LEFT JOIN users u ON c.author_id = u.id
            WHERE t.student_id = ?
        ");
        $stmtThreads->execute([$student_id]);
        $threads = $stmtThreads->fetchAll();

        echo json_encode([
            'ok' => true,
            'courses' => $courses,
            'threads' => $threads
        ]);
    }

    // Khởi tạo thread chat cho khách vãng lai
    public function initGuestThread() {
        header('Content-Type: application/json');
        $name = trim($_POST['guest_name'] ?? '');
        $phone = trim($_POST['guest_phone'] ?? '');

        if (empty($name) || empty($phone)) {
            echo json_encode(['ok' => false, 'error' => 'Vui lòng điền Họ tên và Số điện thoại']);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Tìm thread cũ của số điện thoại này để hiển thị lại lịch sử chat
        $stmt = $db->prepare("SELECT id FROM chat_threads WHERE guest_phone = ? AND type = 'guest_admin' LIMIT 1");
        $stmt->execute([$phone]);
        $thread = $stmt->fetch();

        if ($thread) {
            $thread_id = $thread['id'];
            // Cập nhật lại tên khách nếu có thay đổi
            $db->prepare("UPDATE chat_threads SET guest_name = ? WHERE id = ?")->execute([$name, $thread_id]);
        } else {
            // Tạo mới thread
            $stmtInsert = $db->prepare("INSERT INTO chat_threads (guest_name, guest_phone, type) VALUES (?, ?, 'guest_admin')");
            $stmtInsert->execute([$name, $phone]);
            $thread_id = $db->lastInsertId();
        }

        $_SESSION['guest_chat_thread_id'] = $thread_id;
        $_SESSION['guest_name'] = $name;
        $_SESSION['guest_phone'] = $phone;

        echo json_encode([
            'ok' => true,
            'thread_id' => $thread_id
        ]);
    }

    // Lấy tin nhắn của một thread
    public function getMessages() {
        header('Content-Type: application/json');
        $thread_id = (int)($_GET['thread_id'] ?? 0);

        if (!$thread_id) {
            echo json_encode(['ok' => false, 'error' => 'Thread không hợp lệ']);
            return;
        }

        // Kiểm tra quyền truy cập thread
        if (!$this->checkThreadAccess($thread_id)) {
            echo json_encode(['ok' => false, 'error' => 'Bạn không có quyền truy cập cuộc trò chuyện này']);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Lấy tin nhắn
        $stmt = $db->prepare("SELECT * FROM chat_messages WHERE thread_id = ? ORDER BY created_at ASC");
        $stmt->execute([$thread_id]);
        $messages = $stmt->fetchAll();

        // Đánh dấu các tin nhắn gửi từ admin/giáo viên là đã đọc đối với học sinh/khách
        if (isset($_SESSION['user_id'])) {
            $student_id = $_SESSION['user_id'];
            $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE thread_id = ? AND sender_id != ?")
               ->execute([$thread_id, $student_id]);
        } else {
            $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE thread_id = ? AND sender_id IS NOT NULL")
               ->execute([$thread_id]);
        }

        echo json_encode([
            'ok' => true,
            'messages' => $messages
        ]);
    }

    // Gửi tin nhắn mới (có thể có tệp đính kèm)
    public function sendMessage() {
        header('Content-Type: application/json');
        $thread_id = (int)($_POST['thread_id'] ?? 0);
        $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : null;
        $message_text = trim($_POST['message_text'] ?? '');

        $db = Database::getInstance()->getConnection();

        // Học viên gửi tin nhắn
        if (isset($_SESSION['user_id'])) {
            $student_id = $_SESSION['user_id'];

            // Nếu chưa có thread_id, tìm hoặc tạo mới thread học viên - giáo viên
            if (!$thread_id) {
                if ($course_id > 0) {
                    $stmtCheck = $db->prepare("SELECT id FROM chat_threads WHERE student_id = ? AND course_id = ? LIMIT 1");
                    $stmtCheck->execute([$student_id, $course_id]);
                    $tRow = $stmtCheck->fetch();
                    if ($tRow) {
                        $thread_id = $tRow['id'];
                    } else {
                        $stmtInsert = $db->prepare("INSERT INTO chat_threads (student_id, course_id, type) VALUES (?, ?, 'student_teacher')");
                        $stmtInsert->execute([$student_id, $course_id]);
                        $thread_id = $db->lastInsertId();
                    }
                } else {
                    // Chat chung với admin
                    $stmtCheck = $db->prepare("SELECT id FROM chat_threads WHERE student_id = ? AND course_id IS NULL AND type = 'student_teacher' LIMIT 1");
                    $stmtCheck->execute([$student_id]);
                    $tRow = $stmtCheck->fetch();
                    if ($tRow) {
                        $thread_id = $tRow['id'];
                    } else {
                        $stmtInsert = $db->prepare("INSERT INTO chat_threads (student_id, course_id, type) VALUES (?, NULL, 'student_teacher')");
                        $stmtInsert->execute([$student_id]);
                        $thread_id = $db->lastInsertId();
                    }
                }
            }

            $sender_id = $student_id;
            $sender_name = $_SESSION['full_name'];
        } 
        // Khách gửi tin nhắn
        else {
            if (!$thread_id && isset($_SESSION['guest_chat_thread_id'])) {
                $thread_id = $_SESSION['guest_chat_thread_id'];
            }

            if (!$thread_id || !isset($_SESSION['guest_chat_thread_id']) || $thread_id != $_SESSION['guest_chat_thread_id']) {
                echo json_encode(['ok' => false, 'error' => 'Vui lòng điền thông tin trước khi gửi tin nhắn']);
                return;
            }

            $sender_id = null;
            $sender_name = $_SESSION['guest_name'];
        }

        // Kiểm tra tính hợp lệ của thread_id cuối cùng
        if (!$thread_id) {
            echo json_encode(['ok' => false, 'error' => 'Thread không hợp lệ']);
            return;
        }

        if (empty($message_text) && (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] === UPLOAD_ERR_NO_FILE)) {
            echo json_encode(['ok' => false, 'error' => 'Nội dung tin nhắn trống']);
            return;
        }

        $fileName = null;
        $filePath = null;
        $fileDriveUrl = null;
        $fileDriveId = null;

        // Xử lý tệp đính kèm (nếu có)
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            try {
                // 1. Sao lưu cục bộ (Local backup) vào thư mục uploads/chats/
                $allowedExtensions = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','zip','rar','txt','mp3','mp4'];
                $uploadResult = UploadHelper::uploadFile($_FILES['attachment'], 'uploads/chats/', $allowedExtensions, 20);
                
                if ($uploadResult) {
                    $filePath = $uploadResult['path'];
                    $fileName = $uploadResult['name'];

                    // 2. Đồng thời tải lên Google Drive
                    try {
                        $folder_id = defined('CHAT_DRIVE_FOLDER_ID') ? CHAT_DRIVE_FOLDER_ID : '';
                        if (empty($folder_id)) {
                            throw new Exception('Chưa cấu hình thư mục lưu trữ Drive cho Chat');
                        }

                        $creds = GoogleDriveHelper::loadCredentials();
                        $localFullPath = ROOT_PATH . '/' . $filePath;
                        
                        $driveResult = GoogleDriveHelper::uploadFile($localFullPath, $fileName, $folder_id, $creds);
                        $fileDriveUrl = $driveResult['url'];
                        $fileDriveId = $driveResult['id'];
                    } catch (Exception $ex) {
                        // Ghi nhận lỗi tải lên Google Drive, nhưng vẫn tiếp tục để lưu trữ file cục bộ
                        $fileDriveId = 'error';
                        error_log('Chat Drive Upload Error: ' . $ex->getMessage());
                    }
                }
            } catch (Exception $e) {
                echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
                return;
            }
        }

        // Lưu tin nhắn vào DB
        $stmtMsg = $db->prepare("
            INSERT INTO chat_messages (thread_id, sender_id, sender_name, message_text, file_name, file_path, file_drive_url, file_drive_id, is_read)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)
        ");
        $stmtMsg->execute([
            $thread_id,
            $sender_id,
            $sender_name,
            $message_text,
            $fileName,
            $filePath,
            $fileDriveUrl,
            $fileDriveId
        ]);

        // Cập nhật thời gian hoạt động cuối cùng của thread
        $db->prepare("UPDATE chat_threads SET updated_at = NOW() WHERE id = ?")->execute([$thread_id]);

        echo json_encode([
            'ok' => true,
            'message' => [
                'thread_id' => $thread_id,
                'sender_id' => $sender_id,
                'sender_name' => $sender_name,
                'message_text' => $message_text,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_drive_url' => $fileDriveUrl,
                'file_drive_id' => $fileDriveId,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    // Kiểm tra quyền truy cập thread của người dùng hiện tại
    private function checkThreadAccess($thread_id) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM chat_threads WHERE id = ?");
        $stmt->execute([$thread_id]);
        $thread = $stmt->fetch();

        if (!$thread) return false;

        // Nếu là học viên
        if (isset($_SESSION['user_id'])) {
            return $thread['student_id'] == $_SESSION['user_id'];
        }

        // Nếu là khách vãng lai
        if (isset($_SESSION['guest_chat_thread_id'])) {
            return $thread['id'] == $_SESSION['guest_chat_thread_id'];
        }

        return false;
    }
}
