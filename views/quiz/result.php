<?php
$pct    = $attempt['score'];
$passed = $attempt['passed'];
$correct_count = 0;
foreach ($answers as $a) { if ($a['is_correct']) $correct_count++; }
$total_count = count($answers);
?>
<div class="container py-5" style="max-width:760px; margin:auto;">

    <!-- Ket qua tong quat -->
    <div class="card border-0 shadow text-center mb-4"
         style="background:<?php echo $passed ? '#0d2e0d' : '#2e0d0d'; ?>;
                border:2px solid <?php echo $passed ? '#28a745' : '#dc3545'; ?> !important;">
        <div class="card-body py-5">
            <div style="font-size:4rem;"><?php echo $passed ? '🎉' : '😔'; ?></div>
            <h2 class="fw-bold text-white mt-3"><?php echo $passed ? 'Chúc mừng! Bạn đã qua!' : 'Chưa đạt — Thử lại nhé!'; ?></h2>
            <div class="mt-3" style="font-size:3rem; font-weight:700; color:<?php echo $passed ? '#4caf50' : '#f44336'; ?>;">
                <?php echo $pct; ?>%
            </div>
            <p class="text-white-50 mt-2">Điểm cần đạt: <?php echo $attempt['pass_score']; ?>%</p>
            <p class="text-white-50">Đúng <strong class="text-white"><?php echo $correct_count; ?></strong> / <?php echo $total_count; ?> câu đã trả lời</p>
        </div>
    </div>

    <!-- Chi tiet tung cau -->
    <?php if(!empty($answers)): ?>
    <h5 class="fw-bold text-dark mb-3">Chi tiết bài làm:</h5>
    <?php foreach($answers as $i => $a): ?>
    <div class="card mb-3 border-0 shadow-sm"
         style="border-left:4px solid <?php echo $a['is_correct'] ? '#28a745' : '#dc3545'; ?> !important;">
        <div class="card-body">
            <div class="d-flex gap-2 align-items-start">
                <span class="badge <?php echo $a['is_correct'] ? 'bg-success' : 'bg-danger'; ?> mt-1">
                    <?php echo $a['is_correct'] ? '✓' : '✗'; ?>
                </span>
                <div class="w-100">
                    <div class="fw-semibold mb-2 text-dark"><?php echo $a['question_text']; ?></div>
                    <?php if($a['selected_text']): ?>
                        <div class="small <?php echo $a['is_correct'] ? 'text-success' : 'text-danger'; ?> d-flex align-items-center gap-1 flex-wrap">
                            <i class="bi bi-<?php echo $a['is_correct'] ? 'check-circle' : 'x-circle'; ?> me-1"></i>
                            <span>Bạn chọn: </span> <span><?php echo $a['selected_text']; ?></span>
                        </div>
                    <?php else: ?>
                        <div class="small text-warning">
                            <i class="bi bi-dash-circle me-1"></i>Bỏ qua
                        </div>
                    <?php endif; ?>
                    <?php if(!$a['is_correct'] && !empty($a['correct_text'])): ?>
                        <div class="small text-success mt-1 d-flex align-items-center gap-1 flex-wrap">
                            <i class="bi bi-check2-circle me-1"></i>
                            <span>Đáp án đúng: </span> <strong><?php echo $a['correct_text']; ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- Nut hanh dong -->
    <div class="text-center mt-4 d-flex gap-3 justify-content-center flex-wrap">
        <?php if($course_id): ?>
        <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $attempt['lesson_id']; ?>"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Về bài học
        </a>
        <?php endif; ?>
        <a href="<?php echo APP_URL; ?>/quiz/take?quiz_id=<?php echo $attempt['quiz_id']; ?>"
           class="btn btn-warning fw-bold">
            <i class="bi bi-arrow-repeat me-1"></i>Làm lại bài
        </a>
        <a href="<?php echo APP_URL; ?>/progress" class="btn btn-outline-primary">
            <i class="bi bi-bar-chart me-1"></i>Xem tất cả kết quả
        </a>
    </div>
</div>
