<?php
class PageController extends Controller {
    public function show() {
        // Hỗ trợ cả /page?slug=... và rewrite URL /page/...
        $slug = $_GET['slug'] ?? '';
        
        $parts = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
        if (empty($slug) && count($parts) >= 2 && $parts[0] === 'page') {
            $slug = $parts[1];
        }

        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM posts WHERE slug = ? AND type = 'page' AND status = 'published'");
        $stmt->execute([$slug]);
        $page = $stmt->fetch();

        if (!$page) {
            http_response_code(404);
            $this->render('pages/show', [
                'title' => 'Không tìm thấy trang',
                'error' => 'Trang bạn tìm kiếm không tồn tại hoặc đã bị xóa.'
            ], 'main');
            return;
        }

        $this->render('pages/show', [
            'title' => $page['title'],
            'seo_desc' => mb_substr(strip_tags($page['content']), 0, 150) . '...',
            'seo_image' => $page['thumbnail'],
            'page' => $page
        ], 'main');
    }
}
