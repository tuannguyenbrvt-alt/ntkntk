<div class="container py-4" style="max-width:760px;">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold"><i class="bi bi-pen text-danger me-2"></i>Cham diem: <?php echo htmlspecialchars($sub['full_name']); ?></h5>
    <a href="<?php echo APP_URL; ?>/admin/assignments/submissions?assignment_id=<?php echo $sub['assignment_id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lai</a>
</div>

<!-- Bai lam cua hoc vien -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-light"><strong><?php echo htmlspecialchars($sub['asgn_title']); ?></strong> <span class="badge bg-secondary ms-2"><?php echo $sub['type'] === 'essay' ? 'Tu luan' : 'Nop file'; ?></span></div>
    <div class="card-body">
        <?php if($sub['type'] === 'essay'): ?>
            <div class="p-3 bg-light rounded" style="white-space:pre-wrap;min-height:120px;"><?php echo htmlspecialchars($sub['content'] ?? 'Chua co noi dung'); ?></div>
        <?php else: ?>
            <?php if($sub['file_drive_url']): ?>
                <a href="<?php echo htmlspecialchars($sub['file_drive_url']); ?>" target="_blank" class="btn btn-outline-primary"><i class="bi bi-cloud-download me-2"></i>Mo/Tai file tren Google Drive: <?php echo htmlspecialchars($sub['file_name']); ?></a>
            <?php else: ?>
                <span class="text-muted">Chua co file.</span>
            <?php endif; ?>
        <?php endif; ?>
        <div class="text-muted small mt-2">Nop luc: <?php echo date('d/m/Y H:i', strtotime($sub['submitted_at'])); ?></div>
    </div>
</div>

<!-- Form cham diem -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-warning text-dark"><i class="bi bi-star me-2"></i>Cham diem & Nhan xet</div>
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/admin/assignments/storeGrade">
            <input type="hidden" name="sub_id" value="<?php echo $sub['id']; ?>">
            <input type="hidden" name="assignment_id" value="<?php echo $sub['assignment_id']; ?>">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <div class="mb-3">
                <label class="form-label fw-semibold">Diem so (toi da: <?php echo $sub['max_score']; ?>)</label>
                <input type="number" name="score" class="form-control" style="max-width:160px;" min="0" max="<?php echo $sub['max_score']; ?>" step="0.1" value="<?php echo $sub['score'] ?? ''; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nhan xet cua giao vien</label>
                <textarea name="feedback" class="form-control" rows="4" placeholder="Bai lam tot o diem... Can cai thien o diem..."><?php echo htmlspecialchars($sub['feedback'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-warning text-dark fw-bold"><i class="bi bi-save me-1"></i>Luu diem & Nhan xet</button>
        </form>
    </div>
</div>
</div>
