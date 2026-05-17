<?php
class AuthController extends Controller {
    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $this->render('auth/login', ['title' => 'Đăng nhập'], 'main');
    }

    public function postLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['avatar'] = $user['avatar'] ?? null;

            if ($user['role'] === 'super_admin' || $user['role'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/');
            }
        } else {
            // Sai tài khoản hoặc mật khẩu
            $this->render('auth/login', [
                'title' => 'Đăng nhập',
                'error' => 'Tên đăng nhập hoặc mật khẩu không đúng.'
            ], 'main');
        }
    }

    public function register() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $this->render('auth/register', ['title' => 'Đăng ký'], 'main');
    }

    public function postRegister() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $fullname = $_POST['full_name'] ?? '';

        $db = Database::getInstance()->getConnection();
        
        // Kiểm tra user tồn tại chưa
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $this->render('auth/register', [
                'title' => 'Đăng ký',
                'error' => 'Tên đăng nhập hoặc email đã tồn tại.'
            ], 'main');
            return;
        }

        // Tạo tài khoản mới (mặc định là student)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, 'student')");
        
        if ($stmt->execute([$username, $hashedPassword, $email, $fullname])) {
            $this->redirect('/login');
        } else {
            $this->render('auth/register', [
                'title' => 'Đăng ký',
                'error' => 'Có lỗi xảy ra, vui lòng thử lại.'
            ], 'main');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
