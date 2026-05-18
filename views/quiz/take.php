<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($quiz['title']); ?> — Lam bai</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{background:#0f0f1a;color:#e0e0e0;font-family:'Inter',sans-serif;}
.quiz-card{background:#1a1a2e;border:1px solid #2d2d44;border-radius:12px;padding:1.5rem;}
.option-label{display:block;padding:.75rem 1rem;border:2px solid #2d2d44;border-radius:8px;cursor:pointer;transition:all .2s;margin-bottom:.5rem;}
.option-label:hover{border-color:#4e9af1;background:#1e2a3a;}
input[type=radio]:checked+.option-label{border-color:#4e9af1;background:#1e2a3a;}
#timer{font-size:1.4rem;font-weight:700;color:#ffd700;}
.sticky-top-bar{position:sticky;top:0;z-index:100;background:#12122a;border-bottom:1px solid #2d2d44;padding:.75rem 0;}
</style>
</head>
<body>
<div class="sticky-top-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-white"><?php echo htmlspecialchars($quiz['title']); ?></h6>
        <div class="d-flex align-items-center gap-3">
            <?php if($quiz['time_limit_minutes'] > 0): ?>
            <div><i class="bi bi-clock text-warning me-1"></i><span id="timer">--:--</span></div>
            <?php endif; ?>
            <span class="badge bg-info"><?php echo count($questions); ?> cau hoi</span>
        </div>
    </div>
</div>

<div class="container py-4" style="max-width:760px;">
    <?php if(!empty($quiz['description'])): ?>
    <div class="quiz-card mb-4"><i class="bi bi-info-circle text-info me-2"></i><?php echo nl2br(htmlspecialchars($quiz['description'])); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo APP_URL; ?>/quiz/submit" id="quizForm">
        <input type="hidden" name="attempt_id" value="<?php echo $attempt['id']; ?>">

        <?php foreach($questions as $i => $q): ?>
        <div class="quiz-card mb-4" id="q<?php echo $i+1; ?>">
            <div class="d-flex justify-content-between mb-3">
                <span class="badge bg-primary fs-6">Cau <?php echo $i+1; ?></span>
            </div>
            <p class="fw-semibold fs-5 mb-4"><?php echo htmlspecialchars($q['question_text']); ?></p>
            <?php foreach($q['options'] as $opt): ?>
            <div>
                <input type="radio" name="answers[<?php echo $q['qb_id']; ?>]" id="opt<?php echo $opt['id']; ?>" value="<?php echo $opt['id']; ?>" class="d-none" <?php echo (isset($savedMap[$q['qb_id']]) && $savedMap[$q['qb_id']] == $opt['id']) ? 'checked' : ''; ?>>
                <label for="opt<?php echo $opt['id']; ?>" class="option-label"><?php echo htmlspecialchars($opt['option_text']); ?></label>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <div class="d-flex justify-content-between mt-4">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">Quay lai</a>
            <button type="submit" class="btn btn-success btn-lg px-5" onclick="return confirm('Nop bai? Ban khong the thay doi sau khi nop.')" id="submitBtn">
                <i class="bi bi-send-check me-2"></i>Nop bai
            </button>
        </div>
    </form>
</div>

<script>
<?php if($quiz['time_limit_minutes'] > 0): ?>
    let endTime = Date.now() + <?php echo $quiz['time_limit_minutes'] * 60000; ?>;
    const timer = document.getElementById('timer');
    const iv = setInterval(() => {
        let rem = Math.floor((endTime - Date.now()) / 1000);
        if (rem <= 0) { clearInterval(iv); document.getElementById('quizForm').submit(); return; }
        timer.textContent = Math.floor(rem/60).toString().padStart(2,'0') + ':' + (rem%60).toString().padStart(2,'0');
        if (rem <= 60) timer.style.color = '#ff4444';
    }, 500);
<?php endif; ?>

// Highlight selected options
document.querySelectorAll('input[type=radio]').forEach(r => {
    r.addEventListener('change', function() {
        document.querySelectorAll(`input[name="${this.name}"]`).forEach(x => x.nextElementSibling.style.borderColor = '#2d2d44');
        this.nextElementSibling.style.borderColor = '#4e9af1';
        this.nextElementSibling.style.background = '#1e2a3a';
    });
    if (r.checked) { r.nextElementSibling.style.borderColor = '#4e9af1'; r.nextElementSibling.style.background = '#1e2a3a'; }
});
</script>
</body></html>
