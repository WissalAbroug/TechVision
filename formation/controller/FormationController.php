<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../model/Formation.php');

/**
 * Contrôleur pour la gestion des formations
 */
class FormationController
{
    /**
     * Liste toutes les formations
     */
    public function listFormations()
    {
        $sql = "SELECT * FROM formation ORDER BY date_formation ASC";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Liste les formations disponibles (non complètes et futures)
     */
    public function listFormationsDisponibles()
    {
        $sql = "SELECT * FROM formation 
                WHERE date_formation >= CURDATE() 
                AND places_prises < places_max 
                AND statut = 'Active'
                ORDER BY date_formation ASC";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Ajoute une nouvelle formation
     */
    public function addFormation(Formation $formation)
    {
        $sql = "INSERT INTO formation 
                (nom, date_formation, niveau, places_max, places_prises, prix, description, statut) 
                VALUES 
                (:nom, :date_formation, :niveau, :places_max, :places_prises, :prix, :description, :statut)";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $formation->getNom(),
                'date_formation' => $formation->getDateFormation() ? $formation->getDateFormation()->format('Y-m-d') : null,
                'niveau' => $formation->getNiveau(),
                'places_max' => $formation->getPlacesMax(),
                'places_prises' => $formation->getPlacesPrises(),
                'prix' => $formation->getPrix(),
                'description' => $formation->getDescription(),
                'statut' => $formation->getStatut()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Supprime une formation
     */
    public function deleteFormation($id)
    {
        $sql = "DELETE FROM formation WHERE id = :id";
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
     * Met à jour une formation
     */
    public function updateFormation(Formation $formation, $id)
    {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE formation SET 
                    nom = :nom,
                    date_formation = :date_formation,
                    niveau = :niveau,
                    places_max = :places_max,
                    prix = :prix,
                    description = :description,
                    statut = :statut
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'nom' => $formation->getNom(),
                'date_formation' => $formation->getDateFormation() ? $formation->getDateFormation()->format('Y-m-d') : null,
                'niveau' => $formation->getNiveau(),
                'places_max' => $formation->getPlacesMax(),
                'prix' => $formation->getPrix(),
                'description' => $formation->getDescription(),
                'statut' => $formation->getStatut()
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
            return false;
        }
    }
    public function updateStatut($id, $statut)
    {
        $sql = "UPDATE formation SET statut = :statut WHERE id = :id";
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
     * Récupère une formation par son ID
     */
    public function showFormation($id)
    {
        $sql = "SELECT * FROM formation WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);

        try {
            $query->execute();
            $formation = $query->fetch();
            return $formation;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Incrémente le nombre de places prises
     */
    public function incrementerPlaces($formationId)
    {
        $sql = "UPDATE formation 
                SET places_prises = places_prises + 1 
                WHERE id = :id AND places_prises < places_max";

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $formationId);

        try {
            $query->execute();
            return $query->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtient les statistiques des formations
     */
    public function getStatistiques()
    {
        $sql = "SELECT 
                    COUNT(*) as total_formations,
                    SUM(places_prises) as total_inscriptions,
                    SUM(places_max - places_prises) as places_disponibles
                FROM formation 
                WHERE statut = 'Active'";

        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Calcule les places restantes pour une formation
     */
    public function getPlacesRestantes($formation): int
    {
        return $formation['places_max'] - $formation['places_prises'];
    }

    /**
     * Vérifie si une formation est complète
     */
    public function isComplete($formation): bool
    {
        return $formation['places_prises'] >= $formation['places_max'];
    }

    /**
     * Vérifie si la date de formation est passée
     */
    public function isPassee($formation): bool
    {
        if (empty($formation['date_formation'])) {
            return false;
        }
        $formationDate = new DateTime($formation['date_formation']);
        return $formationDate < new DateTime();
    }

    /**
     * Génère un objet Formation à partir d'un tableau de données
     */
    public function createFormationFromArray($data): Formation
    {
        return new Formation(
            $data['id'] ?? null,
            $data['nom'] ?? null,
            isset($data['date_formation']) ? new DateTime($data['date_formation']) : null,
            $data['niveau'] ?? null,
            $data['places_max'] ?? null,
            $data['places_prises'] ?? 0,
            $data['prix'] ?? null,
            $data['description'] ?? null,
            $data['statut'] ?? 'Active'
        );
    }
}
