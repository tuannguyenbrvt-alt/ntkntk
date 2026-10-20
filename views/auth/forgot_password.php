<div class="row justify-content-center mt-5">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 bg-dark text-white" style="border: 1px solid #333 !important;">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-key-fill fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">Quên mật khẩu?</h3>
                    <p class="text-white-50 small mb-0">Đừng lo lắng! Nhập email đã đăng ký tài khoản của bạn để nhận liên kết đặt lại mật khẩu.</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger bg-danger text-white border-0 fw-semibold text-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success bg-success text-white border-0 fw-semibold text-center mb-4">
                        <i class="bi bi-check-circle-fill me-1"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                    <div class="text-center">
                        <a href="<?php echo APP_URL; ?>/login" class="btn btn-outline-light w-100 py-2">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại Đăng nhập
                        </a>
                    </div>
                <?php else: ?>
                    <form action="<?php echo APP_URL; ?>/forgot-password" method="POST" id="forgotForm">
                        <!-- Honeypot (Bẫy bot ngầm) -->
                        <div style="display:none;" aria-hidden="true">
                            <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-white-50">Địa chỉ Email đăng ký *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" style="background:#111;color:#eee;border-color:#444;" required placeholder="example@gmail.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <?php if (defined('TURNSTILE_ENABLED') && TURNSTILE_ENABLED): ?>
                        <div class="mb-3 d-flex justify-content-center">
                            <div class="cf-turnstile" data-sitekey="<?php echo htmlspecialchars(TURNSTILE_SITE_KEY); ?>" data-theme="dark"></div>
                        </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold mb-3">
                            <i class="bi bi-send-fill me-1"></i>Gửi link đặt lại mật khẩu
                        </button>
                        
                        <div class="text-center pt-2 border-top border-secondary border-opacity-25">
                            <a href="<?php echo APP_URL; ?>/login" class="text-decoration-none text-success-subtle small">
                                <i class="bi bi-arrow-left me-1"></i>Nhớ lại mật khẩu? <span class="fw-bold text-success text-decoration-underline">Đăng nhập</span>
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (defined('TURNSTILE_ENABLED') && TURNSTILE_ENABLED): ?>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<?php endif; ?>
