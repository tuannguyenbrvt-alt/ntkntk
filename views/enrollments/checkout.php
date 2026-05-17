<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-5 bg-primary text-white p-4 d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="bi bi-qr-code-scan display-1 mb-3"></i>
                        <h4 class="fw-bold">Thanh Toán Quét Mã QR</h4>
                        <p class="small text-white-50">Mở ứng dụng ngân hàng và quét mã để thanh toán tự động và nhanh chóng.</p>
                        
                        <!-- Tạo link QR tự động bằng VietQR -->
                        <?php 
                            $bankId = 'MB'; // Ngân hàng TMCP Quân Đội
                            $accountNo = '8875578100615'; // Số tài khoản
                            $accountName = 'NGUYEN MINH TUAN'; // Tên chủ tài khoản
                            $amount = $enrollment['price'];
                            $content = $enrollment['tx_code'];
                            $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$content}&accountName={$accountName}";
                        ?>
                        <div class="bg-white p-2 rounded-3 mb-3">
                            <img src="<?php echo $qrUrl; ?>" alt="QR Code" class="img-fluid" width="200">
                        </div>
                        <div class="badge bg-white text-primary rounded-pill px-3 py-2 fs-6 mb-2 shadow-sm border border-primary">
                            Số tiền: <strong class="fs-5"><?php echo number_format($amount); ?> VNĐ</strong>
                        </div>
                    </div>
                    <div class="col-md-7 p-5">
                        <h3 class="fw-bold mb-4 text-dark">Thông tin đăng ký</h3>
                        <div class="mb-3 border-bottom pb-3">
                            <div class="text-muted small text-uppercase fw-semibold">Khóa học</div>
                            <div class="fw-bold fs-5 text-primary mt-1"><?php echo htmlspecialchars($enrollment['title']); ?></div>
                        </div>
                        <div class="mb-4 border-bottom pb-3">
                            <div class="text-muted small text-uppercase fw-semibold">Nội dung chuyển khoản (Bắt buộc)</div>
                            <div class="fw-bold fs-4 font-monospace text-danger bg-danger bg-opacity-10 p-2 rounded mt-2 text-center border border-danger">
                                <?php echo htmlspecialchars($enrollment['tx_code']); ?>
                            </div>
                            <small class="text-danger mt-2 d-block"><i class="bi bi-exclamation-triangle-fill"></i> Vui lòng nhập ĐÚNG nội dung chuyển khoản này để hệ thống nhận diện tự động.</small>
                        </div>
                        <div class="mb-4">
                            <div class="text-muted small text-uppercase fw-semibold mb-2">Trạng thái hiện tại</div>
                            <?php if($enrollment['status'] == 'pending'): ?>
                                <div class="alert alert-warning border-warning d-flex align-items-center p-2">
                                    <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>
                                    <strong class="text-dark">Đang chờ hệ thống kiểm tra số dư...</strong>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-success px-3 py-2 fs-6"><i class="bi bi-check-circle-fill"></i> Đã thanh toán</span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="<?php echo APP_URL; ?>/enrollment/done" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow-sm"><i class="bi bi-check2-circle"></i> XÁC NHẬN ĐÃ CHUYỂN KHOẢN</a>
                        <div class="text-center mt-4">
                            <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $enrollment['slug']; ?>" class="text-decoration-none text-muted small hover-primary"><i class="bi bi-arrow-left"></i> Quay lại trang khóa học</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
