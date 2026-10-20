<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 bg-dark text-white" style="border: 1px solid #333 !important;">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-lock-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">Đặt lại mật khẩu</h3>
                    <p class="text-white-50 small mb-0">Tạo mật khẩu mới an toàn cho tài khoản của bạn.</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger bg-danger text-white border-0 fw-semibold text-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($expired)): ?>
                    <div class="text-center mt-3">
                        <a href="<?php echo APP_URL; ?>/forgot-password" class="btn btn-success w-100 py-2 fw-bold mb-2">
                            <i class="bi bi-arrow-repeat me-1"></i>Yêu cầu liên kết mới
                        </a>
                        <a href="<?php echo APP_URL; ?>/login" class="text-decoration-none text-success-subtle small">
                            <i class="bi bi-arrow-left me-1"></i>Về trang Đăng nhập
                        </a>
                    </div>
                <?php else: ?>
                    <form action="<?php echo APP_URL; ?>/reset-password" method="POST" id="resetPasswordForm">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-white-50">Mật khẩu mới *</label>
                            <input type="password" name="password" id="password" class="form-control" style="background:#111;color:#eee;border-color:#444;" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-white-50">Xác nhận mật khẩu mới *</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" style="background:#111;color:#eee;border-color:#444;" required minlength="6" placeholder="Nhập lại mật khẩu mới">
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold mb-3">
                            <i class="bi bi-check-circle-fill me-1"></i>Cập nhật mật khẩu mới
                        </button>

                        <div class="text-center pt-2 border-top border-secondary border-opacity-25">
                            <a href="<?php echo APP_URL; ?>/login" class="text-decoration-none text-success-subtle small">
                                <i class="bi bi-arrow-left me-1"></i>Hủy bỏ và <span class="fw-bold text-success text-decoration-underline">Đăng nhập</span>
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
var resetForm = document.getElementById('resetPasswordForm');
if (resetForm) {
    resetForm.addEventListener('submit', function(e) {
        var p1 = document.getElementById('password').value;
        var p2 = document.getElementById('confirm_password').value;
        if (p1 !== p2) {
            e.preventDefault();
            alert('Mật khẩu xác nhận không khớp. Vui lòng kiểm tra lại!');
        }
    });
}
</script>
