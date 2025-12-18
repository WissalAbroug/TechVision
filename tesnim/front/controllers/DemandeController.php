<?php
require_once __DIR__ . '/../models/DemandeEntretien.php';

/**
 * Contrôleur DemandeController
 * Gère la logique métier pour les demandes d'entretien
 */
class DemandeController {
    private $model;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->model = new DemandeEntretien();
    }
    
    /**
     * Crée une nouvelle demande depuis un formulaire POST
     * @return array Réponse JSON
     */
    public function create() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // Vérification de la méthode
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->jsonResponse(false, 'Méthode non autorisée', 405);
            }
            
            // Validation des données
            $errors = $this->validateData($_POST);
            
            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Erreurs de validation', 400, $errors);
            }
            
            // Vérification email unique (optionnel)
            if ($this->model->emailExists($_POST['email'])) {
                return $this->jsonResponse(false, 'Cet email existe déjà', 409);
            }
            
            // Assignation des valeurs
            $this->model->nom = $_POST['nom'];
            $this->model->email = $_POST['email'];
            $this->model->telephone = $_POST['telephone'];
            $this->model->statut = 'nouveau';
            
            // Création
            $id = $this->model->create();
            
            if ($id) {
                return $this->jsonResponse(true, 'Demande créée avec succès', 201, ['id' => $id]);
            }
            
            return $this->jsonResponse(false, 'Erreur lors de la création', 500);
            
        } catch (Exception $e) {
            error_log("Erreur controller create: " . $e->getMessage());
            return $this->jsonResponse(false, 'Erreur serveur', 500);
        }
    }
    
    /**
     * Récupère une demande par ID
     * @param int $id
     * @return array
     */
    public function read($id) {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                return $this->jsonResponse(false, 'ID invalide', 400);
            }
            
            $demande = $this->model->readOne($id);
            
            if ($demande) {
                return $this->jsonResponse(true, 'Demande trouvée', 200, ['demande' => $demande]);
            }
            
            return $this->jsonResponse(false, 'Demande non trouvée', 404);
            
        } catch (Exception $e) {
            error_log("Erreur controller read: " . $e->getMessage());
            return $this->jsonResponse(false, 'Erreur serveur', 500);
        }
    }
    
    /**
     * Récupère toutes les demandes
     * @return array
     */
    public function readAll() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $statut = $_GET['statut'] ?? null;
            $demandes = $this->model->readAll($statut);
            
            return $this->jsonResponse(true, 'Demandes récupérées', 200, [
                'count' => count($demandes),
                'demandes' => $demandes
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur controller readAll: " . $e->getMessage());
            return $this->jsonResponse(false, 'Erreur serveur', 500);
        }
    }
    
    /**
     * Met à jour une demande
     * @return array
     */
    public function update() {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
                return $this->jsonResponse(false, 'Méthode non autorisée', 405);
            }
            
            $data = $_SERVER['REQUEST_METHOD'] === 'PUT' ? json_decode(file_get_contents('php://input'), true) : $_POST;
            
            if (!isset($data['id']) || !filter_var($data['id'], FILTER_VALIDATE_INT)) {
                return $this->jsonResponse(false, 'ID invalide', 400);
            }
            
            $errors = $this->validateData($data);
            
            if (!empty($errors)) {
                return $this->jsonResponse(false, 'Erreurs de validation', 400, $errors);
            }
            
            $this->model->id = $data['id'];
            $this->model->nom = $data['nom'];
            $this->model->email = $data['email'];
            $this->model->telephone = $data['telephone'];
            $this->model->statut = $data['statut'] ?? 'nouveau';
            
            if ($this->model->update()) {
                return $this->jsonResponse(true, 'Demande mise à jour', 200);
            }
            
            return $this->jsonResponse(false, 'Erreur lors de la mise à jour', 500);
            
        } catch (Exception $e) {
            error_log("Erreur controller update: " . $e->getMessage());
            return $this->jsonResponse(false, 'Erreur serveur', 500);
        }
    }
    
    /**
     * Supprime une demande
     * @param int $id
     * @return array
     */
    public function delete($id) {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                return $this->jsonResponse(false, 'ID invalide', 400);
            }
            
            $this->model->id = $id;
            
            if ($this->model->delete()) {
                return $this->jsonResponse(true, 'Demande supprimée', 200);
            }
            
            return $this->jsonResponse(false, 'Erreur lors de la suppression', 500);
            
        } catch (Exception $e) {
            error_log("Erreur controller delete: " . $e->getMessage());
            return $this->jsonResponse(false, 'Erreur serveur', 500);
        }
    }
    
    /**
     * Valide les données du formulaire
     * @param array $data
     * @return array Tableau des erreurs
     */
    private function validateData($data) {
        $errors = [];
        
        // Validation nom
        if (empty($data['nom']) || strlen(trim($data['nom'])) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères';
        }
        
        // Validation email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        }
        
        // Validation téléphone
        if (empty($data['telephone']) || !preg_match('/^[0-9]{8,15}$/', $data['telephone'])) {
            $errors['telephone'] = 'Le téléphone doit contenir entre 8 et 15 chiffres';
        }
        
        // Validation statut (si fourni)
        if (isset($data['statut'])) {
            $statutsValides = ['nouveau', 'en_cours', 'traite', 'archive'];
            if (!in_array($data['statut'], $statutsValides)) {
                $errors['statut'] = 'Statut invalide';
            }
        }
        
        return $errors;
    }
    
    /**
     * Retourne une réponse JSON formatée
     * @param bool $success
     * @param string $message
     * @param int $code
     * @param array $data
     * @return array
     */
    private function jsonResponse($success, $message, $code = 200, $data = []) {
        http_response_code($code);
        
        $response = [
            'success' => $success,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($data)) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
?>