<?php
class DashboardController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $db = Database::getInstance()->getConnection();

        // 1. Thống kê tổng quan (Cards)
        $totalRevenue = $db->query("SELECT SUM(price_paid) FROM enrollments WHERE status = 'active'")->fetchColumn() ?: 0;
        $totalStudents = $db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
        $totalCourses = $db->query("SELECT COUNT(*) FROM courses WHERE status = 'published'")->fetchColumn();
        $pendingEnrollments = $db->query("SELECT COUNT(*) FROM enrollments WHERE status = 'pending'")->fetchColumn();

        // 2. Thống kê doanh thu theo 12 tháng của năm hiện tại
        $year = date('Y');
        $stmtChart = $db->prepare("SELECT MONTH(created_at) as month, SUM(price_paid) as revenue FROM enrollments WHERE status = 'active' AND YEAR(created_at) = ? GROUP BY MONTH(created_at) ORDER BY month ASC");
        $stmtChart->execute([$year]);
        $monthlyData = $stmtChart->fetchAll();
        
        $chartLabels = [];
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = "Tháng $i";
            $chartData[$i] = 0;
        }
        foreach ($monthlyData as $row) {
            $chartData[$row['month']] = (float)$row['revenue'];
        }
        $chartDataValues = array_values($chartData);

        // 3. 5 Giao dịch / Đăng ký gần nhất
        $stmtRecent = $db->query("SELECT e.*, u.full_name, c.title as course_title FROM enrollments e JOIN users u ON e.student_id = u.id JOIN courses c ON e.course_id = c.id ORDER BY e.created_at DESC LIMIT 6");
        $recentEnrollments = $stmtRecent->fetchAll();

        $this->render('admin/dashboard/index', [
            'title' => 'Tổng quan hệ thống',
            'totalRevenue' => $totalRevenue,
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'pendingEnrollments' => $pendingEnrollments,
            'chartLabels' => json_encode($chartLabels),
            'chartDataValues' => json_encode($chartDataValues),
            'recentEnrollments' => $recentEnrollments,
            'currentYear' => $year
        ], 'admin');
    }
}
