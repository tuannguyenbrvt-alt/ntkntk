<?php
$pct    = $attempt['score'];
$passed = $attempt['passed'];
$correct_count = 0;
foreach ($resultDetails as $q) { if ($q['is_correct']) $correct_count++; }
$total_count = count($resultDetails);
?>

<div class="row">
    <!-- Cột trái: Thông tin tổng quan lượt làm bài -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white p-4 fw-bold border-bottom-0 fs-5 pb-2">
                <i class="bi bi-info-circle text-primary me-2"></i> Thông tin lượt làm bài
            </div>
            <div class="card-body pt-0">
                <div class="text-center py-3 mb-3">
                    <div style="font-size: 3rem;"><?php echo $passed ? '🎉' : '😔'; ?></div>
                    <h5 class="fw-bold mt-2 mb-1"><?php echo htmlspecialchars($attempt['student_name']); ?></h5>
                    <span class="badge bg-secondary rounded-pill small">Học viên</span>
                </div>
                
                <hr class="my-3 opacity-50">

                <div class="mb-3">
                    <div class="text-muted small">Đề trắc nghiệm:</div>
                    <strong class="text-dark d-block mt-1"><?php echo htmlspecialchars($attempt['quiz_title']); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Điểm đạt được:</span>
                    <span class="fw-bold fs-5 <?php echo $passed ? 'text-success' : 'text-danger'; ?>"><?php echo $pct; ?>%</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Điểm yêu cầu:</span>
                    <strong class="text-dark"><?php echo $attempt['pass_score']; ?>%</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Số câu đúng:</span>
                    <strong class="text-dark"><span class="text-success"><?php echo $correct_count; ?></span> / <?php echo $total_count; ?> câu</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Trạng thái:</span>
                    <?php echo $passed 
                        ? '<span class="badge bg-success rounded-pill px-3 py-1"><i class="bi bi-check-circle me-1"></i> ĐẠT</span>' 
                        : '<span class="badge bg-danger rounded-pill px-3 py-1"><i class="bi bi-x-circle me-1"></i> CHƯA ĐẠT</span>'; 
                    ?>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Thời gian nộp:</span>
                    <small class="text-muted text-end"><?php echo date('d/m/Y H:i', strtotime($attempt['submitted_at'])); ?></small>
                </div>

                <hr class="my-4 opacity-50">

                <a href="<?php echo APP_URL; ?>/admin/students/show?id=<?php echo $attempt['student_id']; ?>" class="btn btn-outline-secondary w-100 rounded-pill fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại hồ sơ học viên
                </a>
            </div>
        </div>
    </div>

    <!-- Cột phải: Chi tiết từng câu trả lời -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white p-4 fw-bold border-bottom-0 fs-5 pb-2">
                <i class="bi bi-list-check text-success me-2"></i> Chi tiết các câu hỏi đã làm
            </div>
            <div class="card-body p-4 pt-0">
                <?php if (empty($resultDetails)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-exclamation-circle display-4 opacity-25 d-block mb-3"></i>
                        Không có dữ liệu câu trả lời chi tiết cho lượt làm bài này.
                    </div>
                <?php else: ?>
                    <?php foreach ($resultDetails as $i => $q): ?>
                        <div class="card mb-4 border-0 rounded-3 shadow-sm bg-light bg-opacity-50" 
                             style="border-left: 5px solid <?php echo $q['is_correct'] ? '#28a745' : '#dc3545'; ?> !important;">
                            <div class="card-body p-4">
                                <div class="d-flex gap-2 align-items-start mb-3">
                                    <span class="badge <?php echo $q['is_correct'] ? 'bg-success' : 'bg-danger'; ?> rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; min-width: 28px;">
                                        <i class="bi bi-<?php echo $q['is_correct'] ? 'check-lg' : 'x-lg'; ?> text-white fs-6"></i>
                                    </span>
                                    <div class="w-100">
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                            <span class="badge bg-secondary rounded-pill">Câu <?php echo $i + 1; ?></span>
                                            <span class="badge bg-info-subtle text-info border border-info rounded-pill" style="font-size: 0.75rem;">
                                                <?php echo $q['question_type'] === 'multiple' ? 'Chọn nhiều đáp án' : 'Chọn một đáp án'; ?>
                                            </span>
                                        </div>
                                        <div class="fw-bold text-dark fs-6 mt-1"><?php echo $q['question_text']; ?></div>
                                    </div>
                                </div>

                                <div class="options-list ps-4 ms-2">
                                    <?php foreach ($q['options'] as $opt): 
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
                                            $text_color = 'text-danger fw-bold';
                                        } elseif (!$selected && $correct) {
                                            $border_style = 'border: 2px dashed #28a745;';
                                            $bg_style = 'background-color: #f1f8e9;';
                                            $text_color = 'text-success';
                                        } else {
                                            $border_style = 'border: 1px solid #dee2e6;';
                                            $bg_style = 'background-color: #ffffff;';
                                        }
                                    ?>
                                    <div class="p-3 mb-2 rounded-3 d-flex align-items-center justify-content-between flex-wrap g-2" style="<?php echo $border_style . ' ' . $bg_style; ?>">
                                        <div class="d-flex align-items-center gap-3 flex-grow-1">
                                            <div class="d-flex align-items-center">
                                                <?php if($q['question_type'] === 'multiple'): ?>
                                                    <i class="bi bi-<?php echo $selected ? 'check-square-fill text-primary' : 'square text-muted'; ?> fs-5"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-<?php echo $selected ? 'record-circle-fill text-primary' : 'circle text-muted'; ?> fs-5"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="<?php echo $text_color; ?>">
                                                <?php echo $opt['option_text']; ?>
                                            </div>
                                        </div>
                                        <div class="ms-2 d-flex gap-2 flex-wrap">
                                            <?php if ($correct): ?>
                                                <span class="badge bg-success text-white px-2 py-1.5 rounded-pill"><i class="bi bi-check-lg me-1"></i>Đáp án đúng</span>
                                            <?php endif; ?>
                                            <?php if ($selected && !$correct): ?>
                                                <span class="badge bg-danger text-white px-2 py-1.5 rounded-pill"><i class="bi bi-x-lg me-1"></i>Học viên chọn sai</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
