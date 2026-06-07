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

    <!-- Floating Chat Widget -->
    <div id="floating-chat-widget">
        <!-- Nút Tròn Nhấp Nháy Mở Chat -->
        <button class="chat-btn-toggle shadow" id="btn-toggle-chat-widget" title="Hỗ trợ trực tuyến">
            <i class="bi bi-chat-dots-fill"></i>
            <i class="bi bi-x-lg"></i>
        </button>

        <!-- Khung Chat Widget -->
        <div class="chat-window" id="chat-window-widget">
            <!-- Header -->
            <div class="chat-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center text-white me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-size: 0.95rem;">Nguyễn Minh Support</h6>
                        <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;">Online</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" id="btn-close-chat-widget" style="font-size: 0.8rem;"></button>
            </div>

            <!-- Body -->
            <div class="chat-body d-flex flex-column" id="chat-body-widget">
                <!-- Màn hình 1: Cho khách nhập thông tin -->
                <div id="screen-guest-init" class="d-none my-auto">
                    <div class="text-center mb-4">
                        <i class="bi bi-chat-square-quote-fill text-warning fs-1"></i>
                        <h6 class="fw-bold mt-2">Xin chào!</h6>
                        <p class="text-muted small">Vui lòng nhập thông tin để bắt đầu trò chuyện hỗ trợ.</p>
                    </div>
                    <form id="form-guest-init">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-sm rounded-pill px-3" name="guest_name" id="guest-name-input" placeholder="Họ và tên của bạn *" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" class="form-control form-control-sm rounded-pill px-3" name="guest_phone" id="guest-phone-input" placeholder="Số điện thoại *" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-sm w-100 rounded-pill fw-bold text-dark py-2">
                            🚀 Bắt đầu trò chuyện
                        </button>
                    </form>
                </div>

                <!-- Màn hình 2: Chọn lớp học / giáo viên cho học sinh -->
                <div id="screen-student-select" class="d-none">
                    <h6 class="fw-bold mb-3 small"><i class="bi bi-arrow-right-circle-fill text-primary me-1"></i>Chọn người muốn trò chuyện:</h6>
                    <div class="list-group list-group-flush border rounded overflow-hidden shadow-sm" id="student-thread-list-group">
                        <!-- Render qua JS -->
                    </div>
                </div>

                <!-- Màn hình 3: Khung trò chuyện chat chính -->
                <div id="screen-chat-messages" class="d-none d-flex flex-column h-100">
                    <div class="d-flex align-items-center mb-2 border-bottom pb-2">
                        <button class="btn btn-sm btn-link p-0 text-muted text-decoration-none d-none" id="btn-back-to-select">
                            <i class="bi bi-arrow-left-short fs-5"></i> Quay lại
                        </button>
                        <span class="ms-auto small text-muted" id="chat-thread-title">Đang kết nối...</span>
                    </div>
                    <div class="flex-grow-1 overflow-y-auto d-flex flex-column py-2" id="chat-messages-widget-list" style="max-height: 290px;">
                        <!-- Danh sách tin nhắn -->
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="chat-footer d-none" id="chat-footer-widget">
                <!-- Preview attachment -->
                <div id="widget-attachment-preview" class="p-1 bg-light border rounded mb-1 d-none d-flex align-items-center justify-content-between" style="font-size: 0.75rem;">
                    <span id="widget-attachment-name" class="text-truncate text-muted"><i class="bi bi-paperclip"></i>File.pdf</span>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 m-0" id="btn-cancel-widget-attachment"><i class="bi-x-circle-fill"></i></button>
                </div>
                <form id="form-send-message-widget" class="d-flex gap-1.5 align-items-center m-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" id="btn-widget-upload" style="width: 34px; height: 34px; padding: 0;" title="Đính kèm tệp">
                        <i class="bi bi-paperclip"></i>
                    </button>
                    <input type="file" id="widget-file-input" class="d-none">
                    
                    <input type="text" id="widget-message-input" class="form-control form-control-sm rounded-pill px-3 shadow-none border-secondary-subtle" placeholder="Nhập tin nhắn..." autocomplete="off">
                    
                    <button type="submit" class="btn btn-sm btn-warning rounded-circle d-flex align-items-center justify-content-center" id="btn-widget-send" style="width: 34px; height: 34px; padding: 0;">
                        <i class="bi bi-send-fill text-dark" style="font-size: 0.85rem;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        #floating-chat-widget {
            position: fixed;
            bottom: 95px; /* Tránh đè lên nút gọi điện ở góc dưới */
            right: 25px;
            z-index: 10000;
        }
        .chat-btn-toggle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffd700, #ff8c00);
            border: 2px solid #fff;
            color: #1a1a1a;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .chat-btn-toggle:hover {
            transform: scale(1.06);
            box-shadow: 0 5px 15px rgba(255, 140, 0, 0.4);
        }
        .chat-btn-toggle .bi-x-lg { display: none; }
        .chat-btn-toggle.active .bi-chat-dots-fill { display: none; }
        .chat-btn-toggle.active .bi-x-lg { display: block; }
        
        .chat-window {
            width: 345px;
            height: 480px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 215, 0, 0.4);
            border-radius: 16px;
            position: absolute;
            bottom: 70px;
            right: 0;
            display: none;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.18);
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.3s, transform 0.3s;
        }
        .chat-window.show {
            display: flex;
            opacity: 1;
            transform: translateY(0);
        }
        .chat-header {
            background: #000;
            color: #fff;
            border-bottom: 2px solid #c9a84c;
            padding: 12px 16px;
        }
        .chat-body {
            flex-grow: 1;
            overflow-y: auto;
            padding: 14px;
            background: #f7f9fa;
        }
        .chat-footer {
            padding: 10px;
            background: #fff;
            border-top: 1px solid #eee;
        }
        .chat-bubble-client {
            background: linear-gradient(135deg, #c9a84c, #b59238);
            color: #fff;
            align-self: flex-end;
            border-radius: 14px 14px 2px 14px;
            padding: 8px 12px;
            font-size: 0.85rem;
            max-width: 82%;
            margin-bottom: 2px;
            word-wrap: break-word;
            box-shadow: 0 1.5px 3px rgba(201, 168, 76, 0.2);
        }
        .chat-bubble-admin {
            background: #ffffff;
            color: #2b2b2b;
            align-self: flex-start;
            border-radius: 14px 14px 14px 2px;
            padding: 8px 12px;
            font-size: 0.85rem;
            max-width: 82%;
            margin-bottom: 2px;
            word-wrap: break-word;
            border: 1px solid #eef0f2;
            box-shadow: 0 1.5px 3px rgba(0,0,0,0.04);
        }
        .chat-time-widget {
            font-size: 0.65rem;
            color: #8c98a5;
            margin-bottom: 8px;
        }
        .widget-attach-card {
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.04);
            padding: 6px 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: inherit;
            margin-bottom: 4px;
            font-size: 0.78rem;
        }
        .chat-bubble-client .widget-attach-card {
            background: rgba(255, 255, 255, 0.16);
            color: #fff !important;
        }
    </style>

    <script>
    (function() {
        const IS_LOGGED_IN = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const APP_URL = <?php echo json_encode(APP_URL); ?>;
        
        let activeThreadId = sessionStorage.getItem('chat_active_thread_id') || null;
        let selectedCourseId = null; 
        let pollInterval = null;

        const btnToggle = document.getElementById('btn-toggle-chat-widget');
        const chatWin = document.getElementById('chat-window-widget');
        const btnClose = document.getElementById('btn-close-chat-widget');
        const chatBody = document.getElementById('chat-body-widget');
        
        const guestInitScreen = document.getElementById('screen-guest-init');
        const studentSelectScreen = document.getElementById('screen-student-select');
        const chatMessagesScreen = document.getElementById('screen-chat-messages');
        const chatFooter = document.getElementById('chat-footer-widget');
        const backBtn = document.getElementById('btn-back-to-select');
        const chatMsgList = document.getElementById('chat-messages-widget-list');
        const threadTitleSpan = document.getElementById('chat-thread-title');

        // Toggle chat window
        btnToggle.addEventListener('click', function() {
            const isShowing = chatWin.classList.contains('show');
            if (isShowing) {
                chatWin.classList.remove('show');
                btnToggle.classList.remove('active');
                setTimeout(() => chatWin.style.display = 'none', 300);
                if (pollInterval) clearInterval(pollInterval);
            } else {
                chatWin.style.display = 'flex';
                setTimeout(() => {
                    chatWin.classList.add('show');
                    btnToggle.classList.add('active');
                }, 10);
                initWidgetScreen();
            }
        });

        btnClose.addEventListener('click', () => btnToggle.click());

        // Chọn màn hình khởi động dựa trên trạng thái đăng nhập
        function initWidgetScreen() {
            guestInitScreen.classList.add('d-none');
            studentSelectScreen.classList.add('d-none');
            chatMessagesScreen.classList.add('d-none');
            chatFooter.classList.add('d-none');
            
            if (activeThreadId) {
                // Đã có thread đang nhắn tin dở
                openChatThread(activeThreadId);
            } else if (IS_LOGGED_IN) {
                // Học viên: hiện màn hình chọn giáo viên/lớp học
                loadStudentSelection();
            } else {
                // Khách vãng lai: hiện màn điền thông tin
                guestInitScreen.classList.remove('d-none');
            }
        }

        // Tải danh sách giáo viên/khóa học cho học viên
        function loadStudentSelection() {
            studentSelectScreen.classList.remove('d-none');
            const listGroup = document.getElementById('student-thread-list-group');
            listGroup.innerHTML = '<div class="text-center py-4 small text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Đang tải...</div>';

            fetch(`${APP_URL}/chat/active-threads`)
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        let html = '';
                        
                        // Lựa chọn 1: Chat hỗ trợ chung với Admin
                        // Kiểm tra xem đã có sẵn thread với Admin chưa
                        const adminThread = data.threads.find(t => t.course_id == null);
                        const adminThreadId = adminThread ? adminThread.id : '';
                        
                        html += `
                            <a href="#" class="list-group-item list-group-item-action py-2.5 small d-flex align-items-center justify-content-between select-target-btn" 
                               data-thread-id="${adminThreadId}" data-course-id="">
                                <div>
                                    <div class="fw-bold">💬 Tư vấn & Hỗ trợ chung</div>
                                    <span class="text-muted" style="font-size: 0.72rem;">Bộ phận Quản trị viên</span>
                                </div>
                                <i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i>
                            </a>
                        `;

                        // Lựa chọn theo từng khóa học
                        data.courses.forEach(c => {
                            const courseThread = data.threads.find(t => t.course_id == c.course_id);
                            const courseThreadId = courseThread ? courseThread.id : '';
                            
                            html += `
                                <a href="#" class="list-group-item list-group-item-action py-2.5 small d-flex align-items-center justify-content-between select-target-btn" 
                                   data-thread-id="${courseThreadId}" data-course-id="${c.course_id}" data-title="${c.course_title}">
                                    <div>
                                        <div class="fw-bold">👨‍🏫 GV: ${c.teacher_name}</div>
                                        <span class="text-muted" style="font-size: 0.72rem;">Môn: ${c.course_title}</span>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted" style="font-size: 0.75rem;"></i>
                                </a>
                            `;
                        });

                        listGroup.innerHTML = html;

                        // Click chọn mục chat
                        listGroup.querySelectorAll('.select-target-btn').forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                const threadId = this.getAttribute('data-thread-id');
                                const courseId = this.getAttribute('data-course-id');
                                const title = this.getAttribute('data-title') || 'Tư vấn chung';
                                
                                if (threadId) {
                                    // Đã có thread sẵn trong DB, mở trực tiếp
                                    openChatThread(threadId);
                                } else {
                                    // Chưa có thread, lưu lại course_id và chuyển sang giao diện chat để người dùng gửi tin nhắn đầu tiên tạo thread
                                    selectedCourseId = courseId;
                                    activeThreadId = null;
                                    
                                    studentSelectScreen.classList.add('d-none');
                                    chatMessagesScreen.classList.remove('d-none');
                                    chatFooter.classList.remove('d-none');
                                    
                                    backBtn.classList.remove('d-none');
                                    threadTitleSpan.textContent = title;
                                    chatMsgList.innerHTML = '<div class="text-center text-muted my-auto py-5 small">Bắt đầu nhập tin nhắn để kết nối với giáo viên...</div>';
                                }
                            });
                        });
                    }
                })
                .catch(err => {
                    listGroup.innerHTML = '<div class="text-center py-4 text-danger small">Không thể tải dữ liệu. Thử lại sau!</div>';
                });
        }

        // Đăng ký chat cho khách vãng lai
        const guestInitForm = document.getElementById('form-guest-init');
        guestInitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            const submitBtn = this.querySelector('button[type=submit]');
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Đang khởi tạo...';

            fetch(`${APP_URL}/chat/init`, {
                method: 'POST',
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    openChatThread(data.thread_id);
                } else {
                    alert(data.error);
                }
            })
            .catch(() => alert('Có lỗi xảy ra, vui lòng thử lại.'))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = '🚀 Bắt đầu trò chuyện';
            });
        });

        // Mở phòng chat hoạt động
        function openChatThread(threadId) {
            activeThreadId = threadId;
            sessionStorage.setItem('chat_active_thread_id', threadId);

            guestInitScreen.classList.add('d-none');
            studentSelectScreen.classList.add('d-none');
            chatMessagesScreen.classList.remove('d-none');
            chatFooter.classList.remove('d-none');

            if (IS_LOGGED_IN) {
                backBtn.classList.remove('d-none');
            } else {
                backBtn.classList.add('d-none');
            }

            threadTitleSpan.textContent = 'Đang tải tin nhắn...';
            loadWidgetMessages(threadId, true);

            // Bắt đầu polling
            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(() => {
                loadWidgetMessages(threadId, false);
            }, 3000);
        }

        // Quay lại màn hình chọn lớp học cho học viên
        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (pollInterval) clearInterval(pollInterval);
            activeThreadId = null;
            selectedCourseId = null;
            sessionStorage.removeItem('chat_active_thread_id');
            chatMessagesScreen.classList.add('d-none');
            chatFooter.classList.add('d-none');
            studentSelectScreen.classList.remove('d-none');
        });

        // Tải danh sách tin nhắn
        function loadWidgetMessages(threadId, shouldScroll = false) {
            if (!threadId) return;
            const isAtBottom = (chatMsgList.scrollTop + chatMsgList.clientHeight >= chatMsgList.scrollHeight - 35);

            fetch(`${APP_URL}/chat/messages?thread_id=${threadId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        threadTitleSpan.textContent = 'Hỗ trợ trực tuyến';
                        let html = '';
                        
                        if (data.messages.length === 0) {
                            html = '<div class="text-center text-muted my-auto py-5 small">Gửi tin nhắn đầu tiên để bắt đầu cuộc trò chuyện.</div>';
                        } else {
                            data.messages.forEach(msg => {
                                const isMe = msg.sender_id !== null && msg.sender_id !== '' && IS_LOGGED_IN ? (msg.sender_id == <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : -1; ?>) : (msg.sender_id == null);
                                const bubbleClass = isMe ? 'chat-bubble-client align-self-end' : 'chat-bubble-admin align-self-start';
                                const containerClass = isMe ? 'justify-content-end' : 'justify-content-start';
                                
                                let bubbleHtml = '';
                                if (msg.is_recalled == 1) {
                                    bubbleHtml = `
                                        <div class="chat-bubble-client ${bubbleClass} text-muted fst-italic shadow-none" style="background: rgba(220, 225, 230, 0.4); border: 1px dashed #ccc; color: #7f8c8d !important;">
                                            <i class="bi bi-trash3-fill me-1.5"></i>Tin nhắn đã bị thu hồi
                                        </div>
                                    `;
                                } else {
                                    let fileHtml = '';
                                    if (msg.file_name) {
                                        const link = msg.file_drive_url ? msg.file_drive_url : `${APP_URL}/${msg.file_path}`;
                                        const isImg = msg.file_path && msg.file_path.match(/\.(jpg|jpeg|png|gif|webp)$/i);
                                        
                                        if (isImg) {
                                            fileHtml = `
                                                <a href="${link}" target="_blank" class="d-block mb-1.5">
                                                    <img src="${APP_URL}/${msg.file_path}" class="rounded img-fluid" style="max-height: 140px; object-fit: contain;" alt="${msg.file_name}">
                                                </a>
                                            `;
                                        } else {
                                            fileHtml = `
                                                <a href="${link}" target="_blank" class="widget-attach-card text-decoration-none">
                                                    <i class="bi bi-file-earmark-arrow-down fs-5"></i>
                                                    <div class="overflow-hidden">
                                                        <div class="text-truncate fw-bold" style="font-size: 0.72rem; max-width: 160px;">${msg.file_name}</div>
                                                    </div>
                                                </a>
                                            `;
                                        }
                                    }
                                    bubbleHtml = `
                                        <div class="chat-bubble-client ${bubbleClass}">
                                            ${fileHtml}
                                            ${msg.message_text ? `<div>${msg.message_text}</div>` : ''}
                                        </div>
                                    `;
                                }

                                const dateObj = new Date(msg.created_at);
                                const timeStr = dateObj.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

                                let recallBtn = '';
                                if (isMe && msg.is_recalled == 0) {
                                    const timeDiffHours = (new Date().getTime() - dateObj.getTime()) / (3600 * 1000);
                                    if (timeDiffHours < 24) {
                                        recallBtn = `<span class="mx-1">•</span><a href="#" class="text-danger text-decoration-none btn-widget-recall-msg text-decoration-none" data-msg-id="${msg.id}" style="font-size: 0.65rem;">Thu hồi</a>`;
                                    }
                                }

                                html += `
                                    <div class="d-flex ${containerClass} w-100 mb-1">
                                        ${bubbleHtml}
                                    </div>
                                    <div class="d-flex ${containerClass} w-100">
                                        <small class="chat-time-widget px-1.5 ${isMe ? 'text-end' : 'text-start'}">${timeStr}${recallBtn}</small>
                                    </div>
                                `;
                            });
                        }

                        chatMsgList.innerHTML = html;

                        if (shouldScroll || isAtBottom) {
                            chatMsgList.scrollTop = chatMsgList.scrollHeight;
                        }
                    }
                })
                .catch(err => {
                    threadTitleSpan.textContent = 'Lỗi kết nối...';
                });
        }

        // Tệp đính kèm Widget
        const btnWidgetUpload = document.getElementById('btn-widget-upload');
        const widgetFileInput = document.getElementById('widget-file-input');
        const widgetPreview = document.getElementById('widget-attachment-preview');
        const widgetPreviewName = document.getElementById('widget-attachment-name');
        const btnCancelWidgetAttach = document.getElementById('btn-cancel-widget-attachment');

        btnWidgetUpload.addEventListener('click', () => widgetFileInput.click());
        widgetFileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                widgetPreviewName.innerHTML = `<i class="bi bi-paperclip me-1"></i>${file.name}`;
                widgetPreview.classList.remove('d-none');
            }
        });

        btnCancelWidgetAttach.addEventListener('click', () => {
            widgetFileInput.value = '';
            widgetPreview.classList.add('d-none');
        });

        // Bắt sự kiện click thu hồi tin nhắn trên widget chat
        chatMsgList.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-widget-recall-msg')) {
                e.preventDefault();
                const msgId = e.target.getAttribute('data-msg-id');

                if (!confirm('Bạn có chắc chắn muốn thu hồi tin nhắn này không? Tệp đính kèm cũng sẽ bị xóa vĩnh viễn.')) {
                    return;
                }

                const fd = new FormData();
                fd.append('message_id', msgId);

                fetch(`${APP_URL}/chat/recall`, {
                    method: 'POST',
                    body: fd
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        loadWidgetMessages(activeThreadId, false);
                    } else {
                        alert('Lỗi: ' + data.error);
                    }
                })
                .catch(() => alert('Có lỗi xảy ra khi thu hồi tin nhắn.'));
            }
        });

        // Gửi tin nhắn Widget
        const formSend = document.getElementById('form-send-message-widget');
        formSend.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const msgInput = document.getElementById('widget-message-input');
            const text = msgInput.value.trim();
            const hasFile = widgetFileInput.files && widgetFileInput.files.length > 0;

            if (!text && !hasFile) return;

            const fd = new FormData();
            if (activeThreadId) {
                fd.append('thread_id', activeThreadId);
            } else if (selectedCourseId) {
                fd.append('course_id', selectedCourseId);
            }
            fd.append('message_text', text);
            if (hasFile) {
                fd.append('attachment', widgetFileInput.files[0]);
            }

            const sendBtn = document.getElementById('btn-widget-send');
            sendBtn.disabled = true;
            sendBtn.innerHTML = `<span class="spinner-border spinner-border-sm text-dark" style="width: 14px; height: 14px;" role="status"></span>`;

            fetch(`${APP_URL}/chat/send`, {
                method: 'POST',
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    msgInput.value = '';
                    widgetFileInput.value = '';
                    widgetPreview.classList.add('d-none');
                    
                    if (!activeThreadId && data.message.thread_id) {
                        openChatThread(data.message.thread_id);
                    } else {
                        loadWidgetMessages(activeThreadId, true);
                    }
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(() => alert('Không thể gửi tin nhắn. Vui lòng kiểm tra kết nối!'))
            .finally(() => {
                sendBtn.disabled = false;
                sendBtn.innerHTML = `<i class="bi bi-send-fill text-dark" style="font-size: 0.85rem;"></i>`;
            });
        });
    })();
    </script>

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