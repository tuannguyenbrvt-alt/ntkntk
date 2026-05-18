<div class="container py-4" style="max-width:900px;margin:auto;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-white fw-bold mb-0"><i class="bi bi-person-badge me-2 text-info"></i><?php echo htmlspecialchars($user['full_name']); ?></h4>
            <small class="text-muted">SoDT: <?php echo htmlspecialchars($user['phone']); ?></small>
        </div>
        <a href="<?php echo APP_URL; ?>/progress/lookup" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Tra cuu lai</a>
    </div>

    <?php if(empty($quizResults) && empty($asgResults)): ?>
        <div class="alert alert-info">Chua co ket qua bai tap nao.</div>
    <?php endif; ?>

    <?php if(!empty($quizResults)): ?>
    <h5 class="text-white-50 mb-3">Ket qua Trac nghiem</h5>
    <div class="card border-0 shadow-sm mb-4" style="background:#1a1a2e;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead><tr><th>Khoa hoc</th><th>De thi</th><th>Diem</th><th>Ket qua</th><th>Ngay lam</th></tr></thead>
                <tbody>
                <?php foreach($quizResults as $r): ?>
                <tr>
                    <td class="text-muted small"><?php echo htmlspecialchars($r['course_title']); ?></td>
                    <td><?php echo htmlspecialchars($r['quiz_title']); ?></td>
                    <td><strong><?php echo $r['score']; ?>%</strong></td>
                    <td><?php echo $r['passed'] ? '<span class="badge bg-success">Dat</span>' : '<span class="badge bg-danger">Chua dat</span>'; ?></td>
                    <td class="text-muted small"><?php echo date('d/m/Y', strtotime($r['submitted_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!empty($asgResults)): ?>
    <h5 class="text-white-50 mb-3">Ket qua Bai tap</h5>
    <div class="card border-0 shadow-sm" style="background:#1a1a2e;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead><tr><th>Khoa hoc</th><th>Bai tap</th><th>Loai</th><th>Diem</th><th>Nhan xet</th><th>Ngay nop</th></tr></thead>
                <tbody>
                <?php foreach($asgResults as $r): ?>
                <tr>
                    <td class="text-muted small"><?php echo htmlspecialchars($r['course_title']); ?></td>
                    <td><?php echo htmlspecialchars($r['asgn_title']); ?></td>
                    <td><?php echo $r['type']==='essay'?'Tu luan':'File'; ?></td>
                    <td><?php echo $r['score'] !== null ? '<strong>'.$r['score'].'/'.$r['max_score'].'</strong>' : '<span class="text-muted">Chua cham</span>'; ?></td>
                    <td class="small text-muted"><?php echo $r['feedback'] ? mb_substr($r['feedback'],0,60).'...' : '—'; ?></td>
                    <td class="text-muted small"><?php echo date('d/m/Y', strtotime($r['submitted_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
