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
        $type = $_POST['type'] ?? 'text';
        $content = $_POST['content'] ?? '';
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO lesson_items (lesson_id, type, content) VALUES (?, ?, ?)");
        $stmt->execute([$lesson_id, $type, $content]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
    public function deleteItem() {
        $id = $_POST['id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM lesson_items WHERE id = ?");
        $stmt->execute([$id]);
        $this->redirect('/admin/courses/builder?id=' . $course_id);
    }
}
