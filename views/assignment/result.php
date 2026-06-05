<div class="container py-5" style="max-width:700px;margin:auto;">
    <?php $sub = $submission ?? null; ?>
    <!-- Assignment header -->
    <div class="card border-0 shadow-sm mb-4" style="background:#1a1a2e;border-color:#2d2d44!important;">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 mb-2">
                <?php if($assignment['type']==='essay'): ?>
                    <span class="badge bg-primary">Tu luan</span>
                <?php else: ?>
                    <span class="badge bg-info">Nop file</span>
                <?php endif; ?>
                <h5 class="text-white fw-bold mb-0"><?php echo htmlspecialchars($assignment['title']); ?></h5>
            </div>
            <div class="text-white-50 small mb-3"><?php echo nl2br(htmlspecialchars($assignment['description'] ?? '')); ?></div>
            <div class="d-flex gap-3 text-muted small">
                <span><i class="bi bi-award me-1"></i>Diem toi da: <?php echo $assignment['max_score']; ?></span>
                <?php if($assignment['due_date']): ?><span><i class="bi bi-calendar me-1"></i>Han nop: <?php echo date('d/m/Y H:i', strtotime($assignment['due_date'])); ?></span><?php endif; ?>
            </div>
        </div>
    </div>

    <?php if($sub && $sub['status'] === 'graded'): ?>
    <!-- Hien thi ket qua da cham -->
    <div class="card border-0 shadow mb-4" style="background:#1a2e1a;border:2px solid #28a745!important;">
        <div class="card-body text-center py-4">
            <div style="font-size:3rem;">✅</div>
            <h3 class="text-white fw-bold mt-2">Đã được chấm điểm</h3>
            <div class="display-5 fw-bold text-success"><?php echo $sub['score']; ?> / <?php echo $assignment['max_score']; ?></div>
            <div class="text-white-50 small mt-1">Chấm bởi: <?php echo htmlspecialchars($sub['grader_name'] ?? 'Giáo viên'); ?> — <?php echo date('d/m/Y H:i', strtotime($sub['graded_at'])); ?></div>
            <?php if($assignment['type'] === 'file' && !empty($sub['file_name'])): ?>
                <div class="text-white-50 small mt-3">
                    <i class="bi bi-file-earmark-check me-1 text-info"></i>File bài làm: 
                    <?php if($sub['file_drive_url']): ?>
                        <a href="<?php echo htmlspecialchars($sub['file_drive_url']); ?>" target="_blank" class="text-info text-decoration-underline fw-semibold"><?php echo htmlspecialchars($sub['file_name']); ?></a>
                    <?php else: ?>
                        <span class="text-danger fw-semibold"><?php echo htmlspecialchars($sub['file_name']); ?> (Lỗi tải lên Google Drive)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if($sub['feedback']): ?>
        <div class="card-footer border-0" style="background:#152515;">
            <h6 class="text-white"><i class="bi bi-chat-quote me-2"></i>Nhận xét của giáo viên:</h6>
            <div class="text-white-50" style="white-space:pre-wrap;"><?php echo htmlspecialchars($sub['feedback']); ?></div>
        </div>
        <?php endif; ?>
    </div>
    <?php elseif($sub && $sub['file_drive_id'] === 'error'): ?>
    <!-- Loi upload Google Drive -->
    <div class="alert alert-danger py-3 mb-4">
        <div class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Lỗi tải file lên Google Drive</div>
        <div class="small mt-1 text-dark" style="white-space: pre-wrap;"><?php echo htmlspecialchars($sub['content']); ?></div>
        <div class="mt-2 text-danger fw-semibold">Lưu ý: Bài làm của bạn chưa được nộp thành công lên Drive. Hệ thống đã báo lỗi đến giáo viên để khắc phục cấu hình. Vui lòng thử chọn file và nộp lại ở phía dưới.</div>
    </div>
    <?php elseif($sub): ?>
    <div class="alert alert-warning"><i class="bi bi-hourglass me-2"></i>Bạn đã nộp bài. Vui lòng chờ giáo viên chấm điểm.</div>
    <?php endif; ?>

    <?php if(!$sub || $sub['status'] === 'pending'): ?>
    <!-- Form nop bai -->
    <div class="card border-0 shadow-sm" style="background:#1a1a2e;border-color:#2d2d44!important;">
        <div class="card-header text-white" style="background:#2d2d44;"><i class="bi bi-send me-2"></i><?php echo $sub ? 'Cập nhật bài làm' : 'Nộp bài'; ?></div>
        <div class="card-body">
            <?php if($assignment['type']==='essay'): ?>
            <form method="POST" action="<?php echo APP_URL; ?>/assignment/submitEssay">
                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="lesson_id" value="<?php echo $assignment['lesson_id']; ?>">
                <div class="mb-3">
                    <label class="form-label text-white">Bài làm của bạn *</label>
                    <textarea name="content" class="form-control" rows="10" style="background:#111;color:#eee;border-color:#444;" placeholder="Nhập bài làm của bạn ở đây..." required><?php echo htmlspecialchars($sub['content'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Nộp bài</button>
            </form>
            <?php else: ?>
            <form method="POST" action="<?php echo APP_URL; ?>/assignment/submitFile" enctype="multipart/form-data">
                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="lesson_id" value="<?php echo $assignment['lesson_id']; ?>">
                <input type="hidden" name="drive_folder_id" value="<?php echo htmlspecialchars($assignment['drive_folder_id'] ?? ''); ?>">
                <div class="mb-3">
                    <label class="form-label text-white">Chọn file để nộp <span class="text-muted">(tối đa 50MB)</span></label>
                    <input type="file" name="submission_file" class="form-control" style="background:#111;color:#eee;border-color:#444;" required>
                </div>
                <?php if($sub && $sub['file_name']): ?>
                <div class="alert alert-info small">
                    File đã chọn nộp trước: 
                    <?php if($sub['file_drive_url']): ?>
                        <a href="<?php echo htmlspecialchars($sub['file_drive_url']); ?>" target="_blank" class="text-info text-decoration-underline fw-semibold"><?php echo htmlspecialchars($sub['file_name']); ?></a>
                    <?php else: ?>
                        <span class="text-danger fw-semibold"><?php echo htmlspecialchars($sub['file_name']); ?> (Lỗi tải lên Drive)</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-info text-white"><i class="bi bi-cloud-upload me-1"></i><?php echo ($sub && $sub['file_drive_id'] !== 'error') ? 'Nộp lại' : 'Nộp file'; ?></button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $assignment['lesson_id']; ?>" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay ve bai hoc</a>
    </div>
</div>
