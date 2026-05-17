<div class="container py-5">
    <!-- Thanh tìm kiếm -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <form action="<?php echo APP_URL; ?>/search" method="GET">
                <div class="input-group input-group-lg shadow-sm">
                    <input type="search" name="q" class="form-control border-2 rounded-start-pill" placeholder="Tìm khóa học, bài viết..." value="<?php echo htmlspecialchars($q); ?>" autofocus>
                    <button type="submit" class="btn btn-primary px-4 rounded-end-pill fw-bold"><i class="bi bi-search me-2"></i>Tìm</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($q)): ?>
        <p class="text-muted mb-4">Tìm thấy <strong><?php echo $totalResults; ?> kết quả</strong> cho từ khóa: <em>"<?php echo htmlspecialchars($q); ?>"</em></p>

        <!-- Khóa học -->
        <?php if (!empty($courses)): ?>
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-bookmark text-primary me-2"></i>Khóa học (<?php echo count($courses); ?>)</h5>
            <div class="row g-3 mb-5">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>" class="text-decoration-none">
                            <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                                <?php if(!empty($course['thumbnail'])): ?>
                                    <img src="<?php echo APP_URL . '/' . $course['thumbnail']; ?>" class="card-img-top rounded-top-4 object-fit-cover" style="height:150px;">
                                <?php else: ?>
                                    <div class="bg-primary bg-opacity-10 rounded-top-4 d-flex align-items-center justify-content-center" style="height:150px;"><i class="bi bi-journal-bookmark fs-1 text-primary"></i></div>
                                <?php endif; ?>
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($course['title']); ?></h6>
                                    <p class="text-danger fw-bold mb-0"><?php echo number_format($course['price']); ?> đ</p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Bài viết -->
        <?php if (!empty($posts)): ?>
            <h5 class="fw-bold mb-3"><i class="bi bi-newspaper text-success me-2"></i>Bài viết (<?php echo count($posts); ?>)</h5>
            <div class="list-group shadow-sm rounded-4 overflow-hidden mb-4">
                <?php foreach ($posts as $post): ?>
                    <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" class="list-group-item list-group-item-action border-0 border-bottom p-3">
                        <div class="d-flex gap-3">
                            <?php if(!empty($post['thumbnail'])): ?>
                                <img src="<?php echo APP_URL . '/' . $post['thumbnail']; ?>" class="rounded-3 object-fit-cover flex-shrink-0" style="width:70px;height:55px;">
                            <?php endif; ?>
                            <div>
                                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($post['title']); ?></h6>
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($totalResults === 0): ?>
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted opacity-50 d-block mb-3"></i>
                <h5 class="text-muted">Không tìm thấy kết quả nào cho <em>"<?php echo htmlspecialchars($q); ?>"</em></h5>
                <p class="text-muted">Hãy thử từ khóa khác hoặc xem <a href="<?php echo APP_URL; ?>/courses">tất cả khóa học</a>.</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-4 text-muted">Nhập từ khóa để bắt đầu tìm kiếm...</div>
    <?php endif; ?>
</div>

<style>
    .hover-lift { transition: transform .2s, box-shadow .2s; }
    .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,.12) !important; }
</style>
