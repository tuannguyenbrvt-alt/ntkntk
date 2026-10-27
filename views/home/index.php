<!-- ===== HOMEPAGE REDESIGNED 2026 - NTKNTK EDTECH & AI ECOSYSTEM ===== -->
<style>
  :root {
    --primary-glow: #0284c7;
    --accent-glow: #38bdf8;
    --bg-dark-card: rgba(15, 23, 42, 0.75);
  }

  /* Animations */
  @keyframes floatSlow {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(0.8deg); }
  }
  @keyframes pulseGlow {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.06); }
  }
  @keyframes shimmerBadge {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
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
  .text-gradient-green {
    background: linear-gradient(135deg, #34d399 0%, #10b981 50%, #059669 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .glass-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(14, 165, 233, 0.12);
    border-color: #38bdf8;
  }

  .glass-dark-card {
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(56, 189, 248, 0.25);
    transition: all 0.35s ease;
  }
  .glass-dark-card:hover {
    transform: translateY(-5px);
    border-color: #38bdf8;
    box-shadow: 0 15px 35px rgba(2, 132, 199, 0.25);
  }

  .software-badge-hot {
    background: linear-gradient(90deg, #ef4444, #f97316, #ef4444);
    background-size: 200% 100%;
    animation: shimmerBadge 3s infinite linear;
    color: #ffffff;
    font-weight: 800;
  }

  .course-tab-btn {
    transition: all 0.25s ease;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-weight: 600;
    padding: 9px 22px;
    border-radius: 9999px;
    background: #ffffff;
    cursor: pointer;
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

  .software-product-card {
    border-radius: 18px;
    transition: all 0.35s ease;
    border: 1px solid #e2e8f0;
    background: #ffffff;
  }
  .software-product-card:hover {
    transform: translateY(-7px);
    box-shadow: 0 20px 45px rgba(2, 132, 199, 0.12);
    border-color: #0284c7;
  }
</style>

<!-- ========================================================================= -->
<!-- 1. HERO SECTION 3.0: EDTECH, AI ASSISTANT & SOFTWARE ECOSYSTEM             -->
<!-- ========================================================================= -->
<section style="background: radial-gradient(circle at 80% 20%, #1e3a8a 0%, #0f172a 50%, #030712 100%); min-height: 90vh; position: relative; overflow: hidden; display: flex; align-items: center;" class="py-5">
  <!-- Glowing Ambient Background Blobs -->
  <div style="position: absolute; top: -120px; right: 5%; width: 550px; height: 550px; background: radial-gradient(circle, rgba(56, 189, 248, 0.18), transparent 70%); border-radius: 50%; filter: blur(40px); animation: pulseGlow 6s infinite ease-in-out;"></div>
  <div style="position: absolute; bottom: -100px; left: 5%; width: 480px; height: 480px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent 70%); border-radius: 50%; filter: blur(50px); animation: pulseGlow 7s infinite ease-in-out 1.5s;"></div>

  <div class="container py-4 position-relative" style="z-index: 2;">
    <div class="row align-items-center g-5">
      
      <!-- Cột Trái: Tiêu Đề, Định Vị & Nút Hành Động -->
      <div class="col-lg-7">
        
        <!-- Live Ecosystem Badge -->
        <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-2 rounded-pill" style="background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.35); backdrop-filter: blur(8px);">
          <span style="width: 8px; height: 8px; background: #38bdf8; border-radius: 50%; box-shadow: 0 0 12px #38bdf8; display: inline-block;"></span>
          <span class="fw-bold" style="color: #7dd3fc; font-size: 13px; letter-spacing: 0.5px;">🚀 HỆ SINH THÁI GIÁO DỤC CÔNG NGHỆ &amp; TRỢ LÝ AI 2026</span>
        </div>

        <h1 class="display-4 fw-black text-white lh-sm mb-3" style="font-family: 'Inter', system-ui, sans-serif; letter-spacing: -0.5px;">
          Nâng Tầm Tri Thức <span class="text-gradient-cyan">Kỷ Nguyên AI</span><br>
          Khóa Học Thực Chiến &amp; <span class="text-gradient-gold">Phần Mềm Đột Phá</span>
        </h1>

        <p class="fs-5 text-light opacity-75 mb-4 lh-base" style="max-width: 630px;">
          Đào tạo chuyên sâu <strong>Lập trình thực chiến, Trí tuệ nhân tạo (AI), Tin học quốc tế</strong> kết hợp Bộ giải pháp phần mềm độc quyền: <strong>Trợ Lý Soạn Giảng AI</strong>, <strong>Phần Mềm Phòng Máy LAN</strong> &amp; <strong>Quản Lý Kho Hàng</strong>.
        </p>

        <!-- CTA Buttons Group -->
        <div class="d-flex flex-wrap gap-3 mb-4">
          <a href="#tro-ly-soan-giang-ai" class="btn btn-lg fw-bold px-4 py-3 rounded-pill text-white shadow-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; font-size: 15px;">
            🤖 Trợ Lý Soạn Giảng AI (Mới)
          </a>
          <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-lg fw-bold px-4 py-3 rounded-pill text-white shadow-lg" style="background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%); border: none; font-size: 15px;">
            ⚡ Kho Phần Mềm &amp; Dùng Thử
          </a>
          <a href="#khoa-hoc" class="btn btn-lg fw-semibold px-4 py-3 rounded-pill text-white" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); font-size: 15px;">
            📚 Khóa Học Đang Mở
          </a>
          <a href="https://zalo.me/0979875712" target="_blank" class="btn btn-lg fw-semibold px-4 py-3 rounded-pill text-warning" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); font-size: 15px;">
            💬 Zalo: 0979 875 712
          </a>
        </div>

        <!-- Quick Trust Micro-stats -->
        <div class="d-flex align-items-center gap-4 pt-2 text-white-50 small flex-wrap">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-patch-check-fill text-info fs-5"></i>
            <span>100% Thực hành trên máy</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-robot text-success fs-5"></i>
            <span>Tích hợp Google Gemini AI</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
            <span>Hoạt động 100% Offline LAN</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-light fs-5"></i>
            <span>5,000+ Học viên tin chọn</span>
          </div>
        </div>

      </div>

      <!-- Cột Phải: Visual Dashboard Mô Phỏng Hệ Sinh Thái NTKNTK -->
      <div class="col-lg-5">
        <div class="p-4 rounded-4 position-relative" style="background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(56, 189, 248, 0.3); backdrop-filter: blur(20px); box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5); animation: floatSlow 6s ease-in-out infinite;">
          
          <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle" style="width: 12px; height: 12px; background: #ef4444; display: inline-block;"></span>
              <span class="rounded-circle" style="width: 12px; height: 12px; background: #f59e0b; display: inline-block;"></span>
              <span class="rounded-circle" style="width: 12px; height: 12px; background: #10b981; display: inline-block;"></span>
              <span class="text-white-50 ms-2 small fw-semibold">NTKNTK EdTech Control Center</span>
            </div>
            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 small">● Live Hệ Thống</span>
          </div>

          <!-- Feature 1: SP04 Trợ Lý Soạn Giảng AI (HOT) -->
          <a href="#tro-ly-soan-giang-ai" class="d-block text-decoration-none p-3 mb-2 rounded-3" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.35); transition: 0.25s;" onmouseover="this.style.background='rgba(16, 185, 129, 0.2)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.12)'">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <span class="fs-4">🤖</span>
                <div>
                  <h6 class="text-white mb-0 fw-bold">SP04 - Trợ Lý Soạn Giảng AI</h6>
                  <p class="text-white-50 small mb-0">Slide PowerPoint + Giáo án chuẩn 5512/2345</p>
                </div>
              </div>
              <span class="badge software-badge-hot rounded-pill px-2 py-1 small">HOT NEW 🔥</span>
            </div>
          </a>

          <!-- Feature 2: SpaceTyper LAN -->
          <div class="p-3 mb-2 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <span class="fs-4">🚀</span>
                <div>
                  <h6 class="text-white mb-0 fw-bold">SpaceTyper LAN Edition</h6>
                  <p class="text-white-50 small mb-0">Đấu trường gõ 10 ngón realtime phòng máy</p>
                </div>
              </div>
              <span class="badge bg-info bg-opacity-25 text-info">Zero Lag</span>
            </div>
          </div>

          <!-- Feature 3: WebTrợGiảng LAN -->
          <div class="p-3 mb-2 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <span class="fs-4">📝</span>
                <div>
                  <h6 class="text-white mb-0 fw-bold">WebTrợGiảng AutoQuiz</h6>
                  <p class="text-white-50 small mb-0">Nạp đề Word trong 5s • Xuất Excel chuẩn Bộ</p>
                </div>
              </div>
              <span class="badge bg-warning bg-opacity-25 text-warning">Chấm Điểm</span>
            </div>
          </div>

          <!-- Feature 4: SmartWarehouse Mini -->
          <div class="p-3 mb-3 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-3">
                <span class="fs-4">📦</span>
                <div>
                  <h6 class="text-white mb-0 fw-bold">SmartWarehouse Mini</h6>
                  <p class="text-white-50 small mb-0">Quản lý kho hàng &amp; công nợ không phí tháng</p>
                </div>
              </div>
              <span class="badge bg-secondary bg-opacity-50 text-light">Self-Hosted</span>
            </div>
          </div>

          <!-- Mini Stats Grid -->
          <div class="row g-2 pt-1 text-center">
            <div class="col-4">
              <div class="p-2 rounded-2" style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2);">
                <div class="fw-black text-info fs-6 mb-0">10+ Năm</div>
                <div class="text-white-50" style="font-size: 10px;">Kinh Nghiệm</div>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded-2" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2);">
                <div class="fw-black text-success fs-6 mb-0">5,000+</div>
                <div class="text-white-50" style="font-size: 10px;">Học Viên</div>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded-2" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                <div class="fw-black text-warning fs-6 mb-0">3 Giây</div>
                <div class="text-white-50" style="font-size: 10px;">Cấp Key Auto</div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 2. SPECIAL SPOTLIGHT: SP04 TRỢ LÝ SOẠN GIẢNG AI (MỚI RA MẮT 2026)          -->
<!-- ========================================================================= -->
<section id="tro-ly-soan-giang-ai" class="py-5" style="background: linear-gradient(135deg, #091e3a 0%, #0f172a 100%); position: relative; overflow: hidden; border-bottom: 2px solid #0284c7;">
  <div style="position: absolute; left: 0; top: 0; width: 450px; height: 100%; background: radial-gradient(circle, rgba(16, 185, 129, 0.15), transparent 70%); pointer-events: none;"></div>
  
  <div class="container py-4 position-relative" style="z-index: 2;">
    <div class="row align-items-center g-5">
      
      <!-- Cột Trái: Giới thiệu SP04 -->
      <div class="col-lg-6">
        <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-1 rounded-pill" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.45);">
          <span style="color: #34d399; font-size: 13px; font-weight: 800;">🌟 PHIÊN BẢN 2.0 NÂNG CẤP TOÀN DIỆN DÀNH CHO GIÁO VIÊN</span>
        </div>

        <h2 class="display-6 fw-black text-white mb-3 lh-sm">
          Trợ Lý Soạn Giảng AI 2.0<br>
          <span class="text-gradient-green">Soạn Giảng, Ma Trận &amp; Ra Đề Thi Chuẩn Bộ</span>
        </h2>

        <p class="text-light opacity-75 fs-6 mb-4 lh-base">
          Giải phóng 90% thời gian cho thầy cô từ Tiểu học đến Đại học! Tích hợp mô hình <strong>Google Gemini thế hệ mới</strong>, hoạt động theo cơ chế <strong>Zero-API-Cost</strong> (hoàn toàn không tốn phí mua API key hàng tháng).
        </p>

        <!-- 5 Điểm Sư Phạm Đắt Giá Nhất -->
        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex align-items-start gap-3">
            <span class="p-2 rounded-circle bg-danger bg-opacity-25 text-warning mt-1"><i class="bi bi-patch-check-fill"></i></span>
            <div>
              <strong class="text-white">Ra Đề Thi, Ma Trận &amp; Bản Đặc Tả (Mới Trong Bản 2.0):</strong>
              <span class="text-white-50"> Lập ma trận tỉ lệ Nhận biết - Thông hiểu - Vận dụng, bản đặc tả chi tiết, sinh đề trắc nghiệm/tự luận đa mã đề kèm đáp án thang điểm chuẩn Bộ GD&amp;ĐT.</span>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3">
            <span class="p-2 rounded-circle bg-success bg-opacity-25 text-success mt-1"><i class="bi bi-file-earmark-word-fill"></i></span>
            <div>
              <strong class="text-white">Giáo Án Word Chuẩn CV 5512 &amp; 2345:</strong>
              <span class="text-white-50"> Tự động lập bảng tiến trình 4 hoạt động dạy - học, font Times New Roman 14pt đúng quy định sư phạm.</span>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3">
            <span class="p-2 rounded-circle bg-info bg-opacity-25 text-info mt-1"><i class="bi bi-file-earmark-ppt-fill"></i></span>
            <div>
              <strong class="text-white">Slide PowerPoint Tự Sinh Ảnh Imagen 3:</strong>
              <span class="text-white-50"> AI tự vẽ hình minh họa bài học độ nét cao, bố cục trình chiếu khoa học, tương thích 100% định dạng .pptx.</span>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3">
            <span class="p-2 rounded-circle bg-warning bg-opacity-25 text-warning mt-1"><i class="bi bi-puzzle-fill"></i></span>
            <div>
              <strong class="text-white">Tự Động Hóa Trò Chơi Tương Tác:</strong>
              <span class="text-white-50"> Slide trò chơi Lật mảnh ghép bí ẩn, Đố vui Đúng/Sai đã gắn sẵn trigger hiệu ứng chuyên nghiệp.</span>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3">
            <span class="p-2 rounded-circle bg-primary bg-opacity-25 text-primary mt-1"><i class="bi bi-shield-check"></i></span>
            <div>
              <strong class="text-white">Phân Hạng Linh Hoạt: Bản Tiêu Chuẩn 590k &amp; Bản PRO 890k:</strong>
              <span class="text-white-50"> Dùng thử 7 ngày miễn phí trọn vẹn PRO. Bản quyền vĩnh viễn theo máy tính giáo viên, cập nhật trọn đời.</span>
            </div>
          </div>
        </div>

        <div class="d-flex flex-wrap gap-3">
          <a href="https://drive.google.com/file/d/1lhrOruTe0Btcbn_SApsXbjS8KDMnTP-f/view?usp=sharing" target="_blank" class="btn btn-success btn-lg fw-bold px-4 py-3 rounded-pill shadow-lg" style="font-size: 15px;">
            📥 Tải Bản Dùng Thử 7 Ngày (Drive)
          </a>
          <a href="<?php echo APP_URL; ?>/post?slug=tro-ly-soan-giang-ai-powerpoint-giao-an-chuan-5512" class="btn btn-outline-light btn-lg fw-semibold px-4 py-3 rounded-pill" style="font-size: 15px;">
            📖 Chi Tiết Tính Năng 2.0
          </a>
          <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-primary btn-lg fw-bold px-4 py-3 rounded-pill shadow-lg" style="background: linear-gradient(135deg, #7c3aed 0%, #0284c7 100%); border: none; font-size: 15px;">
            ⚡ Xem Bảng Giá &amp; Kích Hoạt Key
          </a>
        </div>
      </div>

      <!-- Cột Phải: Hình ảnh thực tế SP04 -->
      <div class="col-lg-6">
        <div class="position-relative">
          <a href="<?php echo APP_URL; ?>/post?slug=tro-ly-soan-giang-ai-powerpoint-giao-an-chuan-5512" class="d-block text-decoration-none">
            <img src="<?php echo APP_URL; ?>/uploads/posts/6adca259d5a0b-1792844377.png" alt="Trợ Lý Soạn Giảng AI - Slide PowerPoint và Kế hoạch bài dạy chuẩn 5512" class="img-fluid rounded-4 shadow-lg border border-secondary border-opacity-25" style="transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
          </a>
          
          <div class="position-absolute bottom-0 start-0 m-3 p-3 rounded-3 text-white" style="background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(12px); border: 1px solid rgba(16, 185, 129, 0.4);">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-success">SP04 Commercial PRO v2.0</span>
              <small class="fw-bold">Tích hợp Ra Đề Thi 2.0, Imagen 3 &amp; Zero-API-Cost</small>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 3. HỆ THỐNG PHẦN MỀM & GIẢI PHÁP SỐ ĐỘC QUYỀN (SOFTWARE SUITE CATALOG)      -->
<!-- ========================================================================= -->
<section class="py-5" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
  <div class="container py-3">
    
    <div class="text-center mb-5">
      <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill" style="background: #e0f2fe; color: #0284c7; font-size: 13px; font-weight: 700;">
        ⚡ SẢN PHẨM SỐ &amp; MICRO-SAAS THƯƠNG MẠI
      </div>
      <h2 class="display-6 fw-black text-dark mb-2">Kho Phần Mềm Độc Quyền Của NTKNTK</h2>
      <p class="text-muted fs-6 mx-auto" style="max-width: 680px;">
        100% Hoạt động Offline LAN hoặc Self-hosted, không mất phí duy trì hàng tháng. Tự động cấp License Key sau 3 giây khi quét mã thanh toán VietQR.
      </p>
    </div>

    <div class="row g-4">
      
      <!-- Card SP04: Trợ Lý Soạn Giảng AI -->
      <div class="col-md-6 col-lg-3">
        <div class="software-product-card p-4 h-100 d-flex flex-column justify-content-between position-relative shadow-sm" style="border-top: 4px solid #8b5cf6;">
          <div class="position-absolute top-0 end-0 m-3">
            <span class="badge bg-purple text-white rounded-pill px-2 py-1" style="background: #8b5cf6; font-size: 11px;">PRO 2.0 MỚI</span>
          </div>
          <div>
            <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #f5f3ff; color: #8b5cf6; font-size: 28px;">
              <i class="bi bi-robot"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Trợ Lý Soạn Giảng AI 2.0</h5>
            <div class="text-muted small mb-3">Gói: <strong>SP04 &amp; SP04_PRO</strong></div>
            <p class="text-muted small mb-3">
              Slide PPT hiệu ứng &amp; ảnh AI Imagen 3, Giáo án Word 5512/2345 + Phân hệ Ma trận đề, Bản đặc tả chi tiết &amp; Ra đề thi 2.0.
            </p>
            <div class="d-flex flex-column mb-3">
              <div class="d-flex align-items-baseline gap-2">
                <span class="fs-5 fw-black text-danger">590.000 đ</span>
                <span class="text-muted small">(Bản Tiêu Chuẩn)</span>
              </div>
              <div class="d-flex align-items-baseline gap-2">
                <span class="fs-5 fw-black text-primary">890.000 đ</span>
                <span class="text-muted small fw-bold text-success">(Bản PRO 2.0)</span>
              </div>
            </div>
          </div>
          <div class="d-flex flex-column gap-2 pt-2 border-top">
            <a href="https://drive.google.com/file/d/1lhrOruTe0Btcbn_SApsXbjS8KDMnTP-f/view?usp=sharing" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">
              📥 Tải Dùng Thử 7 Ngày (Pro)
            </a>
            <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-primary btn-sm rounded-pill fw-bold text-white" style="background: #7c3aed; border-color: #7c3aed;">
              ⚡ Mua Bản Quyền (VietQR)
            </a>
          </div>
        </div>
      </div>

      <!-- Card Combo: Phòng Máy Tin Học LAN -->
      <div class="col-md-6 col-lg-3">
        <div class="software-product-card p-4 h-100 d-flex flex-column justify-content-between position-relative shadow-sm" style="border-top: 4px solid #f59e0b;">
          <div class="position-absolute top-0 end-0 m-3">
            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 fw-bold" style="font-size: 11px;">TIẾT KIỆM 200K</span>
          </div>
          <div>
            <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #fef3c7; color: #d97706; font-size: 28px;">
              <i class="bi bi-hdd-network"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Combo Phòng Máy LAN</h5>
            <div class="text-muted small mb-3">SpaceTyper + WebTrợGiảng</div>
            <p class="text-muted small mb-3">
              Trọn bộ giải pháp phòng máy Tin học không cần Internet. Game thi đấu gõ 10 ngón + Hệ thống thi trắc nghiệm chấm điểm tự động.
            </p>
            <div class="d-flex align-items-baseline gap-2 mb-3">
              <span class="fs-4 fw-black text-danger">790.000 đ</span>
              <span class="text-muted small text-decoration-line-through">990.000 đ</span>
            </div>
          </div>
          <div class="d-flex flex-column gap-2 pt-2 border-top">
            <a href="https://drive.google.com/file/d/1_PObVdtmjT8d88Nqs9dAOVqwEbanuPp-/view?usp=sharing" target="_blank" class="btn btn-outline-warning btn-sm rounded-pill fw-bold text-dark">
              📥 Tải Dùng Thử Combo
            </a>
            <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-warning btn-sm rounded-pill fw-bold text-dark">
              ⚡ Kích Hoạt Key (VietQR)
            </a>
          </div>
        </div>
      </div>

      <!-- Card SP01: WebTrợGiảng LAN & Cloud -->
      <div class="col-md-6 col-lg-3">
        <div class="software-product-card p-4 h-100 d-flex flex-column justify-content-between position-relative shadow-sm" style="border-top: 4px solid #0284c7;">
          <div>
            <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #e0f2fe; color: #0284c7; font-size: 28px;">
              <i class="bi bi-file-earmark-check"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">WebTrợGiảng LAN</h5>
            <div class="text-muted small mb-3">Mã gói: <strong>SP01</strong></div>
            <p class="text-muted small mb-3">
              Xem video bài giảng mạng nội bộ, thi trắc nghiệm nạp đề từ Word trong 5 giây, tự động chấm điểm xuất file Excel chuẩn Bộ GD&amp;ĐT.
            </p>
            <div class="d-flex align-items-baseline gap-2 mb-3">
              <span class="fs-4 fw-black text-danger">490.000 đ</span>
              <span class="text-muted small text-decoration-line-through">800.000 đ</span>
            </div>
          </div>
          <div class="d-flex flex-column gap-2 pt-2 border-top">
            <a href="https://drive.google.com/file/d/1np5GPg7BiQpQfy42NsXDi-ZI0dbncsf-/view?usp=sharing" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">
              📥 Tải Dùng Thử 7 Ngày
            </a>
            <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-primary btn-sm rounded-pill fw-bold">
              ⚡ Kích Hoạt Key (VietQR)
            </a>
          </div>
        </div>
      </div>

      <!-- Card SP03: SmartWarehouse Mini -->
      <div class="col-md-6 col-lg-3">
        <div class="software-product-card p-4 h-100 d-flex flex-column justify-content-between position-relative shadow-sm" style="border-top: 4px solid #8b5cf6;">
          <div>
            <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #f5f3ff; color: #8b5cf6; font-size: 28px;">
              <i class="bi bi-box-seam"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">SmartWarehouse Mini</h5>
            <div class="text-muted small mb-3">Mã gói: <strong>SP03</strong></div>
            <p class="text-muted small mb-3">
              Quản lý Nhập - Xuất - Tồn kho, công nợ khách hàng &amp; nhà cung cấp, phân quyền thu ngân. Mua 1 lần dùng vĩnh viễn, không phí tháng.
            </p>
            <div class="d-flex align-items-baseline gap-2 mb-3">
              <span class="fs-4 fw-black text-danger">990.000 đ</span>
              <span class="text-muted small text-decoration-line-through">1.990.000 đ</span>
            </div>
          </div>
          <div class="d-flex flex-column gap-2 pt-2 border-top">
            <a href="https://drive.google.com/file/d/1_JNCrwv4BQH8ayIvRTg-fJ0C0my1TfeX/view?usp=sharing" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold">
              📥 Tải Dùng Thử 7 Ngày
            </a>
            <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-dark btn-sm rounded-pill fw-bold" style="background: #6d28d9; border: none;">
              ⚡ Kích Hoạt Key (VietQR)
            </a>
          </div>
        </div>
      </div>

    </div>

    <!-- Nút Vào Trang Bán Hàng Chi Tiết -->
    <div class="text-center mt-5">
      <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border: none; font-size: 16px;">
        🛒 Khám Phá Toàn Bộ Cửa Hàng &amp; Hướng Dẫn Kích Hoạt Tự Động →
      </a>
    </div>

  </div>
</section>

<!-- ========================================================================= -->
<!-- 4. BỐN TRỤ CỘT GIÁ TRỊ VÀNG (CORE VALUE PILLARS)                           -->
<!-- ========================================================================= -->
<section class="py-5" style="background: #ffffff; border-bottom: 1px solid #e2e8f0;">
  <div class="container">
    <div class="row g-4">
      
      <div class="col-md-6 col-lg-3">
        <div class="glass-card p-4 rounded-4 h-100 shadow-sm border-0">
          <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #e0f2fe; color: #0284c7; font-size: 26px;">
            <i class="bi bi-code-slash"></i>
          </div>
          <h5 class="fw-bold mb-2 text-dark">Lập Trình &amp; AI Thực Chiến</h5>
          <p class="text-muted small mb-0">Từ tư duy thuật toán thiếu nhi (Scratch) đến Python nâng cao, xây dựng kịch bản Prompt AI tự động hóa công việc.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="glass-card p-4 rounded-4 h-100 shadow-sm border-0">
          <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #fef3c7; color: #d97706; font-size: 26px;">
            <i class="bi bi-hdd-network"></i>
          </div>
          <h5 class="fw-bold mb-2 text-dark">Phần Mềm Phòng Máy LAN</h5>
          <p class="text-muted small mb-0">Chạy 100% nội bộ không cần Internet. 1 máy chủ giáo viên điều phối đồng loạt 40–60 máy trạm học sinh mượt mà.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="glass-card p-4 rounded-4 h-100 shadow-sm border-0">
          <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #dcfce7; color: #16a34a; font-size: 26px;">
            <i class="bi bi-award"></i>
          </div>
          <h5 class="fw-bold mb-2 text-dark">Luyện Thi MOS &amp; Chứng Chỉ</h5>
          <p class="text-muted small mb-0">Luyện giải đề thực chiến MOS Word, Excel, PowerPoint chuẩn quốc tế và ôn thi chứng chỉ CNTT cơ bản/nâng cao.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="glass-card p-4 rounded-4 h-100 shadow-sm border-0">
          <div class="d-inline-flex p-3 rounded-3 mb-3" style="background: #f3e8ff; color: #9333ea; font-size: 26px;">
            <i class="bi bi-translate"></i>
          </div>
          <h5 class="fw-bold mb-2 text-dark">Tiếng Anh Giao Tiếp &amp; Thi Cử</h5>
          <p class="text-muted small mb-0">Phương pháp phản xạ ngôn ngữ tự nhiên từ Thiếu nhi, THCS, THPT đến luyện thi vào Lớp 10 và Tốt nghiệp THPT.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 5. KHÓA HỌC NỔI BẬT (FEATURED COURSES WITH DYNAMIC TABS)                  -->
<!-- ========================================================================= -->
<section id="khoa-hoc" class="py-5" style="background: #f8fafc;">
  <div class="container py-3">
    
    <div class="text-center mb-4">
      <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill" style="background: #e0f2fe; color: #0284c7; font-size: 13px; font-weight: 700;">
        🎓 CHƯƠNG TRÌNH ĐÀO TẠO THỰC CHIẾN
      </div>
      <h2 class="display-6 fw-black text-dark mb-2">Khóa Học Đang Mở Ghi Danh</h2>
      <p class="text-muted fs-6 mx-auto" style="max-width: 650px;">
        Lộ trình học tập bài bản từ vỡ lòng đến thành thạo, kết hợp công nghệ hiện đại và sự đồng hành kèm cặp sát sao của giảng viên.
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

<!-- ========================================================================= -->
<!-- 6. TẠI SAO CHỌN TRUNG TÂM NGUYỄN MINH (NTKNTK)?                           -->
<!-- ========================================================================= -->
<section class="py-5" style="background: #ffffff;">
  <div class="container py-3">
    <div class="text-center mb-5">
      <div class="d-inline-flex align-items-center gap-2 mb-2 px-3 py-1 rounded-pill" style="background: #e2e8f0; color: #334155; font-size: 13px; font-weight: 700;">
        ⭐ GIÁ TRỊ KHÁC BIỆT
      </div>
      <h2 class="display-6 fw-black text-dark mb-2">Tại Sao Hơn 5,000+ Khách Hàng &amp; Học Viên Tin Tưởng NTKNTK?</h2>
      <p class="text-muted fs-6 mx-auto" style="max-width: 620px;">
        Chất lượng sản phẩm và đào tạo là kim chỉ nam. Chúng tôi xây dựng môi trường học tập hiện đại, thực chất và hỗ trợ tận tâm trọn đời.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="p-4 bg-light rounded-4 h-100 border-0 text-center">
          <div class="mb-3" style="font-size: 40px;">🎯</div>
          <h5 class="fw-bold mb-2">Giáo Trình Thực Chiến</h5>
          <p class="text-muted small mb-0">Học là làm được việc ngay. Không lý thuyết suông, chú trọng dự án và bài tập thực hành trên máy tính 100% thời lượng.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-4 bg-light rounded-4 h-100 border-0 text-center">
          <div class="mb-3" style="font-size: 40px;">👨‍🏫</div>
          <h5 class="fw-bold mb-2">Giảng Viên Tâm Huyết</h5>
          <p class="text-muted small mb-0">Thầy Nguyễn Minh Tuấn cùng đội ngũ chuyên gia giàu kinh nghiệm, kèm cặp 1:1, giải đáp cặn kẽ từng vướng mắc kỹ thuật.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-4 bg-light rounded-4 h-100 border-0 text-center">
          <div class="mb-3" style="font-size: 40px;">🌐</div>
          <h5 class="fw-bold mb-2">Hệ Thống LMS &amp; AI Thông Minh</h5>
          <p class="text-muted small mb-0">Xem video bài giảng mọi lúc, nạp bài tập lũy tiến, nạp đề trắc nghiệm Word và ứng dụng AI tự động hóa công việc sư phạm.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="p-4 bg-light rounded-4 h-100 border-0 text-center">
          <div class="mb-3" style="font-size: 40px;">🏅</div>
          <h5 class="fw-bold mb-2">Cam Kết Chuẩn Đầu Ra</h5>
          <p class="text-muted small mb-0">Hỗ trợ ôn luyện đến khi học viên tự tin vượt qua các kỳ thi trường lớp, thi MOS quốc tế hoặc kỳ thi tuyển sinh.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 7. BÀI VIẾT CHUYÊN SÂU & TIÊU ĐIỂM CÔNG NGHỆ (LATEST POSTS)               -->
<!-- ========================================================================= -->
<?php if (!empty($latestPosts)): ?>
<section class="py-5" style="background: #f1f5f9;">
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

<!-- ========================================================================= -->
<!-- 8. STRATEGIC BOTTOM CONVERSION BANNER (HOTLINE & ZALO)                    -->
<!-- ========================================================================= -->
<section style="background: linear-gradient(135deg, #0284c7 0%, #1e40af 100%); padding: 75px 0;" class="text-white">
  <div class="container text-center">
    <div class="d-inline-flex p-3 rounded-circle bg-white bg-opacity-10 mb-3 fs-3">
      ✨
    </div>
    <h2 class="display-6 fw-black mb-3">Sẵn Sàng Bứt Phá Năng Lực Cùng NTKNTK?</h2>
    <p class="fs-5 opacity-90 mb-4 mx-auto" style="max-width: 650px;">
      Liên hệ ngay để nhận lộ trình tư vấn cá nhân hóa cho học sinh, hoặc nhận bản quyền kích hoạt phần mềm giáo viên &amp; trường học.
    </p>

    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="tel:0979875712" class="btn btn-warning btn-lg fw-bold px-5 py-3 rounded-pill shadow-lg text-dark" style="font-size: 16px;">
        ☎️ Hotline: 0979 875 712
      </a>
      <a href="https://zalo.me/0979875712" target="_blank" class="btn btn-light btn-lg fw-bold px-5 py-3 rounded-pill shadow-lg" style="color: #0284c7; font-size: 16px;">
        💬 Chat Zalo Trực Tiếp
      </a>
      <a href="<?php echo APP_URL; ?>/post?slug=phan-mem" class="btn btn-outline-light btn-lg fw-semibold px-4 py-3 rounded-pill" style="font-size: 16px;">
        🛒 Xem Bảng Giá Phần Mềm
      </a>
    </div>
  </div>
</section>

<!-- ========================================================================= -->
<!-- 9. TAB FILTER JAVASCRIPT                                                  -->
<!-- ========================================================================= -->
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
