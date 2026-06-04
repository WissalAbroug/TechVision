<?php
// config/Database.php

class Database {
    private static $instance = null;
    private $connection;
    private $host = "localhost";
    private $dbname = "web";
    private $username = "root";
    private $password = "";
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Empêcher le clonage de l'instance
    private function __clone() {}
    
    // Empêcher la désérialisation de l'instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}