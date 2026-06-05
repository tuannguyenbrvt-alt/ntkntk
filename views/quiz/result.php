<?php
$pct    = $attempt['score'];
$passed = $attempt['passed'];
$correct_count = 0;
foreach ($resultDetails as $q) { if ($q['is_correct']) $correct_count++; }
$total_count = count($resultDetails);
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
            <p class="text-white-50">Đúng <strong class="text-white"><?php echo $correct_count; ?></strong> / <?php echo $total_count; ?> câu</p>
        </div>
    </div>

    <!-- Chi tiet tung cau -->
    <?php if(!empty($resultDetails)): ?>
    <h5 class="fw-bold text-dark mb-3">Chi tiết bài làm:</h5>
    <?php foreach($resultDetails as $i => $q): ?>
    <div class="card mb-3 border-0 shadow-sm"
         style="border-left:4px solid <?php echo $q['is_correct'] ? '#28a745' : '#dc3545'; ?> !important;">
        <div class="card-body">
            <div class="d-flex gap-2 align-items-start mb-3">
                <span class="badge <?php echo $q['is_correct'] ? 'bg-success' : 'bg-danger'; ?> mt-1" style="font-size: 0.9rem;">
                    <?php echo $q['is_correct'] ? '✓' : '✗'; ?>
                </span>
                <div class="w-100">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <span class="badge bg-secondary">Câu <?php echo $i+1; ?></span>
                        <span class="badge bg-info-subtle text-info border border-info" style="font-size:0.75rem;">
                            <?php echo $q['question_type'] === 'multiple' ? 'Chọn nhiều đáp án' : 'Chọn một đáp án'; ?>
                        </span>
                    </div>
                    <div class="fw-semibold text-dark fs-6 mt-1"><?php echo $q['question_text']; ?></div>
                </div>
            </div>
            
            <div class="options-list ps-4">
                <?php foreach($q['options'] as $opt): 
                    $selected = in_array((int)$opt['id'], $q['selected_option_ids']);
                    $correct = (bool)$opt['is_correct'];
                    
                    $border_style = '';
                    $bg_style = '';
                    $text_color = 'text-dark';
                    
                    if ($selected && $correct) {
                        $border_style = 'border: 2px solid #28a745;';
                        $bg_style = 'background-color: #e8f5e9;';
                        $text_color = 'text-success fw-bold';
                    } elseif ($selected && !$correct) {
                        $border_style = 'border: 2px solid #dc3545;';
                        $bg_style = 'background-color: #ffebee;';
                        $text_color = 'text-danger';
                    } elseif (!$selected && $correct) {
                        $border_style = 'border: 2px dashed #28a745;';
                        $bg_style = 'background-color: #f1f8e9;';
                        $text_color = 'text-success';
                    } else {
                        $border_style = 'border: 1px solid #dee2e6;';
                    }
                ?>
                <div class="p-2 mb-2 rounded d-flex align-items-center gap-2" style="<?php echo $border_style . ' ' . $bg_style; ?>">
                    <div class="d-flex align-items-center">
                        <?php if($q['question_type'] === 'multiple'): ?>
                            <i class="bi bi-<?php echo $selected ? 'check-square-fill' : 'square'; ?> <?php echo $selected ? 'text-primary' : 'text-muted'; ?> fs-5"></i>
                        <?php else: ?>
                            <i class="bi bi-<?php echo $selected ? 'record-circle-fill' : 'circle'; ?> <?php echo $selected ? 'text-primary' : 'text-muted'; ?> fs-5"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1 <?php echo $text_color; ?>">
                        <?php echo $opt['option_text']; ?>
                    </div>
                    <?php if($correct): ?>
                        <span class="badge bg-success text-white"><i class="bi bi-check-lg me-1"></i>Đáp án đúng</span>
                    <?php endif; ?>
                    <?php if($selected && !$correct): ?>
                        <span class="badge bg-danger text-white"><i class="bi bi-x-lg me-1"></i>Bạn chọn sai</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
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
