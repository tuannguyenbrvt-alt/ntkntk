<?php
class CourseController extends Controller {
    public function index() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT c.*, u.full_name as author_name FROM courses c LEFT JOIN users u ON c.author_id = u.id WHERE c.status = 'published' ORDER BY c.created_at DESC");
        $courses = $stmt->fetchAll();

        $this->render('courses/index', [
            'title' => 'Khóa học chất lượng cao',
            'courses' => $courses
        ], 'main');
    }

    public function show() {
        $slug = $_GET['slug'] ?? '';
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT c.*, u.full_name as author_name FROM courses c LEFT JOIN users u ON c.author_id = u.id WHERE c.slug = ? AND c.status = 'published'");
        $stmt->execute([$slug]);
        $course = $stmt->fetch();

        if (!$course) {
            http_response_code(404);
            die("Khóa học không tồn tại.");
        }

        $stmtParts = $db->prepare("SELECT * FROM course_parts WHERE course_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtParts->execute([$course['id']]);
        $parts = $stmtParts->fetchAll();

        $totalLessons = 0;
        foreach ($parts as &$part) {
            $stmtChapters = $db->prepare("SELECT * FROM course_chapters WHERE part_id = ? ORDER BY sort_order ASC, id ASC");
            $stmtChapters->execute([$part['id']]);
            $part['chapters'] = $stmtChapters->fetchAll();

            foreach ($part['chapters'] as &$chapter) {
                $stmtLessons = $db->prepare("SELECT * FROM course_lessons WHERE chapter_id = ? ORDER BY sort_order ASC, id ASC");
                $stmtLessons->execute([$chapter['id']]);
                $chapter['lessons'] = $stmtLessons->fetchAll();
                $totalLessons += count($chapter['lessons']);
            }
        }

        $isEnrolled = false;
        if (isset($_SESSION['user_id'])) {
            $stmtCheck = $db->prepare("SELECT status FROM enrollments WHERE student_id = ? AND course_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmtCheck->execute([$_SESSION['user_id'], $course['id']]);
            $enrollment = $stmtCheck->fetch();
            if ($enrollment && $enrollment['status'] == 'active') {
                $isEnrolled = true;
            } elseif ($enrollment && $enrollment['status'] == 'pending') {
                $isEnrolled = 'pending';
            }
        }

        $this->render('courses/show', [
            'title' => $course['title'],
            'course' => $course,
            'parts' => $parts,
            'totalLessons' => $totalLessons,
            'isEnrolled' => $isEnrolled
        ], 'main');
    }
}
