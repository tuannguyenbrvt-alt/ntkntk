<?php
class ConsultController extends Controller {
    public function store() {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false, 'msg' => 'Invalid request']);
            exit;
        }

        $full_name = trim($_POST['full_name'] ?? '');
        $phone     = trim($_POST['phone'] ?? '');
        $course    = trim($_POST['course'] ?? '');

        if (empty($phone)) {
            echo json_encode(['ok' => false, 'msg' => 'Vui lòng nhập số điện thoại.']);
            exit;
        }
        if (!preg_match('/^[0-9\s\+\-]{8,15}$/', $phone)) {
            echo json_encode(['ok' => false, 'msg' => 'Số điện thoại không hợp lệ.']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO consultation_requests (full_name, phone, course) VALUES (?, ?, ?)");
            $stmt->execute([$full_name, $phone, $course]);
            echo json_encode(['ok' => true, 'msg' => 'Đăng ký thành công! Chúng tôi sẽ liên hệ bạn sớm nhất.']);
        } catch (Exception $e) {
            echo json_encode(['ok' => false, 'msg' => 'Lỗi hệ thống, vui lòng thử lại sau.']);
        }
        exit;
    }
}
