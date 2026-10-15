<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-5 bg-primary text-white p-4 d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="bi bi-qr-code-scan display-1 mb-3"></i>
                        <h4 class="fw-bold">Thanh Toán Quét Mã QR</h4>
                        <p class="small text-white-50">Mở ứng dụng ngân hàng và quét mã để thanh toán tự động và kích hoạt khóa học tức thì.</p>
                        
                        <!-- Tạo link QR tự động bằng VietQR -->
                        <?php 
                            $bankId = 'MB'; // Ngân hàng TMCP Quân Đội
                            $accountNo = '8875578100615'; // Số tài khoản MBBank
                            $accountName = 'NGUYEN MINH TUAN'; // Tên chủ tài khoản
                            $amount = $enrollment['price'];
                            $content = $enrollment['tx_code'];
                            $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$content}&accountName={$accountName}";
                        ?>
                        <div class="bg-white p-2 rounded-3 mb-3 shadow-sm">
                            <img src="<?php echo $qrUrl; ?>" alt="QR Code" class="img-fluid" width="220">
                        </div>
                        <div class="badge bg-white text-primary rounded-pill px-3 py-2 fs-6 mb-2 shadow-sm border border-primary">
                            Số tiền: <strong class="fs-5"><?php echo number_format($amount); ?> VNĐ</strong>
                        </div>
                    </div>
                    <div class="col-md-7 p-4 p-md-5">
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
                            <small class="text-danger mt-2 d-block"><i class="bi bi-exclamation-triangle-fill"></i> Vui lòng giữ NGUYÊN nội dung chuyển khoản để hệ thống SePay tự động kích hoạt khóa học ngay sau khi tiền vào tài khoản.</small>
                        </div>
                        
                        <!-- KHUNG TRẠNG THÁI THANH TOÁN THỜI GIAN THỰC -->
                        <div class="mb-4" id="status-container">
                            <?php if($enrollment['status'] == 'pending'): ?>
                                <div id="box-waiting" class="alert alert-warning border-warning p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="spinner-border spinner-border-sm text-warning me-3" role="status"></div>
                                        <div class="text-dark small">
                                            <strong class="d-block mb-1">⚡ Đang chờ nhận diện chuyển khoản tự động...</strong>
                                            Khóa học sẽ tự động được kích hoạt và chuyển vào bài học trong 3 giây sau khi bạn chuyển khoản thành công.
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="box-success" class="alert alert-success border-success p-3 text-center" style="display: none;">
                                    <i class="bi bi-check-circle-fill display-4 text-success d-block mb-2"></i>
                                    <h5 class="fw-bold text-success mb-1">🎉 THANH TOÁN THÀNH CÔNG!</h5>
                                    <p class="small text-muted mb-3">Khóa học của bạn đã được kích hoạt thành công. Đang chuyển hướng vào học...</p>
                                    <a id="btn-into-learning" href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $enrollment['course_id']; ?>" class="btn btn-success btn-lg w-100 fw-bold rounded-pill shadow-sm">
                                        <i class="bi bi-play-fill fs-5"></i> VÀO HỌC NGAY
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success border-success p-3 text-center">
                                    <i class="bi bi-check-circle-fill fs-2 text-success me-2"></i>
                                    <strong class="fs-6 text-success">Đã thanh toán thành công</strong>
                                    <div class="mt-3">
                                        <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $enrollment['course_id']; ?>" class="btn btn-success btn-lg w-100 fw-bold rounded-pill shadow-sm">
                                            <i class="bi bi-play-fill fs-5"></i> VÀO HỌC NGAY
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($enrollment['status'] == 'pending'): ?>
                            <a id="btn-done-manual" href="<?php echo APP_URL; ?>/enrollment/done" class="btn btn-outline-secondary w-100 fw-semibold rounded-pill py-2">
                                <i class="bi bi-arrow-clockwise"></i> Tôi đã chuyển khoản xong (Tải lại)
                            </a>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $enrollment['slug']; ?>" class="text-decoration-none text-muted small hover-primary"><i class="bi bi-arrow-left"></i> Quay lại trang khóa học</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($enrollment['status'] == 'pending'): ?>
<script>
(function() {
    const enrollmentId = <?php echo (int)$enrollment['id']; ?>;
    const courseId = <?php echo (int)$enrollment['course_id']; ?>;
    let pollCount = 0;
    const maxPoll = 240; // Polling trong 10 phút (240 * 2.5s)

    const timer = setInterval(async () => {
        pollCount++;
        if (pollCount > maxPoll) {
            clearInterval(timer);
            return;
        }

        try {
            const resp = await fetch('<?php echo APP_URL; ?>/api/enrollment/check-status?id=' + enrollmentId);
            if (resp.ok) {
                const data = await resp.json();
                if (data.status === 'active') {
                    clearInterval(timer);

                    // Ẩn spinner chờ, hiện giao diện thành công rực rỡ
                    const boxWaiting = document.getElementById('box-waiting');
                    const boxSuccess = document.getElementById('box-success');
                    const btnManual = document.getElementById('btn-done-manual');
                    if (boxWaiting) boxWaiting.style.display = 'none';
                    if (btnManual) btnManual.style.display = 'none';
                    if (boxSuccess) boxSuccess.style.display = 'block';

                    // Tự động chuyển hướng vào phòng học sau 2.5 giây
                    setTimeout(() => {
                        window.location.href = data.learning_url || ('<?php echo APP_URL; ?>/learning?course_id=' + courseId);
                    }, 2500);
                }
            }
        } catch (e) {
            // Lỗi mạng tạm thời, bỏ qua chờ lượt sau
        }
    }, 2500);
})();
</script>
<?php endif; ?>
