<?php
$__unreadChat = 0;
if (isset($_SESSION['user_id']) && in_array($_SESSION['role'], ['super_admin', 'admin'])) {
    try {
        $db = Database::getInstance()->getConnection();
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        if ($role === 'super_admin') {
            $qUnread = "
                SELECT COUNT(*) 
                FROM chat_messages m
                JOIN chat_threads t ON m.thread_id = t.id
                WHERE m.is_read = 0 
                  AND (m.sender_id IS NULL OR m.sender_id IN (SELECT id FROM users WHERE role = 'student'))
            ";
            $__unreadChat = $db->query($qUnread)->fetchColumn();
        } else {
            $qUnread = "
                SELECT COUNT(*) 
                FROM chat_messages m
                JOIN chat_threads t ON m.thread_id = t.id
                LEFT JOIN courses c ON t.course_id = c.id
                WHERE m.is_read = 0 
                  AND (t.type = 'guest_admin' OR c.author_id = ?)
                  AND (m.sender_id IS NULL OR m.sender_id IN (SELECT id FROM users WHERE role = 'student'))
            ";
            $stmt = $db->prepare($qUnread);
            $stmt->execute([$user_id]);
            $__unreadChat = $stmt->fetchColumn();
        }
    } catch(Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - <?php echo isset($title) ? $title : APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar { 
            min-height: 100vh; 
            background: #212529; 
        }
        .sidebar a { 
            color: #adb5bd; 
            text-decoration: none; 
            padding: 12px 20px; 
            display: block; 
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active { 
            background: #343a40; 
            color: #fff;
            border-left: 3px solid #0d6efd;
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar text-white">
                <div class="p-4 border-bottom border-secondary text-center">
                    <h5 class="mb-0 fw-bold">Admin Panel</h5>
                    <small class="text-muted">NTKNTK</small>
                </div>
                <nav class="mt-3">
                    <a href="<?php echo APP_URL; ?>/admin/dashboard" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : ''; ?>"><i class="bi bi-speedometer2 me-2"></i> Tổng quan</a>
                    <a href="<?php echo APP_URL; ?>/admin/menus" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/menus') !== false ? 'active' : ''; ?>"><i class="bi bi-list-nested me-2"></i> Menu</a>
                    <a href="<?php echo APP_URL; ?>/admin/posts" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/posts') !== false ? 'active' : ''; ?>"><i class="bi bi-file-text me-2"></i> Bài viết</a>
                    <a href="<?php echo APP_URL; ?>/admin/courses" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/courses') !== false ? 'active' : ''; ?>"><i class="bi bi-journal-bookmark me-2"></i> Khóa học</a>
                    <a href="<?php echo APP_URL; ?>/admin/enrollments" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/enrollments') !== false ? 'active' : ''; ?> d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-cart-check me-2"></i> Duyệt đăng ký</span>
                        <?php
                        try {
                            $__pendingEnroll = Database::getInstance()->getConnection()->query("SELECT COUNT(*) FROM enrollments WHERE status = 'pending'")->fetchColumn();
                            if ($__pendingEnroll > 0) echo '<span class="badge bg-danger rounded-pill">' . $__pendingEnroll . '</span>';
                        } catch(Exception $e) {}
                        ?>
                    </a>
                    <a href="<?php echo APP_URL; ?>/admin/students" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/students') !== false ? 'active' : ''; ?>"><i class="bi bi-people me-2"></i> Học viên</a>
                    <a href="<?php echo APP_URL; ?>/admin/assignments/pending" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/assignments') !== false ? 'active' : ''; ?> d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-ui-checks me-2"></i> Chấm bài tập</span>
                        <?php
                        try {
                            $db = Database::getInstance()->getConnection();
                            $q = "SELECT COUNT(*) FROM assignment_submissions s 
                                  JOIN assignments a ON s.assignment_id = a.id 
                                  JOIN course_lessons cl ON a.lesson_id = cl.id 
                                  JOIN course_chapters cc ON cl.chapter_id = cc.id 
                                  JOIN course_parts cp ON cc.part_id = cp.id 
                                  JOIN courses c ON cp.course_id = c.id 
                                  WHERE s.status = 'pending'";
                            if ($_SESSION['role'] !== 'super_admin') {
                                $stmt = $db->prepare($q . " AND c.author_id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $__pendingAsgn = $stmt->fetchColumn();
                            } else {
                                $__pendingAsgn = $db->query($q)->fetchColumn();
                            }
                            if ($__pendingAsgn > 0) echo '<span class="badge bg-danger rounded-pill">' . $__pendingAsgn . '</span>';
                        } catch(Exception $e) {}
                        ?>
                    </a>
                    <a href="<?php echo APP_URL; ?>/admin/chat" class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/chat') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/chat/performance') === false) ? 'active' : ''; ?> d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-chat-dots me-2"></i> Trò chuyện</span>
                        <?php if ($__unreadChat > 0) echo '<span class="badge bg-danger rounded-pill">' . $__unreadChat . '</span>'; ?>
                    </a>
                    <a href="<?php echo APP_URL; ?>/admin/chat/performance" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/chat/performance') !== false ? 'active' : ''; ?>"><i class="bi bi-bar-chart-line me-2"></i> Hiệu suất phản hồi</a>
                    <a href="<?php echo APP_URL; ?>/admin/media" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/media') !== false ? 'active' : ''; ?>"><i class="bi bi-images me-2"></i> Thư viện Media</a>
                    <a href="<?php echo APP_URL; ?>/admin/users" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : ''; ?>"><i class="bi bi-shield-lock me-2"></i> Phân quyền & TK</a>
                    <a href="<?php echo APP_URL; ?>/admin/comments" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/comments') !== false ? 'active' : '';   ?> d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-chat-left-text me-2"></i> Bình luận</span>
                        <?php
                        try {
                            $__pendingComments = Database::getInstance()->getConnection()->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
                            if ($__pendingComments > 0) echo '<span class="badge bg-danger rounded-pill">' . $__pendingComments . '</span>';
                        } catch(Exception $e) {}
                        ?>
                    </a>
                    <a href="<?php echo APP_URL; ?>/admin/consults" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/admin/consults') !== false ? 'active' : ''; ?> d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-headset me-2"></i> Đăng ký Tư vấn</span>
                        <?php
                        try {
                            $__newConsults = Database::getInstance()->getConnection()->query("SELECT COUNT(*) FROM consultation_requests WHERE status='new'")->fetchColumn();
                            if ($__newConsults > 0) echo '<span class="badge bg-danger rounded-pill">' . $__newConsults . '</span>';
                        } catch(Exception $e) {}
                        ?>
                    </a>
                    <hr class="border-secondary my-3 mx-3">
                    <a href="<?php echo APP_URL; ?>/"><i class="bi bi-box-arrow-left me-2"></i> Về trang chủ</a>
                </nav>
                <!-- Admin Profile -->
                <div class="dropdown p-3 border-top border-secondary">
                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <?php if (!empty($_SESSION['avatar'])): ?>
                            <img src="<?php echo APP_URL . '/' . $_SESSION['avatar']; ?>" alt="" width="32" height="32" class="rounded-circle me-2 object-fit-cover">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=random" alt="" width="32" height="32" class="rounded-circle me-2">
                        <?php endif; ?>
                        <strong><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></strong>
                    </a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1 fs-5"><?php echo isset($title) ? $title : 'Dashboard'; ?></span>
                        <div class="ms-auto d-flex align-items-center">
                            <a href="<?php echo APP_URL; ?>/admin/chat" class="position-relative me-4 text-dark text-decoration-none" title="Tin nhắn mới">
                                <i class="bi bi-bell fs-5"></i>
                                <?php if ($__unreadChat > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle p-1.5 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">Tin nhắn mới</span>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <a href="<?php echo APP_URL; ?>/logout" class="btn btn-sm btn-outline-danger">Đăng xuất</a>
                        </div>
                    </div>
                </nav>
                <div class="p-4">
                    <!-- Alert Message Area (Flash messages) -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- View Content -->
                    <?php echo $content; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- TinyMCE CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '.tinymce-editor',
                    height: 350,
                    menubar: false,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                    toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image media | code help',
                    promotion: false,
                    branding: false,
                    setup: function (editor) {
                        editor.on('change', function () {
                            tinymce.triggerSave();
                        });
                    }
                });
                
                tinymce.init({
                    selector: '.tinymce-editor-simple',
                    height: 200,
                    menubar: false,
                    plugins: 'advlist autolink lists link image charmap preview anchor code media wordcount',
                    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | link image media | removeformat',
                    promotion: false,
                    branding: false,
                    setup: function (editor) {
                        editor.on('change', function () {
                            tinymce.triggerSave();
                        });
                    }
                });

                // Tự động triggerSave trước khi submit bất kỳ form nào
                document.addEventListener('submit', function() {
                    tinymce.triggerSave();
                });
            }
        });
    </script>
</body>
</html>
