<?php
// views/shared/comments.php
// Component bình luận dùng chung cho Bài viết, Bài học, Khóa học
// Cần truyền các biến: $post_id, $course_id, $lesson_id (một trong ba giá trị)

$db = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? '';
$isLoggedIn = !empty($user_id);
$isAdmin = in_array($role, ['super_admin', 'admin']);

// Xác định loại mục tiêu
$target_type = '';
$target_id = 0;
$query_cond = '';
$params = [];

if (isset($post_id) && $post_id > 0) {
    $target_type = 'post';
    $target_id = (int)$post_id;
    $query_cond = 'c.post_id = ?';
    $params[] = $target_id;
} elseif (isset($course_id) && $course_id > 0) {
    $target_type = 'course';
    $target_id = (int)$course_id;
    $query_cond = 'c.course_id = ?';
    $params[] = $target_id;
} elseif (isset($lesson_id) && $lesson_id > 0) {
    $target_type = 'lesson';
    $target_id = (int)$lesson_id;
    $query_cond = 'c.lesson_id = ?';
    $params[] = $target_id;
}

if (!$target_type) {
    return; // Không xác định được mục tiêu bình luận
}

// Câu truy vấn lấy danh sách bình luận đã duyệt
// Đối với thành viên đăng nhập: xem được mọi bình luận đã duyệt
// Đối với khách: chỉ xem được bình luận đã duyệt VÀ công khai với khách (is_public_to_guest = 1)
$sql = "
    SELECT c.*, u.full_name as author_name, u.avatar as author_avatar, u.role as author_role
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE {$query_cond} AND c.status = 'approved'
";
if (!$isLoggedIn) {
    $sql .= " AND c.is_public_to_guest = 1";
}
$sql .= " ORDER BY c.created_at ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$commentsList = $stmt->fetchAll();

// Tổ chức bình luận theo cây (parent - child)
$commentsByParent = [];
foreach ($commentsList as $comment) {
    $parentId = $comment['parent_id'] ?: 0;
    $commentsByParent[$parentId][] = $comment;
}

// Hàm đệ quy hiển thị bình luận
function renderCommentNode($comment, $commentsByParent, $isLoggedIn, $userId, $isAdmin, $targetType) {
    $id = $comment['id'];
    $authorName = $comment['user_id'] ? $comment['author_name'] : $comment['guest_name'] . ' (Khách)';
    $isGuestComment = empty($comment['user_id']);
    
    // Avatar
    $avatarLetter = mb_substr($authorName, 0, 1, 'UTF-8');
    $avatarHtml = '';
    if (!$isGuestComment && $comment['author_avatar']) {
        $avatarHtml = '<img src="' . APP_URL . '/' . $comment['author_avatar'] . '" class="rounded-circle object-fit-cover me-2" style="width: 38px; height: 38px;">';
    } else {
        $bg = $isGuestComment ? 'linear-gradient(135deg, #6c757d, #495057)' : 'linear-gradient(135deg, #0d6efd, #0b5ed7)';
        if (!$isGuestComment && in_array($comment['author_role'], ['super_admin', 'admin'])) {
            $bg = 'linear-gradient(135deg, #dc3545, #b02a37)';
        }
        $avatarHtml = '<div class="rounded-circle text-white d-flex align-items-center justify-content-center me-2 font-weight-bold" 
                            style="width: 38px; height: 38px; background: ' . $bg . '; font-size: 0.95rem; font-weight: bold;">' . htmlspecialchars($avatarLetter) . '</div>';
    }

    // Role badge
    $badge = '';
    if (!$isGuestComment) {
        if (in_array($comment['author_role'], ['super_admin', 'admin'])) {
            $badge = '<span class="badge bg-danger ms-1.5" style="font-size:0.68rem;">QTV</span>';
        } elseif ($comment['author_role'] === 'teacher') {
            $badge = '<span class="badge bg-warning text-dark ms-1.5" style="font-size:0.68rem;">Giáo viên</span>';
        }
    }
    
    // Kiểm tra quyền Sửa/Xóa của người xem
    $canEdit = $isLoggedIn && ($comment['user_id'] == $userId);
    $canDelete = $isLoggedIn && (($comment['user_id'] == $userId) || $isAdmin);

    echo '<div class="d-flex mb-3 comment-item" id="comment-node-' . $id . '">';
    echo '  <div class="flex-shrink-0">' . $avatarHtml . '</div>';
    echo '  <div class="flex-grow-1 ms-2">';
    echo '    <div class="bg-white p-3 rounded-3 shadow-xs border border-light">';
    echo '      <div class="d-flex align-items-center justify-content-between mb-1">';
    echo '        <span class="fw-bold text-dark small d-flex align-items-center">' . htmlspecialchars($authorName) . $badge . '</span>';
    echo '        <small class="text-muted" style="font-size: 0.72rem;">' . date('d/m/Y H:i', strtotime($comment['created_at'])) . '</small>';
    echo '      </div>';
    
    // Nội dung comment hiển thị hoặc textbox sửa
    echo '      <div class="comment-text-content text-secondary small" id="comment-text-' . $id . '">' . nl2br(htmlspecialchars($comment['content'])) . '</div>';
    if ($canEdit) {
        echo '      <div class="comment-edit-form d-none mt-2" id="comment-edit-form-' . $id . '">';
        echo '        <textarea class="form-control form-control-sm mb-1.5" rows="2" id="edit-textarea-' . $id . '">' . htmlspecialchars($comment['content']) . '</textarea>';
        echo '        <div class="d-flex gap-1.5">';
        echo '          <button class="btn btn-xs btn-primary py-0.5 px-2 font-weight-bold" style="font-size:0.75rem;" onclick="saveCommentEdit(' . $id . ')">Lưu</button>';
        echo '          <button class="btn btn-xs btn-light py-0.5 px-2 border" style="font-size:0.75rem;" onclick="cancelCommentEdit(' . $id . ')">Hủy</button>';
        echo '        </div>';
        echo '      </div>';
    }

    echo '    </div>';

    // Các liên kết chức năng (Trả lời, Sửa, Xóa)
    echo '    <div class="d-flex align-items-center gap-2.5 mt-1.5 ms-2" style="font-size: 0.75rem;">';
    if ($isLoggedIn) {
        echo '      <a href="#" class="text-primary text-decoration-none fw-bold" onclick="showReplyForm(event, ' . $id . ')">Trả lời</a>';
    }
    if ($canEdit) {
        echo '      <a href="#" class="text-warning text-decoration-none fw-bold" onclick="showCommentEdit(event, ' . $id . ')">Sửa</a>';
    }
    if ($canDelete) {
        echo '      <a href="#" class="text-danger text-decoration-none fw-bold" onclick="deleteComment(event, ' . $id . ')">Xóa</a>';
    }
    echo '    </div>';

    // Vùng chèn form trả lời nhanh
    echo '    <div class="reply-form-wrapper mt-2 d-none" id="reply-form-' . $id . '"></div>';

    // Hiển thị các phản hồi con
    if (isset($commentsByParent[$id])) {
        echo '    <div class="mt-3 ps-3 border-start border-2 border-secondary-subtle">';
        foreach ($commentsByParent[$id] as $child) {
            renderCommentNode($child, $commentsByParent, $isLoggedIn, $userId, $isAdmin, $targetType);
        }
        echo '    </div>';
    }

    echo '  </div>';
    echo '</div>';
}
?>

<div class="card border-0 shadow-sm rounded-4 p-4 mt-4 bg-light bg-opacity-50">
    <h5 class="fw-bold mb-4 d-flex align-items-center">
        <i class="bi bi-chat-left-text-fill text-primary me-2"></i>Bình luận (<?php echo count($commentsList); ?>)
    </h5>

    <!-- Form gửi bình luận chính (Cấp 0) -->
    <div class="mb-4 bg-white p-3 rounded-4 border border-light">
        <?php if ($isLoggedIn): ?>
            <!-- Gửi bình luận khi đã đăng nhập -->
            <form id="comment-form-main">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                
                <div class="d-flex align-items-start gap-2">
                    <textarea name="content" class="form-control form-control-sm border-0 bg-light rounded-3 shadow-none p-2.5" rows="3" placeholder="Nhập bình luận của bạn..." required></textarea>
                    <button type="submit" class="btn btn-primary rounded-3 px-3.5 py-2.5 flex-shrink-0 align-self-end shadow-xs fw-bold" title="Gửi bình luận">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </form>
        <?php elseif ($target_type === 'post'): ?>
            <!-- Gửi bình luận khi là khách (chỉ được đăng ở bài viết) -->
            <form id="comment-form-main">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <div class="alert alert-warning py-2 px-3 small border-0 mb-3 rounded-3">
                    <i class="bi bi-info-circle-fill me-1"></i>Bạn đang bình luận với tư cách **Khách vãng lai**. Bình luận sẽ cần được Quản trị viên duyệt trước khi hiển thị.
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <input type="text" name="guest_name" class="form-control form-control-sm rounded-3 bg-light border-0 px-3 py-2" placeholder="Họ và tên của bạn *" required autocomplete="name">
                    </div>
                    <div class="col-md-6">
                        <input type="tel" name="guest_phone" class="form-control form-control-sm rounded-3 bg-light border-0 px-3 py-2" placeholder="Số điện thoại của bạn *" required autocomplete="tel">
                    </div>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <textarea name="content" class="form-control form-control-sm border-0 bg-light rounded-3 shadow-none p-2.5" rows="2" placeholder="Nội dung bình luận của bạn..." required></textarea>
                    <button type="submit" class="btn btn-primary rounded-3 px-3.5 py-2.5 flex-shrink-0 align-self-end shadow-xs fw-bold" title="Gửi bình luận">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </form>
        <?php else: ?>
            <!-- Học phần / khóa học yêu cầu đăng nhập -->
            <div class="text-center py-3 text-muted small">
                <i class="bi bi-shield-lock-fill fs-3 mb-1.5 d-block text-warning"></i>
                Vui lòng <a href="<?php echo APP_URL; ?>/login" class="fw-bold text-decoration-none">Đăng nhập tài khoản</a> để xem và gửi bình luận.
            </div>
        <?php endif; ?>
    </div>

    <!-- Danh sách các bình luận hiển thị dạng cây -->
    <div class="comments-tree-container mt-2" id="comments-list-wrapper">
        <?php
        if (isset($commentsByParent[0]) && count($commentsByParent[0]) > 0) {
            foreach ($commentsByParent[0] as $rootComment) {
                renderCommentNode($rootComment, $commentsByParent, $isLoggedIn, $user_id, $isAdmin, $target_type);
            }
        } else {
            echo '<div class="text-center py-5 text-muted small rounded-4 border border-dashed" id="no-comment-placeholder">Chưa có bình luận nào. Hãy là người đầu tiên để lại ý kiến!</div>';
        }
        ?>
    </div>
</div>

<script>
    // AJAX xử lý bình luận chính
    document.addEventListener("DOMContentLoaded", function() {
        const formMain = document.getElementById('comment-form-main');
        if (formMain) {
            formMain.addEventListener('submit', function(e) {
                e.preventDefault();
                const btnSubmit = formMain.querySelector('button[type="submit"]');
                btnSubmit.disabled = true;
                
                const formData = new FormData(this);
                
                fetch('<?php echo APP_URL; ?>/comment/store', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        formMain.reset();
                        if (data.status === 'pending') {
                            alert(data.message);
                        } else {
                            window.location.reload(); // Tải lại trang để cập nhật cấu trúc phân cấp chuẩn
                        }
                    } else {
                        alert(data.error);
                    }
                })
                .catch(() => alert('Có lỗi kết nối mạng xảy ra.'))
                .finally(() => btnSubmit.disabled = false);
            });
        }
    });

    // Hiện Form trả lời phụ
    function showReplyForm(e, parentId) {
        e.preventDefault();
        
        // Đóng các reply form khác
        document.querySelectorAll('.reply-form-wrapper').forEach(wrapper => {
            wrapper.innerHTML = '';
            wrapper.classList.add('d-none');
        });

        const wrapper = document.getElementById('reply-form-' + parentId);
        if (!wrapper) return;

        wrapper.innerHTML = `
            <form class="d-flex gap-2 align-items-start mt-2 p-2 bg-white rounded shadow-xs border border-light" onsubmit="submitReplyForm(event, ${parentId})">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
                <input type="hidden" name="parent_id" value="${parentId}">
                <textarea name="content" class="form-control form-control-sm bg-light border-0 rounded p-2" rows="2" placeholder="Trả lời bình luận này..." required></textarea>
                <button type="submit" class="btn btn-sm btn-primary py-2 px-3 align-self-end rounded fw-bold"><i class="bi bi-reply-fill"></i></button>
            </form>
        `;
        wrapper.classList.remove('d-none');
    }

    // Submit form trả lời phụ qua AJAX
    function submitReplyForm(e, parentId) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;

        const formData = new FormData(form);

        fetch('<?php echo APP_URL; ?>/comment/store', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                window.location.reload();
            } else {
                alert(data.error);
            }
        })
        .catch(() => alert('Lỗi mạng.'))
        .finally(() => btn.disabled = false);
    }

    // Hiển thị khung sửa bình luận
    function showCommentEdit(e, id) {
        e.preventDefault();
        document.getElementById('comment-text-' + id).classList.add('d-none');
        document.getElementById('comment-edit-form-' + id).classList.remove('d-none');
    }

    // Hủy sửa
    function cancelCommentEdit(id) {
        document.getElementById('comment-text-' + id).classList.remove('d-none');
        document.getElementById('comment-edit-form-' + id).classList.add('d-none');
    }

    // Gửi yêu cầu cập nhật bình luận
    function saveCommentEdit(id) {
        const text = document.getElementById('edit-textarea-' + id).value.trim();
        if (!text) return;

        const fd = new FormData();
        fd.append('id', id);
        fd.append('content', text);

        fetch('<?php echo APP_URL; ?>/comment/update', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                document.getElementById('comment-text-' + id).innerHTML = text.replace(/\n/g, "<br>");
                cancelCommentEdit(id);
            } else {
                alert(data.error);
            }
        })
        .catch(() => alert('Lỗi mạng.'));
    }

    // Xóa bình luận
    function deleteComment(e, id) {
        e.preventDefault();
        if (!confirm('Bạn chắc chắn muốn xóa bình luận này? Các phản hồi liên quan cũng sẽ bị xóa vĩnh viễn.')) return;

        const fd = new FormData();
        fd.append('id', id);

        fetch('<?php echo APP_URL; ?>/comment/delete', {
            method: 'POST',
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                const node = document.getElementById('comment-node-' + id);
                if (node) {
                    node.style.opacity = '0.5';
                    node.style.pointerEvents = 'none';
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            } else {
                alert(data.error);
            }
        })
        .catch(() => alert('Lỗi mạng.'));
    }
</script>
