<?php
// helpers/ZaloHelper.php

class ZaloHelper {
    /**
     * Gửi tin nhắn Zalo ZNS (Zalo Notification Service)
     * 
     * @param string $phone Số điện thoại người nhận (định dạng 84xxx hoặc 0xxx)
     * @param string $templateId ID mẫu tin nhắn ZNS đã được duyệt
     * @param array $templateData Dữ liệu truyền vào mẫu (ví dụ: ['customer_name' => '...', 'message_snippet' => '...'])
     * @return array Kết quả gửi tin ['success' => bool, 'message' => string]
     */
    public static function sendZNS(string $phone, string $templateId, array $templateData): array {
        // Chuẩn hóa số điện thoại sang định dạng 84...
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($phone, '0') === 0) {
            $phone = '84' . substr($phone, 1);
        }

        // Chế độ mô phỏng (Simulation Mode)
        if (!defined('ZALO_ZNS_ENABLED') || !ZALO_ZNS_ENABLED) {
            return self::logSimulation($phone, $templateId, $templateData);
        }

        // Chế độ thực tế (Live mode)
        $accessToken = defined('ZALO_ACCESS_TOKEN') ? ZALO_ACCESS_TOKEN : '';
        if (empty($accessToken) || $accessToken === 'YOUR_ZALO_ACCESS_TOKEN_HERE') {
            return [
                'success' => false,
                'message' => 'Chưa cấu hình ZALO_ACCESS_TOKEN hợp lệ.'
            ];
        }

        $url = 'https://business.openapi.zalo.me/message/template';
        $payload = [
            'phone' => $phone,
            'template_id' => $templateId,
            'template_data' => $templateData,
            'tracking_id' => 'ntkntk_' . uniqid()
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'access_token: ' . $accessToken
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            error_log('Zalo ZNS cURL Error: ' . $err);
            return [
                'success' => false,
                'message' => 'Lỗi kết nối Zalo API: ' . $err
            ];
        }

        $resDecoded = json_decode($response, true);
        if ($httpCode === 200 && isset($resDecoded['error']) && $resDecoded['error'] === 0) {
            return [
                'success' => true,
                'message' => 'Gửi tin nhắn ZNS thành công.'
            ];
        }

        $errMsg = $resDecoded['message'] ?? 'Lỗi không xác định từ Zalo API';
        error_log("Zalo ZNS API Error (HTTP $httpCode): " . $response);
        return [
            'success' => false,
            'message' => $errMsg
        ];
    }

    /**
     * Ghi log mô phỏng gửi Zalo ZNS
     */
    private static function logSimulation(string $phone, string $templateId, array $templateData): array {
        $logDir = ROOT_PATH . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/zalo_zns.log';
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'SIMULATED_ZNS',
            'phone' => $phone,
            'template_id' => $templateId,
            'template_data' => $templateData,
            'status' => 'SUCCESS_SIMULATED'
        ];

        $logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
        @file_put_contents($logFile, $logLine, FILE_APPEND);

        return [
            'success' => true,
            'message' => '[Mô phỏng] Đã ghi log gửi tin nhắn ZNS thành công tới ' . $phone
        ];
    }
}
