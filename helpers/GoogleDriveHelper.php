<?php
/**
 * GoogleDriveHelper - Upload file len Google Drive qua Service Account
 * Khong can Composer - chi dung cURL va OpenSSL co san tren moi server PHP
 */
class GoogleDriveHelper {

    private static function getAccessToken($serviceAccountJson) {
        $sa = json_decode($serviceAccountJson, true);
        if (!$sa) throw new Exception('Service Account JSON khong hop le.');

        $now   = time();
        $claim = [
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive.file',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $header  = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($claim));
        $header  = rtrim(strtr($header,  '+/', '-_'), '=');
        $payload = rtrim(strtr($payload, '+/', '-_'), '=');

        $data = $header . '.' . $payload;
        $key  = $sa['private_key'];

        if (!openssl_sign($data, $sig, $key, 'SHA256')) {
            throw new Exception('Khong the ky JWT bang private key.');
        }
        $sig64 = rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');
        $jwt   = $data . '.' . $sig64;

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 30,
        ]);
        $res  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200) throw new Exception('Khong the lay access token: ' . $res);
        $data = json_decode($res, true);
        return $data['access_token'];
    }

    /**
     * Upload mot file len Google Drive
     * @param string $localPath     Duong dan tuyet doi den file tren server
     * @param string $originalName  Ten file hien thi tren Drive
     * @param string $folderId      ID thu muc dich tren Google Drive
     * @param string $serviceAccountJson  Noi dung file service-account.json
     * @return array ['id'=>string, 'url'=>string, 'name'=>string]
     */
    public static function uploadFile($localPath, $originalName, $folderId, $serviceAccountJson) {
        $token = self::getAccessToken($serviceAccountJson);

        $mime     = mime_content_type($localPath) ?: 'application/octet-stream';
        $metadata = json_encode([
            'name'    => $originalName,
            'parents' => [$folderId],
        ]);
        $fileData = file_get_contents($localPath);
        if ($fileData === false) throw new Exception('Khong the doc file de upload.');

        $boundary = '------GoogleDriveBoundary' . uniqid();
        $body  = "--{$boundary}\r\n";
        $body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
        $body .= $metadata . "\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: {$mime}\r\n\r\n";
        $body .= $fileData . "\r\n";
        $body .= "--{$boundary}--";

        $ch = curl_init('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,webViewLink');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$token}",
                "Content-Type: multipart/related; boundary={$boundary}",
                'Content-Length: ' . strlen($body),
            ],
            CURLOPT_TIMEOUT        => 120,
        ]);
        $res  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200) throw new Exception('Upload Google Drive that bai (HTTP ' . $code . '): ' . $res);
        $data = json_decode($res, true);

        // Cap quyen anyone can view
        self::makePublic($data['id'], $token);

        return [
            'id'   => $data['id'],
            'url'  => $data['webViewLink'] ?? 'https://drive.google.com/file/d/' . $data['id'] . '/view',
            'name' => $data['name'],
        ];
    }

    private static function makePublic($fileId, $token) {
        $ch = curl_init("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode(['role' => 'reader', 'type' => 'anyone']),
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$token}",
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Doc noi dung file Service Account tu config/
     */
    public static function loadServiceAccount() {
        $path = ROOT_PATH . '/config/google-service-account.json';
        if (!file_exists($path)) {
            throw new Exception('Chua cau hinh Google Drive Service Account. Vui long upload file google-service-account.json vao thu muc config/.');
        }
        return file_get_contents($path);
    }
}
