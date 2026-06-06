<?php $course_id = $course_id ?? 0; $cid = $cid ?? 0; ?>
<div class="container-fluid py-4">
<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h5 class="fw-bold mb-0"><i class="bi bi-list-check text-primary me-2"></i><?php echo htmlspecialchars($quiz['title']); ?></h5>
    <small class="text-muted">Bài học: <?php echo htmlspecialchars($quiz['lesson_title'] ?? ''); ?></small></div>
    <div class="d-flex gap-2">
        <a href="<?php echo APP_URL; ?>/admin/quizzes/edit?id=<?php echo $quiz['id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-warning btn-sm text-white"><i class="bi bi-pencil me-1"></i>Sửa thông tin đề</a>
        <a href="<?php echo APP_URL; ?>/admin/courses/builder?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>
<div class="row g-4">
    <!-- Câu hỏi trong đề -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="bi bi-card-checklist me-2"></i>Câu hỏi trong đề (<?php echo count($inQuizQuestions); ?> câu)</span>
            </div>
            <div class="card-body p-0">
                <?php if(empty($inQuizQuestions)): ?>
                    <div class="p-4 text-center text-muted">Chưa có câu hỏi nào. Thêm từ bên phải.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                    <?php foreach($inQuizQuestions as $i => $q): ?>
                        <div class="list-group-item py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 pe-3">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="badge bg-primary mt-1"><?php echo $i+1; ?></span>
                                        <span class="badge bg-info mt-1" style="font-size: 0.75rem;">
                                            <?php echo ($q['question_type'] ?? 'single') === 'multiple' ? 'Chọn nhiều' : 'Chọn một'; ?>
                                        </span>
                                        <div class="question-text-render text-dark fw-semibold"><?php echo $q['question_text']; ?></div>
                                    </div>
                                    <div class="mt-2 ps-4 small text-muted">
                                        <?php if(!empty($q['options'])): ?>
                                            <?php foreach($q['options'] as $j => $opt): ?>
                                                <div class="d-flex align-items-center gap-2 mb-1 <?php echo $opt['is_correct'] ? 'text-success fw-bold' : ''; ?>">
                                                    <span class="badge <?php echo $opt['is_correct'] ? 'bg-success' : 'bg-secondary'; ?>" style="font-size:0.7rem;"><?php echo chr(65+$j); ?></span>
                                                    <div><?php echo $opt['option_text']; ?></div>
                                                    <?php if($opt['is_correct']): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success" style="font-size:0.6rem;">Đáp án đúng</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex gap-1 align-items-center">
                                    <!-- Button kích hoạt Modal Sửa -->
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editQuestionModal<?php echo $q['qb_id']; ?>"><i class="bi bi-pencil"></i></button>
                                    
                                    <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/removeQuestion" class="d-inline m-0" onsubmit="return confirm('Xóa khỏi đề?')">
                                        <input type="hidden" name="qq_id" value="<?php echo $q['qq_id']; ?>">
                                        <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Sửa Câu Hỏi -->
                        <div class="modal fade" id="editQuestionModal<?php echo $q['qb_id']; ?>" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/updateQuestion">
                                        <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                        <input type="hidden" name="qb_id" value="<?php echo $q['qb_id']; ?>">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Sửa câu hỏi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start text-dark">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nội dung câu hỏi *</label>
                                                <textarea name="question_text" class="form-control tinymce-editor" rows="3" required><?php echo htmlspecialchars($q['question_text']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Loại câu hỏi *</label>
                                                <select name="question_type" class="form-select" onchange="toggleEditQuestionType(<?php echo $q['qb_id']; ?>, this.value)">
                                                    <option value="single" <?php echo ($q['question_type'] ?? 'single') === 'single' ? 'selected' : ''; ?>>Một đáp án đúng (Single Choice)</option>
                                                    <option value="multiple" <?php echo ($q['question_type'] ?? 'single') === 'multiple' ? 'selected' : ''; ?>>Nhiều đáp án đúng (Multiple Choice)</option>
                                                </select>
                                            </div>
                                            <label class="form-label fw-semibold mb-2">Các phương án (chọn đáp án đúng tương ứng)</label>
                                            
                                            <?php 
                                            $qtype = $q['question_type'] ?? 'single';
                                            for($j=0; $j<4; $j++): 
                                                $opt = $q['options'][$j] ?? null;
                                                $is_correct = $opt ? $opt['is_correct'] : ($j === 0 ? 1 : 0);
                                                $opt_text = $opt ? $opt['option_text'] : '';
                                            ?>
                                            <div class="mb-3 border p-2 rounded bg-light bg-opacity-50">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <input type="radio" 
                                                           name="correct_single" 
                                                           value="<?php echo $j; ?>" 
                                                           <?php echo $is_correct ? 'checked' : ''; ?> 
                                                           class="correct-single-<?php echo $q['qb_id']; ?>" 
                                                           style="display: <?php echo $qtype === 'single' ? 'inline-block' : 'none'; ?>;" 
                                                           id="correct_single_<?php echo $q['qb_id']; ?>_<?php echo $j; ?>">
                                                    <input type="checkbox" 
                                                           name="correct_multiple[]" 
                                                           value="<?php echo $j; ?>" 
                                                           <?php echo $is_correct ? 'checked' : ''; ?> 
                                                           class="correct-multiple-<?php echo $q['qb_id']; ?>" 
                                                           style="display: <?php echo $qtype === 'multiple' ? 'inline-block' : 'none'; ?>;" 
                                                           id="correct_multiple_<?php echo $q['qb_id']; ?>_<?php echo $j; ?>">
                                                    <label class="form-label fw-semibold mb-0" for="correct_single_<?php echo $q['qb_id']; ?>_<?php echo $j; ?>">Phương án <?php echo chr(65+$j); ?></label>
                                                </div>
                                                <textarea name="options[]" class="form-control tinymce-editor-simple" rows="2" placeholder="Nhập nội dung phương án <?php echo chr(65+$j); ?>"><?php echo htmlspecialchars($opt_text); ?></textarea>
                                            </div>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Lưu thay đổi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Panel thêm câu hỏi -->
    <div class="col-lg-5">
        <!-- Thêm câu hỏi mới vào ngân hàng & đề -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white"><i class="bi bi-plus-circle me-2"></i>Thêm câu hỏi mới</div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/storeQuestion">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <input type="hidden" name="cid" value="<?php echo $cid; ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nội dung câu hỏi *</label>
                        <textarea name="question_text" class="form-control tinymce-editor" rows="2" placeholder="Nhập nội dung câu hỏi trắc nghiệm..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loại câu hỏi *</label>
                        <select name="question_type" class="form-select" id="new_question_type" onchange="toggleNewQuestionType(this.value)">
                            <option value="single">Một đáp án đúng (Single Choice)</option>
                            <option value="multiple">Nhiều đáp án đúng (Multiple Choice)</option>
                        </select>
                    </div>
                    <label class="form-label fw-semibold mb-2">Các phương án (chọn đáp án đúng tương ứng)</label>
                    <?php for($i=0; $i<4; $i++): ?>
                    <div class="mb-3 border p-2 rounded bg-light bg-opacity-50">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <input type="radio" 
                                   name="correct_single" 
                                   value="<?php echo $i; ?>" 
                                   <?php echo $i===0?'checked':''; ?> 
                                   class="new-correct-single" 
                                   id="new_correct_single_<?php echo $i; ?>">
                            <input type="checkbox" 
                                   name="correct_multiple[]" 
                                   value="<?php echo $i; ?>" 
                                   class="new-correct-multiple" 
                                   style="display: none;" 
                                   id="new_correct_multiple_<?php echo $i; ?>">
                            <label class="form-label fw-semibold mb-0" for="new_correct_single_<?php echo $i; ?>">Phương án <?php echo chr(65+$i); ?></label>
                        </div>
                        <textarea name="options[]" class="form-control tinymce-editor-simple" rows="2" placeholder="Nhập nội dung phương án <?php echo chr(65+$i); ?>"></textarea>
                    </div>
                    <?php endfor; ?>
                    <button type="submit" class="btn btn-success btn-sm w-100 mt-2"><i class="bi bi-plus me-1"></i>Thêm câu hỏi</button>
                </form>
            </div>
        </div>

        <!-- Thêm từ ngân hàng -->
        <?php if(!empty($bankQuestions)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white"><i class="bi bi-bank me-2"></i>Chọn từ Ngân hàng câu hỏi (<?php echo count($bankQuestions); ?> câu)</div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/addFromBank">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <div style="max-height:300px;overflow-y:auto;" class="mb-3 border p-2 rounded bg-light">
                    <?php foreach($bankQuestions as $bq): ?>
                        <div class="form-check mb-2 pb-2 border-bottom">
                            <input class="form-check-input" type="checkbox" name="qb_ids[]" value="<?php echo $bq['id']; ?>" id="bq<?php echo $bq['id']; ?>">
                            <label class="form-check-label small d-block text-dark fw-medium" for="bq<?php echo $bq['id']; ?>">
                                <?php echo strip_tags($bq['question_text']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm text-white w-100"><i class="bi bi-arrow-left-circle me-1"></i>Thêm vào đề</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>

<script>
function toggleEditQuestionType(qbId, type) {
    const radios = document.querySelectorAll('.correct-single-' + qbId);
    const checkboxes = document.querySelectorAll('.correct-multiple-' + qbId);
    if (type === 'multiple') {
        radios.forEach(r => r.style.display = 'none');
        checkboxes.forEach(c => c.style.display = 'inline-block');
    } else {
        radios.forEach(r => r.style.display = 'inline-block');
        checkboxes.forEach(c => c.style.display = 'none');
    }
}

function toggleNewQuestionType(type) {
    const radios = document.querySelectorAll('.new-correct-single');
    const checkboxes = document.querySelectorAll('.new-correct-multiple');
    if (type === 'multiple') {
        radios.forEach(r => r.style.display = 'none');
        checkboxes.forEach(c => c.style.display = 'inline-block');
    } else {
        radios.forEach(r => r.style.display = 'inline-block');
        checkboxes.forEach(c => c.style.display = 'none');
    }
}
</script>
