<?php
// View/FrontOffice/CRUDcategory.php
require_once __DIR__ . '/../../Controller/CategoryController.php';
require_once __DIR__ . '/../../Controller/OfferController.php';

header('Content-Type: application/json');

$categoryController = new CategoryController();
$offerController = new OfferController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getCategory':
        $id = $_GET['id'] ?? 0;
        $category = $categoryController->getCategoryById($id);
        
        if ($category) {
            $offers = $categoryController->getOffersByCategory($id);
            $offersData = array_map(function($offer) {
                return $offer->toArray();
            }, $offers);
            
            echo json_encode([
                'success' => true,
                'category' => $category->toArray(),
                'offers' => $offersData
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Catégorie non trouvée']);
        }
        break;

    case 'getAll':
        $categories = $categoryController->getAllCategories();
        $data = array_map(function($cat) {
            return $cat->toArray();
        }, $categories);
        
        echo json_encode([
            'success' => true,
            'categories' => $data
        ]);
        break;

    case 'getOffersByCategory':
        $categoryId = $_GET['category_id'] ?? 0;
        $offers = $categoryController->getOffersByCategory($categoryId);
        $data = array_map(function($offer) {
            return $offer->toArray();
        }, $offers);
        
        echo json_encode([
            'success' => true,
            'offers' => $data
        ]);
        break;

    case 'getMostViewed':
        $userId = $_GET['user_id'] ?? 0;
        $category = $categoryController->getMostViewedCategory($userId);
        
        if ($category) {
            echo json_encode([
                'success' => true,
                'category' => $category->toArray()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aucune catégorie trouvée']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        break;
}
?>