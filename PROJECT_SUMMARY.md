# Nhật ký Dự án: LMS Ngoại Ngữ Tin Học Nguyễn Minh
**Ngày cập nhật cuối:** 16/05/2026

## 1. Trạng thái hiện tại (Current State)
Dự án đã hoàn tất giai đoạn thiết kế giao diện chính và các tính năng quản trị cốt lõi. Hệ thống đã được tối ưu hóa để chạy ổn định trên Hosting PA Vietnam (MySQL 5.7 & PHP < 7.4).

## 2. Các tính năng đã hoàn thành (Done)
- **Footer chuyên nghiệp:** Cấu trúc 4 cột, tích hợp form tư vấn AJAX.
- **Hệ thống Tư vấn:** `ConsultController`, `AdminConsultController` và bảng `consultation_requests`.
- **Duyệt Đăng ký:** Cho phép Admin Sửa (trạng thái + ghi chú), Duyệt nhanh và Xóa yêu cầu học.
- **Phân quyền (RBAC):** Đã thêm vai trò `teacher`. Admin có thể gán quyền cho người dùng tại `/admin/users`.
- **Thư viện Media:** Fix lỗi logic và giao diện quản lý file.
- **Chia sẻ mạng xã hội:** Tích hợp Facebook, Twitter, Email và Copy Link trên toàn trang.
- **Fix lỗi 500:** Chuyển đổi toàn bộ Arrow Functions sang cú pháp PHP cũ để tương thích hosting.

## 3. Cấu trúc Database quan trọng
- `consultation_requests`: Lưu khách hàng tiềm năng.
- `users`: Cột `role` đã mở rộng (super_admin, admin, teacher, student, guest).
- `enrollments`: Đã thêm cột `note` (TEXT) để lưu vết chăm sóc khách hàng.

## 4. Danh sách File Migration (Cần chạy trên Hosting)
1. `https://ntkntk.com/migrate_consult_table.php`
2. `https://ntkntk.com/migrate_enrollment_note.php`
3. `https://ntkntk.com/migrate_add_teacher_role.php`
*(Lưu ý: Xóa file ngay sau khi chạy xong để bảo mật)*

## 5. Các công việc cần làm tiếp theo (Todo)
- **Phân quyền giáo viên:** Thiết lập để giáo viên chỉ được xem/sửa khóa học của chính họ.
- **Email Notification:** Cài đặt MailHelper để gửi thông báo tự động khi có đăng ký mới.
- **Trang Học tập (Learning):** Xây dựng giao diện học tập cho học viên (Giai đoạn tiếp theo).

---
**Ghi chú:** Khi mở lại máy, hãy mở file này và yêu cầu AI đọc để nắm bắt lại toàn bộ mạch công việc.
