<?php
class LearningController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) && $_GET['action'] != 'preview') {
            $_SESSION['error'] = 'Vui lòng đăng nhập để vào phòng học.';
            $this->redirect('/login');
        }
    }

    public function index() {
        $course_id = $_GET['course_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        
        $stmtCheck = $db->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ? AND status = 'active'");
        $stmtCheck->execute([$_SESSION['user_id'], $course_id]);
        if (!$stmtCheck->fetch()) {
            $_SESSION['error'] = 'Bạn chưa đăng ký khóa học này hoặc chưa được duyệt.';
            $this->redirect('/course?id=' . $course_id);
        }

        $stmtCourse = $db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmtCourse->execute([$course_id]);
        $course = $stmtCourse->fetch();

        $stmtParts = $db->prepare("SELECT * FROM course_parts WHERE course_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtParts->execute([$course_id]);
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

        $current_lesson_id = $_GET['lesson_id'] ?? 0;
        $current_lesson = null;
        $current_items = [];
        $current_attachments = [];
        $is_completed = false;

        if ($current_lesson_id > 0) {
            $stmtLesson = $db->prepare("SELECT * FROM course_lessons WHERE id = ?");
            $stmtLesson->execute([$current_lesson_id]);
            $current_lesson = $stmtLesson->fetch();

            $stmtItems = $db->prepare("SELECT * FROM lesson_items WHERE lesson_id = ? ORDER BY sort_order ASC, id ASC");
            $stmtItems->execute([$current_lesson_id]);
            $current_items = $stmtItems->fetchAll();

            $stmtAtt = $db->prepare("SELECT * FROM lesson_attachments WHERE lesson_id = ? ORDER BY id ASC");
            $stmtAtt->execute([$current_lesson_id]);
            $current_attachments = $stmtAtt->fetchAll();
        } else {
            if (!empty($parts[0]['chapters'][0]['lessons'][0])) {
                $current_lesson = $parts[0]['chapters'][0]['lessons'][0];
                $stmtItems = $db->prepare("SELECT * FROM lesson_items WHERE lesson_id = ? ORDER BY sort_order ASC, id ASC");
                $stmtItems->execute([$current_lesson['id']]);
                $current_items = $stmtItems->fetchAll();

                $stmtAtt = $db->prepare("SELECT * FROM lesson_attachments WHERE lesson_id = ? ORDER BY id ASC");
                $stmtAtt->execute([$current_lesson['id']]);
                $current_attachments = $stmtAtt->fetchAll();
            }
        }

        if ($current_lesson) {
            $stmtCheckProg = $db->prepare("SELECT id FROM course_progress WHERE student_id = ? AND lesson_id = ? AND is_completed = 1");
            $stmtCheckProg->execute([$_SESSION['user_id'], $current_lesson['id']]);
            if ($stmtCheckProg->fetch()) $is_completed = true;

            // Ghi nhận bài học đã mở xem trong phiên
            require_once ROOT_PATH . '/helpers/TrackerHelper.php';
            TrackerHelper::recordLessonView($db, $current_lesson['id']);
        }

        $this->render('learning/index', [
            'title'               => 'Phòng học: ' . $course['title'],
            'course'              => $course,
            'parts'               => $parts,
            'current_lesson'      => $current_lesson,
            'current_items'       => $current_items,
            'current_attachments' => $current_attachments,
            'is_completed'        => $is_completed
        ], 'main');
    }
    
    public function markCompleted() {
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $course_id = $_POST['course_id'] ?? 0;
        if ($lesson_id && isset($_SESSION['user_id'])) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT IGNORE INTO course_progress (student_id, lesson_id, is_completed, completed_at) VALUES (?, ?, 1, NOW())");
            $stmt->execute([$_SESSION['user_id'], $lesson_id]);
            $_SESSION['success'] = 'Tuyệt vời! Bạn đã hoàn thành bài học này.';
        }
        $this->redirect('/learning?course_id=' . $course_id . '&lesson_id=' . $lesson_id);
    }
    
    public function preview() {
        $lesson_id = $_GET['lesson_id'] ?? 0;
        $db = Database::getInstance()->getConnection();
        
        $stmtLesson = $db->prepare("SELECT * FROM course_lessons WHERE id = ? AND is_free_preview = 1");
        $stmtLesson->execute([$lesson_id]);
        $current_lesson = $stmtLesson->fetch();

        if (!$current_lesson) {
            $_SESSION['error'] = 'Bài học không tồn tại hoặc không cho phép học thử.';
            $this->redirect('/');
        }
        
        $stmtItems = $db->prepare("SELECT * FROM lesson_items WHERE lesson_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtItems->execute([$lesson_id]);
        $current_items = $stmtItems->fetchAll();
        
        $this->render('learning/preview', [
            'title' => 'Học thử: ' . $current_lesson['title'],
            'current_lesson' => $current_lesson,
            'current_items' => $current_items
        ], 'main');
    }
}
