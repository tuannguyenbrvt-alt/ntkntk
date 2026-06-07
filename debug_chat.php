<?php
require 'config/config.php';
require 'config/database.php';
require_once 'helpers/GoogleDriveHelper.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== HỆ THỐNG DEBUG GOOGLE DRIVE & CHAT ===\n\n";

// 1. Kiểm tra cấu hình CHAT_DRIVE_FOLDER_ID
echo "1. Cấu hình ID thư mục Drive cho Chat:\n";
if (defined('CHAT_DRIVE_FOLDER_ID')) {
    echo "   CHAT_DRIVE_FOLDER_ID = " . CHAT_DRIVE_FOLDER_ID . "\n";
} else {
    echo "   CHAT_DRIVE_FOLDER_ID chưa được định nghĩa!\n";
}
echo "\n";

// 2. Thử nghiệm tải Credentials Google Drive
echo "2. Kiểm tra tải thông tin xác thực Google Drive:\n";
try {
    $creds = GoogleDriveHelper::loadCredentials();
    $decoded = json_decode($creds, true);
    if ($decoded) {
        echo "   Tải Credentials thành công!\n";
        if (isset($decoded['refresh_token'])) {
            echo "   Loại: OAuth Refresh Token\n";
            echo "   Client ID bắt đầu bằng: " . substr($decoded['client_id'] ?? '', 0, 15) . "...\n";
        } elseif (isset($decoded['client_email'])) {
            echo "   Loại: Service Account\n";
            echo "   Email: " . $decoded['client_email'] . "\n";
        } else {
            echo "   Định dạng credentials không xác định.\n";
        }
    } else {
        echo "   Credentials không phải định dạng JSON hợp lệ.\n";
    }
} catch (Exception $e) {
    echo "   LỖI khi tải Credentials: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Truy vấn các tin nhắn gần nhất có file đính kèm
echo "3. Các tin nhắn có file đính kèm gần đây:\n";
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, sender_name, message_text, file_name, file_path, file_drive_id, file_drive_url, created_at FROM chat_messages WHERE file_name IS NOT NULL ORDER BY id DESC LIMIT 5");
    $rows = $stmt->fetchAll();
    
    if (empty($rows)) {
        echo "   Chưa có tin nhắn nào có file đính kèm trong DB.\n";
    } else {
        foreach ($rows as $row) {
            echo "   - ID: {$row['id']} | Gửi bởi: {$row['sender_name']} | Lúc: {$row['created_at']}\n";
            echo "     Tên file: {$row['file_name']}\n";
            echo "     Đường dẫn cục bộ: {$row['file_path']}\n";
            echo "     Drive File ID: {$row['file_drive_id']}\n";
            echo "     Drive URL: {$row['file_drive_url']}\n";
            echo "     -----------------------------------------\n";
        }
    }
} catch (Exception $e) {
    echo "   LỖI khi truy vấn CSDL: " . $e->getMessage() . "\n";
}
echo "\n";
echo "=== KẾT THÚC CHẨN ĐOÁN ===\n";
