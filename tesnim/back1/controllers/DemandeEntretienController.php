<?php
include(__DIR__ . '/../config/database.php');
require_once __DIR__ . '/../models/DemandeEntretien.php';

class DemandeEntretienController {

    public function listDemandes() {
        $sql = "SELECT * FROM demande_entretien ORDER BY date_creation DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function deleteDemande($id) {
        $sql = "DELETE FROM demande_entretien WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function addDemande(DemandeEntretien $demande) {
        $sql = "INSERT INTO demande_entretien (nom, email, telephone, statut, date_creation) VALUES (:nom, :email, :telephone, :statut, NOW())";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $demande->getNom(),
                'email' => $demande->getEmail(),
                'telephone' => $demande->getTelephone(),
                'statut' => $demande->getStatut()
            ]);
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function updateDemande(DemandeEntretien $demande, $id) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE demande_entretien SET
                    nom = :nom,
                    email = :email,
                    telephone = :telephone,
                    statut = :statut
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'nom' => $demande->getNom(),
                'email' => $demande->getEmail(),
                'telephone' => $demande->getTelephone(),
                'statut' => $demande->getStatut()
            ]);
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function showDemande($id) {
        $sql = "SELECT * FROM demande_entretien WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);

        try {
            $query->execute();
            $demande = $query->fetch(PDO::FETCH_ASSOC);
            return $demande;
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function searchDemandes($searchTerm) {
        $sql = "SELECT * FROM demande_entretien
                WHERE nom LIKE :search
                OR email LIKE :search
                OR telephone LIKE :search
                OR statut LIKE :search
                OR id LIKE :search
                ORDER BY date_creation DESC";

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $searchParam = "%$searchTerm%";
        $query->bindValue(':search', $searchParam);

        try {
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function countDemandes() {
        $sql = "SELECT COUNT(*) as total FROM demande_entretien";
        $db = config::getConnexion();
        try {
            $result = $db->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getByStatut($statut) {
        $sql = "SELECT * FROM demande_entretien WHERE statut = :statut ORDER BY date_creation DESC";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':statut', $statut);

        try {
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Action methods for routing

    public function index() {
        $searchQuery = $_GET['search'] ?? '';
        $records = [];
        $totalRecords = 0;

        try {
            if (!empty($searchQuery)) {
                $records = $this->searchDemandes($searchQuery);
            } else {
                $list = $this->listDemandes();
                $records = $list ? $list->fetchAll(PDO::FETCH_ASSOC) : [];
            }
            $totalRecords = count($records);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        include __DIR__ . '/../views/demande/index.php';
    }

    public function createPage() {
        include __DIR__ . '/../views/demande/create.php';
    }

    public function editPage() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?error=ID manquant');
            exit;
        }

        $record = $this->showDemande($id);
        if (!$record) {
            header('Location: index.php?error=Enregistrement non trouvé');
            exit;
        }

        include __DIR__ . '/../views/demande/edit.php';
    }

    public function api() {
        header('Content-Type: application/json');
        try {
            $list = $this->listDemandes();
            $records = $list ? $list->fetchAll(PDO::FETCH_ASSOC) : [];
            echo json_encode(['success' => true, 'data' => $records]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?error=Méthode non autorisée');
            exit;
        }

        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $statut = $_POST['statut'] ?? 'en attente';

        if (empty($nom) || empty($email)) {
            header('Location: index.php?action=create_page&error=Nom et email requis&nom=' . urlencode($nom) . '&email=' . urlencode($email) . '&telephone=' . urlencode($telephone));
            exit;
        }

        try {
            $demande = new DemandeEntretien(null, $nom, $email, $telephone, $statut, null);
            $this->addDemande($demande);
            header('Location: index.php?success=created');
        } catch (Exception $e) {
            header('Location: index.php?action=create_page&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?error=Méthode non autorisée');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $statut = $_POST['statut'] ?? 'en attente';

        if (!$id || empty($nom) || empty($email)) {
            header('Location: index.php?error=Données manquantes');
            exit;
        }

        try {
            $demande = new DemandeEntretien($id, $nom, $email, $telephone, $statut, null);
            $this->updateDemande($demande, $id);
            header('Location: index.php?success=updated');
        } catch (Exception $e) {
            header('Location: index.php?error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?error=Méthode non autorisée');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: index.php?error=ID manquant');
            exit;
        }

        try {
            $this->deleteDemande($id);
            header('Location: index.php?success=deleted');
        } catch (Exception $e) {
            header('Location: index.php?error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    // Fonctions utilitaires (ajoutées pour maintenir les fonctionnalités)
    public static function escapeHtml($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }

    public static function formatDate($date) {
        if (empty($date)) return '-';
        try {
            $dateObj = new DateTime($date);
            return $dateObj->format('d/m/Y H:i');
        } catch (Exception $e) {
            return $date;
        }
    }

    public static function getBadgeClass($statut) {
        if (empty($statut)) return 'inactive';
        $s = strtolower($statut);
        if ($s === 'actif' || $s === 'active' || $s === 'validé') return 'active';
        if ($s === 'en attente' || $s === 'pending') return 'pending';
        return 'inactive';
    }
}
?>
