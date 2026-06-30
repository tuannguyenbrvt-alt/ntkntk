<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Router.php';
require_once 'core/Controller.php';

// Auto-load controllers and core classes
spl_autoload_register(function ($class) {
    if (file_exists('controllers/' . $class . '.php')) {
        require_once 'controllers/' . $class . '.php';
    } elseif (file_exists('core/' . $class . '.php')) {
        require_once 'core/' . $class . '.php';
    } elseif (file_exists('models/' . $class . '.php')) {
        require_once 'models/' . $class . '.php';
    }
});

// Ghi nhận trạng thái hoạt động cuối cùng của Học viên/Admin/Khách (Last Active Tracker)
try {
    $__db = Database::getInstance()->getConnection();
    $__now = time();

    // Đối với tài khoản đã đăng nhập
    if (isset($_SESSION['user_id'])) {
        if (!isset($_SESSION['last_active_update']) || ($__now - $_SESSION['last_active_update']) > 120) {
            $__stmt = $__db->prepare("UPDATE users SET last_active_at = NOW() WHERE id = ?");
            $__stmt->execute([$_SESSION['user_id']]);
            $_SESSION['last_active_update'] = $__now;
        }
    }
    // Đối với khách vãng lai đang chat
    if (isset($_SESSION['guest_chat_thread_id'])) {
        if (!isset($_SESSION['guest_last_active_update']) || ($__now - $_SESSION['guest_last_active_update']) > 120) {
            $__stmt = $__db->prepare("UPDATE chat_threads SET guest_last_active_at = NOW() WHERE id = ?");
            $__stmt->execute([$_SESSION['guest_chat_thread_id']]);
            $_SESSION['guest_last_active_update'] = $__now;
        }
    }

    // Ghi nhận lượt truy cập trang web (Visitor Tracker)
    require_once 'helpers/TrackerHelper.php';
    TrackerHelper::trackOnline($__db);
    TrackerHelper::recordVisit($__db);
} catch (Exception $__e) {
    // Bỏ qua lỗi kết nối DB để không ảnh hưởng luồng chính
}

$router = new Router();

// Định nghĩa các route cơ bản
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@postLogin');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@postRegister');
$router->get('/logout', 'AuthController@logout');
$router->get('/auth/google', 'AuthController@googleRedirect');
$router->get('/auth/google/callback', 'AuthController@googleCallback');

// Admin Routes
$router->get('/admin/dashboard', 'DashboardController@index');
$router->get('/admin/menus', 'MenuController@index');
$router->post('/admin/menus/store', 'MenuController@store');
$router->post('/admin/menus/update', 'MenuController@update');
$router->post('/admin/menus/delete', 'MenuController@delete');
$router->post('/admin/menus/reorder', 'MenuController@reorder');

// Admin Posts Routes
$router->get('/admin/posts', 'AdminPostController@index');
$router->get('/admin/posts/create', 'AdminPostController@create');
$router->post('/admin/posts/store', 'AdminPostController@store');
$router->get('/admin/posts/edit', 'AdminPostController@edit');
$router->post('/admin/posts/update', 'AdminPostController@update');
$router->post('/admin/posts/delete', 'AdminPostController@delete');

// Admin LMS Routes
$router->get('/admin/courses', 'AdminCourseController@index');
$router->get('/admin/courses/create', 'AdminCourseController@create');
$router->post('/admin/courses/store', 'AdminCourseController@store');
$router->get('/admin/courses/edit', 'AdminCourseController@edit');
$router->post('/admin/courses/update', 'AdminCourseController@update');
$router->post('/admin/courses/delete', 'AdminCourseController@delete');
$router->get('/admin/courses/builder', 'AdminCourseController@builder');

// Admin LMS Content Builder
$router->get('/api/courses/get-structure', 'AdminCourseContentController@getCourseStructure');
$router->post('/admin/courses/content/importPart', 'AdminCourseContentController@importPart');
$router->post('/admin/courses/content/importChapter', 'AdminCourseContentController@importChapter');
$router->post('/admin/courses/content/importLesson', 'AdminCourseContentController@importLesson');

$router->post('/admin/courses/content/storePart', 'AdminCourseContentController@storePart');
$router->post('/admin/courses/content/updatePart', 'AdminCourseContentController@updatePart');
$router->post('/admin/courses/content/deletePart', 'AdminCourseContentController@deletePart');
$router->post('/admin/courses/content/storeChapter', 'AdminCourseContentController@storeChapter');
$router->post('/admin/courses/content/updateChapter', 'AdminCourseContentController@updateChapter');
$router->post('/admin/courses/content/deleteChapter', 'AdminCourseContentController@deleteChapter');
$router->post('/admin/courses/content/storeLesson', 'AdminCourseContentController@storeLesson');
$router->post('/admin/courses/content/updateLesson', 'AdminCourseContentController@updateLesson');
$router->post('/admin/courses/content/deleteLesson', 'AdminCourseContentController@deleteLesson');
$router->post('/admin/courses/content/storeItem', 'AdminCourseContentController@storeItem');
$router->post('/admin/courses/content/updateItem', 'AdminCourseContentController@updateItem');
$router->post('/admin/courses/content/deleteItem', 'AdminCourseContentController@deleteItem');
$router->post('/admin/courses/content/reorderItem', 'AdminCourseContentController@reorderItem');
$router->post('/admin/courses/content/reorderPart', 'AdminCourseContentController@reorderPart');
$router->post('/admin/courses/content/reorderChapter', 'AdminCourseContentController@reorderChapter');
$router->post('/admin/courses/content/reorderLesson', 'AdminCourseContentController@reorderLesson');
$router->post('/admin/courses/content/storeAttachment', 'AdminCourseContentController@storeAttachment');
$router->post('/admin/courses/content/deleteAttachment', 'AdminCourseContentController@deleteAttachment');

// Admin Enrollment Routes
$router->get('/admin/enrollments', 'EnrollmentController@adminIndex');
$router->post('/admin/enrollments/approve', 'EnrollmentController@adminApprove');
$router->post('/admin/enrollments/update', 'EnrollmentController@adminUpdate');
$router->post('/admin/enrollments/delete', 'EnrollmentController@adminDelete');

// Admin User & Permission Management
$router->get('/admin/users', 'AdminUserController@index');
$router->post('/admin/users/update-role', 'AdminUserController@updateRole');
$router->post('/admin/users/delete', 'AdminUserController@delete');

// Admin Student CRM
$router->get('/admin/students', 'AdminStudentController@index');
$router->get('/admin/students/create', 'AdminStudentController@create');
$router->post('/admin/students/store', 'AdminStudentController@store');
$router->post('/admin/students/enroll', 'AdminStudentController@enrollCourse');
$router->get('/admin/students/show', 'AdminStudentController@show');
$router->post('/admin/students/update', 'AdminStudentController@update');
$router->get('/admin/students/quiz-attempt', 'AdminStudentController@quizAttempt');

// Admin Media Library
$router->get('/admin/media', 'MediaController@index');
$router->post('/admin/media/upload', 'MediaController@upload');
$router->post('/admin/media/delete', 'MediaController@delete');

// Public Course Routes
$router->get('/courses', 'CourseController@index');
$router->get('/course', 'CourseController@show');

// Student Enrollment & Learning
$router->get('/enrollment/checkout', 'EnrollmentController@checkout');
$router->get('/enrollment/confirm', 'EnrollmentController@confirm');
$router->get('/enrollment/done', 'EnrollmentController@done');
$router->get('/learning', 'LearningController@index');
$router->get('/learning/preview', 'LearningController@preview');
$router->post('/learning/markCompleted', 'LearningController@markCompleted');

// Student Profile
$router->get('/profile', 'ProfileController@dashboard');
$router->get('/profile/settings', 'ProfileController@settings');
$router->post('/profile/update', 'ProfileController@update');
$router->post('/profile/updatePassword', 'ProfileController@updatePassword');

// Public Posts Routes
$router->get('/blog', 'PostController@index');
$router->get('/post', 'PostController@show');

// API Routes
$router->post('/api/posts/create', 'PostApiController@create');
$router->post('/api/media/upload', 'PostApiController@uploadMedia');
$router->get('/api/courses/lessons', 'QuizApiController@getLessons');
$router->post('/api/quizzes/create', 'QuizApiController@create');

// Static Pages
$router->get('/page', 'PageController@show');

// Search
$router->get('/search', 'SearchController@index');

// Certificate
$router->get('/certificate', 'CertificateController@show');

// Sitemap
$router->get('/sitemap.xml', 'SitemapController@index');

// Consult (form tu van footer - AJAX)
$router->post('/consult/store', 'ConsultController@store');

// Admin - Consult management
$router->get('/admin/consults', 'AdminConsultController@index');
$router->post('/admin/consults/update-status', 'AdminConsultController@updateStatus');
$router->post('/admin/consults/delete', 'AdminConsultController@delete');

// Admin - Quiz management
$router->get('/admin/quizzes', 'AdminQuizController@index');
$router->get('/admin/quizzes/create', 'AdminQuizController@create');
$router->post('/admin/quizzes/store', 'AdminQuizController@store');
$router->get('/admin/quizzes/edit', 'AdminQuizController@edit');
$router->post('/admin/quizzes/update', 'AdminQuizController@update');
$router->get('/admin/quizzes/questions', 'AdminQuizController@questions');
$router->post('/admin/quizzes/storeQuestion', 'AdminQuizController@storeQuestion');
$router->post('/admin/quizzes/updateQuestion', 'AdminQuizController@updateQuestion');
$router->post('/admin/quizzes/addFromBank', 'AdminQuizController@addFromBank');
$router->post('/admin/quizzes/removeQuestion', 'AdminQuizController@removeQuestion');
$router->post('/admin/quizzes/delete', 'AdminQuizController@delete');
$router->get('/admin/quizzes/results', 'AdminQuizController@results');

// Admin - Assignment management
$router->post('/admin/assignments/store', 'AdminAssignmentController@store');
$router->post('/admin/assignments/update', 'AdminAssignmentController@update');
$router->post('/admin/assignments/delete', 'AdminAssignmentController@delete');
$router->get('/admin/assignments/submissions', 'AdminAssignmentController@submissions');
$router->get('/admin/assignments/pending', 'AdminAssignmentController@pending');
$router->get('/admin/assignments/grade', 'AdminAssignmentController@grade');
$router->post('/admin/assignments/storeGrade', 'AdminAssignmentController@storeGrade');
$router->get('/admin/setup-drive-oauth', 'AdminAssignmentController@setupDrive');

// Student - Quiz
$router->get('/quiz/take', 'QuizController@take');
$router->post('/quiz/submit', 'QuizController@submit');
$router->get('/quiz/result', 'QuizController@result');

// Student - Assignment
$router->post('/assignment/submitEssay', 'AssignmentController@submitEssay');
$router->post('/assignment/submitFile', 'AssignmentController@submitFile');
$router->get('/assignment/result', 'AssignmentController@result');

// Progress & Lookup
$router->get('/progress', 'ProgressController@dashboard');
$router->get('/progress/lookup', 'ProgressController@lookup');
$router->post('/progress/lookupResult', 'ProgressController@lookupResult');

// Chat trực tuyến (Public & Học viên)
$router->get('/chat/active-threads', 'ChatController@getActiveThreads');
$router->post('/chat/init', 'ChatController@initGuestThread');
$router->get('/chat/messages', 'ChatController@getMessages');
$router->post('/chat/send', 'ChatController@sendMessage');
$router->post('/chat/recall', 'ChatController@recallMessage');
$router->get('/chat/unread-count', 'ChatController@getUnreadCount');

// Chat trực tuyến (Admin & Giáo viên)
$router->get('/admin/chat', 'AdminChatController@index');
$router->get('/admin/chat/performance', 'AdminChatController@performance');
$router->get('/admin/chat/messages', 'AdminChatController@getMessages');
$router->post('/admin/chat/send', 'AdminChatController@sendMessage');
$router->post('/admin/chat/recall', 'AdminChatController@recallMessage');
$router->get('/admin/chat/search-students', 'AdminChatController@searchStudents');
$router->post('/admin/chat/start-thread', 'AdminChatController@startThread');
$router->get('/admin/setup-chat-db', 'DashboardController@setupChatDb');

// Hệ thống Bình luận (Comments System)
$router->post('/comment/store', 'CommentController@store');
$router->post('/comment/update', 'CommentController@update');
$router->post('/comment/delete', 'CommentController@delete');

// Admin - Comments Management
$router->get('/admin/comments', 'AdminCommentController@index');
$router->post('/admin/comments/approve', 'AdminCommentController@approve');
$router->post('/admin/comments/toggle-public', 'AdminCommentController@togglePublic');
$router->post('/admin/comments/delete', 'AdminCommentController@delete');

// Dispatch Router
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
