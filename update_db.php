<?php
/**
 * Tự động cập nhật Cơ sở dữ liệu cho hệ thống nộp bài tập nhiều file
 * Bảo vệ bằng Token bảo mật để tránh người ngoài truy cập trái phép.
 */

// Định nghĩa token bảo mật (trùng với token trong deploy_webhook.php)
define('SECURE_TOKEN', 'ntkntk_secure_deploy_2026');

// Kiểm tra quyền truy cập (nếu chạy qua trình duyệt thì bắt buộc có token đúng)
if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['token']) || $_GET['token'] !== SECURE_TOKEN) {
        header('HTTP/1.1 403 Forbidden');
        die('Lỗi: Bạn không có quyền truy cập file này (Sai Token bảo mật).');
    }
}

// Bật hiển thị lỗi để dễ kiểm tra
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<h3>🔄 Bắt đầu tiến trình cập nhật cơ sở dữ liệu...</h3>";

try {
    $db = Database::getInstance()->getConnection();
    echo "✔️ Kết nối cơ sở dữ liệu thành công.<br>";

    // 1. Tạo bảng assignment_submission_files nếu chưa tồn tại
    $sqlCreate = "CREATE TABLE IF NOT EXISTS `assignment_submission_files` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `submission_id` int(11) NOT NULL,
      `file_name` varchar(255) NOT NULL,
      `file_drive_url` varchar(500) DEFAULT NULL,
      `file_drive_id` varchar(100) DEFAULT NULL,
      `content` longtext DEFAULT NULL COMMENT 'Lỗi chi tiết nếu tải lên Drive thất bại',
      `score` decimal(5,2) DEFAULT NULL,
      `feedback` text DEFAULT NULL,
      `status` enum('pending','graded') NOT NULL DEFAULT 'pending',
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `submission_id` (`submission_id`),
      CONSTRAINT `sub_files_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `assignment_submissions` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sqlCreate);
    echo "✔️ Đã tạo/kiểm tra bảng <b>`assignment_submission_files`</b> thành công.<br>";

    // 2. Di chuyển dữ liệu cũ từ bảng `assignment_submissions` sang bảng mới
    echo "⏳ Đang quét dữ liệu file cũ để di chuyển...<br>";
    $stmt = $db->query("SELECT id, file_name, file_drive_url, file_drive_id, content, score, feedback, status, submitted_at FROM assignment_submissions WHERE file_name IS NOT NULL AND file_name != ''");
    $oldSubmissions = $stmt->fetchAll();

    $migratedCount = 0;
    foreach ($oldSubmissions as $sub) {
        // Kiểm tra xem file này đã được di chuyển trước đó chưa để tránh trùng lặp
        $check = $db->prepare("SELECT id FROM assignment_submission_files WHERE submission_id = ? AND file_name = ?");
        $check->execute([$sub['id'], $sub['file_name']]);
        if (!$check->fetch()) {
            $insert = $db->prepare("INSERT INTO assignment_submission_files (submission_id, file_name, file_drive_url, file_drive_id, content, score, feedback, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([
                $sub['id'],
                $sub['file_name'],
                $sub['file_drive_url'],
                $sub['file_drive_id'],
                $sub['file_drive_id'] === 'error' ? $sub['content'] : null,
                $sub['score'],
                $sub['feedback'],
                $sub['status'],
                $sub['submitted_at']
            ]);
            $migratedCount++;
        }
    }
    
    echo "✔️ Di chuyển dữ liệu cũ hoàn tất. Số lượng bản ghi đã di chuyển: <b>$migratedCount</b>.<br>";
    echo "🎉 <b>Cập nhật cơ sở dữ liệu thành công rực rỡ!</b> Bạn có thể xóa file này khỏi server sau khi chạy xong.<br>";

} catch (Exception $e) {
    echo "❌ <b>Lỗi:</b> " . $e->getMessage() . "<br>";
}
