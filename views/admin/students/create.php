<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white p-4 border-bottom-0 rounded-top-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-plus-fill me-2"></i>Thêm Học viên mới</h5>
        <a href="<?php echo APP_URL; ?>/admin/students" class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mx-4 mb-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card-body p-4">
        <form action="<?php echo APP_URL; ?>/admin/students/store" method="POST" id="createStudentForm">
            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="bi bi-shield-lock me-1"></i>Thông tin tài khoản</h6>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-muted">Tên đăng nhập *</label>
                    <input type="text" name="username" class="form-control rounded-3" required minlength="4" placeholder="Tối thiểu 4 ký tự" value="<?php echo htmlspecialchars($old['username'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-muted">Mật khẩu đăng nhập *</label>
                    <input type="password" name="password" id="password" class="form-control rounded-3" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                </div>
            </div>
            
            <h6 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="bi bi-person-badge me-1"></i>Thông tin cá nhân & Liên hệ</h6>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-muted">Họ và tên *</label>
                    <input type="text" name="full_name" class="form-control rounded-3" required placeholder="Ví dụ: Nguyễn Văn A" value="<?php echo htmlspecialchars($old['full_name'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-muted">Email *</label>
                    <input type="email" name="email" class="form-control rounded-3" required placeholder="Ví dụ: hocvien@gmail.com" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-muted">Số điện thoại *</label>
                    <input type="tel" name="phone" class="form-control rounded-3" required placeholder="Ví dụ: 0912345678" value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-muted">Ngày sinh</label>
                    <input type="date" name="dob" class="form-control rounded-3" value="<?php echo htmlspecialchars($old['dob'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-muted">Nghề nghiệp / Lớp học</label>
                    <input type="text" name="profession" class="form-control rounded-3" placeholder="Ví dụ: Học sinh, Sinh viên, Người đi làm..." value="<?php echo htmlspecialchars($old['profession'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small text-muted">Địa chỉ / Ghi chú</label>
                    <input type="text" name="address" class="form-control rounded-3" placeholder="Ví dụ: 123 Đường 30/4, Vũng Tàu" value="<?php echo htmlspecialchars($old['address'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold"><i class="bi bi-check-circle me-1"></i> Lưu thông tin</button>
            </div>
        </form>
    </div>
</div>
