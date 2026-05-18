<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
    die('<h3 style="color:red">Không có quyền truy cập.</h3>');
}

require_once __DIR__ . '/../config/config.php';

$action = $_GET['action'] ?? '';

// Xu ly callback tu Google
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $clientId = $_SESSION['oauth_client_id'];
    $clientSecret = $_SESSION['oauth_client_secret'];
    $redirectUri = APP_URL . '/admin/setup-drive-oauth.php';

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    ]);
    $res = curl_exec($ch);
    $codeHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($res, true);

    if ($codeHttp === 200 && isset($data['refresh_token'])) {
        // Luu vao config
        $configData = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $data['refresh_token']
        ];
        file_put_contents(ROOT_PATH . '/config/google-oauth.json', json_encode($configData, JSON_PRETTY_PRINT));
        $success = true;
    } else {
        $error = "Lỗi khi lấy Refresh Token: " . htmlspecialchars($res) . "<br>Lưu ý: Nếu không có refresh_token, hãy thử xóa quyền ứng dụng trong tài khoản Google và thử lại.";
    }
}

// Xu ly submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'start') {
    $_SESSION['oauth_client_id'] = trim($_POST['client_id']);
    $_SESSION['oauth_client_secret'] = trim($_POST['client_secret']);
    
    $redirectUri = urlencode(APP_URL . '/admin/setup-drive-oauth.php');
    $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?client_id={$_SESSION['oauth_client_id']}&redirect_uri={$redirectUri}&response_type=code&scope=https://www.googleapis.com/auth/drive&access_type=offline&prompt=consent";
    header("Location: $authUrl");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Cài đặt Google Drive (OAuth2)</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:800px;">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-google me-2"></i>Khắc phục lỗi Quota - Cài đặt Drive bằng tài khoản thật</h5>
        </div>
        <div class="card-body p-4">
            <?php if(isset($success)): ?>
                <div class="alert alert-success text-center">
                    <h4><i class="bi bi-check-circle-fill me-2"></i>Cài đặt thành công!</h4>
                    <p>Hệ thống đã nhận được Refresh Token và lưu vào <code>config/google-oauth.json</code>.</p>
                    <p>Bây giờ học viên nộp bài sẽ tự động tải lên tài khoản Google Drive của bạn (dùng dung lượng 15GB miễn phí của bạn).</p>
                    <a href="<?php echo APP_URL; ?>/admin/test-drive.php" class="btn btn-primary mt-3">Quay lại trang Kiểm tra (Test)</a>
                </div>
            <?php else: ?>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <strong>Vì sao có lỗi 403 Storage Quota?</strong><br>
                    Google gần đây đã cắt bỏ 15GB miễn phí của Service Account. Vì vậy, Service Account không thể lưu file vào thư mục của tài khoản Gmail miễn phí.<br>
                    <strong>Giải pháp:</strong> Bạn cần cấp quyền cho hệ thống truy cập thẳng vào tài khoản Gmail của bạn thông qua <b>OAuth2</b>.
                </div>

                <h5 class="fw-bold mt-4">Bước 1: Tạo OAuth Client ID</h5>
                <ol>
                    <li>Vào <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console > Credentials</a></li>
                    <li>Bấm <strong>+ CREATE CREDENTIALS</strong> > Chọn <strong>OAuth client ID</strong>.</li>
                    <li>Application type: Chọn <strong>Web application</strong>.</li>
                    <li>Name: Điền tùy ý (VD: LMS Upload).</li>
                    <li>Mục <strong>Authorized redirect URIs</strong>, bấm Add URI và dán chính xác link này vào:<br>
                        <code class="bg-dark text-warning p-1 rounded"><?php echo APP_URL; ?>/admin/setup-drive-oauth.php</code>
                    </li>
                    <li>Bấm <strong>CREATE</strong>. Google sẽ cấp cho bạn <code>Client ID</code> và <code>Client Secret</code>.</li>
                </ol>

                <h5 class="fw-bold mt-5">Bước 2: Kết nối tài khoản</h5>
                <form method="POST" action="?action=start" class="bg-light p-4 rounded border">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Client ID</label>
                        <input type="text" name="client_id" class="form-control" required placeholder="Gán Client ID vào đây...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Client Secret</label>
                        <input type="text" name="client_secret" class="form-control" required placeholder="Gán Client Secret vào đây...">
                    </div>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập với Google
                    </button>
                    <div class="form-text mt-2">Khi bấm nút, bạn sẽ được chuyển sang Google để đăng nhập. Hãy chọn đúng tài khoản Gmail chứa thư mục lưu bài tập. (Nếu Google cảnh báo ứng dụng chưa xác minh, hãy bấm Nâng cao > Tiếp tục).</div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
