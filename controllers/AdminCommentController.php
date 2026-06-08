<?php
// controllers/AdminCommentController.php

class AdminCommentController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    // Hiển thị danh sách bình luận
    public function index() {
        $search = trim($_GET['q'] ?? '');
        $db = Database::getInstance()->getConnection();

        $queryStr = "
            SELECT c.*, 
                   u.full_name as author_name, u.role as author_role,
                   p.title as post_title, p.slug as post_slug,
                   cr.title as course_title, cr.slug as course_slug,
                   l.title as lesson_title, l.id as lesson_id,
                   (SELECT title FROM courses WHERE id = (SELECT course_id FROM course_parts WHERE id = (SELECT part_id FROM course_chapters WHERE id = l.chapter_id))) as lesson_course_title,
                   (SELECT slug FROM courses WHERE id = (SELECT course_id FROM course_parts WHERE id = (SELECT part_id FROM course_chapters WHERE id = l.chapter_id))) as lesson_course_slug
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN posts p ON c.post_id = p.id
            LEFT JOIN courses cr ON c.course_id = cr.id
            LEFT JOIN course_lessons l ON c.lesson_id = l.id
        ";
        
        if ($search !== '') {
            $queryStr .= " WHERE c.content LIKE ? OR u.full_name LIKE ? OR c.guest_name LIKE ? OR c.guest_phone LIKE ? OR p.title LIKE ? OR cr.title LIKE ? OR l.title LIKE ? ";
            $queryStr .= " ORDER BY FIELD(c.status, 'pending', 'approved'), c.created_at DESC LIMIT 150";
            $stmt = $db->prepare($queryStr);
            $stmt->execute([
                '%' . $search . '%', 
                '%' . $search . '%', 
                '%' . $search . '%', 
                '%' . $search . '%', 
                '%' . $search . '%', 
                '%' . $search . '%', 
                '%' . $search . '%'
            ]);
        } else {
            $queryStr .= " ORDER BY FIELD(c.status, 'pending', 'approved'), c.created_at DESC LIMIT 150";
            $stmt = $db->query($queryStr);
        }
        
        $comments = $stmt->fetchAll();

        $this->render('admin/comments/index', [
            'title' => 'Quản lý Bình luận',
            'comments' => $comments,
            'search' => $search
        ], 'admin');
    }

    // Phê duyệt bình luận của khách vãng lai
    public function approve() {
        header('Content-Type: application/json');
        
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['ok' => false, 'error' => 'Thông tin không hợp lệ.']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Lỗi cập nhật CSDL.']);
        }
    }

    // Bật/tắt chế độ hiển thị với khách vãng lai
    public function togglePublic() {
        header('Content-Type: application/json');
        
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['ok' => false, 'error' => 'Thông tin không hợp lệ.']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        
        try {
            // Lấy trạng thái hiện tại
            $stmtGet = $db->prepare("SELECT is_public_to_guest FROM comments WHERE id = ?");
            $stmtGet->execute([$id]);
            $current = $stmtGet->fetchColumn();
            
            $newValue = ($current == 1) ? 0 : 1;
            
            $stmtUpdate = $db->prepare("UPDATE comments SET is_public_to_guest = ? WHERE id = ?");
            $stmtUpdate->execute([$newValue, $id]);
            
            echo json_encode([
                'ok' => true, 
                'is_public_to_guest' => $newValue
            ]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Lỗi cập nhật CSDL.']);
        }
    }

    // Xóa bình luận
    public function delete() {
        header('Content-Type: application/json');
        
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) {
            echo json_encode(['ok' => false, 'error' => 'Thông tin không hợp lệ.']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        
        try {
            // ON DELETE CASCADE sẽ tự động xóa các bình luận con (nếu có)
            $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'Lỗi xóa trong CSDL.']);
        }
    }
}
