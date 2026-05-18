<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Main Video/Content Area -->
        <div class="col-lg-9 bg-dark text-white d-flex flex-column" style="min-height: calc(100vh - 60px);">
            <div class="flex-grow-1 position-relative p-4" style="background: #111;">
                <?php if($current_lesson): ?>
                    <h3 class="fw-bold mb-4"><?php echo htmlspecialchars($current_lesson['title']); ?></h3>
                    
                    <?php foreach($current_items as $item): ?>
                        <?php if($item['type'] == 'video'): ?>
                            <!-- Video -->
                            <?php 
                                $url = $item['content'];
                                $embed_url = '';
                                if(strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ \s]{11})%i', $url, $match);
                                    $youtube_id = $match[1] ?? '';
                                    $embed_url = "https://www.youtube.com/embed/{$youtube_id}";
                                } elseif(strpos($url, 'vimeo.com') !== false) {
                                    $vimeo_id = substr(parse_url($url, PHP_URL_PATH), 1);
                                    $embed_url = "https://player.vimeo.com/video/{$vimeo_id}";
                                } else {
                                    $embed_url = $url;
                                }
                            ?>
                            <?php if($embed_url): ?>
                                <div class="ratio ratio-16x9 mb-4 shadow-lg rounded overflow-hidden border border-secondary border-opacity-25">
                                    <iframe src="<?php echo $embed_url; ?>" title="Video player" allowfullscreen></iframe>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning text-dark">Link video không được hỗ trợ. (<a href="<?php echo $url; ?>" target="_blank" class="text-primary"><?php echo $url; ?></a>)</div>
                            <?php endif; ?>
                        <?php elseif($item['type'] == 'text'): ?>
                            <!-- Text/HTML -->
                            <div class="bg-white text-dark p-4 rounded shadow-sm mb-4" style="font-size:1.1rem;line-height:1.8;">
                                <?php echo $item['content']; ?>
                            </div>
                        <?php elseif($item['type'] == 'pdf'): ?>
                            <!-- PDF Viewer -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-file-pdf text-danger fs-4 me-2"></i>
                                    <span class="fw-semibold text-white">Tài liệu PDF</span>
                                    <a href="<?php echo APP_URL . '/' . $item['content']; ?>" target="_blank" class="btn btn-sm btn-outline-light ms-3"><i class="bi bi-box-arrow-up-right me-1"></i>Mở trong tab mới</a>
                                </div>
                                <div class="rounded overflow-hidden border border-secondary" style="height:75vh;">
                                    <iframe src="<?php echo APP_URL . '/' . $item['content']; ?>" width="100%" height="100%" style="border:none;"></iframe>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <?php if(empty($current_items)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-camera-video-off display-1 text-white-50"></i>
                            <h4 class="mt-3 text-white-50">Bài học này chưa được cập nhật nội dung.</h4>
                        </div>
                    <?php endif; ?>

                    <!-- Attachments panel -->
                    <?php if(!empty($current_attachments)): ?>
                        <div class="mt-4 p-3 rounded" style="background:#1a1a2e;border:1px solid #2d2d44;">
                            <h6 class="text-white mb-3"><i class="bi bi-paperclip text-success me-2"></i>Tài liệu đính kèm</h6>
                            <div class="row g-2">
                                <?php foreach($current_attachments as $att): ?>
                                    <div class="col-md-6">
                                        <a href="<?php echo APP_URL . '/' . $att['file_path']; ?>" download="<?php echo htmlspecialchars($att['name']); ?>" class="d-flex align-items-center gap-2 p-2 rounded text-decoration-none" style="background:#111;border:1px solid #333;transition:background .2s;" onmouseover="this.style.background='#1e3a2f'" onmouseout="this.style.background='#111'">
                                            <i class="bi bi-file-earmark-arrow-down fs-4 text-success flex-shrink-0"></i>
                                            <div style="overflow:hidden;">
                                                <div class="text-white small fw-semibold text-truncate"><?php echo htmlspecialchars($att['name']); ?></div>
                                                <div class="text-muted" style="font-size:.78rem;"><?php echo htmlspecialchars($att['file_size']); ?></div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x display-1 text-white-50"></i>
                        <h4 class="mt-3 text-white-50">Khóa học chưa có bài học nào.</h4>
                    </div>
                <?php endif; ?>
            </div>
            <div class="p-3 bg-black border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                <a href="<?php echo APP_URL; ?>/course?slug=<?php echo $course['slug']; ?>" class="btn btn-outline-light"><i class="bi bi-arrow-left"></i> Quay lại thông tin khóa học</a>
                
                <?php if($current_lesson): ?>
                    <?php if($is_completed): ?>
                        <button class="btn btn-success disabled"><i class="bi bi-check-circle-fill me-1"></i> Đã hoàn thành</button>
                    <?php else: ?>
                        <form action="<?php echo APP_URL; ?>/learning/markCompleted" method="POST" class="m-0">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <input type="hidden" name="lesson_id" value="<?php echo $current_lesson['id']; ?>">
                            <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-check2 me-1"></i> Đánh dấu hoàn thành bài học</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Sidebar Playlist -->
        <div class="col-lg-3 bg-white border-start" style="height: calc(100vh - 60px); overflow-y: auto;">
            <div class="p-3 border-bottom sticky-top bg-white z-1">
                <h5 class="fw-bold text-primary mb-0"><i class="bi bi-list-stars me-2"></i>Nội dung khóa học</h5>
            </div>
            <div class="accordion accordion-flush" id="learningAccordion">
                <?php $i = 0; foreach($parts as $part): ?>
                    <div class="bg-light p-2 fw-bold text-muted small text-uppercase">Phần: <?php echo htmlspecialchars($part['title']); ?></div>
                    <?php foreach($part['chapters'] as $chapter): $i++; ?>
                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button py-3 fw-semibold bg-white text-dark shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#chap<?php echo $chapter['id']; ?>">
                                    Chương <?php echo $i; ?>: <?php echo htmlspecialchars($chapter['title']); ?>
                                </button>
                            </h2>
                            <div id="chap<?php echo $chapter['id']; ?>" class="accordion-collapse collapse show">
                                <div class="accordion-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <?php foreach($chapter['lessons'] as $lesson): ?>
                                            <a href="<?php echo APP_URL; ?>/learning?course_id=<?php echo $course['id']; ?>&lesson_id=<?php echo $lesson['id']; ?>" class="list-group-item list-group-item-action py-3 px-4 <?php echo ($current_lesson && $current_lesson['id'] == $lesson['id']) ? 'bg-primary text-white border-primary active' : 'text-dark'; ?>" style="border-radius: 0;">
                                                <div class="d-flex align-items-center">
                                                    <?php if($current_lesson && $current_lesson['id'] == $lesson['id']): ?>
                                                        <i class="bi bi-play-circle-fill me-3 fs-5"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-play-circle text-muted me-3 fs-5"></i>
                                                    <?php endif; ?>
                                                    <span style="font-size: 0.95rem;"><?php echo htmlspecialchars($lesson['title']); ?></span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
