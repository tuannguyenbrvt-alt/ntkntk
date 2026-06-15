<div class="row g-4 mb-4">
    <!-- Revenue Card -->
    <div class="col-xl-2 col-md-4 col-sm-6 col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-success text-white overflow-hidden position-relative">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-uppercase fw-bold text-white-50 mb-1" style="font-size: 0.75rem;">Tổng doanh thu</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.4rem;"><?php echo number_format($totalRevenue); ?> đ</h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <small class="text-white-50"><i class="bi bi-arrow-up-circle me-1"></i> Từ lúc chạy hệ thống</small>
            </div>
            <i class="bi bi-cash-coin position-absolute text-white opacity-10" style="font-size: 6rem; right: -15px; bottom: -20px;"></i>
        </div>
    </div>
    
    <!-- Students Card -->
    <div class="col-xl-2 col-md-4 col-sm-6 col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white overflow-hidden position-relative">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-uppercase fw-bold text-white-50 mb-1" style="font-size: 0.75rem;">Tổng học viên</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.4rem;"><?php echo number_format($totalStudents); ?></h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <small class="text-white-50"><i class="bi bi-person-check me-1"></i> Có tài khoản trên hệ thống</small>
            </div>
            <i class="bi bi-person-workspace position-absolute text-white opacity-10" style="font-size: 6rem; right: -15px; bottom: -20px;"></i>
        </div>
    </div>

    <!-- Courses Card -->
    <div class="col-xl-2 col-md-4 col-sm-6 col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-info text-white overflow-hidden position-relative">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-uppercase fw-bold text-white-50 mb-1" style="font-size: 0.75rem;">Khóa học xuất bản</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.4rem;"><?php echo number_format($totalCourses); ?></h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                        <i class="bi bi-journal-bookmark fs-5"></i>
                    </div>
                </div>
                <small class="text-white-50"><i class="bi bi-broadcast me-1"></i> Đang hiển thị (Published)</small>
            </div>
            <i class="bi bi-book position-absolute text-white opacity-10" style="font-size: 6rem; right: -15px; bottom: -20px;"></i>
        </div>
    </div>

    <!-- Pending Enrollments Card -->
    <div class="col-xl-2 col-md-4 col-sm-6 col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-warning text-dark overflow-hidden position-relative">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-uppercase fw-bold text-dark opacity-75 mb-1" style="font-size: 0.75rem;">Đơn chờ duyệt</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.4rem;"><?php echo number_format($pendingEnrollments); ?></h3>
                    </div>
                    <div class="bg-dark bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                        <i class="bi bi-hourglass-split fs-5 text-dark"></i>
                    </div>
                </div>
                <?php if($pendingEnrollments > 0): ?>
                    <small class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i> Cần duyệt ngay!</small>
                <?php else: ?>
                    <small class="text-dark opacity-75"><i class="bi bi-check-all me-1"></i> Đã duyệt hết</small>
                <?php endif; ?>
            </div>
            <i class="bi bi-cart-check position-absolute text-dark opacity-10" style="font-size: 6rem; right: -15px; bottom: -20px;"></i>
        </div>
    </div>

    <!-- Today Visits Card -->
    <div class="col-xl-2 col-md-4 col-sm-6 col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-danger text-white overflow-hidden position-relative">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-uppercase fw-bold text-white-50 mb-1" style="font-size: 0.75rem;">Truy cập hôm nay</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.4rem;"><?php echo number_format($visitStats['today']); ?></h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                        <i class="bi bi-eye fs-5"></i>
                    </div>
                </div>
                <small class="text-white-50"><i class="bi bi-calendar-event me-1"></i> Lượt truy cập trong ngày</small>
            </div>
            <i class="bi bi-eye-fill position-absolute text-white opacity-10" style="font-size: 6rem; right: -15px; bottom: -20px;"></i>
        </div>
    </div>

    <!-- Total Visits Card -->
    <div class="col-xl-2 col-md-4 col-sm-6 col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-secondary text-white overflow-hidden position-relative">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-uppercase fw-bold text-white-50 mb-1" style="font-size: 0.75rem;">Tổng lượt truy cập</h6>
                        <h3 class="fw-bold mb-0" style="font-size: 1.4rem;"><?php echo number_format($visitStats['total']); ?></h3>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                        <i class="bi bi-globe fs-5"></i>
                    </div>
                </div>
                <small class="text-white-50"><i class="bi bi-activity me-1"></i> Tích lũy toàn hệ thống</small>
            </div>
            <i class="bi bi-globe2 position-absolute text-white opacity-10" style="font-size: 6rem; right: -15px; bottom: -20px;"></i>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Revenue Chart -->
    <div class="col-xl-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Biểu đồ Doanh thu (<?php echo $currentYear; ?>)</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments -->
    <div class="col-xl-4">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-2"></i>Giao dịch gần đây</h5>
                <a href="<?php echo APP_URL; ?>/admin/enrollments" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($recentEnrollments as $en): ?>
                        <div class="list-group-item border-0 p-4 border-bottom">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 180px;"><?php echo htmlspecialchars($en['full_name']); ?></h6>
                                <?php if ($en['status'] == 'active'): ?>
                                    <span class="badge bg-success rounded-pill">Đã duyệt</span>
                                <?php elseif ($en['status'] == 'pending'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill">Chờ duyệt</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill"><?php echo $en['status']; ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="mb-1 small text-muted text-truncate"><i class="bi bi-book me-1"></i><?php echo htmlspecialchars($en['course_title']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-bold text-danger"><?php echo number_format($en['price_paid']); ?> đ</span>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo date('d/m H:i', strtotime($en['created_at'])); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($recentEnrollments)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted mb-0">Chưa có giao dịch nào.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Tạo gradient màu cho biểu đồ
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.5)'); // primary color with opacity
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $chartLabels; ?>,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: <?php echo $chartDataValues; ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Làm cong đường đồ thị (smooth curves)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false // Ẩn label chú thích ở trên cùng
                    },
                    tooltip: {
                        backgroundColor: '#212529',
                        padding: 12,
                        titleFont: { size: 14, family: "'Inter', sans-serif" },
                        bodyFont: { size: 14, family: "'Inter', sans-serif" },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                if (value >= 1000000) {
                                    return (value / 1000000) + ' Triệu';
                                }
                                return new Intl.NumberFormat('vi-VN').format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
