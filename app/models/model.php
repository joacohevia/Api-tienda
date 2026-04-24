<?php
abstract class Model {
    protected $db;

    public function __construct() {
        // Usamos las constantes definidas en config.php
        $host = MYSQL_HOST;
        $port = defined('MYSQL_PORT') ? MYSQL_PORT : 3306;
        $user = MYSQL_USER;
        $pass = MYSQL_PASS;
        $db   = MYSQL_DB;

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
            $this->db = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log("DB Connection Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
    }
}