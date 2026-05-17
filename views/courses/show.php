<div class="bg-dark text-white pt-5 pb-4">
    <div class="container pt-4 pb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/" class="text-decoration-none text-white-50">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/courses" class="text-decoration-none text-white-50">Khóa học</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($course['title']); ?></li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold mb-3 lh-base"><?php echo htmlspecialchars($course['title']); ?></h1>
                <p class="lead text-white-50 mb-4"><?php echo strip_tags(substr($course['description'], 0, 200)); ?>...</p>
                
                <div class="d-flex flex-wrap align-items-center mb-4">
                    <div class="d-flex align-items-center me-4 mb-2">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($course['author_name']); ?>&background=random" class="rounded-circle me-2 border border-2 border-white" width="40" height="40">
                        <div>
                            <small class="text-white-50 d-block lh-1">Giảng viên</small>
                            <strong><?php echo htmlspecialchars($course['author_name']); ?></strong>
                        </div>
                    </div>
                    <div class="d-flex align-items-center me-4 mb-2">
                        <i class="bi bi-play-btn fs-2 me-2 text-primary"></i>
                        <div>
                            <small class="text-white-50 d-block lh-1">Số bài học</small>
                            <strong><?php echo $totalLessons; ?> bài</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div class="card border-0 shadow-lg" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 1rem; overflow:hidden;">
                    <?php if ($course['thumbnail']): ?>
                        <img src="<?php echo APP_URL . '/' . $course['thumbnail']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body p-4 text-center">
                        <?php if($course['price'] > 0): ?>
                            <h2 class="fw-bold text-white mb-0"><?php echo number_format($course['price']); ?> đ</h2>
                            <?php if($course['original_price']): ?>
                                <small class="text-white-50 text-decoration-line-through fs-6"><?php echo number_format($course['original_price']); ?> đ</small>
                            <?php endif; ?>
                        <?php else: ?>
                            <h2 class="fw-bold text-success mb-0">MIỄN PHÍ</h2>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <?php if ($isEnrolled === true): ?>
                                <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $course['id']; ?>" class="btn btn-success btn-lg w-100 fw-bold rounded-pill py-3"><i class="bi bi-play-fill me-1 fs-5 align-middle"></i> VÀO HỌC NGAY</a>
                            <?php elseif ($isEnrolled === 'pending'): ?>
                                <button class="btn btn-warning btn-lg w-100 fw-bold rounded-pill py-3 disabled"><i class="bi bi-hourglass-split me-1 align-middle"></i> ĐANG CHỜ DUYỆT</button>
                                <div class="mt-3 text-white-50 small"><i class="bi bi-info-circle"></i> Vui lòng chờ Admin duyệt giao dịch chuyển khoản của bạn.</div>
                            <?php else: ?>
                                <a href="<?php echo APP_URL; ?>/enrollment/checkout?course_id=<?php echo $course['id']; ?>" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill py-3"><i class="bi bi-cart-check me-2 align-middle fs-5"></i> ĐĂNG KÝ HỌC</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 pe-lg-5">
            <h3 class="fw-bold mb-4 position-relative pb-2" style="border-bottom: 3px solid #f8f9fa;"><span style="position:absolute; bottom:-3px; left:0; width: 60px; height:3px; background:#0d6efd;"></span>Giới thiệu khóa học</h3>
            <div class="post-content lh-lg mb-4" style="font-size: 1.1rem; color: #444;">
                <?php echo $course['description']; ?>
            </div>

            <div class="d-flex align-items-center gap-3 mb-5 p-3 bg-light rounded-4">
                <?php 
                    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $shareTitle = urlencode($course['title']);
                    $shareUrl = urlencode($currentUrl);
                ?>
                <span class="fw-bold text-muted small me-1">Chia sẻ khóa học:</span>
                <div class="share-buttons d-flex align-items-center gap-2">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle" title="Chia sẻ Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareTitle; ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-circle" title="Chia sẻ Twitter"><i class="bi bi-twitter"></i></a>
                    <a href="mailto:?subject=<?php echo $shareTitle; ?>&body=<?php echo $shareUrl; ?>" class="btn btn-sm btn-outline-danger rounded-circle" title="Gửi qua Email"><i class="bi bi-envelope"></i></a>
                    <button class="btn btn-sm btn-outline-secondary rounded-circle" onclick="copyToClipboard('<?php echo $currentUrl; ?>')" title="Sao chép liên kết"><i class="bi bi-link-45deg"></i></button>
                </div>
            </div>
            
            <h3 class="fw-bold mb-4 position-relative pb-2" style="border-bottom: 3px solid #f8f9fa;"><span style="position:absolute; bottom:-3px; left:0; width: 60px; height:3px; background:#0d6efd;"></span>Đề cương khóa học</h3>
            <div class="accordion accordion-flush" id="syllabusAccordion">
                <?php $i = 0; foreach($parts as $part): ?>
                    <div class="mb-4 bg-light rounded-4 p-4 border border-light shadow-sm">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bookmark-star text-warning me-2"></i>Phần: <?php echo htmlspecialchars($part['title']); ?></h5>
                        <?php foreach($part['chapters'] as $chapter): $i++; ?>
                            <div class="accordion-item mb-2 border rounded bg-white overflow-hidden">
                                <h2 class="accordion-header">
                                    <button class="accordion-button <?php echo $i!=1 ? 'collapsed' : ''; ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $chapter['id']; ?>">
                                        <i class="bi bi-journal-text text-primary me-2"></i> Chương <?php echo $i; ?>: <?php echo htmlspecialchars($chapter['title']); ?>
                                        <span class="ms-auto badge bg-primary bg-opacity-10 text-primary rounded-pill me-2 fw-normal px-3"><?php echo count($chapter['lessons']); ?> bài</span>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $chapter['id']; ?>" class="accordion-collapse collapse <?php echo $i==1 ? 'show' : ''; ?>" data-bs-parent="#syllabusAccordion">
                                    <div class="accordion-body p-0">
                                        <ul class="list-group list-group-flush">
                                            <?php foreach($chapter['lessons'] as $lesson): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-play-circle text-secondary fs-5 me-3"></i>
                                                        <span class="text-dark"><?php echo htmlspecialchars($lesson['title']); ?></span>
                                                    </div>
                                                    <?php if($lesson['is_free_preview']): ?>
                                                        <!-- Ở Giai đoạn 2 ta sẽ làm LearningController -->
                                                        <a href="<?php echo APP_URL; ?>/learning/preview?lesson_id=<?php echo $lesson['id']; ?>" class="btn btn-sm btn-success bg-opacity-10 text-success border-success rounded-pill px-3 fw-semibold">Học thử</a>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($parts)): ?>
                    <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i> Khóa học đang trong quá trình cập nhật nội dung.</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4 mt-5 mt-lg-0">
            <!-- Sidebar widget -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Khóa học này dành cho ai?</h5>
                    <ul class="list-unstyled mb-0 text-muted">
                        <li class="mb-3 d-flex"><i class="bi bi-check-circle-fill text-success fs-5 me-3 align-middle"></i> <span>Học sinh, sinh viên cần nền tảng vững chắc.</span></li>
                        <li class="mb-3 d-flex"><i class="bi bi-check-circle-fill text-success fs-5 me-3 align-middle"></i> <span>Người đi làm muốn nâng cao nghiệp vụ nhanh chóng.</span></li>
                    </ul>
                    
                    <hr class="my-4">
                    
                    <h5 class="fw-bold mb-4">Khóa học bao gồm:</h5>
                    <ul class="list-unstyled mb-0 text-muted">
                        <li class="mb-3"><i class="bi bi-camera-video text-primary fs-5 me-3 align-middle"></i> Thời lượng video không giới hạn</li>
                        <li class="mb-3"><i class="bi bi-phone text-primary fs-5 me-3 align-middle"></i> Truy cập trên mọi thiết bị di động/PC</li>
                        <li class="mb-3"><i class="bi bi-infinity text-primary fs-5 me-3 align-middle"></i> Sở hữu khóa học trọn đời</li>
                        <li class="mb-3"><i class="bi bi-patch-check text-primary fs-5 me-3 align-middle"></i> Cấp chứng chỉ sau khi hoàn thành</li>
                        <li><i class="bi bi-chat-dots text-primary fs-5 me-3 align-middle"></i> Hỗ trợ giải đáp thắc mắc 1-1</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.post-content img { max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: 20px 0;}
.post-content p { margin-bottom: 1.5rem; }
.accordion-button:not(.collapsed) { background-color: #f8f9fa; color: #0d6efd; }
.accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,.125); }
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
