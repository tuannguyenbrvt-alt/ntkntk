<?php
class AdminUserController extends Controller {
    public function __construct() {
        // Chỉ super_admin và admin mới được vào, nhưng chỉ super_admin mới được trao quyền Admin
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $search = trim($_GET['q'] ?? '');
        $db = Database::getInstance()->getConnection();
        if ($search !== '') {
            $stmt = $db->prepare("SELECT * FROM users WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ? OR phone LIKE ? ORDER BY created_at DESC");
            $stmt->execute(['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);
        } else {
            $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
        }
        $users = $stmt->fetchAll();

        $this->render('admin/users/index', [
            'title' => 'Quản lý Tài khoản & Phân quyền',
            'users' => $users,
            'search' => $search
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

    public function sessions() {
        $db = Database::getInstance()->getConnection();

        // Tự động hết hạn các session cũ ngay khi Admin vào trang này để số liệu luôn mới nhất
        try {
            $db->exec("UPDATE user_sessions SET status = 'expired', logout_at = last_activity_at WHERE status = 'active' AND last_activity_at < (NOW() - INTERVAL 15 MINUTE)");
        } catch (Exception $e) {}

        $search = trim($_GET['q'] ?? '');
        $statusFilter = trim($_GET['status'] ?? '');
        
        $page = (int)($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $params = [];
        $where = [];

        if ($search !== '') {
            $where[] = "(u.username LIKE ? OR u.full_name LIKE ? OR us.ip_address LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        if ($statusFilter !== '') {
            $where[] = "us.status = ?";
            $params[] = $statusFilter;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Đếm tổng số bản ghi
        $countQuery = "SELECT COUNT(*) FROM user_sessions us JOIN users u ON us.user_id = u.id $whereClause";
        $stmtCount = $db->prepare($countQuery);
        $stmtCount->execute($params);
        $totalRecords = $stmtCount->fetchColumn();
        $totalPages = ceil($totalRecords / $limit);

        // Truy vấn dữ liệu phân trang
        $dataQuery = "
            SELECT us.*, u.username, u.full_name, u.role
            FROM user_sessions us
            JOIN users u ON us.user_id = u.id
            $whereClause
            ORDER BY us.login_at DESC, us.id DESC
            LIMIT $limit OFFSET $offset
        ";
        $stmtData = $db->prepare($dataQuery);
        $stmtData->execute($params);
        $sessions = $stmtData->fetchAll();

        $this->render('admin/users/sessions', [
            'title' => 'Lịch sử truy cập hệ thống',
            'sessions' => $sessions,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords
        ], 'admin');
    }

    public function sessionDetail() {
        $id = (int)($_GET['id'] ?? 0);
        $db = Database::getInstance()->getConnection();

        // Lấy chi tiết session
        $stmtSession = $db->prepare("
            SELECT us.*, u.username, u.full_name, u.role, u.email, u.avatar
            FROM user_sessions us
            JOIN users u ON us.user_id = u.id
            WHERE us.id = ?
        ");
        $stmtSession->execute([$id]);
        $session = $stmtSession->fetch();

        if (!$session) {
            $_SESSION['error'] = 'Không tìm thấy phiên làm việc.';
            $this->redirect('/admin/sessions');
            return;
        }

        // Lấy danh sách bài học đã mở xem trong session này
        $stmtLessons = $db->prepare("
            SELECT usl.viewed_at, cl.title AS lesson_title, cl.id AS lesson_id, cc.title AS chapter_title, c.title AS course_title, c.id AS course_id
            FROM user_session_lessons usl
            JOIN course_lessons cl ON usl.lesson_id = cl.id
            JOIN course_chapters cc ON cl.chapter_id = cc.id
            JOIN course_parts cp ON cc.part_id = cp.id
            JOIN courses c ON cp.course_id = c.id
            WHERE usl.user_session_id = ?
            ORDER BY usl.viewed_at ASC
        ");
        $stmtLessons->execute([$id]);
        $viewedLessons = $stmtLessons->fetchAll();

        $this->render('admin/users/session_detail', [
            'title' => 'Chi tiết phiên làm việc #' . $id,
            'session' => $session,
            'viewedLessons' => $viewedLessons
        ], 'admin');
    }
}
