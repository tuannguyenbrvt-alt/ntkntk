<?php
class HomeController extends Controller {
    public function index() {
        $db = Database::getInstance()->getConnection();

        // Lấy 9 khóa học nổi bật đang xuất bản (ưu tiên được ghim lên trước)
        $stmtCourses = $db->query("SELECT * FROM courses WHERE status = 'published' ORDER BY is_pinned DESC, created_at DESC LIMIT 9");
        $featuredCourses = $stmtCourses->fetchAll();

        // Lấy 6 bài viết mới nhất (ưu tiên được ghim lên trước)
        $stmtPosts = $db->query("SELECT p.*, u.full_name as author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.status = 'published' AND p.type = 'blog' ORDER BY p.is_pinned DESC, p.created_at DESC LIMIT 6");
        $latestPosts = $stmtPosts->fetchAll();

        $this->render('home/index', [
            'title'          => 'Trang chủ - Trung tâm Ngoại ngữ Tin học Nguyễn Minh',
            'seo_desc'       => 'Hệ sinh thái Giáo dục Công nghệ & Giải pháp Phần mềm Tin học Nguyễn Minh (NTKNTK). Đào tạo Lập trình, AI, Tin học quốc tế, Tiếng Anh thực chiến và Combo phần mềm phòng máy LAN. Hotline: 0979 875 712',

            'featuredCourses'=> $featuredCourses,
            'latestPosts'    => $latestPosts,
        ], 'main');
    }
}
