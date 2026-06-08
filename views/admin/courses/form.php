<div class="card shadow-sm border-0">
    <div class="card-header bg-white p-3">
        <h5 class="mb-0 fw-bold"><?php echo isset($course) ? 'Sửa thông tin Khóa học' : 'Tạo Khóa học mới'; ?></h5>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/admin/courses/<?php echo isset($course) ? 'update' : 'store'; ?>" method="POST" enctype="multipart/form-data" onsubmit="if(typeof tinymce !== 'undefined') { tinymce.triggerSave(); }">
            <?php if(isset($course)): ?>
                <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên khóa học</label>
                        <input type="text" name="title" id="course-title" class="form-control form-control-lg" required value="<?php echo htmlspecialchars($course['title'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                        <input type="text" name="slug" id="course-slug" class="form-control" placeholder="Để trống hệ thống tự tạo" value="<?php echo htmlspecialchars($course['slug'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả giới thiệu khóa học</label>
                        <textarea name="description" id="editor" class="form-control"><?php echo $course['description'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 bg-light mb-4 shadow-sm">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2 fw-bold"><i class="bi bi-save me-1"></i> Lưu thông tin</button>
                            <a href="<?php echo APP_URL; ?>/admin/courses" class="btn btn-outline-secondary w-100">Hủy bỏ</a>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá bán (VNĐ)</label>
                        <input type="text" name="price" class="form-control" value="<?php echo isset($course) ? floatval($course['price']) : 0; ?>">
                        <small class="text-muted">Nhập 0 nếu là khóa học miễn phí.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá gốc (VNĐ) <span class="text-muted fw-normal">- Hiển thị gạch chéo</span></label>
                        <input type="text" name="original_price" class="form-control" value="<?php echo isset($course) && $course['original_price'] ? floatval($course['original_price']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?php echo (isset($course) && $course['status'] == 'draft') ? 'selected' : ''; ?>>Đang soạn thảo (Nháp)</option>
                            <option value="published" <?php echo (isset($course) && $course['status'] == 'published') ? 'selected' : ''; ?>>Công khai (Bán)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="allow_comments" id="allow_comments" value="1" <?php echo (!isset($course) || $course['allow_comments'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-semibold" for="allow_comments">Cho phép bình luận</label>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ảnh đại diện (Thumbnail)</label>
                        <?php if(isset($course) && $course['thumbnail']): ?>
                            <div class="mb-2 text-center bg-white p-2 rounded border">
                                <img src="<?php echo APP_URL . '/' . $course['thumbnail']; ?>" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="thumbnail" class="form-control" accept="image/jpeg, image/png, image/webp">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cấu hình TinyMCE & JS helper -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#editor',
                height: 400,
                plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media table',
                toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code',
                menubar: false,
                setup: function (editor) {
                    editor.on('change', function () {
                        tinymce.triggerSave();
                    });
                }
            });
        }

        var courseTitle = document.getElementById('course-title');
        if (courseTitle) {
            courseTitle.addEventListener('keyup', function() {
                if(document.getElementById('course-slug').value === '' || <?php echo isset($course) ? 'false' : 'true'; ?>) {
                    let title = this.value;
                    let slug = title.toLowerCase()
                        .replace(/[áàảãạâấầẩẫậăắằẳẵặ]/g, 'a')
                        .replace(/[éèẻẽẹêếềểễệ]/g, 'e')
                        .replace(/[íìỉĩị]/g, 'i')
                        .replace(/[óòỏõọôốồổỗộơớờởỡợ]/g, 'o')
                        .replace(/[úùủũụưứừửữự]/g, 'u')
                        .replace(/[ýỳỷỹỵ]/g, 'y')
                        .replace(/đ/g, 'd')
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-');
                    document.getElementById('course-slug').value = slug;
                }
            });
        }
    });
</script>
