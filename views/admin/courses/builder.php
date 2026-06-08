<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-primary">Đề cương Khóa học: <?php echo htmlspecialchars($course['title']); ?></h5>
            <small class="text-muted"><a href="<?php echo APP_URL; ?>/admin/courses" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Quay lại Danh sách</a></small>
        </div>
        <button class="btn btn-primary" onclick="showModal('addPartModal')"><i class="bi bi-plus-circle me-1"></i> Thêm Phần mới</button>
    </div>
    <div class="card-body bg-light">
        <?php foreach($parts as $part): ?>
            <div class="card mb-3 border border-secondary border-opacity-25 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-folder2-open text-warning me-2"></i> Phần: <?php echo htmlspecialchars($part['title']); ?></h5>
                    <div>
                        <!-- Reorder Part Up -->
                        <form action="<?php echo APP_URL; ?>/admin/courses/content/reorderPart" method="POST" class="d-inline m-0">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $part['id']; ?>">
                            <input type="hidden" name="direction" value="up">
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Di chuyển Phần lên"><i class="bi bi-arrow-up"></i></button>
                        </form>
                        <!-- Reorder Part Down -->
                        <form action="<?php echo APP_URL; ?>/admin/courses/content/reorderPart" method="POST" class="d-inline m-0 me-2">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $part['id']; ?>">
                            <input type="hidden" name="direction" value="down">
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Di chuyển Phần xuống"><i class="bi bi-arrow-down"></i></button>
                        </form>

                        <button class="btn btn-sm btn-outline-warning me-1" onclick="showEditPartModal(<?php echo $part['id']; ?>, '<?php echo addslashes(htmlspecialchars($part['title'])); ?>')"><i class="bi bi-pencil"></i> Sửa</button>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="showChapterModal(<?php echo $part['id']; ?>)"><i class="bi bi-plus"></i> Thêm Chương</button>
                        <form action="<?php echo APP_URL; ?>/admin/courses/content/deletePart" method="POST" class="d-inline" onsubmit="return confirm('Xóa toàn bộ Phần này?');">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $part['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="accordion" id="accordionPart<?php echo $part['id']; ?>">
                        <?php foreach($part['chapters'] as $chapter): ?>
                            <div class="accordion-item mb-2 border">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChap<?php echo $chapter['id']; ?>">
                                        <i class="bi bi-journal-bookmark text-primary me-2"></i> Chương: <?php echo htmlspecialchars($chapter['title']); ?>
                                    </button>
                                </h2>
                                <div id="collapseChap<?php echo $chapter['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionPart<?php echo $part['id']; ?>">
                                    <div class="accordion-body bg-light p-2">
                                        <div class="mb-2 d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-1">
                                                <!-- Reorder Chapter Up -->
                                                <form action="<?php echo APP_URL; ?>/admin/courses/content/reorderChapter" method="POST" class="d-inline m-0">
                                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $chapter['id']; ?>">
                                                    <input type="hidden" name="direction" value="up">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Di chuyển Chương lên"><i class="bi bi-arrow-up"></i></button>
                                                </form>
                                                <!-- Reorder Chapter Down -->
                                                <form action="<?php echo APP_URL; ?>/admin/courses/content/reorderChapter" method="POST" class="d-inline m-0">
                                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $chapter['id']; ?>">
                                                    <input type="hidden" name="direction" value="down">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Di chuyển Chương xuống"><i class="bi bi-arrow-down"></i></button>
                                                </form>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-warning me-1" onclick="showEditChapterModal(<?php echo $chapter['id']; ?>, '<?php echo addslashes(htmlspecialchars($chapter['title'])); ?>')"><i class="bi bi-pencil"></i> Sửa chương</button>
                                                <button class="btn btn-sm btn-success" onclick="showLessonModal(<?php echo $chapter['id']; ?>)"><i class="bi bi-plus"></i> Thêm Bài học</button>
                                                <form action="<?php echo APP_URL; ?>/admin/courses/content/deleteChapter" method="POST" class="d-inline ms-1" onsubmit="return confirm('Xóa Chương này?');">
                                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $chapter['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger px-2 py-1"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                        <ul class="list-group">
                                            <?php foreach($chapter['lessons'] as $lesson): ?>
                                                <li class="list-group-item p-3">
                                                    <!-- Lesson Header -->
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <strong class="text-dark">
                                                            <i class="bi bi-play-circle text-danger me-2"></i>
                                                            Bài: <?php echo htmlspecialchars($lesson['title']); ?>
                                                            <?php if($lesson['is_free_preview']) echo '<span class="badge bg-success ms-2">Học thử</span>'; ?>
                                                        </strong>
                                                        <div class="d-flex gap-1 flex-wrap justify-content-end">
                                                            <!-- Reorder Lesson Up -->
                                                            <form action="<?php echo APP_URL; ?>/admin/courses/content/reorderLesson" method="POST" class="d-inline m-0">
                                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                <input type="hidden" name="id" value="<?php echo $lesson['id']; ?>">
                                                                <input type="hidden" name="direction" value="up">
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Di chuyển Bài lên"><i class="bi bi-arrow-up"></i></button>
                                                            </form>
                                                            <!-- Reorder Lesson Down -->
                                                            <form action="<?php echo APP_URL; ?>/admin/courses/content/reorderLesson" method="POST" class="d-inline m-0 me-2">
                                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                <input type="hidden" name="id" value="<?php echo $lesson['id']; ?>">
                                                                <input type="hidden" name="direction" value="down">
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Di chuyển Bài xuống"><i class="bi bi-arrow-down"></i></button>
                                                            </form>

                                                            <button class="btn btn-sm btn-outline-warning" onclick="showEditLessonModal(<?php echo $lesson['id']; ?>, '<?php echo addslashes(htmlspecialchars($lesson['title'])); ?>', <?php echo $lesson['is_free_preview']; ?>, <?php echo $lesson['allow_comments'] ?? 1; ?>)"><i class="bi bi-pencil"></i> Sửa</button>
                                                            <button class="btn btn-sm btn-outline-info" onclick="showItemModal(<?php echo $lesson['id']; ?>)"><i class="bi bi-file-earmark-plus"></i> Nội dung</button>
                                                            <button class="btn btn-sm btn-outline-secondary" onclick="showAttachmentModal(<?php echo $lesson['id']; ?>)"><i class="bi bi-paperclip"></i> Đính kèm</button>
                                                            <a href="<?php echo APP_URL; ?>/admin/quizzes/create?lesson_id=<?php echo $lesson['id']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-trophy"></i> Trắc nghiệm</a>
                                                            <button class="btn btn-sm btn-outline-success" onclick="showAddAssignmentModal(<?php echo $lesson['id']; ?>)"><i class="bi bi-journal-check"></i> Bài tập</button>
                                                            <form action="<?php echo APP_URL; ?>/admin/courses/content/deleteLesson" method="POST" class="d-inline" onsubmit="return confirm('Xóa Bài học này?');">
                                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                <input type="hidden" name="id" value="<?php echo $lesson['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <?php if(!empty($lesson['items'])): ?>
                                                        <div class="ps-3 border-start border-2 border-info mt-2 mb-2">
                                                            <?php foreach($lesson['items'] as $item): ?>
                                                                <div class="d-flex justify-content-between align-items-center mb-1 bg-white p-2 rounded border shadow-xs">
                                                                    <span class="text-truncate" style="max-width: 65%;">
                                                                        <?php if($item['type'] == 'video'): ?>
                                                                            <i class="bi bi-youtube text-danger me-2"></i><span class="badge bg-danger me-1">Video</span>
                                                                        <?php elseif($item['type'] == 'pdf'): ?>
                                                                            <i class="bi bi-file-pdf text-danger me-2"></i><span class="badge bg-warning text-dark me-1">PDF</span>
                                                                        <?php elseif($item['type'] == 'quiz'): ?>
                                                                            <i class="bi bi-trophy text-warning me-2"></i><span class="badge bg-warning text-dark me-1">Trắc nghiệm</span>
                                                                            <a href="<?php echo APP_URL; ?>/admin/quizzes/questions?quiz_id=<?php echo $item['content']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-xs btn-outline-warning btn-sm ms-1" style="padding:1px 4px;font-size:.65rem;">Quản lý</a>
                                                                            <a href="<?php echo APP_URL; ?>/admin/quizzes/edit?id=<?php echo $item['content']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-xs btn-outline-primary btn-sm ms-1" style="padding:1px 4px;font-size:.65rem;"><i class="bi bi-pencil-square"></i> Sửa đề</a>
                                                                        <?php elseif($item['type'] == 'assignment_essay'): ?>
                                                                            <i class="bi bi-journal-text text-success me-2"></i><span class="badge bg-success me-1">BT Tự luận</span>
                                                                            <a href="<?php echo APP_URL; ?>/admin/assignments/submissions?assignment_id=<?php echo $item['content']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-success ms-1" style="padding:1px 4px;font-size:.65rem;">Xem bài nộp</a>
                                                                        <?php elseif($item['type'] == 'assignment_file'): ?>
                                                                            <i class="bi bi-cloud-upload text-info me-2"></i><span class="badge bg-info me-1">BT Nộp file</span>
                                                                            <a href="<?php echo APP_URL; ?>/admin/assignments/submissions?assignment_id=<?php echo $item['content']; ?>&course_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-info ms-1" style="padding:1px 4px;font-size:.65rem;">Xem bài nộp</a>
                                                                        <?php else: ?>
                                                                            <i class="bi bi-file-text text-primary me-2"></i><span class="badge bg-primary me-1">Văn bản</span>
                                                                        <?php endif; ?>
                                                                        <?php if(!in_array($item['type'], ['quiz','assignment_essay','assignment_file'])): ?>
                                                                        <small class="text-muted text-truncate d-inline-block" style="max-width:280px; vertical-align:bottom;"><?php echo htmlspecialchars(strip_tags($item['content'])); ?></small>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <!-- Reorder Up -->
                                                                        <form action="<?php echo APP_URL; ?>/admin/courses/content/reorderItem" method="POST" class="d-inline m-0">
                                                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                                            <input type="hidden" name="direction" value="up">
                                                                            <button type="submit" class="btn btn-sm btn-light py-0 px-1 border-0" title="Di chuyển lên"><i class="bi bi-arrow-up text-secondary"></i></button>
                                                                        </form>
                                                                        <!-- Reorder Down -->
                                                                        <form action="<?php echo APP_URL; ?>/admin/courses/content/reorderItem" method="POST" class="d-inline m-0">
                                                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                                            <input type="hidden" name="direction" value="down">
                                                                            <button type="submit" class="btn btn-sm btn-light py-0 px-1 border-0" title="Di chuyển xuống"><i class="bi bi-arrow-down text-secondary"></i></button>
                                                                        </form>

                                                                        <!-- Edit button (Text, Video, PDF or Assignment) -->
                                                                        <?php if(in_array($item['type'], ['video', 'text', 'pdf'])): ?>
                                                                            <button class="btn btn-sm btn-light py-0 px-1 border-0 text-warning" onclick="showEditItemModal(<?php echo $item['id']; ?>, '<?php echo $item['type']; ?>', <?php echo htmlspecialchars(json_encode($item['content'])); ?>)" title="Sửa nội dung"><i class="bi bi-pencil-square"></i></button>
                                                                        <?php elseif(in_array($item['type'], ['assignment_essay', 'assignment_file'])): ?>
                                                                            <?php
                                                                                $asgnStmt = Database::getInstance()->getConnection()->prepare("SELECT * FROM assignments WHERE id = ?");
                                                                                $asgnStmt->execute([$item['content']]);
                                                                                $asgnDetails = $asgnStmt->fetch();
                                                                            ?>
                                                                            <?php if($asgnDetails): ?>
                                                                                <button class="btn btn-sm btn-light py-0 px-1 border-0 text-warning" onclick="showEditAssignmentModal(<?php echo htmlspecialchars(json_encode($asgnDetails)); ?>)" title="Sửa bài tập"><i class="bi bi-pencil-square"></i></button>
                                                                            <?php endif; ?>
                                                                        <?php endif; ?>

                                                                        <form action="<?php echo APP_URL; ?>/admin/courses/content/deleteItem" method="POST" class="d-inline m-0" onsubmit="return confirm('Xóa nội dung này?');">
                                                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                                            <button type="submit" class="btn btn-sm btn-light py-0 px-1 border-0 text-danger" title="Xóa"><i class="bi bi-x-circle"></i></button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Attachments -->
                                                    <?php if(!empty($lesson['attachments'])): ?>
                                                        <div class="ps-3 border-start border-2 border-success mt-1">
                                                            <small class="text-muted fw-semibold"><i class="bi bi-paperclip me-1"></i>Tập tin đính kèm:</small>
                                                            <?php foreach($lesson['attachments'] as $att): ?>
                                                                <div class="d-flex justify-content-between align-items-center mt-1 bg-white p-2 rounded border border-success border-opacity-25">
                                                                    <span>
                                                                        <i class="bi bi-file-earmark-arrow-down text-success me-1"></i>
                                                                        <small><?php echo htmlspecialchars($att['name']); ?></small>
                                                                        <span class="badge bg-light text-muted ms-1"><?php echo htmlspecialchars($att['file_size']); ?></span>
                                                                    </span>
                                                                    <form action="<?php echo APP_URL; ?>/admin/courses/content/deleteAttachment" method="POST" class="d-inline" onsubmit="return confirm('Xóa file đính kèm này?');">
                                                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                                        <input type="hidden" name="id" value="<?php echo $att['id']; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-light text-danger"><i class="bi bi-x-circle"></i></button>
                                                                    </form>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($parts)): ?>
            <div class="alert alert-info text-center py-5">Chưa có phần nào. Hãy nhấn <strong>Thêm Phần mới</strong> ở góc trên.</div>
        <?php endif; ?>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- MODALS                                                      -->
<!-- ══════════════════════════════════════════════════════════ -->

<!-- Add Part -->
<div class="modal fade" id="addPartModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storePart" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-folder-plus text-warning me-2"></i>Thêm Phần</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="mb-3"><label>Tiêu đề Phần</label><input type="text" name="title" class="form-control" required placeholder="Ví dụ: Phần 1: Nhập môn"></div></div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
</div>

<!-- Edit Part -->
<div class="modal fade" id="editPartModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/updatePart" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="id" id="edit_part_id">
            <div class="modal-header bg-warning bg-opacity-10"><h5 class="modal-title"><i class="bi bi-pencil-square text-warning me-2"></i>Sửa Phần</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="mb-3"><label class="form-label fw-semibold">Tiêu đề Phần</label><input type="text" name="title" id="edit_part_title" class="form-control" required></div></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button><button type="submit" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Lưu thay đổi</button></div>
        </form>
    </div>
</div>

<!-- Add Chapter -->
<div class="modal fade" id="addChapterModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storeChapter" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="part_id" id="chapter_part_id">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-journal-plus text-primary me-2"></i>Thêm Chương</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="mb-3"><label>Tiêu đề Chương</label><input type="text" name="title" class="form-control" required placeholder="Ví dụ: Chương 1: Kiến thức cơ bản"></div></div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
</div>

<!-- Edit Chapter -->
<div class="modal fade" id="editChapterModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/updateChapter" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="id" id="edit_chapter_id">
            <div class="modal-header bg-warning bg-opacity-10"><h5 class="modal-title"><i class="bi bi-pencil-square text-warning me-2"></i>Sửa Chương</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><div class="mb-3"><label class="form-label fw-semibold">Tiêu đề Chương</label><input type="text" name="title" id="edit_chapter_title" class="form-control" required></div></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button><button type="submit" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Lưu thay đổi</button></div>
        </form>
    </div>
</div>

<!-- Add Lesson -->
<div class="modal fade" id="addLessonModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storeLesson" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="chapter_id" id="lesson_chapter_id">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-play-circle text-success me-2"></i>Thêm Bài học</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label>Tiêu đề Bài</label><input type="text" name="title" class="form-control" required placeholder="Ví dụ: Bài 1: Cài đặt môi trường"></div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_free_preview" id="is_free" value="1">
                    <label class="form-check-label" for="is_free">Cho phép học thử (Miễn phí)</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="allow_comments" id="add_lesson_allow_comments" value="1" checked>
                    <label class="form-check-label" for="add_lesson_allow_comments">Cho phép bình luận</label>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
</div>

<!-- Edit Lesson -->
<div class="modal fade" id="editLessonModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/updateLesson" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="id" id="edit_lesson_id">
            <div class="modal-header bg-warning bg-opacity-10"><h5 class="modal-title"><i class="bi bi-pencil-square text-warning me-2"></i>Sửa Bài học</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label fw-semibold">Tiêu đề Bài học</label><input type="text" name="title" id="edit_lesson_title" class="form-control" required></div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_free_preview" id="edit_lesson_free" value="1">
                    <label class="form-check-label" for="edit_lesson_free">Cho phép học thử (Miễn phí)</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="allow_comments" id="edit_lesson_allow_comments" value="1">
                    <label class="form-check-label" for="edit_lesson_allow_comments">Cho phép bình luận</label>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button><button type="submit" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Lưu thay đổi</button></div>
        </form>
    </div>
</div>

<!-- Add Item: Video / Text (form thông thường, không cần enctype) -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storeItem" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="lesson_id" id="item_lesson_id">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-file-earmark-plus text-info me-2"></i>Thêm Nội dung bài học</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Loại nội dung</label>
                    <select name="type" class="form-select" id="item_type" onchange="toggleContentInput()">
                        <option value="video">🎬 Video (Link Youtube / Vimeo)</option>
                        <option value="text">📝 Văn bản / Tài liệu (HTML)</option>
                        <option value="pdf">📄 File PDF (xem trực tiếp)</option>
                    </select>
                </div>
                <!-- Video -->
                <div class="mb-3" id="video_input_div">
                    <label class="form-label">Link Video (Youtube / Vimeo / Google Drive MP4)</label>
                    <input type="text" id="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <!-- Text -->
                <div class="mb-3 d-none" id="text_input_div">
                    <label class="form-label">Nội dung Văn bản</label>
                    <textarea id="editor_item" class="form-control tinymce-editor"></textarea>
                </div>
                <!-- PDF notice -->
                <div class="mb-3 d-none" id="pdf_notice_div">
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <span>Để upload file PDF, vui lòng nhấn nút <strong>Upload PDF</strong> bên dưới. Cửa sổ này sẽ đóng và mở form upload chuyên dụng.</span>
                    </div>
                </div>
                <!-- Hidden textarea cho video/text -->
                <textarea name="content" id="real_content" class="d-none"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="pdf_switch_btn" class="btn btn-warning text-white d-none" onclick="switchToPdfModal()"><i class="bi bi-file-pdf me-1"></i>Upload PDF</button>
                <button type="button" id="item_submit_btn" onclick="submitItemForm(this)" class="btn btn-primary"><i class="bi bi-save me-1"></i>Lưu Nội dung</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Item Modal (Video, Text, PDF) -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/updateItem" method="POST" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="id" id="edit_item_id">
            <input type="hidden" name="type" id="edit_item_type">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-warning me-2"></i>Sửa nội dung bài học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start text-dark">
                <!-- Video -->
                <div class="mb-3 d-none" id="edit_video_input_div">
                    <label class="form-label fw-semibold">Link Video (Youtube / Vimeo / Google Drive MP4)</label>
                    <input type="text" id="edit_video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <!-- Text -->
                <div class="mb-3 d-none" id="edit_text_input_div">
                    <label class="form-label fw-semibold">Nội dung Văn bản</label>
                    <textarea id="edit_editor_item" class="form-control tinymce-editor"></textarea>
                </div>
                <!-- PDF upload -->
                <div class="mb-3 d-none" id="edit_pdf_input_div">
                    <label class="form-label fw-semibold">Tải lên file PDF mới <span class="text-muted fw-normal">(để trống nếu muốn giữ nguyên file cũ)</span></label>
                    <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                    <div class="mt-2 text-info small" id="edit_pdf_current_path"></div>
                </div>
                <textarea name="content" id="edit_real_content" class="d-none"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" onclick="submitEditItemForm(this)" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Item: PDF riêng biệt (dùng enctype multipart/form-data) -->
<div class="modal fade" id="addPdfModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storeItem" method="POST" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="lesson_id" id="pdf_lesson_id">
            <input type="hidden" name="type" value="pdf">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-file-pdf text-danger me-2"></i>Upload File PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Chọn file PDF <span class="text-muted fw-normal">(tối đa 50MB)</span></label>
                    <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                    <div class="form-text"><i class="bi bi-info-circle text-info me-1"></i>Học viên sẽ xem PDF trực tiếp trên trình duyệt trong bài học.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-upload me-1"></i>Tải lên PDF</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Attachment -->
<div class="modal fade" id="addAttachmentModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo APP_URL; ?>/admin/courses/content/storeAttachment" method="POST" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="lesson_id" id="attachment_lesson_id">
            <div class="modal-header bg-success bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-paperclip text-success me-2"></i>Thêm Tập tin đính kèm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Chọn tập tin <span class="text-muted fw-normal">(tối đa 100MB)</span></label>
                    <input type="file" name="attachment_file" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.mp3,.mp4,.png,.jpg,.jpeg">
                    <div class="form-text"><i class="bi bi-info-circle text-success me-1"></i>Học viên có thể tải file này về máy trong bài học. Chấp nhận: PDF, Word, Excel, PowerPoint, ZIP, hình ảnh, âm thanh, video.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i>Tải lên</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Them Bai tap (Tu luan / Nop file) -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?php echo APP_URL; ?>/admin/assignments/store" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="lesson_id" id="asgn_lesson_id">
            <div class="modal-header bg-success bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-journal-check text-success me-2"></i>Tạo Bài tập mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start text-dark">
                <div class="row g-3">
                    <div class="col-md-8"><label class="form-label fw-semibold">Tiêu đề bài tập *</label><input type="text" name="title" class="form-control" required placeholder="Bài tập cuối chương 1..."></div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Loại bài tập</label>
                        <select name="type" class="form-select" id="asgn_type" onchange="toggleDriveFolderField()">
                            <option value="essay">📝 Tự luận (nhập văn bản)</option>
                            <option value="file">📁 Nộp file (Google Drive)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Mô tả / Đề bài</label>
                        <textarea name="description" id="asgn_description" class="form-control tinymce-editor" placeholder="Mô tả đề bài..."></textarea>
                    </div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Điểm tối đa</label><input type="number" name="max_score" class="form-control" value="10" min="1" step="0.5"></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Hạn nộp (tùy chọn)</label><input type="datetime-local" name="due_date" class="form-control"></div>
                    <!-- Folder ID chi hien khi chon loai Nop file -->
                    <div class="col-12 d-none" id="drive_folder_div">
                        <label class="form-label fw-semibold"><i class="bi bi-google text-primary me-1"></i>Google Drive Folder ID *</label>
                        <input type="text" name="drive_folder_id" id="drive_folder_id_input" class="form-control font-monospace" placeholder="1AbCdEfGhIjKlMnOpQrStUvWxYz...">
                        <div class="form-text"><i class="bi bi-info-circle text-info me-1"></i>Lấy Folder ID từ URL của thư mục Google Drive: <code>https://drive.google.com/drive/folders/<strong>[ID_ở_đây]</strong></code>. Thư mục phải được chia sẻ với email Service Account.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" onclick="submitAssignmentForm(this)" class="btn btn-success"><i class="bi bi-save me-1"></i>Tạo bài tập</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Sua Bai tap (Tu luan / Nop file) -->
<div class="modal fade" id="editAssignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="<?php echo APP_URL; ?>/admin/assignments/update" method="POST" class="modal-content">
            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
            <input type="hidden" name="id" id="edit_asgn_id">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-warning me-2"></i>Sửa bài tập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start text-dark">
                <div class="row g-3">
                    <div class="col-md-8"><label class="form-label fw-semibold">Tiêu đề bài tập *</label><input type="text" name="title" id="edit_asgn_title" class="form-control" required></div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Loại bài tập</label>
                        <select name="type" class="form-select" id="edit_asgn_type" onchange="toggleEditDriveFolderField()">
                            <option value="essay">📝 Tự luận (nhập văn bản)</option>
                            <option value="file">📁 Nộp file (Google Drive)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Mô tả / Đề bài</label>
                        <textarea name="description" id="edit_asgn_description" class="form-control tinymce-editor"></textarea>
                    </div>
                    <div class="col-md-4"><label class="form-label fw-semibold">Điểm tối đa</label><input type="number" name="max_score" id="edit_asgn_max_score" class="form-control" min="1" step="0.5"></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Hạn nộp (tùy chọn)</label><input type="datetime-local" name="due_date" id="edit_asgn_due_date" class="form-control"></div>
                    <!-- Folder ID chi hien khi chon loai Nop file -->
                    <div class="col-12 d-none" id="edit_drive_folder_div">
                        <label class="form-label fw-semibold"><i class="bi bi-google text-primary me-1"></i>Google Drive Folder ID *</label>
                        <input type="text" name="drive_folder_id" id="edit_drive_folder_id_input" class="form-control font-monospace">
                        <div class="form-text"><i class="bi bi-info-circle text-info me-1"></i>Lấy Folder ID từ URL của thư mục Google Drive.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" onclick="submitEditAssignmentForm(this)" class="btn btn-warning text-white"><i class="bi bi-save me-1"></i>Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showModal(id) { new bootstrap.Modal(document.getElementById(id)).show(); }

    // Add modals
    function showChapterModal(partId)   { document.getElementById('chapter_part_id').value = partId; showModal('addChapterModal'); }
    function showLessonModal(chapterId) { document.getElementById('lesson_chapter_id').value = chapterId; showModal('addLessonModal'); }
    function showItemModal(lessonId) {
        document.getElementById('item_lesson_id').value = lessonId;
        document.getElementById('video_url').value = '';
        document.getElementById('item_type').value = 'video';
        let editor = (typeof tinymce !== 'undefined') ? tinymce.get('editor_item') : null;
        if (editor) {
            editor.setContent('');
        } else {
            document.getElementById('editor_item').value = '';
        }
        toggleContentInput();
        showModal('addItemModal');
    }
    function showAttachmentModal(lessonId) { document.getElementById('attachment_lesson_id').value = lessonId; showModal('addAttachmentModal'); }
    function showAddAssignmentModal(lessonId) { 
        document.getElementById('asgn_lesson_id').value = lessonId; 
        let editor = (typeof tinymce !== 'undefined') ? tinymce.get('asgn_description') : null;
        if (editor) {
            editor.setContent('');
        } else {
            document.getElementById('asgn_description').value = '';
        }
        showModal('addAssignmentModal'); 
    }

    // Edit modals
    function showEditPartModal(id, title)    { document.getElementById('edit_part_id').value = id; document.getElementById('edit_part_title').value = title; showModal('editPartModal'); }
    function showEditChapterModal(id, title) { document.getElementById('edit_chapter_id').value = id; document.getElementById('edit_chapter_title').value = title; showModal('editChapterModal'); }
    function showEditLessonModal(id, title, isFree, allowComments) {
        document.getElementById('edit_lesson_id').value = id;
        document.getElementById('edit_lesson_title').value = title;
        document.getElementById('edit_lesson_free').checked = (isFree == 1);
        document.getElementById('edit_lesson_allow_comments').checked = (allowComments == 1);
        showModal('editLessonModal');
    }

    function showEditItemModal(id, type, content) {
        document.getElementById('edit_item_id').value = id;
        document.getElementById('edit_item_type').value = type;
        
        document.getElementById('edit_video_input_div').classList.add('d-none');
        document.getElementById('edit_text_input_div').classList.add('d-none');
        document.getElementById('edit_pdf_input_div').classList.add('d-none');
        
        if (type === 'video') {
            document.getElementById('edit_video_input_div').classList.remove('d-none');
            document.getElementById('edit_video_url').value = content;
        } else if (type === 'text') {
            document.getElementById('edit_text_input_div').classList.remove('d-none');
            let editor = (typeof tinymce !== 'undefined') ? tinymce.get('edit_editor_item') : null;
            if (editor) {
                editor.setContent(content);
            } else {
                document.getElementById('edit_editor_item').value = content;
            }
        } else if (type === 'pdf') {
            document.getElementById('edit_pdf_input_div').classList.remove('d-none');
            document.getElementById('edit_pdf_current_path').innerText = 'File hiện tại: ' + content;
        }
        
        showModal('editItemModal');
    }

    function showEditAssignmentModal(asgn) {
        document.getElementById('edit_asgn_id').value = asgn.id;
        document.getElementById('edit_asgn_title').value = asgn.title;
        document.getElementById('edit_asgn_type').value = asgn.type;
        document.getElementById('edit_asgn_max_score').value = asgn.max_score;
        
        if (asgn.due_date) {
            let d = new Date(asgn.due_date);
            let year = d.getFullYear();
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let day = String(d.getDate()).padStart(2, '0');
            let hours = String(d.getHours()).padStart(2, '0');
            let minutes = String(d.getMinutes()).padStart(2, '0');
            document.getElementById('edit_asgn_due_date').value = `${year}-${month}-${day}T${hours}:${minutes}`;
        } else {
            document.getElementById('edit_asgn_due_date').value = '';
        }
        
        if (asgn.drive_folder_id) {
            document.getElementById('edit_drive_folder_id_input').value = asgn.drive_folder_id;
        } else {
            document.getElementById('edit_drive_folder_id_input').value = '';
        }
        
        let editor = (typeof tinymce !== 'undefined') ? tinymce.get('edit_asgn_description') : null;
        if (editor) {
            editor.setContent(asgn.description || '');
        } else {
            document.getElementById('edit_asgn_description').value = asgn.description || '';
        }
        
        toggleEditDriveFolderField();
        showModal('editAssignmentModal');
    }

    // Toggle content input based on type
    // Toggle content input based on type
    function toggleContentInput() {
        let type = document.getElementById('item_type').value;
        document.getElementById('video_input_div').classList.toggle('d-none', type !== 'video');
        document.getElementById('text_input_div').classList.toggle('d-none', type !== 'text');
        document.getElementById('pdf_notice_div').classList.toggle('d-none', type !== 'pdf');
        // Show/hide buttons
        document.getElementById('pdf_switch_btn').classList.toggle('d-none', type !== 'pdf');
        document.getElementById('item_submit_btn').classList.toggle('d-none', type === 'pdf');
    }

    function switchToPdfModal() {
        let lessonId = document.getElementById('item_lesson_id').value;
        document.getElementById('pdf_lesson_id').value = lessonId;
        bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
        setTimeout(function() { showModal('addPdfModal'); }, 300);
    }

    function submitItemForm(btn) {
        let type = document.getElementById('item_type').value;
        if (type === 'video') {
            document.getElementById('real_content').value = document.getElementById('video_url').value;
        } else if (type === 'text') {
            let editor = (typeof tinymce !== 'undefined') ? tinymce.get('editor_item') : null;
            if (editor) {
                document.getElementById('real_content').value = editor.getContent();
            } else {
                document.getElementById('real_content').value = document.getElementById('editor_item').value;
            }
        }
        btn.closest('form').submit();
    }

    function submitEditItemForm(btn) {
        let type = document.getElementById('edit_item_type').value;
        if (type === 'video') {
            document.getElementById('edit_real_content').value = document.getElementById('edit_video_url').value;
        } else if (type === 'text') {
            let editor = (typeof tinymce !== 'undefined') ? tinymce.get('edit_editor_item') : null;
            if (editor) {
                document.getElementById('edit_real_content').value = editor.getContent();
            } else {
                document.getElementById('edit_real_content').value = document.getElementById('edit_editor_item').value;
            }
        }
        btn.closest('form').submit();
    }

    function submitAssignmentForm(btn) {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
        btn.closest('form').submit();
    }

    function submitEditAssignmentForm(btn) {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
        btn.closest('form').submit();
    }

    function toggleDriveFolderField() {
        let type = document.getElementById('asgn_type').value;
        let div  = document.getElementById('drive_folder_div');
        let inp  = document.getElementById('drive_folder_id_input');
        if (type === 'file') {
            div.classList.remove('d-none');
            inp.required = true;
        } else {
            div.classList.add('d-none');
            inp.required = false;
        }
    }

    function toggleEditDriveFolderField() {
        let type = document.getElementById('edit_asgn_type').value;
        let div  = document.getElementById('edit_drive_folder_div');
        let inp  = document.getElementById('edit_drive_folder_id_input');
        if (type === 'file') {
            div.classList.remove('d-none');
            inp.required = true;
        } else {
            div.classList.add('d-none');
            inp.required = false;
        }
    }
</script>
