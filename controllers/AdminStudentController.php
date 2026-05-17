<?php
class AdminStudentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT u.*, (SELECT COUNT(*) FROM enrollments WHERE student_id = u.id AND status = 'active') as active_courses FROM users u WHERE u.role = 'student' ORDER BY u.created_at DESC");
        $students = $stmt->fetchAll();

        $this->render('admin/students/index', [
            'title' => 'Quản lý Học viên',
            'students' => $students
        ], 'admin');
    }

    public function show() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
        $stmt->execute([$id]);
        $student = $stmt->fetch();

        if (!$student) {
            $this->redirect('/admin/students');
        }

        $stmtCourses = $db->prepare("SELECT c.title, c.price, e.status, e.price_paid, e.created_at as enrolled_at, e.tx_code FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? ORDER BY e.created_at DESC");
        $stmtCourses->execute([$id]);
        $enrollments = $stmtCourses->fetchAll();

        $totalPaid = 0;
        foreach ($enrollments as $en) {
            if ($en['status'] == 'active') {
                $totalPaid += $en['price_paid'];
            }
        }

        $this->render('admin/students/show', [
            'title' => 'Chi tiết Hồ sơ Học viên',
            'student' => $student,
            'enrollments' => $enrollments,
            'totalPaid' => $totalPaid
        ], 'admin');
    }

    public function update() {
        $id = $_POST['id'] ?? 0;
        $full_name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $profession = $_POST['profession'] ?? '';
        $address = $_POST['address'] ?? '';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE users SET full_name = ?, phone = ?, profession = ?, address = ? WHERE id = ?");
        $stmt->execute([$full_name, $phone, $profession, $address, $id]);
        
        $_SESSION['success'] = 'Cập nhật hồ sơ học viên thành công.';
        $this->redirect('/admin/students/show?id=' . $id);
    }
}
