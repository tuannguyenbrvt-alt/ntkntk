<?php
class AdminUserController extends Controller {
    public function __construct() {
        // Chỉ super_admin và admin mới được vào, nhưng chỉ super_admin mới được trao quyền Admin
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        // Lấy danh sách tất cả người dùng
        $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();

        $this->render('admin/users/index', [
            'title' => 'Quản lý Tài khoản & Phân quyền',
            'users' => $users
        ], 'admin');
    }

    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('/admin/users');

        $id = (int)($_POST['id'] ?? 0);
        $newRole = $_POST['role'] ?? '';
        $allowedRoles = ['admin', 'teacher', 'student', 'guest'];

        // Bảo mật: Admin thường không thể tự hạ cấp mình hoặc nâng cấp người khác lên Super Admin
        if (!in_array($newRole, $allowedRoles)) {
            $_SESSION['error'] = 'Vai trò không hợp lệ.';
            $this->redirect('/admin/users');
            return;
        }

        $db = Database::getInstance()->getConnection();
        
        // Kiểm tra user mục tiêu
        $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $targetUser = $stmt->fetch();

        if (!$targetUser) {
            $_SESSION['error'] = 'Không tìm thấy người dùng.';
            $this->redirect('/admin/users');
            return;
        }

        if ($targetUser['role'] === 'super_admin' && $_SESSION['role'] !== 'super_admin') {
            $_SESSION['error'] = 'Bạn không có quyền thay đổi tài khoản Super Admin.';
            $this->redirect('/admin/users');
            return;
        }

        $stmtUpdate = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmtUpdate->execute([$newRole, $id]);

        $_SESSION['success'] = 'Cập nhật phân quyền thành công!';
        $this->redirect('/admin/users');
    }

    public function delete() {
        if ($_SESSION['role'] !== 'super_admin') {
            $_SESSION['error'] = 'Chỉ Super Admin mới có quyền xóa tài khoản.';
            $this->redirect('/admin/users');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Bạn không thể tự xóa tài khoản của chính mình.';
            $this->redirect('/admin/users');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'super_admin'");
        $stmt->execute([$id]);

        $_SESSION['success'] = 'Đã xóa người dùng thành công.';
        $this->redirect('/admin/users');
    }
}
