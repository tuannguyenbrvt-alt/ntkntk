<?php
class UploadHelper {
    public static function uploadImage($file, $destinationFolder = 'uploads/posts/') {
        if (!file_exists(ROOT_PATH . '/' . $destinationFolder)) {
            mkdir(ROOT_PATH . '/' . $destinationFolder, 0755, true);
        }
        
        if (isset($file) && $file['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Chỉ cho phép upload file ảnh (JPG, PNG, GIF, WEBP).");
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '-' . time() . '.' . $ext;
            $destination = ROOT_PATH . '/' . $destinationFolder . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                return $destinationFolder . $filename;
            } else {
                throw new Exception("Lỗi khi lưu file upload.");
            }
        }
        return null;
    }

    /**
     * Upload a generic file (PDF, DOC, ZIP, etc.)
     * @param array  $file              $_FILES element
     * @param string $destinationFolder Relative path from ROOT_PATH
     * @param array  $allowedExtensions e.g. ['pdf','docx','zip']
     * @param int    $maxSizeMB         Maximum allowed file size in MB
     * @return array ['path'=>string, 'name'=>string, 'size'=>string] or null
     */
    public static function uploadFile($file, $destinationFolder = 'uploads/files/', $allowedExtensions = ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','txt','mp3','mp4'], $maxSizeMB = 50) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Lỗi upload file. Mã lỗi: " . ($file['error'] ?? 'N/A'));
        }

        $maxBytes = $maxSizeMB * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            throw new Exception("File quá lớn. Kích thước tối đa cho phép là {$maxSizeMB}MB.");
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            throw new Exception("Định dạng file không được phép. Chỉ chấp nhận: " . implode(', ', $allowedExtensions));
        }

        if (!file_exists(ROOT_PATH . '/' . $destinationFolder)) {
            mkdir(ROOT_PATH . '/' . $destinationFolder, 0755, true);
        }

        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        // Sanitize filename
        $originalName = preg_replace('/[^a-zA-Z0-9_\-\p{L}]/u', '_', $originalName);
        $filename = uniqid() . '-' . time() . '.' . $ext;
        $destination = ROOT_PATH . '/' . $destinationFolder . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'path' => $destinationFolder . $filename,
                'name' => $file['name'],
                'size' => self::formatBytes($file['size'])
            ];
        } else {
            throw new Exception("Lỗi khi lưu file lên máy chủ.");
        }
    }

    public static function formatBytes($bytes) {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
