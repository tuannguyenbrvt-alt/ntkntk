<!-- views/admin/users/session_detail.php -->
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="<?php echo APP_URL; ?>/admin/sessions" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Chi tiết phiên làm việc #<?php echo $session['id']; ?></h1>
                <p class="text-muted mb-0">Xem hồ sơ người dùng và các bài học đã mở trong phiên này.</p>
            </div>
            <div>
                <?php if ($session['status'] === 'active'): ?>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 fs-7">
                        <span class="d-inline-block rounded-circle bg-success me-1 animate-pulse" style="width: 8px; height: 8px; transform: translateY(-1px);"></span>
                        Đang hoạt động (Online)
                    </span>
                <?php elseif ($session['status'] === 'logged_out'): ?>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1.5 fs-7">
                        Đã đăng xuất
                    </span>
                <?php else: ?>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1.5 fs-7">
                        Đóng trình duyệt / Offline
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Thông tin phiên -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">Hồ sơ người dùng</h5>
                </div>
                <div class="card-body text-center pt-2">
                    <div class="mb-3">
                        <?php if (!empty($session['avatar'])): ?>
                            <img src="<?php echo strpos($session['avatar'], 'http') === 0 ? $session['avatar'] : APP_URL . '/' . $session['avatar']; ?>" alt="" width="72" height="72" class="rounded-circle object-fit-cover shadow-sm border">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($session['full_name']); ?>&background=random&size=128" alt="" width="72" height="72" class="rounded-circle shadow-sm">
                        <?php endif; ?>
                    </div>
                    <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($session['full_name']); ?></h5>
                    <p class="text-muted mb-2">@<?php echo htmlspecialchars($session['username']); ?></p>
                    
                    <?php
                    $roleBadge = 'bg-secondary';
                    $roleName = 'Học viên';
                    if ($session['role'] === 'super_admin') {
                        $roleBadge = 'bg-danger';
                        $roleName = 'Super Admin';
                    } elseif ($session['role'] === 'admin') {
                        $roleBadge = 'bg-warning text-dark';
                        $roleName = 'Admin';
                    } elseif ($session['role'] === 'teacher') {
                        $roleBadge = 'bg-success';
                        $roleName = 'Giáo viên';
                    }
                    ?>
                    <span class="badge <?php echo $roleBadge; ?> mb-3"><?php echo $roleName; ?></span>
                    <hr class="my-3">
                    
                    <div class="text-start">
                        <div class="mb-2">
                            <small class="text-muted d-block">Địa chỉ Email:</small>
                            <span class="fw-semibold text-gray-800"><?php echo htmlspecialchars($session['email'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Địa chỉ IP đăng nhập:</small>
                            <code class="fw-semibold text-primary"><?php echo htmlspecialchars($session['ip_address'] ?? 'N/A'); ?></code>
                        </div>
                        <div>
                            <small class="text-muted d-block">Thiết bị (User Agent):</small>
                            <small class="text-gray-700 font-monospace d-block bg-light p-2 rounded border" style="font-size: 0.75rem; word-break: break-all; max-height: 100px; overflow-y: auto;">
                                <?php echo htmlspecialchars($session['user_agent'] ?? 'Unknown'); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">Thông tin Thời gian</h5>
                </div>
                <div class="card-body pt-2">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 mb-2">
                            <div>
                                <i class="bi bi-box-arrow-in-right text-success me-2"></i>
                                <small class="text-muted">Thời điểm đăng nhập</small>
                            </div>
                            <span class="fw-semibold text-gray-800"><?php echo date('H:i:s d/m/Y', strtotime($session['login_at'])); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 mb-2">
                            <div>
                                <i class="bi bi-activity text-primary me-2"></i>
                                <small class="text-muted">Hoạt động cuối cùng</small>
                            </div>
                            <span class="fw-semibold text-gray-800"><?php echo date('H:i:s d/m/Y', strtotime($session['last_activity_at'])); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                            <div>
                                <i class="bi bi-box-arrow-left text-danger me-2"></i>
                                <small class="text-muted">Thời điểm đăng xuất</small>
                            </div>
                            <span class="fw-semibold text-gray-800">
                                <?php 
                                if ($session['logout_at']) {
                                    echo date('H:i:s d/m/Y', strtotime($session['logout_at']));
                                } else {
                                    echo '<span class="text-italic text-secondary">Đang Online / Chưa rõ</span>';
                                }
                                ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Lịch sử bài học -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Bài học đã mở xem trong phiên</h5>
                    <span class="badge bg-primary rounded-pill"><?php echo count($viewedLessons); ?> bài học</span>
                </div>
                <div class="card-body">
                    <?php if (empty($viewedLessons)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block mb-3 text-secondary"></i>
                            Trong phiên làm việc này, người dùng chưa mở xem bất kỳ bài học nào.
                        </div>
                    <?php else: ?>
                        <!-- Timeline -->
                        <div class="position-relative ps-4 border-start border-2 border-light ms-2 py-2">
                            <?php foreach ($viewedLessons as $index => $lesson): ?>
                                <div class="mb-4 position-relative">
                                    <!-- Timeline Dot -->
                                    <div class="position-absolute bg-primary rounded-circle border border-white" 
                                         style="width: 12px; height: 12px; left: -27px; top: 6px; box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);"></div>
                                    
                                    <div class="p-3 bg-light rounded shadow-sm border border-light-subtle">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-light text-primary border border-primary-subtle rounded-pill">
                                                <i class="bi bi-clock me-1"></i>
                                                <?php echo date('H:i:s d/m/Y', strtotime($lesson['viewed_at'])); ?>
                                            </span>
                                            <span class="text-muted small">Khóa học ID: #<?php echo $lesson['course_id']; ?></span>
                                        </div>
                                        
                                        <h6 class="fw-bold text-gray-800 mb-1">
                                            <i class="bi bi-journal-text me-1 text-primary"></i>
                                            Bài học: <?php echo htmlspecialchars($lesson['lesson_title']); ?>
                                        </h6>
                                        <div class="text-muted small mb-2">
                                            <span>Chương: <?php echo htmlspecialchars($lesson['chapter_title']); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                                            <span class="small text-gray-700">
                                                <i class="bi bi-bookmark-fill me-1 text-secondary"></i>
                                                <?php echo htmlspecialchars($lesson['course_title']); ?>
                                            </span>
                                            <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $lesson['course_id']; ?>&lesson_id=<?php echo $lesson['lesson_id']; ?>" 
                                               target="_blank" 
                                               class="btn btn-xs btn-link text-decoration-none p-0 text-primary small">
                                                Xem bài học <i class="bi bi-box-arrow-up-right small"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.animate-pulse {
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0% {
        transform: scale(0.95) translateY(-1px);
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
    }
    70% {
        transform: scale(1) translateY(-1px);
        box-shadow: 0 0 0 5px rgba(25, 135, 84, 0);
    }
    100% {
        transform: scale(0.95) translateY(-1px);
        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
    }
}
.btn-xs {
    padding: 0.15rem 0.4rem;
    font-size: 0.75rem;
}
</style>
