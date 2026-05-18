<div class="sticky-top-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <h6 class="mb-0 text-white"><i class="bi bi-trophy text-warning me-2"></i><?php echo htmlspecialchars($quiz['title']); ?></h6>
        <div class="d-flex align-items-center gap-3">
            <?php if($quiz['time_limit_minutes'] > 0): ?>
            <div><i class="bi bi-clock text-warning me-1"></i><span id="timer">--:--</span></div>
            <?php endif; ?>
            <span class="badge bg-info"><?php echo count($questions); ?> câu hỏi</span>
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
                <span class="badge bg-primary" style="font-size:.95rem;">Câu <?php echo $i+1; ?></span>
            </div>
            <p class="fw-semibold mb-4" style="font-size:1.1rem;"><?php echo nl2br(htmlspecialchars($q['question_text'])); ?></p>
            <?php foreach($q['options'] as $opt): ?>
            <div>
                <input type="radio"
                       name="answers[<?php echo (int)$q['qb_id']; ?>]"
                       id="opt<?php echo (int)$opt['id']; ?>"
                       value="<?php echo (int)$opt['id']; ?>"
                       class="d-none"
                       <?php echo (isset($savedMap[$q['qb_id']]) && $savedMap[$q['qb_id']] == $opt['id']) ? 'checked' : ''; ?>>
                <label for="opt<?php echo (int)$opt['id']; ?>" class="option-label"><?php echo htmlspecialchars($opt['option_text']); ?></label>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <?php if(empty($questions)): ?>
        <div class="quiz-card text-center py-5">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-white-50">Đề thi chưa có câu hỏi nào.</h5>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between mt-4 pb-5">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
            <?php if(!empty($questions)): ?>
            <button type="submit" class="btn btn-success btn-lg px-5"
                    onclick="return confirm('Nộp bài? Bạn không thể thay đổi sau khi nộp.')"
                    id="submitBtn">
                <i class="bi bi-send-check me-2"></i>Nộp bài
            </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
<?php if($quiz['time_limit_minutes'] > 0): ?>
var endTime = Date.now() + <?php echo (int)$quiz['time_limit_minutes'] * 60000; ?>;
var timerEl = document.getElementById('timer');
var iv = setInterval(function() {
    var rem = Math.floor((endTime - Date.now()) / 1000);
    if (rem <= 0) { clearInterval(iv); document.getElementById('quizForm').submit(); return; }
    timerEl.textContent = Math.floor(rem/60).toString().padStart(2,'0') + ':' + (rem%60).toString().padStart(2,'0');
    if (rem <= 60) timerEl.style.color = '#ff4444';
}, 500);
<?php endif; ?>

// Highlight dap an duoc chon
document.querySelectorAll('input[type=radio]').forEach(function(r) {
    r.addEventListener('change', function() {
        var name = this.name;
        document.querySelectorAll('input[name="' + name + '"]').forEach(function(x) {
            x.nextElementSibling.style.borderColor = '#2d2d44';
            x.nextElementSibling.style.background  = '';
        });
        this.nextElementSibling.style.borderColor = '#4e9af1';
        this.nextElementSibling.style.background  = '#1e2a3a';
    });
    if (r.checked) {
        r.nextElementSibling.style.borderColor = '#4e9af1';
        r.nextElementSibling.style.background  = '#1e2a3a';
    }
});
</script>
