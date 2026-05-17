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

$router = new Router();

// Định nghĩa các route cơ bản
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@postLogin');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@postRegister');
$router->get('/logout', 'AuthController@logout');

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
$router->post('/admin/courses/content/storePart', 'AdminCourseContentController@storePart');
$router->post('/admin/courses/content/deletePart', 'AdminCourseContentController@deletePart');
$router->post('/admin/courses/content/storeChapter', 'AdminCourseContentController@storeChapter');
$router->post('/admin/courses/content/deleteChapter', 'AdminCourseContentController@deleteChapter');
$router->post('/admin/courses/content/storeLesson', 'AdminCourseContentController@storeLesson');
$router->post('/admin/courses/content/deleteLesson', 'AdminCourseContentController@deleteLesson');
$router->post('/admin/courses/content/storeItem', 'AdminCourseContentController@storeItem');
$router->post('/admin/courses/content/deleteItem', 'AdminCourseContentController@deleteItem');

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
$router->get('/admin/students/show', 'AdminStudentController@show');
$router->post('/admin/students/update', 'AdminStudentController@update');

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

// Dispatch Router
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
