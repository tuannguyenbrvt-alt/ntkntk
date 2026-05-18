<div class="container py-5" style="max-width:900px;margin:auto;">
    <h4 class="fw-bold text-white mb-4"><i class="bi bi-graph-up-arrow text-success me-2"></i>Ket qua hoc tap cua toi</h4>

    <!-- Tien do cac khoa hoc -->
    <h5 class="text-white-50 mb-3">Tien do hoc tap</h5>
    <div class="row g-3 mb-5">
        <?php foreach($courses as $c): ?>
        <div class="col-md-6">
            <div class="card border-0" style="background:#1a1a2e;border-color:#2d2d44!important;">
                <div class="card-body">
                    <h6 class="text-white fw-bold"><?php echo htmlspecialchars($c['title']); ?></h6>
                    <?php $pct = $c['total_lessons'] > 0 ? round($c['done_lessons']/$c['total_lessons']*100) : 0; ?>
                    <div class="progress mt-2" style="height:8px;background:#2d2d44;">
                        <div class="progress-bar bg-success" style="width:<?php echo $pct; ?>%"></div>
                    </div>
                    <small class="text-muted mt-1 d-block"><?php echo $c['done_lessons']; ?>/<?php echo $c['total_lessons']; ?> bai — <?php echo $pct; ?>%</small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Ket qua trac nghiem -->
    <?php if(!empty($quizResults)): ?>
    <h5 class="text-white-50 mb-3">Ket qua trac nghiem</h5>
    <div class="card border-0 shadow-sm mb-5" style="background:#1a1a2e;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead><tr><th>De</th><th>Bai hoc</th><th>Diem</th><th>Ket qua</th><th>Thoi gian</th></tr></thead>
                <tbody>
                <?php foreach($quizResults as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['quiz_title']); ?></td>
                    <td class="text-muted small"><?php echo htmlspecialchars($r['lesson_title']); ?></td>
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

    <!-- Ket qua bai tap -->
    <?php if(!empty($asgResults)): ?>
    <h5 class="text-white-50 mb-3">Ket qua bai tap</h5>
    <div class="card border-0 shadow-sm" style="background:#1a1a2e;">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0">
                <thead><tr><th>Bai tap</th><th>Loai</th><th>Trang thai</th><th>Diem</th><th>Nop luc</th><th>Xem</th></tr></thead>
                <tbody>
                <?php foreach($asgResults as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['asgn_title']); ?></td>
                    <td><?php echo $r['type']==='essay' ? '<span class="badge bg-primary">Tu luan</span>' : '<span class="badge bg-info">File</span>'; ?></td>
                    <td><?php echo $r['status']==='graded' ? '<span class="badge bg-success">Da cham</span>' : '<span class="badge bg-warning text-dark">Cho cham</span>'; ?></td>
                    <td><?php echo $r['score'] !== null ? $r['score'].'/'.$r['max_score'] : '—'; ?></td>
                    <td class="text-muted small"><?php echo date('d/m/Y', strtotime($r['submitted_at'])); ?></td>
                    <td><a href="<?php echo APP_URL; ?>/assignment/result?assignment_id=<?php echo $r['assignment_id']; ?>&course_id=0" class="btn btn-sm btn-outline-light">Xem</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
