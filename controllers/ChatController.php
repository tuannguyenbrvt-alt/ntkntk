<?php
require_once ROOT_PATH . '/helpers/UploadHelper.php';
require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';
require_once ROOT_PATH . '/helpers/MailHelper.php';
require_once ROOT_PATH . '/helpers/ZaloHelper.php';

class ChatController extends Controller {

    // Lấy thông tin lớp học và các thread chat hiện tại của học viên
    public function getActiveThreads() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['ok' => false, 'error' => 'Chưa đăng nhập']);
            return;
        }

        $student_id = $_SESSION['user_id'];
        session_write_close(); // Giải phóng session lock sớm để tránh nghẽn luồng tải trang khác

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
            SELECT t.*, c.title as course_title, u.full_name as teacher_name,
                   (
                       SELECT COUNT(*) 
                       FROM chat_messages m 
                       WHERE m.thread_id = t.id 
                         AND m.is_read = 0 
                         AND m.sender_id != t.student_id
                         AND m.is_recalled = 0
                   ) as unread_count
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

        $user_id = $_SESSION['user_id'] ?? null;
        session_write_close(); // Giải phóng session lock sớm để tránh nghẽn luồng tải trang khác

        $db = Database::getInstance()->getConnection();

        // Lấy tin nhắn
        $stmt = $db->prepare("SELECT * FROM chat_messages WHERE thread_id = ? ORDER BY created_at ASC");
        $stmt->execute([$thread_id]);
        $messages = $stmt->fetchAll();

        // Đánh dấu các tin nhắn gửi từ admin/giáo viên là đã đọc đối với học sinh/khách
        if ($user_id !== null) {
            $db->prepare("UPDATE chat_messages SET is_read = 1 WHERE thread_id = ? AND sender_id != ?")
               ->execute([$thread_id, $user_id]);
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

        // Gửi thông báo nếu Giáo viên/Admin offline
        try {
            $stmtThread = $db->prepare("SELECT * FROM chat_threads WHERE id = ?");
            $stmtThread->execute([$thread_id]);
            $threadInfo = $stmtThread->fetch();

            if ($threadInfo) {
                $recipients = [];
                if ($threadInfo['type'] === 'student_teacher') {
                    if (!empty($threadInfo['course_id'])) {
                        // Giáo viên dạy khóa học đó
                        $stmtAuthor = $db->prepare("SELECT u.* FROM users u JOIN courses c ON c.author_id = u.id WHERE c.id = ?");
                        $stmtAuthor->execute([$threadInfo['course_id']]);
                        $recipients = $stmtAuthor->fetchAll();
                    } else {
                        // Chat chung với admin -> Gửi tất cả admin
                        $stmtAdmins = $db->query("SELECT * FROM users WHERE role IN ('admin', 'super_admin')");
                        $recipients = $stmtAdmins->fetchAll();
                    }
                } elseif ($threadInfo['type'] === 'guest_admin') {
                    // Khách vãng lai -> Gửi tất cả admin
                    $stmtAdmins = $db->query("SELECT * FROM users WHERE role IN ('admin', 'super_admin')");
                    $recipients = $stmtAdmins->fetchAll();
                }

                $lastNotified = $threadInfo['last_notified_at'];
                $cooldownPassed = ($lastNotified === null || (time() - strtotime($lastNotified)) > CHAT_NOTIFICATION_COOLDOWN);

                if ($cooldownPassed && !empty($recipients)) {
                    $notifiedAny = false;
                    $snippet = !empty($message_text) ? $message_text : "[Tệp đính kèm: " . $fileName . "]";
                    $chatUrl = APP_URL . "/admin/chat?thread_id=" . $thread_id;

                    foreach ($recipients as $recipient) {
                        $lastActive = $recipient['last_active_at'];
                        $isOffline = ($lastActive === null || (time() - strtotime($lastActive)) > CHAT_OFFLINE_THRESHOLD);

                        if ($isOffline) {
                            // 1. Gửi Email
                            MailHelper::sendChatNotification(
                                (string)$recipient['email'],
                                (string)$recipient['full_name'],
                                (string)$sender_name,
                                (string)$snippet,
                                (string)$chatUrl
                            );

                            // 2. Gửi Zalo ZNS nếu có SĐT
                            if (!empty($recipient['phone'])) {
                                $templateData = [
                                    'customer_name' => (string)$recipient['full_name'],
                                    'sender_name' => (string)$sender_name,
                                    'message_snippet' => (string)$snippet
                                ];
                                ZaloHelper::sendZNS((string)$recipient['phone'], ZALO_TEMPLATE_ID, $templateData);
                            }
                            $notifiedAny = true;
                        }
                    }

                    if ($notifiedAny) {
                        $db->prepare("UPDATE chat_threads SET last_notified_at = NOW() WHERE id = ?")->execute([$thread_id]);
                    }
                }
            }
        } catch (Throwable $e) {
            error_log("Error sending chat notification (Student -> Admin): " . $e->getMessage());
        }

        // Đảm bảo không có ký tự lạ hoặc cảnh báo PHP làm hỏng chuỗi JSON trả về
        if (ob_get_length()) {
            ob_clean();
        }
        header('Content-Type: application/json');
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
        exit;
    }

    // Thu hồi tin nhắn (trong vòng 24 giờ)
    public function recallMessage() {
        header('Content-Type: application/json');
        $message_id = (int)($_POST['message_id'] ?? 0);

        if (!$message_id) {
            echo json_encode(['ok' => false, 'error' => 'Tin nhắn không hợp lệ']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM chat_messages WHERE id = ?");
        $stmt->execute([$message_id]);
        $message = $stmt->fetch();

        if (!$message) {
            echo json_encode(['ok' => false, 'error' => 'Không tìm thấy tin nhắn']);
            return;
        }

        if ($message['is_recalled']) {
            echo json_encode(['ok' => false, 'error' => 'Tin nhắn đã được thu hồi trước đó']);
            return;
        }

        // Xác định quyền sở hữu
        $isOwner = false;
        if (isset($_SESSION['user_id'])) {
            $isOwner = ($message['sender_id'] == $_SESSION['user_id']);
        } else if (isset($_SESSION['guest_chat_thread_id'])) {
            $isOwner = ($message['sender_id'] === null && $message['thread_id'] == $_SESSION['guest_chat_thread_id']);
        }

        if (!$isOwner) {
            echo json_encode(['ok' => false, 'error' => 'Bạn không có quyền thu hồi tin nhắn này']);
            return;
        }

        // Kiểm tra thời gian gửi tin nhắn (trong vòng 24 giờ)
        $timeSent = strtotime($message['created_at']);
        if (time() - $timeSent > 24 * 3600) {
            echo json_encode(['ok' => false, 'error' => 'Đã quá 24 giờ, không thể thu hồi tin nhắn này']);
            return;
        }

        // Thực hiện xóa tệp cục bộ nếu có
        if (!empty($message['file_path'])) {
            $localFile = ROOT_PATH . '/' . $message['file_path'];
            if (file_exists($localFile)) {
                @unlink($localFile);
            }
        }

        // Thực hiện xóa tệp trên Google Drive nếu có
        if (!empty($message['file_drive_id']) && $message['file_drive_id'] !== 'error') {
            try {
                $creds = GoogleDriveHelper::loadCredentials();
                GoogleDriveHelper::deleteFile($message['file_drive_id'], $creds);
            } catch (Exception $e) {
                error_log("GoogleDrive Delete on Recall failed: " . $e->getMessage());
            }
        }

        // Cập nhật DB
        $db->prepare("
            UPDATE chat_messages 
            SET is_recalled = 1, message_text = NULL, file_name = NULL, file_path = NULL, file_drive_url = NULL, file_drive_id = NULL 
            WHERE id = ?
        ")->execute([$message_id]);

        echo json_encode(['ok' => true]);
    }

    // Lấy tổng số tin nhắn chưa đọc của học viên/khách
    public function getUnreadCount() {
        header('Content-Type: application/json');
        $user_id = $_SESSION['user_id'] ?? null;
        $guest_thread_id = $_SESSION['guest_chat_thread_id'] ?? null;
        session_write_close(); // Giải phóng session lock sớm để tránh nghẽn luồng tải trang khác

        $db = Database::getInstance()->getConnection();
        $total_unread = 0;

        if ($user_id !== null) {
            $student_id = $user_id;
            $stmt = $db->prepare("
                SELECT COUNT(*) 
                FROM chat_messages m
                JOIN chat_threads t ON m.thread_id = t.id
                WHERE t.student_id = ? 
                  AND m.is_read = 0 
                  AND m.sender_id != ?
                  AND m.is_recalled = 0
            ");
            $stmt->execute([$student_id, $student_id]);
            $total_unread = (int)$stmt->fetchColumn();
        } else if ($guest_thread_id !== null) {
            $thread_id = $guest_thread_id;
            $stmt = $db->prepare("
                SELECT COUNT(*) 
                FROM chat_messages 
                WHERE thread_id = ? 
                  AND is_read = 0 
                  AND sender_id IS NOT NULL
                  AND is_recalled = 0
            ");
            $stmt->execute([$thread_id]);
            $total_unread = (int)$stmt->fetchColumn();
        }

        echo json_encode([
            'ok' => true,
            'unread_count' => $total_unread
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
