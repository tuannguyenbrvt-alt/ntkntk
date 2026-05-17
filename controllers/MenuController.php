<?php
class MenuController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM menus ORDER BY sort_order ASC");
        $menus = $stmt->fetchAll();

        // Lấy danh sách bài viết và trang tĩnh để cho phép gán vào menu
        $stmtPosts = $db->query("SELECT id, title, slug, type FROM posts WHERE status = 'published' ORDER BY created_at DESC");
        $postsList = $stmtPosts->fetchAll();

        require_once ROOT_PATH . '/helpers/MenuHelper.php';
        $menuTree = MenuHelper::buildTree($menus);

        $this->render('admin/menus/index', [
            'title' => 'Quản lý Menu',
            'menuTree' => $menuTree,
            'flatMenus' => $menus,
            'postsList' => $postsList
        ], 'admin');
    }

    public function store() {
        $title = $_POST['title'] ?? '';
        $url = $_POST['url'] ?? '';
        $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO menus (title, url, parent_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $url, $parent_id]);

        $_SESSION['success'] = 'Thêm menu thành công!';
        $this->redirect('/admin/menus');
    }

    public function update() {
        $id = $_POST['id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $url = $_POST['url'] ?? '';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE menus SET title = ?, url = ? WHERE id = ?");
        $stmt->execute([$title, $url, $id]);

        $_SESSION['success'] = 'Cập nhật menu thành công!';
        $this->redirect('/admin/menus');
    }

    public function delete() {
        $id = $_POST['id'] ?? 0;

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM menus WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = 'Xóa menu thành công!';
        $this->redirect('/admin/menus');
    }

    public function reorder() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!empty($data) && is_array($data)) {
            $db = Database::getInstance()->getConnection();
            try {
                $db->beginTransaction();
                foreach ($data as $item) {
                    $stmt = $db->prepare("UPDATE menus SET parent_id = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute([
                        $item['parent_id'] !== '' ? $item['parent_id'] : null, 
                        $item['sort_order'], 
                        $item['id']
                    ]);
                }
                $db->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        }
    }
}
