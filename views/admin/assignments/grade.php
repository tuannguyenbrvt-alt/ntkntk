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
            <?php if(empty($subFiles)): ?>
                <div class="alert alert-warning mt-2 mb-0 py-2">
                    <i class="bi bi-exclamation-triangle me-2"></i>Chưa có file bài làm nào được nộp thành công.
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach($subFiles as $index => $file): ?>
                        <div class="p-3 border rounded bg-white shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-secondary rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;"><?php echo $index + 1; ?></span>
                                <i class="bi bi-file-earmark-arrow-down text-primary" style="font-size:1.8rem;"></i>
                                <div>
                                    <div class="fw-semibold text-dark"><?php echo htmlspecialchars($file['file_name'] ?? 'Không rõ tên file'); ?></div>
                                    <div class="text-muted small">Nộp lúc: <?php echo date('d/m/Y H:i', strtotime($file['created_at'])); ?></div>
                                    <?php if($file['status'] === 'graded'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success border-opacity-25 mt-1"><i class="bi bi-check-circle me-1"></i>Đã chấm: <?php echo $file['score']; ?> đ</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25 mt-1"><i class="bi bi-hourglass me-1"></i>Chưa chấm</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <?php if($file['file_drive_id'] === 'error'): ?>
                                    <div class="text-danger small mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i>Lỗi Google Drive</div>
                                    <div class="text-muted mb-2" style="font-size:0.8rem; max-width:300px;"><?php echo htmlspecialchars($file['content'] ?? 'Không xác định được nguyên nhân.'); ?></div>
                                <?php elseif(!empty($file['file_drive_url'])): ?>
                                    <?php
                                    $isLocalFile = (strpos($file['file_drive_url'], 'uploads/submissions/') !== false || strpos($file['file_drive_url'], APP_URL . '/uploads/') !== false);
                                    ?>
                                    <?php if($isLocalFile): ?>
                                        <a href="<?php echo htmlspecialchars($file['file_drive_url']); ?>" target="_blank" class="btn btn-sm btn-outline-success" download>
                                            <i class="bi bi-download me-1"></i>Tải về (Server)
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($file['file_drive_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-google me-1"></i>Mở Drive
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

            <?php if($sub['type'] === 'file' && !empty($subFiles)): ?>
                <div class="mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary"><i class="bi bi-files me-2"></i>Chi tiết điểm và nhận xét cho từng file:</h6>
                    <?php foreach($subFiles as $index => $file): ?>
                        <div class="p-3 bg-light rounded border mb-3">
                            <div class="fw-bold mb-2 text-dark"><i class="bi bi-file-earmark me-1 text-primary"></i>File <?php echo $index + 1; ?>: <?php echo htmlspecialchars($file['file_name']); ?></div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Điểm số *</label>
                                    <input type="number" name="file_scores[<?php echo $file['id']; ?>]" class="form-control file-score-input" min="0" max="<?php echo $sub['max_score']; ?>" step="0.5" value="<?php echo $file['score'] !== null ? $file['score'] : ''; ?>" required>
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label small fw-semibold">Nhận xét riêng cho file này</label>
                                    <input type="text" name="file_feedbacks[<?php echo $file['id']; ?>]" class="form-control" placeholder="Nhận xét riêng câu này..." value="<?php echo htmlspecialchars($file['feedback'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold"><?php echo $sub['type'] === 'file' ? 'Tổng điểm gợi ý' : 'Điểm số'; ?> (tối đa: <strong><?php echo $sub['max_score']; ?></strong>)</label>
                    <input type="number" name="score" class="form-control form-control-lg" min="0" max="<?php echo $sub['max_score']; ?>" step="0.5" value="<?php echo $sub['score'] ?? ''; ?>" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nhận xét chung của giáo viên</label>
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

<?php if($sub['type'] === 'file' && !empty($subFiles)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scoreInputs = document.querySelectorAll('.file-score-input');
    const overallScoreInput = document.querySelector('input[name="score"]');
    const maxScore = parseFloat(<?php echo $sub['max_score']; ?>);

    function updateOverallScore() {
        let total = 0;
        scoreInputs.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) {
                total += val;
            }
        });
        if (total > maxScore) {
            total = maxScore;
        }
        overallScoreInput.value = total;
    }

    // Neu chua co diem tong, tu dong tinh luon khi load trang
    if (overallScoreInput.value === '') {
        updateOverallScore();
    }

    scoreInputs.forEach(input => {
        input.addEventListener('input', updateOverallScore);
    });
});
</script>
<?php endif; ?>
