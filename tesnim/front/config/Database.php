<?php
/**
 * Classe Database - Gestion de la connexion PDO
 * Pattern Singleton pour une seule instance
 */
class Database {
    private static $instance = null;
    private $connection;
    
    // Configuration de la base de données
    private $host = 'localhost';
    private $dbname = 'web';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    /**
     * Constructeur privé (Singleton)
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        }
    }
    
    /**
     * Récupère l'instance unique de Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Retourne la connexion PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Empêche le clonage de l'instance
     */
    private function __clone() {}
    
    /**
     * Empêche la désérialisation
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>