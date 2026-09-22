<?php
// helpers/TurnstileHelper.php

class TurnstileHelper {
    const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Xác thực token Cloudflare Turnstile gửi từ client
     *
     * @param string|null $token Giá trị cf-turnstile-response từ POST request
     * @param string|null $remoteIp IP của người dùng (tùy chọn)
     * @return bool True nếu hợp lệ hoặc Turnstile bị tắt, False nếu bot / lỗi xác thực
     */
    public static function verify(?string $token, ?string $remoteIp = null): bool {
        // Nếu tính năng này bị tắt trong config, cho phép pass
        if (defined('TURNSTILE_ENABLED') && !TURNSTILE_ENABLED) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        $secretKey = defined('TURNSTILE_SECRET_KEY') ? TURNSTILE_SECRET_KEY : '';
        if (empty($secretKey)) {
            // Chưa có secret key -> không thể xác thực
            return false;
        }

        $ip = $remoteIp ?: ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '');
        // Lấy IP đầu tiên nếu có danh sách proxy
        if (strpos($ip, ',') !== false) {
            $ipParts = explode(',', $ip);
            $ip = trim($ipParts[0]);
        }

        $postData = [
            'secret'   => $secretKey,
            'response' => $token,
            'remoteip' => $ip
        ];

        try {
            if (function_exists('curl_init')) {
                $ch = curl_init(self::VERIFY_URL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && $response) {
                    $responseData = json_decode($response, true);
                    return !empty($responseData['success']);
                }
            } else {
                // Fallback stream_context
                $options = [
                    'http' => [
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($postData),
                        'timeout' => 10
                    ]
                ];
                $context = stream_context_create($options);
                $response = @file_get_contents(self::VERIFY_URL, false, $context);
                if ($response) {
                    $responseData = json_decode($response, true);
                    return !empty($responseData['success']);
                }
            }
        } catch (\Throwable $e) {
            error_log('[TurnstileHelper] Verification error: ' . $e->getMessage());
            return false;
        }

        return false;
    }
}
