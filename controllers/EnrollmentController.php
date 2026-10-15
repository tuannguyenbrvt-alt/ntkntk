<?php
class EnrollmentController extends Controller {
    public function __construct() {
        // Cho phép gọi API check-status mà không bị redirect HTML
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/api/enrollment/check-status') !== false) {
            return;
        }

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
                // Nếu đang chờ thanh toán, đưa thẳng học viên tới trang thanh toán để quét mã QR
                $this->redirect('/enrollment/confirm?id=' . $enrollment['id']);
                return;
            }
        }

        // Nếu khóa học Miễn phí -> Kích hoạt ngay lập tức
        if ((int)$course['price'] <= 0) {
            $tx_code = 'FREE' . rand(100000, 999999);
            $stmtInsert = $db->prepare("INSERT INTO enrollments (student_id, course_id, status, price_paid, payment_method, tx_code) VALUES (?, ?, 'active', 0, 'free', ?)");
            $stmtInsert->execute([$_SESSION['user_id'], $course_id, $tx_code]);
            $_SESSION['success'] = 'Đăng ký khóa học miễn phí thành công!';
            $this->redirect('/learning?course_id=' . $course_id);
            return;
        }

        // Tạo mã giao dịch NTK + 6 số ngẫu nhiên không trùng lặp
        $tx_code = '';
        for ($i = 0; $i < 10; $i++) {
            $candidate = 'NTK' . rand(100000, 999999);
            $stmtCheckTx = $db->prepare("SELECT id FROM enrollments WHERE tx_code = ?");
            $stmtCheckTx->execute([$candidate]);
            if (!$stmtCheckTx->fetch()) {
                $tx_code = $candidate;
                break;
            }
        }
        if (empty($tx_code)) {
            $tx_code = 'NTK' . time();
        }

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
        $search = trim($_GET['q'] ?? '');
        $db = Database::getInstance()->getConnection();
        if ($search !== '') {
            $stmt = $db->prepare("SELECT e.*, u.full_name, u.email, c.title as course_title FROM enrollments e JOIN users u ON e.student_id = u.id JOIN courses c ON e.course_id = c.id WHERE u.full_name LIKE ? OR u.email LIKE ? OR c.title LIKE ? OR e.tx_code LIKE ? ORDER BY e.created_at DESC");
            $stmt->execute(['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);
        } else {
            $stmt = $db->query("SELECT e.*, u.full_name, u.email, c.title as course_title FROM enrollments e JOIN users u ON e.student_id = u.id JOIN courses c ON e.course_id = c.id ORDER BY e.created_at DESC");
        }
        $enrollments = $stmt->fetchAll();

        $this->render('admin/enrollments/index', [
            'title' => 'Quản lý Duyệt khóa học',
            'enrollments' => $enrollments,
            'search' => $search
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

    /**
     * GET /api/enrollment/check-status?id=...&code=...
     * Kiểm tra trạng thái kích hoạt khóa học thời gian thực
     */
    public function checkStatus() {
        header('Content-Type: application/json; charset=utf-8');
        $id = (int)($_GET['id'] ?? 0);
        $tx_code = trim($_GET['code'] ?? '');

        if (!$id && empty($tx_code)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin đăng ký']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        if ($id) {
            $stmt = $db->prepare("SELECT e.*, c.title as course_title, c.slug FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.id = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $db->prepare("SELECT e.*, c.title as course_title, c.slug FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE UPPER(e.tx_code) = ?");
            $stmt->execute([strtoupper($tx_code)]);
        }

        $enrollment = $stmt->fetch();
        if (!$enrollment) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy lượt đăng ký']);
            return;
        }

        echo json_encode([
            'status' => $enrollment['status'], // 'pending' hoặc 'active'
            'course_id' => $enrollment['course_id'],
            'course_title' => $enrollment['course_title'],
            'learning_url' => APP_URL . '/learning?course_id=' . $enrollment['course_id']
        ]);
    }
}
