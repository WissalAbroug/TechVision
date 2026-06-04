<?php
/**
 * Point d'entrée principal de l'application MVC
 * Gère le routage et instancie les contrôleurs
 */

// Configuration des erreurs (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure le contrôleur
require_once __DIR__ . '/../controllers/DemandeEntretienController.php';

// Instancier le contrôleur
$controller = new DemandeEntretienController();

// Router simple
$action = $_GET['action'] ?? 'index';

try {
    switch ($action) {
        case 'index':
            // Page principale
            $controller->index();
            break;
            
        case 'create_page':
            // Page de création
            $controller->createPage();
            break;
            
        case 'api':
            // API JSON
            $controller->api();
            break;
            
        case 'edit':
            // Page d'édition
            $controller->editPage();
            break;

        case 'create':
            // Créer un enregistrement
            $controller->create();
            break;

        case 'update':
            // Mettre à jour un enregistrement
            $controller->update();
            break;

        case 'delete':
            // Supprimer un enregistrement
            $controller->delete();
            break;
            
        default:
            // Action non trouvée
            http_response_code(404);
            echo "Page non trouvée";
            break;
    }
} catch (Exception $e) {
    // Gestion globale des erreurs
    error_log("Erreur application: " . $e->getMessage());
    http_response_code(500);
    echo "Une erreur est survenue. Veuillez réessayer plus tard.";
}