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
            
            $code = $_GET['code'] ?? '';
            if (empty($code)) {
                $_SESSION['error'] = 'Xác thực Google Drive thất bại hoặc bị hủy.';
                $this->redirect('/admin/assignments/pending');
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
            if (empty($tokenData['refresh_token'])) {
                $_SESSION['error'] = 'Không thể lấy Refresh Token từ Google. Vui lòng đảm bảo bạn chọn đúng tài khoản và đồng ý cấp quyền truy cập Drive ở màn hình tiếp theo.';
                $this->redirect('/admin/assignments/pending');
                return;
            }
            
            $creds = [
                'client_id'     => GOOGLE_CLIENT_ID,
                'client_secret' => GOOGLE_CLIENT_SECRET,
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

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
