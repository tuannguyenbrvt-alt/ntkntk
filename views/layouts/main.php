<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    require_once ROOT_PATH . '/helpers/SEOHelper.php';
    echo SEOHelper::generateMetaTags(
        $title ?? APP_NAME,
        $seo_desc ?? '',
        $seo_image ?? '',
        $seo_url ?? ''
    );
    ?>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow"
        style="background:#000;border-bottom:2px solid #c9a84c;padding-top:4px;padding-bottom:4px;">
        <div class="container">
            <a class="navbar-brand p-0 me-4" href="<?php echo APP_URL; ?>/">
                <img src="<?php echo APP_URL; ?>/assets/images/logo.png" alt="<?php echo APP_NAME; ?>"
                    style="height:70px;width:auto;object-fit:contain;" loading="eager">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="target"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php
                    require_once ROOT_PATH . '/helpers/MenuHelper.php';
                    $menuDb = Database::getInstance()->getConnection();
                    $menuStmt = $menuDb->query("SELECT * FROM menus ORDER BY sort_order ASC");
                    $allMenus = $menuStmt->fetchAll();
                    $frontendMenuTree = MenuHelper::buildTree($allMenus);
                    echo MenuHelper::renderFrontendMenu($frontendMenuTree);
                    ?>
                    <!-- Search -->
                    <li class="nav-item ms-2">
                        <form action="<?php echo APP_URL; ?>/search" method="GET" class="d-flex">
                            <input
                                class="form-control form-control-sm rounded-pill bg-white bg-opacity-25 border-0 text-white me-1"
                                type="search" name="q" placeholder="Tìm kiếm..." style="width:150px;"
                                value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                            <button class="btn btn-sm btn-outline-light rounded-pill px-3" type="submit"><i
                                    class="bi bi-search"></i></button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-vh-100">
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer
        style="background:linear-gradient(180deg,#0d1117 0%,#010409 100%);color:#cdd9e5;padding:56px 0 0;border-top:3px solid #c9a84c;">
        <div class="container">
            <div class="row g-4">

                <!-- Cột 1: Thông tin -->
                <div class="col-lg-3 col-md-6">
                    <a href="<?php echo APP_URL; ?>/" class="d-block mb-3">
                        <img src="<?php echo APP_URL; ?>/assets/images/logo.png" alt="<?php echo APP_NAME; ?>"
                            style="height:60px;width:auto;">
                    </a>
                    <p style="color:#8b949e;font-size:.88rem;line-height:1.7;margin-bottom:18px;">
                        Trung tâm đào tạo Ngoại ngữ &amp; Tin học uy tín.<br>
                        <em>"Khơi nguồn tri thức – Vững bước tương lai"</em>.
                    </p>
                    <ul style="list-style:none;padding:0;margin:0 0 20px;display:flex;flex-direction:column;gap:9px;">
                        <li style="display:flex;align-items:center;gap:9px;"><span style="color:#c9a84c;">📍</span><span
                                style="color:#8b949e;font-size:.88rem;">Long Điền,TP. Hồ Chí Minh</span></li>
                        <li style="display:flex;align-items:center;gap:9px;"><span style="color:#c9a84c;">☎️</span><a
                                href="tel:0397883255" style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">0397
                                883 255</a></li>
                        <li style="display:flex;align-items:center;gap:9px;"><span style="color:#c9a84c;">📧</span><a
                                href="mailto:tuannguyen.brvt@gmail.com"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'"
                                onmouseout="this.style.color='#8b949e'">tuannguyen.brvt@gmail.com</a></li>
                        <li style="display:flex;align-items:center;gap:9px;"><span style="color:#c9a84c;">⏰</span><span
                                style="color:#8b949e;font-size:.88rem;">Thứ 2 – Thứ 7: 7:30 – 20:30</span></li>
                    </ul>
                    <p style="color:#cdd9e5;font-weight:700;margin-bottom:10px;font-size:.92rem;">Mạng xã hội</p>
                    <div style="display:flex;gap:9px;">
                        <a href="https://fb.com/NguyenMinh.edu.vn" target="_blank" title="Facebook"
                            style="width:36px;height:36px;border-radius:8px;background:#1877f2;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:1rem;transition:transform .2s;"
                            onmouseover="this.style.transform='translateY(-3px)'"
                            onmouseout="this.style.transform=''">📘</a>
                        <a href="https://zalo.me/0397883255" target="_blank" title="Zalo"
                            style="width:36px;height:36px;border-radius:8px;background:#0068ff;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:1rem;transition:transform .2s;"
                            onmouseover="this.style.transform='translateY(-3px)'"
                            onmouseout="this.style.transform=''">💬</a>
                        <a href="https://youtube.com" target="_blank" title="YouTube"
                            style="width:36px;height:36px;border-radius:8px;background:#ff0000;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:1rem;transition:transform .2s;"
                            onmouseover="this.style.transform='translateY(-3px)'"
                            onmouseout="this.style.transform=''">▶️</a>
                        <a href="https://tiktok.com" target="_blank" title="TikTok"
                            style="width:36px;height:36px;border-radius:8px;background:#111;border:1px solid #333;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:1rem;transition:transform .2s;"
                            onmouseover="this.style.transform='translateY(-3px)'"
                            onmouseout="this.style.transform=''">🎵</a>
                    </div>
                </div>

                <!-- Cột 2: Khóa học & Liên kết -->
                <div class="col-lg-3 col-md-6">
                    <h5
                        style="color:#fff;font-weight:800;margin-bottom:16px;font-size:1rem;padding-bottom:8px;border-bottom:2px solid #c9a84c;display:inline-block;">
                        Khóa Học</h5>
                    <ul style="list-style:none;padding:0;margin:0 0 20px;display:flex;flex-direction:column;gap:8px;">
                        <li><a href="<?php echo APP_URL; ?>/courses"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">🇬🇧
                                Tiếng Anh Thiếu Nhi</a></li>
                        <li><a href="<?php echo APP_URL; ?>/courses"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">🇬🇧
                                Tiếng Anh THCS / THPT</a></li>
                        <li><a href="<?php echo APP_URL; ?>/courses"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">🎯
                                Luyện Thi Lớp 10 &amp; ĐH</a></li>
                        <li><a href="<?php echo APP_URL; ?>/courses"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">💻 Tin
                                Học Thiếu Nhi</a></li>
                        <li><a href="<?php echo APP_URL; ?>/courses"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">🐍 Lập
                                Trình Scratch / Python</a></li>
                        <li><a href="<?php echo APP_URL; ?>/courses"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">🤖 Tin
                                Học Ứng Dụng AI</a></li>
                        <li><a href="<?php echo APP_URL; ?>/courses"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">🏅
                                Luyện Thi MOS &amp; HSG</a></li>
                        <li><a href="<?php echo APP_URL; ?>/courses"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">🌐 Lập
                                Trình Web / App</a></li>
                    </ul>
                    <h5
                        style="color:#fff;font-weight:800;margin-bottom:12px;font-size:1rem;padding-bottom:8px;border-bottom:2px solid #c9a84c;display:inline-block;">
                        Liên Kết Nhanh</h5>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:7px;">
                        <li><a href="<?php echo APP_URL; ?>/post?slug=gioi-thieu"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">ℹ️ Giới
                                thiệu</a></li>
                        <li><a href="<?php echo APP_URL; ?>/blog"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">📰 Tin
                                tức</a></li>
                        <li><a href="<?php echo APP_URL; ?>/post?slug=lien-he"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">📞 Liên
                                hệ</a></li>
                        <li><a href="<?php echo APP_URL; ?>/post?slug=chinh-sach-bao-mat"
                                style="color:#8b949e;text-decoration:none;font-size:.88rem;"
                                onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#8b949e'">🔒
                                Chính sách bảo mật</a></li>
                    </ul>
                </div>

                <!-- Cột 3: Đăng ký tư vấn -->
                <div class="col-lg-3 col-md-6">
                    <h5
                        style="color:#fff;font-weight:800;margin-bottom:16px;font-size:1rem;padding-bottom:8px;border-bottom:2px solid #c9a84c;display:inline-block;">
                        Đăng Ký Tư Vấn</h5>
                    <p style="color:#8b949e;font-size:.85rem;line-height:1.6;margin-bottom:14px;">Để lại thông tin,
                        chúng tôi sẽ liên hệ tư vấn miễn phí cho bạn!</p>
                    <form id="footer-consult-form" style="display:flex;flex-direction:column;gap:10px;">
                        <input type="text" name="full_name" placeholder="Họ và tên"
                            style="background:#161b22;border:1px solid #30363d;color:#cdd9e5;padding:9px 13px;border-radius:7px;font-size:.88rem;outline:none;"
                            onfocus="this.style.borderColor='#c9a84c'" onblur="this.style.borderColor='#30363d'">
                        <input type="tel" name="phone" placeholder="Số điện thoại *" required
                            style="background:#161b22;border:1px solid #30363d;color:#cdd9e5;padding:9px 13px;border-radius:7px;font-size:.88rem;outline:none;"
                            onfocus="this.style.borderColor='#c9a84c'" onblur="this.style.borderColor='#30363d'">
                        <select name="course"
                            style="background:#161b22;border:1px solid #30363d;color:#8b949e;padding:9px 13px;border-radius:7px;font-size:.88rem;outline:none;"
                            onfocus="this.style.borderColor='#c9a84c'" onblur="this.style.borderColor='#30363d'">
                            <option value="">-- Khóa học quan tâm --</option>
                            <option>Tiếng Anh Thiếu Nhi</option>
                            <option>Tiếng Anh THCS / THPT</option>
                            <option>Luyện Thi Lớp 10 / Đại Học</option>
                            <option>Tin Học Thiếu Nhi</option>
                            <option>Lập Trình Scratch / Python</option>
                            <option>Tin Học Ứng Dụng AI</option>
                            <option>Luyện Thi MOS</option>
                            <option>Lập Trình Web / App</option>
                        </select>
                        <button type="submit"
                            style="background:linear-gradient(90deg,#ffd700,#ff8c00);color:#1a1a1a;font-weight:800;padding:10px;border-radius:7px;border:none;cursor:pointer;font-size:.92rem;"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            📨 Gửi Đăng Ký
                        </button>
                        <div id="footer-form-msg"
                            style="display:none;text-align:center;font-size:.83rem;font-weight:700;color:#ffd700;">
                        </div>
                    </form>
                    <p style="color:#8b949e;font-size:.8rem;margin-top:8px;text-align:center;">Hoặc gọi: <a
                            href="tel:0397883255" style="color:#ffd700;font-weight:700;text-decoration:none;">0397 883
                            255</a></p>
                </div>

                <!-- Cột 4: Fanpage Facebook -->
                <div class="col-lg-3 col-md-6">
                    <h5
                        style="color:#fff;font-weight:800;margin-bottom:16px;font-size:1rem;padding-bottom:8px;border-bottom:2px solid #c9a84c;display:inline-block;">
                        Fanpage Facebook</h5>
                    <div style="border-radius:10px;overflow:hidden;border:1px solid #30363d;margin-bottom:12px;">
                        <iframe
                            src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2FNguyenMinh.edu.vn&tabs&width=260&height=200&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true"
                            width="100%" height="200" style="border:none;overflow:hidden;display:block;" scrolling="no"
                            frameborder="0" allowfullscreen="true"
                            allow="autoplay;clipboard-write;encrypted-media;picture-in-picture;web-share"></iframe>
                    </div>
                    <a href="https://fb.com/NguyenMinh.edu.vn" target="_blank"
                        style="display:flex;align-items:center;justify-content:center;gap:8px;background:#1877f2;color:#fff;font-weight:700;padding:9px;border-radius:8px;text-decoration:none;font-size:.9rem;"
                        onmouseover="this.style.background='#1558b0'" onmouseout="this.style.background='#1877f2'">
                        👍 Theo dõi Fanpage
                    </a>
                </div>

            </div>
        </div>

        <!-- Bottom bar -->
        <div style="border-top:1px solid #21262d;margin-top:40px;padding:16px 0;background:#010409;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p style="color:#6e7681;font-size:.83rem;margin:0;">&copy; <?php echo date('Y'); ?> <strong
                                style="color:#c9a84c;"><?php echo APP_NAME; ?></strong>. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end mt-1 mt-md-0">
                        <a href="<?php echo APP_URL; ?>/post?slug=chinh-sach-bao-mat"
                            style="color:#6e7681;font-size:.83rem;text-decoration:none;margin-left:14px;"
                            onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#6e7681'">Chính sách
                            bảo mật</a>
                        <a href="<?php echo APP_URL; ?>/post?slug=lien-he"
                            style="color:#6e7681;font-size:.83rem;text-decoration:none;margin-left:14px;"
                            onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#6e7681'">Liên hệ</a>
                        <a href="<?php echo APP_URL; ?>/sitemap.xml"
                            style="color:#6e7681;font-size:.83rem;text-decoration:none;margin-left:14px;"
                            onmouseover="this.style.color='#c9a84c'" onmouseout="this.style.color='#6e7681'">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('footer-consult-form')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const m = document.getElementById('footer-form-msg');
            const btn = this.querySelector('button[type=submit]');
            const fd = new FormData(this);
            btn.disabled = true;
            btn.textContent = '⏳ Đang gửi...';
            m.style.display = 'none';
            fetch('<?php echo APP_URL; ?>/consult/store', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    m.style.display = 'block';
                    m.style.color = data.ok ? '#4caf50' : '#f44336';
                    m.textContent = data.ok ? '✅ ' + data.msg : '❌ ' + data.msg;
                    if (data.ok) { document.getElementById('footer-consult-form').reset(); }
                })
                .catch(() => { m.style.display = 'block'; m.style.color = '#f44336'; m.textContent = '❌ Lỗi kết nối, thử lại sau.'; })
                .finally(() => { btn.disabled = false; btn.textContent = '📨 Gửi Đăng Ký'; setTimeout(() => m.style.display = 'none', 5000); });
        });
    </script>
</body>

</html>