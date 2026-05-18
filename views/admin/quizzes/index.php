<?php $course_id = $course_id ?? 0; ?>
<div class="container-fluid py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-trophy text-warning me-2"></i>Quan ly De Trac Nghiem</h4>
        <small class="text-muted">Bai hoc: <?php echo htmlspecialchars($lesson['title'] ?? ''); ?></small>
    </div>
    <div>
        <a href="<?php echo APP_URL; ?>/admin/courses/builder?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-sm me-2"><i class="bi bi-arrow-left me-1"></i>Quay lai de cuong</a>
        <a href="<?php echo APP_URL; ?>/admin/quizzes/create?lesson_id=<?php echo $lesson['id'] ?? 0; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Tao de moi</a>
    </div>
</div>
<?php if(empty($quizzes)): ?>
    <div class="alert alert-info">Chua co de trac nghiem nao. Hay tao de moi!</div>
<?php else: ?>
    <div class="row g-3">
    <?php foreach($quizzes as $q): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold"><?php echo htmlspecialchars($q['title']); ?></h5>
                    <div class="d-flex gap-3 text-muted small mb-3">
                        <span><i class="bi bi-clock me-1"></i><?php echo $q['time_limit_minutes'] > 0 ? $q['time_limit_minutes'].' phut' : 'Khong gioi han'; ?></span>
                        <span><i class="bi bi-bar-chart me-1"></i>Diem qua: <?php echo $q['pass_score']; ?>%</span>
                        <span><i class="bi bi-arrow-repeat me-1"></i><?php echo $q['max_attempts'] > 0 ? $q['max_attempts'].' luot' : 'Khong gioi han'; ?></span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?php echo APP_URL; ?>/admin/quizzes/questions?quiz_id=<?php echo $q['id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-list-check me-1"></i>Quan ly cau hoi</a>
                        <a href="<?php echo APP_URL; ?>/admin/quizzes/results?quiz_id=<?php echo $q['id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-bar-chart me-1"></i>Xem ket qua</a>
                        <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/delete" class="d-inline" onsubmit="return confirm('Xoa de nay?')">
                            <input type="hidden" name="id" value="<?php echo $q['id']; ?>">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <input type="hidden" name="lesson_id" value="<?php echo $lesson['id'] ?? 0; ?>">
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
</div>
