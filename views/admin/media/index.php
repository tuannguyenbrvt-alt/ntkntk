<?php
// Format bytes ra dạng đọc được
function formatBytes($bytes, $precision = 1) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-images text-primary me-2"></i>Thư viện Media</h4>
        <small class="text-muted"><?php echo count($files); ?> file đã tải lên</small>
    </div>
    <button class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-cloud-arrow-up me-2"></i>Tải ảnh lên
    </button>
</div>

<!-- Upload Drop Zone Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tải file lên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="drop-zone" class="border border-2 border-dashed rounded-3 p-5 text-center text-muted" style="border-color: #dee2e6; cursor: pointer; transition: all .2s;">
                    <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-2 d-block"></i>
                    <p class="fw-semibold mb-1">Kéo thả ảnh vào đây</p>
                    <p class="small mb-3">Hoặc nhấn để chọn từ máy tính</p>
                    <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="d-none">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4" onclick="document.getElementById('file-input').click()">Chọn ảnh</button>
                </div>
                <div id="upload-progress" class="mt-3 d-none">
                    <div class="progress rounded-pill" style="height: 8px;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                    <p id="upload-status" class="small text-muted mt-2 mb-0"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Media Gallery Grid -->
<?php if(empty($files)): ?>
    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
        <i class="bi bi-folder2-open fs-1 text-muted opacity-50 d-block mb-3"></i>
        <p class="text-muted">Chưa có file nào. Hãy tải ảnh lên bằng nút ở trên.</p>
    </div>
<?php else: ?>
    <div class="row g-3" id="media-gallery">
        <?php foreach ($files as $file): ?>
            <?php $isImage = in_array($file['ext'], ['jpg','jpeg','png','gif','webp']); ?>
            <div class="col-6 col-sm-4 col-md-3 col-xl-2 media-item">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100 position-relative media-card" style="transition: transform .2s;">
                    <!-- Preview -->
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 130px; overflow: hidden;">
                        <?php if($isImage): ?>
                            <img src="<?php echo htmlspecialchars($file['url']); ?>" class="w-100 h-100 object-fit-cover" loading="lazy">
                        <?php else: ?>
                            <i class="bi bi-file-earmark fs-1 text-secondary"></i>
                        <?php endif; ?>
                    </div>
                    <!-- Info -->
                    <div class="card-body p-2">
                        <p class="small mb-0 text-truncate fw-semibold" title="<?php echo htmlspecialchars($file['name']); ?>"><?php echo htmlspecialchars($file['name']); ?></p>
                        <small class="text-muted"><?php echo formatBytes($file['size']); ?></small>
                    </div>
                    <!-- Hover Overlay -->
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center gap-2 media-overlay" style="background: rgba(0,0,0,.55); opacity: 0; transition: opacity .2s;">
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-semibold copy-btn" data-url="<?php echo htmlspecialchars($file['url']); ?>">
                            <i class="bi bi-clipboard me-1"></i>Copy Link
                        </button>
                        <form method="POST" action="<?php echo APP_URL; ?>/admin/media/delete" onsubmit="return confirm('Xóa file này?')">
                            <input type="hidden" name="filename" value="<?php echo htmlspecialchars($file['name']); ?>">
                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                <i class="bi bi-trash me-1"></i>Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="copy-toast" class="toast align-items-center text-bg-success border-0 rounded-3" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-semibold"><i class="bi bi-check-circle me-2"></i>Đã sao chép link!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<style>
    .media-card:hover { transform: translateY(-3px); }
    .media-card:hover .media-overlay { opacity: 1 !important; }
    .border-dashed { border-style: dashed !important; }
    #drop-zone.drag-over { background: #e8f0fe; border-color: #0d6efd !important; }
</style>

<script>
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const uploadUrl = '<?php echo APP_URL; ?>/admin/media/upload';

// Drag-over styling
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    uploadFiles(e.dataTransfer.files);
});
fileInput.addEventListener('change', () => uploadFiles(fileInput.files));

async function uploadFiles(files) {
    if (!files.length) return;
    const progress = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const status = document.getElementById('upload-status');
    progress.classList.remove('d-none');
    let uploaded = 0;

    for (let file of files) {
        status.textContent = `Đang tải lên ${file.name}...`;
        const form = new FormData();
        form.append('file', file);
        try {
            const res = await fetch(uploadUrl, { method: 'POST', body: form });
            const data = await res.json();
            if (data.location) {
                uploaded++;
                progressBar.style.width = (uploaded / files.length * 100) + '%';
            } else {
                status.textContent = 'Lỗi: ' + (data.error || 'Không rõ');
            }
        } catch(err) {
            status.textContent = 'Lỗi kết nối.';
        }
    }
    if (uploaded === files.length) {
        status.innerHTML = '<span class="text-success fw-bold">✓ Tải lên thành công! Đang làm mới...</span>';
        setTimeout(() => location.reload(), 1200);
    }
}

// Copy to Clipboard
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.copy-btn');
    if (!btn) return;
    navigator.clipboard.writeText(btn.dataset.url).then(() => {
        const toast = new bootstrap.Toast(document.getElementById('copy-toast'));
        toast.show();
    });
});
</script>
