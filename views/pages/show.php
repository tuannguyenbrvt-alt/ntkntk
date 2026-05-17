<div class="container py-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php if(isset($error)): ?>
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="120" class="mb-4 opacity-50">
                    <h2 class="fw-bold text-muted"><?php echo $error; ?></h2>
                    <a href="<?php echo APP_URL; ?>/" class="btn btn-primary mt-3 rounded-pill px-4"><i class="bi bi-house me-2"></i>Về trang chủ</a>
                </div>
            <?php else: ?>
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/" class="text-decoration-none"><i class="bi bi-house"></i> Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($page['title']); ?></li>
                    </ol>
                </nav>

                <h1 class="fw-bold mb-4 text-primary"><?php echo htmlspecialchars($page['title']); ?></h1>
                
                <?php if(!empty($page['thumbnail'])): ?>
                    <div class="mb-4 text-center">
                        <img src="<?php echo APP_URL . '/' . $page['thumbnail']; ?>" class="img-fluid rounded-4 shadow-sm" style="max-height: 400px; object-fit: cover;">
                    </div>
                <?php endif; ?>

                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm post-content mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                    <?php echo $page['content']; ?>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-3 mb-5">
                    <?php 
                        $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $shareTitle = urlencode($page['title']);
                        $shareUrl = urlencode($currentUrl);
                    ?>
                    <div class="share-buttons d-flex align-items-center gap-2">
                        <span class="fw-bold text-muted me-1">Chia sẻ:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle" title="Chia sẻ Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareTitle; ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-circle" title="Chia sẻ Twitter"><i class="bi bi-twitter"></i></a>
                        <a href="mailto:?subject=<?php echo $shareTitle; ?>&body=<?php echo $shareUrl; ?>" class="btn btn-sm btn-outline-danger rounded-circle" title="Gửi qua Email"><i class="bi bi-envelope"></i></a>
                        <button class="btn btn-sm btn-outline-secondary rounded-circle" onclick="copyToClipboard('<?php echo $currentUrl; ?>')" title="Sao chép liên kết"><i class="bi bi-link-45deg"></i></button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Styling nội dung trang (giữ tỷ lệ ảnh, font chữ...) */
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 15px 0;
    }
    .post-content h2, .post-content h3 {
        margin-top: 30px;
        margin-bottom: 15px;
        font-weight: 700;
        color: #0d6efd;
    }
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
