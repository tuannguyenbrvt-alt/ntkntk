<?php
class AdminPostController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $search = trim($_GET['q'] ?? '');
        $db = Database::getInstance()->getConnection();
        if ($search !== '') {
            $stmt = $db->prepare("SELECT p.*, u.full_name as author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.title LIKE ? OR p.content LIKE ? ORDER BY p.created_at DESC");
            $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
        } else {
            $stmt = $db->query("SELECT p.*, u.full_name as author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.created_at DESC");
        }
        $posts = $stmt->fetchAll();

        $this->render('admin/posts/index', ['title' => 'Quản lý Bài viết', 'posts' => $posts, 'search' => $search], 'admin');
    }

    public function create() {
        $this->render('admin/posts/form', ['title' => 'Thêm bài viết mới'], 'admin');
    }

    public function store() {
        if (empty($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['error'] = 'Lỗi: Dung lượng file tải lên vượt quá giới hạn của máy chủ. Vui lòng chọn ảnh nhẹ hơn.';
            $this->redirect('/admin/posts/create');
            return;
        }
        $title = $_POST['title'] ?? '';
        require_once ROOT_PATH . '/helpers/SlugHelper.php';
        $slug = !empty($_POST['slug']) ? $_POST['slug'] : SlugHelper::generate($title);
        $content = $_POST['content'] ?? '';
        $type = $_POST['type'] ?? 'blog';
        $status = $_POST['status'] ?? 'draft';
        $author_id = $_SESSION['user_id'];
        $thumbnail = null;

        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Lỗi tải ảnh. Mã lỗi PHP: ' . $_FILES['thumbnail']['error'] . '. Vui lòng kiểm tra lại dung lượng ảnh (Nên < 2MB).';
                $this->redirect('/admin/posts/create');
                return;
            }
            require_once ROOT_PATH . '/helpers/UploadHelper.php';
            try {
                $thumbnail = UploadHelper::uploadImage($_FILES['thumbnail']);
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/admin/posts/create');
                return;
            }
        }

        $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;
        $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
        $is_pinned = ($type === 'blog') ? $is_pinned : 0;

        $db = Database::getInstance()->getConnection();
        
        // Check slug exist
        $stmtCheck = $db->prepare("SELECT id FROM posts WHERE slug = ?");
        $stmtCheck->execute([$slug]);
        if ($stmtCheck->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare("INSERT INTO posts (title, slug, content, thumbnail, type, status, author_id, allow_comments, is_pinned) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $slug, $content, $thumbnail, $type, $status, $author_id, $allow_comments, $is_pinned])) {
            $_SESSION['success'] = 'Thêm bài viết thành công!';
            $this->redirect('/admin/posts');
        } else {
            $_SESSION['error'] = 'Lỗi lưu database.';
            $this->redirect('/admin/posts/create');
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();

        if (!$post) $this->redirect('/admin/posts');

        $this->render('admin/posts/form', ['title' => 'Sửa bài viết', 'post' => $post], 'admin');
    }

    public function update() {
        if (empty($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['error'] = 'Lỗi: Dung lượng file tải lên vượt quá giới hạn của máy chủ. Vui lòng chọn ảnh nhẹ hơn.';
            $this->redirect('/admin/posts');
            return;
        }
        $id = $_POST['id'] ?? 0;
        $title = $_POST['title'] ?? '';
        require_once ROOT_PATH . '/helpers/SlugHelper.php';
        $slug = !empty($_POST['slug']) ? $_POST['slug'] : SlugHelper::generate($title);
        $content = $_POST['content'] ?? '';
        $type = $_POST['type'] ?? 'blog';
        $status = $_POST['status'] ?? 'draft';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT thumbnail FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        $thumbnail = $post['thumbnail'];

        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Lỗi tải ảnh. Mã lỗi PHP: ' . $_FILES['thumbnail']['error'] . '. Vui lòng kiểm tra lại dung lượng ảnh (Nên < 2MB).';
                $this->redirect('/admin/posts/edit?id=' . $id);
                return;
            }
            require_once ROOT_PATH . '/helpers/UploadHelper.php';
            try {
                $thumbnail = UploadHelper::uploadImage($_FILES['thumbnail']);
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/admin/posts/edit?id=' . $id);
                return;
            }
        }

        $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;
        $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
        $is_pinned = ($type === 'blog') ? $is_pinned : 0;

        $stmtCheck = $db->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
        $stmtCheck->execute([$slug, $id]);
        if ($stmtCheck->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare("UPDATE posts SET title = ?, slug = ?, content = ?, thumbnail = ?, type = ?, status = ?, allow_comments = ?, is_pinned = ? WHERE id = ?");
        if ($stmt->execute([$title, $slug, $content, $thumbnail, $type, $status, $allow_comments, $is_pinned, $id])) {
            $_SESSION['success'] = 'Cập nhật thành công!';
            $this->redirect('/admin/posts');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra.';
            $this->redirect('/admin/posts/edit?id=' . $id);
        }
    }

    public function delete() {
        $id = $_POST['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Đã xóa bài viết!';
        $this->redirect('/admin/posts');
    }

    public function togglePin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/posts');
            return;
        }
        $id = (int)($_POST['id'] ?? 0);
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT is_pinned, type FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
        
        if ($post) {
            if ($post['type'] !== 'blog') {
                $_SESSION['error'] = 'Chỉ có thể ghim bài viết dạng Blog.';
                $this->redirect('/admin/posts');
                return;
            }
            $newPin = $post['is_pinned'] ? 0 : 1;
            $stmtUpdate = $db->prepare("UPDATE posts SET is_pinned = ? WHERE id = ?");
            $stmtUpdate->execute([$newPin, $id]);
            $_SESSION['success'] = $newPin ? 'Đã ghim bài viết lên đầu trang thành công!' : 'Đã bỏ ghim bài viết.';
        } else {
            $_SESSION['error'] = 'Không tìm thấy bài viết.';
        }
        $this->redirect('/admin/posts');
    }
}
