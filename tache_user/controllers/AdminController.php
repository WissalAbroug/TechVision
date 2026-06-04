<?php
require_once __DIR__ . '/../models/AdminModel.php';
require_once __DIR__ . '/../config/Database.php';

class AdminController {
    private $pdo;
    private string $table = 'admin';

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Gère la connexion d'un admin
     */
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Nom d\'utilisateur et mot de passe requis'];
        }

        $admin = $this->authenticate($username, $password);

        if ($admin) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin->getId();
            $_SESSION['admin_username'] = $admin->getUsername();
            $_SESSION['admin_logged_in'] = true;

            return [
                'success' => true,
                'message' => 'Connexion admin réussie',
                'admin' => [
                    'id' => $admin->getId(),
                    'username' => $admin->getUsername(),
                    'last_login' => $admin->getLastLogin()->format('Y-m-d H:i:s')
                ]
            ];
        }

        return ['success' => false, 'message' => 'Nom d\'utilisateur ou mot de passe incorrect'];
    }

    /**
     * Crée un nouvel admin
     */
    public function createAdmin($username, $password, $confirmPassword) {
        if (empty($username) || empty($password) || empty($confirmPassword)) {
            return ['success' => false, 'message' => 'Tous les champs sont requis'];
        }

        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Les mots de passe ne correspondent pas'];
        }

        if (strlen($username) < 3) {
            return ['success' => false, 'message' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères'];
        }

        if ($this->usernameExists($username)) {
            return ['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà utilisé'];
        }

        try {
            $adminId = $this->createAdminInDB($username, $password);
            if ($adminId) {
                return ['success' => true, 'message' => 'Admin créé avec succès', 'admin_id' => $adminId];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la création de l'admin: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création de l\'admin. Veuillez réessayer.'];
        }

        return ['success' => false, 'message' => 'Erreur lors de la création de l\'admin'];
    }

    /**
     * Récupère un admin par ID
     */
    public function getAdmin($id) {
        $admin = $this->getAdminById($id);
        if ($admin) {
            return [
                'id' => $admin->getId(),
                'username' => $admin->getUsername(),
                'created_at' => $admin->getCreatedAt()->format('Y-m-d H:i:s'),
                'last_login' => $admin->getLastLogin()->format('Y-m-d H:i:s')
            ];
        }
        return null;
    }

    /**
     * Récupère tous les admins
     */
    public function getAllAdmins() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id ASC";
        $stmt = $this->pdo->query($sql);
        
        $result = [];
        while ($adminData = $stmt->fetch()) {
            $result[] = [
                'id' => (int)$adminData['id'],
                'username' => $adminData['user'],
                'created_at' => $adminData['created_at'],
                'last_login' => $adminData['last_login']
            ];
        }
        return $result;
    }

    /**
     * Mise à jour d'un admin
     */
    public function updateAdmin($id, $username, $password = null) {
        if (empty($username)) {
            return ['success' => false, 'message' => 'Nom d\'utilisateur requis'];
        }

        $admin = $this->getAdminById($id);
        if (!$admin) {
            return ['success' => false, 'message' => 'Admin non trouvé'];
        }

        // Vérifier si le username est déjà utilisé par un autre admin
        if ($this->usernameExists($username) && $admin->getUsername() !== $username) {
            return ['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà utilisé'];
        }

        try {
            if ($this->updateAdminData($id, $username, $password)) {
                return ['success' => true, 'message' => 'Admin modifié avec succès'];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la modification de l'admin: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la modification. Veuillez réessayer.'];
        }

        return ['success' => false, 'message' => 'Erreur lors de la modification'];
    }

    /**
     * Suppression d'un admin
     */
    public function deleteAdmin($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute(['id' => $id])) {
                return ['success' => true, 'message' => 'Admin supprimé avec succès'];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression: " . $e->getMessage());
        }
        return ['success' => false, 'message' => 'Erreur lors de la suppression'];
    }

    /**
     * Recherche d'admins
     */
    public function searchAdmins($searchTerm) {
        if (empty($searchTerm)) {
            return $this->getAllAdmins();
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE user LIKE :term ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['term' => "%$searchTerm%"]);
        
        $result = [];
        while ($adminData = $stmt->fetch()) {
            $result[] = [
                'id' => (int)$adminData['id'],
                'username' => $adminData['user'],
                'created_at' => $adminData['created_at'],
                'last_login' => $adminData['last_login']
            ];
        }
        return $result;
    }

    /**
     * Déconnexion admin
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();

        return ['success' => true, 'message' => 'Déconnexion admin réussie'];
    }

    /**
     * Vérifie si un admin est connecté
     */
    public function isAdminLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($_SESSION['admin_logged_in']);
    }

    /**
     * Récupère l'admin connecté
     */
    public function getCurrentAdmin() {
        if (!$this->isAdminLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['admin_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? 'Admin'
        ];
    }

    /**
     * Nombre total d'admins
     */
    public function countAdmins() {
        $r = $this->pdo->query("SELECT COUNT(*) AS total FROM {$this->table}")->fetch();
        return (int)$r['total'];
    }

    /**
     * Statistiques admin
     */
    public function getAdminStats() {
        return [
            'total_admins' => $this->countAdmins(),
            'admins' => $this->getAllAdmins()
        ];
    }

    /**
     * Charge un admin par ID
     */
    public function loadAdminById(int $id): ?Admin {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $admin = $stmt->fetch();

        if ($admin) {
            return new Admin(
                (int)$admin['id'],
                $admin['user'],
                $admin['password'],
                new DateTime($admin['created_at']),
                new DateTime($admin['last_login'])
            );
        }
        return null;
    }

    // ===============================================================
    // MÉTHODES PRIVÉES POUR LES OPÉRATIONS DE BASE DE DONNÉES
    // ===============================================================

    /**
     * Récupère un admin par ID (méthode interne)
     */
    private function getAdminById(int $id): ?Admin {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $adminData = $stmt->fetch();

        if ($adminData) {
            return new Admin(
                (int)$adminData['id'],
                $adminData['user'],
                $adminData['password'],
                new DateTime($adminData['created_at']),
                new DateTime($adminData['last_login'])
            );
        }
        return null;
    }

    /**
     * Création d'un admin dans la BD
     */
    private function createAdminInDB(string $username, string $password): ?int {
        $sql = "INSERT INTO {$this->table} (user, password, created_at, last_login)
                VALUES (:user, :password, NOW(), NOW())";

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            'user' => $username,
            'password' => $password
        ]);

        if ($ok) {
            return (int)$this->pdo->lastInsertId();
        }
        return null;
    }

    /**
     * Met à jour les données d'un admin dans la BD
     */
    private function updateAdminData(int $id, string $username, ?string $password): bool {
        if (!empty($password)) {
            $sql = "UPDATE {$this->table}
                    SET user = :user, password = :password
                    WHERE id = :id";
            $params = [
                'id' => $id,
                'user' => $username,
                'password' => $password
            ];
        } else {
            $sql = "UPDATE {$this->table}
                    SET user = :user
                    WHERE id = :id";
            $params = [
                'id' => $id,
                'user' => $username
            ];
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Mise à jour du last_login
     */
    private function updateLastLogin(int $id): bool {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Vérifie si un username existe
     */
    private function usernameExists(string $username): bool {
        $sql = "SELECT COUNT(*) AS nb FROM {$this->table} WHERE user = :user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user' => $username]);
        $res = $stmt->fetch();
        return ($res['nb'] ?? 0) > 0;
    }

    /**
     * Authentification d'un admin
     */
    private function authenticate(string $username, string $password): ?Admin {
        $sql = "SELECT * FROM {$this->table} WHERE user = :user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user' => $username]);
        $adminData = $stmt->fetch();

        if ($adminData && $adminData['password'] === $password) {
            $admin = new Admin(
                (int)$adminData['id'],
                $adminData['user'],
                $adminData['password'],
                new DateTime($adminData['created_at']),
                new DateTime($adminData['last_login'])
            );
            
            // Mettre à jour le last_login
            $this->updateLastLogin($admin->getId());
            $admin->setLastLogin(new DateTime());
            
            return $admin;
        }
        return null;
    }
}
?>