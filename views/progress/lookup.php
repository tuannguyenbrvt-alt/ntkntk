<div class="container py-5" style="max-width:560px;margin:auto;">
    <div class="card border-0 shadow-sm" style="background:#1a1a2e;">
        <div class="card-body text-center py-5">
            <i class="bi bi-search" style="font-size:3rem;color:#4e9af1;"></i>
            <h4 class="text-white fw-bold mt-3">Tra cuu Ket qua Hoc tap</h4>
            <p class="text-white-50">Nhap so dien thoai de xem diem va ket qua bai tap</p>
            <form method="POST" action="<?php echo APP_URL; ?>/progress/lookupResult" class="mt-4">
                <div class="input-group input-group-lg">
                    <input type="tel" name="phone" class="form-control" style="background:#111;color:#eee;border-color:#444;" placeholder="Nhap so dien thoai..." required>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <p class="text-muted small mt-3">So dien thoai phai chinh xac voi so da dang ky</p>
        </div>
    </div>
</div>
