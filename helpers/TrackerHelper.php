<?php
// helpers/TrackerHelper.php

class TrackerHelper {
    /**
     * Ghi nhận lượt truy cập của khách/học viên.
     * Sử dụng session-based tracking để tránh tăng ảo số lượt truy cập khi reload trang.
     * 
     * @param PDO $db Kết nối cơ sở dữ liệu
     */
    public static function recordVisit($db) {
        if (!$db) {
            return;
        }

        // Chỉ ghi nhận cho các yêu cầu GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }

        // Bỏ qua các yêu cầu AJAX
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if ($isAjax) {
            return;
        }

        // Bỏ qua các đường dẫn API, Chat, hoặc tải lên/tài nguyên tĩnh
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (preg_match('/^\/(api|chat|admin\/chat|uploads|assets)/i', $uri)) {
            return;
        }

        // Bỏ qua nếu là file tĩnh trực tiếp được rewrite qua index
        if (preg_match('/\.(jpg|jpeg|png|gif|css|js|ico|pdf|txt|xml)$/i', $uri)) {
            return;
        }

        // Phiên làm việc hôm nay của người dùng
        $today = date('Y-m-d');
        if (isset($_SESSION['last_recorded_visit_date']) && $_SESSION['last_recorded_visit_date'] === $today) {
            return;
        }

        try {
            $stmt = $db->prepare("INSERT INTO site_visits (visit_date, visit_count) VALUES (?, 1) ON DUPLICATE KEY UPDATE visit_count = visit_count + 1");
            $stmt->execute([$today]);
            
            // Đánh dấu đã ghi nhận trong phiên hôm nay
            $_SESSION['last_recorded_visit_date'] = $today;
        } catch (PDOException $e) {
            // Tự động sửa lỗi (self-healing) nếu bảng site_visits chưa tồn tại
            if ($e->getCode() === '42S02' || strpos($e->getMessage(), '1146') !== false) {
                try {
                    $db->exec("CREATE TABLE IF NOT EXISTS `site_visits` (
                      `visit_date` date NOT NULL,
                      `visit_count` int(11) NOT NULL DEFAULT '0',
                      PRIMARY KEY (`visit_date`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
                    
                    // Thử ghi nhận lại
                    $stmt = $db->prepare("INSERT INTO site_visits (visit_date, visit_count) VALUES (?, 1) ON DUPLICATE KEY UPDATE visit_count = visit_count + 1");
                    $stmt->execute([$today]);
                    
                    $_SESSION['last_recorded_visit_date'] = $today;
                } catch (Exception $innerEx) {
                    // Bỏ qua lỗi để không làm gián đoạn trải nghiệm người dùng
                }
            }
        } catch (Exception $e) {
            // Bỏ qua các lỗi chung khác
        }
    }

    /**
     * Ghi nhận trạng thái online của khách/học viên.
     * 
     * @param PDO $db Kết nối cơ sở dữ liệu
     */
    public static function trackOnline($db) {
        if (!$db) {
            return;
        }

        // Chỉ ghi nhận cho các yêu cầu GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return;
        }

        // Bỏ qua các yêu cầu AJAX
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        if ($isAjax) {
            return;
        }

        // Bỏ qua các đường dẫn API, Chat, hoặc tải lên/tài nguyên tĩnh
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (preg_match('/^\/(api|chat|admin\/chat|uploads|assets)/i', $uri)) {
            return;
        }

        // Bỏ qua nếu là file tĩnh trực tiếp được rewrite qua index
        if (preg_match('/\.(jpg|jpeg|png|gif|css|js|ico|pdf|txt|xml)$/i', $uri)) {
            return;
        }

        $sessionId = session_id();
        if (!$sessionId) {
            return;
        }

        $now = time();
        try {
            $stmt = $db->prepare("INSERT INTO site_online (session_id, last_activity) VALUES (?, ?) ON DUPLICATE KEY UPDATE last_activity = ?");
            $stmt->execute([$sessionId, $now, $now]);

            // Dọn dẹp các session cũ quá 5 phút (300 giây)
            $expired = $now - 300;
            $stmtDel = $db->prepare("DELETE FROM site_online WHERE last_activity < ?");
            $stmtDel->execute([$expired]);
        } catch (PDOException $e) {
            // Tự động sửa lỗi (self-healing) nếu bảng site_online chưa tồn tại
            if ($e->getCode() === '42S02' || strpos($e->getMessage(), '1146') !== false) {
                try {
                    $db->exec("CREATE TABLE IF NOT EXISTS `site_online` (
                      `session_id` varchar(255) NOT NULL,
                      `last_activity` int(11) NOT NULL,
                      PRIMARY KEY (`session_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
                    
                    // Thử lại
                    $stmt = $db->prepare("INSERT INTO site_online (session_id, last_activity) VALUES (?, ?) ON DUPLICATE KEY UPDATE last_activity = ?");
                    $stmt->execute([$sessionId, $now, $now]);
                } catch (Exception $innerEx) {
                    // Bỏ qua để không làm gián đoạn người dùng
                }
            }
        } catch (Exception $e) {
            // Bỏ qua các lỗi chung khác
        }
    }

    /**
     * Lấy số liệu thống kê lượt truy cập.
     * 
     * @param PDO $db Kết nối cơ sở dữ liệu
     * @return array Mảng chứa 'online', 'today' và 'total'
     */
    public static function getStats($db) {
        $stats = ['online' => 0, 'today' => 0, 'total' => 0];
        if (!$db) {
            return $stats;
        }

        try {
            $today = date('Y-m-d');
            
            // 1. Thống kê hôm nay
            $stmtToday = $db->prepare("SELECT visit_count FROM site_visits WHERE visit_date = ?");
            $stmtToday->execute([$today]);
            $stats['today'] = (int)($stmtToday->fetchColumn() ?: 0);

            // 2. Tổng thống kê
            $stmtTotal = $db->query("SELECT SUM(visit_count) FROM site_visits");
            $stats['total'] = (int)($stmtTotal->fetchColumn() ?: 0);

            // 3. Đang online (hoạt động trong vòng 5 phút qua)
            $now = time();
            $expired = $now - 300;
            $stmtOnline = $db->prepare("SELECT COUNT(*) FROM site_online WHERE last_activity >= ?");
            $stmtOnline->execute([$expired]);
            $stats['online'] = (int)($stmtOnline->fetchColumn() ?: 0);

            // Đảm bảo tối thiểu hiển thị 1 người online (chính là user hiện tại đang truy cập)
            if ($stats['online'] < 1) {
                $stats['online'] = 1;
            }
            
        } catch (PDOException $e) {
            // Trả về 0 nếu bảng chưa được khởi tạo
        }

        return $stats;
    }
}
