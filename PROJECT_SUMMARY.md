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
  - **Tích hợp Google Drive (MỚI):** Đã chuyển đổi từ Service Account sang **OAuth2 Refresh Token** để tự động upload bài tập học sinh lên Drive cá nhân (Giải quyết triệt để lỗi giới hạn dung lượng "403 Storage Quota"). Token được lưu tại `config/google-oauth.json`.
* **Quản lý Chấm bài (Grading Dashboard - MỚI):** 
  - Giáo viên có Dashboard tổng hợp các bài chờ chấm (`/admin/assignments/pending`).
  - **Phân quyền:** Super Admin thấy tất cả, Admin (Giáo viên) chỉ thấy bài thuộc khóa học do mình tạo ra (`courses.author_id`).
  - Hiển thị người chấm bài (`graded_by`) trên màn hình kết quả của học sinh.
* **Hồ sơ học tập / Tiến độ học:** 
  - Học viên xem được % hoàn thành khóa học, kết quả thi trắc nghiệm, điểm bài tập. (Đã fix lỗi HTTP 500 do sai tên bảng).

## 3. Cấu trúc Database cần chú ý
Do dự án được refactor nhiều lần, cần đặc biệt lưu ý tên chính xác của các bảng liên kết khóa học:
- **Tên ĐÚNG:** `course_parts`, `course_chapters`, `course_lessons`, `course_progress` (KHÔNG DÙNG: `parts`, `chapters`, `lessons`).
- **Lesson Items:** Bảng `lesson_items` là nơi map Bài học với Nội dung chi tiết (Text, Quiz, Assignment). 
- **Quizzes:** Các câu hỏi được lấy từ `question_bank` map qua bảng `quiz_questions`. 

## 4. Ghi chú Bảo mật & Quy trình Deploy
- **Lưu ý cấu hình:**
  - Tuyệt đối không để lộ file `config/config.php` và `config/google-oauth.json` (chứa Access Token Google Drive cấp quyền ghi file).
- **Quy trình cập nhật code (Workflow):**
  1. Viết code & Test tại Local.
  2. Dùng Git add & commit.
  3. `git push origin main`.
  4. Hệ thống Hosting tự động cập nhật qua Webhook. Mọi thay đổi sẽ live ngay lập tức trên `ntkntk.com`.

## 5. Định hướng cho phiên làm việc tiếp theo
- **Kiểm tra độ ổn định:** Do hệ thống vừa cập nhật các thay đổi liên quan đến cấu trúc bài học và nộp bài, nên test lại luồng (Học sinh mua khóa học -> Làm bài -> Giáo viên chấm bài).
- **Phát triển tính năng mới:** Tuỳ thuộc vào yêu cầu của bạn, có thể bổ sung chức năng Chứng chỉ (Certificate generation), Thống kê doanh thu, hoặc Email thông báo.
- **Nâng cấp Hệ thống Tài khoản:**
  - Thêm trường "Nhập lại mật khẩu" (Confirm Password) vào form đăng ký.
  - Nghiên cứu tích hợp tính năng Đăng nhập/Đăng ký trực tiếp qua tài khoản Google (Gmail).
- **Nâng cấp Báo cáo Học tập (Hồ sơ học tập):**
  - Cập nhật thêm bảng tổng hợp kết quả chi tiết: Thống kê tổng số bài tập của mỗi loại (Tự luận/Nộp file), sinh viên đã làm được bao nhiêu bài, tổng điểm đạt được là bao nhiêu.
  - Mở rộng chức năng Tra cứu: Cho phép xem kết quả học tập kể cả khi không cần đăng nhập, chỉ yêu cầu nhập đúng Tên đăng nhập và Số điện thoại.

---
*(Lần cập nhật cuối: 18/05/2026)*
