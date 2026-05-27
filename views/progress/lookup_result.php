<div class="container py-4" style="max-width:960px;margin:auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-dark fw-bold mb-1"><i class="bi bi-person-badge me-2 text-success"></i>Học viên: <?php echo htmlspecialchars($user['full_name']); ?></h4>
            <div class="text-muted small">
                <span>Tên đăng nhập: <strong class="text-success"><?php echo htmlspecialchars($user['username']); ?></strong></span>
                <span class="mx-2">|</span>
                <span>Số điện thoại: <strong class="text-success"><?php echo htmlspecialchars($user['phone']); ?></strong></span>
            </div>
        </div>
        <a href="<?php echo APP_URL; ?>/progress/lookup" class="btn btn-outline-dark btn-sm"><i class="bi bi-arrow-left me-1"></i>Tra cứu lại</a>
    </div>

    <!-- Bảng tổng hợp kết quả thống kê -->
    <h5 class="text-secondary mb-3"><i class="bi bi-pie-chart me-1 text-success"></i>Bảng tổng hợp kết quả học tập</h5>
    <div class="row g-3 mb-5">
        <!-- Trắc nghiệm -->
        <div class="col-md-4">
            <div class="card border-0 h-100 bg-dark text-white shadow-sm" style="border: 1px solid #333 !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold text-success"><i class="bi bi-list-check me-1"></i>Trắc nghiệm</span>
                            <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-50">Quiz</span>
                        </div>
                        <h3 class="fw-bold mb-2"><?php echo $quizStats['attempted_quizzes']; ?> <span class="fs-6 text-white-50">/ <?php echo $quizStats['total_quizzes']; ?> đề</span></h3>
                        <div class="text-muted small">
                            <div class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-25">
                                <span>Số bài đạt:</span> <strong class="text-success"><?php echo $quizStats['passed_quizzes']; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 text-white-50">
                                <span>Tỷ lệ đạt:</span> <strong class="text-success"><?php echo $quizStats['attempted_quizzes'] > 0 ? round($quizStats['passed_quizzes'] / $quizStats['attempted_quizzes'] * 100) : 0; ?>%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bài tập Tự luận -->
        <?php $essay = $asgStats['essay']; ?>
        <div class="col-md-4">
            <div class="card border-0 h-100 bg-dark text-white shadow-sm" style="border: 1px solid #333 !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold text-primary"><i class="bi bi-journal-text me-1"></i>BT Tự luận</span>
                            <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-50">Essay</span>
                        </div>
                        <h3 class="fw-bold mb-2"><?php echo $essay['submitted']; ?> <span class="fs-6 text-white-50">/ <?php echo $essay['total']; ?> bài</span></h3>
                        <div class="text-muted small">
                            <div class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-25">
                                <span>Đã chấm:</span> <strong class="text-white"><?php echo $essay['graded']; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 text-white-50">
                                <span>Điểm tích lũy:</span> <strong class="text-primary"><?php echo $essay['score']; ?> <span class="text-white-50">/ <?php echo $essay['max_score']; ?></span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bài tập Nộp file -->
        <?php $file = $asgStats['file']; ?>
        <div class="col-md-4">
            <div class="card border-0 h-100 bg-dark text-white shadow-sm" style="border: 1px solid #333 !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold text-info"><i class="bi bi-cloud-arrow-up me-1"></i>BT Nộp file</span>
                            <span class="badge bg-info bg-opacity-20 text-info border border-info border-opacity-50">File</span>
                        </div>
                        <h3 class="fw-bold mb-2"><?php echo $file['submitted']; ?> <span class="fs-6 text-white-50">/ <?php echo $file['total']; ?> bài</span></h3>
                        <div class="text-muted small">
                            <div class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-25">
                                <span>Đã chấm:</span> <strong class="text-white"><?php echo $file['graded']; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between py-1 text-white-50">
                                <span>Điểm tích lũy:</span> <strong class="text-info"><?php echo $file['score']; ?> <span class="text-white-50">/ <?php echo $file['max_score']; ?></span></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết quiz -->
    <?php if(!empty($quizResults)): ?>
    <h5 class="text-secondary mb-3"><i class="bi py-1 bi-list-check text-success me-2"></i>Kết quả Trắc nghiệm</h5>
    <div class="card border-0 shadow-sm mb-5 bg-dark text-white" style="border: 1px solid #333 !important;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead><tr><th>Khóa học</th><th>Đề thi</th><th>Điểm số</th><th>Kết quả</th><th>Ngày làm</th></tr></thead>
                <tbody>
                <?php foreach($quizResults as $r): ?>
                <tr>
                    <td class="text-muted small"><?php echo htmlspecialchars($r['course_title']); ?></td>
                    <td><?php echo htmlspecialchars($r['quiz_title']); ?></td>
                    <td><strong class="text-success"><?php echo $r['score']; ?>%</strong></td>
                    <td><?php echo $r['passed'] ? '<span class="badge bg-success-subtle text-success border border-success">Đạt</span>' : '<span class="badge bg-danger-subtle text-danger border border-danger">Chưa đạt</span>'; ?></td>
                    <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($r['submitted_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Chi tiết bài tập -->
    <?php if(!empty($asgResults)): ?>
    <h5 class="text-secondary mb-3"><i class="bi bi-file-earmark-text text-success me-2"></i>Kết quả Bài tập</h5>
    <div class="card border-0 shadow-sm bg-dark text-white" style="border: 1px solid #333 !important;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead><tr><th>Khóa học</th><th>Bài tập</th><th>Loại bài</th><th>Điểm số</th><th>Nhận xét giáo viên</th><th>Ngày nộp</th></tr></thead>
                <tbody>
                <?php foreach($asgResults as $r): ?>
                <tr>
                    <td class="text-muted small"><?php echo htmlspecialchars($r['course_title']); ?></td>
                    <td><?php echo htmlspecialchars($r['asgn_title']); ?></td>
                    <td><?php echo $r['type']==='essay'?'Tự luận':'Nộp file'; ?></td>
                    <td><?php echo $r['score'] !== null ? '<strong class="text-success">'.$r['score'].'</strong>/'.$r['max_score'] : '<span class="text-muted">Chờ chấm</span>'; ?></td>
                    <td class="small text-muted"><?php echo $r['feedback'] ? htmlspecialchars($r['feedback']) : '—'; ?></td>
                    <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($r['submitted_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
