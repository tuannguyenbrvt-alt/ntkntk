<?php
class HomeController extends Controller {
    public function index() {
        $db = Database::getInstance()->getConnection();

        // Lấy 6 khóa học nổi bật đang xuất bản (ưu tiên được ghim lên trước)
        $stmtCourses = $db->query("SELECT * FROM courses WHERE status = 'published' ORDER BY is_pinned DESC, created_at DESC LIMIT 6");
        $featuredCourses = $stmtCourses->fetchAll();

        // Lấy 3 bài viết mới nhất (ưu tiên được ghim lên trước)
        $stmtPosts = $db->query("SELECT p.*, u.full_name as author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.status = 'published' AND p.type = 'blog' ORDER BY p.is_pinned DESC, p.created_at DESC LIMIT 3");
        $latestPosts = $stmtPosts->fetchAll();

        $this->render('home/index', [
            'title'          => 'Trang chủ - Trung tâm Ngoại ngữ Tin học Nguyễn Minh',
            'seo_desc'       => 'Bom tấn mùa hè 2026 - Ưu đãi Kép! Đăng ký 1 khóa Tin học tặng ngay 1 khóa Tiếng Anh. Hotline: 0397 883 255',
            'featuredCourses'=> $featuredCourses,
            'latestPosts'    => $latestPosts,
        ], 'main');
    }
}
