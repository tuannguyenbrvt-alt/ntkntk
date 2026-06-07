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

## 3. Cấu trúc Database cần chú ý
- **Tên ĐÚNG:** `course_parts`, `course_chapters`, `course_lessons`, `course_progress` (KHÔNG DÙNG: `parts`, `chapters`, `lessons`).
- **Lesson Items:** Bảng `lesson_items` map Bài học với Nội dung chi tiết (Text, Quiz, Assignment). Trường `sort_order` lưu thứ tự.
- **Quizzes & Assignments:** Map trực tiếp qua lesson_id/lesson_items.

## 4. Ghi chú Bảo mật & Quy trình Deploy
- **Lưu ý cấu hình & Phân biệt Client ID:**
  - Hệ thống sử dụng 2 Client ID Google khác nhau:
    1. Client ID Đăng nhập Google (Được định nghĩa tại `config/secrets.php` và `config/config.php` - bắt đầu bằng `160692658866-...` trên sản xuất).
    2. Client ID Google Drive gửi file (Được định nghĩa tại `config/google-oauth.json` - bắt đầu bằng `533283503649-...`).
  - Đường dẫn chuyển hướng (Authorized redirect URIs) của cả 2 Client ID trên Google Cloud Console đều phải được đăng ký chung là: `https://ntkntk.com/auth/google/callback`.
  - File `config/google-oauth.json` chứa Refresh Token Google Drive.
- **Quy trình cập nhật code:**
  1. Viết code & Test tại Local.
  2. Git add, commit và push lên GitHub nhánh `main`.
  3. Webhook sẽ tự động pull code lên server thật (`ntkntk.com`) và cập nhật ngay lập tức.

## 5. Định hướng cho phiên làm việc tiếp theo
- **Xây dựng chức năng nhắn tin trực tuyến (Chat System):**
  - **Nhắn tin Học viên - Giáo viên:** Cho phép học viên gửi tin nhắn trực tiếp để trao đổi với Giáo viên giảng dạy.
  - **Nhắn tin Khách - Quản trị viên:** Cho phép khách vãng lai nhắn tin với Quản trị viên hỗ trợ, chỉ yêu cầu nhập Họ tên và Số điện thoại trước khi bắt đầu hội thoại.
  - **Nhắc nhở tin nhắn mới:** Tích hợp chuông thông báo / huy hiệu cảnh báo trên trang Dashboard/Navbar của Quản trị viên và Giáo viên khi đăng nhập để nhắc nhở trả lời tin nhắn chưa đọc.
  - **Đính kèm hình ảnh và tệp tin:** Hỗ trợ học viên gửi ảnh và tệp đính kèm trong hội thoại. File gửi lên sẽ được tự động tải lên thư mục Google Drive được chỉ định trước, đồng thời lưu trữ bản sao dự phòng trên máy chủ local.

---
*(Lần cập nhật cuối: 07/06/2026)*
