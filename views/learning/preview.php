<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="fw-bold mb-4"><span class="badge bg-success me-2 rounded-pill px-3 py-2">Học thử</span><?php echo htmlspecialchars($current_lesson['title']); ?></h2>
            <div class="card border-0 shadow-lg overflow-hidden rounded-4">
                <div class="card-body p-0 bg-dark text-white">
                    <?php foreach($current_items as $item): ?>
                        <?php if($item['type'] == 'video'): ?>
                            <?php 
                                $url = $item['content'];
                                $embed_url = '';
                                if(strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
                                    $youtube_id = $match[1] ?? '';
                                    $embed_url = "https://www.youtube.com/embed/{$youtube_id}";
                                } elseif(strpos($url, 'vimeo.com') !== false) {
                                    $vimeo_id = substr(parse_url($url, PHP_URL_PATH), 1);
                                    $embed_url = "https://player.vimeo.com/video/{$vimeo_id}";
                                }
                            ?>
                            <?php if($embed_url): ?>
                                <div class="ratio ratio-16x9">
                                    <iframe src="<?php echo $embed_url; ?>" title="Video player" allowfullscreen></iframe>
                                </div>
                            <?php endif; ?>
                        <?php elseif($item['type'] == 'text'): ?>
                            <div class="bg-white text-dark p-5 rounded-bottom" style="font-size:1.1rem;">
                                <?php echo $item['content']; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="card-footer bg-light p-4 text-center border-top">
                    <h5 class="fw-bold mb-3 mt-2 text-dark">Bạn thấy bài giảng bổ ích?</h5>
                    <p class="text-muted mb-4">Đăng ký toàn bộ khóa học để mở khóa tất cả các bài học và nhận hỗ trợ 1-1 từ giảng viên.</p>
                    <a href="javascript:history.back()" class="btn btn-primary rounded-pill px-5 py-3 fw-bold fs-5 shadow-sm"><i class="bi bi-cart-check me-2"></i>ĐĂNG KÝ KHÓA HỌC NGAY</a>
                </div>
            </div>
        </div>
    </div>
</div>
