<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4 fw-bold">Đăng ký tài khoản</h3>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form action="<?php echo APP_URL; ?>/register" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên</label>
                        <input type="text" name="full_name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" required minlength="4" placeholder="Tối thiểu 4 ký tự">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="Email của bạn">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="Tối thiểu 6 ký tự">
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Đăng ký</button>
                    <div class="text-center mt-4">
                        <a href="<?php echo APP_URL; ?>/login" class="text-decoration-none">Đã có tài khoản? Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
