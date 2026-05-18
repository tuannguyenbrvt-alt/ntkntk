<div class="container-fluid py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold"><i class="bi bi-inbox text-warning me-2"></i>Bai nop: <?php echo htmlspecialchars($assignment['title']); ?></h5>
    <a href="<?php echo APP_URL; ?>/admin/courses/builder?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lai</a>
</div>
<?php
$pending = 0;
foreach ($submissions as $s) { if ($s['status'] === 'pending') $pending++; }
?>
<?php if($pending > 0): ?><div class="alert alert-warning"><i class="bi bi-bell-fill me-2"></i>Co <strong><?php echo $pending; ?></strong> bai chua duoc cham diem!</div><?php endif; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if(empty($submissions)): ?>
            <div class="alert alert-info">Chua co hoc vien nao nop bai.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>Hoc vien</th><th>SoDT</th><th>Loai</th><th>Trang thai</th><th>Diem</th><th>Nop luc</th><th>Thaotac</th></tr></thead>
                <tbody>
                <?php foreach($submissions as $s): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($s['full_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($s['phone'] ?? '—'); ?></td>
                    <td><?php echo $assignment['type'] === 'essay' ? '<span class="badge bg-primary">Tu luan</span>' : '<span class="badge bg-info">File</span>'; ?></td>
                    <td><?php echo $s['status'] === 'graded' ? '<span class="badge bg-success">Da cham</span>' : '<span class="badge bg-warning text-dark">Chua cham</span>'; ?></td>
                    <td><?php echo $s['score'] !== null ? $s['score'].'/'.$assignment['max_score'] : '—'; ?></td>
                    <td><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($s['submitted_at'])); ?></small></td>
                    <td><a href="<?php echo APP_URL; ?>/admin/assignments/grade?sub_id=<?php echo $s['id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-primary">Xem & Cham</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
