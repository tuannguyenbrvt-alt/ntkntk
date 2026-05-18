<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-primary">Đề cương Khóa học: <?php echo htmlspecialchars($course['title']); ?></h5>
            <small class="text-muted"><a href="<?php echo APP_URL; ?>/admin/courses" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Quay lại Danh sách</a></small>
        </div>
        <button class="btn btn-primary" onclick="showModal('addPartModal')"><i class="bi bi-plus-circle me-1"></i> Thêm Phần mới</button>
    </div>
    <div class="card-body bg-light">
        <!-- Loop Parts -->
        <?php foreach($parts as $part): ?>
            <div class="card mb-3 border border-secondary border-opacity-25 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-folder2-open text-warning me-2"></i> Phần: <?php echo htmlspecialchars($part['title']); ?></h5>
                    <div>
                        <button class="btn btn-sm btn-outline-warning me-1"
                            onclick="showEditPartModal(<?php echo $part['id']; ?>, '<?php echo addslashes(htmlspecialchars($part['title'])); ?>')">
                            <i class="bi bi-pencil"></i> Sửa
                        </button>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="showChapterModal(<?php echo $part['id']; ?>)"><i class="bi bi-plus"></i> Thêm Chương</button>
                        <form action="<?php echo APP_URL; ?>/admin/courses/content/deletePart" method="POST" class="d-inline" onsubmit="return confirm('Xóa toàn bộ nội dung Phần này?');">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $part['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="accordion" id="accordionPart<?php echo $part['id']; ?>">
                        <!-- Loop Chapters -->
                        <?php foreach($part['chapters'] as $chapter): ?>
                            <div class="accordion-item mb-2 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChap<?php echo $chapter['id']; ?>">
                                        <i class="bi bi-journal-bookmark text-primary me-2"></i> Chương: <?php echo htmlspecialchars($chapter['title']); ?>
                                    </button>
                                </h2>
                                <div id="collapseChap<?php echo $chapter['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionPart<?php echo $part['id']; ?>">
                                    <div class="accordion-body bg-light p-2">
                                        <div class="mb-2 text-end">
                                            <button class="btn btn-sm btn-outline-warning me-1"
                                                onclick="showEditChapterModal(<?php echo $chapter['id']; ?>, '<?php echo addslashes(htmlspecialchars($chapter['title'])); ?>')">
                                                <i class="bi bi-pencil"></i> Sửa chương
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="showLessonModal(<?php echo $chapter['id']; ?>)"><i class="bi bi-plus"></i> Thêm Bài học</button>
                                            <form action="<?php echo APP_URL; ?>/admin/courses/content/deleteChapter" method="POST" class="d-inline ms-1" onsubmit="return confirm('Xóa Chương này?');">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <input type="hidden" name="id" value="<?php echo $chapter['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger px-2 py-1"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>

                                        <!-- Loop Lessons -->
                                        <ul class="list-group">
                                            <?php foreach($chapter['lessons'] as $lesson): ?>
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <strong class="text-dark">
                                                            <i class="bi bi-play-circle text-danger me-2"></i>
                                                            Bài: <?php echo htmlspecialchars($lesson['title']); ?>
                                                            <?php if($lesson['is_free_preview']) echo '<span class="badge bg-success ms-2">Học thử</span>'; ?>
                                                        </strong>
                                                        <div>
                                                            <button class="btn btn-sm btn-outline-warning me-1"
                                                                onclick="showEditLessonModal(<?php echo $lesson['id']; ?>, '<?php echo addslashes(htmlspecialchars($lesson['title'])); ?>', <?php echo $lesson['is_free_preview']; ?>)">
                                                                <i class="bi bi-pencil"></i> Sửa
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-info" onclick="showItemModal(<?php echo $lesson['id']; ?>)"><i class="bi bi-file-earmark-plus"></i> Thêm Nội dung</button>
                                                            <form action="<?php echo APP_URL; ?>/admin/courses/content/deleteLesson" method="POST" class="d-inline ms-1" onsubmit="return confirm('Xóa Bài học này?');">
                                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                <input type="hidden" name="id" value="<?php echo $lesson['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <!-- Loop Items -->
                                                    <?php if(!empty($lesson['items'])): ?>
                                                        <div class="ps-4 border-start border-2 border-info mt-2">
                                                            <?php foreach($lesson['items'] as $item): ?>
                                                                <div class="d-flex justify-content-between align-items-center mb-1 bg-white p-2 rounded border">
                                                                    <span>
                                                                        <?php if($item['type'] == 'video') echo '<i class="bi bi-youtube text-danger me-2"></i> [Video]'; else echo '<i class="bi bi-file-text text-primary me-2"></i> [Văn bản/Khác]'; ?>
                                                                        <small class="text-muted text-truncate d-inline-block" style="max-width:300px; vertical-align: bottom;"><?php echo htmlspecialchars(strip_tags($item['content'])); ?></small>
                                                                    </span>
                                                                    <form action="<?php echo APP_URL; ?>/admin/courses/content/deleteItem" method="POST" class="d-inline ms-1" onsubmit="return confirm('Xóa nội dung này?');">
                                                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-light text-danger"><i class="bi bi-x-circle"></i></button>
                                                                    </form>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <!-- End Lessons -->
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!-- End Chapters -->
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <!-- End Parts -->
        <?php if(empty($parts)): ?>
            <div class="alert alert-info text-center py-5">
                Chưa có phần nào được tạo. Hãy nhấn nút <strong>Thêm Phần mới</strong> ở góc trên.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODALS -->

<!-- Modal Add Part -->
<div class="modal fade" id="addPartModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storePart" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-folder-plus text-warning me-2"></i>Thêm Phần</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Tiêu đề Phần</label>
                    <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Phần 1: Nhập môn">
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
</div>

<!-- Modal Edit Part -->
<div class="modal fade" id="editPartModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/updatePart" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="id" id="edit_part_id">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-pencil-square text-warning me-2"></i>Sửa Phần</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tiêu đề Phần</label>
                    <input type="text" name="title" id="edit_part_title" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Chapter -->
<div class="modal fade" id="addChapterModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storeChapter" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="part_id" id="chapter_part_id">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-journal-plus text-primary me-2"></i>Thêm Chương</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Tiêu đề Chương</label>
                    <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Chương 1: Kiến thức cơ bản">
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
</div>

<!-- Modal Edit Chapter -->
<div class="modal fade" id="editChapterModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/updateChapter" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="id" id="edit_chapter_id">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-pencil-square text-warning me-2"></i>Sửa Chương</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tiêu đề Chương</label>
                    <input type="text" name="title" id="edit_chapter_title" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Lesson -->
<div class="modal fade" id="addLessonModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storeLesson" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="chapter_id" id="lesson_chapter_id">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-play-circle text-success me-2"></i>Thêm Bài học</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Tiêu đề Bài</label>
                    <input type="text" name="title" class="form-control" required placeholder="Ví dụ: Bài 1: Cài đặt môi trường">
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_free_preview" id="is_free" value="1">
                    <label class="form-check-label" for="is_free">Cho phép học thử (Miễn phí)</label>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
</div>

<!-- Modal Edit Lesson -->
<div class="modal fade" id="editLessonModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/updateLesson" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="id" id="edit_lesson_id">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-pencil-square text-warning me-2"></i>Sửa Bài học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tiêu đề Bài học</label>
                    <input type="text" name="title" id="edit_lesson_title" class="form-control" required>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_free_preview" id="edit_lesson_free" value="1">
                    <label class="form-check-label" for="edit_lesson_free">Cho phép học thử (Miễn phí)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Item -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storeItem" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="lesson_id" id="item_lesson_id">
            <div class="modal-header"><h5 class="modal-title">Thêm Nội dung vào bài học</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Loại nội dung</label>
                    <select name="type" class="form-select" id="item_type" onchange="toggleContentInput()">
                        <option value="video">Video (Link Youtube/Vimeo)</option>
                        <option value="text">Văn bản / Tài liệu (HTML)</option>
                    </select>
                </div>
                <div class="mb-3" id="video_input_div">
                    <label>Link Video (Youtube/Vimeo/Google Drive MP4)</label>
                    <input type="text" id="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div class="mb-3 d-none" id="text_input_div">
                    <label>Nội dung Văn bản</label>
                    <textarea id="editor_item" class="form-control"></textarea>
                </div>
                <!-- Hidden input chứa nội dung thật gửi lên -->
                <textarea name="content" id="real_content" class="d-none"></textarea>
            </div>
            <div class="modal-footer"><button type="button" onclick="submitItemForm(this)" class="btn btn-primary">Lưu Nội dung</button></div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#editor_item',
        height: 300,
        plugins: 'link image code',
        toolbar: 'undo redo | bold italic | link image | code',
        menubar: false
    });

    function showModal(id) {
        new bootstrap.Modal(document.getElementById(id)).show();
    }

    // ── ADD MODALS ──────────────────────────────────────────────
    function showChapterModal(partId) {
        document.getElementById('chapter_part_id').value = partId;
        showModal('addChapterModal');
    }

    function showLessonModal(chapterId) {
        document.getElementById('lesson_chapter_id').value = chapterId;
        showModal('addLessonModal');
    }

    function showItemModal(lessonId) {
        document.getElementById('item_lesson_id').value = lessonId;
        document.getElementById('video_url').value = '';
        if(tinymce.get('editor_item')) tinymce.get('editor_item').setContent('');
        showModal('addItemModal');
    }

    // ── EDIT MODALS ─────────────────────────────────────────────
    function showEditPartModal(id, title) {
        document.getElementById('edit_part_id').value = id;
        document.getElementById('edit_part_title').value = title;
        showModal('editPartModal');
    }

    function showEditChapterModal(id, title) {
        document.getElementById('edit_chapter_id').value = id;
        document.getElementById('edit_chapter_title').value = title;
        showModal('editChapterModal');
    }

    function showEditLessonModal(id, title, isFree) {
        document.getElementById('edit_lesson_id').value = id;
        document.getElementById('edit_lesson_title').value = title;
        document.getElementById('edit_lesson_free').checked = (isFree == 1);
        showModal('editLessonModal');
    }

    // ── ITEM FORM ────────────────────────────────────────────────
    function toggleContentInput() {
        let type = document.getElementById('item_type').value;
        if(type === 'video') {
            document.getElementById('video_input_div').classList.remove('d-none');
            document.getElementById('text_input_div').classList.add('d-none');
        } else {
            document.getElementById('video_input_div').classList.add('d-none');
            document.getElementById('text_input_div').classList.remove('d-none');
        }
    }

    function submitItemForm(btn) {
        let type = document.getElementById('item_type').value;
        if(type === 'video') {
            document.getElementById('real_content').value = document.getElementById('video_url').value;
        } else {
            document.getElementById('real_content').value = tinymce.get('editor_item').getContent();
        }
        btn.closest('form').submit();
    }
</script>
