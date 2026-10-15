<?php
/**
 * PaymentApiController - Xử lý tạo đơn hàng, polling trạng thái và Webhook nhận tiền tự động
 * Tích hợp chuẩn VietQR Napas 24/7 (MBBank) + Cổng SePay / Casso
 */
require_once ROOT_PATH . '/helpers/LicenseHelper.php';

class PaymentApiController extends Controller {

    private static $PRODUCT_CATALOG = [
        'COMBO_EDU' => [
            'name' => 'Combo Phòng Máy LAN 2 Trong 1',
            'amount' => 690000,
            'desc' => 'Trọn bộ Game Luyện Gõ 10 Ngón + Web Trợ Giảng & Thi Trắc Nghiệm Mạng LAN'
        ],
        'SP01' => [
            'name' => 'Web Trợ Giảng & Thi Trắc Nghiệm LAN',
            'amount' => 490000,
            'desc' => 'Web Trợ Giảng, Phát Video Bài Giảng & Thi Trắc Nghiệm Mạng LAN'
        ],
        'SP02' => [
            'name' => 'Game Luyện Gõ Bàn Phím LAN',
            'amount' => 390000,
            'desc' => 'Đấu Trường Luyện Gõ 10 Ngón Cho Phòng Máy Tin Học Mạng LAN'
        ],
    ];

    /**
     * POST /api/payment/create-order
     * Tạo đơn hàng mới và trả về thông tin VietQR thanh toán
     */
    public function createOrder() {
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $productCode = strtoupper(trim($input['product_code'] ?? 'COMBO_EDU'));
        $machineId = strtoupper(trim($input['machine_id'] ?? ''));
        $buyerContact = trim($input['buyer_contact'] ?? ($input['buyer_phone'] ?? ''));
        $buyerEmail = trim($input['buyer_email'] ?? '');

        if (!isset(self::$PRODUCT_CATALOG[$productCode])) {
            $productCode = 'COMBO_EDU';
        }

        $product = self::$PRODUCT_CATALOG[$productCode];
        $amount = (int)$product['amount'];
        $productName = $product['name'];

        try {
            $db = Database::getInstance()->getConnection();

            // Sinh mã đơn hàng dạng EDU + 4 số ngẫu nhiên (đảm bảo không trùng)
            $orderCode = '';
            for ($i = 0; $i < 10; $i++) {
                $candidate = 'EDU' . rand(1000, 9999);
                $stmt = $db->prepare("SELECT id FROM software_orders WHERE order_code = ?");
                $stmt->execute([$candidate]);
                if (!$stmt->fetch()) {
                    $orderCode = $candidate;
                    break;
                }
            }

            if (empty($orderCode)) {
                $orderCode = 'EDU' . time();
            }

            // Lưu đơn hàng vào database
            $insert = $db->prepare("INSERT INTO software_orders 
                (order_code, product_code, product_name, amount, machine_id, buyer_contact, buyer_email, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $insert->execute([
                $orderCode,
                $productCode,
                $productName,
                $amount,
                $machineId ?: null,
                $buyerContact ?: null,
                $buyerEmail ?: null
            ]);

            // Sinh VietQR QuickLink chuẩn Napas 24/7 (MBBank)
            $transferContent = $orderCode;
            $qrUrl = "https://img.vietqr.io/image/970422-0979875712-compact2.png?amount={$amount}&addInfo=" . urlencode($transferContent) . "&accountName=" . urlencode("NGUYEN MINH TUAN");

            echo json_encode([
                'status' => 'success',
                'order_code' => $orderCode,
                'product_code' => $productCode,
                'product_name' => $productName,
                'amount' => $amount,
                'amount_formatted' => number_format($amount, 0, ',', '.') . ' VNĐ',
                'bank_account' => '0979875712',
                'bank_name' => 'MBBank (Ngân Hàng Quân Đội)',
                'account_name' => 'NGUYEN MINH TUAN',
                'transfer_content' => $transferContent,
                'qr_url' => $qrUrl,
                'machine_id' => $machineId
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * GET /api/payment/check-order?code=EDUxxxx
     * Khách hàng trên trình duyệt polling kiểm tra xem tiền đã về chưa
     */
    public function checkOrder() {
        header('Content-Type: application/json; charset=utf-8');

        $orderCode = strtoupper(trim($_GET['code'] ?? ''));
        if (empty($orderCode)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Thiếu mã đơn hàng.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM software_orders WHERE order_code = ? LIMIT 1");
            $stmt->execute([$orderCode]);
            $order = $stmt->fetch();

            if (!$order) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng.'], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Nếu đơn đã thanh toán nhưng chưa có key và đã có machine_id -> tự động sinh ngay
            if ($order['status'] === 'paid' && empty($order['license_key']) && !empty($order['machine_id'])) {
                $licenseKey = LicenseHelper::generateKey($order['machine_id'], $order['product_code'], $order['buyer_email'] ?: $order['buyer_contact']);
                $up = $db->prepare("UPDATE software_orders SET license_key = ? WHERE id = ?");
                $up->execute([$licenseKey, $order['id']]);
                $order['license_key'] = $licenseKey;
            }

            echo json_encode([
                'status' => $order['status'], // 'pending' hoặc 'paid'
                'order_code' => $order['order_code'],
                'product_name' => $order['product_name'],
                'amount' => (int)$order['amount'],
                'machine_id' => $order['machine_id'],
                'license_key' => $order['license_key'],
                'paid_at' => $order['paid_at']
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * POST /api/payment/claim-key
     * Trường hợp khách chuyển khoản xong mới nhập Mã máy chủ (Machine ID)
     */
    public function claimKey() {
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $orderCode = strtoupper(trim($input['order_code'] ?? ''));
        $machineId = strtoupper(trim($input['machine_id'] ?? ''));

        if (empty($orderCode) || empty($machineId)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng cung cấp mã đơn hàng và mã máy chủ!'], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM software_orders WHERE order_code = ? LIMIT 1");
            $stmt->execute([$orderCode]);
            $order = $stmt->fetch();

            if (!$order) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng này.'], JSON_UNESCAPED_UNICODE);
                return;
            }

            if ($order['status'] !== 'paid') {
                echo json_encode([
                    'status' => 'unpaid',
                    'message' => 'Đơn hàng này chưa nhận được thanh toán. Vui lòng quét mã QR chuyển khoản trước khi nhận Key!'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Sinh License Key
            $licenseKey = LicenseHelper::generateKey($machineId, $order['product_code'], $order['buyer_email'] ?: $order['buyer_contact']);

            $up = $db->prepare("UPDATE software_orders SET machine_id = ?, license_key = ? WHERE id = ?");
            $up->execute([$machineId, $licenseKey, $order['id']]);

            echo json_encode([
                'status' => 'success',
                'order_code' => $orderCode,
                'product_name' => $order['product_name'],
                'machine_id' => $machineId,
                'license_key' => $licenseKey,
                'message' => 'Cấp License Key thành công!'
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * POST /api/payment/webhook
     * Webhook nhận thông báo biến động số dư ngân hàng từ SePay / Casso
     */
    public function webhook() {
        header('Content-Type: application/json; charset=utf-8');

        $rawBody = file_get_contents('php://input');
        $payload = json_decode($rawBody, true);

        // Ghi log webhook để theo dõi và đối soát
        $logFile = ROOT_PATH . '/uploads/webhook_payment.log';
        @file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $rawBody . PHP_EOL, FILE_APPEND);

        if (!$payload) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dữ liệu webhook rỗng.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 1. Phân giải dữ liệu từ SePay hoặc Casso
        $transactions = [];

        // Trường hợp SePay (gửi 1 object giao dịch duy nhất)
        if (isset($payload['transferAmount']) || isset($payload['gateway'])) {
            $transactions[] = [
                'id' => $payload['id'] ?? ($payload['referenceCode'] ?? ''),
                'amount' => (int)($payload['transferAmount'] ?? 0),
                'content' => $payload['content'] ?? ($payload['description'] ?? ''),
                'gateway' => $payload['gateway'] ?? 'SePay',
                'raw' => $rawBody
            ];
        }
        // Trường hợp Casso (gửi mảng data)
        elseif (isset($payload['data']) && is_array($payload['data'])) {
            foreach ($payload['data'] as $item) {
                $transactions[] = [
                    'id' => $item['id'] ?? ($item['tid'] ?? ''),
                    'amount' => (int)($item['amount'] ?? 0),
                    'content' => $item['description'] ?? '',
                    'gateway' => 'Casso',
                    'raw' => json_encode($item)
                ];
            }
        }
        // Trường hợp test thủ công
        elseif (isset($payload['order_code'])) {
            $transactions[] = [
                'id' => 'TEST_' . time(),
                'amount' => (int)($payload['amount'] ?? 0),
                'content' => $payload['order_code'],
                'gateway' => 'ManualTest',
                'raw' => $rawBody
            ];
        }

        if (empty($transactions)) {
            echo json_encode(['success' => true, 'message' => 'Không có giao dịch nào cần xử lý.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $processedCount = 0;
        $db = Database::getInstance()->getConnection();

        foreach ($transactions as $tx) {
            $content = $tx['content'];
            $amount = $tx['amount'];

            // Trích xuất mã đơn hàng dạng EDUxxxx (từ 4 đến 8 chữ số)
            if (preg_match('/(EDU\d{4,8})/i', $content, $matches)) {
                $orderCode = strtoupper($matches[1]);

                $stmt = $db->prepare("SELECT * FROM software_orders WHERE order_code = ? LIMIT 1");
                $stmt->execute([$orderCode]);
                $order = $stmt->fetch();

                if ($order) {
                    // Kiểm tra số tiền chuyển có đủ với giá trị đơn hàng hay không
                    // Cho phép nếu số tiền >= đơn hàng
                    if ($amount >= (int)$order['amount']) {
                        $licenseKey = null;

                        // Nếu đơn hàng đã có machine_id -> sinh ngay License Key vĩnh viễn
                        if (!empty($order['machine_id'])) {
                            $licenseKey = LicenseHelper::generateKey(
                                $order['machine_id'], 
                                $order['product_code'], 
                                $order['buyer_email'] ?: $order['buyer_contact']
                            );
                        }

                        // Cập nhật trạng thái paid
                        $update = $db->prepare("UPDATE software_orders SET 
                            status = 'paid',
                            paid_at = NOW(),
                            license_key = COALESCE(?, license_key),
                            payment_gateway = ?,
                            payment_transaction_id = ?,
                            payment_raw_data = ?
                            WHERE id = ?");
                        $update->execute([
                            $licenseKey,
                            $tx['gateway'],
                            $tx['id'],
                            $tx['raw'],
                            $order['id']
                        ]);

                        $processedCount++;
                        error_log("[WEBHOOK] Khớp đơn hàng {$orderCode} - Đã cấp License Key!");
                    } else {
                        error_log("[WEBHOOK] Đơn hàng {$orderCode} thiếu tiền: chuyển {$amount}, yêu cầu {$order['amount']}");
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'processed' => $processedCount,
            'message' => "Đã xử lý {$processedCount} giao dịch thành công."
        ], JSON_UNESCAPED_UNICODE);
    }
}
