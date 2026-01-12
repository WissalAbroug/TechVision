<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Category.php';

/**
 * CategoryController - Gère toutes les opérations CRUD sur les catégories
 * Sert d'intermédiaire entre la base de données et les vues
 */

class CategoryController {
    private $db;
    private $conn;

    /**
     * Constructeur - Initialise la connexion à la base de données
     */
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Récupérer toutes les catégories avec le nombre d'offres associées
     * @return array
     */
    public function getAllCategories() {
        try {
            $query = "SELECT c.*, COUNT(o.id) as nbOffres 
                      FROM category c 
                      LEFT JOIN offre o ON c.id = o.category_id AND o.statut = 'active'
                      GROUP BY c.id 
                      ORDER BY c.nom ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $categories = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $categories[] = $this->rowToCategory($row);
            }
            
            return $categories;
        } catch (PDOException $e) {
            error_log("Erreur getAllCategories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer une catégorie par son ID avec le nombre d'offres
     * @param int $id
     * @return Category|null
     */
    public function getCategoryById($id) {
        try {
            $query = "SELECT c.*, COUNT(o.id) as nbOffres 
                      FROM category c 
                      LEFT JOIN offre o ON c.id = o.category_id AND o.statut = 'active'
                      WHERE c.id = :id 
                      GROUP BY c.id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->rowToCategory($row);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erreur getCategoryById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ajouter une nouvelle catégorie
     * @param Category $category
     * @return bool
     */
    // Dans CategoryController.php, vérifiez la méthode addCategory :
public function addCategory(Category $category) {
    try {
        // Valider les données
        $errors = $category->validate();
        if (!empty($errors)) {
            error_log("Erreurs de validation: " . implode(", ", $errors));
            return false;
        }

        $query = "INSERT INTO category (nom, description, icone, date_creation, date_modification) 
                  VALUES (:nom, :description, :icone, NOW(), NOW())";
        
        error_log("Requête SQL: " . $query);
        
        $stmt = $this->conn->prepare($query);
        
        $nom = $category->getNom();
        $description = $category->getDescription();
        $icone = $category->getIcone();
        
        error_log("Paramètres: nom=$nom, description=$description, icone=$icone");
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':icone', $icone);
        
        $result = $stmt->execute();
        
        if ($result) {
            error_log("Catégorie ajoutée avec succès - ID: " . $this->conn->lastInsertId());
            return true;
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("Échec de l'ajout de la catégorie: " . print_r($errorInfo, true));
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur addCategory PDO: " . $e->getMessage());
        error_log("Code erreur: " . $e->getCode());
        return false;
    }
}

    /**
     * Mettre à jour une catégorie existante
     * @param Category $category
     * @return bool
     */
    public function updateCategory(Category $category) {
        try {
            // Valider les données
            $errors = $category->validate();
            if (!empty($errors)) {
                error_log("Erreurs de validation: " . implode(", ", $errors));
                return false;
            }

            $query = "UPDATE category SET 
                      nom = :nom, 
                      description = :description, 
                      icone = :icone,
                      date_modification = NOW()
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $id = $category->getId();
            $nom = $category->getNom();
            $description = $category->getDescription();
            $icone = $category->getIcone();
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':icone', $icone);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur updateCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer une catégorie (avec vérification des offres liées)
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id) {
        try {
            // Vérifier s'il y a des offres liées
            $query = "SELECT COUNT(*) as count FROM offre WHERE category_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row['count'] > 0) {
                error_log("Impossible de supprimer: " . $row['count'] . " offres liées");
                return false;
            }

            // Supprimer la catégorie
            $query = "DELETE FROM category WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur deleteCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les offres d'une catégorie spécifique
     * @param int $categoryId
     * @return array
     */
    public function getOffersByCategory($categoryId) {
        try {
            require_once __DIR__ . '/../Model/Offer.php';
            
            $query = "SELECT * FROM offre WHERE category_id = :categoryId AND statut = 'active' ORDER BY dateCreation DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            
            $offers = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $offers[] = $this->rowToOffer($row);
            }
            
            return $offers;
        } catch (PDOException $e) {
            error_log("Erreur getOffersByCategory: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir la catégorie la plus consultée par un client
     * @param int $userId
     * @return Category|null
     */
    public function getMostViewedCategory($userId) {
        // Cette fonctionnalité nécessiterait une table de tracking des consultations
        // Pour l'instant, retourner la catégorie avec le plus d'offres
        try {
            $query = "SELECT c.*, COUNT(o.id) as nbOffres 
                      FROM category c 
                      LEFT JOIN offre o ON c.id = o.category_id AND o.statut = 'active'
                      GROUP BY c.id 
                      ORDER BY nbOffres DESC 
                      LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->rowToCategory($row);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erreur getMostViewedCategory: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Convertir une ligne de BDD en objet Category
     * @param array $row
     * @return Category
     */
    private function rowToCategory($row) {
        return new Category(
            $row['id'],
            $row['nom'] ?? '',
            $row['description'] ?? '',
            $row['icone'] ?? null,
            $row['date_creation'] ?? null,
            $row['date_modification'] ?? null,
            (int)($row['nbOffres'] ?? 0)
        );
    }

    /**
     * Convertir une ligne de BDD en objet Offer
     * @param array $row
     * @return Offer
     */
    private function rowToOffer($row) {
        $competences = [];
        if (!empty($row['competences'])) {
            $decoded = json_decode($row['competences'], true);
            $competences = is_array($decoded) ? $decoded : [];
        }
        
        $requirements = [];
        if (!empty($row['requirements'])) {
            $decoded = json_decode($row['requirements'], true);
            $requirements = is_array($decoded) ? $decoded : [];
        }
        
        return new Offer(
            $row['id'],
            $row['titre'] ?? '',
            $row['description'] ?? '',
            $row['nomSociete'] ?? '',
            $row['localisation'] ?? '',
            (int)($row['salaireMin'] ?? 0),
            (int)($row['salaireMax'] ?? 0),
            $row['typeContrat'] ?? 'CDI',
            $row['experienceRequise'] ?? '',
            $competences,
            $requirements,
            (int)($row['nbPlace'] ?? 1),
            $row['dateLimite'] ?? null,
            $row['dateCreation'] ?? null,
            $row['statut'] ?? 'active'
        );
    }
}
?>