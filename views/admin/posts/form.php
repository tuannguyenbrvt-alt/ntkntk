<div class="card shadow-sm border-0">
    <div class="card-header bg-white p-3">
        <h5 class="mb-0 fw-bold"><?php echo isset($post) ? 'Chỉnh sửa bài viết' : 'Thêm bài viết mới'; ?></h5>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/admin/posts/<?php echo isset($post) ? 'update' : 'store'; ?>" method="POST" enctype="multipart/form-data">
            <?php if(isset($post)): ?>
                <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-9">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tiêu đề bài viết</label>
                        <input type="text" name="title" id="post-title" class="form-control form-control-lg" required value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Đường dẫn tĩnh (Slug)</label>
                        <input type="text" name="slug" id="post-slug" class="form-control" placeholder="Để trống hệ thống sẽ tự động tạo từ tiêu đề" value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nội dung bài viết</label>
                        <textarea name="content" id="editor" class="form-control"><?php echo $post['content'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-light mb-4 shadow-sm">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2 fw-bold"><i class="bi bi-save me-1"></i> <?php echo isset($post) ? 'Lưu thay đổi' : 'Đăng bài'; ?></button>
                            <a href="<?php echo APP_URL; ?>/admin/posts" class="btn btn-outline-secondary w-100">Hủy bỏ</a>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="published" <?php echo (isset($post) && $post['status'] == 'published') ? 'selected' : ''; ?>>Đã xuất bản (Public)</option>
                            <option value="draft" <?php echo (isset($post) && $post['status'] == 'draft') ? 'selected' : ''; ?>>Bản nháp (Draft)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phân loại</label>
                        <select name="type" class="form-select">
                            <option value="blog" <?php echo (isset($post) && $post['type'] == 'blog') ? 'selected' : ''; ?>>Blog / Tin tức</option>
                            <option value="page" <?php echo (isset($post) && $post['type'] == 'page') ? 'selected' : ''; ?>>Trang tĩnh (Ví dụ: Giới thiệu)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ảnh đại diện (Thumbnail)</label>
                        <?php if(isset($post) && $post['thumbnail']): ?>
                            <div class="mb-2 text-center bg-light p-2 rounded border">
                                <img src="<?php echo APP_URL . '/' . $post['thumbnail']; ?>" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="thumbnail" class="form-control" accept="image/jpeg, image/png, image/webp">
                        <small class="text-muted">Định dạng hỗ trợ: JPG, PNG, WEBP.</small>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tích hợp TinyMCE WYSIWYG Editor qua CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#editor',
        height: 600,
        plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table directionality emoticons',
        toolbar: 'undo redo | blocks | bold italic strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat | fullscreen code',
        menubar: true,
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image media',
        // ==== Upload ảnh thẳng lên Server (không dùng Base64 nữa) ====
        images_upload_url: '<?php echo APP_URL; ?>/admin/media/upload',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo APP_URL; ?>/admin/media/upload');
                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };
                xhr.onload = function () {
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }
                    var json = JSON.parse(xhr.responseText);
                    if (!json || typeof json.location != 'string') {
                        reject({ message: 'Lỗi: ' + xhr.responseText, remove: true });
                        return;
                    }
                    resolve(json.location);
                };
                xhr.onerror = function () {
                    reject({ message: 'Lỗi kết nối.', remove: false });
                };
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            });
        }
    });

    // Tạo Auto Slug bằng JS thân thiện
    document.getElementById('post-title').addEventListener('keyup', function() {
        if(document.getElementById('post-slug').value === '' || <?php echo isset($post) ? 'false' : 'true'; ?>) {
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
            document.getElementById('post-slug').value = slug;
        }
    });
</script>
