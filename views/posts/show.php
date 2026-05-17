<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/blog" class="text-decoration-none">Tin tức</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($post['title']); ?></li>
                </ol>
            </nav>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <?php if ($post['thumbnail']): ?>
                    <img src="<?php echo APP_URL . '/' . $post['thumbnail']; ?>" class="card-img-top w-100" style="max-height: 500px; object-fit: cover;">
                <?php endif; ?>
                
                <div class="card-body p-md-5">
                    <h1 class="fw-bold mb-4 display-6" style="line-height: 1.4;"><?php echo htmlspecialchars($post['title']); ?></h1>
                    
                    <div class="d-flex flex-wrap align-items-center text-muted mb-5 bg-light p-3 rounded-3">
                        <div class="d-flex align-items-center me-4 mb-2 mb-md-0">
                            <i class="bi bi-person-circle fs-4 text-primary me-2"></i>
                            <span class="fw-semibold text-dark"><?php echo htmlspecialchars($post['author_name']); ?></span>
                        </div>
                        <div class="d-flex align-items-center me-4 mb-2 mb-md-0">
                            <i class="bi bi-calendar-event text-primary me-2"></i>
                            <span><?php echo date('d/m/Y - H:i', strtotime($post['created_at'])); ?></span>
                        </div>
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <i class="bi bi-tag text-primary me-2"></i>
                            <span><?php echo $post['type'] == 'page' ? 'Trang tĩnh' : 'Tin tức'; ?></span>
                        </div>
                    </div>
                    
                    <div class="post-content lh-lg">
                        <?php echo $post['content']; ?>
                    </div>
                    
                    <div class="mt-5 pt-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <?php 
                            $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                            $shareTitle = urlencode($post['title']);
                            $shareUrl = urlencode($currentUrl);
                        ?>
                        <div class="share-buttons d-flex align-items-center gap-2">
                            <span class="fw-bold me-2">Chia sẻ:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle" title="Chia sẻ Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareTitle; ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-circle" title="Chia sẻ Twitter"><i class="bi bi-twitter"></i></a>
                            <a href="mailto:?subject=<?php echo $shareTitle; ?>&body=<?php echo $shareUrl; ?>" class="btn btn-sm btn-outline-danger rounded-circle" title="Gửi qua Email"><i class="bi bi-envelope"></i></a>
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" onclick="copyToClipboard('<?php echo $currentUrl; ?>')" title="Sao chép liên kết"><i class="bi bi-link-45deg"></i></button>
                        </div>
                        <a href="<?php echo APP_URL; ?>/blog" class="btn btn-primary px-4 rounded-pill"><i class="bi bi-arrow-left me-1"></i> Trở về Blog</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS Tùy chỉnh cho nội dung bài viết từ TinyMCE */
.post-content { font-size: 1.1rem; color: #333; }
.post-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.post-content iframe { max-width: 100%; border-radius: 8px; margin: 20px 0; }
.post-content h2, .post-content h3 { margin-top: 30px; margin-bottom: 15px; font-weight: 700; color: #000; }
.post-content p { margin-bottom: 20px; }
.post-content ul, .post-content ol { margin-bottom: 20px; padding-left: 20px; }
.post-content blockquote { border-left: 4px solid #0d6efd; padding-left: 15px; color: #555; font-style: italic; background: #f8f9fa; padding: 15px; border-radius: 0 8px 8px 0; }
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Đã sao chép liên kết vào bộ nhớ tạm!');
    }).catch(err => {
        console.error('Lỗi sao chép: ', err);
    });
}
</script>
