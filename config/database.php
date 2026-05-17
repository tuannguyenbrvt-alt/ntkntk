<?php
// config/database.php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Báo lỗi dạng Exception
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mặc định fetch theo mảng kết hợp
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Dùng native prepared statements của MySQL
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            // Trong môi trường production không nên in thẳng $e->getMessage() ra màn hình
            die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
        }
    }

    // Design Pattern: Singleton để tránh tạo nhiều kết nối DB
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
    
    // Ngăn chặn clone object
    private function __clone() {}
    
    // Ngăn chặn serialize/deserialize object
    public function __wakeup() {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}
