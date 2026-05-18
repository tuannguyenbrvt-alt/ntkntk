<?php $course_id = $course_id ?? 0; $cid = $cid ?? 0; ?>
<div class="container-fluid py-4">
<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h5 class="fw-bold mb-0"><i class="bi bi-list-check text-primary me-2"></i><?php echo htmlspecialchars($quiz['title']); ?></h5>
    <small class="text-muted">Bai hoc: <?php echo htmlspecialchars($quiz['lesson_title'] ?? ''); ?></small></div>
    <a href="<?php echo APP_URL; ?>/admin/courses/builder?id=<?php echo $course_id; ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay lai</a>
</div>
<div class="row g-4">
    <!-- Cau hoi trong de -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <span><i class="bi bi-card-checklist me-2"></i>Cau hoi trong de (<?php echo count($inQuizQuestions); ?> cau)</span>
            </div>
            <div class="card-body p-0">
                <?php if(empty($inQuizQuestions)): ?>
                    <div class="p-4 text-center text-muted">Chua co cau hoi nao. Them tu ben phai.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                    <?php foreach($inQuizQuestions as $i => $q): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-primary me-2"><?php echo $i+1; ?></span>
                                    <span><?php echo htmlspecialchars($q['question_text']); ?></span>
                                </div>
                                <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/removeQuestion" class="d-inline" onsubmit="return confirm('Xoa khoi de?')">
                                    <input type="hidden" name="qq_id" value="<?php echo $q['qq_id']; ?>">
                                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-x-circle"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Panel them cau hoi -->
    <div class="col-lg-5">
        <!-- Them cau hoi moi vao ngan hang & de -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white"><i class="bi bi-plus-circle me-2"></i>Them cau hoi moi</div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/storeQuestion">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <input type="hidden" name="cid" value="<?php echo $cid; ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Noi dung cau hoi *</label>
                        <textarea name="question_text" class="form-control" rows="2" required placeholder="Dau la thu do cua Viet Nam?"></textarea>
                    </div>
                    <label class="form-label fw-semibold">Cac phuong an (chon 1 dap an dung)</label>
                    <?php for($i=0; $i<4; $i++): ?>
                    <div class="input-group mb-2">
                        <div class="input-group-text"><input type="radio" name="correct" value="<?php echo $i; ?>" <?php echo $i===0?'checked':''; ?>></div>
                        <input type="text" name="options[]" class="form-control" placeholder="Phuong an <?php echo chr(65+$i); ?>">
                    </div>
                    <?php endfor; ?>
                    <small class="text-muted d-block mb-3">Chon nut tron ben trai de danh dau dap an dung.</small>
                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-plus me-1"></i>Them cau hoi</button>
                </form>
            </div>
        </div>

        <!-- Them tu ngan hang -->
        <?php if(!empty($bankQuestions)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white"><i class="bi bi-bank me-2"></i>Chon tu Ngan hang cau hoi (<?php echo count($bankQuestions); ?> cau)</div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/admin/quizzes/addFromBank">
                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <div style="max-height:200px;overflow-y:auto;" class="mb-3">
                    <?php foreach($bankQuestions as $bq): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="qb_ids[]" value="<?php echo $bq['id']; ?>" id="bq<?php echo $bq['id']; ?>">
                            <label class="form-check-label small" for="bq<?php echo $bq['id']; ?>"><?php echo htmlspecialchars(mb_substr($bq['question_text'],0,70).'...'); ?></label>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm text-white w-100"><i class="bi bi-arrow-left-circle me-1"></i>Them vao de</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
