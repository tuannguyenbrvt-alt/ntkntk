<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-ui-checks text-danger me-2"></i>Chấm bài tập</h4>
            <p class="text-muted mb-0">Danh sách các bài tập học viên vừa nộp đang chờ bạn chấm điểm.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Học viên</th>
                            <th>Bài tập</th>
                            <th>Khóa học</th>
                            <th>Thời gian nộp</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pendingSubs)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-cup-hot fs-1 d-block mb-3"></i>
                                    Tuyệt vời! Không có bài tập nào đang chờ chấm.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pendingSubs as $sub): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($sub['student_name']); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-primary"><?php echo htmlspecialchars($sub['assignment_title']); ?></div>
                                        <div class="small text-muted">
                                            <?php if ($sub['assignment_type'] === 'essay'): ?>
                                                <span class="badge bg-secondary rounded-pill"><i class="bi bi-text-paragraph me-1"></i>Tự luận</span>
                                            <?php else: ?>
                                                <span class="badge bg-info rounded-pill"><i class="bi bi-file-earmark-arrow-up me-1"></i>Nộp file</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-secondary"><?php echo htmlspecialchars($sub['course_title']); ?></span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="bi bi-clock me-1"></i><?php echo date('d/m/Y', strtotime($sub['submitted_at'])); ?><br>
                                            <span class="text-muted"><?php echo date('H:i', strtotime($sub['submitted_at'])); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?php echo APP_URL; ?>/admin/assignments/grade?sub_id=<?php echo $sub['id']; ?>&course_id=<?php echo $sub['course_id']; ?>&from_pending=1" class="btn btn-sm btn-danger fw-bold shadow-sm">
                                            <i class="bi bi-pen me-1"></i> Chấm bài ngay
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
