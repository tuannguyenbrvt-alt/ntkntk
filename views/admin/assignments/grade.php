<div class="container py-4" style="max-width:760px;">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold"><i class="bi bi-pen text-danger me-2"></i>Chấm điểm: <strong><?php echo htmlspecialchars($sub['full_name']); ?></strong></h5>
    <?php if(!empty($_GET['from_pending'])): ?>
        <a href="<?php echo APP_URL; ?>/admin/assignments/pending" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách chờ chấm
        </a>
    <?php else: ?>
        <a href="<?php echo APP_URL; ?>/admin/assignments/submissions?assignment_id=<?php echo $sub['assignment_id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
        </a>
    <?php endif; ?>
</div>

<!-- Bai lam cua hoc vien -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <strong><?php echo htmlspecialchars($sub['asgn_title']); ?></strong>
        <span class="badge bg-secondary"><?php echo $sub['type'] === 'essay' ? '📝 Tự luận' : '📁 Nộp file'; ?></span>
    </div>
    <div class="card-body">
        <?php if($sub['type'] === 'essay'): ?>
            <!-- Hien thi bai tu luan -->
            <div class="p-3 bg-light rounded" style="white-space:pre-wrap; min-height:120px; font-size:1rem; line-height:1.7;">
                <?php echo htmlspecialchars($sub['content'] ?? '(Chưa có nội dung)'); ?>
            </div>

        <?php else: ?>
            <!-- Hien thi file nop -->
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <i class="bi bi-file-earmark-arrow-down text-primary" style="font-size:2rem;"></i>
                <div>
                    <div class="fw-semibold"><?php echo htmlspecialchars($sub['file_name'] ?? 'Không rõ tên file'); ?></div>
                    <?php if(!empty($sub['file_drive_url'])): ?>
                        <?php
                        // Phan biet link Drive va link local
                        $isLocalFile = (strpos($sub['file_drive_url'], 'uploads/submissions/') !== false || strpos($sub['file_drive_url'], APP_URL . '/uploads/') !== false);
                        ?>
                        <?php if($isLocalFile): ?>
                            <a href="<?php echo htmlspecialchars($sub['file_drive_url']); ?>" target="_blank" class="btn btn-outline-success mt-2" download>
                                <i class="bi bi-download me-2"></i>Tải file về máy (Lưu trên server)
                            </a>
                            <div class="text-muted small mt-1"><i class="bi bi-info-circle me-1"></i>File được lưu trên server (chưa đồng bộ Drive)</div>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($sub['file_drive_url']); ?>" target="_blank" class="btn btn-outline-primary mt-2">
                                <i class="bi bi-google me-2"></i>Mở / Tải file từ Google Drive
                            </a>
                            <div class="text-muted small mt-1"><i class="bi bi-check-circle text-success me-1"></i>File đã lưu trên Google Drive</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning mt-2 mb-0 py-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>Chưa có link file. Bài nộp có thể bị lỗi khi upload.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="text-muted small mt-3">
            <i class="bi bi-clock me-1"></i>Nộp lúc: <?php echo date('d/m/Y H:i', strtotime($sub['submitted_at'])); ?>
            &nbsp;|&nbsp; SĐT: <?php echo htmlspecialchars($sub['phone'] ?? '—'); ?>
        </div>
    </div>
</div>

<!-- Form cham diem -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-warning text-dark fw-bold"><i class="bi bi-star me-2"></i>Chấm điểm &amp; Nhận xét</div>
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/admin/assignments/storeGrade">
            <input type="hidden" name="sub_id" value="<?php echo $sub['id']; ?>">
            <input type="hidden" name="assignment_id" value="<?php echo $sub['assignment_id']; ?>">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <?php if(!empty($_GET['from_pending'])): ?>
                <input type="hidden" name="from_pending" value="1">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Điểm số (tối đa: <strong><?php echo $sub['max_score']; ?></strong>)</label>
                    <input type="number" name="score" class="form-control form-control-lg" min="0" max="<?php echo $sub['max_score']; ?>" step="0.5" value="<?php echo $sub['score'] ?? ''; ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nhận xét của giáo viên</label>
                    <textarea name="feedback" class="form-control" rows="4" placeholder="Bài làm tốt ở điểm... Cần cải thiện ở điểm..."><?php echo htmlspecialchars($sub['feedback'] ?? ''); ?></textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                    <i class="bi bi-save me-1"></i>Lưu điểm &amp; Nhận xét
                </button>
                <?php if($sub['status'] === 'graded'): ?>
                    <span class="ms-3 text-success"><i class="bi bi-check-circle-fill me-1"></i>Đã chấm trước đó: <?php echo $sub['score']; ?> điểm</span>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
</div>
