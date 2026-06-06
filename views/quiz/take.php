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
            <div class="fw-semibold mb-4 text-dark" style="font-size:1.1rem;"><?php echo $q['question_text']; ?></div>
            <?php 
            $qtype = $q['question_type'] ?? 'single';
            foreach($q['options'] as $opt): 
                $is_checked = isset($savedMap[$q['qb_id']]) && in_array((int)$opt['id'], $savedMap[$q['qb_id']]);
            ?>
            <div>
                <?php if($qtype === 'multiple'): ?>
                <input type="checkbox"
                       name="answers[<?php echo (int)$q['qb_id']; ?>][]"
                       id="opt<?php echo (int)$opt['id']; ?>"
                       value="<?php echo (int)$opt['id']; ?>"
                       class="d-none"
                       <?php echo $is_checked ? 'checked' : ''; ?>>
                <?php else: ?>
                <input type="radio"
                       name="answers[<?php echo (int)$q['qb_id']; ?>]"
                       id="opt<?php echo (int)$opt['id']; ?>"
                       value="<?php echo (int)$opt['id']; ?>"
                       class="d-none"
                       <?php echo $is_checked ? 'checked' : ''; ?>>
                <?php endif; ?>
                <label for="opt<?php echo (int)$opt['id']; ?>" class="option-label text-start d-flex align-items-center gap-2" style="display:block;">
                    <?php if($qtype === 'multiple'): ?>
                        <i class="bi bi-<?php echo $is_checked ? 'check-square-fill' : 'square'; ?> text-muted fs-5 opt-icon"></i>
                    <?php else: ?>
                        <i class="bi bi-<?php echo $is_checked ? 'record-circle-fill' : 'circle'; ?> text-muted fs-5 opt-icon"></i>
                    <?php endif; ?>
                    <span><?php echo $opt['option_text']; ?></span>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

        <?php if(empty($questions)): ?>
        <div class="quiz-card text-center py-5">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-muted">Đề thi chưa có câu hỏi nào.</h5>
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

// Highlight dap an duoc chon va cap nhat icon
function applyHighlight(el) {
    var label = el.nextElementSibling;
    var icon = label.querySelector('.opt-icon');
    if (el.checked) {
        label.style.borderColor = '#3b82f6';
        label.style.background  = '#e0f2fe';
        label.style.color       = '#0369a1';
        if (icon) {
            if (el.type === 'radio') {
                icon.className = 'bi bi-record-circle-fill text-primary fs-5 opt-icon';
            } else {
                icon.className = 'bi bi-check-square-fill text-primary fs-5 opt-icon';
            }
        }
    } else {
        label.style.borderColor = '#e2e8f0';
        label.style.background  = '#ffffff';
        label.style.color       = '#1e293b';
        if (icon) {
            if (el.type === 'radio') {
                icon.className = 'bi bi-circle text-muted fs-5 opt-icon';
            } else {
                icon.className = 'bi bi-square text-muted fs-5 opt-icon';
            }
        }
    }
}

document.querySelectorAll('input[type=radio], input[type=checkbox]').forEach(function(input) {
    input.addEventListener('change', function() {
        if (this.type === 'radio') {
            var name = this.name;
            var escapedName = name.replace(/([\[\]])/g, '\\$1');
            document.querySelectorAll('input[name="' + escapedName + '"]').forEach(function(x) {
                applyHighlight(x);
            });
        } else {
            applyHighlight(this);
        }
    });
    applyHighlight(input);
});
</script>
