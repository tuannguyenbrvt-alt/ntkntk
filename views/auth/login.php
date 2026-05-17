<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-4 fw-bold">Đăng nhập</h3>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form action="<?php echo APP_URL; ?>/login" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" required placeholder="Nhập username...">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required placeholder="Nhập password...">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Đăng nhập</button>
                    <div class="text-center mt-4">
                        <a href="<?php echo APP_URL; ?>/register" class="text-decoration-none">Chưa có tài khoản? Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
