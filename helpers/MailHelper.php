<?php
// helpers/MailHelper.php
// Hệ thống gửi Email hỗ trợ cả SMTP Gmail trực tiếp và PHP mail() dự phòng

class MailHelper {
    /**
     * Gửi email chính (ưu tiên qua SMTP Gmail nếu có cấu hình, tự động fallback về PHP mail())
     */
    public static function send(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
        $smtpPass = defined('SMTP_PASS') ? SMTP_PASS : '';
        $smtpEnabled = defined('SMTP_ENABLED') ? SMTP_ENABLED : true;

        if ($smtpEnabled && !empty($smtpPass)) {
            $smtpSuccess = self::smtpSend($toEmail, $toName, $subject, $htmlBody);
            if ($smtpSuccess) {
                return true;
            }
            error_log("[MailHelper] Gửi qua SMTP thất bại, đang chuyển sang PHP mail() dự phòng.");
        }

        return self::nativeMailSend($toEmail, $toName, $subject, $htmlBody);
    }

    /**
     * Gửi email bằng giao thức SMTP Socket (chuẩn cho Gmail SSL cổng 465 hoặc TLS cổng 587)
     */
    private static function smtpSend(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
        $host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
        $port = defined('SMTP_PORT') ? (int)SMTP_PORT : 465;
        $user = defined('SMTP_USER') ? SMTP_USER : 'tuannguyen.brvt@gmail.com';
        $pass = defined('SMTP_PASS') ? str_replace(' ', '', SMTP_PASS) : '';
        $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : (defined('APP_NAME') ? APP_NAME : 'Trung tâm Nguyễn Minh');

        $isSsl = ($port === 465);
        $remote = ($isSsl ? "ssl://" : "tcp://") . $host . ":" . $port;

        $context = stream_context_create([
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ]);

        $socket = @stream_socket_client($remote, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            error_log("[MailHelper] Lỗi kết nối SMTP socket: $errstr ($errno)");
            return false;
        }

        stream_set_timeout($socket, 15);

        $readResponse = function() use ($socket) {
            $data = '';
            while (!feof($socket)) {
                $line = fgets($socket, 515);
                if ($line === false) break;
                $data .= $line;
                if (isset($line[3]) && $line[3] === ' ') {
                    break;
                }
            }
            return $data;
        };

        $sendCommand = function(string $cmd) use ($socket) {
            fwrite($socket, $cmd . "\r\n");
        };

        $res = $readResponse();
        if (substr($res, 0, 3) !== '220') {
            fclose($socket);
            return false;
        }

        $clientHost = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
        $sendCommand("EHLO " . $clientHost);
        $res = $readResponse();

        // Nâng cấp TLS nếu dùng cổng 587
        if (!$isSsl && $port === 587) {
            $sendCommand("STARTTLS");
            $res = $readResponse();
            if (substr($res, 0, 3) !== '220') {
                fclose($socket);
                return false;
            }
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($socket);
                return false;
            }
            $sendCommand("EHLO " . $clientHost);
            $res = $readResponse();
        }

        // Xác thực người dùng (AUTH LOGIN)
        $sendCommand("AUTH LOGIN");
        $res = $readResponse();
        if (substr($res, 0, 3) !== '334') {
            fclose($socket);
            return false;
        }

        $sendCommand(base64_encode($user));
        $res = $readResponse();
        if (substr($res, 0, 3) !== '334') {
            fclose($socket);
            return false;
        }

        $sendCommand(base64_encode($pass));
        $res = $readResponse();
        if (substr($res, 0, 3) !== '235') {
            error_log("[MailHelper] Đăng nhập SMTP thất bại. Vui lòng kiểm tra lại Mật khẩu ứng dụng Google: " . trim($res));
            fclose($socket);
            return false;
        }

        // Người gửi và người nhận
        $sendCommand("MAIL FROM:<" . $user . ">");
        $res = $readResponse();
        if (substr($res, 0, 3) !== '250') {
            fclose($socket);
            return false;
        }

        $sendCommand("RCPT TO:<" . $toEmail . ">");
        $res = $readResponse();
        if (substr($res, 0, 3) !== '250') {
            fclose($socket);
            return false;
        }

        // Bắt đầu truyền nội dung
        $sendCommand("DATA");
        $res = $readResponse();
        if (substr($res, 0, 3) !== '354') {
            fclose($socket);
            return false;
        }

        // Soạn Header & Body chuẩn UTF-8 Base64
        $encodedSubject  = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedFromName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';
        $encodedToName   = !empty($toName) ? '=?UTF-8?B?' . base64_encode($toName) . '?=' : '';

        $headers = [];
        $headers[] = "Date: " . date('r');
        $headers[] = "From: {$encodedFromName} <{$user}>";
        $headers[] = "Reply-To: {$user}";
        $headers[] = "To: " . ($encodedToName ? "{$encodedToName} <{$toEmail}>" : "<{$toEmail}>");
        $headers[] = "Subject: {$encodedSubject}";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: base64";
        $headers[] = "X-Mailer: NTK Mailer (PHP Socket)";

        $payload = implode("\r\n", $headers) . "\r\n\r\n" . chunk_split(base64_encode($htmlBody)) . "\r\n.";
        $sendCommand($payload);
        $res = $readResponse();

        $sendCommand("QUIT");
        $readResponse();
        fclose($socket);

        return (substr($res, 0, 3) === '250');
    }

    /**
     * Gửi email qua PHP mail() nguyên bản (dự phòng khi chưa cấu hình SMTP)
     */
    private static function nativeMailSend(string $toEmail, string $toName, string $subject, string $htmlBody): bool {
        $fromEmail = defined('SMTP_USER') ? SMTP_USER : 'noreply@ntkntk.com';
        $fromName  = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : (defined('APP_NAME') ? APP_NAME : 'Trung tâm Nguyễn Minh');

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <" . $fromEmail . ">\r\n";
        $headers .= "Reply-To: " . $fromEmail . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        $to = !empty($toName) ? "=?UTF-8?B?" . base64_encode($toName) . "?= <$toEmail>" : $toEmail;

        return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlBody, $headers);
    }

    /**
     * Email gửi link đặt lại mật khẩu cho người dùng
     */
    public static function sendPasswordReset(array $user, string $resetUrl): bool {
        $subject = "🔑 Yêu cầu đặt lại mật khẩu - " . APP_NAME;
        $fullName = !empty($user['full_name']) ? $user['full_name'] : $user['username'];
        $body = self::wrapTemplate("
            <h2 style='color:#0d6efd;margin-top:0;'>Yêu cầu đặt lại mật khẩu</h2>
            <p>Xin chào <strong>{$fullName}</strong>,</p>
            <p>Hệ thống nhận được yêu cầu đặt lại mật khẩu cho tài khoản <strong>{$user['username']}</strong> tại <strong>" . APP_NAME . "</strong>.</p>
            <p>Nếu bạn thực hiện yêu cầu này, vui lòng nhấn vào nút bên dưới để tạo mật khẩu mới:</p>
            <div style='text-align:center;margin:30px 0;'>
                <a href='{$resetUrl}' style='display:inline-block;background:#198754;color:#ffffff;padding:12px 32px;border-radius:50px;text-decoration:none;font-weight:bold;font-size:16px;box-shadow:0 4px 6px rgba(25,135,84,0.25);'>Đặt lại mật khẩu ngay →</a>
            </div>
            <p style='color:#6c757d;font-size:14px;margin-bottom:10px;'>⏱️ Đường dẫn này chỉ có hiệu lực trong vòng <strong>30 phút</strong>.</p>
            <div style='background:#f8f9fa;border-left:4px solid #0d6efd;padding:12px 15px;border-radius:4px;word-break:break-all;font-size:13px;color:#495057;'>
                Nếu không bấm được nút trên, bạn có thể sao chép liên kết sau dán vào trình duyệt:<br>
                <a href='{$resetUrl}' style='color:#0d6efd;text-decoration:underline;'>{$resetUrl}</a>
            </div>
            <hr style='border:none;border-top:1px solid #eee;margin:25px 0;'>
            <p style='color:#dc3545;font-size:13px;margin:0;'><strong>Lưu ý:</strong> Nếu bạn không yêu cầu đặt lại mật khẩu, xin vui lòng bỏ qua email này. Mật khẩu hiện tại của bạn vẫn được giữ an toàn tuyệt đối.</p>
        ");
        return self::send($user['email'], $fullName, $subject, $body);
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
        $siteName = defined('APP_NAME') ? APP_NAME : 'Trung Tâm Ngoại Ngữ Tin Học Nguyễn Minh';
        return "
        <div style='font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:0 auto;background:#f4f6f9;padding:20px 10px;'>
            <div style='background:#0d6efd;padding:20px 25px;border-radius:8px 8px 0 0;text-align:center;'>
                <h1 style='color:#ffffff;margin:0;font-size:20px;font-weight:bold;'>" . htmlspecialchars($siteName) . "</h1>
            </div>
            <div style='padding:30px;background:#ffffff;border:1px solid #dee2e6;border-top:none;border-radius:0 0 8px 8px;'>
                $content
                <hr style='margin:30px 0;border:none;border-top:1px solid #dee2e6;'>
                <p style='color:#6c757d;font-size:12px;margin:0;text-align:center;'>Email này được gửi tự động từ hệ thống website. Vui lòng không trả lời trực tiếp email này.</p>
            </div>
        </div>";
    }
}
