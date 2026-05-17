<?php require_once ROOT_PATH . '/helpers/MenuHelper.php'; ?>

<div class="row">
    <!-- Form Thêm Menu -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">Thêm Menu mới</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo APP_URL; ?>/admin/menus/store" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="title" id="menu-title" class="form-control" required placeholder="Ví dụ: Giới thiệu">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gắn Bài viết / Trang</label>
                        <select class="form-select mb-2" onchange="if(this.value) { document.getElementById('menu-url').value = this.value; document.getElementById('menu-title').value = this.options[this.selectedIndex].text.replace(/\[.*?\]\s*/, ''); }">
                            <option value="">-- Hoặc tự nhập đường dẫn --</option>
                            <?php foreach ($postsList as $p): ?>
                                <option value="/post?slug=<?php echo $p['slug']; ?>">
                                    [<?php echo strtoupper($p['type']); ?>] <?php echo htmlspecialchars($p['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label class="form-label">Đường dẫn (URL)</label>
                        <input type="text" name="url" id="menu-url" class="form-control" required placeholder="Ví dụ: /gioi-thieu">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Menu cha (Tùy chọn)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Trống (Menu gốc) --</option>
                            <?php foreach ($flatMenus as $m): ?>
                                <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-circle me-1"></i> Thêm mới</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Danh sách Menu (Kéo thả) -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Cấu trúc Menu</h5>
                <button id="saveOrderBtn" class="btn btn-sm btn-success d-none"><i class="bi bi-save me-1"></i> Lưu thứ tự</button>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3"><i class="bi bi-info-circle"></i> Kéo thả biểu tượng <i class="bi bi-arrows-move"></i> để sắp xếp thứ tự và cấp độ menu. Nhấn <strong>Lưu thứ tự</strong> sau khi chỉnh sửa.</p>
                <div id="menu-nested-list">
                    <?php echo MenuHelper::renderAdminTree($menuTree); ?>
                </div>
                <?php if(empty($menuTree)): ?>
                    <div class="alert alert-warning text-center">Chưa có menu nào được tạo.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Menu -->
<div class="modal fade" id="editMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Chỉnh sửa Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo APP_URL; ?>/admin/menus/update" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="title" id="edit-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gắn Bài viết / Trang</label>
                        <select class="form-select mb-2" onchange="if(this.value) { document.getElementById('edit-url').value = this.value; document.getElementById('edit-title').value = this.options[this.selectedIndex].text.replace(/\[.*?\]\s*/, ''); }">
                            <option value="">-- Hoặc tự nhập đường dẫn --</option>
                            <?php foreach ($postsList as $p): ?>
                                <option value="/post?slug=<?php echo $p['slug']; ?>">
                                    [<?php echo strtoupper($p['type']); ?>] <?php echo htmlspecialchars($p['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label class="form-label">Đường dẫn (URL)</label>
                        <input type="text" name="url" id="edit-url" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
function editMenu(id, title, url) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-title').value = title;
    document.getElementById('edit-url').value = url;
    var modal = new bootstrap.Modal(document.getElementById('editMenuModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    function initSortable() {
        var lists = document.querySelectorAll('.sortable-list');
        for (var i = 0; i < lists.length; i++) {
            new Sortable(lists[i], {
                group: 'nested',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                handle: '.handle',
                onEnd: function (evt) {
                    document.getElementById('saveOrderBtn').classList.remove('d-none');
                }
            });
        }
    }
    initSortable();

    document.getElementById('saveOrderBtn').addEventListener('click', function() {
        var data = [];
        
        function traverseList(ul, parentId) {
            var items = ul.children;
            var order = 1;
            for (var i = 0; i < items.length; i++) {
                var li = items[i];
                if(li.tagName && li.tagName.toLowerCase() === 'li') {
                    var id = li.getAttribute('data-id');
                    if (id) {
                        data.push({
                            id: id,
                            parent_id: parentId,
                            sort_order: order++
                        });
                        var childUl = li.querySelector('ul.sortable-list');
                        if (childUl) {
                            traverseList(childUl, id);
                        }
                    }
                }
            }
        }
        
        var rootUl = document.querySelector('#menu-nested-list > ul.sortable-list');
        if (rootUl) traverseList(rootUl, '');

        var btn = this;
        var originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang lưu...';
        btn.disabled = true;

        fetch('<?php echo APP_URL; ?>/admin/menus/reorder', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(res => {
            if(res.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + res.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            alert('Lỗi kết nối!');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });
});
</script>

<style>
.sortable-list {
    min-height: 20px;
    padding-left: 20px !important;
    list-style-type: none;
}
#menu-nested-list > ul.sortable-list {
    padding-left: 0 !important;
}
.sortable-ghost {
    opacity: 0.4;
    background-color: #f8f9fa;
    border: 1px dashed #ced4da;
}
.empty-list {
    border: 1px dashed transparent;
    transition: all 0.2s;
}
.sortable-drag .empty-list {
    border-color: #dee2e6;
    background: #f8f9fa;
}
</style>
