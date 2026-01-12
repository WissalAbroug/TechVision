<?php
// View/FrontOffice/CRUD.php
require_once __DIR__ . '/../../Controller/OfferController.php';

header('Content-Type: application/json');

// Log pour déboguer
error_log("CRUD.php appelé avec action: " . ($_GET['action'] ?? $_POST['action'] ?? 'inconnue'));

$offerController = new OfferController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getOffer':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        error_log("Récupération offre ID: $id");
        
        $offer = $offerController->getOfferById($id);
        
        if ($offer) {
            echo json_encode([
                'success' => true,
                'offer' => $offer->toArray()
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Offre non trouvée',
                'id_received' => $id
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'apply':
        $offerId = $_POST['offer_id'] ?? 0;
        error_log("Candidature pour offre ID: $offerId");
        
        // Simuler une candidature
        echo json_encode([
            'success' => true, 
            'message' => 'Candidature envoyée'
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'favorite':
        $offerId = $_POST['offer_id'] ?? 0;
        $action = $_POST['fav_action'] ?? 'add';
        error_log("Favori pour offre ID: $offerId, action: $action");
        
        // Simuler l'ajout aux favoris
        echo json_encode([
            'success' => true, 
            'message' => 'Favori mis à jour'
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'getFiltered':
        $filters = [
            'title' => $_POST['title'] ?? '',
            'location' => $_POST['location'] ?? '',
            'skills' => $_POST['skills'] ?? ''
        ];
        
        error_log("Filtres appliqués: " . print_r($filters, true));
        
        $offers = $offerController->getOffersForFront($filters);
        $data = array_map(function($offer) {
            return $offer->toArray();
        }, $offers);
        
        echo json_encode([
            'success' => true, 
            'offers' => $data,
            'count' => count($data)
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        echo json_encode([
            'success' => false, 
            'message' => 'Action non reconnue',
            'action_received' => $action
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>