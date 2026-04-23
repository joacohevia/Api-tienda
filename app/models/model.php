<?php
abstract class Model {

    protected $db;

    public function __construct() {
        $host = $_ENV['MYSQL_HOST']     ?? $_SERVER['MYSQL_HOST']     ?? 'mysql';
        $user = $_ENV['MYSQL_USER']     ?? $_SERVER['MYSQL_USER']     ?? 'root';
        $pass = $_ENV['MYSQL_PASSWORD'] ?? $_SERVER['MYSQL_PASSWORD'] ?? 'root';
        $db   = $_ENV['MYSQL_DATABASE'] ?? $_SERVER['MYSQL_DATABASE'] ?? 'tiendaropa';

        $this->db = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    }
}