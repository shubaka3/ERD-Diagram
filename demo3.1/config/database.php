<?php
if (!class_exists('Database')) { // Kiểm tra nếu class chưa tồn tại
    class Database {
        private $host = "localhost";
        private $db_name = "demo7"; // Tên database
        private $username = "root"; // Username mặc định của XAMPP
        private $password = "1234"; // Password mặc định của XAMPP (để trống)
        public $conn;

        public function getConnection() {
            $this->conn = null;
            try {
                $pdo = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Tạo database nếu chưa có
                $pdo->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name);
                $pdo->exec("USE " . $this->db_name);

                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo "Connection error: " . $exception->getMessage();
            }
            return $this->conn;
        }
    }
}
?>
