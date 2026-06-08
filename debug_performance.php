<?php
// debug_performance.php
// Tạm thời kích hoạt hiển thị lỗi tối đa ở dòng đầu tiên
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>PHP Version: " . phpversion() . "</h3>";

try {
    echo "Trace A: Loading config.php...<br>";
    require_once 'config/config.php';
    
    echo "Trace B: Loading database.php...<br>";
    require_once 'config/database.php';
    
    echo "Trace C: Loading Router.php...<br>";
    require_once 'core/Router.php';
    
    echo "Trace D: Loading Controller.php...<br>";
    require_once 'core/Controller.php';
} catch (Throwable $e) {
    echo "<b>Lỗi nạp core:</b> " . $e->getMessage() . " tại " . $e->getFile() . " (Dòng " . $e->getLine() . ")<br>";
    exit;
}

// Tự động load các class cần thiết
spl_autoload_register(function ($class) {
    try {
        if (file_exists('controllers/' . $class . '.php')) {
            require_once 'controllers/' . $class . '.php';
        } elseif (file_exists('core/' . $class . '.php')) {
            require_once 'core/' . $class . '.php';
        } elseif (file_exists('models/' . $class . '.php')) {
            require_once 'models/' . $class . '.php';
        }
    } catch (Throwable $e) {
        echo "<b>Lỗi Autoload class {$class}:</b> " . $e->getMessage() . "<br>";
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

echo "<h3>Bắt đầu nạp AdminChatController...</h3>";

try {
    require_once 'controllers/AdminChatController.php';
    echo "<b>Đã nạp AdminChatController thành công!</b><br>";
} catch (Throwable $e) {
    echo "<b>Lỗi nạp AdminChatController:</b> " . $e->getMessage() . " tại " . $e->getFile() . " (Dòng " . $e->getLine() . ")<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

echo "<h3>Bắt đầu khởi chạy performance()...</h3>";

try {
    $controller = new AdminChatController();
    $controller->performance();
} catch (Throwable $e) {
    echo "<b>Lỗi khởi chạy performance():</b> " . $e->getMessage() . " tại " . $e->getFile() . " (Dòng " . $e->getLine() . ")<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}
