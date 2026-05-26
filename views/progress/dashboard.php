<div class="container py-4" style="max-width:960px;margin:auto;">
    <h4 class="fw-bold text-white mb-4"><i class="bi bi-graph-up-arrow text-success me-2"></i>Kết quả học tập của tôi</h4>

    <!-- Bảng tổng hợp kết quả thống kê -->
    <h5 class="text-white-50 mb-3"><i class="bi bi-pie-chart me-1 text-success"></i>Bảng tổng hợp kết quả học tập</h5>
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

    <!-- Tiến độ các khóa học -->
    <h5 class="text-white-50 mb-3"><i class="bi bi-journal-bookmark me-1 text-success"></i>Tiến độ học tập</h5>
    <div class="row g-3 mb-5">
        <?php foreach($courses as $c): ?>
        <div class="col-md-6">
            <div class="card border-0 bg-dark text-white shadow-sm" style="border:1px solid #333!important;">
                <div class="card-body p-4">
                    <h6 class="text-white fw-bold mb-3"><?php echo htmlspecialchars($c['title']); ?></h6>
                    <?php $pct = $c['total_lessons'] > 0 ? round($c['done_lessons']/$c['total_lessons']*100) : 0; ?>
                    <div class="progress" style="height:10px;background:#111;">
                        <div class="progress-bar bg-success rounded" style="width:<?php echo $pct; ?>%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-white-50">
                        <span>Đã học: <strong><?php echo $c['done_lessons']; ?></strong> / <?php echo $c['total_lessons']; ?> bài</span>
                        <span class="text-success fw-bold"><?php echo $pct; ?>%</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Kết quả trắc nghiệm -->
    <?php if(!empty($quizResults)): ?>
    <h5 class="text-white-50 mb-3"><i class="bi bi-check-circle me-1 text-success"></i>Chi tiết Kết quả Trắc nghiệm</h5>
    <div class="card border-0 shadow-sm mb-5 bg-dark text-white" style="border: 1px solid #333 !important;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead><tr><th>Đề thi</th><th>Bài học</th><th>Điểm số</th><th>Kết quả</th><th>Ngày làm</th></tr></thead>
                <tbody>
                <?php foreach($quizResults as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['quiz_title']); ?></td>
                    <td class="text-muted small"><?php echo htmlspecialchars($r['lesson_title']); ?></td>
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

    <!-- Kết quả bài tập -->
    <?php if(!empty($asgResults)): ?>
    <h5 class="text-white-50 mb-3"><i class="bi bi-file-earmark-text me-1 text-success"></i>Chi tiết Kết quả Bài tập</h5>
    <div class="card border-0 shadow-sm bg-dark text-white" style="border: 1px solid #333 !important;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead><tr><th>Bài tập</th><th>Loại bài</th><th>Trạng thái</th><th>Điểm số</th><th>Nộp lúc</th><th>Chi tiết</th></tr></thead>
                <tbody>
                <?php foreach($asgResults as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['asgn_title']); ?></td>
                    <td><?php echo $r['type']==='essay' ? '<span class="badge bg-primary-subtle text-primary border border-primary">Tự luận</span>' : '<span class="badge bg-info-subtle text-info border border-info">Nộp file</span>'; ?></td>
                    <td><?php echo $r['status']==='graded' ? '<span class="badge bg-success-subtle text-success border border-success">Đã chấm</span>' : '<span class="badge bg-warning-subtle text-warning border border-warning">Chờ chấm</span>'; ?></td>
                    <td><?php echo $r['score'] !== null ? '<strong class="text-success">'.$r['score'].'</strong>/'.$r['max_score'] : '<span class="text-muted">—</span>'; ?></td>
                    <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($r['submitted_at'])); ?></td>
                    <td><a href="<?php echo APP_URL; ?>/assignment/result?assignment_id=<?php echo $r['assignment_id']; ?>&course_id=0" class="btn btn-sm btn-outline-light py-0.5 px-2 small">Xem bài làm</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
