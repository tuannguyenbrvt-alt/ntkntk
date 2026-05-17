<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo APP_URL . '/' . $user['avatar']; ?>" class="rounded-circle mx-auto mb-3 object-fit-cover shadow-sm border border-3 border-light" width="100" height="100">
                <?php else: ?>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['full_name']); ?>&background=random&size=128" class="rounded-circle mx-auto mb-3" width="100">
                <?php endif; ?>
                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                <p class="text-muted small mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                <hr>
                <div class="list-group list-group-flush text-start">
                    <a href="<?php echo APP_URL; ?>/profile" class="list-group-item list-group-item-action active fw-semibold border-0 rounded"><i class="bi bi-book me-2"></i> Khóa học của tôi</a>
                    <a href="<?php echo APP_URL; ?>/profile/settings" class="list-group-item list-group-item-action fw-semibold border-0 rounded"><i class="bi bi-person-gear me-2"></i> Cài đặt tài khoản</a>
                    <a href="<?php echo APP_URL; ?>/logout" class="list-group-item list-group-item-action text-danger fw-semibold border-0 rounded"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <h3 class="fw-bold mb-4">Khóa học của tôi</h3>
            <div class="row g-4">
                <?php if (empty($enrolledCourses)): ?>
                    <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                        <i class="bi bi-journal-x fs-1 text-muted opacity-50 d-block mb-3"></i>
                        <h5 class="text-muted">Bạn chưa đăng ký khóa học nào.</h5>
                        <a href="<?php echo APP_URL; ?>/courses" class="btn btn-primary rounded-pill px-4 mt-3">Xem các khóa học</a>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($enrolledCourses as $ec): ?>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <?php if(!empty($ec['thumbnail'])): ?>
                                        <img src="<?php echo APP_URL . '/' . $ec['thumbnail']; ?>" class="card-img-top rounded-top-4 object-fit-cover" style="height:130px;">
                                    <?php endif; ?>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($ec['title']); ?></h6>
                                        <div class="progress mb-1 rounded-pill" style="height:6px;">
                                            <div class="progress-bar bg-success" style="width:<?php echo $ec['progress_pct']; ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?php echo $ec['done_lessons']; ?>/<?php echo $ec['total_lessons']; ?> bài - <?php echo $ec['progress_pct']; ?>%</small>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 p-3 pt-0 d-flex gap-2">
                                        <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $ec['id']; ?>" class="btn btn-sm btn-primary rounded-pill flex-fill">
                                            <i class="bi bi-play-circle me-1"></i>Tiếp tục học
                                        </a>
                                        <?php if($ec['progress_pct'] >= 80): ?>
                                        <a href="<?php echo APP_URL; ?>/certificate?course_id=<?php echo $ec['id']; ?>" class="btn btn-sm btn-outline-warning rounded-pill" title="Nhận chứng chỉ">
                                            <i class="bi bi-award"></i>
                                        </a>
                                        <?php endif; ?>
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
