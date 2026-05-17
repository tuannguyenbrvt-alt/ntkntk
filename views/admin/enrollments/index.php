<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
        <i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center rounded-top-4">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-cart-check text-primary me-2"></i>Duyệt Đăng ký Khóa học</h5>
            <p class="text-muted small mb-0">Quản lý và xử lý các yêu cầu đăng ký từ học viên</p>
        </div>
        <div class="d-flex gap-2">
            <?php
            $pendingCount = 0;
            foreach ($enrollments as $en_item) {
                if ($en_item['status'] === 'pending') $pendingCount++;
            }
            if ($pendingCount > 0):
            ?>
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">
                <i class="bi bi-hourglass-split me-1"></i><?php echo $pendingCount; ?> chờ duyệt
            </span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Học viên</th>
                        <th>Khóa học</th>
                        <th>Học phí</th>
                        <th>Mã GD (Nội dung CK)</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $en): ?>
                        <tr class="<?php echo $en['status'] === 'pending' ? 'table-warning' : ''; ?>">
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($en['full_name']); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($en['email']); ?></div>
                            </td>
                            <td><span class="text-primary fw-semibold"><?php echo htmlspecialchars($en['course_title']); ?></span></td>
                            <td><span class="text-danger fw-bold"><?php echo number_format($en['price_paid']); ?>đ</span></td>
                            <td><code class="fs-6 bg-light p-1 rounded text-dark border"><?php echo htmlspecialchars($en['tx_code']); ?></code></td>
                            <td>
                                <?php
                                $statusMap = [
                                    'active'    => ['class' => 'success', 'icon' => 'check-circle-fill', 'label' => 'Đã kích hoạt'],
                                    'pending'   => ['class' => 'warning text-dark', 'icon' => 'hourglass-split', 'label' => 'Chờ duyệt CK'],
                                    'cancelled' => ['class' => 'danger',  'icon' => 'x-circle-fill',   'label' => 'Đã huỷ'],
                                ];
                                $s = $statusMap[$en['status']] ?? ['class'=>'secondary','icon'=>'circle','label'=>$en['status']];
                                echo '<span class="badge bg-' . $s['class'] . ' bg-opacity-15 text-' . $s['class'] . ' border border-' . $s['class'] . '">';
                                echo '<i class="bi bi-' . $s['icon'] . ' me-1"></i>' . $s['label'] . '</span>';
                                ?>
                                <?php if (!empty($en['note'])): ?>
                                    <div class="text-muted small mt-1" style="font-size:.78rem;"><i class="bi bi-sticky me-1"></i><?php echo htmlspecialchars($en['note']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($en['created_at'])); ?></small></td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <!-- Nút Duyệt nhanh (chỉ hiện khi pending) -->
                                    <?php if ($en['status'] === 'pending'): ?>
                                    <form action="<?php echo APP_URL; ?>/admin/enrollments/approve" method="POST" onsubmit="return confirm('Xác nhận duyệt mở khóa học này?');">
                                        <input type="hidden" name="id" value="<?php echo $en['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" title="Duyệt ngay">
                                            <i class="bi bi-unlock-fill me-1"></i>Duyệt
                                        </button>
                                    </form>
                                    <?php endif; ?>

                                    <!-- Nút Sửa -->
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                        title="Chỉnh sửa"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal<?php echo $en['id']; ?>">
                                        <i class="bi bi-pencil me-1"></i>Sửa
                                    </button>

                                    <!-- Nút Xoá -->
                                    <form action="<?php echo APP_URL; ?>/admin/enrollments/delete" method="POST"
                                        onsubmit="return confirm('Xoá đăng ký này? Hành động không thể hoàn tác!');">
                                        <input type="hidden" name="id" value="<?php echo $en['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Xoá">
                                            <i class="bi bi-trash me-1"></i>Xoá
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Sửa -->
                        <div class="modal fade" id="editModal<?php echo $en['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow-lg">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-pencil-square text-primary me-2"></i>
                                            Sửa đăng ký — <span class="text-primary"><?php echo htmlspecialchars($en['full_name']); ?></span>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="<?php echo APP_URL; ?>/admin/enrollments/update" method="POST">
                                        <div class="modal-body px-4 pb-2">
                                            <input type="hidden" name="id" value="<?php echo $en['id']; ?>">
                                            <div class="mb-3 p-3 bg-light rounded-3">
                                                <div class="fw-bold"><?php echo htmlspecialchars($en['course_title']); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($en['full_name']); ?> &bull; <?php echo htmlspecialchars($en['email']); ?></div>
                                                <div class="text-muted small">Mã GD: <code><?php echo $en['tx_code']; ?></code> &bull; <?php echo number_format($en['price_paid']); ?>đ</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                                                <select name="status" class="form-select rounded-3" required>
                                                    <option value="pending"   <?php echo $en['status']==='pending'   ? 'selected' : ''; ?>>⏳ Chờ duyệt chuyển khoản</option>
                                                    <option value="active"    <?php echo $en['status']==='active'    ? 'selected' : ''; ?>>✅ Đã kích hoạt</option>
                                                    <option value="cancelled" <?php echo $en['status']==='cancelled' ? 'selected' : ''; ?>>❌ Đã huỷ</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Ghi chú nội bộ</label>
                                                <textarea name="note" class="form-control rounded-3" rows="3"
                                                    placeholder="Vd: Đã nhận tiền ngày 15/5, số TK 123..."><?php echo htmlspecialchars($en['note'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Huỷ</button>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                                <i class="bi bi-save me-1"></i>Lưu thay đổi
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($enrollments)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                Chưa có giao dịch đăng ký nào.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
