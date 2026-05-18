<?php
/**
 * Script kiem tra cau hinh Google Drive
 * Truy cap: yourdomain.com/admin/test-drive
 * XOA file nay sau khi da test xong!
 */
session_start();
// Bao ve - chi admin moi xem duoc
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
    die('<h3 style="color:red">Khong co quyen truy cap. Vui long dang nhap Admin truoc.</h3>');
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/GoogleDriveHelper.php';

$steps = [];

// =====================================================
// BUOC 1: Kiem tra file Service Account ton tai
// =====================================================
$saPath = ROOT_PATH . '/config/google-service-account.json';
if (file_exists($saPath)) {
    $steps[] = ['ok', 'File google-service-account.json ton tai tai: ' . $saPath];
    $saContent = file_get_contents($saPath);
} else {
    $steps[] = ['fail', 'KHONG TIM THAY file: ' . $saPath . ' — Hay upload file JSON len thu muc config/'];
    $saContent = null;
}

// =====================================================
// BUOC 2: Kiem tra JSON hop le
// =====================================================
if ($saContent) {
    $sa = json_decode($saContent, true);
    if ($sa && isset($sa['client_email'], $sa['private_key'], $sa['type'])) {
        $steps[] = ['ok', 'JSON hop le. Service Account Email: <strong>' . htmlspecialchars($sa['client_email']) . '</strong>'];
        $steps[] = ['ok', 'Loai key: ' . htmlspecialchars($sa['type'])];
    } else {
        $steps[] = ['fail', 'JSON khong hop le hoac thieu truong client_email / private_key'];
        $sa = null;
    }
} else {
    $sa = null;
}

// =====================================================
// BUOC 3: Thu lay Access Token tu Google OAuth2
// =====================================================
$token = null;
if ($sa) {
    try {
        // Dung reflection de goi private method (chi de test)
        $now   = time();
        $claim = [
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive.file',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];
        $header  = rtrim(strtr(base64_encode(json_encode(['alg'=>'RS256','typ'=>'JWT'])), '+/','-_'), '=');
        $payload = rtrim(strtr(base64_encode(json_encode($claim)), '+/','-_'), '=');
        $data    = $header . '.' . $payload;

        if (!openssl_sign($data, $sig, $sa['private_key'], 'SHA256')) {
            throw new Exception('openssl_sign that bai. OpenSSL co the chua duoc cai tren server nay.');
        }
        $jwt = $data . '.' . rtrim(strtr(base64_encode($sig), '+/','-_'), '=');

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query(['grant_type'=>'urn:ietf:params:oauth:grant-type:jwt-bearer','assertion'=>$jwt]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $res  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($curlErr) throw new Exception('cURL Error: ' . $curlErr);
        if ($code !== 200) throw new Exception('HTTP ' . $code . ': ' . $res);

        $tokenData = json_decode($res, true);
        if (!isset($tokenData['access_token'])) throw new Exception('Khong nhan duoc access_token: ' . $res);

        $token = $tokenData['access_token'];
        $steps[] = ['ok', 'Lay Access Token thanh cong! (het han sau 1 gio)'];

    } catch (Exception $e) {
        $steps[] = ['fail', 'Lay Access Token THAT BAI: ' . $e->getMessage()];
    }
}

// =====================================================
// BUOC 4: Kiem tra quyen truy cap Drive API
// =====================================================
if ($token) {
    $ch = curl_init('https://www.googleapis.com/drive/v3/about?fields=user');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token],
        CURLOPT_TIMEOUT        => 15,
    ]);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $about = json_decode($res, true);
    if ($code === 200 && isset($about['user'])) {
        $steps[] = ['ok', 'Ket noi Google Drive API thanh cong! User Drive: <strong>' . htmlspecialchars($about['user']['displayName'] ?? $sa['client_email']) . '</strong>'];
    } else {
        $steps[] = ['fail', 'Goi Drive API that bai (HTTP ' . $code . '): ' . htmlspecialchars($res)];
    }
}

// =====================================================
// BUOC 5: Kiem tra Folder ID (neu co)
// =====================================================
$folderId = $_GET['folder_id'] ?? '';
if ($token && !empty($folderId)) {
    $ch = curl_init('https://www.googleapis.com/drive/v3/files/' . urlencode($folderId) . '?fields=id,name,mimeType');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token],
        CURLOPT_TIMEOUT        => 15,
    ]);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $folderInfo = json_decode($res, true);
    if ($code === 200 && isset($folderInfo['id'])) {
        $steps[] = ['ok', 'Thu muc Drive tim thay: <strong>' . htmlspecialchars($folderInfo['name']) . '</strong> (ID: ' . $folderId . ')'];
    } else {
        $steps[] = ['fail', 'Khong tim thay thu muc ID: ' . $folderId . '. Kiem tra lai: (1) Folder ID dung chua? (2) Da chia se thu muc voi ' . htmlspecialchars($sa['client_email'] ?? '') . ' chua?<br>Response: ' . htmlspecialchars($res)];
    }
} elseif ($token) {
    $steps[] = ['info', 'Chua kiem tra Folder ID. Them ?folder_id=YOUR_FOLDER_ID vao URL de kiem tra.'];
}

// =====================================================
// BUOC 6: Thu upload file test len Drive
// =====================================================
if ($token && !empty($folderId) && isset($folderInfo) && $code === 200) {
    $testContent  = 'Test file - he thong LMS kiem tra ket noi Google Drive - ' . date('Y-m-d H:i:s');
    $boundary     = '------TestBoundary' . uniqid();
    $metadata     = json_encode(['name' => 'test_lms_connection.txt', 'parents' => [$folderId]]);
    $body  = "--{$boundary}\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadata}\r\n";
    $body .= "--{$boundary}\r\nContent-Type: text/plain\r\n\r\n{$testContent}\r\n";
    $body .= "--{$boundary}--";

    $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,webViewLink');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: multipart/related; boundary=' . $boundary,
            'Content-Length: ' . strlen($body),
        ],
        CURLOPT_TIMEOUT        => 30,
    ]);
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $uploaded = json_decode($res, true);
    if ($code === 200 && isset($uploaded['id'])) {
        $steps[] = ['ok', 'Upload test file THANH CONG! File: <a href="' . htmlspecialchars($uploaded['webViewLink']) . '" target="_blank">test_lms_connection.txt</a> — Google Drive dang hoat dong tot!'];
    } else {
        $steps[] = ['fail', 'Upload test file THAT BAI (HTTP ' . $code . '): ' . htmlspecialchars($res)];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kiem tra Google Drive</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:820px;">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-google me-2"></i>Kiểm tra Cấu hình Google Drive</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Lưu ý bảo mật:</strong> XÓA file <code>admin/test-drive.php</code> sau khi đã kiểm tra xong!
            </div>

            <?php foreach($steps as $step): ?>
            <?php [$type, $msg] = $step; ?>
            <div class="d-flex align-items-start gap-3 mb-3 p-3 rounded border <?php echo $type==='ok'?'border-success bg-success bg-opacity-10':($type==='fail'?'border-danger bg-danger bg-opacity-10':'border-info bg-info bg-opacity-10'); ?>">
                <i class="bi bi-<?php echo $type==='ok'?'check-circle-fill text-success':($type==='fail'?'x-circle-fill text-danger':'info-circle text-info'); ?> fs-4 flex-shrink-0 mt-1"></i>
                <div><?php echo $msg; ?></div>
            </div>
            <?php endforeach; ?>

            <!-- Kiem tra folder cu the -->
            <div class="mt-4 p-3 bg-light rounded border">
                <h6 class="fw-bold">Kiểm tra Folder ID cụ thể:</h6>
                <form method="GET">
                    <div class="input-group">
                        <input type="text" name="folder_id" class="form-control font-monospace" value="<?php echo htmlspecialchars($folderId); ?>" placeholder="Dán Folder ID vào đây...">
                        <button type="submit" class="btn btn-primary">Kiểm tra</button>
                    </div>
                    <small class="text-muted">Ví dụ: 1AbCdEfGhIjKlMnOpQrStUvWxYz1234567</small>
                </form>
            </div>

            <!-- Ket qua tong -->
            <?php $hasError = count(array_filter($steps, function($s){ return $s[0]==='fail'; })) > 0; ?>
            <div class="mt-4 alert <?php echo $hasError ? 'alert-danger' : 'alert-success'; ?>">
                <?php if($hasError): ?>
                    <i class="bi bi-x-circle-fill me-2"></i><strong>Có lỗi cần khắc phục</strong> — Xem chi tiết các bước đỏ ở trên.
                <?php else: ?>
                    <i class="bi bi-check-circle-fill me-2"></i><strong>Google Drive đã sẵn sàng!</strong> Cấu hình hoạt động tốt.
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
