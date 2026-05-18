<?php
// Layout don gian cho trang lam bai (khong co navbar/footer)
// $content duoc inject boi Controller::render()
?><!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($title ?? 'Làm bài'); ?> — <?php echo APP_NAME; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body { background:#0f0f1a; color:#e0e0e0; font-family:'Inter',sans-serif; }
.quiz-card { background:#1a1a2e; border:1px solid #2d2d44; border-radius:12px; padding:1.5rem; }
.option-label { display:block; padding:.75rem 1rem; border:2px solid #2d2d44; border-radius:8px; cursor:pointer; transition:all .2s; margin-bottom:.5rem; }
.option-label:hover { border-color:#4e9af1; background:#1e2a3a; }
input[type=radio]:checked + .option-label { border-color:#4e9af1; background:#1e2a3a; }
#timer { font-size:1.4rem; font-weight:700; color:#ffd700; }
.sticky-top-bar { position:sticky; top:0; z-index:100; background:#12122a; border-bottom:1px solid #2d2d44; padding:.75rem 0; }
</style>
</head>
<body>
<?php echo $content; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
