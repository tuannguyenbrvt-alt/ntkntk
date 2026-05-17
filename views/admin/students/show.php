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
        <div class="card shadow-sm border-0 rounded-4 h-100">
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
</div>
