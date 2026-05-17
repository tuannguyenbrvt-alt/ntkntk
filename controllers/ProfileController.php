<?php
class ProfileController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xem hồ sơ.';
            $this->redirect('/login');
        }
    }

    public function dashboard() {
        $db = Database::getInstance()->getConnection();
        $user_id = $_SESSION['user_id'];
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        $stmtCourses = $db->prepare("SELECT c.*, e.created_at as enrolled_at FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? AND e.status = 'active' ORDER BY e.created_at DESC");
        $stmtCourses->execute([$user_id]);
        $enrolledCourses = $stmtCourses->fetchAll();

        foreach ($enrolledCourses as &$course) {
            $stmtLessons = $db->prepare("SELECT COUNT(li.id) FROM lesson_items li JOIN lessons l ON li.lesson_id = l.id JOIN chapters ch ON ch.id = l.chapter_id JOIN course_parts cp ON cp.id = ch.part_id WHERE cp.course_id = ?");
            $stmtLessons->execute([$course['id']]);
            $totalLessons = (int)$stmtLessons->fetchColumn();

            $stmtProgress = $db->prepare("SELECT COUNT(*) FROM course_progress WHERE student_id = ? AND course_id = ?");
            $stmtProgress->execute([$user_id, $course['id']]);
            $doneLessons = (int)$stmtProgress->fetchColumn();

            $course['total_lessons']  = $totalLessons;
            $course['done_lessons']   = $doneLessons;
            $course['progress_pct']   = $totalLessons > 0 ? round($doneLessons / $totalLessons * 100) : 0;
        }

        $this->render('profile/dashboard', [
            'title'           => 'Hồ sơ học tập',
            'user'            => $user,
            'enrolledCourses' => $enrolledCourses
        ], 'main');
    }

    public function settings() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        $this->render('profile/settings', [
            'title' => 'Cài đặt tài khoản',
            'user' => $user
        ], 'main');
    }

    public function update() {
        if (empty($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['error'] = 'Lỗi: Dung lượng file tải lên quá lớn.';
            $this->redirect('/profile/settings');
            return;
        }

        $full_name = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $dob = $_POST['dob'] ?? null;
        if(empty($dob)) $dob = null;
        $address = $_POST['address'] ?? '';
        $profession = $_POST['profession'] ?? '';
        $user_id = $_SESSION['user_id'];

        $db = Database::getInstance()->getConnection();

        $stmtCheck = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmtCheck->execute([$email, $user_id]);
        if ($stmtCheck->fetch()) {
            $_SESSION['error'] = 'Email này đã được sử dụng bởi tài khoản khác.';
            $this->redirect('/profile/settings');
            return;
        }
        
        $stmtCurrent = $db->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmtCurrent->execute([$user_id]);
        $currentUser = $stmtCurrent->fetch();
        $avatar = $currentUser['avatar'];

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Lỗi tải ảnh đại diện. Mã lỗi PHP: ' . $_FILES['avatar']['error'] . ' (Nên chọn ảnh < 2MB).';
                $this->redirect('/profile/settings');
                return;
            }
            require_once ROOT_PATH . '/helpers/UploadHelper.php';
            try {
                $avatar = UploadHelper::uploadImage($_FILES['avatar'], 'uploads/users/');
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/profile/settings');
                return;
            }
        }

        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, dob = ?, address = ?, profession = ?, avatar = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $email, $phone, $dob, $address, $profession, $avatar, $user_id])) {
            $_SESSION['full_name'] = $full_name; 
            $_SESSION['avatar'] = $avatar;
            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi lưu trữ.';
        }
        $this->redirect('/profile/settings');
    }

    public function updatePassword() {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $user_id = $_SESSION['user_id'];

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (password_verify($old_password, $user['password'])) {
            if (strlen($new_password) >= 6) {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmtUpdate = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmtUpdate->execute([$hash, $user_id]);
                $_SESSION['success'] = 'Đổi mật khẩu thành công!';
            } else {
                $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            }
        } else {
            $_SESSION['error'] = 'Mật khẩu hiện tại không đúng.';
        }
        $this->redirect('/profile/settings');
    }
}
