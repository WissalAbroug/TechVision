<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Offer.php';

/**
 * OfferController - Gère toutes les opérations CRUD sur les offres
 * Sert d'intermédiaire entre la base de données et les vues
 */

class OfferController {
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
     * Récupérer toutes les offres
     * @return array
     */
    public function getAllOffers() {
        try {
            $query = "SELECT * FROM offre ORDER BY dateCreation DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $offers = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $offers[] = $this->rowToOffer($row);
            }
            
            return $offers;
        } catch (PDOException $e) {
            error_log("Erreur getAllOffers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer une offre par son ID
     * @param int $id
     * @return Offer|null
     */
    public function getOfferById($id) {
        try {
            $query = "SELECT * FROM offre WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $this->rowToOffer($row);
            }
            return null;
        } catch (PDOException $e) {
            error_log("Erreur getOfferById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ajouter une nouvelle offre
     * @param Offer $offer
     * @return bool
     */
    public function addOffer(Offer $offer) {
        $stmt = null;
        try {
            $query = "INSERT INTO offre (titre, description, nomSociete, localisation, salaireMin, salaireMax, 
                      typeContrat, experienceRequise, competences, requirements, nbPlace, dateLimite, 
                      dateCreation, statut) 
                      VALUES (:titre, :description, :nomSociete, :localisation, :salaireMin, :salaireMax, 
                      :typeContrat, :experienceRequise, :competences, :requirements, :nbPlace, :dateLimite, 
                      :dateCreation, :statut)";
            
            $stmt = $this->conn->prepare($query);
            
            // Récupérer les valeurs
            $titre = $offer->getTitre();
            $description = $offer->getDescription();
            $nomSociete = $offer->getNomSociete();
            $localisation = $offer->getLocalisation();
            $salaireMin = $offer->getSalaireMin();
            $salaireMax = $offer->getSalaireMax();
            $typeContrat = $offer->getTypeContrat();
            $experienceRequise = $offer->getExperienceRequise();
            $nbPlace = $offer->getNbPlace();
            $dateLimite = $offer->getDateLimite();
            $dateCreation = $offer->getDateCreation();
            $statut = $offer->getStatut();
            
            // Convertir les tableaux en JSON
            $competences = json_encode($offer->getCompetences(), JSON_UNESCAPED_UNICODE);
            $requirements = json_encode($offer->getRequirements(), JSON_UNESCAPED_UNICODE);
            
            // Bind des paramètres
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':nomSociete', $nomSociete);
            $stmt->bindParam(':localisation', $localisation);
            $stmt->bindParam(':salaireMin', $salaireMin, PDO::PARAM_INT);
            $stmt->bindParam(':salaireMax', $salaireMax, PDO::PARAM_INT);
            $stmt->bindParam(':typeContrat', $typeContrat);
            $stmt->bindParam(':experienceRequise', $experienceRequise);
            $stmt->bindParam(':competences', $competences);
            $stmt->bindParam(':requirements', $requirements);
            $stmt->bindParam(':nbPlace', $nbPlace, PDO::PARAM_INT);
            $stmt->bindParam(':dateLimite', $dateLimite);
            $stmt->bindParam(':dateCreation', $dateCreation);
            $stmt->bindParam(':statut', $statut);
            
            $result = $stmt->execute();
            
            if ($result) {
                error_log("Offre ajoutée avec succès - ID: " . $this->conn->lastInsertId());
            } else {
                error_log("Échec de l'ajout de l'offre");
                if ($stmt !== null) {
                    error_log("SQL Error Info: " . print_r($stmt->errorInfo(), true));
                }
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur addOffer: " . $e->getMessage());
            if ($stmt !== null) {
                error_log("SQL Error Info: " . print_r($stmt->errorInfo(), true));
            }
            return false;
        }
    }

    /**
     * Mettre à jour une offre existante
     * @param Offer $offer
     * @return bool
     */
    public function updateOffer(Offer $offer) {
        try {
            $query = "UPDATE offre SET 
                      titre = :titre, 
                      description = :description, 
                      nomSociete = :nomSociete, 
                      localisation = :localisation, 
                      salaireMin = :salaireMin, 
                      salaireMax = :salaireMax, 
                      typeContrat = :typeContrat, 
                      experienceRequise = :experienceRequise, 
                      competences = :competences, 
                      requirements = :requirements, 
                      nbPlace = :nbPlace, 
                      dateLimite = :dateLimite, 
                      statut = :statut 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Récupérer les valeurs
            $id = $offer->getId();
            $titre = $offer->getTitre();
            $description = $offer->getDescription();
            $nomSociete = $offer->getNomSociete();
            $localisation = $offer->getLocalisation();
            $salaireMin = $offer->getSalaireMin();
            $salaireMax = $offer->getSalaireMax();
            $typeContrat = $offer->getTypeContrat();
            $experienceRequise = $offer->getExperienceRequise();
            $nbPlace = $offer->getNbPlace();
            $dateLimite = $offer->getDateLimite();
            $statut = $offer->getStatut();
            
            // Convertir les tableaux en JSON
            $competences = json_encode($offer->getCompetences(), JSON_UNESCAPED_UNICODE);
            $requirements = json_encode($offer->getRequirements(), JSON_UNESCAPED_UNICODE);
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':nomSociete', $nomSociete);
            $stmt->bindParam(':localisation', $localisation);
            $stmt->bindParam(':salaireMin', $salaireMin, PDO::PARAM_INT);
            $stmt->bindParam(':salaireMax', $salaireMax, PDO::PARAM_INT);
            $stmt->bindParam(':typeContrat', $typeContrat);
            $stmt->bindParam(':experienceRequise', $experienceRequise);
            $stmt->bindParam(':competences', $competences);
            $stmt->bindParam(':requirements', $requirements);
            $stmt->bindParam(':nbPlace', $nbPlace, PDO::PARAM_INT);
            $stmt->bindParam(':dateLimite', $dateLimite);
            $stmt->bindParam(':statut', $statut);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur updateOffer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer une offre
     * @param int $id
     * @return bool
     */
    public function deleteOffer($id) {
        try {
            $query = "DELETE FROM offre WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur deleteOffer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Rechercher des offres avec filtres
     * @param array $filters
     * @return array
     */
    public function searchOffers($filters) {
        try {
            $query = "SELECT * FROM offre WHERE 1=1";
            $params = [];
            
            if (!empty($filters['metier'])) {
                $query .= " AND titre LIKE :metier";
                $params[':metier'] = '%' . $filters['metier'] . '%';
            }
            
            if (!empty($filters['localisation'])) {
                $query .= " AND localisation LIKE :localisation";
                $params[':localisation'] = '%' . $filters['localisation'] . '%';
            }
            
            if (!empty($filters['competence'])) {
                $query .= " AND competences LIKE :competence";
                $params[':competence'] = '%' . $filters['competence'] . '%';
            }
            
            if (!empty($filters['typeContrat'])) {
                $query .= " AND typeContrat = :typeContrat";
                $params[':typeContrat'] = $filters['typeContrat'];
            }
            
            $query .= " ORDER BY dateCreation DESC";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            $offers = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $offers[] = $this->rowToOffer($row);
            }
            
            return $offers;
        } catch (PDOException $e) {
            error_log("Erreur searchOffers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Archiver une offre (changer le statut)
     * @param int $id
     * @return bool
     */
    public function archiveOffer($id) {
        try {
            $query = "UPDATE offre SET statut = 'closed' WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur archiveOffer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Convertir une ligne de BDD en objet Offer
     * @param array $row
     * @return Offer
     */
    private function rowToOffer($row) {
        // Décoder les JSON en tableaux
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