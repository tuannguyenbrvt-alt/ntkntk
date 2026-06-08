# PROJECT SUMMARY - LMS NTKNTK (Trung tâm Nguyễn Minh)

*Tài liệu này lưu trữ toàn bộ trạng thái hiện tại của dự án để các phiên làm việc sau có thể tiếp tục ngay lập tức.*

## 1. Tổng quan dự án
- **Mục tiêu:** Hệ thống quản lý học tập (LMS) cho Trung tâm Ngoại ngữ & Tin học Nguyễn Minh.
- **Tech Stack:** PHP thuần (Custom MVC Framework), MySQL, Bootstrap 5.
- **Môi trường:** 
  - Code tại local: `c:\Users\N_M_T_\Documents\ntkntk`
  - Version Control: GitHub (`tuannguyenbrvt-alt/ntkntk`, nhánh `main`)
  - Production: Chạy trên Hosting thật, tự động đồng bộ mã nguồn qua **GitHub Webhook** mỗi khi có code mới push lên nhánh `main`.

## 2. Các tính năng cốt lõi đã hoàn thiện
* **Cấu trúc Khóa học:** Khóa học -> Phần (Parts) -> Chương (Chapters) -> Bài học (Lessons) -> Nội dung (Items: Text, Quiz, Assignment...).
* **Hệ thống Nộp bài tập (Assignments):**
  - Hỗ trợ nộp bài **Tự luận** và **Upload File**.
  - **Tích hợp Google Drive & Báo lỗi thông minh:** File bài làm chỉ lưu trực tiếp trên Google Drive để tránh tốn dung lượng máy chủ.
  - **Cơ chế báo lỗi:** Nếu xảy ra sự cố upload lên Drive (do hết hạn token, lỗi 403, 404, lỗi kết nối API...), hệ thống tự động ghi lại lịch sử lỗi chi tiết kỹ thuật vào cơ sở dữ liệu (`file_drive_id = 'error'`) và chuyển thông tin lỗi cho Giáo viên. Học viên chỉ nhận được thông báo rút gọn, thân thiện và tiếp tục hiển thị form để nộp lại sau khi giáo viên sửa cấu hình.
  - **Liên kết tải file học sinh:** Tích hợp liên kết thẻ `<a>` cho học viên click để tự tải/xem lại file bài làm của mình trên Google Drive sau khi nộp thành công.
* **Làm mới quyền Google Drive cho Admin (MỚI NÂNG CẤP):**
  - Tích hợp trang làm mới tự động quyền truy cập Drive tại `/admin/setup-drive-oauth` hoặc qua nút **"Cấu hình & Cấp lại quyền Google Drive"** trên dashboard chấm bài của giáo viên.
  - Tự động đọc và sử dụng đúng cặp Client ID/Secret chuyên dụng của Drive (`533283503649-...`) để yêu cầu cấp quyền offline, giải quyết lỗi hết hạn token (`invalid_grant`), sau đó cập nhật đè token mới vào `config/google-oauth.json`.
* **Thông tin Thanh toán & Huy hiệu Duyệt đăng ký (MỚI NÂNG CẤP):**
  - **Trang thanh toán QR:** Bổ sung hướng dẫn học viên chụp màn hình chuyển khoản gửi qua Zalo `0979875712` để hỗ trợ kích hoạt khóa học nhanh hơn.
  - **Huy hiệu duyệt đăng ký:** Sidebar Admin tại mục **"Duyệt đăng ký"** tự động đếm và hiển thị số lượng yêu cầu duyệt kích hoạt khóa học đang chờ duyệt (`status = 'pending'`) bằng huy hiệu màu đỏ nổi bật.
* **Quản lý Chấm bài (Grading Dashboard):** 
  - Giáo viên có Dashboard tổng hợp các bài chờ chấm (`/admin/assignments/pending`).
  - Phân quyền rõ ràng: Super Admin chấm tất cả; Giáo viên chỉ chấm các khóa do mình tạo.
  - Hiển thị người chấm bài (`graded_by`) trên giao diện bài làm học sinh.
* **Đăng ký Học viên Toàn diện:**
  - Biểu mẫu đăng ký mới hỗ trợ xác minh trùng khớp mật khẩu (`confirm_password`).
  - Yêu cầu điền đầy đủ các thông tin quan trọng khác như: **Số điện thoại** (bắt buộc), **Ngày sinh**, **Địa chỉ**, **Nghề nghiệp / Lớp học** (lưu trực tiếp vào bảng `users`).
* **Đăng nhập Google OAuth2:**
  - Tích hợp đăng nhập/đăng ký một chạm với tài khoản Google (Gmail) sử dụng cURL thuần, nhẹ nhàng và an toàn tối đa.
  - Tự động tạo hồ sơ học viên mới trong DB nếu chưa tồn tại.
* **Báo cáo Học tập Chi tiết:**
  - Thêm bảng tổng hợp trực quan dạng Thẻ (Cards) về tiến độ: Tổng số bài đã nộp, tổng điểm đạt được / tối đa của từng loại hình (Trắc nghiệm, Bài tập Tự luận, Bài tập Nộp file).
* **Tra cứu Kết quả Bảo mật hai lớp:**
  - Chức năng tra cứu ngoài không cần đăng nhập (`/progress/lookup`) yêu cầu nhập đồng thời cả **Tên đăng nhập (Username)** và **Số điện thoại** (Phone) để bảo vệ quyền riêng tư học viên.
* **Hệ thống Soạn thảo & Tương tác nâng cao:**
  - Sửa và chèn hình ảnh, âm thanh, video qua TinyMCE trong Trắc nghiệm & Bài tập.
  - Thay đổi thứ tự Phần, Chương, Bài học và Nội dung chi tiết trong bài học qua các nút di chuyển Lên ⬆️ và Xuống ⬇️ trực quan.
  - Hỗ trợ xem PDF trực tiếp trên di động qua Google Docs Viewer API.
* **Cải tiến giao diện Trắc nghiệm (MỚI NÂNG CẤP):**
  - Chuyển phông nền vùng câu hỏi và đáp án trắc nghiệm sang màu trắng, chữ tối màu giúp tăng độ tương phản và đảm bảo văn bản màu đen (được soạn thảo từ editor rich text) hiển thị rõ nét trên giao diện làm bài của học sinh.
* **Quản lý thông tin đề Trắc nghiệm (MỚI NÂNG CẤP):**
  - Hỗ trợ sửa tiêu đề, mô tả và các cấu hình đề trắc nghiệm trực tiếp từ trang Builder đề cương khóa học hoặc trang quản lý câu hỏi.
* **Hệ thống Nhắn tin Trực tuyến - Chat System (MỚI NÂNG CẤP):**
  - **Nhắn tin đa đối tượng**: Học viên và khách vãng lai (nhập Họ tên + SĐT) nhắn tin trực tiếp qua Floating Widget ở ngoài trang chủ. Admin và Giáo viên phản hồi và quản lý cuộc trò chuyện qua trang `/admin/chat`.
  - **Huy hiệu thông báo tin nhắn chưa đọc**: Hiển thị số lượng tin nhắn chưa đọc trên Navbar Admin/Giáo viên và trên nút Widget chat học sinh kèm theo chỉ báo cụ thể từng kênh. Tự động polling ngầm thông minh (10 giây khi đóng, 5 giây khi mở danh sách).
  - **Lưu trữ tệp tin đính kèm**: Hỗ trợ đính kèm tệp đa định dạng, lưu trữ đồng thời trên thư mục Google Drive chuyên dụng (`1ZYASrXxviVSU5DOWPuAXIOhlyqSFhQ97`) và sao lưu cục bộ tại `uploads/chats/`.
  - **Thu hồi tin nhắn**: Cho phép người gửi (học sinh/khách/admin) thu hồi tin nhắn trong vòng 24 giờ, xóa sạch file cục bộ và trên Google Drive.
  - **Chủ động tạo hội thoại**: Admin/Giáo viên tìm kiếm học viên theo tên/SĐT/Email để chủ động mở thread trò chuyện hỗ trợ hoặc hỏi bài.
  - **Phân biệt giao diện rõ rệt**: Giao diện chat Admin hiển thị tin nhắn gửi đi căn phải (màu xanh dương), tin nhắn nhận từ học viên căn trái (màu trắng) giúp quản lý dễ dàng.
  - **Thông báo nâng cao khi offline (MỚI NÂNG CẤP)**: Tự động gửi Email thông báo tin nhắn mới và giả lập gửi tin Zalo ZNS khi người nhận (học viên, khách, hoặc giáo viên/admin) offline > 5 phút. Hỗ trợ cơ chế giãn cách cooldown 1 giờ để chống spam hòm thư.
  - **Thống kê hiệu suất phản hồi (MỚI NÂNG CẤP)**: Bổ sung trang thống kê hiệu suất `/admin/chat/performance` phân tích thời gian phản hồi trung bình, nhanh nhất, chậm nhất của từng giáo viên/admin kèm lịch sử gắn badge màu sắc trực quan.

## 3. Cấu trúc Database cần chú ý
- **Tên ĐÚNG:** `course_parts`, `course_chapters`, `course_lessons`, `course_progress` (KHÔNG DÙNG: `parts`, `chapters`, `lessons`).
- **Lesson Items:** Bảng `lesson_items` map Bài học với Nội dung chi tiết (Text, Quiz, Assignment). Trường `sort_order` lưu thứ tự.
- **Quizzes & Assignments:** Map trực tiếp qua lesson_id/lesson_items.
- **Các trường Chat mở rộng:** `users.last_active_at`, `chat_threads.guest_last_active_at`, `chat_threads.last_notified_at`.

## 4. Ghi chú Bảo mật & Quy trình Deploy
- **Lưu ý cấu hình & Phân biệt Client ID:**
  - Hệ thống sử dụng 2 Client ID Google khác nhau:
    1. Client ID Đăng nhập Google (Được định nghĩa tại `config/secrets.php` và `config/config.php` - bắt đầu bằng `160692658866-...` trên sản xuất).
    2. Client ID Google Drive gửi file (Được định nghĩa tại `config/google-oauth.json` - bắt đầu bằng `533283503649-...`).
  - Đường dẫn chuyển hướng (Authorized redirect URIs) của cả 2 Client ID trên Google Cloud Console đều phải được đăng ký chung là: `https://ntkntk.com/auth/google/callback`.
  - File `config/google-oauth.json` chứa Refresh Token Google Drive.
- **Quy trình cập nhật code:**
  - Viết code & Test tại Local.
  - Git add, commit và push lên GitHub nhánh `main`.
  - Webhook sẽ tự động pull code lên server thật (`ntkntk.com`) và cập nhật ngay lập tức.

## 5. Định hướng cho phiên làm việc tiếp theo
- **Tối ưu kết nối**:
  - Xem xét chuyển đổi cơ chế polling sang WebSockets (hoặc Server-Sent Events) nếu quy mô lượt truy cập đồng thời tăng cao.

---
*(Lần cập nhật cuối: 08/06/2026)*
