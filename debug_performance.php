<?php
// debug_performance.php
// Tạm thời kích hoạt hiển thị lỗi tối đa ở dòng đầu tiên
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Router.php';
require_once 'core/Controller.php';

// Tự động load các class cần thiết
spl_autoload_register(function ($class) {
    if (file_exists('controllers/' . $class . '.php')) {
        require_once 'controllers/' . $class . '.php';
    } elseif (file_exists('core/' . $class . '.php')) {
        require_once 'core/' . $class . '.php';
    } elseif (file_exists('models/' . $class . '.php')) {
        require_once 'models/' . $class . '.php';
    }
});

// Giả lập session admin nếu chưa đăng nhập để chạy thử
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Giả định ID admin
    $_SESSION['role'] = 'super_admin';
    $_SESSION['full_name'] = 'Admin Debugger';
}

echo "<h3>Bắt đầu khởi chạy trang thống kê hiệu suất...</h3>";

$controller = new AdminChatController();
$controller->performance();
