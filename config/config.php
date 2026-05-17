<?php
// config/config.php

// Thông tin chung của ứng dụng
define('APP_NAME', 'Trung Tâm Ngoại Ngữ Tin Học Nguyễn Minh');
define('APP_URL', 'https://ntkntk.com'); // Thay đổi khi deploy: https://ntkntk.com

// Cấu hình Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'ntknt9be30ca_ntkntk');
define('DB_USER', 'ntknt9be30ca_nmt');
define('DB_PASS', '123451212');
define('DB_CHARSET', 'utf8mb4');

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
