<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Modèle DemandeEntretien
 * Gère les opérations CRUD sur la table demande_entretien
 */
class DemandeEntretien {
    private $db;
    private $table = 'demende_entretien';
    
    // Propriétés
    public $id;
    public $nom;
    public $email;
    public $telephone;
    public $statut;
    public $date_creation;
    public $date_modification;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Crée une nouvelle demande
     * @return int|false ID de la demande créée ou false
     */
    public function create() {
        $query = "INSERT INTO {$this->table} (nom, email, telephone, statut) 
                  VALUES (:nom, :email, :telephone, :statut)";
        
        try {
            $stmt = $this->db->prepare($query);
            
            // Nettoyage des données
            $this->nom = htmlspecialchars(strip_tags($this->nom));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->telephone = htmlspecialchars(strip_tags($this->telephone));
            $this->statut = $this->statut ?? 'nouveau';
            
            // Liaison des paramètres
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':telephone', $this->telephone);
            $stmt->bindParam(':statut', $this->statut);
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Erreur création demande: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lit une demande par ID
     * @param int $id
     * @return object|false
     */
    public function readOne($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_OBJ);
            
        } catch (PDOException $e) {
            error_log("Erreur lecture demande: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lit toutes les demandes
     * @param string $statut Filtrer par statut (optionnel)
     * @return array
     */
    public function readAll($statut = null) {
        $query = "SELECT * FROM {$this->table}";
        
        if ($statut) {
            $query .= " WHERE statut = :statut";
        }
        
        $query .= " ORDER BY date_creation DESC";
        
        try {
            $stmt = $this->db->prepare($query);
            
            if ($statut) {
                $stmt->bindParam(':statut', $statut);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
            
        } catch (PDOException $e) {
            error_log("Erreur lecture demandes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Met à jour une demande
     * @return bool
     */
    public function update() {
        $query = "UPDATE {$this->table} 
                  SET nom = :nom, 
                      email = :email, 
                      telephone = :telephone, 
                      statut = :statut 
                  WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($query);
            
            // Nettoyage
            $this->nom = htmlspecialchars(strip_tags($this->nom));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->telephone = htmlspecialchars(strip_tags($this->telephone));
            $this->statut = htmlspecialchars(strip_tags($this->statut));
            $this->id = htmlspecialchars(strip_tags($this->id));
            
            // Liaison
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':telephone', $this->telephone);
            $stmt->bindParam(':statut', $this->statut);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erreur mise à jour demande: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime une demande
     * @return bool
     */
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($query);
            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Erreur suppression demande: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie si un email existe déjà
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        $query = "SELECT id FROM {$this->table} WHERE email = :email LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Erreur vérification email: " . $e->getMessage());
            return false;
        }
    }
}
?>