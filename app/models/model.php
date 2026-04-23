<?php
abstract class Model {

    protected $db;

    public function __construct() {
        $host = $_ENV['MYSQL_HOST']     ?? $_SERVER['MYSQL_HOST']     ?? 'mysql';
        $user = $_ENV['MYSQL_USER']     ?? $_SERVER['MYSQL_USER']     ?? 'root';
        $pass = $_ENV['MYSQL_PASSWORD'] ?? $_SERVER['MYSQL_PASSWORD'] ?? 'root';
        $db   = $_ENV['MYSQL_DATABASE'] ?? $_SERVER['MYSQL_DATABASE'] ?? 'tiendaropa';

        try {
            $this->db = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error de conexión a la base de datos']);
            exit;
        }
    }
}