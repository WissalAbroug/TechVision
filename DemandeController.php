<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../model/DemandeFormation.php');

/**
 * Contrôleur pour la gestion des demandes d'inscription
 */
class DemandeController
{
    /**
     * Liste toutes les demandes avec informations de formation (JOINTURE)
     */
    public function listDemandes()
    {
        $sql = "SELECT 
                    d.*,
                    f.nom as formation_nom,
                    f.niveau as formation_niveau,
                    f.date_formation as formation_date
                FROM demande_formation d
                INNER JOIN formation f ON d.formation_id = f.id
                ORDER BY d.date_inscription DESC";

        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Ajoute une nouvelle demande
     */
    public function addDemande(DemandeFormation $demande)
    {
        // Vérifier d'abord si l'inscription existe déjà
        if ($this->checkDoublon($demande->getTel(), $demande->getFormationId())) {
            return false; // Doublon détecté
        }

        $sql = "INSERT INTO demande_formation 
                (nom, email, tel, formation_id, date_inscription, statut, numero_demande, niveau, mode_paiement) 
                VALUES 
                (:nom, :email, :tel, :formation_id, :date_inscription, :statut, :numero_demande, :niveau, :mode_paiement)";

        $db = config::getConnexion();
        try {
            $db->beginTransaction();

            // Insérer la demande
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $demande->getNom(),
                'email' => $demande->getEmail(),
                'tel' => $demande->getTel(),
                'formation_id' => $demande->getFormationId(),
                'date_inscription' => $demande->getDateInscription()->format('Y-m-d H:i:s'),
                'statut' => $demande->getStatut(),
                'numero_demande' => $demande->getNumeroDemande(),
                'niveau' => $demande->getNiveau(),
                'mode_paiement' => $demande->getModePaiement()
            ]);

            // Incrémenter les places prises
            $sqlUpdate = "UPDATE formation 
                         SET places_prises = places_prises + 1 
                         WHERE id = :formation_id AND places_prises < places_max";
            $queryUpdate = $db->prepare($sqlUpdate);
            $queryUpdate->execute(['formation_id' => $demande->getFormationId()]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Vérifie si une inscription existe déjà
     */
    private function checkDoublon($tel, $formationId)
    {
        $sql = "SELECT COUNT(*) as count FROM demande_formation 
                WHERE tel = :tel AND formation_id = :formation_id";

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['tel' => $tel, 'formation_id' => $formationId]);
        $result = $query->fetch();

        return $result['count'] > 0;
    }

    /**
     * Supprime une demande
     */
    public function deleteDemande($id)
    {
        $sql = "DELETE FROM demande_formation WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour le statut d'une demande
     */
    public function updateStatut($id, $statut)
    {
        $sql = "UPDATE demande_formation SET statut = :statut WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute(['statut' => $statut, 'id' => $id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Récupère une demande par son ID
     */
    public function showDemande($id)
    {
        $sql = "SELECT * FROM demande_formation WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);

        try {
            $query->execute();
            $demande = $query->fetch();
            return $demande;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Recherche des demandes par nom, email ou téléphone
     */
    public function searchDemandes($search)
    {
        $sql = "SELECT 
                    d.*,
                    f.nom as formation_nom,
                    f.niveau as formation_niveau,
                    f.date_formation as formation_date
                FROM demande_formation d
                INNER JOIN formation f ON d.formation_id = f.id
                WHERE d.nom LIKE :search 
                   OR d.email LIKE :search 
                   OR d.tel LIKE :search
                ORDER BY d.date_inscription DESC";

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $searchParam = '%' . $search . '%';
        $query->bindValue(':search', $searchParam);

        try {
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Compte le nombre total de demandes
     */
    public function countDemandes()
    {
        $sql = "SELECT COUNT(*) as total FROM demande_formation";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql)->fetch();
            return $result['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Récupère les inscriptions récentes
     */
    public function getRecentDemandes($limit = 5)
    {
        $sql = "SELECT 
                    d.*,
                    f.nom as formation_nom
                FROM demande_formation d
                INNER JOIN formation f ON d.formation_id = f.id
                ORDER BY d.date_inscription DESC
                LIMIT :limit";

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);

        try {
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Génère un numéro de demande unique
     */
    private function genererNumeroDemande(): string
    {
        return 'DEM-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }

    /**
     * Valide l'adresse email
     */
    public function validateEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valide le numéro de téléphone
     */
    public function validateTel($tel): bool
    {
        // Format: au moins 8 chiffres
        return preg_match('/^[0-9]{8,}$/', preg_replace('/[^0-9]/', '', $tel));
    }

    /**
     * Génère un objet DemandeFormation à partir d'un tableau de données
     */
    public function createDemandeFromArray($data): DemandeFormation
    {
        $numeroDemande = $this->genererNumeroDemande();

        return new DemandeFormation(
            $data['id'] ?? null,
            $data['nom'] ?? null,
            $data['email'] ?? null,
            $data['tel'] ?? null,
            $data['formation_id'] ?? null,
            isset($data['date_inscription']) ? new DateTime($data['date_inscription']) : new DateTime(),
            $data['statut'] ?? 'En attente',
            $numeroDemande,
            $data['niveau'] ?? null,
            $data['mode_paiement'] ?? 'Non spécifié'
        );
    }
}
