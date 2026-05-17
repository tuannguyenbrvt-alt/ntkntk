<?php
// controllers/SearchController.php
class SearchController extends Controller {
    public function index() {
        $q = trim($_GET['q'] ?? '');
        $courses = [];
        $posts = [];

        if (strlen($q) >= 2) {
            $db = Database::getInstance()->getConnection();
            $search = "%{$q}%";

            // Tìm khóa học
            $stmtC = $db->prepare("SELECT * FROM courses WHERE status = 'published' AND (title LIKE ? OR description LIKE ?) ORDER BY created_at DESC LIMIT 12");
            $stmtC->execute([$search, $search]);
            $courses = $stmtC->fetchAll();

            // Tìm bài viết
            $stmtP = $db->prepare("SELECT p.*, u.full_name as author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.status = 'published' AND p.type = 'blog' AND (p.title LIKE ? OR p.content LIKE ?) ORDER BY p.created_at DESC LIMIT 8");
            $stmtP->execute([$search, $search]);
            $posts = $stmtP->fetchAll();
        }

        $totalResults = count($courses) + count($posts);

        $this->render('search/results', [
            'title'        => $q ? "Kết quả tìm kiếm: \"$q\"" : 'Tìm kiếm',
            'seo_desc'     => "Kết quả tìm kiếm cho \"$q\" - " . APP_NAME,
            'q'            => $q,
            'courses'      => $courses,
            'posts'        => $posts,
            'totalResults' => $totalResults,
        ], 'main');
    }
}
