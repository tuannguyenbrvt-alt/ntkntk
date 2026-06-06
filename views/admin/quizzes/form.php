<div class="container py-4" style="max-width:620px;">
<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="bi bi-trophy me-2"></i><?php echo $quiz ? 'Sửa Đề Trắc Nghiệm' : 'Tạo Đề Trắc Nghiệm Mới'; ?></h5></div>
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/<?php echo $quiz ? 'update' : 'store'; ?>">
            <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <?php if ($quiz): ?>
                <input type="hidden" name="id" value="<?php echo $quiz['id']; ?>">
            <?php endif; ?>
            <div class="mb-3"><label class="form-label fw-semibold">Tiêu đề đề *</label><input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($quiz ? $quiz['title'] : ''); ?>" placeholder="Bài kiểm tra cuối chương..."></div>
            <div class="mb-3"><label class="form-label fw-semibold">Mô tả / Hướng dẫn làm bài</label><textarea name="description" class="form-control" rows="3" placeholder="Làm bài cẩn thận, mỗi câu 1 điểm..."><?php echo htmlspecialchars($quiz ? $quiz['description'] : ''); ?></textarea></div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label fw-semibold">Thời gian (phút)</label><input type="number" name="time_limit_minutes" class="form-control" value="<?php echo htmlspecialchars($quiz ? $quiz['time_limit_minutes'] : '0'); ?>" min="0"><small class="text-muted">0 = không giới hạn</small></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Điểm để qua (%)</label><input type="number" name="pass_score" class="form-control" value="<?php echo htmlspecialchars($quiz ? $quiz['pass_score'] : '50'); ?>" min="0" max="100" step="0.01"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Số lượt tối đa</label><input type="number" name="max_attempts" class="form-control" value="<?php echo htmlspecialchars($quiz ? $quiz['max_attempts'] : '0'); ?>" min="0"><small class="text-muted">0 = không giới hạn</small></div>
            </div>
            <div class="mt-3 p-3 bg-light rounded">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="shuffle_questions" id="sq" <?php echo ($quiz === null || $quiz['shuffle_questions']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="sq"><i class="bi bi-shuffle me-1 text-primary"></i>Tự động đảo thứ tự câu hỏi</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="shuffle_options" id="so" <?php echo ($quiz === null || $quiz['shuffle_options']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="so"><i class="bi bi-shuffle me-1 text-success"></i>Tự động đảo thứ tự đáp án</label>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?php echo $quiz ? 'Lưu thay đổi' : 'Lưu & Thêm câu hỏi'; ?></button>
                <a href="<?php echo APP_URL; ?>/admin/courses/builder?id=<?php echo $course_id; ?>" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>
</div>
