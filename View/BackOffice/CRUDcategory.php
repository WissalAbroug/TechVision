
<?php
// CRUDcategory.php - API pour la gestion des catégories

// Activer le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Journaliser pour le débogage
error_log("=== CRUDcategory.php appelé ===");
error_log("Méthode HTTP: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("GET data: " . print_r($_GET, true));

// Vérifier que le fichier est appelé
if (!defined('ROOT_PATH')) {
    // Définir le chemin racine
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
    error_log("ROOT_PATH défini: " . ROOT_PATH);
}

// Inclure les fichiers nécessaires
$modelPath = ROOT_PATH . '/Model/Category.php';
$controllerPath = ROOT_PATH . '/Controller/CategoryController.php';
$configPath = ROOT_PATH . '/config.php';

error_log("Vérification des fichiers:");
error_log("- Category.php: " . (file_exists($modelPath) ? "EXISTE" : "MANQUANT"));
error_log("- CategoryController.php: " . (file_exists($controllerPath) ? "EXISTE" : "MANQUANT"));
error_log("- config.php: " . (file_exists($configPath) ? "EXISTE" : "MANQUANT"));

// Inclure config d'abord
require_once $configPath;

// Puis les autres fichiers
require_once $modelPath;
require_once $controllerPath;

// Configurer les headers pour JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Initialiser le contrôleur
try {
    error_log("Initialisation de CategoryController...");
    $categoryController = new CategoryController();
    error_log("CategoryController initialisé avec succès");
} catch (Exception $e) {
    error_log("ERREUR initialisation: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erreur d\'initialisation: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    exit();
}

// Déterminer l'action
$action = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lire les données POST
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if ($contentType === "application/x-www-form-urlencoded") {
        // Les données sont déjà dans $_POST
        $action = $_POST['action'] ?? '';
    } else {
        // Lire depuis php://input
        $input = file_get_contents('php://input');
        error_log("Input brut: " . $input);
        if (!empty($input)) {
            parse_str($input, $_POST);
            $action = $_POST['action'] ?? '';
        }
    }
}

error_log("Action déterminée: " . $action);

// Si pas d'action, retourner une liste des actions disponibles
if (empty($action)) {
    echo json_encode([
        'success' => false,
        'message' => 'Aucune action spécifiée',
        'available_actions' => ['getCategory', 'getAll', 'create', 'addCategory', 'update', 'delete'],
        'post_data' => $_POST,
        'get_data' => $_GET
    ]);
    exit();
}

// Traiter l'action
switch ($action) {
    case 'getCategory':
        $id = $_GET['id'] ?? 0;
        error_log("getCategory appelé avec ID: " . $id);
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            break;
        }
        
        try {
            $category = $categoryController->getCategoryById($id);
            if ($category) {
                echo json_encode([
                    'success' => true,
                    'category' => $category->toArray()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Catégorie non trouvée']);
            }
        } catch (Exception $e) {
            error_log("Erreur getCategory: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
        break;

    case 'getAll':
        error_log("getAll appelé");
        try {
            $categories = $categoryController->getAllCategories();
            $data = array_map(function($cat) {
                return $cat->toArray();
            }, $categories);
            
            echo json_encode([
                'success' => true,
                'categories' => $data,
                'count' => count($data)
            ]);
        } catch (Exception $e) {
            error_log("Erreur getAll: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
        break;

    //case 'create':
    case 'addCategory': // Ajout de cette action
        error_log("create/addCategory appelé avec données: " . print_r($_POST, true));
        
        try {
            // Valider les données
            if (empty($_POST['nom'])) {
                echo json_encode(['success' => false, 'message' => 'Le nom est obligatoire']);
                break;
            }
            
            // Créer la catégorie
            $category = new Category(
                null,
                trim($_POST['nom']),
                trim($_POST['description'] ?? ''),
                trim($_POST['icone'] ?? null)
            );
            
            error_log("Catégorie créée: " . print_r($category->toArray(), true));
            
            // Valider
            $errors = $category->validate();
            if (!empty($errors)) {
                error_log("Erreurs de validation: " . implode(', ', $errors));
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            // Ajouter
            error_log("Tentative d'ajout dans la base de données...");
            $result = $categoryController->addCategory($category);
            error_log("Résultat addCategory: " . ($result ? "SUCCÈS" : "ÉCHEC"));
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Catégorie créée avec succès',
                    'category' => $category->toArray()
                ]);
            } else {
                // Vérifier s'il y a une erreur de contrainte unique (nom déjà existant)
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erreur lors de la création. Vérifiez que le nom n\'existe pas déjà.'
                ]);
            }
        } catch (Exception $e) {
            error_log("Exception dans create: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        break;

    case 'update':
        error_log("update appelé avec ID: " . ($_POST['id'] ?? '0'));
        
        try {
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
                break;
            }
            
            // Récupérer la catégorie existante
            $category = $categoryController->getCategoryById($id);
            if (!$category) {
                echo json_encode(['success' => false, 'message' => 'Catégorie non trouvée']);
                break;
            }
            
            // Mettre à jour
            $category->setNom(trim($_POST['nom'] ?? ''));
            $category->setDescription(trim($_POST['description'] ?? ''));
            $category->setIcone(trim($_POST['icone'] ?? null));
            
            // Valider
            $errors = $category->validate();
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            // Sauvegarder
            if ($categoryController->updateCategory($category)) {
                echo json_encode(['success' => true, 'message' => 'Catégorie mise à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } catch (Exception $e) {
            error_log("Erreur update: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
        break;

    case 'delete':
        error_log("delete appelé avec ID: " . ($_POST['id'] ?? '0'));
        
        try {
            $id = $_POST['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID manquant']);
                break;
            }
            
            if ($categoryController->deleteCategory($id)) {
                echo json_encode(['success' => true, 'message' => 'Catégorie supprimée']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Impossible de supprimer. Des offres sont peut-être liées.']);
            }
        } catch (Exception $e) {
            error_log("Erreur delete: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        error_log("Action non reconnue: " . $action);
        echo json_encode([
            'success' => false,
            'message' => 'Action non reconnue: ' . $action,
            'available_actions' => ['getCategory', 'getAll', 'create', 'addCategory', 'update', 'delete']
        ]);
        break;
}

error_log("=== Fin CRUDcategory.php ===");
?>
