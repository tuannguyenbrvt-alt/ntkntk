<?php
// run_pinning_migration.php
// Tập tin nâng cấp Cơ sở dữ liệu để thêm trường ghim khóa học và bài viết lên đầu trang

header('Content-Type: text/plain; charset=utf-8');
require_once 'config/config.php';
require_once 'config/database.php';

echo "--- BẮT ĐẦU NÂNG CẤP CƠ SỞ DỮ LIỆU: GHIM KHÓA HỌC & BÀI VIẾT ---\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Kiểm tra và thêm cột is_pinned vào bảng courses
    echo "1. Kiểm tra bảng `courses`...\n";
    $stmt = $db->query("SHOW COLUMNS FROM `courses` LIKE 'is_pinned'");
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "   -> Cột `is_pinned` đã tồn tại trên bảng `courses`.\n";
    } else {
        echo "   -> Đang thêm cột `is_pinned` vào bảng `courses`...\n";
        $db->exec("ALTER TABLE `courses` ADD COLUMN `is_pinned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `allow_comments`");
        echo "   -> Đã thêm cột `is_pinned` thành công.\n";
    }
    
    // Kiểm tra index trên courses
    $stmtIdx = $db->query("SHOW INDEX FROM `courses` WHERE Key_name = 'idx_courses_pinned'");
    $indexExists = $stmtIdx->fetch();
    if ($indexExists) {
        echo "   -> Index `idx_courses_pinned` đã tồn tại.\n";
    } else {
        echo "   -> Đang tạo Index `idx_courses_pinned` cho cột `is_pinned`...\n";
        $db->exec("ALTER TABLE `courses` ADD INDEX `idx_courses_pinned` (`is_pinned`)");
        echo "   -> Đã tạo Index `idx_courses_pinned` thành công.\n";
    }

    // 2. Kiểm tra và thêm cột is_pinned vào bảng posts
    echo "\n2. Kiểm tra bảng `posts`...\n";
    $stmtPost = $db->query("SHOW COLUMNS FROM `posts` LIKE 'is_pinned'");
    $columnPostExists = $stmtPost->fetch();
    
    if ($columnPostExists) {
        echo "   -> Cột `is_pinned` đã tồn tại trên bảng `posts`.\n";
    } else {
        echo "   -> Đang thêm cột `is_pinned` vào bảng `posts`...\n";
        $db->exec("ALTER TABLE `posts` ADD COLUMN `is_pinned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `allow_comments`");
        echo "   -> Đã thêm cột `is_pinned` thành công.\n";
    }
    
    // Kiểm tra index trên posts
    $stmtIdxPost = $db->query("SHOW INDEX FROM `posts` WHERE Key_name = 'idx_posts_pinned'");
    $indexPostExists = $stmtIdxPost->fetch();
    if ($indexPostExists) {
        echo "   -> Index `idx_posts_pinned` đã tồn tại.\n";
    } else {
        echo "   -> Đang tạo Index `idx_posts_pinned` cho cột `is_pinned`...\n";
        $db->exec("ALTER TABLE `posts` ADD INDEX `idx_posts_pinned` (`is_pinned`)");
        echo "   -> Đã tạo Index `idx_posts_pinned` thành công.\n";
    }

    echo "\n🎉 HOÀN THÀNH NÂNG CẤP CƠ SỞ DỮ LIỆU THÀNH CÔNG!\n";
    
} catch (Exception $e) {
    echo "\n❌ LỖI HỆ THỐNG: " . $e->getMessage() . "\n";
}
