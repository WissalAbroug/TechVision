<?php
/**
 * Configuration de la base de données
 * Singleton Pattern pour connexion PDO
 */
class config {
    private static $pdo = null;

    /**
     * Obtenir la connexion à la base de données
     * @return PDO
     */
    public static function getConnexion() {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "web";
            
            try {
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password
                );
                
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                
            } catch (Exception $e) {
                die('Erreur de connexion à la base de données: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}