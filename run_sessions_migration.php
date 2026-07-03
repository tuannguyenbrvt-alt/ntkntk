<?php
// run_sessions_migration.php
// Tập tin nâng cấp Cơ sở dữ liệu để thêm bảng ghi nhận lịch sử truy cập và sử dụng hệ thống

header('Content-Type: text/plain; charset=utf-8');
require_once 'config/config.php';
require_once 'config/database.php';

echo "--- BẮT ĐẦU NÂNG CẤP CƠ SỞ DỮ LIỆU: LỊCH SỬ TRUY CẬP ---\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    echo "1. Tạo bảng `user_sessions`...\n";
    $db->exec("CREATE TABLE IF NOT EXISTS `user_sessions` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` INT NOT NULL,
      `session_id` VARCHAR(255) NOT NULL,
      `ip_address` VARCHAR(45) DEFAULT NULL,
      `user_agent` TEXT DEFAULT NULL,
      `login_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `logout_at` TIMESTAMP NULL DEFAULT NULL,
      `last_activity_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `status` ENUM('active', 'logged_out', 'expired') DEFAULT 'active',
      KEY `idx_user_id` (`user_id`),
      KEY `idx_session_id` (`session_id`),
      KEY `idx_status_last_activity` (`status`, `last_activity_at`),
      CONSTRAINT `fk_user_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "   -> Đã tạo bảng `user_sessions` thành công.\n";

    echo "2. Tạo bảng `user_session_lessons`...\n";
    $db->exec("CREATE TABLE IF NOT EXISTS `user_session_lessons` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_session_id` INT NOT NULL,
      `lesson_id` INT NOT NULL,
      `viewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY `uk_session_lesson` (`user_session_id`, `lesson_id`),
      KEY `idx_session` (`user_session_id`),
      KEY `idx_lesson` (`lesson_id`),
      CONSTRAINT `fk_session_lessons_session` FOREIGN KEY (`user_session_id`) REFERENCES `user_sessions` (`id`) ON DELETE CASCADE,
      CONSTRAINT `fk_session_lessons_lesson` FOREIGN KEY (`lesson_id`) REFERENCES `course_lessons` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "   -> Đã tạo bảng `user_session_lessons` thành công.\n";

    echo "\n🎉 HOÀN THÀNH NÂNG CẤP CƠ SỞ DỮ LIỆU THÀNH CÔNG!\n";
    
} catch (Exception $e) {
    echo "\n❌ LỖI HỆ THỐNG: " . $e->getMessage() . "\n";
}
