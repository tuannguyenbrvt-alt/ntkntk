<?php
// run_visits_migration.php
// Tập tin nâng cấp Cơ sở dữ liệu cho chức năng thống kê lượt truy cập

header('Content-Type: text/plain; charset=utf-8');
require_once 'config/config.php';
require_once 'config/database.php';

echo "--- BẮT ĐẦU NÂNG CẤP CƠ SỞ DỮ LIỆU LƯỢT TRUY CẬP ---\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "1. Kiểm tra bảng `site_visits`...\n";
    
    // Kiểm tra xem bảng site_visits có tồn tại không
    try {
        $db->query("SELECT 1 FROM `site_visits` LIMIT 1");
        echo "   -> Bảng `site_visits` đã tồn tại.\n";
    } catch (Exception $e) {
        echo "   -> Bảng `site_visits` chưa tồn tại. Đang tiến hành tạo bảng...\n";
        
        $sql = "CREATE TABLE `site_visits` (
          `visit_date` date NOT NULL,
          `visit_count` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`visit_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $db->exec($sql);
        echo "   -> Đã tạo bảng `site_visits` thành công.\n";
    }

    echo "\n🎉 HOÀN THÀNH NÂNG CẤP CƠ SỞ DỮ LIỆU THÀNH CÔNG!\n";
    
} catch (Exception $e) {
    echo "\n❌ LỖI HỆ THỐNG: " . $e->getMessage() . "\n";
}
