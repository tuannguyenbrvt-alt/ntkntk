<?php
// controllers/CertificateController.php
class CertificateController extends Controller {
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $courseId = (int)($_GET['course_id'] ?? 0);
        $userId   = $_SESSION['user_id'];
        $db = Database::getInstance()->getConnection();

        // Kiểm tra học viên đã đăng ký khóa học và đang active chưa
        $stmtE = $db->prepare("SELECT e.*, c.title as course_title, c.slug as course_slug FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? AND e.course_id = ? AND e.status = 'active'");
        $stmtE->execute([$userId, $courseId]);
        $enrollment = $stmtE->fetch();

        if (!$enrollment) {
            $_SESSION['error'] = 'Bạn chưa hoàn thành hoặc chưa đăng ký khóa học này.';
            $this->redirect('/profile');
            return;
        }

        // Lấy thông tin học viên
        $stmtU = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmtU->execute([$userId]);
        $user = $stmtU->fetch();

        // Kiểm tra tiến độ hoàn thành (>= 80% coi là đủ điều kiện nhận chứng chỉ)
        $stmtTotal = $db->prepare("SELECT COUNT(li.id) FROM lessons l JOIN lesson_items li ON li.lesson_id = l.id JOIN chapters ch ON ch.id = l.chapter_id JOIN course_parts cp ON cp.id = ch.part_id WHERE cp.course_id = ?");
        $stmtTotal->execute([$courseId]);
        $totalItems = (int)$stmtTotal->fetchColumn();

        $stmtDone = $db->prepare("SELECT COUNT(*) FROM course_progress WHERE student_id = ? AND course_id = ?");
        $stmtDone->execute([$userId, $courseId]);
        $doneItems = (int)$stmtDone->fetchColumn();

        $progressPct = $totalItems > 0 ? round($doneItems / $totalItems * 100) : 100;

        if ($progressPct < 80 && $totalItems > 0) {
            $_SESSION['error'] = "Bạn mới hoàn thành {$progressPct}% khóa học. Cần đạt ít nhất 80% để nhận chứng chỉ.";
            $this->redirect('/profile');
            return;
        }

        $this->render('certificate/template', [
            'title'      => 'Chứng chỉ hoàn thành',
            'user'       => $user,
            'enrollment' => $enrollment,
            'certDate'   => date('d/m/Y'),
            'certCode'   => strtoupper(substr(md5($userId . $courseId . APP_NAME), 0, 12)),
        ], 'certificate');
    }
}
