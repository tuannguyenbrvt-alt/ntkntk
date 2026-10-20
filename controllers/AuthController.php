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
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['avatar'] = $user['avatar'] ?? null;

            // Ghi nhận lịch sử đăng nhập
            require_once ROOT_PATH . '/helpers/TrackerHelper.php';
            TrackerHelper::recordLogin($db, $user['id']);

            if ($user['role'] === 'super_admin' || $user['role'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/');
            }
        } else {
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
        // 1. Kiểm tra Honeypot (Bẫy bot ngầm: người dùng thật không nhìn thấy trường này)
        if (!empty($_POST['website_hp'])) {
            $this->render('auth/register', [
                'title' => 'Đăng ký',
                'error' => 'Phát hiện yêu cầu không hợp lệ (Spam bot).'
            ], 'main');
            return;
        }

        // 2. Xác thực Cloudflare Turnstile (Chống bot tự động)
        if (defined('TURNSTILE_ENABLED') && TURNSTILE_ENABLED) {
            $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
            require_once ROOT_PATH . '/helpers/TurnstileHelper.php';
            if (!TurnstileHelper::verify($turnstileToken)) {
                $this->render('auth/register', [
                    'title' => 'Đăng ký',
                    'error' => 'Xác thực "Không phải người máy" thất bại hoặc đã hết hạn. Vui lòng thử lại.'
                ], 'main');
                return;
            }
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $fullname = trim($_POST['full_name'] ?? '');
        
        $phone = trim($_POST['phone'] ?? '');
        $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
        $address = trim($_POST['address'] ?? '');
        $profession = trim($_POST['profession'] ?? '');

        if ($password !== $confirmPassword) {
            $this->render('auth/register', [
                'title' => 'Đăng ký',
                'error' => 'Mật khẩu nhập lại không khớp.'
            ], 'main');
            return;
        }

        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $this->render('auth/register', [
                'title' => 'Đăng ký',
                'error' => 'Tên đăng nhập hoặc email đã tồn tại.'
            ], 'main');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password, email, full_name, phone, dob, address, profession, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'student')");
        
        if ($stmt->execute([$username, $hashedPassword, $email, $fullname, $phone ?: null, $dob, $address ?: null, $profession ?: null])) {
            $_SESSION['success'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
            $this->redirect('/login');
        } else {
            $this->render('auth/register', [
                'title' => 'Đăng ký',
                'error' => 'Có lỗi xảy ra, vui lòng thử lại.'
            ], 'main');
        }
    }

    public function googleRedirect() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
            return;
        }
        
        $params = [
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid profile email',
            'state'         => bin2hex(random_bytes(16))
        ];
        
        $_SESSION['oauth2state'] = $params['state'];
        
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        $this->redirect($url);
    }

    public function googleCallback() {
        if (isset($_SESSION['oauth_purpose']) && $_SESSION['oauth_purpose'] === 'drive_setup') {
            unset($_SESSION['oauth_purpose']);
            
            $clientId = $_SESSION['drive_client_id'] ?? GOOGLE_CLIENT_ID;
            $clientSecret = $_SESSION['drive_client_secret'] ?? GOOGLE_CLIENT_SECRET;
            unset($_SESSION['drive_client_id']);
            unset($_SESSION['drive_client_secret']);

            $code = $_GET['code'] ?? '';
            if (empty($code)) {
                $_SESSION['error'] = 'Xác thực Google Drive thất bại hoặc bị hủy.';
                $this->redirect('/admin/assignments/pending');
                return;
            }
            
            $postParams = [
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => GOOGLE_REDIRECT_URI,
                'grant_type'    => 'authorization_code'
            ];
            
            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $tokenData = json_decode($response, true);
            if (empty($tokenData['refresh_token'])) {
                $_SESSION['error'] = 'Không thể lấy Refresh Token từ Google. Vui lòng đảm bảo bạn chọn đúng tài khoản và đồng ý cấp quyền truy cập Drive ở màn hình tiếp theo.';
                $this->redirect('/admin/assignments/pending');
                return;
            }
            
            $creds = [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $tokenData['refresh_token']
            ];
            
            $oauthPath = ROOT_PATH . '/config/google-oauth.json';
            if (file_put_contents($oauthPath, json_encode($creds, JSON_PRETTY_PRINT))) {
                $_SESSION['success'] = 'Liên kết Google Drive thành công! Refresh Token đã được cập nhật.';
            } else {
                $_SESSION['error'] = 'Lỗi ghi tệp config/google-oauth.json. Vui lòng kiểm tra quyền ghi thư mục config.';
            }
            
            $this->redirect('/admin/assignments/pending');
            return;
        }

        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
            return;
        }
        
        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            $_SESSION['error'] = 'Xác thực Google thất bại hoặc bị hủy.';
            $this->redirect('/login');
            return;
        }
        
        $postParams = [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code'
        ];
        
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'] ?? '';
        if (empty($accessToken)) {
            $_SESSION['error'] = 'Không thể lấy token xác thực từ Google.';
            $this->redirect('/login');
            return;
        }
        
        $ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $userInfoResponse = curl_exec($ch);
        curl_close($ch);
        
        $userInfo = json_decode($userInfoResponse, true);
        $email = $userInfo['email'] ?? '';
        $fullName = $userInfo['name'] ?? '';
        $avatar = $userInfo['picture'] ?? null;
        
        if (empty($email)) {
            $_SESSION['error'] = 'Không thể lấy thông tin email từ Google.';
            $this->redirect('/login');
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Ghi nhận lịch sử đăng nhập
            require_once ROOT_PATH . '/helpers/TrackerHelper.php';
            TrackerHelper::recordLogin($db, $user['id']);
            
            if (empty($user['avatar']) && !empty($avatar)) {
                $db->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$avatar, $user['id']]);
                $_SESSION['avatar'] = $avatar;
            } else {
                $_SESSION['avatar'] = $user['avatar'];
            }
            
            if ($user['role'] === 'super_admin' || $user['role'] === 'admin') {
                $this->redirect('/admin/dashboard');
            } else {
                $this->redirect('/');
            }
            return;
        } else {
            $usernamePrefix = explode('@', $email)[0];
            $username = $usernamePrefix;
            
            $chk = $db->prepare("SELECT id FROM users WHERE username = ?");
            $chk->execute([$username]);
            if ($chk->fetch()) {
                $username = $usernamePrefix . rand(100, 999);
            }
            
            $randomPassword = bin2hex(random_bytes(16));
            $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
            
            $ins = $db->prepare("INSERT INTO users (username, password, email, full_name, avatar, role) VALUES (?, ?, ?, ?, ?, 'student')");
            if ($ins->execute([$username, $hashedPassword, $email, $fullName, $avatar])) {
                $newUserId = $db->lastInsertId();
                
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'student';
                $_SESSION['full_name'] = $fullName;
                $_SESSION['avatar'] = $avatar;
                
                // Ghi nhận lịch sử đăng nhập
                require_once ROOT_PATH . '/helpers/TrackerHelper.php';
                TrackerHelper::recordLogin($db, $newUserId);
                
                $_SESSION['success'] = 'Đăng nhập thành công bằng Google! Tài khoản của bạn đã được khởi tạo tự động.';
                $this->redirect('/');
                return;
            } else {
                $_SESSION['error'] = 'Lỗi trong quá trình tạo tài khoản tự động.';
                $this->redirect('/login');
                return;
            }
        }
    }

    public function forgotPassword() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
            return;
        }
        $this->render('auth/forgot_password', ['title' => 'Quên mật khẩu'], 'main');
    }

    public function postForgotPassword() {
        // 1. Kiểm tra Honeypot (Bẫy bot ngầm)
        if (!empty($_POST['website_hp'])) {
            $this->render('auth/forgot_password', [
                'title' => 'Quên mật khẩu',
                'error' => 'Phát hiện yêu cầu không hợp lệ (Spam bot).'
            ], 'main');
            return;
        }

        // 2. Xác thực Cloudflare Turnstile
        if (defined('TURNSTILE_ENABLED') && TURNSTILE_ENABLED) {
            $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
            require_once ROOT_PATH . '/helpers/TurnstileHelper.php';
            if (!TurnstileHelper::verify($turnstileToken)) {
                $this->render('auth/forgot_password', [
                    'title' => 'Quên mật khẩu',
                    'error' => 'Xác thực "Không phải người máy" thất bại hoặc đã hết hạn. Vui lòng thử lại.'
                ], 'main');
                return;
            }
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/forgot_password', [
                'title' => 'Quên mật khẩu',
                'error' => 'Vui lòng nhập địa chỉ email hợp lệ.'
            ], 'main');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username, email, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Xóa các token cũ nếu có
            $del = $db->prepare("DELETE FROM password_resets WHERE email = ?");
            $del->execute([$email]);

            // Tạo token mới ngẫu nhiên 64 ký tự hex
            $token = bin2hex(random_bytes(32));
            // Thời hạn hết hạn: 30 phút
            $expiresAt = date('Y-m-d H:i:s', time() + 1800);

            $ins = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $ins->execute([$email, $token, $expiresAt]);

            $resetUrl = APP_URL . '/reset-password?token=' . urlencode($token);

            require_once ROOT_PATH . '/helpers/MailHelper.php';
            $sent = MailHelper::sendPasswordReset($user, $resetUrl);
            if (!$sent) {
                error_log("[AuthController] Không thể gửi email đặt lại mật khẩu cho " . $email);
            }
        }

        // Luôn trả về thông báo an toàn, bảo vệ danh tính email
        $this->render('auth/forgot_password', [
            'title' => 'Quên mật khẩu',
            'success' => 'Nếu email của bạn tồn tại trong hệ thống, hướng dẫn đặt lại mật khẩu đã được gửi đến hộp thư. Vui lòng kiểm tra Hộp thư đến (hoặc mục Thư rác/Spam).'
        ], 'main');
    }

    public function resetPassword() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
            return;
        }

        $token = trim($_GET['token'] ?? '');
        if (empty($token)) {
            $_SESSION['error'] = 'Mã xác thực đặt lại mật khẩu không hợp lệ.';
            $this->redirect('/login');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
        $stmt->execute([$token]);
        $resetRecord = $stmt->fetch();

        if (!$resetRecord) {
            $this->render('auth/reset_password', [
                'title' => 'Đặt lại mật khẩu',
                'error' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn (chỉ có hiệu lực trong 30 phút). Vui lòng gửi lại yêu cầu mới.',
                'expired' => true
            ], 'main');
            return;
        }

        $this->render('auth/reset_password', [
            'title' => 'Đặt lại mật khẩu',
            'token' => $token
        ], 'main');
    }

    public function postResetPassword() {
        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token)) {
            $_SESSION['error'] = 'Mã xác thực đặt lại mật khẩu không hợp lệ.';
            $this->redirect('/login');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
        $stmt->execute([$token]);
        $resetRecord = $stmt->fetch();

        if (!$resetRecord) {
            $this->render('auth/reset_password', [
                'title' => 'Đặt lại mật khẩu',
                'error' => 'Liên kết đặt lại mật khẩu đã hết hạn hoặc không tồn tại. Vui lòng gửi lại yêu cầu.',
                'expired' => true
            ], 'main');
            return;
        }

        if (strlen($password) < 6) {
            $this->render('auth/reset_password', [
                'title' => 'Đặt lại mật khẩu',
                'error' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
                'token' => $token
            ], 'main');
            return;
        }

        if ($password !== $confirmPassword) {
            $this->render('auth/reset_password', [
                'title' => 'Đặt lại mật khẩu',
                'error' => 'Mật khẩu xác nhận không khớp. Vui lòng nhập lại.',
                'token' => $token
            ], 'main');
            return;
        }

        // Băm mật khẩu mới
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Cập nhật mật khẩu cho học viên
        $update = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->execute([$hashedPassword, $resetRecord['email']]);

        // Xóa token đã hoàn thành
        $del = $db->prepare("DELETE FROM password_resets WHERE email = ?");
        $del->execute([$resetRecord['email']]);

        $_SESSION['success'] = 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới của bạn.';
        $this->redirect('/login');
    }

    public function logout() {
        $db = Database::getInstance()->getConnection();
        require_once ROOT_PATH . '/helpers/TrackerHelper.php';
        TrackerHelper::recordLogout($db);

        session_destroy();
        $this->redirect('/login');
    }
}
