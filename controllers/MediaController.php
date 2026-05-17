<?php
class MediaController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $uploadDir = ROOT_PATH . '/uploads/media/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $files = [];
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'pdf'];
        foreach (scandir($uploadDir) as $filename) {
            if ($filename === '.' || $filename === '..') continue;
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts)) continue;
            $files[] = [
                'name' => $filename,
                'url' => APP_URL . '/uploads/media/' . $filename,
                'size' => filesize($uploadDir . $filename),
                'ext' => $ext,
                'time' => filemtime($uploadDir . $filename),
            ];
        }
        // Sắp xếp mới nhất lên đầu
        usort($files, function($a, $b) {
            return $b['time'] - $a['time'];
        });

        $this->render('admin/media/index', [
            'title' => 'Thư viện Media',
            'files' => $files,
        ], 'admin');
    }

    public function upload() {
        header('Content-Type: application/json');

        if (!isset($_FILES['file'])) {
            echo json_encode(['error' => 'Không có file nào được gửi lên.']);
            exit;
        }

        $file = $_FILES['file'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['error' => 'Chỉ hỗ trợ ảnh JPG, PNG, GIF, WEBP.']);
            exit;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['error' => 'File quá lớn (tối đa 5MB).']);
            exit;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => 'Lỗi upload. Mã lỗi: ' . $file['error']]);
            exit;
        }

        $uploadDir = ROOT_PATH . '/uploads/media/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('img_') . '_' . time() . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // TinyMCE yêu cầu response theo format: {"location": "URL_ảnh"}
            echo json_encode(['location' => APP_URL . '/uploads/media/' . $filename]);
        } else {
            echo json_encode(['error' => 'Không thể lưu file lên server.']);
        }
        exit;
    }

    public function delete() {
        $filename = basename($_POST['filename'] ?? '');
        if (empty($filename)) {
            $_SESSION['error'] = 'Tên file không hợp lệ.';
            $this->redirect('/admin/media');
            return;
        }
        $filepath = ROOT_PATH . '/uploads/media/' . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
            $_SESSION['success'] = 'Đã xóa file: ' . $filename;
        } else {
            $_SESSION['error'] = 'Không tìm thấy file.';
        }
        $this->redirect('/admin/media');
    }
}
