<?php
// views/admin/chat/index.php
?>
<div class="card shadow-sm border-0" style="height: calc(100vh - 120px); min-height: 500px;">
    <div class="row g-0 h-100">
        
        <!-- Cột trái: Danh sách cuộc trò chuyện -->
        <div class="col-md-4 border-end d-flex flex-column h-100 bg-white">
            <div class="p-3 border-bottom bg-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Hội thoại</h5>
                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newChatModal">
                        <i class="bi bi-plus-lg me-1"></i>Tạo chat
                    </button>
                </div>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="search-threads" class="form-control border-start-0 ps-0 shadow-none" placeholder="Tìm tên, số điện thoại...">
                </div>
            </div>
            
            <div class="flex-grow-1 overflow-y-auto" id="thread-list" style="max-height: calc(100vh - 240px);">
                <?php if (empty($threads)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-square-text fs-1 mb-2 d-block"></i>
                        <span>Chưa có cuộc trò chuyện nào.</span>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($threads as $thread): ?>
                            <?php 
                            $isGuest = ($thread['type'] === 'guest_admin');
                            $name = $isGuest ? $thread['guest_name'] : $thread['student_name'];
                            $phone = $isGuest ? $thread['guest_phone'] : $thread['student_phone'];
                            $avatarLetter = mb_substr($name ?? 'C', 0, 1, 'UTF-8');
                            $courseInfo = $thread['course_title'] ? 'Khóa: ' . htmlspecialchars($thread['course_title']) : 'Hỗ trợ chung';
                            ?>
                            <a href="#" class="list-group-item list-group-item-action p-3 thread-item border-bottom position-relative" 
                               data-id="<?php echo $thread['id']; ?>"
                               data-name="<?php echo htmlspecialchars($name ?? 'Học viên'); ?>"
                               data-phone="<?php echo htmlspecialchars($phone ?? ''); ?>"
                               data-info="<?php echo htmlspecialchars($courseInfo); ?>"
                               data-type="<?php echo $thread['type']; ?>">
                               
                               <div class="d-flex align-items-center">
                                   <!-- Avatar -->
                                   <div class="flex-shrink-0">
                                       <?php if (!$isGuest && !empty($thread['student_avatar'])): ?>
                                           <img src="<?php echo APP_URL . '/' . $thread['student_avatar']; ?>" class="rounded-circle object-fit-cover" style="width: 45px; height: 45px;" alt="">
                                       <?php else: ?>
                                           <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" 
                                                style="width: 45px; height: 45px; background: <?php echo $isGuest ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #3b82f6, #1d4ed8)'; ?>; font-size: 1.1rem;">
                                                <?php echo htmlspecialchars($avatarLetter); ?>
                                           </div>
                                       <?php endif; ?>
                                   </div>
                                   
                                   <!-- Chi tiết -->
                                   <div class="flex-grow-1 ms-3 overflow-hidden">
                                       <div class="d-flex align-items-center justify-content-between mb-1">
                                           <h6 class="mb-0 text-truncate fw-bold thread-name"><?php echo htmlspecialchars($name ?? 'Ẩn danh'); ?></h6>
                                           <small class="text-muted text-nowrap thread-time" style="font-size: 0.75rem;">
                                               <?php echo date('H:i', strtotime($thread['updated_at'])); ?>
                                           </small>
                                       </div>
                                       
                                       <div class="d-flex align-items-center justify-content-between">
                                           <div class="text-muted text-truncate w-75 small thread-preview-text">
                                               <?php 
                                               if ($thread['last_message']) {
                                                   echo htmlspecialchars($thread['last_message']);
                                               } elseif ($thread['last_file']) {
                                                   echo '📎 ' . htmlspecialchars($thread['last_file']);
                                               } else {
                                                   echo 'Bắt đầu cuộc trò chuyện...';
                                               }
                                               ?>
                                           </div>
                                           
                                           <!-- Badge chưa đọc -->
                                           <span class="badge bg-danger rounded-pill unread-badge <?php echo $thread['unread_count'] > 0 ? '' : 'd-none'; ?>">
                                               <?php echo $thread['unread_count']; ?>
                                           </span>
                                       </div>
                                       
                                       <div class="mt-1 d-flex gap-1 align-items-center" style="font-size: 0.75rem;">
                                           <span class="badge <?php echo $isGuest ? 'bg-warning text-dark' : 'bg-info text-dark'; ?> px-1.5 py-0.5">
                                               <?php echo $isGuest ? 'Khách' : 'Học viên'; ?>
                                           </span>
                                           <span class="text-muted text-truncate ms-1"><?php echo htmlspecialchars($courseInfo); ?></span>
                                       </div>
                                   </div>
                               </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Cột phải: Phòng chat -->
        <div class="col-md-8 d-flex flex-column h-100 bg-light" id="chat-window-container">
            <!-- Trạng thái trống (Chưa chọn thread) -->
            <div id="chat-empty-state" class="d-flex flex-column align-items-center justify-content-center flex-grow-1 text-muted p-5">
                <div class="bg-white rounded-circle p-4 shadow-sm mb-3">
                    <i class="bi bi-chat-text text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Chọn một cuộc trò chuyện</h5>
                <p class="text-muted text-center" style="max-width: 350px;">Hãy chọn một người cần hỗ trợ ở thanh bên trái để bắt đầu cuộc trò chuyện trực tuyến.</p>
            </div>
            
            <!-- Trạng thái phòng chat hoạt động (Sẽ hiển thị qua JS) -->
            <div id="chat-active-state" class="d-flex flex-column h-100 d-none">
                <!-- Chat Header -->
                <div class="p-3 bg-white border-bottom shadow-sm d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div id="active-chat-avatar" class="rounded-circle d-flex align-items-center justify-content-center text-white me-3" 
                             style="width: 45px; height: 45px; background: #3b82f6; font-size: 1.1rem; font-weight: bold;">
                             C
                        </div>
                        <div>
                            <h6 id="active-chat-name" class="mb-0 fw-bold">Họ và tên</h6>
                            <small id="active-chat-sub" class="text-muted d-flex gap-2 align-items-center">
                                <span id="active-chat-phone"><i class="bi bi-telephone me-1"></i>0123456789</span>
                                <span>•</span>
                                <span id="active-chat-info"><i class="bi bi-book me-1"></i>Lớp học</span>
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Messages Area -->
                <div class="flex-grow-1 p-4 overflow-y-auto" id="chat-messages-container" style="background-color: #f0f2f5; max-height: calc(100vh - 280px);">
                    <!-- Các tin nhắn sẽ được render tự động qua AJAX -->
                </div>
                
                <!-- Chat Input Form -->
                <div class="p-3 bg-white border-top">
                    <!-- Preview đính kèm file -->
                    <div id="attachment-preview" class="p-2 bg-light border rounded mb-2 d-none d-flex align-items-center justify-content-between">
                        <span id="attachment-name" class="text-truncate small text-muted"><i class="bi bi-paperclip me-1"></i>File_name.pdf</span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" id="btn-cancel-attachment"><i class="bi bi-x-circle-fill"></i></button>
                    </div>
                    
                    <form id="chat-send-form" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="thread_id" id="chat-thread-id-input">
                        
                        <!-- Nút đính kèm -->
                        <button type="button" class="btn btn-outline-secondary rounded-circle px-3 py-2 flex-shrink-0" id="btn-trigger-upload" title="Đính kèm ảnh/tệp tin">
                            <i class="bi bi-paperclip"></i>
                        </button>
                        <input type="file" id="chat-file-input" name="attachment" class="d-none">
                        
                        <!-- Ô nhập tin nhắn -->
                        <input type="text" name="message_text" id="chat-message-text-input" class="form-control rounded-pill px-4 shadow-none border-secondary-subtle" placeholder="Nhập tin nhắn..." autocomplete="off">
                        
                        <!-- Nút gửi -->
                        <button type="submit" class="btn btn-primary rounded-circle px-3 py-2 flex-shrink-0" id="btn-send-chat" title="Gửi">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
            
    <!-- Modal Tạo cuộc trò chuyện mới -->
    <div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white border-bottom border-warning border-2">
                    <h6 class="modal-title fw-bold" id="newChatModalLabel"><i class="bi bi-person-plus-fill text-warning me-2"></i>Tạo cuộc trò chuyện mới</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="search-student-input" class="form-control shadow-none" placeholder="Tìm học viên theo Tên, Số điện thoại...">
                    </div>
                    
                    <div id="student-search-results" class="list-group overflow-y-auto" style="max-height: 250px;">
                        <div class="text-center text-muted py-4 small">Nhập ít nhất 2 ký tự để tìm kiếm học viên.</div>
                    </div>
                    
                    <!-- Vùng chọn khóa học -->
                    <div id="course-select-area" class="mt-3 d-none border-top pt-3">
                        <input type="hidden" id="selected-student-id">
                        <label for="student-course-select" class="form-label small fw-bold">Chọn Khóa học / Lớp học (Tùy chọn):</label>
                        <select id="student-course-select" class="form-select form-select-sm shadow-none">
                            <!-- Render via JS -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light p-2.5">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" id="btn-confirm-start-chat" disabled>Bắt đầu nhắn tin</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling custom cho chat admin */
    .thread-item {
        transition: background-color 0.2s;
        border-left: 4px solid transparent;
        cursor: pointer;
    }
    .thread-item:hover {
        background-color: #f8f9fa;
    }
    .thread-item.active {
        background-color: #e6f0fa;
        border-left-color: #0d6efd;
    }
    .chat-bubble {
        max-width: 70%;
        border-radius: 18px;
        padding: 10px 16px;
        margin-bottom: 4px;
        position: relative;
        font-size: 0.95rem;
        word-wrap: break-word;
    }
    .chat-bubble.admin-reply {
        background-color: #0d6efd;
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }
    .chat-bubble.client-msg {
        background-color: #ffffff;
        color: #1a1a1a;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    }
    .chat-time {
        font-size: 0.7rem;
        margin-bottom: 12px;
        display: block;
    }
    .attachment-card {
        border-radius: 10px;
        background: rgba(0, 0, 0, 0.05);
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: inherit;
        margin-bottom: 6px;
        transition: 0.2s;
    }
    .chat-bubble.admin-reply .attachment-card {
        background: rgba(255, 255, 255, 0.15);
        color: #fff !important;
    }
    .chat-bubble.admin-reply .attachment-card:hover {
        background: rgba(255, 255, 255, 0.25);
    }
    .attachment-card:hover {
        background: rgba(0, 0, 0, 0.1);
    }
    .attachment-icon {
        font-size: 1.5rem;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let currentThreadId = null;
    let pollInterval = null;
    const chatMessagesContainer = document.getElementById('chat-messages-container');
    const emptyState = document.getElementById('chat-empty-state');
    const activeState = document.getElementById('chat-active-state');
    const threadListItems = document.querySelectorAll('.thread-item');

    // Tìm kiếm thread
    const searchInput = document.getElementById('search-threads');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            document.querySelectorAll('.thread-item').forEach(item => {
                const name = item.querySelector('.thread-name').textContent.toLowerCase();
                const phone = item.getAttribute('data-phone').toLowerCase();
                if (name.includes(query) || phone.includes(query)) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        });
    }

    // Chọn thread chat
    threadListItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Cập nhật trạng thái kích hoạt trên giao diện
            threadListItems.forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            currentThreadId = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const phone = this.getAttribute('data-phone');
            const info = this.getAttribute('data-info');
            const type = this.getAttribute('data-type');

            // Cập nhật thông tin Header phòng chat
            document.getElementById('chat-thread-id-input').value = currentThreadId;
            document.getElementById('active-chat-name').textContent = name;
            document.getElementById('active-chat-phone').innerHTML = `<i class="bi bi-telephone me-1"></i>${phone || 'Không có sđt'}`;
            document.getElementById('active-chat-info').innerHTML = `<i class="bi bi-book me-1"></i>${info}`;
            
            const avatarDiv = document.getElementById('active-chat-avatar');
            avatarDiv.textContent = name.substring(0, 1).toUpperCase();
            if (type === 'guest_admin') {
                avatarDiv.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
            } else {
                avatarDiv.style.background = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
            }

            // Hiển thị khung chat chính
            emptyState.classList.add('d-none');
            activeState.classList.remove('d-none');

            document.getElementById('chat-empty-state').classList.add('d-none');
            document.getElementById('chat-active-state').classList.remove('d-none');

            const badge = this.querySelector('.unread-badge');
            if (badge) badge.classList.add('d-none');

            loadMessages(currentThreadId, true);

            if (pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(function() {
                loadMessages(currentThreadId, false);
            }, 3000);
        });
    });

    // Tự động kích hoạt thread chỉ định từ URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const selectThreadId = urlParams.get('select_thread_id');
    if (selectThreadId) {
        const targetItem = document.querySelector(`.thread-item[data-id="${selectThreadId}"]`);
        if (targetItem) {
            setTimeout(() => targetItem.click(), 250);
        }
    }

    // Hàm tải tin nhắn
    function loadMessages(threadId, shouldScroll = false) {
        if (!threadId) return;
        
        const isAtBottom = (chatMessagesContainer.scrollTop + chatMessagesContainer.clientHeight >= chatMessagesContainer.scrollHeight - 50);

        fetch(`<?php echo APP_URL; ?>/admin/chat/messages?thread_id=${threadId}`)
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    let html = '';
                    data.messages.forEach(msg => {
                        const isAdminReply = msg.sender_id !== null && msg.sender_id !== '';
                        const bubbleClass = isAdminReply ? 'admin-reply align-self-end' : 'client-msg align-self-start';
                        const containerClass = isAdminReply ? 'justify-content-end' : 'justify-content-start';
                        
                        let bubbleHtml = '';
                        if (msg.is_recalled == 1) {
                            bubbleHtml = `
                                <div class="chat-bubble ${bubbleClass} text-muted fst-italic shadow-none" style="background: rgba(220, 225, 230, 0.4); border: 1px dashed #ccc; color: #7f8c8d !important;">
                                    <i class="bi bi-trash3-fill me-1.5"></i>Tin nhắn đã bị thu hồi
                                </div>
                            `;
                        } else {
                            let fileHtml = '';
                            if (msg.file_name) {
                                const link = msg.file_drive_url ? msg.file_drive_url : `<?php echo APP_URL; ?>/${msg.file_path}`;
                                const icon = msg.file_path && msg.file_path.match(/\.(jpg|jpeg|png|gif|webp)$/i) ? 'bi-image' : 'bi-file-earmark';
                                
                                if (icon === 'bi-image') {
                                    fileHtml = `
                                        <a href="${link}" target="_blank" class="d-block mb-2">
                                            <img src="<?php echo APP_URL; ?>/${msg.file_path}" class="rounded img-fluid" style="max-height: 180px; object-fit: contain;" alt="${msg.file_name}">
                                        </a>
                                    `;
                                } else {
                                    fileHtml = `
                                        <a href="${link}" target="_blank" class="attachment-card">
                                            <i class="bi ${icon} attachment-icon"></i>
                                            <div class="overflow-hidden">
                                                <div class="text-truncate small fw-bold">${msg.file_name}</div>
                                                <span style="font-size: 0.7rem; opacity: 0.8;">Tải về tệp tin</span>
                                            </div>
                                        </a>
                                    `;
                                }
                            }
                            bubbleHtml = `
                                <div class="chat-bubble ${bubbleClass}">
                                    ${fileHtml}
                                    ${msg.message_text ? `<div>${msg.message_text}</div>` : ''}
                                </div>
                            `;
                        }

                        // Parse timestamp
                        const dateObj = new Date(msg.created_at);
                        const timeStr = dateObj.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

                        // Nút thu hồi nếu do chính admin gửi trong vòng 24 giờ
                        let recallBtn = '';
                        if (isAdminReply && msg.is_recalled == 0) {
                            const timeDiffHours = (new Date().getTime() - dateObj.getTime()) / (3600 * 1000);
                            if (timeDiffHours < 24) {
                                recallBtn = `<span class="mx-1">•</span><a href="#" class="text-danger text-decoration-none btn-recall-msg small" data-msg-id="${msg.id}" title="Thu hồi tin nhắn">Thu hồi</a>`;
                            }
                        }

                        html += `
                            <div class="d-flex ${containerClass} mb-1">
                                ${bubbleHtml}
                            </div>
                            <div class="d-flex ${containerClass}">
                                <small class="text-muted chat-time px-2 ${isAdminReply ? 'text-end' : 'text-start'}">${timeStr}${recallBtn}</small>
                            </div>
                        `;
                    });

                    chatMessagesContainer.innerHTML = html;

                    if (shouldScroll || isAtBottom) {
                        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
                    }
                }
            })
            .catch(err => console.error('Lỗi tải tin nhắn:', err));
    }

    // Xử lý thu hồi tin nhắn từ Admin
    chatMessagesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-recall-msg')) {
            e.preventDefault();
            const msgId = e.target.getAttribute('data-msg-id');

            if (!confirm('Bạn có chắc chắn muốn thu hồi tin nhắn này? Toàn bộ nội dung tệp tin đính kèm (nếu có) cũng sẽ bị xóa vĩnh viễn.')) {
                return;
            }

            const fd = new FormData();
            fd.append('message_id', msgId);

            fetch('<?php echo APP_URL; ?>/admin/chat/recall', {
                method: 'POST',
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    loadMessages(currentThreadId, false);
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(() => alert('Có lỗi xảy ra, không thể thu hồi tin nhắn.'));
        }
    });

    // Modal tìm kiếm học viên và tạo thread chủ động
    const searchStudentInput = document.getElementById('search-student-input');
    const studentSearchResults = document.getElementById('student-search-results');
    const courseSelectArea = document.getElementById('course-select-area');
    const studentCourseSelect = document.getElementById('student-course-select');
    const btnConfirmStartChat = document.getElementById('btn-confirm-start-chat');
    const selectedStudentIdInput = document.getElementById('selected-student-id');

    let searchTimeout = null;

    if (searchStudentInput) {
        searchStudentInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                studentSearchResults.innerHTML = '<div class="text-center text-muted py-4 small">Nhập ít nhất 2 ký tự để tìm kiếm học viên.</div>';
                courseSelectArea.classList.add('d-none');
                btnConfirmStartChat.disabled = true;
                return;
            }

            studentSearchResults.innerHTML = '<div class="text-center py-4 small text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Đang tìm kiếm...</div>';

            searchTimeout = setTimeout(() => {
                fetch(`<?php echo APP_URL; ?>/admin/chat/search-students?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            if (data.students.length === 0) {
                                studentSearchResults.innerHTML = '<div class="text-center text-muted py-4 small">Không tìm thấy học viên nào phù hợp.</div>';
                                return;
                            }

                            let html = '';
                            data.students.forEach(st => {
                                html += `
                                    <button type="button" class="list-group-item list-group-item-action d-flex align-items-center py-2.5 btn-select-student" 
                                            data-id="${st.id}" data-name="${st.full_name}" data-courses='${JSON.stringify(st.courses)}'>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2.5 font-weight-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            ${st.full_name.substring(0, 1).toUpperCase()}
                                        </div>
                                        <div class="text-start overflow-hidden">
                                            <div class="fw-bold small text-truncate">${st.full_name}</div>
                                            <span class="text-muted" style="font-size: 0.7rem;">SĐT: ${st.phone || 'N/A'} | Email: ${st.email || 'N/A'}</span>
                                        </div>
                                    </button>
                                `;
                            });
                            studentSearchResults.innerHTML = html;

                            // Click chọn học viên trong list kết quả
                            document.querySelectorAll('.btn-select-student').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    document.querySelectorAll('.btn-select-student').forEach(el => el.classList.remove('active', 'bg-primary', 'text-white'));
                                    this.classList.add('active', 'bg-primary', 'text-white');

                                    const studentId = this.getAttribute('data-id');
                                    selectedStudentIdInput.value = studentId;

                                    // Lấy danh sách khóa học của học sinh đó
                                    const courses = JSON.parse(this.getAttribute('data-courses'));
                                    let courseHtml = '<option value="">-- Trò chuyện hỗ trợ chung --</option>';
                                    courses.forEach(c => {
                                        courseHtml += `<option value="${c.course_id}">Hỏi bài: ${c.course_title}</option>`;
                                    });
                                    studentCourseSelect.innerHTML = courseHtml;

                                    courseSelectArea.classList.remove('d-none');
                                    btnConfirmStartChat.disabled = false;
                                });
                            });
                        }
                    })
                    .catch(err => {
                        studentSearchResults.innerHTML = '<div class="text-center text-danger py-4 small">Lỗi kết nối. Thử lại sau!</div>';
                    });
            }, 300);
        });
    }

    if (btnConfirmStartChat) {
        btnConfirmStartChat.addEventListener('click', function() {
            const studentId = selectedStudentIdInput.value;
            const courseId = studentCourseSelect.value;

            if (!studentId) return;

            this.disabled = true;
            this.textContent = 'Đang tạo...';

            const formData = new FormData();
            formData.append('student_id', studentId);
            if (courseId) {
                formData.append('course_id', courseId);
            }

            fetch('<?php echo APP_URL; ?>/admin/chat/start-thread', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    // Đóng modal
                    const modalEl = document.getElementById('newChatModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    // Chuyển hướng để kích hoạt thread mới
                    window.location.href = `<?php echo APP_URL; ?>/admin/chat?select_thread_id=${data.thread_id}`;
                } else {
                    alert('Lỗi: ' + data.error);
                    this.disabled = false;
                    this.textContent = 'Bắt đầu nhắn tin';
                }
            })
            .catch(() => {
                alert('Có lỗi xảy ra, vui lòng thử lại.');
                this.disabled = false;
                this.textContent = 'Bắt đầu nhắn tin';
            });
        });
    }

    // Đính kèm tệp đính kèm
    const btnUpload = document.getElementById('btn-trigger-upload');
    const fileInput = document.getElementById('chat-file-input');
    const previewContainer = document.getElementById('attachment-preview');
    const previewName = document.getElementById('attachment-name');
    const btnCancelAttachment = document.getElementById('btn-cancel-attachment');

    if (btnUpload && fileInput) {
        btnUpload.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                const file = this.files[0];
                previewName.innerHTML = `<i class="bi bi-paperclip me-1"></i>${file.name} (${formatSize(file.size)})`;
                previewContainer.classList.remove('d-none');
            }
        });
    }

    if (btnCancelAttachment) {
        btnCancelAttachment.addEventListener('click', () => {
            fileInput.value = '';
            previewContainer.classList.add('d-none');
        });
    }

    // Gửi tin nhắn
    const sendForm = document.getElementById('chat-send-form');
    if (sendForm) {
        sendForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageTextInput = document.getElementById('chat-message-text-input');
            const text = messageTextInput.value.trim();
            const hasFile = fileInput.files && fileInput.files.length > 0;

            if (!text && !hasFile) return;

            const formData = new FormData(this);
            const sendButton = document.getElementById('btn-send-chat');
            
            // Disable nút gửi khi đang tải lên
            sendButton.disabled = true;
            sendButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

            fetch('<?php echo APP_URL; ?>/admin/chat/send', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    messageTextInput.value = '';
                    fileInput.value = '';
                    previewContainer.classList.add('d-none');
                    
                    // Nạp lại hội thoại ngay lập tức
                    loadMessages(currentThreadId, true);
                    
                    // Cập nhật xem trước tin nhắn ở Sidebar
                    const activeItem = document.querySelector(`.thread-item[data-id="${currentThreadId}"]`);
                    if (activeItem) {
                        const previewEl = activeItem.querySelector('.thread-preview-text');
                        previewEl.textContent = data.message.message_text ? data.message.message_text : '📎 ' + data.message.file_name;
                        activeItem.querySelector('.thread-time').textContent = 'Vừa xong';
                        
                        // Đẩy thread lên đầu danh sách
                        const listGroup = activeItem.parentNode;
                        listGroup.insertBefore(activeItem, listGroup.firstChild);
                    }
                } else {
                    alert('Lỗi gửi tin nhắn: ' . data.error);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            })
            .finally(() => {
                sendButton.disabled = false;
                sendButton.innerHTML = `<i class="bi bi-send-fill"></i>`;
            });
        });
    }

    // Helper format size
    function formatSize(bytes) {
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return bytes + ' B';
    }
});
</script>
