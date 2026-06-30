<?php
// run_optimize_migration.php
// Tập tin nâng cấp Cơ sở dữ liệu để tối ưu hóa hiệu năng

header('Content-Type: text/plain; charset=utf-8');
require_once 'config/config.php';
require_once 'config/database.php';

echo "--- BẮT ĐẦU NÂNG CẤP CƠ SỞ DỮ LIỆU TỐI ƯU HIỆU NĂNG ---\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "1. Kiểm tra bảng `site_online`...\n";
    
    // Đảm bảo bảng tồn tại
    try {
        $db->query("SELECT 1 FROM `site_online` LIMIT 1");
        echo "   -> Bảng `site_online` đã tồn tại.\n";
    } catch (Exception $e) {
        echo "   -> Bảng `site_online` chưa tồn tại. Đang tiến hành tạo bảng...\n";
        $db->exec("CREATE TABLE IF NOT EXISTS `site_online` (
          `session_id` varchar(255) NOT NULL,
          `last_activity` int(11) NOT NULL,
          PRIMARY KEY (`session_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        echo "   -> Đã tạo bảng `site_online` thành công.\n";
    }

    echo "2. Kiểm tra Index `idx_last_activity`...\n";
    $stmt = $db->query("SHOW INDEX FROM `site_online` WHERE Key_name = 'idx_last_activity'");
    $indexExists = $stmt->fetch();

    if ($indexExists) {
        echo "   -> Index `idx_last_activity` đã tồn tại trên bảng `site_online`.\n";
    } else {
        echo "   -> Đang tạo Index `idx_last_activity` cho cột `last_activity`...\n";
        $db->exec("ALTER TABLE `site_online` ADD INDEX `idx_last_activity` (`last_activity`)");
        echo "   -> Đã tạo Index `idx_last_activity` thành công.\n";
    }

    echo "\n🎉 HOÀN THÀNH NÂNG CẤP CƠ SỞ DỮ LIỆU THÀNH CÔNG!\n";
    
} catch (Exception $e) {
    echo "\n❌ LỖI HỆ THỐNG: " . $e->getMessage() . "\n";
}
