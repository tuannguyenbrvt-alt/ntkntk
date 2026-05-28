<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 bg-dark text-white" style="border: 1px solid #333 !important;">
            <div class="card-body p-4 p-md-5">
                <h3 class="text-center mb-4 fw-bold text-success"><i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập</h3>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger bg-danger text-white border-0 fw-semibold text-center mb-3"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form action="<?php echo APP_URL; ?>/login" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-white-50">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" style="background:#111;color:#eee;border-color:#444;" required placeholder="Nhập tên đăng nhập...">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-white-50">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" style="background:#111;color:#eee;border-color:#444;" required placeholder="Nhập mật khẩu...">
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold"><i class="bi bi-door-open-fill me-1"></i>Đăng nhập</button>
                    
                    <div class="text-center mt-3 mb-2 text-muted small">— HOẶC —</div>
                    
                    <a href="<?php echo APP_URL; ?>/auth/google" class="btn btn-outline-light w-100 py-2 d-flex align-items-center justify-content-center gap-2 border-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google text-danger" viewBox="0 0 16 16">
                            <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0c2.2 0 4.07.818 5.542 2.215l-2.284 2.284c-.607-.58-1.666-1.257-3.258-1.257-2.793 0-5.068 2.312-5.068 5.16s2.275 5.16 5.068 5.16c3.24 0 4.461-2.327 4.654-3.53H8v-2.91h7.545z"/>
                        </svg>
                        Đăng nhập bằng Google (Gmail)
                    </a>

                    <div class="text-center mt-4 border-top border-secondary border-opacity-25 pt-3">
                        <a href="<?php echo APP_URL; ?>/register" class="text-decoration-none text-success-subtle">Chưa có tài khoản? <span class="fw-bold text-success text-decoration-underline">Đăng ký ngay</span></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
