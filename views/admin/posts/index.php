<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-3 p-3">
        <h5 class="mb-0 fw-bold">Quản lý bài viết (Blog/Page)</h5>
        <div class="d-flex align-items-center gap-2">
            <form action="" method="GET" class="d-flex align-items-center gap-1">
                <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="Tìm kiếm bài viết..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="width: 220px;">
                <?php if (!empty($search)): ?>
                    <a href="?" class="btn btn-sm btn-outline-secondary rounded-pill" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-search"></i> Tìm</button>
            </form>
            <a href="<?php echo APP_URL; ?>/admin/posts/create" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-pencil-square me-1"></i> Viết bài mới</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Hình ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Tác giả</th>
                        <th>Phân loại</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td class="ps-4">
                                <?php if ($post['thumbnail']): ?>
                                    <img src="<?php echo APP_URL . '/' . $post['thumbnail']; ?>" class="rounded shadow-sm" width="70" height="45" style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width:70px; height:45px;"><i class="bi bi-image text-muted"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                <?php if ($post['type'] == 'blog'): ?>
                                    <form action="<?php echo APP_URL; ?>/admin/posts/toggle-pin" method="POST" class="d-inline ms-1">
                                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="btn btn-link p-0 border-0 align-baseline" title="<?php echo $post['is_pinned'] ? 'Bỏ ghim bài viết' : 'Ghim bài viết lên đầu'; ?>">
                                            <i class="bi <?php echo $post['is_pinned'] ? 'bi-pin-angle-fill text-danger' : 'bi-pin-angle text-muted'; ?> fs-6"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <br>
                                <small class="text-muted"><a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" target="_blank" class="text-decoration-none"><?php echo htmlspecialchars($post['slug']); ?></a></small>
                            </td>
                            <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                            <td>
                                <?php if($post['type'] == 'page'): ?>
                                    <span class="badge bg-info text-dark">Trang tĩnh</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Blog</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($post['status'] == 'published'): ?>
                                    <span class="badge bg-success">Đã xuất bản</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Bản nháp</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                            <td class="text-end pe-4">
                                <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Xem thử"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo APP_URL; ?>/admin/posts/edit?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo APP_URL; ?>/admin/posts/delete" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài viết này chứ?');">
                                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($posts)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Chưa có bài viết nào trong hệ thống.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
