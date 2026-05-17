<?php
/**
 * PHP Auto-Deployment Webhook for DirectAdmin / PA Vietnam Hosting
 * Designed for public GitHub repository: tuannguyenbrvt-alt/ntkntk
 */

// 1. Khai báo token bảo mật (Thay đổi nếu muốn bảo mật hơn)
define('SECURE_TOKEN', 'ntkntk_secure_deploy_2026');

// Kiểm tra Token bảo mật
if (!isset($_GET['token']) || $_GET['token'] !== SECURE_TOKEN) {
    header('HTTP/1.1 403 Forbidden');
    die('Lỗi: Không có quyền truy cập (Sai Token).');
}

// Bật hiển thị trạng thái và bỏ giới hạn thời gian chạy
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300);

echo "<h2>🔄 Bắt đầu tiến trình tự động cập nhật mã nguồn...</h2>";

// 2. Định nghĩa các đường dẫn
$repoZipUrl = 'https://github.com/tuannguyenbrvt-alt/ntkntk/archive/refs/heads/main.zip';
$tempZipFile = __DIR__ . '/temp_deploy.zip';
$tempExtractDir = __DIR__ . '/temp_extract';

// 3. Tải file ZIP từ GitHub về hosting
echo "1. Đang tải mã nguồn mới từ GitHub...<br>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $repoZipUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
$zipContent = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$zipContent) {
    die("❌ Lỗi: Không thể tải file ZIP từ GitHub (HTTP Code: $httpCode).");
}

file_put_contents($tempZipFile, $zipContent);
echo "✔️ Đã tải file ZIP về hosting thành công.<br>";

// 4. Giải nén file
echo "2. Đang tiến hành giải nén mã nguồn...<br>";
if (!class_exists('ZipArchive')) {
    unlink($tempZipFile);
    die("❌ Lỗi: Hosting của bạn chưa bật thư viện ZipArchive (Vui lòng liên hệ PA Việt Nam để bật).");
}

$zip = new ZipArchive;
if ($zip->open($tempZipFile) === TRUE) {
    if (!is_dir($tempExtractDir)) {
        mkdir($tempExtractDir, 0755, true);
    }
    $zip->extractTo($tempExtractDir);
    $zip->close();
    echo "✔️ Giải nén thành công.<br>";
} else {
    unlink($tempZipFile);
    die("❌ Lỗi: Không thể mở file ZIP vừa tải về.");
}

// 5. Di chuyển các file từ thư mục tạm ra thư mục gốc
echo "3. Đang đồng bộ các file thay đổi vào dự án...<br>";

// Thư mục mặc định GitHub tạo ra trong file nén ZIP thường là: ntkntk-main/
$extractedFolder = $tempExtractDir . '/ntkntk-main';

if (!is_dir($extractedFolder)) {
    // Tìm thư mục con nếu tên không phải là ntkntk-main
    $subDirs = glob($tempExtractDir . '/*', GLOB_ONLYDIR);
    if (!empty($subDirs)) {
        $extractedFolder = $subDirs[0];
    } else {
        cleanup($tempZipFile, $tempExtractDir);
        die("❌ Lỗi: Không tìm thấy thư mục giải nén hợp lệ.");
    }
}

// Hàm di chuyển file đè lên file cũ
function moveFilesRecursive($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                moveFilesRecursive($src . '/' . $file, $dst . '/' . $file);
            } else {
                // Không ghi đè các file cấu hình bảo mật hoặc file webhook này
                if ($file === 'deploy_webhook.php' || $file === 'config.php' && file_exists($dst . '/' . $file)) {
                    continue;
                }
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

moveFilesRecursive($extractedFolder, __DIR__);
echo "✔️ Đã cập nhật toàn bộ file mã nguồn mới thành công!<br>";

// 6. Dọn dẹp các file tạm
echo "4. Đang dọn dẹp bộ nhớ tạm...<br>";
cleanup($tempZipFile, $tempExtractDir);
echo "🎉 <b>Cập nhật website thành công rực rỡ!</b>";

function cleanup($zip, $dir) {
    if (file_exists($zip)) {
        unlink($zip);
    }
    if (is_dir($dir)) {
        deleteDirectory($dir);
    }
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}
