<?php
// views/admin/chat/performance.php

// Helper closure to format response time duration into human readable string
$formatDuration = function($seconds) {
    if ($seconds <= 0) return '0 giây';
    if ($seconds < 60) return $seconds . ' giây';
    if ($seconds < 3600) {
        $mins = floor($seconds / 60);
        $secs = $seconds % 60;
        return $mins . ' phút' . ($secs > 0 ? ' ' . $secs . ' giây' : '');
    }
    if ($seconds < 86400) {
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds % 3600) / 60);
        return $hours . ' giờ' . ($mins > 0 ? ' ' . $mins . ' phút' : '');
    }
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    return $days . ' ngày' . ($hours > 0 ? ' ' . $hours . ' giờ' : '');
};

// Compute general statistics across all admins
$totalReplies = 0;
$totalSeconds = 0;
$allMin = 999999999;
$allMax = 0;

foreach ($responseStats as $stat) {
    $totalReplies += $stat['count'];
    $totalSeconds += $stat['total_time'];
    if ($stat['min_time'] < $allMin && $stat['count'] > 0) {
        $allMin = $stat['min_time'];
    }
    if ($stat['max_time'] > $allMax) {
        $allMax = $stat['max_time'];
    }
}
$avgSystemTime = $totalReplies > 0 ? round($totalSeconds / $totalReplies) : 0;
if ($allMin === 999999999) {
    $allMin = 0;
}
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold text-dark"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Hiệu suất phản hồi Chat</h4>
                <p class="text-muted mb-0">Thống kê chi tiết tốc độ phản hồi tin nhắn của Giáo viên và Admin để tối ưu chất lượng hỗ trợ học viên.</p>
            </div>
            <a href="<?php echo APP_URL; ?>/admin/chat" class="btn btn-outline-secondary">
                <i class="bi bi-chat-dots me-2"></i> Vào trang Chat
            </a>
        </div>
    </div>
</div>

<!-- Key Performance Indicators (Cards) -->
<div class="row mb-4">
    <!-- Card 1: Avg Response Time -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7); color: white;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="fs-6 opacity-75">T.gian Phản hồi Trung bình</span>
                        <h2 class="fw-bold mt-2 mb-0"><?php echo $formatDuration($avgSystemTime); ?></h2>
                    </div>
                    <div class="p-2 bg-white bg-opacity-25 rounded-3">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <?php if ($avgSystemTime == 0): ?>
                        <span class="badge bg-white bg-opacity-25 text-white">Chưa có dữ liệu</span>
                    <?php elseif ($avgSystemTime < 300): ?>
                        <span class="badge bg-success text-white"><i class="bi bi-lightning-fill me-1"></i> Xuất sắc (< 5m)</span>
                    <?php elseif ($avgSystemTime < 900): ?>
                        <span class="badge bg-info text-dark"><i class="bi bi-check-circle-fill me-1"></i> Tốt (< 15m)</span>
                    <?php elseif ($avgSystemTime < 3600): ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i> Chậm (< 1h)</span>
                    <?php else: ?>
                        <span class="badge bg-danger text-white"><i class="bi bi-x-circle-fill me-1"></i> Cần cải thiện (> 1h)</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Replies -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3 text-dark">
                    <div>
                        <span class="fs-6 text-muted">Tổng số lượt phản hồi</span>
                        <h2 class="fw-bold mt-2 mb-0 text-primary"><?php echo number_format($totalReplies); ?></h2>
                    </div>
                    <div class="p-2 bg-primary bg-opacity-10 rounded-3 text-primary">
                        <i class="bi bi-chat-left-heart fs-4"></i>
                    </div>
                </div>
                <p class="text-muted mb-0"><small>Tổng số tin nhắn của admin gửi sau tin nhắn học viên</small></p>
            </div>
        </div>
    </div>

    <!-- Card 3: Fastest Response -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3 text-dark">
                    <div>
                        <span class="fs-6 text-muted">Phản hồi Nhanh nhất</span>
                        <h2 class="fw-bold mt-2 mb-0 text-success"><?php echo $totalReplies > 0 ? $formatDuration($allMin) : 'Chưa có'; ?></h2>
                    </div>
                    <div class="p-2 bg-success bg-opacity-10 rounded-3 text-success">
                        <i class="bi bi-lightning-charge fs-4"></i>
                    </div>
                </div>
                <p class="text-muted mb-0"><small>Tốc độ phản hồi nhanh nhất từng được ghi nhận</small></p>
            </div>
        </div>
    </div>

    <!-- Card 4: Slowest Response -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3 text-dark">
                    <div>
                        <span class="fs-6 text-muted">Phản hồi Lâu nhất</span>
                        <h2 class="fw-bold mt-2 mb-0 text-danger"><?php echo $totalReplies > 0 ? $formatDuration($allMax) : 'Chưa có'; ?></h2>
                    </div>
                    <div class="p-2 bg-danger bg-opacity-10 rounded-3 text-danger">
                        <i class="bi bi-hourglass-bottom fs-4"></i>
                    </div>
                </div>
                <p class="text-muted mb-0"><small>Khoảng cách phản hồi lớn nhất (cần khắc phục)</small></p>
            </div>
        </div>
    </div>
</div>

<!-- Teachers Performance Table -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-people-fill text-primary me-2"></i> Hiệu suất theo Giáo viên / Admin</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-nowrap align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="ps-4">Tên Giáo viên / Admin</th>
                    <th scope="col">Số lượt phản hồi</th>
                    <th scope="col">Thời gian trung bình</th>
                    <th scope="col">Nhanh nhất</th>
                    <th scope="col">Lâu nhất</th>
                    <th scope="col" class="pe-4">Đánh giá chung</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($responseStats)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-3"></i>
                            Chưa có dữ liệu phản hồi nào được ghi nhận.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($responseStats as $id => $stat): ?>
                        <tr>
                            <td class="ps-4 fw-bold">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($stat['name']); ?>&background=random" alt="" class="rounded-circle me-2" width="32" height="32">
                                    <?php echo htmlspecialchars($stat['name']); ?>
                                </div>
                            </td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fs-6 rounded-pill"><?php echo $stat['count']; ?></span></td>
                            <td class="fw-bold"><?php echo $formatDuration($stat['avg_time']); ?></td>
                            <td class="text-success"><?php echo $formatDuration($stat['min_time']); ?></td>
                            <td class="text-danger"><?php echo $formatDuration($stat['max_time']); ?></td>
                            <td class="pe-4">
                                <?php
                                $avg = $stat['avg_time'];
                                if ($avg == 0):
                                    echo '<span class="badge bg-secondary">Không có đánh giá</span>';
                                elseif ($avg < 300):
                                    echo '<span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-star-fill me-1"></i> Xuất sắc (< 5m)</span>';
                                elseif ($avg < 900):
                                    echo '<span class="badge bg-info text-dark px-3 py-2 rounded-pill"><i class="bi bi-star-half me-1"></i> Tốt (< 15m)</span>';
                                elseif ($avg < 3600):
                                    echo '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i> Trung bình (< 1h)</span>';
                                else:
                                    echo '<span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-x-octagon me-1"></i> Cần cải thiện</span>';
                                endif;
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed Logs of Recent Responses -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-primary me-2"></i> Lịch sử phản hồi gần nhất (Tối đa 50 sự kiện)</h5>
    </div>
    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light sticky-top">
                <tr>
                    <th scope="col" class="ps-4">Cuộc trò chuyện (Khóa học/Chung)</th>
                    <th scope="col">Học viên/Khách</th>
                    <th scope="col">Người phản hồi</th>
                    <th scope="col">Tin nhắn học viên</th>
                    <th scope="col">Tin phản hồi</th>
                    <th scope="col" class="pe-4 text-end">Thời gian phản hồi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($detailedLogs)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Chưa có lịch sử phản hồi chi tiết.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($detailedLogs as $log): ?>
                        <?php 
                        $delay = $log['response_time'];
                        if ($delay < 300) {
                            $badgeClass = 'bg-success';
                            $badgeText = 'Xuất sắc';
                        } elseif ($delay < 900) {
                            $badgeClass = 'bg-info text-dark';
                            $badgeText = 'Tốt';
                        } elseif ($delay < 3600) {
                            $badgeClass = 'bg-warning text-dark';
                            $badgeText = 'Chậm';
                        } else {
                            $badgeClass = 'bg-danger';
                            $badgeText = 'Rất chậm';
                        }
                        ?>
                        <tr>
                            <td class="ps-4">
                                <small class="text-muted d-block"><?php echo htmlspecialchars($log['thread_title']); ?></small>
                            </td>
                            <td><strong><?php echo htmlspecialchars($log['student_name']); ?></strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-check-fill text-primary me-1"></i>
                                    <?php echo htmlspecialchars($log['responder_name']); ?>
                                </div>
                            </td>
                            <td><small class="text-muted"><?php echo date('H:i d/m/Y', strtotime($log['student_time'])); ?></small></td>
                            <td><small class="text-muted"><?php echo date('H:i d/m/Y', strtotime($log['responder_time'])); ?></small></td>
                            <td class="pe-4 text-end">
                                <span class="d-block fw-bold"><?php echo $formatDuration($delay); ?></span>
                                <span class="badge <?php echo $badgeClass; ?> rounded-pill" style="font-size: 10px;"><?php echo $badgeText; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
