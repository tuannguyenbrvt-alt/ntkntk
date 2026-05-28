<div class="row justify-content-center mt-4">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0 bg-dark text-white" style="border: 1px solid #333 !important;">
            <div class="card-body p-4 p-md-5">
                <h3 class="text-center mb-4 fw-bold text-success"><i class="bi bi-person-plus-fill me-2"></i>Đăng ký tài khoản</h3>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger bg-danger text-white border-0 fw-semibold text-center mb-3"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form action="<?php echo APP_URL; ?>/register" method="POST" id="registerForm">
                    <h6 class="text-success border-bottom border-secondary border-opacity-25 pb-2 mb-3"><i class="bi bi-shield-lock me-1"></i>Thông tin đăng nhập</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-white-50">Tên đăng nhập *</label>
                            <input type="text" name="username" class="form-control" style="background:#111;color:#eee;border-color:#444;" required minlength="4" placeholder="Tối thiểu 4 ký tự" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-white-50">Email *</label>
                            <input type="email" name="email" class="form-control" style="background:#111;color:#eee;border-color:#444;" required placeholder="example@gmail.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-white-50">Mật khẩu *</label>
                            <input type="password" name="password" id="password" class="form-control" style="background:#111;color:#eee;border-color:#444;" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-white-50">Nhập lại mật khẩu *</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" style="background:#111;color:#eee;border-color:#444;" required minlength="6" placeholder="Nhập lại mật khẩu">
                        </div>
                    </div>

                    <h6 class="text-success border-bottom border-secondary border-opacity-25 pb-2 mb-3 mt-3"><i class="bi bi-person-badge me-1"></i>Thông tin cá nhân</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-white-50">Họ và tên học viên *</label>
                        <input type="text" name="full_name" class="form-control" style="background:#111;color:#eee;border-color:#444;" required placeholder="Ví dụ: Nguyễn Văn A" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-white-50">Số điện thoại *</label>
                            <input type="tel" name="phone" class="form-control" style="background:#111;color:#eee;border-color:#444;" required placeholder="Ví dụ: 0987654321" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small text-white-50">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control" style="background:#111;color:#eee;border-color:#444;" value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-white-50">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" style="background:#111;color:#eee;border-color:#444;" placeholder="Ví dụ: 123 Đường 30/4, Vũng Tàu" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-white-50">Nghề nghiệp / Lớp học</label>
                        <input type="text" name="profession" class="form-control" style="background:#111;color:#eee;border-color:#444;" placeholder="Ví dụ: Học sinh lớp 12, Sinh viên đại học, Người đi làm..." value="<?php echo htmlspecialchars($_POST['profession'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Hoàn tất đăng ký</button>
                    
                    <div class="text-center mt-3 mb-1 text-muted small">— HOẶC ĐĂNG KÝ NHANH —</div>
                    
                    <a href="<?php echo APP_URL; ?>/auth/google" class="btn btn-outline-light w-100 py-2 d-flex align-items-center justify-content-center gap-2 border-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google text-danger" viewBox="0 0 16 16">
                            <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0c2.2 0 4.07.818 5.542 2.215l-2.284 2.284c-.607-.58-1.666-1.257-3.258-1.257-2.793 0-5.068 2.312-5.068 5.16s2.275 5.16 5.068 5.16c3.24 0 4.461-2.327 4.654-3.53H8v-2.91h7.545z"/>
                        </svg>
                        Đăng ký bằng Google (Gmail)
                    </a>
                    
                    <div class="text-center mt-4 border-top border-secondary border-opacity-25 pt-3">
                        <a href="<?php echo APP_URL; ?>/login" class="text-decoration-none text-success-subtle">Đã có tài khoản? <span class="fw-bold text-success text-decoration-underline">Đăng nhập ngay</span></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    var password = document.getElementById('password').value;
    var confirmPassword = document.getElementById('confirm_password').value;
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Mật khẩu nhập lại không khớp. Vui lòng kiểm tra lại!');
    }
});
</script>
