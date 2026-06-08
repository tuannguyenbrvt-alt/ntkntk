<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-3 p-3">
        <h5 class="mb-0 fw-bold">Danh sách Khóa học</h5>
        <div class="d-flex align-items-center gap-2">
            <form action="" method="GET" class="d-flex align-items-center gap-1">
                <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="Tìm kiếm khóa học..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="width: 220px;">
                <?php if (!empty($search)): ?>
                    <a href="?" class="btn btn-sm btn-outline-secondary rounded-pill" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-search"></i> Tìm</button>
            </form>
            <a href="<?php echo APP_URL; ?>/admin/courses/create" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-plus-lg me-1"></i> Tạo khóa học mới</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Hình ảnh</th>
                        <th>Tên Khóa học</th>
                        <th>Học phí</th>
                        <th>Giảng viên</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td class="ps-4">
                                <?php if ($course['thumbnail']): ?>
                                    <img src="<?php echo APP_URL . '/' . $course['thumbnail']; ?>" class="rounded shadow-sm" width="80" height="50" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width:80px; height:50px;"><i class="bi bi-play-btn text-muted fs-4"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                            </td>
                            <td>
                                <?php if($course['price'] > 0): ?>
                                    <span class="text-danger fw-bold"><?php echo number_format($course['price']); ?>đ</span>
                                    <?php if($course['original_price']): ?>
                                        <br><small class="text-decoration-line-through text-muted"><?php echo number_format($course['original_price']); ?>đ</small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-success">Miễn phí</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($course['author_name']); ?></td>
                            <td>
                                <?php if ($course['status'] == 'published'): ?>
                                    <span class="badge bg-success">Đang bán</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Bản nháp</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?php echo APP_URL; ?>/admin/courses/builder?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-warning" title="Sửa Đề cương"><i class="bi bi-list-task"></i> Đề cương</a>
                                <a href="<?php echo APP_URL; ?>/admin/courses/edit?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary" title="Sửa thông tin"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo APP_URL; ?>/admin/courses/delete" method="POST" class="d-inline" onsubmit="return confirm('Toàn bộ chương và bài học bên trong sẽ bị xóa theo. Bạn có chắc chắn?');">
                                    <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa khóa học"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($courses)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Chưa có khóa học nào trong hệ thống.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
