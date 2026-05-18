<div class="container py-5" style="max-width:700px;margin:auto;">
    <?php $pct = $attempt['score']; $passed = $attempt['passed']; ?>
    <div class="card border-0 shadow text-center mb-4" style="background:<?php echo $passed ? '#1a2e1a' : '#2e1a1a'; ?>;border:2px solid <?php echo $passed ? '#28a745' : '#dc3545'; ?> !important;">
        <div class="card-body py-5">
            <div style="font-size:4rem;"><?php echo $passed ? '🎉' : '😔'; ?></div>
            <h2 class="fw-bold text-white mt-3"><?php echo $passed ? 'Chuc mung! Ban da qua!' : 'Chua dat — Thu lai nhe!'; ?></h2>
            <div class="display-4 fw-bold mt-3" style="color:<?php echo $passed ? '#28a745' : '#dc3545'; ?>"><?php echo $pct; ?>%</div>
            <p class="text-muted mt-2">Diem can dat: <?php echo $attempt['pass_score']; ?>%</p>
            <?php $correct = count(array_filter($answers, fn($a) => $a['is_correct'])); ?>
            <p class="text-white-50">Dung <?php echo $correct; ?> / <?php echo count($answers); ?> cau</p>
        </div>
    </div>

    <!-- Chi tiet tung cau -->
    <h5 class="fw-bold text-white mb-3">Chi tiet bai lam:</h5>
    <?php foreach($answers as $i => $a): ?>
    <div class="card mb-3 border-0" style="background:<?php echo $a['is_correct'] ? '#1a2e1a' : '#2e1a1a'; ?>">
        <div class="card-body">
            <div class="d-flex gap-2 align-items-start">
                <span class="badge <?php echo $a['is_correct'] ? 'bg-success' : 'bg-danger'; ?> mt-1">Cau <?php echo $i+1; ?></span>
                <div>
                    <p class="text-white mb-2"><?php echo htmlspecialchars($a['question_text']); ?></p>
                    <?php if($a['selected_text']): ?>
                        <div class="small <?php echo $a['is_correct'] ? 'text-success' : 'text-danger'; ?>">
                            <i class="bi bi-<?php echo $a['is_correct'] ? 'check-circle' : 'x-circle'; ?> me-1"></i>
                            Ban chon: <?php echo htmlspecialchars($a['selected_text']); ?>
                        </div>
                    <?php else: ?>
                        <div class="small text-warning"><i class="bi bi-dash-circle me-1"></i>Bo qua</div>
                    <?php endif; ?>
                    <?php if(!$a['is_correct'] && $a['correct_text']): ?>
                        <div class="small text-success mt-1"><i class="bi bi-check2-circle me-1"></i>Dap an dung: <?php echo htmlspecialchars($a['correct_text']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="text-center mt-4">
        <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $attempt['lesson_id']; ?>" class="btn btn-outline-light me-2"><i class="bi bi-arrow-left me-1"></i>Ve bai hoc</a>
        <a href="<?php echo APP_URL; ?>/quiz/take?quiz_id=<?php echo $attempt['quiz_id']; ?>" class="btn btn-warning"><i class="bi bi-arrow-repeat me-1"></i>Lam lai</a>
    </div>
</div>
