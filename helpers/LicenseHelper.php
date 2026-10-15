<?php
/**
 * LicenseHelper - Quản lý sinh và xác thực License Key cho phần mềm phòng máy LAN
 * Sử dụng thuật toán HMAC-SHA256 theo Fingerprint phần cứng (Machine ID)
 */
class LicenseHelper {
    const MASTER_SECRET = "NTKNTK_MMO_SECRET_KEY_2026_TIENTO_EDU";

    /**
     * Sinh License Key vĩnh viễn hoặc dùng thử
     * @param string $machineId Mã máy của khách (VD: 8A4F-1B2C-9E3D-7F0A)
     * @param string $productCode Mã sản phẩm (COMBO_EDU, SP01, SP02)
     * @param string $buyerEmail Email hoặc thông tin người mua
     * @param int $daysValid 0 là vĩnh viễn, >0 là số ngày dùng thử
     * @return string License Key định dạng LIC-base64-SIGNATURE
     */
    public static function generateKey($machineId, $productCode = 'COMBO_EDU', $buyerEmail = '', $daysValid = 0) {
        $cleanMachineId = strtoupper(trim($machineId));
        if (empty($cleanMachineId)) {
            $cleanMachineId = 'ANY-DEVICE';
        }

        $payload = [
            'm' => $cleanMachineId,
            'p' => $productCode,
            'e' => $buyerEmail ?: 'client@ntkntk.com',
            't' => time(),
            'd' => (int)$daysValid,
        ];

        $rawJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = strtoupper(substr(hash_hmac('sha256', $rawJson, self::MASTER_SECRET), 0, 12));
        $b64 = rtrim(strtr(base64_encode($rawJson), '+/', '-_'), '=');

        return "LIC-{$b64}-{$signature}";
    }

    /**
     * Xác thực License Key
     */
    public static function verifyKey($licenseKey, $machineId) {
        try {
            $parts = explode('-', trim($licenseKey));
            if (count($parts) < 3 || $parts[0] !== 'LIC') {
                return ['valid' => false, 'message' => 'Định dạng Key không hợp lệ!'];
            }
            $signature = end($parts);
            $b64 = implode('-', array_slice($parts, 1, -1));
            $rem = strlen($b64) % 4;
            if ($rem > 0) $b64 .= str_repeat('=', 4 - $rem);

            $rawJson = base64_decode(strtr($b64, '-_', '+/'));
            if (!$rawJson) {
                return ['valid' => false, 'message' => 'Không thể giải mã nội dung Key!'];
            }

            $expectedSig = strtoupper(substr(hash_hmac('sha256', $rawJson, self::MASTER_SECRET), 0, 12));
            if ($expectedSig !== $signature) {
                return ['valid' => false, 'message' => 'Chữ ký License không hợp lệ hoặc đã bị can thiệp!'];
            }

            $data = json_decode($rawJson, true);
            if (!$data) {
                return ['valid' => false, 'message' => 'Dữ liệu License bị hỏng!'];
            }

            $targetMachine = strtoupper(trim($machineId));
            if ($data['m'] !== 'ANY-DEVICE' && $data['m'] !== $targetMachine) {
                return ['valid' => false, 'message' => "Key được cấp cho máy ({$data['m']}), không khớp máy này ({$targetMachine})!"];
            }

            if ($data['d'] > 0) {
                $expireAt = $data['t'] + ($data['d'] * 86400);
                if (time() > $expireAt) {
                    return ['valid' => false, 'message' => 'Bản quyền dùng thử đã hết hạn!'];
                }
            }

            return [
                'valid' => true,
                'message' => $data['d'] > 0 ? "Dùng thử {$data['d']} ngày" : "Bản quyền Vĩnh Viễn",
                'data' => $data
            ];
        } catch (Exception $e) {
            return ['valid' => false, 'message' => $e->getMessage()];
        }
    }
}
