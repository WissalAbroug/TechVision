<?php
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST, DB_NAME, DB_CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Ne jamais exposer les détails en production
                error_log('[DB Error] ' . $e->getMessage());
                die('<div style="font-family:sans-serif;padding:2rem;color:#dc2626;">
                    <h2>⚠️ Erreur de connexion à la base de données</h2>
                    <p>Veuillez vérifier votre configuration dans <code>config/config.php</code> et vous assurer que MySQL est démarré.</p>
                </div>');
            }
        }
        return self::$instance;
    }
}
