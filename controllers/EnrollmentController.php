<?php
class EnrollmentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để đăng ký khóa học.';
            $this->redirect('/login');
        }
    }

    // Học viên checkout
    public function checkout() {
        $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ? AND status = 'published'");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch();

        if (!$course) {
            $this->redirect('/courses');
        }

        // Check if already enrolled
        $stmtCheck = $db->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmtCheck->execute([$_SESSION['user_id'], $course_id]);
        $enrollment = $stmtCheck->fetch();

        if ($enrollment) {
            if ($enrollment['status'] == 'active') {
                $this->redirect('/learning?course_id=' . $course_id);
                return;
            } elseif ($enrollment['status'] == 'pending') {
                $_SESSION['success'] = 'Bạn đã gửi yêu cầu. Vui lòng chờ Admin duyệt.';
                $this->redirect('/course?slug=' . $course['slug']);
                return;
            }
        }

        // Tạo mã giao dịch random
        $tx_code = 'NTK' . time() . rand(10, 99);

        // Lưu vào DB trạng thái pending
        $stmtInsert = $db->prepare("INSERT INTO enrollments (student_id, course_id, status, price_paid, payment_method, tx_code) VALUES (?, ?, 'pending', ?, 'chuyen_khoan', ?)");
        $stmtInsert->execute([$_SESSION['user_id'], $course_id, $course['price'], $tx_code]);
        $enrollment_id = $db->lastInsertId();

        // Redirect to confirm UI
        $this->redirect('/enrollment/confirm?id=' . $enrollment_id);
    }

    public function confirm() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT e.*, c.title, c.price, c.slug FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.id = ? AND e.student_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $enrollment = $stmt->fetch();

        if (!$enrollment) $this->redirect('/courses');

        $this->render('enrollments/checkout', [
            'title' => 'Thanh toán Khóa học',
            'enrollment' => $enrollment
        ], 'main');
    }

    public function done() {
        $_SESSION['success'] = 'Đã xác nhận thanh toán. Vui lòng chờ hệ thống kiểm tra và duyệt kích hoạt khóa học!';
        $this->redirect('/courses');
    }

    // Admin xử lý enrollments
    public function adminIndex() {
        if (!in_array($_SESSION['role'], ['super_admin', 'admin'])) $this->redirect('/');
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT e.*, u.full_name, u.email, c.title as course_title FROM enrollments e JOIN users u ON e.student_id = u.id JOIN courses c ON e.course_id = c.id ORDER BY e.created_at DESC");
        $enrollments = $stmt->fetchAll();

        $this->render('admin/enrollments/index', [
            'title' => 'Quản lý Duyệt khóa học',
            'enrollments' => $enrollments
        ], 'admin');
    }

    public function adminApprove() {
        if (!in_array($_SESSION['role'], ['super_admin', 'admin'])) $this->redirect('/');
        $id = $_POST['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE enrollments SET status = 'active' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Đã duyệt kích hoạt khóa học thành công!';
        $this->redirect('/admin/enrollments');
    }

    public function adminUpdate() {
        if (!in_array($_SESSION['role'], ['super_admin', 'admin'])) $this->redirect('/');
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $note   = trim($_POST['note'] ?? '');
        if (!$id || !in_array($status, ['pending', 'active', 'cancelled'])) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ.';
            $this->redirect('/admin/enrollments');
            return;
        }
        $db = Database::getInstance()->getConnection();
        $db->prepare("UPDATE enrollments SET status = ?, note = ? WHERE id = ?")->execute([$status, $note, $id]);
        $_SESSION['success'] = 'Cập nhật đăng ký thành công!';
        $this->redirect('/admin/enrollments');
    }

    public function adminDelete() {
        if (!in_array($_SESSION['role'], ['super_admin', 'admin'])) $this->redirect('/');
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            Database::getInstance()->getConnection()
                ->prepare("DELETE FROM enrollments WHERE id = ?")->execute([$id]);
            $_SESSION['success'] = 'Đã xóa đăng ký thành công.';
        }
        $this->redirect('/admin/enrollments');
    }
}
