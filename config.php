<?php
/**
 * Configuration de la connexion à la base de données
 * Utilise PDO pour une connexion sécurisée
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Database {
    private $host = "localhost";
    private $db_name = "web";
    private $username = "root";
    private $password = "";
    private $conn;

    /**
     * Obtenir la connexion PDO à la base de données
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            
            // Définir le mode d'erreur PDO sur Exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Désactiver l'émulation des requêtes préparées
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            // Définir le mode de récupération par défaut
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez vérifier votre configuration.");
        }

        return $this->conn;
    }
}
?>