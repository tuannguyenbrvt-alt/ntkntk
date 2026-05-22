<?php
// controllers/PostApiController.php

class PostApiController extends Controller {
    public function create() {
        // Trả về định dạng JSON
        header('Content-Type: application/json; charset=utf-8');

        // Hỗ trợ cả JSON input hoặc POST form thông thường
        $inputData = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true) ?? [];
        } else {
            $inputData = $_POST;
        }

        // 1. Xác thực API Key
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? ($inputData['api_key'] ?? '');
        if (empty($apiKey) || $apiKey !== API_SECRET_KEY) {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Xác thực không hợp lệ. API Key không chính xác.'
            ]);
            return;
        }

        // 2. Lấy dữ liệu bài viết
        $title = $inputData['title'] ?? '';
        $content = $inputData['content'] ?? '';
        $slug = $inputData['slug'] ?? '';
        $status = $inputData['status'] ?? 'published'; // Mặc định là published
        $type = $inputData['type'] ?? 'blog';

        if (empty($title) || empty($content)) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Thiếu dữ liệu bắt buộc (title, content).'
            ]);
            return;
        }

        // Chuẩn hóa status
        if (!in_array($status, ['draft', 'published'])) {
            $status = 'published';
        }

        $db = Database::getInstance()->getConnection();

        // 3. Đảm bảo tác giả N_M_T_AI tồn tại trong cơ sở dữ liệu
        $authorUsername = 'N_M_T_AI';
        $authorId = null;

        $stmtUser = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmtUser->execute([$authorUsername]);
        $user = $stmtUser->fetch();

        if ($user) {
            $authorId = $user['id'];
        } else {
            // Tự động tạo người dùng N_M_T_AI
            $randomPassword = bin2hex(random_bytes(8)); // Mật khẩu ngẫu nhiên
            $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
            $email = 'nmt_ai@ntkntk.com';
            $fullName = 'Trợ Lý Viết Bài AI';
            $role = 'admin'; // Cấp quyền admin để có thể quản lý bài viết

            $stmtCreateUser = $db->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmtCreateUser->execute([$authorUsername, $hashedPassword, $email, $fullName, $role])) {
                $authorId = $db->lastInsertId();
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Không thể khởi tạo tài khoản tác giả tự động.'
                ]);
                return;
            }
        }

        // 4. Xử lý Slug
        require_once ROOT_PATH . '/helpers/SlugHelper.php';
        if (empty($slug)) {
            $slug = SlugHelper::generate($title);
        } else {
            $slug = SlugHelper::generate($slug);
        }

        // Kiểm tra trùng lặp slug
        $stmtCheck = $db->prepare("SELECT id FROM posts WHERE slug = ?");
        $stmtCheck->execute([$slug]);
        if ($stmtCheck->fetch()) {
            $slug .= '-' . time();
        }

        // 5. Xử lý Thumbnail
        $thumbnailPath = null;
        $destinationFolder = 'uploads/posts/';
        $absoluteDestFolder = ROOT_PATH . '/' . $destinationFolder;

        if (!file_exists($absoluteDestFolder)) {
            mkdir($absoluteDestFolder, 0755, true);
        }

        // Hỗ trợ 3 hình thức tải ảnh lên:
        // A. Tải ảnh lên trực tiếp qua file multipart/form-data
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            require_once ROOT_PATH . '/helpers/UploadHelper.php';
            try {
                $thumbnailPath = UploadHelper::uploadImage($_FILES['thumbnail'], $destinationFolder);
            } catch (Exception $e) {
                // Tiếp tục không lỗi để không chặn bài viết
            }
        } 
        // B. Tải ảnh bằng chuỗi Base64
        elseif (!empty($inputData['thumbnail_base64'])) {
            try {
                $base64Data = $inputData['thumbnail_base64'];
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $typeMatch)) {
                    $imageType = strtolower($typeMatch[1]); // png, jpg, jpeg, webp, gif
                    $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                } else {
                    $imageType = 'png'; // mặc định
                }

                $decodedData = base64_decode($base64Data);
                if ($decodedData !== false) {
                    $filename = uniqid() . '-' . time() . '.' . $imageType;
                    $fullPath = $absoluteDestFolder . $filename;
                    if (file_put_contents($fullPath, $decodedData)) {
                        $thumbnailPath = $destinationFolder . $filename;
                    }
                }
            } catch (Exception $e) {
                // Tiếp tục không lỗi
            }
        } 
        // C. Tải ảnh bằng URL ngoài
        elseif (!empty($inputData['thumbnail_url'])) {
            try {
                $url = $inputData['thumbnail_url'];
                $ctx = stream_context_create([
                    'http' => [
                        'timeout' => 15,
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ]
                ]);
                $imageData = file_get_contents($url, false, $ctx);
                if ($imageData !== false) {
                    // Đoán định dạng file từ URL hoặc mặc định png
                    $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
                    if (empty($ext) || !in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $ext = 'png';
                    }
                    $filename = uniqid() . '-' . time() . '.' . strtolower($ext);
                    $fullPath = $absoluteDestFolder . $filename;
                    if (file_put_contents($fullPath, $imageData)) {
                        $thumbnailPath = $destinationFolder . $filename;
                    }
                }
            } catch (Exception $e) {
                // Tiếp tục không lỗi
            }
        }

        // 6. Ghi vào database
        $stmtInsert = $db->prepare("INSERT INTO posts (title, slug, content, thumbnail, type, status, author_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmtInsert->execute([$title, $slug, $content, $thumbnailPath, $type, $status, $authorId])) {
            $postId = $db->lastInsertId();
            $postUrl = APP_URL . '/post?slug=' . $slug;

            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Bài viết đã được đăng thành công!',
                'post' => [
                    'id' => $postId,
                    'title' => $title,
                    'slug' => $slug,
                    'url' => $postUrl,
                    'thumbnail' => $thumbnailPath ? APP_URL . '/' . $thumbnailPath : null,
                    'status' => $status
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Lỗi lưu bài viết vào database.'
            ]);
        }
    }
}
