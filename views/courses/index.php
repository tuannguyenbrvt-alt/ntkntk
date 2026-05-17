<div class="container py-5">
    <div class="row mb-5 text-center">
        <div class="col-12">
            <h1 class="fw-bold display-5 text-primary">Khóa học Chất lượng cao</h1>
            <div class="mx-auto" style="width: 60px; height: 4px; background: #0d6efd; border-radius: 2px;"></div>
            <p class="text-muted mt-3 lead">Đầu tư vào tri thức là khoản đầu tư sinh lời nhất</p>
        </div>
    </div>
    <div class="row g-4">
        <?php foreach ($courses as $course): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden" style="transition: 0.3s;" onmouseover="this.classList.add('shadow-lg', 'translate-middle-y')" onmouseout="this.classList.remove('shadow-lg', 'translate-middle-y')">
                    <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>">
                        <?php if ($course['thumbnail']): ?>
                            <img src="<?php echo APP_URL . '/' . $course['thumbnail']; ?>" class="card-img-top" style="height: 220px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 220px;">
                                <i class="bi bi-play-circle text-primary" style="font-size: 4rem;"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">E-Learning</span>
                        </div>
                        <h5 class="card-title fw-bold mb-3">
                            <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>" class="text-dark text-decoration-none lh-base"><?php echo htmlspecialchars($course['title']); ?></a>
                        </h5>
                        <div class="d-flex align-items-center text-muted mb-4 small fw-semibold">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($course['author_name']); ?>&background=random" class="rounded-circle me-2" width="28" height="28">
                            <?php echo htmlspecialchars($course['author_name']); ?>
                        </div>
                        <div class="mt-auto d-flex align-items-center justify-content-between pt-3 border-top">
                            <div class="price">
                                <?php if($course['price'] > 0): ?>
                                    <span class="fw-bold text-danger fs-5"><?php echo number_format($course['price']); ?>đ</span>
                                    <?php if($course['original_price']): ?>
                                        <small class="text-muted text-decoration-line-through ms-1 d-block" style="font-size:0.8rem;"><?php echo number_format($course['original_price']); ?>đ</small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="fw-bold text-success fs-5">Miễn phí</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Xem ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($courses)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-journal-x text-muted display-1 opacity-25"></i>
                <h4 class="text-muted mt-3">Chưa có khóa học nào được phát hành.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>
