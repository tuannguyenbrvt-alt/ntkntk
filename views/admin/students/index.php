<div class="card shadow-sm border-0">
    <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-people me-2"></i> Danh sách Học viên (CRM)</h5>
        <form action="" method="GET" class="d-flex align-items-center gap-1">
            <input type="text" name="q" class="form-control form-control-sm rounded-pill px-3" placeholder="Tìm kiếm học viên..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="width: 220px;">
            <?php if (!empty($search)): ?>
                <a href="?" class="btn btn-sm btn-outline-secondary rounded-pill" title="Xóa tìm kiếm"><i class="bi bi-x-lg"></i></a>
            <?php endif; ?>
            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-search"></i> Tìm</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Họ và tên</th>
                        <th>Liên hệ</th>
                        <th>Ngày sinh</th>
                        <th>Nghề nghiệp</th>
                        <th class="text-center">Số khóa học</th>
                        <th>Ngày tham gia</th>
                        <th class="text-end pe-4">Hồ sơ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $stu): ?>
                        <tr>
                            <td class="ps-4 fw-bold">
                                <?php echo htmlspecialchars($stu['full_name']); ?>
                            </td>
                            <td>
                                <div><i class="bi bi-envelope text-muted me-1"></i> <span class="small"><?php echo htmlspecialchars($stu['email']); ?></span></div>
                                <?php if($stu['phone']): ?>
                                    <div><i class="bi bi-telephone text-muted me-1"></i> <span class="small"><?php echo htmlspecialchars($stu['phone']); ?></span></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $stu['dob'] ? date('d/m/Y', strtotime($stu['dob'])) : '<span class="text-muted">-</span>'; ?></td>
                            <td><?php echo htmlspecialchars($stu['profession'] ?: '-'); ?></td>
                            <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3"><?php echo $stu['active_courses']; ?> khóa</span></td>
                            <td><small class="text-muted"><?php echo date('d/m/Y', strtotime($stu['created_at'])); ?></small></td>
                            <td class="text-end pe-4">
                                <a href="<?php echo APP_URL; ?>/admin/students/show?id=<?php echo $stu['id']; ?>" class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-person-lines-fill"></i> Chi tiết</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
