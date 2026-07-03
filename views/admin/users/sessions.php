<!-- views/admin/users/sessions.php -->
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Lịch sử truy cập hệ thống</h1>
            <p class="text-muted mb-0">Theo dõi phiên đăng nhập, thời gian hoạt động và bài học đã xem của người dùng.</p>
        </div>
    </div>

    <!-- Alert thông báo -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Bộ lọc tìm kiếm -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo APP_URL; ?>/admin/sessions" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm theo Username, Họ tên, IP...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="status">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Đang hoạt động (Online)</option>
                        <option value="logged_out" <?php echo $statusFilter === 'logged_out' ? 'selected' : ''; ?>>Đã đăng xuất</option>
                        <option value="expired" <?php echo $statusFilter === 'expired' ? 'selected' : ''; ?>>Hết hạn / Đóng trình duyệt</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Lọc</button>
                    <a href="<?php echo APP_URL; ?>/admin/sessions" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 80px;">ID</th>
                            <th>Người dùng</th>
                            <th>Vai trò</th>
                            <th>IP Address</th>
                            <th>Thiết bị / Trình duyệt</th>
                            <th>Đăng nhập lúc</th>
                            <th>Đăng xuất lúc</th>
                            <th>Trạng thái</th>
                            <th class="pe-4 text-end" style="width: 150px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sessions)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                                    Không tìm thấy lịch sử phiên hoạt động nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sessions as $item): ?>
                                <tr>
                                    <td class="ps-4 text-muted fw-semibold">#<?php echo $item['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-light text-primary rounded-circle d-flex align-items-center justify-content-centerfw-bold" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold text-gray-800"><?php echo htmlspecialchars($item['full_name']); ?></h6>
                                                <small class="text-muted">@<?php echo htmlspecialchars($item['username']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $roleBadge = 'bg-secondary';
                                        $roleName = 'Học viên';
                                        if ($item['role'] === 'super_admin') {
                                            $roleBadge = 'bg-danger';
                                            $roleName = 'Super Admin';
                                        } elseif ($item['role'] === 'admin') {
                                            $roleBadge = 'bg-warning text-dark';
                                            $roleName = 'Admin';
                                        } elseif ($item['role'] === 'teacher') {
                                            $roleBadge = 'bg-success';
                                            $roleName = 'Giáo viên';
                                        }
                                        ?>
                                        <span class="badge <?php echo $roleBadge; ?>"><?php echo $roleName; ?></span>
                                    </td>
                                    <td class="font-monospace text-muted"><?php echo htmlspecialchars($item['ip_address'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                        $ua = $item['user_agent'] ?? '';
                                        $device = 'PC / Desktop';
                                        $icon = 'bi-laptop';
                                        if (preg_match('/(android|iphone|ipad|mobile)/i', $ua)) {
                                            $device = 'Thiết bị di động';
                                            $icon = 'bi-phone';
                                        }
                                        // Phân tích sơ bộ trình duyệt
                                        $browser = 'Unknown Browser';
                                        if (strpos($ua, 'Edg') !== false) $browser = 'Edge';
                                        elseif (strpos($ua, 'Chrome') !== false) $browser = 'Chrome';
                                        elseif (strpos($ua, 'Safari') !== false) $browser = 'Safari';
                                        elseif (strpos($ua, 'Firefox') !== false) $browser = 'Firefox';
                                        ?>
                                        <span class="text-gray-700" title="<?php echo htmlspecialchars($ua); ?>">
                                            <i class="bi <?php echo $icon; ?> me-1 text-secondary"></i>
                                            <?php echo $browser; ?> (<?php echo $device; ?>)
                                        </span>
                                    </td>
                                    <td><?php echo date('H:i:s d/m/Y', strtotime($item['login_at'])); ?></td>
                                    <td class="text-muted">
                                        <?php 
                                        if ($item['logout_at']) {
                                            echo date('H:i:s d/m/Y', strtotime($item['logout_at']));
                                        } else {
                                            echo '<span class="text-italic text-secondary">Chưa ghi nhận</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($item['status'] === 'active'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1">
                                                <span class="d-inline-block rounded-circle bg-success me-1" style="width: 6px; height: 6px; transform: translateY(-1px);"></span>
                                                Online
                                            </span>
                                        <?php elseif ($item['status'] === 'logged_out'): ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2.5 py-1">
                                                Đã đăng xuất
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1" title="Đóng trình duyệt hoặc mất kết nối">
                                                Đóng trình duyệt
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="<?php echo APP_URL; ?>/admin/sessions/detail?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-eye me-1"></i> Xem chi tiết
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

    <!-- Phân trang -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- Nút trang trước -->
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo APP_URL; ?>/admin/sessions?page=<?php echo $page - 1; ?>&q=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo; Trở lại</span>
                    </a>
                </li>
                
                <!-- Hiển thị các số trang -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo APP_URL; ?>/admin/sessions?page=<?php echo $i; ?>&q=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endphp; ?>
                
                <!-- Nút trang sau -->
                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo APP_URL; ?>/admin/sessions?page=<?php echo $page + 1; ?>&q=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>" aria-label="Next">
                        <span aria-hidden="true">Kế tiếp &raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
