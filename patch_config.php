<?php
$configPath = __DIR__ . '/config/config.php';

header('Content-Type: text/plain; charset=utf-8');

if (file_exists($configPath)) {
    $content = file_get_contents($configPath);
    if (strpos($content, 'CHAT_DRIVE_FOLDER_ID') === false) {
        // Tìm đoạn code secrets.php để chèn phía dưới
        $target = "if (file_exists(__DIR__ . '/secrets.php')) {\n    include_once __DIR__ . '/secrets.php';\n}";
        if (strpos($content, $target) !== false) {
            $replacement = $target . "\n\n// Cấu hình Google Drive Folder ID cho Chat trực tuyến\ndefine('CHAT_DRIVE_FOLDER_ID', '1ZYASrXxviVSU5DOWPuAXIOhlyqSFhQ97');";
            $content = str_replace($target, $replacement, $content);
            file_put_contents($configPath, $content);
            echo "Thành công: Đã thêm CHAT_DRIVE_FOLDER_ID vào config/config.php trên server!";
        } else {
            // Nếu không tìm thấy, chèn ngay sau tag <?php
            $content = preg_replace('/<\?php/', "<?php\n// Cấu hình Google Drive Folder ID cho Chat trực tuyến\ndefine('CHAT_DRIVE_FOLDER_ID', '1ZYASrXxviVSU5DOWPuAXIOhlyqSFhQ97');\n", $content, 1);
            file_put_contents($configPath, $content);
            echo "Thành công: Đã chèn CHAT_DRIVE_FOLDER_ID vào đầu file config/config.php trên server!";
        }
    } else {
        echo "Thông báo: CHAT_DRIVE_FOLDER_ID đã được định nghĩa sẵn trong config/config.php trên server.";
    }
} else {
    echo "Lỗi: Không tìm thấy file config/config.php trên server.";
}
