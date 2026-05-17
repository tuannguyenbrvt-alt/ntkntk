<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chứng chỉ - <?php echo htmlspecialchars($user['full_name']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cinzel:wght@400;700&family=Inter:wght@400;600&display=swap">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background: #f0e6d3; display: flex; flex-direction: column; align-items: center; min-height: 100vh; padding: 20px; font-family: 'Inter', sans-serif; }
        
        .print-btn { margin-bottom: 20px; display: flex; gap: 12px; }
        .print-btn a, .print-btn button {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 24px; border-radius: 50px; font-weight: 600; font-size: 15px;
            text-decoration: none; cursor: pointer; border: none;
        }
        .btn-print { background: #0d6efd; color: white; }
        .btn-back { background: white; color: #495057; border: 2px solid #dee2e6 !important; }
        
        .certificate {
            width: 900px; max-width: 100%;
            background: #fffdf7;
            border: 12px solid #c9a84c;
            outline: 4px solid #9b7d3c;
            outline-offset: -18px;
            border-radius: 4px;
            padding: 60px 80px;
            position: relative;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
        }
        
        /* Corner ornaments */
        .certificate::before, .certificate::after,
        .corner-bl, .corner-br {
            content: '✦';
            position: absolute;
            font-size: 28px;
            color: #c9a84c;
        }
        .certificate::before { top: 14px; left: 14px; }
        .certificate::after  { top: 14px; right: 14px; }
        .corner-bl { bottom: 14px; left: 14px; }
        .corner-br { bottom: 14px; right: 14px; }

        .cert-header { text-align: center; margin-bottom: 30px; }
        .cert-logo { font-family: 'Cinzel', serif; font-size: 13px; letter-spacing: 4px; text-transform: uppercase; color: #9b7d3c; font-weight: 700; }
        .cert-divider { width: 60%; height: 1px; background: linear-gradient(to right, transparent, #c9a84c, transparent); margin: 12px auto; }
        .cert-type { font-family: 'Cinzel', serif; font-size: 28px; letter-spacing: 6px; text-transform: uppercase; color: #6b4c1e; font-weight: 700; margin-bottom: 4px; }
        .cert-subtitle { font-size: 14px; color: #888; letter-spacing: 2px; text-transform: uppercase; }

        .cert-body { text-align: center; margin: 28px 0; }
        .cert-presented { font-size: 15px; color: #666; margin-bottom: 12px; letter-spacing: 1px; }
        .cert-name { font-family: 'Great Vibes', cursive; font-size: 64px; color: #1a3a5c; line-height: 1.1; margin: 4px 0 16px; }
        .cert-desc { font-size: 16px; color: #555; line-height: 1.7; max-width: 600px; margin: 0 auto; }
        .cert-course { font-family: 'Cinzel', serif; font-size: 22px; font-weight: 700; color: #1a3a5c; margin: 10px 0 8px; display: block; }

        .cert-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 40px; }
        .cert-sign { text-align: center; }
        .sign-line { width: 180px; height: 1px; background: #333; margin-bottom: 6px; }
        .sign-name { font-size: 13px; font-weight: 600; color: #333; }
        .sign-title { font-size: 12px; color: #888; }

        .cert-seal { text-align: center; }
        .seal-circle {
            width: 90px; height: 90px; border-radius: 50%;
            background: radial-gradient(circle, #f5e3a3, #c9a84c);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            border: 3px solid #9b7d3c;
            margin: 0 auto;
        }
        .seal-circle .seal-star { font-size: 26px; color: #6b4c1e; }
        .seal-circle .seal-text { font-size: 9px; color: #6b4c1e; font-weight: 700; letter-spacing: 1px; text-align: center; line-height: 1.2; }

        .cert-code { text-align: center; margin-top: 24px; font-size: 11px; color: #bbb; letter-spacing: 2px; }

        @media print {
            body { background: white; padding: 0; }
            .print-btn { display: none; }
            .certificate { box-shadow: none; }
        }
        @media (max-width: 640px) {
            .certificate { padding: 30px 20px; }
            .cert-name { font-size: 44px; }
            .cert-type { font-size: 20px; }
            .cert-footer { flex-direction: column; gap: 20px; align-items: center; }
        }
    </style>
</head>
<body>
    <div class="print-btn">
        <button class="btn-print" onclick="window.print()">🖨️ In / Lưu PDF</button>
        <a href="<?php echo APP_URL; ?>/profile" class="btn-back">← Quay lại</a>
    </div>

    <div class="certificate">
        <span class="corner-bl">✦</span>
        <span class="corner-br">✦</span>

        <div class="cert-header">
            <div class="cert-logo"><?php echo APP_NAME; ?></div>
            <div class="cert-divider"></div>
            <div class="cert-type">Chứng Chỉ</div>
            <div class="cert-subtitle">Certificate of Completion</div>
        </div>

        <div class="cert-divider"></div>

        <div class="cert-body">
            <p class="cert-presented">Chứng nhận rằng</p>
            <div class="cert-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
            <p class="cert-desc">đã hoàn thành xuất sắc khóa học</p>
            <span class="cert-course">"<?php echo htmlspecialchars($enrollment['course_title']); ?>"</span>
            <p class="cert-desc" style="font-size:14px; margin-top:8px;">
                do <strong><?php echo APP_NAME; ?></strong> tổ chức và giảng dạy.
            </p>
        </div>

        <div class="cert-divider"></div>

        <div class="cert-footer">
            <div class="cert-sign">
                <div class="sign-line"></div>
                <div class="sign-name">Nguyễn Minh</div>
                <div class="sign-title">Giám đốc Trung tâm</div>
            </div>
            <div class="cert-seal">
                <div class="seal-circle">
                    <div class="seal-star">⭐</div>
                    <div class="seal-text">ĐÃ<br>HOÀN THÀNH</div>
                </div>
            </div>
            <div class="cert-sign">
                <div class="sign-line"></div>
                <div class="sign-name"><?php echo $certDate; ?></div>
                <div class="sign-title">Ngày cấp chứng chỉ</div>
            </div>
        </div>

        <div class="cert-code">Mã chứng chỉ: <?php echo $certCode; ?></div>
    </div>
</body>
</html>
