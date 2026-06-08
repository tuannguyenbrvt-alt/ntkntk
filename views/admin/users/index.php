<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white p-4 d-flex justify-content-between align-items-center flex-wrap gap-3 rounded-top-4">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-shield-lock text-primary me-2"></i>Quản lý Tài khoản & Phân quyền</h5>
            <p class="text-muted small mb-0">Cấp quyền Quản trị viên, Giáo viên hoặc Học viên</p>
        </div>
        <form action="" method="GET" class="d-flex align-items-center gap-1">
            <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="Tìm kiếm tài khoản..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="width: 220px;">
            <?php if (!empty($search)): ?>
                <a href="?" class="btn btn-sm btn-outline-secondary rounded-pill" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-search"></i> Tìm</button>
        </form>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-4 mb-0"><i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-4 mb-0"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Người dùng</th>
                        <th>Email / SĐT</th>
                        <th>Vai trò hiện tại</th>
                        <th>Ngày tham gia</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <?php if($u['avatar']): ?>
                                    <img src="<?php echo APP_URL . '/' . $u['avatar']; ?>" class="rounded-circle me-3 object-fit-cover" width="45" height="45">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3 fw-bold" style="width: 45px; height: 45px;">
                                        <?php echo strtoupper(substr($u['full_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                    <div class="text-muted small">@<?php echo htmlspecialchars($u['username']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-envelope me-1 text-muted"></i><?php echo htmlspecialchars($u['email']); ?></div>
                            <div class="small"><i class="bi bi-telephone me-1 text-muted"></i><?php echo htmlspecialchars($u['phone'] ?: '—'); ?></div>
                        </td>
                        <td>
                            <?php
                            $roleBadges = [
                                'super_admin' => ['class'=>'dark', 'label'=>'Super Admin', 'icon'=>'bi-patch-check-fill'],
                                'admin'       => ['class'=>'primary', 'label'=>'Quản trị viên', 'icon'=>'bi-shield-shaded'],
                                'teacher'     => ['class'=>'info text-dark', 'label'=>'Giáo viên', 'icon'=>'bi-person-badge'],
                                'student'     => ['class'=>'success', 'label'=>'Học viên', 'icon'=>'bi-mortarboard'],
                                'guest'       => ['class'=>'secondary', 'label'=>'Khách', 'icon'=>'bi-person'],
                            ];
                            $r = $roleBadges[$u['role']] ?? ['class'=>'secondary','label'=>$u['role'], 'icon'=>'bi-circle'];
                            ?>
                            <span class="badge bg-<?php echo $r['class']; ?> bg-opacity-10 text-<?php echo $r['class']; ?> border border-<?php echo $r['class']; ?> px-3 py-2">
                                <i class="bi <?php echo $r['icon']; ?> me-1"></i><?php echo $r['label']; ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                        <td class="text-end pe-4">
                            <?php if ($u['role'] !== 'super_admin'): ?>
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-2" data-bs-toggle="modal" data-bs-target="#roleModal<?php echo $u['id']; ?>">
                                    <i class="bi bi-arrow-left-right me-1"></i>Đổi quyền
                                </button>
                                <?php if ($_SESSION['role'] === 'super_admin'): ?>
                                    <form action="<?php echo APP_URL; ?>/admin/users/delete" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài khoản này vĩnh viễn?');">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Xóa tài khoản"><i class="bi bi-trash fs-5"></i></button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small font-italic">Cố định</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Modal đổi quyền -->
                    <div class="modal fade" id="roleModal<?php echo $u['id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold">Thay đổi vai trò</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="<?php echo APP_URL; ?>/admin/users/update-role" method="POST">
                                    <div class="modal-body p-4">
                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                        <div class="mb-3 text-center">
                                            <div class="fw-bold fs-5 mb-1"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                            <div class="text-muted small mb-4"><?php echo htmlspecialchars($u['email']); ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Chọn vai trò mới:</label>
                                            <select name="role" class="form-select rounded-3 p-3">
                                                <option value="student" <?php echo $u['role']==='student'?'selected':''; ?>>🎓 Học viên (Mặc định)</option>
                                                <option value="teacher" <?php echo $u['role']==='teacher'?'selected':''; ?>>👨‍🏫 Giáo viên (Quản lý khóa học)</option>
                                                <option value="admin"   <?php echo $u['role']==='admin'  ?'selected':''; ?>>🛡️ Quản trị viên (Toàn quyền)</option>
                                                <option value="guest"   <?php echo $u['role']==='guest'  ?'selected':''; ?>>👤 Khách</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-warning small border-0 py-2">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Lưu ý: Quyền "Quản trị viên" có thể xóa/sửa nội dung toàn hệ thống.
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Xác nhận thay đổi</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
