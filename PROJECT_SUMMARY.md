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
  - **Tích hợp Google Drive:** Đã chuyển đổi từ Service Account sang **OAuth2 Refresh Token** để tự động upload bài tập học sinh lên Drive cá nhân. Token được lưu tại `config/google-oauth.json`.
* **Quản lý Chấm bài (Grading Dashboard):** 
  - Giáo viên có Dashboard tổng hợp các bài chờ chấm (`/admin/assignments/pending`).
  - Phân quyền rõ ràng: Super Admin chấm tất cả; Giáo viên chỉ chấm các khóa do mình tạo.
  - Hiển thị người chấm bài (`graded_by`) trên giao diện bài làm học sinh.
* **Đăng ký Học viên Toàn diện (MỚI NÂNG CẤP):**
  - Biểu mẫu đăng ký mới hỗ trợ xác minh trùng khớp mật khẩu (`confirm_password`).
  - Yêu cầu điền đầy đủ các thông tin quan trọng khác như: **Số điện thoại** (bắt buộc), **Ngày sinh**, **Địa chỉ**, **Nghề nghiệp / Lớp học** (lưu trực tiếp vào bảng `users`).
* **Đăng nhập Google OAuth2 (MỚI NÂNG CẤP):**
  - Tích hợp đăng nhập/đăng ký một chạm với tài khoản Google (Gmail) sử dụng cURL thuần, nhẹ nhàng và an toàn tối đa.
  - Tự động tạo hồ sơ học viên mới trong DB nếu chưa tồn tại.
* **Báo cáo Học tập Chi tiết (MỚI NÂNG CẤP):**
  - Thêm bảng tổng hợp trực quan dạng Thẻ (Cards) về tiến độ: Tổng số bài đã nộp, tổng điểm đạt được / tối đa của từng loại hình (Trắc nghiệm, Bài tập Tự luận, Bài tập Nộp file).
* **Tra cứu Kết quả Bảo mật hai lớp (MỚI NÂNG CẤP):**
  - Chức năng tra cứu ngoài không cần đăng nhập (`/progress/lookup`) yêu cầu nhập đồng thời cả **Tên đăng nhập (Username)** và **Số điện thoại** (Phone) để bảo vệ quyền riêng tư học viên.
* **Hệ thống Soạn thảo & Tương tác nâng cao:**
  - Sửa và chèn hình ảnh, âm thanh, video qua TinyMCE trong Trắc nghiệm & Bài tập.
  - Thay đổi thứ tự nội dung bài học qua nút ⬆️ và ⬇️.
  - Hỗ trợ xem PDF trực tiếp trên di động qua Google Docs Viewer API.

## 3. Cấu trúc Database cần chú ý
- **Tên ĐÚNG:** `course_parts`, `course_chapters`, `course_lessons`, `course_progress` (KHÔNG DÙNG: `parts`, `chapters`, `lessons`).
- **Lesson Items:** Bảng `lesson_items` map Bài học với Nội dung chi tiết (Text, Quiz, Assignment). Trường `sort_order` lưu thứ tự.
- **Quizzes & Assignments:** Map trực tiếp qua lesson_id/lesson_items.

## 4. Ghi chú Bảo mật & Quy trình Deploy
- **Lưu ý cấu hình:**
  - File `config/config.php` chứa thông tin kết nối Database và thông số Google Login OAuth2 (`GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`).
  - File `config/google-oauth.json` chứa Access Token Google Drive.
- **Quy trình cập nhật code:**
  1. Viết code & Test tại Local.
  2. Git add, commit và push lên GitHub nhánh `main`.
  3. Webhook sẽ tự động pull code lên server thật (`ntkntk.com`) và cập nhật ngay lập tức.

## 5. Định hướng cho phiên làm việc tiếp theo
- **Tối ưu hóa & Theo dõi vận hành:**
  - Lắng nghe phản hồi từ học viên và giáo viên về bộ thống kê học tập mới để nâng cấp biểu đồ trực quan (ví dụ: dùng Chart.js).
  - Tích hợp thông báo email tự động (như gửi email báo kết quả chấm bài của giáo viên).
  - Nghiên cứu thêm chức năng thi thử trắc nghiệm tính giờ và tự động khóa đề khi hết giờ.

---
*(Lần cập nhật cuối: 27/05/2026)*
