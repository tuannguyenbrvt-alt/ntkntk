<?php
// config/config.php

// Thông tin chung của ứng dụng
define('APP_NAME', 'Trung Tâm Ngoại Ngữ Tin Học Nguyễn Minh');
define('APP_URL', 'https://ntkntk.com'); // Thay đổi khi deploy: https://ntkntk.com

// Nạp các cấu hình bảo mật / bí mật từ file secrets.php (không tracked bởi Git) nếu có
if (file_exists(__DIR__ . '/secrets.php')) {
    include_once __DIR__ . '/secrets.php';
}

// Cấu hình Google Drive Folder ID cho Chat trực tuyến
define('CHAT_DRIVE_FOLDER_ID', '1ZYASrXxviVSU5DOWPuAXIOhlyqSFhQ97');

// Cấu hình Google Login OAuth2 mặc định (Thay thế bằng Client ID / Secret thật trong secrets.php)
if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_PLACEHOLDER');
}
if (!defined('GOOGLE_CLIENT_SECRET')) {
    define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET_PLACEHOLDER');
}
if (!defined('GOOGLE_REDIRECT_URI')) {
    define('GOOGLE_REDIRECT_URI', APP_URL . '/auth/google/callback');
}

// Cấu hình Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'ntknt9be30ca_ntkntk');
define('DB_USER', 'ntknt9be30ca_nmt');
define('DB_PASS', '123451212');
define('DB_CHARSET', 'utf8mb4');

// Cấu hình mã bảo mật API để đăng bài tự động
define('API_SECRET_KEY', 'ntkntk_secure_api_key_8b9f1a2c3d4e5f6a');

// Cấu hình đường dẫn hệ thống
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', APP_URL . '/uploads');

// Cấu hình Múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Bật hiển thị lỗi CHỈ trong môi trường DEV (Tắt khi lên Production)
// Để tắt lỗi production: đổi thành: define('APP_ENV', 'production');
define('APP_ENV', 'production');
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
    // Ghi lỗi ra file log (cPanel > Error Logs)
    ini_set('log_errors', 1);
}
