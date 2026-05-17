<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold display-5">Tin tức & Sự kiện</h1>
            <div class="mx-auto" style="width: 60px; height: 4px; background: #0d6efd; border-radius: 2px;"></div>
            <p class="text-muted mt-3">Những thông tin cập nhật mới nhất từ Trung Tâm Ngoại Ngữ Tin Học Nguyễn Minh</p>
        </div>
    </div>
    
    <div class="row g-4">
        <?php foreach ($posts as $post): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden" style="transition: transform 0.3s; cursor: pointer;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>">
                        <?php if ($post['thumbnail']): ?>
                            <img src="<?php echo APP_URL . '/' . $post['thumbnail']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>" style="height: 220px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 220px;">
                                <i class="bi bi-newspaper text-muted opacity-50" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary me-auto">Tin tức</span>
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
                        </div>
                        <h5 class="card-title fw-bold mb-3">
                            <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" class="text-dark text-decoration-none">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted mb-4" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo strip_tags($post['content']); ?>
                        </p>
                        <div class="mt-auto d-flex align-items-center pt-3 border-top">
                            <i class="bi bi-person-circle text-muted fs-4 me-2"></i>
                            <span class="text-muted small fw-semibold"><?php echo htmlspecialchars($post['author_name']); ?></span>
                            <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" class="ms-auto text-primary text-decoration-none fw-bold small">Đọc tiếp <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if(empty($posts)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x display-1 text-muted opacity-25"></i>
                <h4 class="text-muted mt-3">Chưa có bài viết nào được đăng.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>
