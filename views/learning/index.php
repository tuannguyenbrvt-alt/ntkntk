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
                            <?php
                            $pdf_url = APP_URL . '/' . $item['content'];
                            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                            $isMobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) 
                                        || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|lf(r |l)|mo(bi|te)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|ab|id)|subi|link|webc|whit|wi(g |nc|nw)|wmlb|wonu|xda\-[0-9]/i', substr($userAgent, 0, 4));
                            
                            $embed_src = $pdf_url;
                            if ($isMobile) {
                                $embed_src = "https://docs.google.com/gview?url=" . urlencode($pdf_url) . "&embedded=true";
                            }
                            ?>
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-file-pdf text-danger fs-4 me-2"></i>
                                    <span class="fw-semibold text-white">Tài liệu PDF</span>
                                    <a href="<?php echo $pdf_url; ?>" target="_blank" class="btn btn-sm btn-outline-light ms-3"><i class="bi bi-box-arrow-up-right me-1"></i>Mở trong tab mới</a>
                                </div>
                                <div class="rounded overflow-hidden border border-secondary" style="height:75vh;">
                                    <iframe src="<?php echo $embed_src; ?>" width="100%" height="100%" style="border:none;"></iframe>
                                </div>
                            </div>

                        <?php elseif($item['type'] == 'quiz'): ?>
                            <!-- QUIZ -->
                            <?php
                            $quiz_id = (int)$item['content'];
                            $db = Database::getInstance()->getConnection();
                            $qz = $db->prepare("SELECT * FROM quizzes WHERE id=?"); $qz->execute([$quiz_id]); $quiz_info = $qz->fetch();
                            if ($quiz_info):
                                // Ket qua lan lam gan nhat
                                $la = $db->prepare("SELECT * FROM quiz_attempts WHERE quiz_id=? AND student_id=? ORDER BY id DESC LIMIT 1");
                                $la->execute([$quiz_id, $_SESSION['user_id']]); $last_attempt = $la->fetch();
                                // So lan da lam
                                $cnt_a = $db->prepare("SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id=? AND student_id=? AND submitted_at IS NOT NULL");
                                $cnt_a->execute([$quiz_id, $_SESSION['user_id']]); $attempt_count = (int)$cnt_a->fetchColumn();
                                $can_retry = ($quiz_info['max_attempts'] == 0 || $attempt_count < $quiz_info['max_attempts']);
                            ?>
                            <div class="mb-4 p-4 rounded" style="background:#1a1a2e;border:2px solid #f0b429;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div style="font-size:2.5rem;">🏆</div>
                                    <div>
                                        <h5 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($quiz_info['title']); ?></h5>
                                        <div class="d-flex gap-3 text-muted small">
                                            <?php if($quiz_info['time_limit_minutes'] > 0): ?>
                                                <span><i class="bi bi-clock me-1"></i><?php echo $quiz_info['time_limit_minutes']; ?> phút</span>
                                            <?php endif; ?>
                                            <span><i class="bi bi-bar-chart me-1"></i>Điểm qua: <?php echo $quiz_info['pass_score']; ?>%</span>
                                            <?php if($quiz_info['max_attempts'] > 0): ?>
                                                <span><i class="bi bi-arrow-repeat me-1"></i><?php echo $attempt_count; ?>/<?php echo $quiz_info['max_attempts']; ?> lượt</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($quiz_info['description']): ?>
                                            <p class="text-white-50 small mt-1 mb-0"><?php echo htmlspecialchars($quiz_info['description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if($last_attempt && $last_attempt['submitted_at']): ?>
                                    <!-- Da lam xong it nhat 1 lan -->
                                    <div class="p-3 rounded mb-3" style="background:<?php echo $last_attempt['passed'] ? '#0d2e0d' : '#2e0d0d'; ?>">
                                        <span class="fw-bold" style="color:<?php echo $last_attempt['passed'] ? '#4caf50' : '#f44336'; ?>">
                                            <?php echo $last_attempt['passed'] ? '✅ Đạt' : '❌ Chưa đạt'; ?> — Điểm gần nhất: <strong><?php echo $last_attempt['score']; ?>%</strong>
                                        </span>
                                        <a href="<?php echo APP_URL; ?>/quiz/result?attempt_id=<?php echo $last_attempt['id']; ?>" class="btn btn-sm btn-outline-light ms-3">Xem kết quả chi tiết</a>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex gap-2 flex-wrap">
                                    <?php if($last_attempt && !$last_attempt['submitted_at']): ?>
                                        <a href="<?php echo APP_URL; ?>/quiz/take?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-warning fw-bold">
                                            <i class="bi bi-play-fill me-1"></i>Tiếp tục làm bài
                                        </a>
                                    <?php elseif($can_retry): ?>
                                        <a href="<?php echo APP_URL; ?>/quiz/take?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-warning fw-bold">
                                            <i class="bi bi-play-fill me-1"></i><?php echo $attempt_count > 0 ? 'Làm lại bài' : 'Bắt đầu làm bài'; ?>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>Đã hết lượt làm bài</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php elseif($item['type'] == 'assignment_essay' || $item['type'] == 'assignment_file'): ?>
                            <!-- ASSIGNMENT -->
                            <?php
                            $asgn_id = (int)$item['content'];
                            $db = Database::getInstance()->getConnection();
                            $as = $db->prepare("SELECT * FROM assignments WHERE id=?"); $as->execute([$asgn_id]); $asgn_info = $as->fetch();
                            if ($asgn_info):
                                $sub = $db->prepare("SELECT * FROM assignment_submissions WHERE assignment_id=? AND student_id=?");
                                $sub->execute([$asgn_id, $_SESSION['user_id']]); $my_sub = $sub->fetch();
                            ?>
                            <div class="mb-4 p-4 rounded" style="background:#1a1a2e;border:2px solid <?php echo $asgn_info['type']==='essay' ? '#28a745' : '#17a2b8'; ?>;">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div style="font-size:2.5rem;"><?php echo $asgn_info['type']==='essay' ? '📝' : '📁'; ?></div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if($asgn_info['type']==='essay'): ?>
                                                <span class="badge bg-success">Tự luận</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Nộp file</span>
                                            <?php endif; ?>
                                            <h5 class="text-white fw-bold mb-0"><?php echo htmlspecialchars($asgn_info['title']); ?></h5>
                                        </div>
                                        <div class="d-flex gap-3 text-muted small mt-1">
                                            <span><i class="bi bi-award me-1"></i>Điểm tối đa: <?php echo $asgn_info['max_score']; ?></span>
                                            <?php if($asgn_info['due_date']): ?>
                                                <span><i class="bi bi-calendar me-1"></i>Hạn nộp: <?php echo date('d/m/Y H:i', strtotime($asgn_info['due_date'])); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if($asgn_info['description']): ?>
                                     <div class="p-3 rounded mb-3 text-white-50 small text-start" style="background:#111;border-left:3px solid #444;"><?php echo $asgn_info['description']; ?></div>
                                 <?php endif; ?>

                                <?php if($my_sub && $my_sub['status'] === 'graded'): ?>
                                    <!-- Da cham diem -->
                                    <div class="p-3 rounded mb-3" style="background:#0d2e0d;border:1px solid #28a745;">
                                        <div class="text-success fw-bold">✅ Đã được chấm điểm: <span class="fs-5"><?php echo $my_sub['score']; ?>/<?php echo $asgn_info['max_score']; ?></span></div>
                                        <?php if(!empty($my_sub['file_name'])): ?>
                                            <div class="text-white-50 small mt-2">
                                                <i class="bi bi-file-earmark-check me-1 text-info"></i>File bài làm: 
                                                <?php if($my_sub['file_drive_url']): ?>
                                                    <a href="<?php echo htmlspecialchars($my_sub['file_drive_url']); ?>" target="_blank" class="text-info text-decoration-underline fw-semibold"><?php echo htmlspecialchars($my_sub['file_name']); ?></a>
                                                <?php else: ?>
                                                    <span class="text-danger fw-semibold"><?php echo htmlspecialchars($my_sub['file_name']); ?> (Lỗi tải lên Google Drive)</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($my_sub['feedback']): ?>
                                            <div class="text-white-50 small mt-2"><i class="bi bi-chat-quote me-1"></i>Nhận xét: <?php echo htmlspecialchars($my_sub['feedback']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif($my_sub && $my_sub['file_drive_id'] === 'error'): ?>
                                    <!-- Loi upload Google Drive -->
                                    <div class="alert alert-danger py-3 mb-3">
                                        <div class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Lỗi tải file lên Google Drive</div>
                                        <div class="small mt-1 text-dark" style="white-space: pre-wrap;"><?php echo htmlspecialchars($my_sub['content']); ?></div>
                                        <div class="mt-2 text-danger fw-semibold">Lưu ý: Bài làm của bạn chưa được nộp thành công lên Drive. Hệ thống đã báo lỗi đến giáo viên để khắc phục cấu hình. Vui lòng thử chọn file và nộp lại ở phía dưới.</div>
                                    </div>
                                <?php elseif($my_sub): ?>
                                    <div class="alert alert-warning py-2"><i class="bi bi-hourglass me-2"></i>Đã nộp bài — Đang chờ giáo viên chấm điểm.</div>
                                <?php endif; ?>

                                <?php if(!$my_sub || $my_sub['status'] === 'pending'): ?>
                                    <?php if($asgn_info['type'] === 'essay'): ?>
                                        <form method="POST" action="<?php echo APP_URL; ?>/assignment/submitEssay">
                                            <input type="hidden" name="assignment_id" value="<?php echo $asgn_id; ?>">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <input type="hidden" name="lesson_id" value="<?php echo $current_lesson['id']; ?>">
                                            <textarea name="content" class="form-control mb-3" rows="8" style="background:#111;color:#eee;border-color:#444;" placeholder="Nhập bài làm của bạn tại đây..." required><?php echo htmlspecialchars($my_sub['content'] ?? ''); ?></textarea>
                                            <button type="submit" class="btn btn-success"><i class="bi bi-send me-1"></i><?php echo $my_sub ? 'Cập nhật bài làm' : 'Nộp bài'; ?></button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="<?php echo APP_URL; ?>/assignment/submitFile" enctype="multipart/form-data">
                                            <input type="hidden" name="assignment_id" value="<?php echo $asgn_id; ?>">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <input type="hidden" name="lesson_id" value="<?php echo $current_lesson['id']; ?>">
                                            <input type="hidden" name="drive_folder_id" value="<?php echo htmlspecialchars($asgn_info['drive_folder_id'] ?? ''); ?>">
                                            <?php if($my_sub && $my_sub['file_name']): ?>
                                                <div class="text-white-50 small mb-2">
                                                    <i class="bi bi-file-check me-1 text-info"></i>File đã chọn nộp trước: 
                                                    <?php if($my_sub['file_drive_url']): ?>
                                                        <a href="<?php echo htmlspecialchars($my_sub['file_drive_url']); ?>" target="_blank" class="text-info text-decoration-underline fw-semibold"><?php echo htmlspecialchars($my_sub['file_name']); ?></a>
                                                    <?php else: ?>
                                                        <span class="text-danger fw-semibold"><?php echo htmlspecialchars($my_sub['file_name']); ?> (Lỗi tải lên Drive)</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="input-group mb-3">
                                                <input type="file" name="submission_file" class="form-control" style="background:#111;color:#eee;border-color:#444;" required>
                                                <button type="submit" class="btn btn-info text-white"><i class="bi bi-cloud-upload me-1"></i><?php echo ($my_sub && $my_sub['file_drive_id'] !== 'error') ? 'Nộp lại' : 'Nộp file'; ?></button>
                                            </div>
                                            <small class="text-muted">Tối đa 50MB. File sẽ được lưu trên Google Drive.</small>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

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
