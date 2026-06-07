<?php
require_once ROOT_PATH . '/helpers/UploadHelper.php';
require_once ROOT_PATH . '/helpers/GoogleDriveHelper.php';

class AdminChatController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'Từ chối truy cập']);
                exit;
            }
            $this->redirect('/login');
            exit;
        }
    }

    // Trang chủ giao diện quản lý chat của Admin/Giáo viên
    public function index() {
        $db = Database::getInstance()->getConnection();
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        // Lấy danh sách các thread chat kèm thông tin tin nhắn chưa đọc
        if ($role === 'super_admin') {
            $stmt = $db->query("
                SELECT t.*, 
                       u.full_name as student_name, u.avatar as student_avatar, u.phone as student_phone,
                       c.title as course_title,
                       (
                           SELECT COUNT(*) 
                           FROM chat_messages m 
                           WHERE m.thread_id = t.id 
                             AND m.is_read = 0 
                             AND (m.sender_id IS NULL OR m.sender_id IN (SELECT id FROM users WHERE role = 'student'))
                       ) as unread_count,
                       (
                           SELECT message_text 
                           FROM chat_messages m 
                           WHERE m.thread_id = t.id 
                           ORDER BY m.created_at DESC LIMIT 1
                       ) as last_message,
                       (
                           SELECT file_name 
                           FROM chat_messages m 
                           WHERE m.thread_id = t.id 
                           ORDER BY m.created_at DESC LIMIT 1
                       ) as last_file
                FROM chat_threads t
                LEFT JOIN users u ON t.student_id = u.id
                LEFT JOIN courses c ON t.course_id = c.id
                ORDER BY t.updated_at DESC
            ");
            $threads = $stmt->fetchAll();
        } else {
            // Đối với giáo viên: chỉ xem được thread chat liên quan đến các khóa học họ dạy, hoặc chat của khách vãng lai
            $stmt = $db->prepare("
                SELECT t.*, 
                       u.full_name as student_name, u.avatar as student_avatar, u.phone as student_phone,
                       c.title as course_title,
                       (
                           SELECT COUNT(*) 
                           FROM chat_messages m 
                           WHERE m.thread_id = t.id 
                             AND m.is_read = 0 
                             AND (m.sender_id IS NULL OR m.sender_id IN (SELECT id FROM users WHERE role = 'student'))
                       ) as unread_count,
                       (
                           SELECT message_text 
                           FROM chat_messages m 
                           WHERE m.thread_id = t.id 
                           ORDER BY m.created_at DESC LIMIT 1
                       ) as last_message,
                       (
                           SELECT file_name 
                           FROM chat_messages m 
                           WHERE m.thread_id = t.id 
                           ORDER BY m.created_at DESC LIMIT 1
                       ) as last_file
                FROM chat_threads t
                LEFT JOIN users u ON t.student_id = u.id
                LEFT JOIN courses c ON t.course_id = c.id
                WHERE t.type = 'guest_admin' OR c.author_id = ?
                ORDER BY t.updated_at DESC
            ");
            $stmt->execute([$user_id]);
            $threads = $stmt->fetchAll();
        }

        $this->render('admin/chat/index', [
            'title' => 'Trò chuyện trực tuyến',
            'threads' => $threads
        ], 'admin');
    }

    // Lấy tin nhắn của một thread chỉ định (phía Admin)
    public function getMessages() {
        header('Content-Type: application/json');
        $thread_id = (int)($_GET['thread_id'] ?? 0);

        if (!$thread_id) {
            echo json_encode(['ok' => false, 'error' => 'Thread không hợp lệ']);
            return;
        }

        if (!$this->checkAdminThreadAccess($thread_id)) {
            echo json_encode(['ok' => false, 'error' => 'Không có quyền truy cập thread này']);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Lấy tin nhắn
        $stmt = $db->prepare("
            SELECT m.*, u.role as sender_role 
            FROM chat_messages m
            LEFT JOIN users u ON m.sender_id = u.id
            WHERE m.thread_id = ? 
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$thread_id]);
        $messages = $stmt->fetchAll();

        // Đánh dấu các tin nhắn gửi từ học viên/khách là đã đọc
        $db->prepare("
            UPDATE chat_messages 
            SET is_read = 1 
            WHERE thread_id = ? 
              AND (sender_id IS NULL OR sender_id IN (SELECT id FROM users WHERE role = 'student'))
        ")->execute([$thread_id]);

        echo json_encode([
            'ok' => true,
            'messages' => $messages
        ]);
    }

    // Gửi tin nhắn phản hồi từ Admin/Giáo viên
    public function sendMessage() {
        header('Content-Type: application/json');
        $thread_id = (int)($_POST['thread_id'] ?? 0);
        $message_text = trim($_POST['message_text'] ?? '');

        if (!$thread_id) {
            echo json_encode(['ok' => false, 'error' => 'Thread không hợp lệ']);
            return;
        }

        if (!$this->checkAdminThreadAccess($thread_id)) {
            echo json_encode(['ok' => false, 'error' => 'Không có quyền gửi tin nhắn đến thread này']);
            return;
        }

        if (empty($message_text) && (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] === UPLOAD_ERR_NO_FILE)) {
            echo json_encode(['ok' => false, 'error' => 'Nội dung tin nhắn trống']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $sender_id = $_SESSION['user_id'];
        $sender_name = $_SESSION['full_name'];

        $fileName = null;
        $filePath = null;
        $fileDriveUrl = null;
        $fileDriveId = null;

        // Xử lý tệp đính kèm gửi từ admin
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            try {
                // 1. Lưu cục bộ
                $allowedExtensions = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','zip','rar','txt','mp3','mp4'];
                $uploadResult = UploadHelper::uploadFile($_FILES['attachment'], 'uploads/chats/', $allowedExtensions, 20);
                
                if ($uploadResult) {
                    $filePath = $uploadResult['path'];
                    $fileName = $uploadResult['name'];

                    // 2. Tải lên Google Drive
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
                        $fileDriveId = 'error';
                        error_log('Admin Chat Drive Upload Error: ' . $ex->getMessage());
                    }
                }
            } catch (Exception $e) {
                echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
                return;
            }
        }

        // Lưu tin nhắn
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

        // Cập nhật thời gian hoạt động của thread
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

    // Thu hồi tin nhắn Admin (trong vòng 24 giờ)
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

        // Đảm bảo tin nhắn được gửi bởi chính admin/giáo viên đang đăng nhập
        if ($message['sender_id'] != $_SESSION['user_id']) {
            echo json_encode(['ok' => false, 'error' => 'Bạn không có quyền thu hồi tin nhắn của người khác']);
            return;
        }

        // Kiểm tra thời gian (trong vòng 24 giờ)
        $timeSent = strtotime($message['created_at']);
        if (time() - $timeSent > 24 * 3600) {
            echo json_encode(['ok' => false, 'error' => 'Đã quá 24 giờ, không thể thu hồi tin nhắn này']);
            return;
        }

        // Thực hiện xóa tệp cục bộ
        if (!empty($message['file_path'])) {
            $localFile = ROOT_PATH . '/' . $message['file_path'];
            if (file_exists($localFile)) {
                @unlink($localFile);
            }
        }

        // Thực hiện xóa tệp trên Drive
        if (!empty($message['file_drive_id']) && $message['file_drive_id'] !== 'error') {
            try {
                $creds = GoogleDriveHelper::loadCredentials();
                GoogleDriveHelper::deleteFile($message['file_drive_id'], $creds);
            } catch (Exception $e) {
                error_log("Admin GoogleDrive Delete on Recall failed: " . $e->getMessage());
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

    // Tìm kiếm học viên để chủ động nhắn tin
    public function searchStudents() {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');

        if (strlen($q) < 2) {
            echo json_encode(['ok' => true, 'students' => []]);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $term = "%{$q}%";

        // Query tìm kiếm học viên
        if ($role === 'super_admin') {
            $stmt = $db->prepare("
                SELECT id, username, full_name, phone, email, avatar
                FROM users
                WHERE role = 'student'
                  AND (full_name LIKE ? OR phone LIKE ? OR email LIKE ? OR username LIKE ?)
                LIMIT 15
            ");
            $stmt->execute([$term, $term, $term, $term]);
        } else {
            // Đối với giáo viên thường: chỉ tìm học viên đăng ký các khóa của mình
            $stmt = $db->prepare("
                SELECT DISTINCT u.id, u.username, u.full_name, u.phone, u.email, u.avatar
                FROM users u
                JOIN enrollments e ON u.id = e.student_id
                JOIN courses c ON e.course_id = c.id
                WHERE u.role = 'student'
                  AND c.author_id = ?
                  AND (u.full_name LIKE ? OR u.phone LIKE ? OR u.email LIKE ?)
                LIMIT 15
            ");
            $stmt->execute([$user_id, $term, $term, $term]);
        }
        $students = $stmt->fetchAll();

        // Với mỗi học viên, lấy các khóa học họ đang học
        foreach ($students as &$student) {
            if ($role === 'super_admin') {
                $stmtC = $db->prepare("
                    SELECT c.id as course_id, c.title as course_title
                    FROM enrollments e
                    JOIN courses c ON e.course_id = c.id
                    WHERE e.student_id = ? AND e.status = 'active'
                ");
                $stmtC->execute([$student['id']]);
            } else {
                $stmtC = $db->prepare("
                    SELECT c.id as course_id, c.title as course_title
                    FROM enrollments e
                    JOIN courses c ON e.course_id = c.id
                    WHERE e.student_id = ? AND e.status = 'active' AND c.author_id = ?
                ");
                $stmtC->execute([$student['id'], $user_id]);
            }
            $student['courses'] = $stmtC->fetchAll();
        }

        echo json_encode(['ok' => true, 'students' => $students]);
    }

    // Tạo / Mở cuộc trò chuyện chủ động với học viên
    public function startThread() {
        header('Content-Type: application/json');
        $student_id = (int)($_POST['student_id'] ?? 0);
        $course_id = isset($_POST['course_id']) && $_POST['course_id'] !== '' ? (int)$_POST['course_id'] : null;

        if (!$student_id) {
            echo json_encode(['ok' => false, 'error' => 'Học viên không hợp lệ']);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Kiểm tra xem học viên có tồn tại không
        $stmtUser = $db->prepare("SELECT id, role FROM users WHERE id = ?");
        $stmtUser->execute([$student_id]);
        $user = $stmtUser->fetch();
        if (!$user || $user['role'] !== 'student') {
            echo json_encode(['ok' => false, 'error' => 'Người dùng này không phải là học viên']);
            return;
        }

        // Lọc xem thread đã tồn tại hay chưa
        if ($course_id) {
            $stmtCheck = $db->prepare("SELECT id FROM chat_threads WHERE student_id = ? AND course_id = ? AND type = 'student_teacher' LIMIT 1");
            $stmtCheck->execute([$student_id, $course_id]);
        } else {
            $stmtCheck = $db->prepare("SELECT id FROM chat_threads WHERE student_id = ? AND course_id IS NULL AND type = 'student_teacher' LIMIT 1");
            $stmtCheck->execute([$student_id]);
        }
        $thread = $stmtCheck->fetch();

        if ($thread) {
            $thread_id = $thread['id'];
        } else {
            // Khởi tạo thread mới
            $stmtInsert = $db->prepare("INSERT INTO chat_threads (student_id, course_id, type) VALUES (?, ?, 'student_teacher')");
            $stmtInsert->execute([$student_id, $course_id]);
            $thread_id = $db->lastInsertId();
        }

        // Cập nhật lại thời gian hoạt động của thread để đẩy lên đầu
        $db->prepare("UPDATE chat_threads SET updated_at = NOW() WHERE id = ?")->execute([$thread_id]);

        echo json_encode([
            'ok' => true,
            'thread_id' => $thread_id
        ]);
    }

    // Kiểm tra quyền quản trị thread
    private function checkAdminThreadAccess($thread_id) {
        $db = Database::getInstance()->getConnection();
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];

        if ($role === 'super_admin') {
            return true;
        }

        $stmt = $db->prepare("
            SELECT t.*, c.author_id 
            FROM chat_threads t 
            LEFT JOIN courses c ON t.course_id = c.id 
            WHERE t.id = ?
        ");
        $stmt->execute([$thread_id]);
        $thread = $stmt->fetch();

        if (!$thread) return false;

        // Cho phép admin/giáo viên truy cập cuộc trò chuyện của khách hoặc lớp học do chính họ dạy
        return ($thread['type'] === 'guest_admin' || $thread['author_id'] == $user_id);
    }

    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
