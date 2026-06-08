<?php
// views/admin/comments/index.php
?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-white p-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-chat-left-text-fill text-primary me-2"></i>Quản lý Bình luận</h5>
        <div class="d-flex align-items-center gap-3">
            <form action="" method="GET" class="d-flex align-items-center gap-1">
                <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="Tìm kiếm bình luận..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="width: 220px;">
                <?php if (!empty($search)): ?>
                    <a href="?" class="btn btn-sm btn-outline-secondary rounded-pill" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-search"></i> Tìm</button>
            </form>
            <span class="text-muted small">Hiển thị tối đa 150 bình luận mới nhất</span>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">Người gửi</th>
                        <th style="width: 35%;">Nội dung bình luận</th>
                        <th style="width: 20%;">Vị trí hiển thị</th>
                        <th style="width: 10%;" class="text-center">Trạng thái</th>
                        <th style="width: 10%;" class="text-center">Hiện với khách</th>
                        <th style="width: 10%;" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($comments)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-left-text fs-1 mb-2 d-block text-secondary"></i>
                                Chưa có bình luận nào trên trang web.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <?php 
                                $isGuest = empty($comment['user_id']);
                                $authorName = $isGuest ? $comment['guest_name'] : $comment['author_name'];
                                $authorSub = $isGuest ? 'SĐT: ' . htmlspecialchars($comment['guest_phone']) : 'Thành viên (' . htmlspecialchars($comment['author_role']) . ')';
                                
                                // Tạo đường dẫn trực quan tới trang hiển thị
                                $targetLink = '#';
                                $targetTitle = '';
                                if ($comment['post_id']) {
                                    $targetLink = APP_URL . '/post?slug=' . $comment['post_slug'];
                                    $targetTitle = 'Bài viết: ' . htmlspecialchars($comment['post_title']);
                                } elseif ($comment['course_id']) {
                                    $targetLink = APP_URL . '/course?slug=' . $comment['course_slug'];
                                    $targetTitle = 'Khóa học: ' . htmlspecialchars($comment['course_title']);
                                } elseif ($comment['lesson_id']) {
                                    $targetLink = APP_URL . '/learning?course_id=' . $comment['lesson_id'] . '&lesson_id=' . $comment['lesson_id'];
                                    $targetTitle = 'Bài học: ' . htmlspecialchars($comment['lesson_title']) . '<br><small class="text-muted">Khóa: ' . htmlspecialchars($comment['lesson_course_title']) . '</small>';
                                }
                            ?>
                            <tr id="comment-row-<?php echo $comment['id']; ?>">
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($authorName); ?>">
                                        <?php echo htmlspecialchars($authorName); ?>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;"><?php echo $authorSub; ?></small>
                                </td>
                                <td>
                                    <div class="text-secondary small text-wrap" style="max-width: 450px; word-wrap: break-word;">
                                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i>Gửi lúc: <?php echo date('d/m/Y H:i:s', strtotime($comment['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="small">
                                        <a href="<?php echo $targetLink; ?>" target="_blank" class="text-decoration-none fw-semibold">
                                            <?php echo $targetTitle; ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-2.5 py-1.5 status-badge <?php echo $comment['status'] === 'pending' ? 'bg-warning text-dark' : 'bg-success'; ?>" id="status-badge-<?php echo $comment['id']; ?>">
                                        <?php echo $comment['status'] === 'pending' ? 'Chờ duyệt' : 'Đã hiển thị'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               id="switch-public-<?php echo $comment['id']; ?>" 
                                               onchange="togglePublicToGuest(<?php echo $comment['id']; ?>)"
                                               <?php echo $comment['is_public_to_guest'] == 1 ? 'checked' : ''; ?>>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Nút duyệt nếu đang chờ duyệt -->
                                        <button class="btn btn-sm btn-success btn-approve-comment <?php echo $comment['status'] === 'approved' ? 'd-none' : ''; ?>" 
                                                id="btn-approve-<?php echo $comment['id']; ?>"
                                                onclick="approveComment(<?php echo $comment['id']; ?>)" 
                                                title="Phê duyệt bình luận">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        
                                        <!-- Nút xóa -->
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteComment(<?php echo $comment['id']; ?>)" 
                                                title="Xóa bình luận">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Duyệt bình luận của khách
    function approveComment(id) {
        if (!confirm('Duyệt bình luận này cho phép mọi thành viên đọc chứ?')) return;
        
        const btn = document.getElementById('btn-approve-' + id);
        btn.disabled = true;

        const fd = new FormData();
        fd.append('id', id);

        fetch('<?php echo APP_URL; ?>/admin/comments/approve', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                // Cập nhật giao diện
                const badge = document.getElementById('status-badge-' + id);
                badge.className = 'badge rounded-pill px-2.5 py-1.5 bg-success';
                badge.innerText = 'Đã hiển thị';
                
                // Ẩn nút duyệt
                btn.classList.add('d-none');
            } else {
                alert('Lỗi: ' + data.error);
                btn.disabled = false;
            }
        })
        .catch(() => {
            alert('Lỗi kết nối mạng.');
            btn.disabled = false;
        });
    }

    // Bật tắt hiển thị với khách
    function togglePublicToGuest(id) {
        const checkbox = document.getElementById('switch-public-' + id);
        checkbox.disabled = true;

        const fd = new FormData();
        fd.append('id', id);

        fetch('<?php echo APP_URL; ?>/admin/comments/toggle-public', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                checkbox.checked = (data.is_public_to_guest == 1);
            } else {
                alert('Lỗi: ' + data.error);
                checkbox.checked = !checkbox.checked; // Reset về trạng thái cũ
            }
        })
        .catch(() => {
            alert('Lỗi kết nối mạng.');
            checkbox.checked = !checkbox.checked;
        })
        .finally(() => {
            checkbox.disabled = false;
        });
    }

    // Xóa bình luận
    function deleteComment(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa bình luận này cùng tất cả các câu trả lời liên quan? Hành động này không thể hoàn tác.')) return;

        const row = document.getElementById('comment-row-' + id);

        const fd = new FormData();
        fd.append('id', id);

        fetch('<?php echo APP_URL; ?>/admin/comments/delete', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                row.style.opacity = '0.3';
                row.style.pointerEvents = 'none';
                setTimeout(() => {
                    row.remove();
                }, 400);
            } else {
                alert('Lỗi: ' + data.error);
            }
        })
        .catch(() => alert('Lỗi kết nối mạng.'));
    }
</script>
