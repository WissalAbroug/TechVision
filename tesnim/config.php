<?php
// Configuration de la base de données
class config
{   
    private static $pdo = null;
    
    // Base URL pour les assets
    public static $base_url = '';
    
    public static function getConnexion()
    {
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
            } catch (Exception $e) {
                die('Erreur de connexion: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Définir le chemin de base pour les assets
define('BASE_URL', '/'); // Changez selon votre configuration
define('ASSETS_PATH', BASE_URL . 'assets/');

// Test de connexion
config::getConnexion();
?>