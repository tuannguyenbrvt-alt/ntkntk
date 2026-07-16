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
* **Hệ thống Nộp bài tập (Assignments) (MỚI NÂNG CẤP):**
  - Hỗ trợ nộp bài **Tự luận** và **Upload File**.
  - **Nộp nhiều file lũy tiến và Chấm điểm/Nhận xét từng file:** Học viên có thể nộp nhiều file cho một nội dung bài tập theo hình thức lũy tiến (nộp trước một số file, đợi chấm, sau đó tiếp tục nộp thêm file mới cho các câu tiếp theo) mà không bị ghi đè hay mất dữ liệu cũ. Học viên có thể tự xóa/rút lại các file bài làm nếu giáo viên chưa chấm điểm.
  - **Chấm điểm riêng lẻ từng file:** Giáo viên xem, nhập điểm và nhận xét chi tiết cho từng file bài làm độc lập. Điểm tổng của toàn bộ bài nộp sẽ tự động được gợi ý dựa trên tổng điểm của các file thành phần (không vượt quá điểm tối đa của bài tập) và giáo viên vẫn có thể chỉnh sửa thủ công.
  - **Tích hợp Google Drive & Báo lỗi thông minh:** File bài làm lưu trên Google Drive để tiết kiệm dung lượng máy chủ. Nếu có lỗi khi upload, hệ thống ghi chi tiết lỗi vào cơ sở dữ liệu và hiển thị thân thiện với học sinh để nộp lại.
  - **Tự động hóa nâng cấp CSDL:** Cung cấp tệp `update_db.php` để tự động nâng cấp cấu trúc cơ sở dữ liệu và di chuyển an toàn toàn bộ dữ liệu file bài nộp cũ sang bảng mới `assignment_submission_files` khi deploy lên hosting chính thức.
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
* **Quản lý & Kích hoạt Học viên (MỚI NÂNG CẤP):**
  - **Tạo học viên mới (Super Admin)**: Chỉ tài khoản có quyền `super_admin` mới được truy cập trang `/admin/students/create` và tạo học viên mới. Biểu mẫu đăng ký xác thực đầy đủ các thông tin quan trọng, giữ lại dữ liệu cũ khi gặp lỗi validate.
  - **Cấp khóa học trực tiếp (Super Admin)**: Tích hợp nút và modal "Cấp khóa học" ngay trong trang chi tiết học viên. Cho phép Super Admin kích hoạt thủ công bất kỳ khóa học nào với giá tiền tùy chỉnh. Tự động chuyển đổi các đăng ký trạng thái chờ duyệt (`pending`) hiện tại sang đã kích hoạt (`active`) để tránh tạo bản ghi trùng lặp.
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
* **Hệ thống Bình luận đa đối tượng (MỚI NÂNG CẤP):**
  - **Bật/Tắt bình luận**: Cho phép Admin bật/tắt bình luận cho từng Bài viết, Khóa học và Bài học thông qua checkbox trong Admin Panel.
  - **Giao diện cây bình luận (Nested Replies)**: Component dùng chung `views/shared/comments.php` hiển thị phân cấp bình luận đẹp mắt và hỗ trợ gửi bình luận, trả lời (reply) nhanh, sửa hoặc xóa bình luận bằng AJAX không reload trang.
  - **Bảo mật và Phân quyền**: Thành viên đăng nhập được bình luận trực tiếp (tự động duyệt) và có quyền Sửa/Xóa bình luận của mình. Khách vãng lai chỉ xem được các bình luận được phê duyệt công khai với khách (`is_public_to_guest = 1`), và chỉ được bình luận trên Bài viết khi cung cấp Họ tên và SĐT (tin nhắn chờ duyệt `status = 'pending'`).
  - **Dashboard Điều duyệt Bình luận**: Admin Panel tích hợp trang `/admin/comments` và bộ đếm badge ở sidebar để duyệt bình luận của khách, bật/tắt quyền hiển thị với khách, hoặc xóa bất kỳ bình luận nào.
* **Tối ưu hóa hiệu năng & giảm tải Database (MỚI NÂNG CẤP):**
  - **Mở khóa Session sớm (PHP Session Locking):** Gọi `session_write_close()` ngay sau khi lấy thông tin xác thực từ session trong các AJAX endpoints lấy thông tin unread count, tải tin nhắn và danh sách chat. Tránh việc trình duyệt bị nghẽn luồng xếp hàng khi người dùng vừa chạy polling ngầm vừa tải trang chính.
  - **Giảm tải ghi/xóa Online Tracker:** Giới hạn tần suất ghi vào bảng `site_online` xuống tối đa 1 lần/phút cho mỗi phiên truy cập. Giảm tần suất chạy lệnh `DELETE` dọn dẹp các session hết hạn xuống còn xác suất 1% thay vì chạy 100% trên mọi request.
  - **Tối ưu chỉ mục (Index) Database:** Đánh chỉ mục `idx_last_activity` cho cột `last_activity` của bảng `site_online` giúp loại bỏ hoàn toàn việc MySQL quét toàn bộ bảng (Full Table Scan) khi đếm hoặc xóa session hết hạn.
  - **Script tự động nâng cấp database:** Thêm script `run_optimize_migration.php` chạy trực tiếp trên hosting để tạo index cho database thật mà không làm ảnh hưởng đến dữ liệu hiện có.
* **Tính năng Sao chép Đề cương Khóa học - LMS Clone (MỚI NÂNG CẤP):**
  - Hỗ trợ nhân bản sâu (Deep Copy) toàn bộ Phần, Chương, Bài học cùng các nội dung đi kèm (tin nhắn/văn bản, video, tệp PDF đính kèm, bài tập tự luận/nộp file, đề trắc nghiệm) từ các khóa học trước đó.
  - **Sao chép Ngân hàng câu hỏi độc lập:** Nhân bản các câu hỏi và đáp án liên kết trong `question_bank` sang khóa học đích tự động để tránh lỗi liên kết khóa ngoại khi xóa khóa học cũ.
  - Tích hợp giao diện trực quan gồm 3 nút sao chép tương ứng với 3 cấp độ (Phần, Chương, Bài học) và modals Bootstrap 5 gọi AJAX lấy cấu trúc động từ khóa học nguồn.

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
*(Lần cập nhật cuối: 16/07/2026)*
