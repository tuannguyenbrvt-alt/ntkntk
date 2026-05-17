<?php
class PostController extends Controller {
    public function index() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT p.*, u.full_name as author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.status = 'published' AND p.type = 'blog' ORDER BY p.created_at DESC");
        $posts = $stmt->fetchAll();

        $this->render('posts/index', [
            'title' => 'Tin tức & Sự kiện',
            'posts' => $posts
        ], 'main');
    }

    public function show() {
        $slug = $_GET['slug'] ?? '';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT p.*, u.full_name as author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.slug = ? AND p.status = 'published'");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();

        if (!$post) {
            http_response_code(404);
            die("Bài viết không tồn tại hoặc đã bị ẩn.");
        }
        
        $this->render('posts/show', [
            'title' => $post['title'],
            'post' => $post
        ], 'main');
    }
}
