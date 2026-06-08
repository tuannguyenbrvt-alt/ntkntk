<?php
// controllers/CommentController.php

class CommentController extends Controller {

    // Thêm bình luận mới
    public function store() {
        header('Content-Type: application/json');
        
        $post_id   = isset($_POST['post_id']) && $_POST['post_id'] !== '' ? (int)$_POST['post_id'] : null;
        $course_id = isset($_POST['course_id']) && $_POST['course_id'] !== '' ? (int)$_POST['course_id'] : null;
        $lesson_id = isset($_POST['lesson_id']) && $_POST['lesson_id'] !== '' ? (int)$_POST['lesson_id'] : null;
        $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int)$_POST['parent_id'] : null;
        $content   = trim($_POST['content'] ?? '');

        if (empty($content)) {
            echo json_encode(['ok' => false, 'error' => 'Nội dung bình luận không được để trống.']);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // 1. Kiểm tra cấu hình allow_comments của đối tượng mục tiêu
        if ($post_id) {
            $stmt = $db->prepare("SELECT allow_comments FROM posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $item = $stmt->fetch();
            if (!$item || !$item['allow_comments']) {
                echo json_encode(['ok' => false, 'error' => 'Chức năng bình luận đã bị tắt cho bài viết này.']);
                return;
            }
        } elseif ($course_id) {
            $stmt = $db->prepare("SELECT allow_comments FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $item = $stmt->fetch();
            if (!$item || !$item['allow_comments']) {
                echo json_encode(['ok' => false, 'error' => 'Chức năng bình luận đã bị tắt cho khóa học này.']);
                return;
            }
        } elseif ($lesson_id) {
            $stmt = $db->prepare("SELECT allow_comments FROM course_lessons WHERE id = ?");
            $stmt->execute([$lesson_id]);
            $item = $stmt->fetch();
            if (!$item || !$item['allow_comments']) {
                echo json_encode(['ok' => false, 'error' => 'Chức năng bình luận đã bị tắt cho bài học này.']);
                return;
            }
        } else {
            echo json_encode(['ok' => false, 'error' => 'Đối tượng bình luận không hợp lệ.']);
            return;
        }

        // 2. Phân biệt người dùng đăng nhập và khách vãng lai
        $user_id = $_SESSION['user_id'] ?? null;
        $guest_name = null;
        $guest_phone = null;
        $status = 'approved'; // Mặc định thành viên thì tự động duyệt

        if (!$user_id) {
            // Khách vãng lai
            if (!$post_id) {
                // Khách chỉ được bình luận trên bài viết
                echo json_encode(['ok' => false, 'error' => 'Vui lòng đăng nhập để bình luận.']);
                return;
            }

            $guest_name = trim($_POST['guest_name'] ?? '');
            $guest_phone = trim($_POST['guest_phone'] ?? '');

            if (empty($guest_name) || empty($guest_phone)) {
                echo json_encode(['ok' => false, 'error' => 'Vui lòng nhập đầy đủ Họ tên và Số điện thoại để gửi bình luận.']);
                return;
            }

            // Khách vãng lai gửi thì luôn cần duyệt
            $status = 'pending';
        }

        // 3. Lưu bình luận vào Database
        try {
            $stmt = $db->prepare("
                INSERT INTO comments (user_id, guest_name, guest_phone, course_id, post_id, lesson_id, parent_id, content, status, is_public_to_guest)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
            ");
            $stmt->execute([
                $user_id,
                $guest_name,
                $guest_phone,
                $course_id,
                $post_id,
                $lesson_id,
                $parent_id,
                $content,
                $status
            ]);
            
            $comment_id = $db->lastInsertId();

            if ($status === 'pending') {
                echo json_encode([
                    'ok' => true,
                    'status' => 'pending',
                    'message' => 'Bình luận của bạn đã được gửi và đang chờ quản trị viên phê duyệt.'
                ]);
            } else {
                // Lấy thông tin hiển thị bình luận mới thêm
                $stmtGet = $db->prepare("
                    SELECT c.*, u.full_name as author_name, u.avatar as author_avatar, u.role as author_role
                    FROM comments c
                    LEFT JOIN users u ON c.user_id = u.id
                    WHERE c.id = ?
                ");
                $stmtGet->execute([$comment_id]);
                $newComment = $stmtGet->fetch();
                
                echo json_encode([
                    'ok' => true,
                    'status' => 'approved',
                    'comment' => $newComment
                ]);
            }
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    // Chỉnh sửa bình luận của bản thân
    public function update() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['ok' => false, 'error' => 'Bạn chưa đăng nhập.']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if (!$id || empty($content)) {
            echo json_encode(['ok' => false, 'error' => 'Thông tin không hợp lệ.']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        
        // Kiểm tra quyền sở hữu
        $stmt = $db->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $comment = $stmt->fetch();

        if (!$comment) {
            echo json_encode(['ok' => false, 'error' => 'Bình luận không tồn tại.']);
            return;
        }

        if ($comment['user_id'] != $_SESSION['user_id']) {
            echo json_encode(['ok' => false, 'error' => 'Bạn không có quyền chỉnh sửa bình luận này.']);
            return;
        }

        try {
            $stmtUpdate = $db->prepare("UPDATE comments SET content = ? WHERE id = ?");
            $stmtUpdate->execute([$content, $id]);
            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Lỗi cập nhật CSDL.']);
        }
    }

    // Xóa bình luận
    public function delete() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['ok' => false, 'error' => 'Bạn chưa đăng nhập.']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if (!$id) {
            echo json_encode(['ok' => false, 'error' => 'Thông tin không hợp lệ.']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        
        // Kiểm tra bình luận
        $stmt = $db->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        $comment = $stmt->fetch();

        if (!$comment) {
            echo json_encode(['ok' => false, 'error' => 'Bình luận không tồn tại hoặc đã bị xóa.']);
            return;
        }

        // Quyền xóa: chính chủ hoặc admin
        $isAdmin = in_array($_SESSION['role'] ?? '', ['super_admin', 'admin']);
        $isOwner = ($comment['user_id'] == $_SESSION['user_id']);

        if (!$isOwner && !$isAdmin) {
            echo json_encode(['ok' => false, 'error' => 'Bạn không có quyền xóa bình luận này.']);
            return;
        }

        try {
            // Do khóa ngoại constraint ON DELETE CASCADE, xóa comment cha sẽ tự xóa các con
            $stmtDelete = $db->prepare("DELETE FROM comments WHERE id = ?");
            $stmtDelete->execute([$id]);
            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Lỗi xóa trong CSDL.']);
        }
    }
}
