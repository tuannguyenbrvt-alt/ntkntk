<!-- ===== HOMEPAGE REDESIGNED 2026 - NTKNTK EDTECH ECOSYSTEM ===== -->
<style>
  :root {
    --primary-glow: #0284c7;
    --accent-glow: #38bdf8;
    --bg-dark-card: rgba(15, 23, 42, 0.75);
  }

  /* Animations */
  @keyframes floatSlow {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-12px) rotate(1deg); }
  }
  @keyframes pulseGlow {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.06); }
  }
  @keyframes borderShimmer {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  .text-gradient-cyan {
    background: linear-gradient(135deg, #38bdf8 0%, #818cf8 50%, #c084fc 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .text-gradient-gold {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .glass-card {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .glass-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(14, 165, 233, 0.12);
    border-color: #38bdf8;
  }

  .course-tab-btn {
    transition: all 0.25s ease;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-weight: 600;
    padding: 8px 20px;
    border-radius: 9999px;
    background: #ffffff;
  }
  .course-tab-btn.active, .course-tab-btn:hover {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
  }

  .course-item {
    transition: all 0.35s ease;
  }
</style>

<!-- ===== 1. HERO SECTION 2.0 (MODERN EDTECH & AI SHOWCASE) ===== -->
<section style="background: radial-gradient(circle at 80% 20%, #1e3a8a 0%, #0f172a 50%, #030712 100%); min-height: 88vh; position: relative; overflow: hidden; display: flex; align-items: center;" class="py-5">
  <!-- Glowing Ambient Background Blobs -->
  <div style="position: absolute; top: -120px; right: 5%; width: 550px; height: 550px; background: radial-gradient(circle, rgba(56, 189, 248, 0.18), transparent 70%); border-radius: 50%; filter: blur(40px); animation: pulseGlow 6s infinite ease-in-out;"></div>
  <div style="position: absolute; bottom: -100px; left: 5%; width: 480px; height: 480px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent 70%); border-radius: 50%; filter: blur(50px); animation: pulseGlow 7s infinite ease-in-out 1.5s;"></div>

  <div class="container py-4 position-relative" style="z-index: 2;">
    <div class="row align-items-center g-5">
      
      <!-- Cột Trái: Tiêu Đề, Định Vị & Nút Hành Động -->
      <div class="col-lg-7">
        
        <!-- Live Badge -->
        <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-2 rounded-pill" style="background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.35); backdrop-filter: blur(8px);">
          <span style="width: 8px; height: 8px; background: #38bdf8; border-radius: 50%; box-shadow: 0 0 12px #38bdf8; display: inline-block;"></span>
          <span class="fw-bold" style="color: #7dd3fc; font-size: 13px; letter-spacing: 0.5px;">🚀 HỆ SINH THÁI GIÁO DỤC CÔNG NGHỆ &amp; EDTECH 2026</span>
        </div>

        <h1 class="display-4 fw-black text-white lh-sm mb-3" style="font-family: 'Inter', sans-serif; letter-spacing: -0.5px;">
          Nâng Tầm Tri Thức <span class="text-gradient-cyan">Kỷ Nguyên AI</span><br>
          Làm Chủ Công Nghệ &amp; Tương Lai
        </h1>

        <p class="fs-5 text-light opacity-75 mb-4 lh-base" style="max-width: 620px;">
          Đào tạo chuyên sâu <strong>Lập trình thực chiến</strong>, kỹ năng ứng dụng <strong>Trí tuệ nhân tạo (AI)</strong>, <strong>Tin học quốc tế</strong> &amp; Cung cấp <strong>Giải pháp Phần mềm phòng máy LAN</strong> độc quyền cho giáo viên và trường học.
        </p>

        <!-- CTA Buttons -->
        <div class="d-flex flex-wrap gap-3 mb-4">
          <a href="#khoa-hoc" class="btn btn-lg fw-bold px-4 py-3 rounded-pill text-white shadow-lg" style="background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%); border: none; font-size: 15px;">
            📚 Khám Phá Khóa Học
          </a>
          <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-lg fw-bold px-4 py-3 rounded-pill shadow" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); color: #ffffff; border: none; font-size: 15px;">
            ⚡ Combo Phần Mềm LAN (Dùng Thử)
          </a>
          <a href="https://zalo.me/0979875712" target="_blank" class="btn btn-lg fw-semibold px-4 py-3 rounded-pill" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); font-size: 15px;">
            💬 Hỗ Trợ Zalo: 0979 875 712
          </a>
        </div>

        <!-- Quick Trust Micro-stats -->
        <div class="d-flex align-items-center gap-4 pt-2 text-white-50 small flex-wrap">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-patch-check-fill text-info fs-5"></i>
            <span>100% Thực hành trên máy</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
            <span>Hệ thống 100% Offline LAN</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-success fs-5"></i>
            <span>5,000+ Học viên tin chọn</span>
          </div>
        </div>

      </div>

      <!-- Cột Phải: Visual Dashboard Mô Phỏng Hệ Sinh Thái -->
      <div class="col-lg-5">
        <div class="p-4 rounded-4 position-relative" style="background: rgba(15, 23, 42, 0.7); border: 1px solid rgba(56, 189, 248, 0.3); backdrop-filter: blur(20px); box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5); animation: floatSlow 6s ease-in-out infinite;">
          
          <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle" style="width: 12px; height: 12px; background: #ef4444; display: inline-block;"></span>
              <span class="rounded-circle" style="width: 12px; height: 12px; background: #f59e0b; display: inline-block;"></span>
              <span class="rounded-circle" style="width: 12px; height: 12px; background: #10b981; display: inline-block;"></span>
              <span class="text-white-50 ms-2 small fw-semibold">NTKNTK EdTech Control Center</span>
            </div>
            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 small">● Live Hệ Thống</span>
          </div>

          <!-- Feature 1 Preview -->
          <div class="p-3 mb-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <span class="fs-4">🚀</span>
                <div>
                  <h6 class="text-white mb-0 fw-bold">SpaceTyper LAN Edition</h6>
                  <p class="text-white-50 small mb-0">Đấu trường gõ 10 ngón realtime (Leaderboard)</p>
                </div>
              </div>
              <span class="badge bg-info bg-opacity-25 text-info">Zero Latency</span>
            </div>
          </div>

          <!-- Feature 2 Preview -->
          <div class="p-3 mb-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <span class="fs-4">📝</span>
                <div>
                  <h6 class="text-white mb-0 fw-bold">WebTrợGiảng AutoQuiz</h6>
                  <p class="text-white-50 small mb-0">Nạp đề Word trong 5s • Xuất Excel chuẩn Bộ</p>
                </div>
              </div>
              <span class="badge bg-warning bg-opacity-25 text-warning">Auto Grade</span>
            </div>
          </div>

          <!-- Feature 3 Preview -->
          <div class="p-3 mb-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <span class="fs-4">🤖</span>
                <div>
                  <h6 class="text-white mb-0 fw-bold">Khoá Học Lập Trình &amp; AI</h6>
                  <p class="text-white-50 small mb-0">Python, Scratch, AI thực chiến &amp; MOS</p>
                </div>
              </div>
              <span class="badge bg-primary bg-opacity-25 text-primary">Chứng Chỉ</span>
            </div>
          </div>

          <!-- Mini Stats Grid -->
          <div class="row g-2 pt-2 text-center">
            <div class="col-6">
              <div class="p-2 rounded-2" style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2);">
                <div class="fw-black text-info fs-5 mb-0">10+ Năm</div>
                <div class="text-white-50" style="font-size: 11px;">Kinh Nghiệm Giảng Dạy</div>
              </div>
            </div>
            <div class="col-6">
              <div class="p-2 rounded-2" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2);">
                <div class="fw-black text-success fs-5 mb-0">100%</div>
                <div class="text-white-50" style="font-size: 11px;">Hài Lòng &amp; Hỗ Trợ 1:1</div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===== 2. BỐN TRỤ CỘT GIÁ TRỊ VÀNG (CORE VALUE PILLARS) ===== -->
<section class="py-5" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
  <div class="container">
    <div class="row g-4">
      
      <div class="col-md-6 col-lg-3">
        <div class="glass-card p-4 rounded-4 h-100 shadow-sm border-0">
          <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #e0f2fe; color: #0284c7; font-size: 26px;">
            <i class="bi bi-code-slash"></i>
          </div>
          <h5 class="fw-bold mb-2 text-dark">Lập Trình &amp; AI Thực Chiến</h5>
          <p class="text-muted small mb-0">Từ tư duy thuật toán thiếu nhi (Scratch) đến Python nâng cao và kỹ năng làm chủ công cụ AI thế hệ mới.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="glass-card p-4 rounded-4 h-100 shadow-sm border-0">
          <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #fef3c7; color: #d97706; font-size: 26px;">
            <i class="bi bi-hdd-network"></i>
          </div>
          <h5 class="fw-bold mb-2 text-dark">Phần Mềm Phòng Máy LAN</h5>
          <p class="text-muted small mb-0">Chạy 100% nội bộ không cần Internet. 1 máy chủ giáo viên quản lý đồng loạt 40–60 máy trạm học sinh mượt mà.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="glass-card p-4 rounded-4 h-100 shadow-sm border-0">
          <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #dcfce7; color: #16a34a; font-size: 26px;">
            <i class="bi bi-award"></i>
          </div>
          <h5 class="fw-bold mb-2 text-dark">Luyện Thi MOS &amp; Chứng Chỉ</h5>
          <p class="text-muted small mb-0">Luyện giải đề thực chiến MOS Word, Excel, PowerPoint chuẩn quốc tế và ôn thi chứng chỉ CNTT cơ bản.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="glass-card p-4 rounded-4 h-100 shadow-sm border-0">
          <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #f3e8ff; color: #9333ea; font-size: 26px;">
            <i class="bi bi-translate"></i>
          </div>
          <h5 class="fw-bold mb-2 text-dark">Tiếng Anh Giao Tiếp &amp; Thi Cử</h5>
          <p class="text-muted small mb-0">Phương pháp phản xạ ngôn ngữ tự nhiên từ Thiếu nhi, THCS, THPT đến luyện thi vào Lớp 10 và Đại học.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===== 3. SPECIAL VIP SHOWCASE: COMBO PHẦN MỀM PHÒNG MÁY LAN ===== -->
<section class="py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); position: relative; overflow: hidden;">
  <div style="position: absolute; right: 0; top: 0; width: 400px; height: 100%; background: radial-gradient(circle, rgba(2, 132, 199, 0.15), transparent 70%); pointer-events: none;"></div>

  <div class="container py-3 position-relative" style="z-index: 2;">
    <div class="row align-items-center g-5">
      
      <!-- Cột Mô Tả Sản Phẩm -->
      <div class="col-lg-6">
        <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-1 rounded-pill" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4);">
          <span style="color: #fbbf24; font-size: 13px; font-weight: 700;">🏫 SẢN PHẨM MŨI NHỌN DÀNH CHO GIÁO VIÊN &amp; TRƯỜNG HỌC</span>
        </div>

        <h2 class="display-6 fw-black text-white mb-3 lh-sm">
          Combo Phần Mềm Phòng Máy LAN<br>
          <span class="text-gradient-cyan">SpaceTyper &amp; WebTrợGiảng</span>
        </h2>

        <p class="text-light opacity-75 fs-6 mb-4 lh-base">
          Giải pháp công nghệ chuyên biệt chấm dứt hoàn toàn tình trạng nghẽn mạng Internet giờ cao điểm tại phòng máy. 1 máy chủ giáo viên quản lý trọn gói toàn bộ lớp học với độ trễ 0 giây.
        </p>

        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex align-items-start gap-3">
            <span class="p-2 rounded-circle bg-primary bg-opacity-25 text-info mt-1"><i class="bi bi-controller"></i></span>
            <div>
              <strong class="text-white">SpaceTyper LAN:</strong>
              <span class="text-white-50"> Đấu trường gõ 10 ngón phi thuyền không gian, chống rớt điểm khi F5, bảng xếp hạng trực tiếp trên máy chiếu.</span>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3">
            <span class="p-2 rounded-circle bg-warning bg-opacity-25 text-warning mt-1"><i class="bi bi-file-earmark-spreadsheet"></i></span>
            <div>
              <strong class="text-white">WebTrợGiảng LAN:</strong>
              <span class="text-white-50"> Nạp đề thi từ file Word trong 5 giây, học sinh thi trắc nghiệm bấm giờ, tự động chấm và xuất Excel theo chuẩn Bộ GD&amp;ĐT.</span>
            </div>
          </div>
          <div class="d-flex align-items-start gap-3">
            <span class="p-2 rounded-circle bg-success bg-opacity-25 text-success mt-1"><i class="bi bi-shield-check"></i></span>
            <div>
              <strong class="text-white">Bảo Mật &amp; Tiết Kiệm:</strong>
              <span class="text-white-50"> Hoạt động 100% Offline, không phí thuê bao hàng tháng, bản quyền vĩnh viễn theo máy chủ.</span>
            </div>
          </div>
        </div>

        <div class="d-flex flex-wrap gap-3">
          <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-primary btn-lg fw-bold px-4 py-3 rounded-pill shadow-lg" style="background: #0284c7; border: none; font-size: 15px;">
            ⚡ Tải Dùng Thử 7 Ngày Miễn Phí
          </a>
          <a href="<?php echo APP_URL; ?>/post?slug=giai-phap-phong-may-tin-hoc-mang-lan-khong-can-internet" class="btn btn-outline-light btn-lg fw-semibold px-4 py-3 rounded-pill" style="font-size: 15px;">
            📖 Xem Chi Tiết Giải Pháp &amp; Video
          </a>
        </div>
      </div>

      <!-- Cột Ảnh Sản Phẩm 3D -->
      <div class="col-lg-6">
        <div class="position-relative">
          <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="d-block text-decoration-none">
            <img src="<?php echo APP_URL; ?>/uploads/posts/6ace3263b376a-1791898211.jpeg" alt="Combo Phần Mềm Phòng Máy Tin Học Mạng LAN" class="img-fluid rounded-4 shadow-lg border border-secondary border-opacity-25" style="transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
          </a>
          <div class="position-absolute bottom-0 start-0 m-4 p-3 rounded-3 text-white" style="background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(56, 189, 248, 0.3);">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-success">Hot Micro-SaaS</span>
              <small class="fw-bold">VietQR Kích Hoạt Tức Thì</small>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===== 4. KHÓA HỌC NỔI BẬT (FEATURED COURSES WITH DYNAMIC TABS) ===== -->
<section id="khoa-hoc" class="py-5" style="background: #ffffff;">
  <div class="container py-3">
    
    <div class="text-center mb-4">
      <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill" style="background: #e0f2fe; color: #0284c7; font-size: 13px; font-weight: 700;">
        🎓 CHƯƠNG TRÌNH ĐÀO TẠO THỰC CHIẾN
      </div>
      <h2 class="display-6 fw-black text-dark mb-2">Khóa Học Đang Mở Ghi Danh</h2>
      <p class="text-muted fs-6 mx-auto" style="max-width: 650px;">
        Lộ trình học tập bài bản từ vỡ lòng đến thành thạo, kết hợp công nghệ hiện đại và sự đồng hành sát sao của giảng viên.
      </p>

      <!-- Category Filter Tabs -->
      <div class="d-flex justify-content-center flex-wrap gap-2 mt-4" id="courseFilterTabs">
        <button class="course-tab-btn active" data-filter="all">Tất Cả Khóa Học</button>
        <button class="course-tab-btn" data-filter="lap-trinh">💻 Lập Trình &amp; AI</button>
        <button class="course-tab-btn" data-filter="tin-hoc">📊 Tin Học &amp; MOS</button>
        <button class="course-tab-btn" data-filter="tieng-anh">🇬🇧 Tiếng Anh</button>
      </div>
    </div>

    <!-- Course Grid from DB -->
    <?php if (!empty($featuredCourses)): ?>
    <div class="row g-4 mt-2" id="courseGrid">
      <?php foreach ($featuredCourses as $course): 
        $titleLower = mb_strtolower($course['title'], 'UTF-8');
        $descLower = mb_strtolower($course['description'] ?? '', 'UTF-8');
        
        // Auto categorize
        $cat = 'tin-hoc';
        $badgeName = 'Tin Học';
        $badgeClass = 'bg-success';
        
        if (strpos($titleLower, 'python') !== false || strpos($titleLower, 'scratch') !== false || strpos($titleLower, 'lập trình') !== false || strpos($titleLower, 'ai') !== false || strpos($descLower, 'lập trình') !== false) {
            $cat = 'lap-trinh';
            $badgeName = 'Lập Trình & AI';
            $badgeClass = 'bg-primary';
        } elseif (strpos($titleLower, 'tiếng anh') !== false || strpos($titleLower, 'anh văn') !== false || strpos($titleLower, 'english') !== false) {
            $cat = 'tieng-anh';
            $badgeName = 'Tiếng Anh';
            $badgeClass = 'bg-info text-dark';
        } elseif (strpos($titleLower, 'mos') !== false || strpos($titleLower, 'excel') !== false || strpos($titleLower, 'word') !== false || strpos($titleLower, 'powerpoint') !== false) {
            $cat = 'tin-hoc';
            $badgeName = 'Chứng Chỉ MOS';
            $badgeClass = 'bg-warning text-dark';
        }
      ?>
      <div class="col-md-6 col-lg-4 course-item" data-category="<?php echo $cat; ?>">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative glass-card">
          
          <!-- Category Badge -->
          <div class="position-absolute top-0 start-0 m-3" style="z-index: 3;">
            <span class="badge <?php echo $badgeClass; ?> rounded-pill px-3 py-2 shadow-sm font-monospace" style="font-size: 11px; font-weight: 700;">
              <?php echo $badgeName; ?>
            </span>
          </div>

          <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>" class="text-decoration-none">
            <?php if(!empty($course['thumbnail'])): ?>
              <img src="<?php echo APP_URL . '/' . $course['thumbnail']; ?>" class="card-img-top object-fit-cover" style="height: 195px;" alt="<?php echo htmlspecialchars($course['title']); ?>">
            <?php else: ?>
              <div class="d-flex align-items-center justify-content-center" style="height: 195px; background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);">
                <i class="bi bi-code-square text-primary" style="font-size: 3.5rem;"></i>
              </div>
            <?php endif; ?>
          </a>

          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <div>
              <h5 class="fw-bold mb-2 lh-sm">
                <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 48px;">
                  <?php echo htmlspecialchars($course['title']); ?>
                </a>
              </h5>
              <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                <?php echo strip_tags($course['description'] ?? 'Khoá học thực hành bài bản, cung cấp kiến thức nền tảng và nâng cao vững chắc.'); ?>
              </p>
            </div>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted d-block" style="font-size: 11px;">Học Phí</small>
                <span class="fw-black text-danger fs-5">
                  <?php echo $course['price'] > 0 ? number_format($course['price']) . ' đ' : 'Miễn Phí'; ?>
                </span>
              </div>
              <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                Xem Lộ Trình →
              </a>
            </div>

          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-5">
      <a href="<?php echo APP_URL; ?>/courses" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm" style="background: #0284c7; border: none;">
        Xem Toàn Bộ Danh Sách Khóa Học (<?php echo APP_URL; ?>/courses) →
      </a>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- ===== 5. TẠI SAO CHỌN TRUNG TÂM NGUYỄN MINH (NTKNTK)? ===== -->
<section class="py-5" style="background: #f1f5f9;">
  <div class="container py-3">
    <div class="text-center mb-5">
      <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill" style="background: #e2e8f0; color: #334155; font-size: 13px; font-weight: 700;">
        ⭐ GIÁ TRỊ KHÁC BIỆT
      </div>
      <h2 class="display-6 fw-black text-dark mb-2">Tại Sao Hơn 5,000+ Học Viên Tin Tưởng NTKNTK?</h2>
      <p class="text-muted fs-6 mx-auto" style="max-width: 600px;">
        Chất lượng đào tạo là kim chỉ nam. Chúng tôi xây dựng môi trường học tập hiện đại, thực chất và truyền cảm hứng.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="p-4 bg-white rounded-4 h-100 shadow-sm border-0 text-center">
          <div class="mb-3" style="font-size: 40px;">🎯</div>
          <h5 class="fw-bold mb-2">Giáo Trình Thực Chiến</h5>
          <p class="text-muted small mb-0">Học là làm được việc ngay. Không lý thuyết suông, chú trọng dự án và bài tập thực hành trên máy tính 100% thời lượng.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-4 bg-white rounded-4 h-100 shadow-sm border-0 text-center">
          <div class="mb-3" style="font-size: 40px;">👨‍🏫</div>
          <h5 class="fw-bold mb-2">Giảng Viên Tâm Huyết</h5>
          <p class="text-muted small mb-0">Thầy Nguyễn Minh Tuấn cùng đội ngũ giáo viên giàu kinh nghiệm, kèm cặp 1:1, giải đáp cặn kẽ từng vướng mắc.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-4 bg-white rounded-4 h-100 shadow-sm border-0 text-center">
          <div class="mb-3" style="font-size: 40px;">🌐</div>
          <h5 class="fw-bold mb-2">Hệ Thống LMS Thông Minh</h5>
          <p class="text-muted small mb-0">Xem video bài giảng mọi lúc, nạp bài tập lũy tiến, tra cứu bảng điểm và tiến độ học tập minh bạch 24/7.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-4 bg-white rounded-4 h-100 shadow-sm border-0 text-center">
          <div class="mb-3" style="font-size: 40px;">🏅</div>
          <h5 class="fw-bold mb-2">Cam Kết Chuẩn Đầu Ra</h5>
          <p class="text-muted small mb-0">Hỗ trợ ôn luyện đến khi học viên tự tin vượt qua các kỳ thi trường lớp, thi MOS quốc tế hoặc kỳ thi tuyển sinh.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== 6. BÀI VIẾT CHUYÊN SÂU & TIÊU ĐIỂM CÔNG NGHỆ (LATEST POSTS) ===== -->
<?php if (!empty($latestPosts)): ?>
<section class="py-5" style="background: #ffffff;">
  <div class="container py-3">
    
    <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
      <div>
        <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill" style="background: #f3e8ff; color: #7e22ce; font-size: 13px; font-weight: 700;">
          💡 TIN TỨC &amp; NGHIÊN CỨU CÔNG NGHỆ
        </div>
        <h2 class="display-6 fw-black text-dark mb-0">Bài Viết Mới &amp; Hướng Dẫn</h2>
      </div>
      <a href="<?php echo APP_URL; ?>/blog" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">
        Xem Tất Cả Bài Viết →
      </a>
    </div>

    <div class="row g-4">
      <?php foreach ($latestPosts as $post): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden glass-card">
          <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" class="text-decoration-none">
            <?php if(!empty($post['thumbnail'])): ?>
              <img src="<?php echo APP_URL . '/' . $post['thumbnail']; ?>" class="card-img-top object-fit-cover" style="height: 180px;" alt="<?php echo htmlspecialchars($post['title']); ?>">
            <?php else: ?>
              <div style="height: 180px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-newspaper text-secondary" style="font-size: 3rem;"></i>
              </div>
            <?php endif; ?>
          </a>
          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                <i class="bi bi-calendar3"></i>
                <span><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                <?php if(!empty($post['is_pinned'])): ?>
                  <span class="badge bg-danger rounded-pill px-2 py-1 ms-auto">Ghim Nổi Bật</span>
                <?php endif; ?>
              </div>
              <h6 class="fw-bold mb-2 lh-sm">
                <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" class="text-dark text-decoration-none" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 42px;">
                  <?php echo htmlspecialchars($post['title']); ?>
                </a>
              </h6>
            </div>
            <div class="pt-3 border-top mt-2">
              <a href="<?php echo APP_URL; ?>/post?slug=<?php echo $post['slug']; ?>" class="text-primary fw-bold text-decoration-none small">
                Đọc Bài Viết →
              </a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>
<?php endif; ?>

<!-- ===== 7. STRATEGIC BOTTOM CONVERSION BANNER (HOTLINE & ZALO) ===== -->
<section style="background: linear-gradient(135deg, #0284c7 0%, #1e40af 100%); padding: 75px 0;" class="text-white">
  <div class="container text-center">
    <div class="d-inline-flex p-3 rounded-circle bg-white bg-opacity-10 mb-3 fs-3">
      ✨
    </div>
    <h2 class="display-6 fw-black mb-3">Sẵn Sàng Bứt Phá Năng Lực Cùng NTKNTK?</h2>
    <p class="fs-5 opacity-90 mb-4 mx-auto" style="max-width: 650px;">
      Liên hệ ngay để nhận lộ trình tư vấn cá nhân hóa cho học sinh, hoặc đặt lịch demo giải pháp phần mềm phòng máy LAN trường học.
    </p>

    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="tel:0979875712" class="btn btn-warning btn-lg fw-bold px-5 py-3 rounded-pill shadow-lg text-dark" style="font-size: 16px;">
        ☎️ Hotline: 0979 875 712
      </a>
      <a href="https://zalo.me/0979875712" target="_blank" class="btn btn-light btn-lg fw-bold px-5 py-3 rounded-pill shadow-lg" style="color: #0284c7; font-size: 16px;">
        💬 Chat Zalo Trực Tiếp
      </a>
      <a href="https://fb.com/NguyenMinh.edu.vn" target="_blank" class="btn btn-outline-light btn-lg fw-semibold px-4 py-3 rounded-pill" style="font-size: 16px;">
        📘 Fanpage Facebook
      </a>
    </div>
  </div>
</section>

<!-- ===== TAB FILTER JAVASCRIPT ===== -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('#courseFilterTabs .course-tab-btn');
    const courseItems = document.querySelectorAll('#courseGrid .course-item');

    filterButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        filterButtons.forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filterValue = this.getAttribute('data-filter');

        courseItems.forEach(item => {
          if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
            item.style.display = 'block';
            setTimeout(() => { item.style.opacity = '1'; item.style.transform = 'scale(1)'; }, 10);
          } else {
            item.style.opacity = '0';
            item.style.transform = 'scale(0.95)';
            setTimeout(() => { item.style.display = 'none'; }, 200);
          }
        });
      });
    });
  });
</script>
