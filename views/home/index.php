<!-- ===== HERO SECTION ===== -->
<section style="background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); min-height: 92vh; display: flex; align-items: center; position: relative; overflow: hidden;">
    <!-- Animated background blobs -->
    <div style="position:absolute;top:-100px;right:-100px;width:500px;height:500px;background:radial-gradient(circle,rgba(255,200,0,.15),transparent 70%);border-radius:50%;animation:pulse 4s ease-in-out infinite;"></div>
    <div style="position:absolute;bottom:-80px;left:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(0,200,255,.1),transparent 70%);border-radius:50%;animation:pulse 5s ease-in-out infinite 1s;"></div>

    <div class="container py-5 position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <!-- Badge -->
                <div class="d-inline-flex align-items-center gap-2 mb-4 px-3 py-2 rounded-pill" style="background:rgba(255,200,0,.15);border:1px solid rgba(255,200,0,.4);">
                    <span style="width:8px;height:8px;background:#ffd700;border-radius:50%;animation:pulse 1s infinite;display:inline-block;"></span>
                    <span class="fw-semibold" style="color:#ffd700;font-size:13px;letter-spacing:1px;">💥 BOM TẤN MÙA HÈ 2026</span>
                </div>

                <h1 class="display-4 fw-black text-white lh-sm mb-3" style="font-family:'Inter',sans-serif;">
                    Ưu Đãi <span style="background:linear-gradient(90deg,#ffd700,#ff8c00);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">"KÉP"</span><br>
                    Phát Triển Toàn Diện<br>
                    <span style="color:#4dd0e1;">Kỹ Năng Vàng!</span>
                </h1>

                <p class="text-white-50 fs-5 mb-4 lh-base">
                    Không cần phải chọn 1 trong 2! Mùa hè này, chỉ với <strong class="text-white">1 lần học phí</strong>,<br>con được trang bị trọn bộ "vũ khí" tri thức sắc bén nhất.
                </p>

                <!-- Promo Cards -->
                <div class="d-flex flex-column gap-3 mb-5">
                    <div class="d-flex align-items-center gap-3 px-4 py-3 rounded-3" style="background:linear-gradient(90deg,rgba(255,140,0,.25),rgba(255,140,0,.05));border:1px solid rgba(255,140,0,.4);">
                        <span style="font-size:28px;">🔥</span>
                        <div>
                            <p class="mb-0 fw-bold text-white">Đăng ký 1 khóa <span style="color:#ff8c00;">TIN HỌC</span></p>
                            <p class="mb-0 text-white-50 small">➡️ Tặng ngay 1 khóa <strong class="text-white">TIẾNG ANH</strong> hoàn toàn miễn phí!</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 px-4 py-3 rounded-3" style="background:linear-gradient(90deg,rgba(77,208,225,.2),rgba(77,208,225,.05));border:1px solid rgba(77,208,225,.35);">
                        <span style="font-size:28px;">🔥</span>
                        <div>
                            <p class="mb-0 fw-bold text-white">Đăng ký 1 khóa <span style="color:#4dd0e1;">TIẾNG ANH</span></p>
                            <p class="mb-0 text-white-50 small">➡️ Tặng ngay 1 khóa <strong class="text-white">TIN HỌC</strong> hoàn toàn miễn phí!</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo APP_URL; ?>/courses" class="btn btn-lg fw-bold px-5 py-3 rounded-pill shadow-lg" style="background:linear-gradient(90deg,#ffd700,#ff8c00);color:#1a1a1a;border:none;font-size:1rem;">
                        📚 Xem Khóa Học
                    </a>
                    <a href="https://fb.com/NguyenMinh.edu.vn" target="_blank" class="btn btn-lg fw-semibold px-5 py-3 rounded-pill" style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.3);backdrop-filter:blur(10px);font-size:1rem;">
                        💬 Inbox Fanpage
                    </a>
                </div>

                <!-- Hotline -->
                <div class="d-flex align-items-center gap-3 mt-4">
                    <a href="tel:0397883255" class="d-flex align-items-center gap-2 text-decoration-none">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:rgba(255,215,0,.2);border:1px solid rgba(255,215,0,.4);">
                            <span>📞</span>
                        </div>
                        <div>
                            <p class="mb-0 text-white-50 small">Zalo / Hotline</p>
                            <p class="mb-0 fw-bold text-white" style="font-size:1.1rem;">0397 883 255</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Right stats -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="row g-3">
                    <?php
                    $stats = [
                        ['icon'=>'🏆','num'=>'10+','label'=>'Năm kinh nghiệm','color'=>'rgba(255,215,0,.15)','border'=>'rgba(255,215,0,.3)'],
                        ['icon'=>'👨‍🎓','num'=>'5000+','label'=>'Học viên đã học','color'=>'rgba(77,208,225,.15)','border'=>'rgba(77,208,225,.3)'],
                        ['icon'=>'📖','num'=>'20+','label'=>'Khóa học chất lượng','color'=>'rgba(152,251,152,.15)','border'=>'rgba(152,251,152,.3)'],
                        ['icon'=>'⭐','num'=>'98%','label'=>'Học viên hài lòng','color'=>'rgba(255,140,0,.15)','border'=>'rgba(255,140,0,.3)'],
                    ];
                    foreach($stats as $s): ?>
                    <div class="col-6">
                        <div class="text-center p-4 rounded-3 h-100" style="background:<?php echo $s['color']; ?>;border:1px solid <?php echo $s['border']; ?>;backdrop-filter:blur(10px);">
                            <div style="font-size:2rem;"><?php echo $s['icon']; ?></div>
                            <h3 class="fw-black text-white my-1"><?php echo $s['num']; ?></h3>
                            <p class="text-white-50 small mb-0"><?php echo $s['label']; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== PROMO ALERT BANNER ===== -->
<div style="background:linear-gradient(90deg,#ff4500,#ff8c00,#ffd700,#ff8c00,#ff4500);background-size:300%;animation:shimmer 3s linear infinite;padding:14px 0;">
    <p class="text-center mb-0 fw-bold text-white" style="font-size:1rem;letter-spacing:.5px;">
        ⚠️ SỐ LƯỢNG SUẤT "MUA 1 TẶNG 1" CÓ HẠN — ƯU TIÊN ĐĂNG KÝ SỚM! &nbsp;|&nbsp; ☎️ 0397 883 255
    </p>
</div>

<!-- ===== COURSES SECTION ===== -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <!-- English -->
        <div class="text-center mb-5">
            <span class="badge rounded-pill px-4 py-2 mb-3 fs-6" style="background:linear-gradient(90deg,#0057b7,#004494);color:#fff;">🇬🇧 CHINH PHỤC TIẾNG ANH</span>
            <h2 class="fw-black fs-1">Tự Tin Giao Tiếp</h2>
            <p class="text-muted fs-5">Lộ trình bài bản từ thiếu nhi đến luyện thi chuyên sâu</p>
        </div>
        <div class="row g-4 mb-5">
            <?php
            $english = [
                ['icon'=>'👶','title'=>'Tiếng Anh Thiếu Nhi','desc'=>'Đánh thức phản xạ ngôn ngữ tự nhiên qua trò chơi, bài hát.','tag'=>'Thiếu nhi','color'=>'#0057b7'],
                ['icon'=>'📚','title'=>'Tiếng Anh THCS & THPT','desc'=>'Lấp lỗ hổng ngữ pháp, bứt tốc từ vựng theo từng cấp độ.','tag'=>'THCS / THPT','color'=>'#0057b7'],
                ['icon'=>'🎯','title'=>'Luyện thi Lớp 10 & Đại Học','desc'=>'Lộ trình cá nhân hóa, cam kết bao chuẩn đầu ra đầu vào.','tag'=>'Luyện thi','color'=>'#0057b7'],
            ];
            foreach($english as $c): ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition:transform .3s,box-shadow .3s;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 20px 50px rgba(0,87,183,.2)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="height:6px;background:<?php echo $c['color']; ?>;"></div>
                    <div class="card-body p-4">
                        <div class="mb-3 d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(0,87,183,.1);font-size:24px;"><?php echo $c['icon']; ?></div>
                            <span class="badge rounded-pill px-3 py-2" style="background:rgba(0,87,183,.1);color:<?php echo $c['color']; ?>;"><?php echo $c['tag']; ?></span>
                        </div>
                        <h5 class="fw-bold mb-2"><?php echo $c['title']; ?></h5>
                        <p class="text-muted mb-0"><?php echo $c['desc']; ?></p>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="<?php echo APP_URL; ?>/courses" class="btn btn-sm rounded-pill px-4 fw-semibold" style="background:rgba(0,87,183,.1);color:#0057b7;">Xem chi tiết →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- IT -->
        <div class="text-center mb-5">
            <span class="badge rounded-pill px-4 py-2 mb-3 fs-6" style="background:linear-gradient(90deg,#1a6e00,#258900);color:#fff;">💻 LÀM CHỦ CÔNG NGHỆ</span>
            <h2 class="fw-black fs-1">Thỏa Sức Sáng Tạo</h2>
            <p class="text-muted fs-5">Từ nền tảng vững chắc đến kỹ năng AI & lập trình chuyên nghiệp</p>
        </div>
        <div class="row g-4">
            <?php
            $it = [
                ['icon'=>'🖥️','title'=>'Tin Học Thiếu Nhi','desc'=>'Nền tảng vững chắc, an toàn trên không gian mạng.','tag'=>'Thiếu nhi','color'=>'#1a6e00'],
                ['icon'=>'🐍','title'=>'Lập Trình Scratch / Python','desc'=>'Khởi tạo tư duy logic, bước đầu làm coder thực thụ.','tag'=>'Lập trình','color'=>'#1a6e00'],
                ['icon'=>'🤖','title'=>'Tin Học Ứng Dụng AI','desc'=>'Bắt kịp làn sóng Trí tuệ nhân tạo thế hệ mới.','tag'=>'AI / Công nghệ','color'=>'#1a6e00'],
                ['icon'=>'🏅','title'=>'Luyện Thi MOS & HSG','desc'=>'Rinh chứng chỉ quốc tế, làm đẹp hồ sơ năng lực.','tag'=>'Luyện thi','color'=>'#1a6e00'],
                ['icon'=>'🌐','title'=>'Lập Trình Ứng Dụng / Web','desc'=>'Thiết kế sản phẩm thực tế, làm việc được ngay sau khóa.','tag'=>'Web / App','color'=>'#1a6e00'],
            ];
            foreach($it as $c): ?>
            <div class="col-md-4 col-lg-<?php echo count($it) <= 5 ? '4' : '4'; ?>">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition:transform .3s,box-shadow .3s;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 20px 50px rgba(26,110,0,.2)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                    <div style="height:6px;background:<?php echo $c['color']; ?>;"></div>
                    <div class="card-body p-4">
                        <div class="mb-3 d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:52px;height:52px;background:rgba(26,110,0,.1);font-size:24px;"><?php echo $c['icon']; ?></div>
                            <span class="badge rounded-pill px-3 py-2" style="background:rgba(26,110,0,.1);color:<?php echo $c['color']; ?>;"><?php echo $c['tag']; ?></span>
                        </div>
                        <h5 class="fw-bold mb-2"><?php echo $c['title']; ?></h5>
                        <p class="text-muted mb-0"><?php echo $c['desc']; ?></p>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0">
                        <a href="<?php echo APP_URL; ?>/courses" class="btn btn-sm rounded-pill px-4 fw-semibold" style="background:rgba(26,110,0,.1);color:#1a6e00;">Xem chi tiết →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== CTA SECTION ===== -->
<section style="background:linear-gradient(135deg,#1e3c72,#2a5298);padding:80px 0;">
    <div class="container text-center">
        <span style="font-size:3rem;">✨</span>
        <h2 class="fw-black text-white fs-1 mt-2 mb-3">Mùa hè trôi qua rất nhanh...</h2>
        <p class="text-white-50 fs-5 mb-4 mx-auto" style="max-width:650px;">
            Hãy trao cho con cơ hội để <strong class="text-white">"Khơi nguồn tri thức – Vững bước tương lai"</strong>.<br>
            Đội ngũ giáo viên tận tâm đã sẵn sàng đồng hành!
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="tel:0397883255" class="btn btn-lg fw-bold px-5 py-3 rounded-pill shadow" style="background:linear-gradient(90deg,#ffd700,#ff8c00);color:#1a1a1a;border:none;">
                ☎️ Gọi Ngay: 0397 883 255
            </a>
            <a href="https://zalo.me/0397883255" target="_blank" class="btn btn-lg fw-semibold px-5 py-3 rounded-pill" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.3);">
                💬 Nhắn Zalo
            </a>
            <a href="https://fb.com/NguyenMinh.edu.vn" target="_blank" class="btn btn-lg fw-semibold px-5 py-3 rounded-pill" style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.3);">
                📘 Fanpage Facebook
            </a>
        </div>
    </div>
</section>

<!-- ===== FEATURED COURSES FROM DB ===== -->
<?php if (!empty($featuredCourses)): ?>
<section class="py-5">
    <div class="container py-3">
        <div class="text-center mb-5">
            <h2 class="fw-black fs-1">Khóa học đang mở</h2>
            <p class="text-muted fs-5">Đăng ký ngay để nhận ưu đãi Mua 1 Tặng 1!</p>
        </div>
        <div class="row g-4">
            <?php foreach ($featuredCourses as $course): ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition:transform .3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform=''">
                        <?php if(!empty($course['thumbnail'])): ?>
                            <img src="<?php echo APP_URL . '/' . $course['thumbnail']; ?>" class="card-img-top object-fit-cover" style="height:185px;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center" style="height:185px;background:linear-gradient(135deg,#e8f0fe,#d2e3fc);">
                                <i class="bi bi-journal-bookmark-fill text-primary" style="font-size:3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-2 lh-sm"><?php echo htmlspecialchars($course['title']); ?></h6>
                            <p class="text-muted small mb-3 lh-base" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"><?php echo strip_tags($course['description'] ?? ''); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-black text-danger fs-5"><?php echo $course['price'] > 0 ? number_format($course['price']) . ' đ' : 'Miễn phí'; ?></span>
                                <span class="btn btn-sm btn-primary rounded-pill px-3">Xem ngay</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo APP_URL; ?>/courses" class="btn btn-outline-primary btn-lg rounded-pill px-5">Xem tất cả khóa học →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== LATEST POSTS ===== -->
<?php if (!empty($latestPosts)): ?>
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center mb-5">
            <h2 class="fw-black fs-1">Tin tức & Sự kiện</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($latestPosts as $post): ?>
            <div class="col-md-4">
                <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition:transform .3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform=''">
                        <?php if(!empty($post['thumbnail'])): ?>
                            <img src="<?php echo APP_URL . '/' . $post['thumbnail']; ?>" class="card-img-top object-fit-cover" style="height:170px;">
                        <?php else: ?>
                            <div style="height:170px;background:linear-gradient(135deg,#f3e5f5,#e1bee7);display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-newspaper text-purple" style="font-size:3rem;color:#7b1fa2;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body p-4">
                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></small>
                            <h6 class="fw-bold text-dark mt-2 mb-0 lh-sm"><?php echo htmlspecialchars($post['title']); ?></h6>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo APP_URL; ?>/blog" class="btn btn-outline-secondary btn-lg rounded-pill px-5">Xem tất cả bài viết →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== CONTACT STICKY FLOAT ===== -->
<a href="tel:0397883255" title="Gọi ngay" style="position:fixed;bottom:24px;right:24px;z-index:9999;width:58px;height:58px;background:linear-gradient(135deg,#25D366,#128C7E);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 25px rgba(37,211,102,.5);animation:bounce 2s ease-in-out infinite;text-decoration:none;">
    <span style="font-size:26px;">📞</span>
</a>

<style>
@keyframes pulse { 0%,100%{transform:scale(1);opacity:.8;} 50%{transform:scale(1.05);opacity:1;} }
@keyframes shimmer { 0%{background-position:0%} 100%{background-position:300%} }
@keyframes bounce { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-8px);} }
</style>
