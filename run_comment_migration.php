<?php
// run_comment_migration.php
// Tập tin nâng cấp Cơ sở dữ liệu cho chức năng bình luận

header('Content-Type: text/plain; charset=utf-8');
require_once 'config/config.php';
require_once 'config/database.php';

echo "--- BẮT ĐẦU NÂNG CẤP CƠ SỞ DỮ LIỆU BÌNH LUẬN ---\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Thêm cột allow_comments cho bảng posts
    echo "1. Kiểm tra cột `allow_comments` trong bảng `posts`...\n";
    $check = $db->query("SHOW COLUMNS FROM `posts` LIKE 'allow_comments'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE `posts` ADD COLUMN `allow_comments` TINYINT(1) NOT NULL DEFAULT 1");
        echo "   -> Đã thêm cột `allow_comments` thành công.\n";
    } else {
        echo "   -> Cột `allow_comments` đã tồn tại.\n";
    }

    // 2. Thêm cột allow_comments cho bảng courses
    echo "\n2. Kiểm tra cột `allow_comments` trong bảng `courses`...\n";
    $check = $db->query("SHOW COLUMNS FROM `courses` LIKE 'allow_comments'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE `courses` ADD COLUMN `allow_comments` TINYINT(1) NOT NULL DEFAULT 1");
        echo "   -> Đã thêm cột `allow_comments` thành công.\n";
    } else {
        echo "   -> Cột `allow_comments` đã tồn tại.\n";
    }

    // 3. Thêm cột allow_comments cho bảng course_lessons
    echo "\n3. Kiểm tra cột `allow_comments` trong bảng `course_lessons`...\n";
    $check = $db->query("SHOW COLUMNS FROM `course_lessons` LIKE 'allow_comments'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE `course_lessons` ADD COLUMN `allow_comments` TINYINT(1) NOT NULL DEFAULT 1");
        echo "   -> Đã thêm cột `allow_comments` thành công.\n";
    } else {
        echo "   -> Cột `allow_comments` đã tồn tại.\n";
    }

    // 4. Cấu hình lại bảng comments
    echo "\n4. Kiểm tra cấu trúc bảng `comments`...\n";
    
    // Kiểm tra xem bảng comments có tồn tại không
    try {
        $db->query("SELECT 1 FROM `comments` LIMIT 1");
        echo "   -> Bảng `comments` đã tồn tại. Tiến hành nâng cấp cấu trúc...\n";
    } catch (Exception $e) {
        echo "   -> Bảng `comments` chưa tồn tại. Đang tạo bảng mới...\n";
        $db->exec("CREATE TABLE `comments` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) DEFAULT NULL,
          `course_id` int(11) DEFAULT NULL,
          `post_id` int(11) DEFAULT NULL,
          `lesson_id` int(11) DEFAULT NULL,
          `parent_id` int(11) DEFAULT NULL,
          `content` text NOT NULL,
          `status` enum('pending','approved') NOT NULL DEFAULT 'approved',
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        echo "   -> Đã tạo bảng `comments` thành công.\n";
    }

    // Drop khóa ngoại cũ của comments liên quan đến users để cho phép user_id NULL
    echo "   -> Chuyển đổi cột `user_id` sang NULLable...\n";
    try {
        // Thử drop foreign key nếu có
        @$db->exec("ALTER TABLE `comments` DROP FOREIGN KEY `comments_ibfk_1`");
        echo "      * Đã gỡ bỏ ràng buộc khóa ngoại cũ (comments_ibfk_1).\n";
    } catch (Exception $ex) {
        echo "      * Ràng buộc khóa ngoại cũ không tồn tại hoặc đã bị gỡ bỏ trước đó.\n";
    }

    // Cho phép user_id nhận giá trị NULL
    $db->exec("ALTER TABLE `comments` MODIFY `user_id` INT(11) NULL");
    echo "      * Đã chuyển cột `user_id` sang cho phép NULL.\n";

    // Thiết lập lại khóa ngoại cho user_id có hỗ trợ SET NULL khi xóa tài khoản hoặc giữ nguyên CASCADE
    try {
        $db->exec("ALTER TABLE `comments` ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE");
        echo "      * Đã tạo lại ràng buộc khóa ngoại (comments_ibfk_1).\n";
    } catch (Exception $ex) {
        echo "      * Không thể thiết lập lại khóa ngoại (Có thể đã tồn tại ràng buộc tương tự).\n";
    }

    // Thêm các cột lưu thông tin khách
    echo "   -> Kiểm tra cột thông tin khách và trạng thái duyệt...\n";
    
    $check = $db->query("SHOW COLUMNS FROM `comments` LIKE 'guest_name'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE `comments` ADD COLUMN `guest_name` VARCHAR(100) NULL AFTER `user_id`");
        echo "      * Đã thêm cột `guest_name` thành công.\n";
    }
    
    $check = $db->query("SHOW COLUMNS FROM `comments` LIKE 'guest_phone'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE `comments` ADD COLUMN `guest_phone` VARCHAR(20) NULL AFTER `guest_name`");
        echo "      * Đã thêm cột `guest_phone` thành công.\n";
    }

    $check = $db->query("SHOW COLUMNS FROM `comments` LIKE 'is_public_to_guest'")->fetch();
    if (!$check) {
        $db->exec("ALTER TABLE `comments` ADD COLUMN `is_public_to_guest` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
        echo "      * Đã thêm cột `is_public_to_guest` thành công.\n";
    }

    // Đảm bảo kiểu dữ liệu cột status là ENUM('pending', 'approved')
    $db->exec("ALTER TABLE `comments` MODIFY `status` ENUM('pending', 'approved') NOT NULL DEFAULT 'approved'");
    echo "      * Đã chuẩn hóa cấu hình cột `status`.\n";

    // 5. Thêm khóa ngoại cho các liên kết khác nếu chưa có
    echo "\n5. Kiểm tra và thiết lập các khóa ngoại liên quan khác...\n";
    $foreignKeys = [
        ['comments_ibfk_2', 'course_id', 'courses'],
        ['comments_ibfk_3', 'post_id', 'posts'],
        ['comments_ibfk_4', 'lesson_id', 'course_lessons'],
        ['comments_ibfk_5', 'parent_id', 'comments']
    ];
    foreach ($foreignKeys as $fk) {
        list($fkName, $col, $refTable) = $fk;
        try {
            @$db->exec("ALTER TABLE `comments` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$col}`) REFERENCES `{$refTable}` (`id`) ON DELETE CASCADE");
            echo "   -> Đã thiết lập ràng buộc `{$fkName}` thành công.\n";
        } catch (Exception $ex) {
            echo "   -> Ràng buộc `{$fkName}` đã tồn tại hoặc bảng tham chiếu lỗi.\n";
        }
    }

    echo "\n🎉 HOÀN THÀNH NÂNG CẤP CƠ SỞ DỮ LIỆU THÀNH CÔNG!\n";
    
} catch (Exception $e) {
    echo "\n❌ LỖI HỆ THỐNG: " . $e->getMessage() . "\n";
}
