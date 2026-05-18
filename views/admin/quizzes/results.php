<div class="container-fluid py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold"><i class="bi bi-bar-chart text-success me-2"></i>Ket qua: <?php echo htmlspecialchars($quiz['title'] ?? ''); ?></h5>
    <a href="<?php echo APP_URL; ?>/admin/courses/builder?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lai</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if(empty($attempts)): ?>
            <div class="alert alert-info">Chua co hoc vien nao lam bai nay.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>Hoc vien</th><th>SoDT</th><th>Diem (%)</th><th>Ket qua</th><th>Thoi gian nop</th></tr></thead>
                <tbody>
                <?php foreach($attempts as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($a['phone'] ?? '—'); ?></td>
                        <td><strong><?php echo $a['score']; ?>%</strong></td>
                        <td><?php if($a['passed']): ?><span class="badge bg-success">Dat</span><?php else: ?><span class="badge bg-danger">Chua dat</span><?php endif; ?></td>
                        <td><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($a['submitted_at'])); ?></small></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
