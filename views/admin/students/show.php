<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body text-center p-4">
                <?php if (!empty($student['avatar'])): ?>
                    <img src="<?php echo APP_URL . '/' . $student['avatar']; ?>" class="rounded-circle mb-3 border border-3 border-light shadow-sm object-fit-cover" width="100" height="100">
                <?php else: ?>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student['full_name']); ?>&background=random&size=128" class="rounded-circle mb-3 border border-3 border-light shadow-sm" width="100">
                <?php endif; ?>
                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h5>
                <p class="text-muted mb-3"><i class="bi bi-envelope-at me-1"></i><?php echo htmlspecialchars($student['email']); ?></p>
                <div class="badge bg-success bg-opacity-10 text-success border border-success px-4 py-2 fs-6 rounded-pill shadow-sm">
                    Tổng chi tiêu: <strong class="ms-1"><?php echo number_format($totalPaid); ?> đ</strong>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mt-4">
            <div class="card-header bg-white p-3 fw-bold border-bottom-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Cập nhật thông tin nội bộ</div>
            <div class="card-body bg-light rounded-bottom-4">
                <form action="<?php echo APP_URL; ?>/admin/students/update" method="POST">
                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-semibold">Họ và tên</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($student['full_name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-semibold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-semibold">Nghề nghiệp</label>
                        <input type="text" name="profession" class="form-control" value="<?php echo htmlspecialchars($student['profession'] ?? ''); ?>">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-semibold">Địa chỉ / Ghi chú</label>
                        <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($student['address'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-3 border-0 bg-white p-2 rounded-4 shadow-sm" id="studentDetailTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill fw-bold px-4 py-2" id="enrollment-tab" data-bs-toggle="tab" data-bs-target="#enrollment-pane" type="button" role="tab" aria-controls="enrollment-pane" aria-selected="true">
                    <i class="bi bi-clock-history me-1"></i> Đăng ký & Thanh toán
                </button>
            </li>
            <li class="nav-item ms-2" role="presentation">
                <button class="nav-link rounded-pill fw-bold px-4 py-2" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress-pane" type="button" role="tab" aria-controls="progress-pane" aria-selected="false">
                    <i class="bi bi-graph-up-arrow me-1"></i> Kết quả học tập
                </button>
            </li>
        </ul>

        <div class="tab-content" id="studentDetailTabContent">
            <!-- TAB 1: ĐĂNG KÝ & THANH TOÁN -->
            <div class="tab-pane fade show active" id="enrollment-pane" role="tabpanel" aria-labelledby="enrollment-tab">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white p-4 fw-bold border-bottom-0 fs-5"><i class="bi bi-clock-history text-primary me-2"></i> Lịch sử Đăng ký & Thanh toán</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 text-muted small text-uppercase">Khóa học</th>
                                        <th class="text-muted small text-uppercase">Thời gian</th>
                                        <th class="text-muted small text-uppercase">Mã GD (TX)</th>
                                        <th class="text-muted small text-uppercase">Số tiền</th>
                                        <th class="text-end pe-4 text-muted small text-uppercase">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrollments as $en): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($en['title']); ?></td>
                                            <td><small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y H:i', strtotime($en['enrolled_at'])); ?></small></td>
                                            <td><code class="text-danger bg-danger bg-opacity-10 px-2 py-1 rounded border border-danger"><?php echo htmlspecialchars($en['tx_code']); ?></code></td>
                                            <td class="text-dark fw-bold"><?php echo number_format($en['price_paid']); ?> đ</td>
                                            <td class="text-end pe-4">
                                                <?php if ($en['status'] == 'active'): ?>
                                                    <span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i> Đã kích hoạt</span>
                                                <?php elseif ($en['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning text-dark rounded-pill px-3"><i class="bi bi-hourglass-split me-1"></i> Chờ duyệt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary rounded-pill px-3"><?php echo $en['status']; ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($enrollments)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-journal-x display-4 d-block mb-3 opacity-25"></i>Học viên này chưa đăng ký khóa học nào.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: KẾT QUẢ HỌC TẬP -->
            <div class="tab-pane fade" id="progress-pane" role="tabpanel" aria-labelledby="progress-tab">
                <!-- 1. Tiến độ khóa học -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white p-4 fw-bold border-bottom-0 fs-5">
                        <i class="bi bi-journal-bookmark text-success me-2"></i> Tiến độ khóa học đang tham gia
                    </div>
                    <div class="card-body pt-0">
                        <?php if (empty($activeCourses)): ?>
                            <p class="text-muted text-center py-3 mb-0"><i class="bi bi-info-circle me-1"></i> Không có khóa học nào đang học (active).</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($activeCourses as $c): ?>
                                    <?php $pct = $c['total_lessons'] > 0 ? round($c['done_lessons'] / $c['total_lessons'] * 100) : 0; ?>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                                            <div class="fw-bold text-dark mb-2 text-truncate" title="<?php echo htmlspecialchars($c['title']); ?>">
                                                <?php echo htmlspecialchars($c['title']); ?>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success rounded" style="width: <?php echo $pct; ?>%"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2 small text-muted">
                                                <span>Đã học: <strong><?php echo $c['done_lessons']; ?></strong> / <?php echo $c['total_lessons']; ?> bài</span>
                                                <span class="text-success fw-bold"><?php echo $pct; ?>%</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. Thống kê tổng quan dạng Cards -->
                <div class="row g-3 mb-4">
                    <!-- Trắc nghiệm Card -->
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success fw-bold small text-uppercase"><i class="bi bi-list-check me-1"></i>Trắc nghiệm</span>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Quiz</span>
                                </div>
                                <h4 class="fw-bold mb-1">
                                    <?php echo $quizStats['attempted_quizzes']; ?> <span class="fs-6 text-muted fw-normal">/ <?php echo $quizStats['total_quizzes']; ?> đề</span>
                                </h4>
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Số bài đạt:</span> <strong class="text-success"><?php echo $quizStats['passed_quizzes']; ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Tỷ lệ đạt:</span> <strong class="text-success"><?php echo $quizStats['attempted_quizzes'] > 0 ? round($quizStats['passed_quizzes'] / $quizStats['attempted_quizzes'] * 100) : 0; ?>%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tự luận Card -->
                    <?php $essay = $asgStats['essay']; ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-primary fw-bold small text-uppercase"><i class="bi bi-journal-text me-1"></i>BT Tự luận</span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">Essay</span>
                                </div>
                                <h4 class="fw-bold mb-1">
                                    <?php echo $essay['submitted']; ?> <span class="fs-6 text-muted fw-normal">/ <?php echo $essay['total']; ?> bài</span>
                                </h4>
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Đã chấm:</span> <strong><?php echo $essay['graded']; ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Điểm tích lũy:</span> <strong class="text-primary"><?php echo $essay['score']; ?> / <?php echo $essay['max_score']; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nộp file Card -->
                    <?php $file = $asgStats['file']; ?>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8 !important;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-info fw-bold small text-uppercase"><i class="bi bi-cloud-arrow-up me-1"></i>BT Nộp file</span>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill">File</span>
                                </div>
                                <h4 class="fw-bold mb-1">
                                    <?php echo $file['submitted']; ?> <span class="fs-6 text-muted fw-normal">/ <?php echo $file['total']; ?> bài</span>
                                </h4>
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span>Đã chấm:</span> <strong><?php echo $file['graded']; ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between py-1">
                                        <span>Điểm tích lũy:</span> <strong class="text-info"><?php echo $file['score']; ?> / <?php echo $file['max_score']; ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Chi tiết Kết quả Trắc nghiệm -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white p-4 fw-bold border-bottom-0 fs-5 pb-2">
                        <i class="bi bi-check-circle text-success me-2"></i> Lịch sử làm bài Trắc nghiệm
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Khóa học / Đề thi</th>
                                        <th>Bài học</th>
                                        <th>Điểm</th>
                                        <th>Kết quả</th>
                                        <th>Thời gian nộp</th>
                                        <th class="text-end pe-4">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($quizResults as $r): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="small text-muted mb-0.5"><?php echo htmlspecialchars($r['course_title']); ?></div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($r['quiz_title']); ?></div>
                                            </td>
                                            <td class="small text-muted"><?php echo htmlspecialchars($r['lesson_title']); ?></td>
                                            <td><strong class="text-success"><?php echo $r['score']; ?>%</strong></td>
                                            <td>
                                                <?php echo $r['passed'] 
                                                    ? '<span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">Đạt</span>' 
                                                    : '<span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1 rounded-pill">Chưa đạt</span>'; 
                                                ?>
                                            </td>
                                            <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($r['submitted_at'])); ?></td>
                                            <td class="text-end pe-4">
                                                <a href="<?php echo APP_URL; ?>/admin/students/quiz-attempt?attempt_id=<?php echo $r['attempt_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3"><i class="bi bi-eye"></i> Xem bài làm</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($quizResults)): ?>
                                        <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> Chưa làm đề trắc nghiệm nào.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 4. Chi tiết Kết quả Bài tập -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white p-4 fw-bold border-bottom-0 fs-5 pb-2">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i> Lịch sử nộp Bài tập
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Khóa học / Bài tập</th>
                                        <th>Loại bài</th>
                                        <th>Bài làm</th>
                                        <th>Trạng thái</th>
                                        <th>Điểm số</th>
                                        <th>Phản hồi & Người chấm</th>
                                        <th>Nộp ngày</th>
                                        <th class="text-end pe-4">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($asgResults as $r): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="small text-muted mb-0.5"><?php echo htmlspecialchars($r['course_title']); ?></div>
                                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($r['asgn_title']); ?></div>
                                            </td>
                                            <td>
                                                <?php echo $r['type'] === 'essay' 
                                                    ? '<span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 rounded-pill">Tự luận</span>' 
                                                    : '<span class="badge bg-info-subtle text-info border border-info px-2 py-1 rounded-pill">Nộp file</span>'; 
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($r['type'] === 'essay'): ?>
                                                    <button class="btn btn-sm btn-link text-decoration-none p-0 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#essayCollapse<?php echo $r['submission_id']; ?>">
                                                        <i class="bi bi-file-text me-1"></i> Xem nội dung
                                                    </button>
                                                    <div class="collapse mt-2" id="essayCollapse<?php echo $r['submission_id']; ?>" style="min-width: 200px;">
                                                        <div class="card card-body bg-light border-0 p-2 small text-wrap" style="max-height: 150px; overflow-y: auto;">
                                                            <?php echo nl2br(htmlspecialchars($r['content'])); ?>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <?php if (!empty($r['file_drive_url'])): ?>
                                                        <a href="<?php echo $r['file_drive_url']; ?>" target="_blank" class="text-info fw-semibold small text-truncate d-inline-block" style="max-width: 150px;" title="<?php echo htmlspecialchars($r['file_name']); ?>">
                                                            <i class="bi bi-file-earmark-arrow-down me-1"></i><?php echo htmlspecialchars($r['file_name'] ?: 'Tải xuống file'); ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-danger small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Lỗi File/Chưa tải lên</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo $r['status'] === 'graded' 
                                                    ? '<span class="badge bg-success-subtle text-success border border-success px-2 py-1 rounded-pill">Đã chấm</span>' 
                                                    : '<span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1 rounded-pill">Chờ chấm</span>'; 
                                                ?>
                                            </td>
                                            <td>
                                                <?php echo $r['score'] !== null 
                                                    ? '<strong class="text-success fs-5">'.$r['score'].'</strong> <small class="text-muted">/ '.$r['max_score'].'</small>' 
                                                    : '<span class="text-muted">—</span>'; 
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($r['status'] === 'graded'): ?>
                                                    <div class="small text-dark text-wrap" style="max-width: 150px;"><?php echo htmlspecialchars($r['feedback'] ?: 'Không có phản hồi'); ?></div>
                                                    <div class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-person-check me-1"></i><?php echo htmlspecialchars($r['grader_name'] ?: 'Admin'); ?></div>
                                                <?php else: ?>
                                                    <span class="text-muted small">Chưa có</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($r['submitted_at'])); ?></td>
                                            <td class="text-end pe-4">
                                                <a href="<?php echo APP_URL; ?>/admin/assignments/grade?sub_id=<?php echo $r['submission_id']; ?>" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                                    <i class="bi bi-pencil-square"></i> <?php echo $r['status'] === 'graded' ? 'Sửa điểm' : 'Chấm điểm'; ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($asgResults)): ?>
                                        <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> Chưa nộp bài tập nào.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
</div>
