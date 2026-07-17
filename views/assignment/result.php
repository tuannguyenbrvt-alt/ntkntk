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

    <?php if($sub): ?>
    <!-- Hien thi ket qua da cham hoac dang cho -->
    <div class="card border-0 shadow mb-4" style="<?php echo $sub['status'] === 'graded' ? 'background:#1a2e1a;border:2px solid #28a745!important;' : 'background:#1a1a2e;border:2px solid #f0b429!important;'; ?>">
        <div class="card-body text-center py-4">
            <?php if($sub['status'] === 'graded'): ?>
                <div style="font-size:3rem;">✅</div>
                <h3 class="text-white fw-bold mt-2">Đã được chấm điểm</h3>
                <div class="display-5 fw-bold text-success"><?php echo $sub['score']; ?> / <?php echo $assignment['max_score']; ?></div>
                <div class="text-white-50 small mt-1">Chấm bởi: <?php echo htmlspecialchars($sub['grader_name'] ?? 'Giáo viên'); ?> — <?php echo date('d/m/Y H:i', strtotime($sub['graded_at'])); ?></div>
            <?php else: ?>
                <div style="font-size:3rem;">⏳</div>
                <h3 class="text-white fw-bold mt-2">Đang chờ chấm điểm</h3>
                <div class="text-white-50 small mt-1">Bài nộp đang ở trạng thái chờ giáo viên chấm điểm (hoặc chấm các file mới cập nhật).</div>
            <?php endif; ?>
        </div>
        <?php if($sub['feedback']): ?>
        <div class="card-footer border-0" style="<?php echo $sub['status'] === 'graded' ? 'background:#152515;' : 'background:#151525;'; ?>">
            <h6 class="text-white"><i class="bi bi-chat-quote me-2"></i>Nhận xét của giáo viên:</h6>
            <div class="text-white-50" style="white-space:pre-wrap;"><?php echo htmlspecialchars($sub['feedback']); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Danh sach cac file da nop -->
    <?php if($assignment['type'] === 'file' && !empty($subFiles)): ?>
        <div class="card border-0 shadow mb-4" style="background:#1a1a2e;border-color:#2d2d44!important;">
            <div class="card-header text-white" style="background:#2d2d44;"><i class="bi bi-folder2-open me-2"></i>Danh sách file đã nộp</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush bg-transparent">
                    <?php foreach($subFiles as $index => $file): ?>
                        <div class="list-group-item bg-transparent text-white border-secondary border-opacity-25 p-3 d-flex align-items-center justify-content-between flex-wrap gap-2 text-start" style="<?php echo $file['is_deleted'] ? 'opacity: 0.65; border-color: rgba(220, 53, 69, 0.3) !important;' : ''; ?>">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary rounded-circle" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;"><?php echo $index + 1; ?></span>
                                <div class="text-start">
                                    <?php if($file['is_deleted']): ?>
                                        <span class="text-decoration-line-through text-muted fw-semibold small"><?php echo htmlspecialchars($file['file_name']); ?></span>
                                        <span class="badge bg-danger ms-2"><i class="bi bi-x-circle me-1"></i>Giáo viên đã xóa</span>
                                        <div class="text-danger small mt-1"><i class="bi bi-info-circle me-1"></i>Lý do: <strong><?php echo htmlspecialchars($file['delete_reason'] ?? 'Không có lý do'); ?></strong></div>
                                    <?php else: ?>
                                        <?php if($file['file_drive_url']): ?>
                                            <a href="<?php echo htmlspecialchars($file['file_drive_url']); ?>" target="_blank" class="text-info text-decoration-underline fw-semibold small"><?php echo htmlspecialchars($file['file_name']); ?></a>
                                        <?php else: ?>
                                            <span class="text-danger fw-semibold small"><?php echo htmlspecialchars($file['file_name']); ?> (Lỗi tải lên Drive)</span>
                                        <?php endif; ?>
                                        
                                        <div class="mt-1 small">
                                            <?php if($file['status'] === 'graded'): ?>
                                                <span class="text-success font-monospace fw-bold"><i class="bi bi-check-circle me-1"></i><?php echo $file['score']; ?> đ</span>
                                                <?php if($file['feedback']): ?>
                                                    <span class="text-muted ms-2">| Nhận xét: <?php echo htmlspecialchars($file['feedback']); ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-warning small"><i class="bi bi-hourglass-split me-1"></i>Đang chờ chấm</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <?php if(!$file['is_deleted'] && $file['status'] === 'pending'): ?>
                                    <form method="POST" action="<?php echo APP_URL; ?>/assignment/deleteFile" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa file này không?');">
                                        <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                        <input type="hidden" name="lesson_id" value="<?php echo $assignment['lesson_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 small"><i class="bi bi-trash"></i> Xóa</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Form nop bai -->
    <?php if($assignment['type']==='essay'): ?>
        <?php if(!$sub || $sub['status'] === 'pending'): ?>
        <div class="card border-0 shadow-sm mb-4" style="background:#1a1a2e;border-color:#2d2d44!important;">
            <div class="card-header text-white" style="background:#2d2d44;"><i class="bi bi-send me-2"></i><?php echo $sub ? 'Cập nhật bài làm' : 'Nộp bài'; ?></div>
            <div class="card-body">
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
            </div>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="card border-0 shadow-sm mb-4" style="background:#1a1a2e;border-color:#2d2d44!important;">
            <div class="card-header text-white" style="background:#2d2d44;"><i class="bi bi-cloud-upload me-2"></i>Nộp thêm / Cập nhật file bài làm</div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/assignment/submitFile" enctype="multipart/form-data">
                    <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <input type="hidden" name="lesson_id" value="<?php echo $assignment['lesson_id']; ?>">
                    <input type="hidden" name="drive_folder_id" value="<?php echo htmlspecialchars($assignment['drive_folder_id'] ?? ''); ?>">
                    <div class="mb-3 text-start">
                        <label class="form-label text-white">Chọn file để nộp <span class="text-muted">(tối đa 50MB)</span></label>
                        <input type="file" name="submission_files[]" class="form-control" style="background:#111;color:#eee;border-color:#444;" multiple required>
                    </div>
                    <button type="submit" class="btn btn-info text-white"><i class="bi bi-cloud-upload me-1"></i>Nộp file</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $assignment['lesson_id']; ?>" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i>Quay ve bai hoc</a>
    </div>
</div>
