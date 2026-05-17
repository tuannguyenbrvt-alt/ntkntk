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
                    <a href="<?php echo APP_URL; ?>/profile" class="list-group-item list-group-item-action fw-semibold border-0 rounded"><i class="bi bi-book me-2"></i> Khóa học của tôi</a>
                    <a href="<?php echo APP_URL; ?>/profile/settings" class="list-group-item list-group-item-action active fw-semibold border-0 rounded"><i class="bi bi-person-gear me-2"></i> Cài đặt tài khoản</a>
                    <a href="<?php echo APP_URL; ?>/logout" class="list-group-item list-group-item-action text-danger fw-semibold border-0 rounded"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-bottom-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-lines-fill text-primary me-2"></i> Hồ sơ cá nhân</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="<?php echo APP_URL; ?>/profile/update" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Họ và tên</label>
                                <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <label class="form-label text-muted small fw-semibold">Địa chỉ Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Số điện thoại</label>
                                <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <label class="form-label text-muted small fw-semibold">Ngày sinh</label>
                                <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Nghề nghiệp / Công việc hiện tại</label>
                            <input type="text" class="form-control" name="profession" value="<?php echo htmlspecialchars($user['profession'] ?? ''); ?>" placeholder="Ví dụ: Sinh viên, Kế toán, Lập trình viên...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Địa chỉ liên hệ</label>
                            <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-semibold">Ảnh đại diện (Tùy chọn)</label>
                            <input type="file" class="form-control" name="avatar" accept="image/jpeg, image/png, image/webp, image/gif">
                            <small class="text-muted">Hỗ trợ JPG, PNG, WEBP. Kích thước &lt; 2MB</small>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm"><i class="bi bi-save me-1"></i> Lưu thông tin</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white p-4 border-bottom-0">
                    <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock text-dark me-2"></i> Đổi mật khẩu</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <form action="<?php echo APP_URL; ?>/profile/updatePassword" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" name="old_password" required>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Mật khẩu mới</label>
                                <input type="password" class="form-control" name="new_password" required minlength="6">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark px-4 fw-bold rounded-pill"><i class="bi bi-key me-1"></i> Cập nhật mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
