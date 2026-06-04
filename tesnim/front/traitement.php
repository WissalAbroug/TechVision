<?php
/**
 * Point d'entrée pour le traitement du formulaire
 * Routage vers le contrôleur approprié
 */

// Configuration des erreurs (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Headers de sécurité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Autoriser CORS pour le développement (à adapter en production)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Chargement du contrôleur
require_once __DIR__ . '/controllers/DemandeController.php';

try {
    $controller = new DemandeController();
    
    // Routage simple basé sur la méthode HTTP et les paramètres
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? 'create';
    $id = $_GET['id'] ?? null;
    
    switch ($action) {
        case 'create':
            if ($method === 'POST') {
                $controller->create();
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            }
            break;
            
        case 'read':
            if ($method === 'GET' && $id) {
                $controller->read($id);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID requis']);
            }
            break;
            
        case 'readAll':
        case 'list':
            if ($method === 'GET') {
                $controller->readAll();
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            }
            break;
            
        case 'update':
            if (($method === 'POST' || $method === 'PUT')) {
                $controller->update();
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            }
            break;
            
        case 'delete':
            if ($method === 'DELETE' || ($method === 'POST' && $id)) {
                $controller->delete($id);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Action non trouvée']);
            break;
    }
    
} catch (Exception $e) {
    error_log("Erreur traitement.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur serveur interne',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>