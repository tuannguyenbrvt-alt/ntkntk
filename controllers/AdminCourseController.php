<?php
class AdminCourseController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['super_admin', 'admin'])) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT c.*, u.full_name as author_name FROM courses c LEFT JOIN users u ON c.author_id = u.id ORDER BY c.created_at DESC");
        $courses = $stmt->fetchAll();

        $this->render('admin/courses/index', [
            'title' => 'Quản lý Khóa học',
            'courses' => $courses
        ], 'admin');
    }

    public function create() {
        $this->render('admin/courses/form', [
            'title' => 'Thêm khóa học mới'
        ], 'admin');
    }

    public function store() {
        if (empty($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['error'] = 'Lỗi: Dung lượng file tải lên vượt quá giới hạn của máy chủ. Vui lòng chọn ảnh nhẹ hơn.';
            $this->redirect('/admin/courses/create');
            return;
        }
        $title = $_POST['title'] ?? '';
        require_once ROOT_PATH . '/helpers/SlugHelper.php';
        $slug = !empty($_POST['slug']) ? $_POST['slug'] : SlugHelper::generate($title);
        $description = $_POST['description'] ?? '';
        $price = !empty($_POST['price']) ? str_replace(',', '', $_POST['price']) : 0;
        $original_price = !empty($_POST['original_price']) ? str_replace(',', '', $_POST['original_price']) : null;
        $status = $_POST['status'] ?? 'draft';
        $author_id = $_SESSION['user_id'];
        $thumbnail = null;

        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Lỗi tải ảnh. Mã lỗi PHP: ' . $_FILES['thumbnail']['error'] . '. Vui lòng kiểm tra lại dung lượng ảnh (Nên < 2MB).';
                $this->redirect('/admin/courses/create');
                return;
            }
            require_once ROOT_PATH . '/helpers/UploadHelper.php';
            try {
                $thumbnail = UploadHelper::uploadImage($_FILES['thumbnail'], 'uploads/courses/');
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/admin/courses/create');
                return;
            }
        }

        $db = Database::getInstance()->getConnection();
        
        $stmtCheck = $db->prepare("SELECT id FROM courses WHERE slug = ?");
        $stmtCheck->execute([$slug]);
        if ($stmtCheck->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare("INSERT INTO courses (title, slug, description, thumbnail, price, original_price, status, author_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $slug, $description, $thumbnail, $price, $original_price, $status, $author_id])) {
            $courseId = $db->lastInsertId();
            $_SESSION['success'] = 'Thêm khóa học thành công! Vui lòng xây dựng đề cương.';
            // Chuyển tới giao diện Builder
            $this->redirect('/admin/courses/builder?id=' . $courseId);
        } else {
            $_SESSION['error'] = 'Lỗi lưu database.';
            $this->redirect('/admin/courses/create');
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch();

        if (!$course) $this->redirect('/admin/courses');

        $this->render('admin/courses/form', ['title' => 'Sửa khóa học', 'course' => $course], 'admin');
    }

    public function update() {
        if (empty($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['error'] = 'Lỗi: Dung lượng file tải lên vượt quá giới hạn của máy chủ. Vui lòng chọn ảnh nhẹ hơn.';
            $this->redirect('/admin/courses');
            return;
        }
        $id = $_POST['id'] ?? 0;
        $title = $_POST['title'] ?? '';
        require_once ROOT_PATH . '/helpers/SlugHelper.php';
        $slug = !empty($_POST['slug']) ? $_POST['slug'] : SlugHelper::generate($title);
        $description = $_POST['description'] ?? '';
        $price = !empty($_POST['price']) ? str_replace(',', '', $_POST['price']) : 0;
        $original_price = !empty($_POST['original_price']) ? str_replace(',', '', $_POST['original_price']) : null;
        $status = $_POST['status'] ?? 'draft';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT thumbnail FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch();
        $thumbnail = $course['thumbnail'];

        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Lỗi tải ảnh. Mã lỗi PHP: ' . $_FILES['thumbnail']['error'] . '. Vui lòng kiểm tra lại dung lượng ảnh (Nên < 2MB).';
                $this->redirect('/admin/courses/edit?id=' . $id);
                return;
            }
            require_once ROOT_PATH . '/helpers/UploadHelper.php';
            try {
                $thumbnail = UploadHelper::uploadImage($_FILES['thumbnail'], 'uploads/courses/');
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/admin/courses/edit?id=' . $id);
                return;
            }
        }

        $stmtCheck = $db->prepare("SELECT id FROM courses WHERE slug = ? AND id != ?");
        $stmtCheck->execute([$slug, $id]);
        if ($stmtCheck->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $db->prepare("UPDATE courses SET title = ?, slug = ?, description = ?, thumbnail = ?, price = ?, original_price = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$title, $slug, $description, $thumbnail, $price, $original_price, $status, $id])) {
            $_SESSION['success'] = 'Cập nhật thành công!';
            $this->redirect('/admin/courses');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra.';
            $this->redirect('/admin/courses/edit?id=' . $id);
        }
    }

    public function delete() {
        $id = $_POST['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Đã xóa khóa học!';
        $this->redirect('/admin/courses');
    }

    public function builder() {
        $id = $_GET['id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $course = $stmt->fetch();
        
        if (!$course) $this->redirect('/admin/courses');

        $stmtParts = $db->prepare("SELECT * FROM course_parts WHERE course_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtParts->execute([$id]);
        $parts = $stmtParts->fetchAll();

        foreach ($parts as &$part) {
            $stmtChapters = $db->prepare("SELECT * FROM course_chapters WHERE part_id = ? ORDER BY sort_order ASC, id ASC");
            $stmtChapters->execute([$part['id']]);
            $part['chapters'] = $stmtChapters->fetchAll();

            foreach ($part['chapters'] as &$chapter) {
                $stmtLessons = $db->prepare("SELECT * FROM course_lessons WHERE chapter_id = ? ORDER BY sort_order ASC, id ASC");
                $stmtLessons->execute([$chapter['id']]);
                $chapter['lessons'] = $stmtLessons->fetchAll();

                foreach ($chapter['lessons'] as &$lesson) {
                    $stmtItems = $db->prepare("SELECT * FROM lesson_items WHERE lesson_id = ? ORDER BY sort_order ASC, id ASC");
                    $stmtItems->execute([$lesson['id']]);
                    $lesson['items'] = $stmtItems->fetchAll();
                }
            }
        }

        $this->render('admin/courses/builder', [
            'title' => 'Cấu trúc Khóa học: ' . htmlspecialchars($course['title']),
            'course' => $course,
            'parts' => $parts
        ], 'admin');
    }
}
