<?php
class AdminCourseContentController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function storePart() {
        $course_id = $_POST['course_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO course_parts (course_id, title) VALUES (?, ?)");
        $stmt->execute([$course_id, $title]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function updatePart() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        if ($title) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE course_parts SET title = ? WHERE id = ?");
            $stmt->execute([$title, $id]);
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function deletePart() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM course_parts WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function storeChapter() {
        $part_id = $_POST['part_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO course_chapters (part_id, title) VALUES (?, ?)");
        $stmt->execute([$part_id, $title]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function updateChapter() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        if ($title) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE course_chapters SET title = ? WHERE id = ?");
            $stmt->execute([$title, $id]);
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function deleteChapter() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM course_chapters WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function storeLesson() {
        $chapter_id = $_POST['chapter_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $is_free_preview = isset($_POST['is_free_preview']) ? 1 : 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO course_lessons (chapter_id, title, is_free_preview) VALUES (?, ?, ?)");
        $stmt->execute([$chapter_id, $title, $is_free_preview]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function updateLesson() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $is_free_preview = isset($_POST['is_free_preview']) ? 1 : 0;
        if ($title) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE course_lessons SET title = ?, is_free_preview = ? WHERE id = ?");
            $stmt->execute([$title, $is_free_preview, $id]);
        }
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function deleteLesson() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM course_lessons WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function storeItem() {
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $type      = $_POST['type']      ?? 'text';
        $content   = '';

        if ($type === 'pdf') {
            if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] === UPLOAD_ERR_NO_FILE) {
                $_SESSION['error'] = 'Vui long chon file PDF.';
                $this->redirect('/admin/courses/builder?id=' . $course_id);
                return;
            }
            $file = $_FILES['pdf_file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Loi upload file. Ma loi: ' . $file['error'];
                $this->redirect('/admin/courses/builder?id=' . $course_id);
                return;
            }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $_SESSION['error'] = 'Chi chap nhan file PDF.';
                $this->redirect('/admin/courses/builder?id=' . $course_id);
                return;
            }
            $uploadDir = ROOT_PATH . '/uploads/course_pdfs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = uniqid() . '-' . time() . '.pdf';
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $content = 'uploads/course_pdfs/' . $filename;
            } else {
                $_SESSION['error'] = 'Loi luu file PDF len may chu.';
                $this->redirect('/admin/courses/builder?id=' . $course_id);
                return;
            }
        } else {
            $content = $_POST['content'] ?? '';
        }

        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO lesson_items (lesson_id, type, content) VALUES (?, ?, ?)");
        $stmt->execute([$lesson_id, $type, $content]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function deleteItem() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db        = Database::getInstance()->getConnection();
        // Delete physical PDF file if exists
        $s = $db->prepare("SELECT type, content FROM lesson_items WHERE id = ?");
        $s->execute([$id]);
        $item = $s->fetch();
        if ($item && $item['type'] === 'pdf' && file_exists(ROOT_PATH . '/' . $item['content'])) {
            @unlink(ROOT_PATH . '/' . $item['content']);
        }
        $stmt = $db->prepare("DELETE FROM lesson_items WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    // -- ATTACHMENTS --
    public function storeAttachment() {
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;

        if (!isset($_FILES['attachment_file']) || $_FILES['attachment_file']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['error'] = 'Vui long chon file dinh kem.';
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        $file = $_FILES['attachment_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Loi upload. Ma loi: ' . $file['error'];
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        $allowed = ['pdf','doc','docx','xls','xlsx','ppt','pptx','zip','rar','txt','mp3','mp4','png','jpg','jpeg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $_SESSION['error'] = 'Dinh dang file khong duoc phep.';
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        $uploadDir = ROOT_PATH . '/uploads/attachments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = uniqid() . '-' . time() . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $_SESSION['error'] = 'Loi luu file len may chu.';
            $this->redirect('/admin/courses/builder?id=' . $course_id);
            return;
        }
        // Tinh kich thuoc file
        $bytes = $file['size'];
        if ($bytes >= 1048576)      $size = round($bytes/1048576, 1) . ' MB';
        elseif ($bytes >= 1024)     $size = round($bytes/1024, 1) . ' KB';
        else                        $size = $bytes . ' B';

        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO lesson_attachments (lesson_id, name, file_path, file_size) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lesson_id, $file['name'], 'uploads/attachments/' . $filename, $size]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }

    public function deleteAttachment() {
        $id        = $_POST['id']        ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db        = Database::getInstance()->getConnection();
        $s         = $db->prepare("SELECT file_path FROM lesson_attachments WHERE id = ?");
        $s->execute([$id]);
        $row = $s->fetch();
        if ($row && file_exists(ROOT_PATH . '/' . $row['file_path'])) {
            @unlink(ROOT_PATH . '/' . $row['file_path']);
        }
        $stmt = $db->prepare("DELETE FROM lesson_attachments WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
}
