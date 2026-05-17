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
}
