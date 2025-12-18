<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/DemandeEntretien.php');
include_once(__DIR__ . '/EntretienController.php');

class DemandeController {

    // Lister toutes les demandes avec informations d'entretien
    public function listDemandes() {
        $sql = "SELECT d.*, e.type, e.date, e.heure 
                FROM demande_entretien d 
                LEFT JOIN entretien e ON d.entretien_id = e.id 
                ORDER BY d.date_demande DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Lister les demandes pour un entretien spécifique
    public function listDemandesByEntretien($entretienId) {
        $sql = "SELECT * FROM demande_entretien 
                WHERE entretien_id = :entretienId 
                ORDER BY date_demande DESC";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['entretienId' => $entretienId]);
            return $query;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Ajouter une demande
    public function addDemande(DemandeEntretien $demande) {
        $db = config::getConnexion();
        
        try {
            // Démarrer une transaction
            $db->beginTransaction();
            
            // Vérifier si l'entretien n'est pas complet
            $sqlCheck = "SELECT places, places_prises FROM entretien WHERE id = :entretienId";
            $queryCheck = $db->prepare($sqlCheck);
            $queryCheck->execute(['entretienId' => $demande->getEntretienId()]);
            $entretien = $queryCheck->fetch();
            
            if (!$entretien || $entretien['places_prises'] >= $entretien['places']) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Cet entretien est complet.'];
            }
            
            // Vérifier doublon (même email, même entretien)
            $sqlDupli = "SELECT COUNT(*) as count FROM demande_entretien 
                         WHERE email = :email AND entretien_id = :entretienId";
            $queryDupli = $db->prepare($sqlDupli);
            $queryDupli->execute([
                'email' => $demande->getEmail(),
                'entretienId' => $demande->getEntretienId()
            ]);
            $dupli = $queryDupli->fetch();
            
            if ($dupli['count'] > 0) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Vous avez déjà réservé cet entretien.'];
            }
            
            // Insérer la demande
            $sql = "INSERT INTO demande_entretien (nom, tel, email, entretien_id, statut) 
                    VALUES (:nom, :tel, :email, :entretienId, :statut)";
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $demande->getNom(),
                'tel' => $demande->getTel(),
                'email' => $demande->getEmail(),
                'entretienId' => $demande->getEntretienId(),
                'statut' => 'Confirmé'
            ]);
            
            // Incrémenter places_prises
            $sqlUpdate = "UPDATE entretien SET places_prises = places_prises + 1 
                          WHERE id = :entretienId";
            $queryUpdate = $db->prepare($sqlUpdate);
            $queryUpdate->execute(['entretienId' => $demande->getEntretienId()]);
            
            // Valider la transaction
            $db->commit();
            return ['success' => true, 'message' => 'Réservation confirmée avec succès !'];
            
        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    // Supprimer une demande
    public function deleteDemande($id) {
        $db = config::getConnexion();
        
        try {
            // Démarrer une transaction
            $db->beginTransaction();
            
            // Récupérer l'entretien_id avant suppression
            $sqlGet = "SELECT entretien_id FROM demande_entretien WHERE id = :id";
            $queryGet = $db->prepare($sqlGet);
            $queryGet->execute(['id' => $id]);
            $demande = $queryGet->fetch();
            
            if ($demande) {
                // Supprimer la demande
                $sql = "DELETE FROM demande_entretien WHERE id = :id";
                $req = $db->prepare($sql);
                $req->execute(['id' => $id]);
                
                // Décrémenter places_prises
                $sqlUpdate = "UPDATE entretien SET places_prises = places_prises - 1 
                              WHERE id = :entretienId AND places_prises > 0";
                $queryUpdate = $db->prepare($sqlUpdate);
                $queryUpdate->execute(['entretienId' => $demande['entretien_id']]);
            }
            
            // Valider la transaction
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Afficher une demande par ID
    public function showDemande($id) {
        $sql = "SELECT d.*, e.type, e.date, e.heure 
                FROM demande_entretien d 
                LEFT JOIN entretien e ON d.entretien_id = e.id 
                WHERE d.id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        try {
            $query->execute(['id' => $id]);
            $demande = $query->fetch();
            return $demande;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Mettre à jour le statut
    public function updateStatut($id, $statut) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare('UPDATE demande_entretien SET statut = :statut WHERE id = :id');
            $query->execute([
                'id' => $id,
                'statut' => $statut
            ]);
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
        }
    }

    // Récupérer les dernières demandes
    public function getRecentDemandes($limit = 5) {
        $sql = "SELECT d.*, e.type, e.date, e.heure 
                FROM demande_entretien d 
                LEFT JOIN entretien e ON d.entretien_id = e.id 
                ORDER BY d.date_demande DESC 
                LIMIT :limit";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            return $query;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>