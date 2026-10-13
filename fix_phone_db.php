<?php
/**
 * Script cập nhật số điện thoại 0979 875 712 trong cơ sở dữ liệu
 */
define('SECURE_TOKEN', 'ntkntk_secure_deploy_2026');

if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['token']) || $_GET['token'] !== SECURE_TOKEN) {
        header('HTTP/1.1 403 Forbidden');
        die('Lỗi: Sai Token bảo mật.');
    }
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
echo "<h3>🔄 Bắt đầu cập nhật số điện thoại sang 0979 875 712...</h3>";

try {
    $db = Database::getInstance()->getConnection();
    echo "✔️ Kết nối cơ sở dữ liệu thành công.<br>";

    // 1. Cập nhật bảng posts (đặc biệt cho slug 'lien-he' và 'gioi-thieu')
    $stmtPosts = $db->prepare("
        UPDATE posts 
        SET content = REPLACE(REPLACE(content, '0397 883 255', '0979 875 712'), '0397883255', '0979875712')
        WHERE slug IN ('lien-he', 'gioi-thieu') OR content LIKE '%0397883255%' OR content LIKE '%0397 883 255%'
    ");
    $stmtPosts->execute();
    $affectedPosts = $stmtPosts->rowCount();
    echo "✔️ Đã cập nhật {$affectedPosts} bài viết trong bảng `posts`.<br>";

    // 2. Cập nhật bảng courses nếu có số cũ
    $stmtCourses = $db->prepare("
        UPDATE courses 
        SET description = REPLACE(REPLACE(description, '0397 883 255', '0979 875 712'), '0397883255', '0979875712')
        WHERE description LIKE '%0397883255%' OR description LIKE '%0397 883 255%'
    ");
    $stmtCourses->execute();
    $affectedCourses = $stmtCourses->rowCount();
    echo "✔️ Đã cập nhật {$affectedCourses} khóa học trong bảng `courses`.<br>";

    // 3. Kiểm tra lại nội dung trang lien-he và gioi-thieu
    foreach (['lien-he', 'gioi-thieu'] as $slug) {
        $check = $db->prepare("SELECT title, content FROM posts WHERE slug = ?");
        $check->execute([$slug]);
        $row = $check->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $hasOld = (strpos($row['content'], '0397') !== false);
            $hasNew = (strpos($row['content'], '0979 875 712') !== false || strpos($row['content'], '0979875712') !== false);
            echo "• Bài [{$slug}] - {$row['title']}: " . ($hasOld ? "❌ Vẫn còn số cũ" : "✅ SẠCH SỐ CŨ") . " | " . ($hasNew ? "✅ Đã có số mới 0979 875 712" : "⚠️ Chưa thấy số mới") . "<br>";
        } else {
            echo "• Không tìm thấy bài có slug [{$slug}]!<br>";
        }
    }

    echo "<h4>🎉 Hoàn tất cập nhật số điện thoại thành công!</h4>";

} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
