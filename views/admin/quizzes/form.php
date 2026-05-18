<div class="container py-4" style="max-width:620px;">
<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Tao De Trac Nghiem Moi</h5></div>
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/store">
            <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            <div class="mb-3"><label class="form-label fw-semibold">Tieu de de *</label><input type="text" name="title" class="form-control" required placeholder="Bai kiem tra cuoi chuong..."></div>
            <div class="mb-3"><label class="form-label fw-semibold">Mo ta / Huong dan lam bai</label><textarea name="description" class="form-control" rows="3" placeholder="Lam bai can than, moi cau 1 diem..."></textarea></div>
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label fw-semibold">Thoi gian (phut)</label><input type="number" name="time_limit_minutes" class="form-control" value="0" min="0"><small class="text-muted">0 = khong gioi han</small></div>
                <div class="col-md-4"><label class="form-label fw-semibold">Diem de qua (%)</label><input type="number" name="pass_score" class="form-control" value="50" min="0" max="100" step="0.01"></div>
                <div class="col-md-4"><label class="form-label fw-semibold">So luot toi da</label><input type="number" name="max_attempts" class="form-control" value="0" min="0"><small class="text-muted">0 = khong gioi han</small></div>
            </div>
            <div class="mt-3 p-3 bg-light rounded">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="shuffle_questions" id="sq" checked>
                    <label class="form-check-label" for="sq"><i class="bi bi-shuffle me-1 text-primary"></i>Tu dong dao thu tu cau hoi</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="shuffle_options" id="so" checked>
                    <label class="form-check-label" for="so"><i class="bi bi-shuffle me-1 text-success"></i>Tu dong dao thu tu dap an</label>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Luu & Them cau hoi</button>
                <a href="<?php echo APP_URL; ?>/admin/courses/builder?id=<?php echo $course_id; ?>" class="btn btn-secondary">Huy</a>
            </div>
        </form>
    </div>
</div>
</div>
