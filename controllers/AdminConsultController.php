<?php
class AdminConsultController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login'); exit;
        }
    }

    public function index() {
        $db   = Database::getInstance()->getConnection();
        $status = $_GET['status'] ?? '';
        $where = $status ? "WHERE status = " . $db->quote($status) : '';
        $rows = $db->query("SELECT * FROM consultation_requests $where ORDER BY created_at DESC")->fetchAll();
        $counts = $db->query("SELECT status, COUNT(*) as cnt FROM consultation_requests GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
        $this->render('admin/consults/index', [
            'title'   => 'Danh sách Đăng ký Tư vấn',
            'rows'    => $rows,
            'counts'  => $counts,
            'filter'  => $status,
        ], 'admin');
    }

    public function updateStatus() {
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $note   = trim($_POST['note'] ?? '');
        if (!$id || !in_array($status, ['new', 'called', 'done'])) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.'; $this->redirect('/admin/consults'); return;
        }
        $db = Database::getInstance()->getConnection();
        $db->prepare("UPDATE consultation_requests SET status=?, note=? WHERE id=?")->execute([$status, $note, $id]);
        $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
        $this->redirect('/admin/consults');
    }

    public function delete() {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Database::getInstance()->getConnection()
                ->prepare("DELETE FROM consultation_requests WHERE id=?")->execute([$id]);
            $_SESSION['success'] = 'Đã xoá yêu cầu tư vấn.';
        }
        $this->redirect('/admin/consults');
    }
}
