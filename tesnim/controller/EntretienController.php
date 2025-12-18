<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../model/Entretien.php');

class EntretienController {

    // Lister tous les entretiens
    public function listEntretiens() {
        $sql = "SELECT * FROM entretien ORDER BY date ASC, heure ASC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Lister les entretiens disponibles (non complets, futurs)
    public function listEntretiensDisponibles() {
        $sql = "SELECT * FROM entretien 
                WHERE date >= CURDATE() 
                AND places_prises < places 
                ORDER BY date ASC, heure ASC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Ajouter un entretien
    public function addEntretien(Entretien $entretien) {
        $sql = "INSERT INTO entretien (type, date, heure, places, places_prises) 
                VALUES (:type, :date, :heure, :places, :placesPrises)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'type' => $entretien->getType(),
                'date' => $entretien->getDate() ? $entretien->getDate()->format('Y-m-d') : null,
                'heure' => $entretien->getHeure(),
                'places' => $entretien->getPlaces(),
                'placesPrises' => $entretien->getPlacesPrises()
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Mettre à jour un entretien
    public function updateEntretien(Entretien $entretien, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE entretien SET 
                    type = :type,
                    date = :date,
                    heure = :heure,
                    places = :places,
                    places_prises = :placesPrises
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'type' => $entretien->getType(),
                'date' => $entretien->getDate() ? $entretien->getDate()->format('Y-m-d') : null,
                'heure' => $entretien->getHeure(),
                'places' => $entretien->getPlaces(),
                'placesPrises' => $entretien->getPlacesPrises()
            ]);
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
        }
    }

    // Supprimer un entretien
    public function deleteEntretien($id) {
        $sql = "DELETE FROM entretien WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Afficher un entretien par ID
    public function showEntretien($id) {
        $sql = "SELECT * FROM entretien WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        try {
            $query->execute(['id' => $id]);
            $entretien = $query->fetch();
            return $entretien;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Incrémenter le nombre de places prises
    public function incrementerPlacesPrises($id) {
        $sql = "UPDATE entretien SET places_prises = places_prises + 1 
                WHERE id = :id AND places_prises < places";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount() > 0; // Retourne true si mise à jour réussie
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Décrémenter le nombre de places prises
    public function decrementerPlacesPrises($id) {
        $sql = "UPDATE entretien SET places_prises = places_prises - 1 
                WHERE id = :id AND places_prises > 0";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount() > 0;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Obtenir les statistiques
    public function getStatistiques() {
        $db = config::getConnexion();
        try {
            $stats = [];
            
            // Total sessions
            $sql = "SELECT COUNT(*) as total FROM entretien";
            $result = $db->query($sql)->fetch();
            $stats['total_sessions'] = $result['total'];
            
            // Sessions à venir
            $sql = "SELECT COUNT(*) as total FROM entretien WHERE date >= CURDATE()";
            $result = $db->query($sql)->fetch();
            $stats['sessions_avenir'] = $result['total'];
            
            // Total réservations
            $sql = "SELECT COUNT(*) as total FROM demande_entretien";
            $result = $db->query($sql)->fetch();
            $stats['total_reservations'] = $result['total'];
            
            // Réservations aujourd'hui
            $sql = "SELECT COUNT(*) as total FROM demande_entretien WHERE DATE(date_demande) = CURDATE()";
            $result = $db->query($sql)->fetch();
            $stats['reservations_aujourdhui'] = $result['total'];
            
            return $stats;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>