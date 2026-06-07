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

    public function setupChatDb() {
        $db = Database::getInstance()->getConnection();
        try {
            $db->exec("CREATE TABLE IF NOT EXISTS `chat_threads` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `student_id` int(11) DEFAULT NULL,
                `course_id` int(11) DEFAULT NULL,
                `guest_name` varchar(100) DEFAULT NULL,
                `guest_phone` varchar(20) DEFAULT NULL,
                `type` enum('student_teacher','guest_admin') NOT NULL,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `student_id` (`student_id`),
                KEY `course_id` (`course_id`),
                CONSTRAINT `chat_threads_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                CONSTRAINT `chat_threads_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            $db->exec("CREATE TABLE IF NOT EXISTS `chat_messages` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `thread_id` int(11) NOT NULL,
                `sender_id` int(11) DEFAULT NULL,
                `sender_name` varchar(100) DEFAULT NULL,
                `message_text` text DEFAULT NULL,
                `file_name` varchar(255) DEFAULT NULL,
                `file_path` varchar(500) DEFAULT NULL,
                `file_drive_url` varchar(500) DEFAULT NULL,
                `file_drive_id` varchar(100) DEFAULT NULL,
                `is_read` tinyint(1) NOT NULL DEFAULT '0',
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `thread_id` (`thread_id`),
                KEY `sender_id` (`sender_id`),
                CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `chat_threads` (`id`) ON DELETE CASCADE,
                CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            // Thêm cột is_recalled nếu chưa tồn tại
            try {
                $db->exec("ALTER TABLE `chat_messages` ADD COLUMN `is_recalled` tinyint(1) NOT NULL DEFAULT '0' AFTER `is_read`");
            } catch (Exception $e) {
                // Bỏ qua nếu cột đã tồn tại
            }
 
            $_SESSION['success'] = 'Khởi tạo cơ sở dữ liệu Chat trực tuyến thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi tạo bảng: ' . $e->getMessage();
        }
        $this->redirect('/admin/dashboard');
    }
}
