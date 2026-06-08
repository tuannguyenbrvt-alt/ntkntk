<?php
// helpers/MailHelper.php
// Gửi Email qua PHP mail() tích hợp sẵn (không cần cài thêm thư viện)
// Để dùng SMTP (Gmail/Mailjet...), chỉ cần đổi hàm send() bên dưới

class MailHelper {
    private static $fromEmail = 'noreply@ntkntk.com';
    private static $fromName  = 'Trung tâm Nguyễn Minh';

    /**
     * Gửi email
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
        $headers .= "Reply-To: " . self::$fromEmail . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        $to = self::$fromName ? "$toName <$toEmail>" : $toEmail;

        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlBody, $headers);
    }

    /**
     * Email xác nhận đăng ký khóa học cho Học viên
     */
    public static function sendEnrollmentConfirmation(array $user, array $course): bool {
        $subject = "✅ Xác nhận đăng ký: " . $course['title'];
        $body = self::wrapTemplate("
            <h2 style='color:#0d6efd;'>Xin chào, {$user['full_name']}!</h2>
            <p>Chúng tôi đã nhận được yêu cầu đăng ký khóa học của bạn và đang chờ xác nhận thanh toán.</p>
            <div style='background:#f8f9fa;border-radius:8px;padding:20px;margin:20px 0;'>
                <h3 style='margin:0 0 10px;'>{$course['title']}</h3>
                <p style='margin:0;color:#6c757d;'>Học phí: <strong style='color:#dc3545;'>" . number_format($course['price']) . " đ</strong></p>
            </div>
            <p>Sau khi Admin xác nhận thanh toán, tài khoản của bạn sẽ được kích hoạt khóa học ngay lập tức.</p>
            <p>Cảm ơn bạn đã tin tưởng <strong>" . APP_NAME . "</strong>!</p>
        ");
        return self::send($user['email'], $user['full_name'], $subject, $body);
    }

    /**
     * Email thông báo Admin có đơn đăng ký mới
     */
    public static function sendAdminNewEnrollment(array $adminEmail, array $user, array $course): bool {
        $subject = "🔔 Đơn đăng ký mới: " . $user['full_name'];
        $body = self::wrapTemplate("
            <h2 style='color:#198754;'>Có đơn đăng ký mới!</h2>
            <table style='width:100%;border-collapse:collapse;margin:15px 0;'>
                <tr><td style='padding:8px;background:#f8f9fa;width:40%;'>Học viên</td><td style='padding:8px;'><strong>{$user['full_name']}</strong></td></tr>
                <tr><td style='padding:8px;background:#f8f9fa;'>Email</td><td style='padding:8px;'>{$user['email']}</td></tr>
                <tr><td style='padding:8px;background:#f8f9fa;'>Khóa học</td><td style='padding:8px;'><strong>{$course['title']}</strong></td></tr>
                <tr><td style='padding:8px;background:#f8f9fa;'>Học phí</td><td style='padding:8px;color:#dc3545;'><strong>" . number_format($course['price']) . " đ</strong></td></tr>
            </table>
            <a href='" . APP_URL . "/admin/enrollments' style='display:inline-block;background:#0d6efd;color:#fff;padding:10px 24px;border-radius:50px;text-decoration:none;font-weight:bold;'>Duyệt ngay →</a>
        ");
        return self::send($adminEmail['email'], $adminEmail['full_name'] ?? 'Admin', $subject, $body);
    }

    /**
     * Email thông báo khóa học được kích hoạt
     */
    public static function sendEnrollmentApproved(array $user, array $course): bool {
        $subject = "🎉 Tài khoản đã được kích hoạt: " . $course['title'];
        $body = self::wrapTemplate("
            <h2 style='color:#198754;'>Chúc mừng {$user['full_name']}!</h2>
            <p>Thanh toán của bạn đã được xác nhận. Bạn có thể bắt đầu học ngay bây giờ!</p>
            <div style='background:#d1e7dd;border-radius:8px;padding:20px;margin:20px 0;'>
                <h3 style='margin:0 0 10px;color:#0f5132;'>{$course['title']}</h3>
                <p style='margin:0;color:#0f5132;'>✅ Trạng thái: Đã kích hoạt</p>
            </div>
            <a href='" . APP_URL . "/profile' style='display:inline-block;background:#198754;color:#fff;padding:10px 24px;border-radius:50px;text-decoration:none;font-weight:bold;'>Vào học ngay →</a>
        ");
        return self::send($user['email'], $user['full_name'], $subject, $body);
    }

    /**
     * Email thông báo có tin nhắn mới khi người dùng offline
     */
    public static function sendChatNotification(string $toEmail, string $toName, string $senderName, string $messageSnippet, string $chatUrl): bool {
        $subject = "💬 Tin nhắn mới từ " . $senderName;
        $body = self::wrapTemplate("
            <h2 style='color:#0d6efd;'>Bạn có tin nhắn mới!</h2>
            <p>Xin chào <strong>{$toName}</strong>,</p>
            <p>Hệ thống ghi nhận bạn có tin nhắn mới chưa đọc từ <strong>{$senderName}</strong>:</p>
            <div style='background:#f8f9fa; border-left: 4px solid #0d6efd; border-radius: 4px; padding: 15px; margin: 20px 0; font-style: italic; color: #495057;'>
                \"" . htmlspecialchars($messageSnippet) . "\"
            </div>
            <p style='margin-bottom: 30px;'>Vui lòng nhấp vào nút bên dưới để xem chi tiết và phản hồi cuộc trò chuyện:</p>
            <div style='text-align: center;'>
                <a href='{$chatUrl}' style='display:inline-block; background:#0d6efd; color:#fff; padding:12px 30px; border-radius:50px; text-decoration:none; font-weight:bold; font-size:16px; box-shadow: 0 4px 6px rgba(13,110,253,0.15);'>Xem cuộc trò chuyện →</a>
            </div>
        ");
        return self::send($toEmail, $toName, $subject, $body);
    }

    private static function wrapTemplate(string $content): string {
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#0d6efd;padding:20px 30px;border-radius:8px 8px 0 0;text-align:center;'>
                <h1 style='color:#fff;margin:0;font-size:22px;'>" . APP_NAME . "</h1>
            </div>
            <div style='padding:30px;background:#fff;border:1px solid #dee2e6;border-top:none;border-radius:0 0 8px 8px;'>
                $content
                <hr style='margin:30px 0;border-color:#dee2e6;'>
                <p style='color:#6c757d;font-size:13px;margin:0;'>Email này được gửi tự động từ hệ thống. Vui lòng không reply lại.</p>
            </div>
        </div>";
    }
}
