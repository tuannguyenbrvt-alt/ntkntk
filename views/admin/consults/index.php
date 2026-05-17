<?php
$pageTitle = $title ?? 'Đăng ký Tư vấn';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-headset text-primary me-2"></i>Danh sách Đăng ký Tư vấn</h4>
        <p class="text-muted small mb-0">Danh sách học viên đăng ký qua form Footer website</p>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show rounded-3"><i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <?php
    $total = array_sum($counts);
    $statCards = [
        ['label'=>'Tất cả', 'val'=>$total,              'icon'=>'bi-list-ul',       'color'=>'primary', 'filter'=>''],
        ['label'=>'Mới',    'val'=>$counts['new']??0,    'icon'=>'bi-bell-fill',     'color'=>'danger',  'filter'=>'new'],
        ['label'=>'Đã gọi','val'=>$counts['called']??0,  'icon'=>'bi-telephone-fill','color'=>'warning', 'filter'=>'called'],
        ['label'=>'Xong',   'val'=>$counts['done']??0,   'icon'=>'bi-check-circle',  'color'=>'success', 'filter'=>'done'],
    ];
    foreach ($statCards as $s): ?>
    <div class="col-6 col-md-3">
        <a href="?status=<?php echo $s['filter']; ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 <?php echo $filter === $s['filter'] ? 'border-' . $s['color'] . ' border-2' : ''; ?>">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="rounded-3 p-2" style="background:var(--bs-<?php echo $s['color']; ?>-bg-subtle);">
                        <i class="bi <?php echo $s['icon']; ?> text-<?php echo $s['color']; ?> fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0"><?php echo $s['val']; ?></h4>
                        <p class="text-muted small mb-0"><?php echo $s['label']; ?></p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <?php if (empty($rows)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                <p>Chưa có yêu cầu tư vấn nào.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Họ và tên</th>
                        <th>Số điện thoại</th>
                        <th>Khóa học quan tâm</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $row): ?>
                    <tr <?php echo $row['status'] === 'new' ? 'class="table-warning"' : ''; ?>>
                        <td class="ps-4 text-muted small"><?php echo $i + 1; ?></td>
                        <td class="fw-semibold"><?php echo htmlspecialchars($row['full_name'] ?: '—'); ?></td>
                        <td>
                            <a href="tel:<?php echo $row['phone']; ?>" class="fw-bold text-primary text-decoration-none">
                                <i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($row['phone']); ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($row['course']): ?>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3"><?php echo htmlspecialchars($row['course']); ?></span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $badges = ['new'=>'danger','called'=>'warning','done'=>'success'];
                            $labels = ['new'=>'🔴 Mới','called'=>'🟡 Đã gọi','done'=>'🟢 Xong'];
                            echo '<span class="badge rounded-pill bg-' . ($badges[$row['status']] ?? 'secondary') . '">' . ($labels[$row['status']] ?? $row['status']) . '</span>';
                            ?>
                        </td>
                        <td class="text-muted small"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary rounded-pill me-1" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $row['id']; ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="<?php echo APP_URL; ?>/admin/consults/delete" class="d-inline" onsubmit="return confirm('Xoá yêu cầu này?');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <!-- Modal cập nhật -->
                    <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow-lg">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Cập nhật: <?php echo htmlspecialchars($row['phone']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="<?php echo APP_URL; ?>/admin/consults/update-status">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Trạng thái</label>
                                            <select name="status" class="form-select rounded-3">
                                                <option value="new"    <?php echo $row['status']==='new'    ?'selected':''; ?>>🔴 Mới — chưa liên hệ</option>
                                                <option value="called" <?php echo $row['status']==='called' ?'selected':''; ?>>🟡 Đã gọi — đang tư vấn</option>
                                                <option value="done"   <?php echo $row['status']==='done'   ?'selected':''; ?>>🟢 Xong — hoàn tất</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Ghi chú nội bộ</label>
                                            <textarea name="note" class="form-control rounded-3" rows="3" placeholder="Ghi chú kết quả cuộc gọi..."><?php echo htmlspecialchars($row['note'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Huỷ</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">💾 Lưu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
