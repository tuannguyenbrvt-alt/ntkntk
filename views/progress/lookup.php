<div class="container py-5" style="max-width:500px;margin:auto;">
    <div class="card border-0 shadow bg-dark text-white" style="border: 1px solid #333 !important;">
        <div class="card-body text-center p-4 p-md-5">
            <div class="mb-4">
                <i class="bi bi-search text-success" style="font-size:3.5rem;"></i>
            </div>
            <h4 class="fw-bold mt-2 text-success">Tra cứu Kết quả Học tập</h4>
            <p class="text-white-50 small">Nhập tên đăng nhập và số điện thoại để tra cứu kết quả bài tập & trắc nghiệm</p>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger bg-danger bg-opacity-20 text-danger border border-danger border-opacity-50 mt-3 small text-start">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo APP_URL; ?>/progress/lookupResult" class="mt-4 text-start">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-white-50">Tên đăng nhập *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary border-opacity-50 text-white-50"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" style="background:#111;color:#eee;border-color:#444;" placeholder="Nhập tên đăng nhập của học viên..." required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-white-50">Số điện thoại *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary border-opacity-50 text-white-50"><i class="bi bi-telephone"></i></span>
                        <input type="tel" name="phone" class="form-control" style="background:#111;color:#eee;border-color:#444;" placeholder="Nhập số điện thoại đăng ký..." required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold"><i class="bi bi-shield-check me-1"></i>Xác minh & Tra cứu</button>
            </form>
            <div class="text-muted small mt-4 border-top border-secondary border-opacity-25 pt-3">
                <i class="bi bi-info-circle me-1 text-success"></i>Thông tin tra cứu phải khớp hoàn toàn với hồ sơ đăng ký lớp học của trung tâm.
            </div>
        </div>
    </div>
</div>
