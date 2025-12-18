<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../config/Database.php';

class UserController {
    private $pdo;
    private $table = 'compte';

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Gère la connexion d'un utilisateur
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email et mot de passe requis'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format d\'email invalide'];
        }

        $user = $this->authenticate($email, $password);

        if ($user) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_name'] = $user->getUsername();
            $_SESSION['logged_in'] = true;

            return [
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail()
                ]
            ];
        }

        return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
    }

    /**
     * Inscription d'un utilisateur
     * 🔥 CORRECTION: Hashage du mot de passe AVANT l'insertion
     */
    public function register($username, $email, $password) {
        error_log("=== DEBUT REGISTER CONTROLLER ===");
        error_log("Username: $username");
        error_log("Email: $email");
        error_log("Password length: " . strlen($password));
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            error_log("ERROR: Champs vides");
            return ['success' => false, 'message' => 'Tous les champs sont requis'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("ERROR: Email invalide");
            return ['success' => false, 'message' => 'Format d\'email invalide'];
        }

        if (strlen($username) < 3) {
            error_log("ERROR: Username trop court");
            return ['success' => false, 'message' => 'Le nom d\'utilisateur doit contenir au moins 3 caractères'];
        }

        if (strlen($password) < 8) {
            error_log("ERROR: Password trop court");
            return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères'];
        }

        // Vérifier si email existe
        if ($this->emailExists($email)) {
            error_log("ERROR: Email existe déjà");
            return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
        }

        try {
            error_log("Tentative de création...");
            
            // 🔥 CORRECTION IMPORTANTE: Hasher le mot de passe AVANT de l'envoyer à createUser
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            error_log("Password hashé: " . substr($hashedPassword, 0, 30) . "...");
            
            // Passer le mot de passe HASHÉ à createUser
            $userId = $this->createUser($username, $email, $hashedPassword);
            
            if ($userId) {
                error_log("SUCCESS: Utilisateur créé avec ID: $userId");
                $_SESSION['signup_success'] = true;
                $_SESSION['signup_message'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                return [
                    'success' => true, 
                    'message' => 'Inscription réussie',
                    'user_id' => $userId
                ];
            } else {
                error_log("ERROR: Échec de création");
                return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
            }
        } catch (Exception $e) {
            error_log("EXCEPTION: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Charge un utilisateur par ID
     */
    public function loadUserById(int $id): ?UserModel {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if ($user) {
            return new UserModel(
                (int)$user['id'],
                $user['user'],
                $user['email'],
                $user['password'],
                $user['profile_photo'] ?? null,
                new DateTime($user['created_at'] ?? "now")
            );
        }
        return null;
    }

    /**
     * Récupère un utilisateur par ID
     */
    public function getUser($id) {
        $user = $this->getUserById($id);
        if ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail()
            ];
        }
        return null;
    }

    /**
     * Récupère un utilisateur par ID (méthode interne)
     */
    private function getUserById(int $id): ?UserModel {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $userData = $stmt->fetch();

        if ($userData) {
            return new UserModel(
                (int)$userData['id'],
                $userData['user'],
                $userData['email'],
                $userData['password'],
                $userData['profile_photo'] ?? null,
                isset($userData['created_at']) ? new DateTime($userData['created_at']) : null
            );
        }
        return null;
    }

    /**
     * Récupère tous les utilisateurs
     */
    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY id DESC");
        $result = [];
        
        while ($userData = $stmt->fetch()) {
            $result[] = [
                'id' => (int)$userData['id'],
                'username' => $userData['user'],
                'email' => $userData['email']
            ];
        }
        return $result;
    }

    /**
     * Mise à jour d'un utilisateur
     */
    public function updateUser($id, $username, $email, $password = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($username) || empty($email)) {
            return ['success' => false, 'message' => 'Nom d\'utilisateur et email requis'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format d\'email invalide'];
        }

        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Utilisateur non trouvé'];
        }

        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        if ($this->emailExists($email) && $user->getEmail() !== $email) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
        }

        try {
            // Hasher le mot de passe si fourni
            $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
            
            if ($this->updateUserData($id, $username, $email, $hashedPassword)) {
                $_SESSION['update_success'] = true;
                return ['success' => true, 'message' => 'Utilisateur modifié avec succès'];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la modification: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la modification. Veuillez réessayer.'];
        }

        return ['success' => false, 'message' => 'Erreur lors de la modification'];
    }

    /**
     * Met à jour les données d'un utilisateur dans la BD
     */
    private function updateUserData(int $id, string $username, string $email, ?string $hashedPassword): bool {
        if (!empty($hashedPassword)) {
            $sql = "UPDATE {$this->table}
                    SET user = :user, email = :email, password = :password
                    WHERE id = :id";
            $params = [
                'id' => $id,
                'user' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ];
        } else {
            $sql = "UPDATE {$this->table}
                    SET user = :user, email = :email
                    WHERE id = :id";
            $params = [
                'id' => $id,
                'user' => $username,
                'email' => $email
            ];
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Supprime un utilisateur
     */
    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute(['id' => $id])) {
                return ['success' => true, 'message' => 'Utilisateur supprimé avec succès'];
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la suppression'];
        }

        return ['success' => false, 'message' => 'Erreur lors de la suppression'];
    }

    /**
     * Recherche d'utilisateurs
     */
    public function searchUsers($searchTerm) {
        if (empty($searchTerm)) {
            return $this->getAllUsers();
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE user LIKE :t OR email LIKE :t ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['t' => "%$searchTerm%"]);

        $result = [];
        while ($userData = $stmt->fetch()) {
            $result[] = [
                'id' => (int)$userData['id'],
                'username' => $userData['user'],
                'email' => $userData['email']
            ];
        }
        return $result;
    }

    /**
     * Déconnexion
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

        return ['success' => true, 'message' => 'Déconnexion réussie'];
    }

    /**
     * Vérifie si connecté
     */
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return !empty($_SESSION['logged_in']);
    }

    /**
     * Utilisateur connecté
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? 'Utilisateur',
            'email' => $_SESSION['user_email'] ?? null
        ];
    }

    /**
     * Nombre total d'utilisateurs
     */
    public function countUsers() {
        $r = $this->pdo->query("SELECT COUNT(*) AS total FROM {$this->table}")->fetch();
        return (int)$r['total'];
    }

    /**
     * Statistiques
     */
    public function getStats() {
        return [
            'total_users' => $this->countUsers(),
            'recent_users' => count($this->getAllUsers())
        ];
    }

    // ===============================================================
    // MÉTHODES PRIVÉES POUR LES OPÉRATIONS DE BASE DE DONNÉES
    // ===============================================================

    /**
     * Création d'un utilisateur dans la BD
     * ⚠️ IMPORTANT: Cette méthode reçoit le mot de passe DÉJÀ HASHÉ
     */
    private function createUser(string $username, string $email, string $hashedPassword): ?int {
        error_log("=== CREATE USER IN DATABASE ===");
        error_log("Username: $username");
        error_log("Email: $email");
        error_log("Hashed password (30 first chars): " . substr($hashedPassword, 0, 30) . "...");
        
        $sql = "INSERT INTO {$this->table} (user, email, password) VALUES (:user, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        
        $ok = $stmt->execute([
            'user' => $username,
            'email' => $email,
            'password' => $hashedPassword // Le password est déjà hashé
        ]);

        if ($ok) {
            $userId = (int)$this->pdo->lastInsertId();
            error_log("User created with ID: $userId");
            return $userId;
        }
        
        error_log("ERROR: Failed to insert user");
        return null;
    }

    /**
     * Vérifie si un email existe
     */
    private function emailExists(string $email): bool {
        $sql = "SELECT COUNT(*) AS nb FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $res = $stmt->fetch();
        return ($res['nb'] ?? 0) > 0;
    }

    /**
     * Authentification d'un utilisateur
     */
    private function authenticate(string $email, string $password): ?UserModel {
        error_log("=== AUTHENTICATE METHOD ===");
        error_log("Email recherché: " . $email);
        error_log("Password length: " . strlen($password));
        
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $userData = $stmt->fetch();

        if ($userData) {
            error_log("✅ Utilisateur trouvé en BD");
            error_log("ID: " . $userData['id']);
            error_log("Username: " . $userData['user']);
            error_log("Hash en BD (30 premiers chars): " . substr($userData['password'], 0, 30) . "...");
            
            $passwordMatch = password_verify($password, $userData['password']);
            error_log("Password verify result: " . ($passwordMatch ? 'MATCH ✅' : 'NO MATCH ❌'));
            
            if ($passwordMatch) {
                error_log("SUCCESS: Création de l'objet UserModel");
                
                return new UserModel(
                    (int)$userData['id'],
                    $userData['user'],
                    $userData['email'],
                    $userData['password'],
                    $userData['profile_photo'] ?? null,
                    isset($userData['created_at']) ? new DateTime($userData['created_at']) : null
                );
            } else {
                error_log("FAILED: Password does not match");
            }
        } else {
            error_log("❌ Utilisateur non trouvé en BD");
        }
        
        error_log("Authentication FAILED");
        return null;
    }

    // ===============================================================
    // MÉTHODES POUR LA RÉCUPÉRATION DE MOT DE PASSE
    // ===============================================================

    /**
     * Récupère un utilisateur par email
     */
    private function getUserByEmail(string $email): ?UserModel {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $userData = $stmt->fetch();

        if ($userData) {
            return new UserModel(
                (int)$userData['id'],
                $userData['user'],
                $userData['email'],
                $userData['password'],
                $userData['profile_photo'] ?? null,
                isset($userData['created_at']) ? new DateTime($userData['created_at']) : null
            );
        }
        return null;
    }

    /**
     * Envoie un code de réinitialisation par email
     */
    public function sendPasswordResetCode($email) {
        try {
            $user = $this->getUserByEmail($email);
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Aucun compte associé à cet email.'
                ];
            }
            
            $code = sprintf('%06d', mt_rand(0, 999999));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            
            $saved = $this->savePasswordResetCode(
                $user->getId(),
                $email,
                $code,
                $expiresAt
            );
            
            if (!$saved) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la sauvegarde du code.'
                ];
            }
            
            $userName = $user->getUsername();
            $emailSent = $this->sendResetEmail($email, $userName, $code);
            
            if ($emailSent) {
                return [
                    'success' => true,
                    'message' => '✅ Un code de vérification a été envoyé à votre adresse email.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Erreur sendPasswordResetCode: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }

    /**
     * Vérifie le code de réinitialisation
     */
    public function verifyResetCode($email, $code) {
        try {
            $reset = $this->getPasswordResetByEmailAndCode($email, $code);
            
            if (!$reset) {
                return [
                    'success' => false,
                    'message' => '❌ Code de vérification invalide.'
                ];
            }
            
            if ($reset['used'] == 1) {
                return [
                    'success' => false,
                    'message' => '❌ Ce code a déjà été utilisé.'
                ];
            }
            
            if (strtotime($reset['expires_at']) < time()) {
                return [
                    'success' => false,
                    'message' => '❌ Ce code a expiré. Veuillez demander un nouveau code.'
                ];
            }
            
            return [
                'success' => true,
                'message' => '✅ Code vérifié avec succès.'
            ];
            
        } catch (Exception $e) {
            error_log("Erreur verifyResetCode: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }

    /**
     * Réinitialise le mot de passe
     */
    public function resetPassword($email, $newPassword) {
        try {
            $reset = $this->getValidPasswordReset($email);
            
            if (!$reset) {
                return [
                    'success' => false,
                    'message' => 'Session expirée. Veuillez recommencer le processus.'
                ];
            }
            
            $user = $this->getUserById($reset['user_id']);
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ];
            }
            
            // Hasher le nouveau mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            if (!$this->updateUserData($user->getId(), $user->getUsername(), $user->getEmail(), $hashedPassword)) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du mot de passe.'
                ];
            }
            
            $this->markPasswordResetAsUsed($reset['id']);
            
            return [
                'success' => true,
                'message' => '✅ Votre mot de passe a été réinitialisé avec succès.'
            ];
            
        } catch (Exception $e) {
            error_log("Erreur resetPassword: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.'
            ];
        }
    }

    private function savePasswordResetCode(int $userId, string $email, string $code, string $expiresAt): bool {
        try {
            $this->createPasswordResetsTableIfNotExists();
            
            $sql = "INSERT INTO password_resets (user_id, email, code, expires_at, created_at) 
                    VALUES (:user_id, :email, :code, :expires_at, NOW())
                    ON DUPLICATE KEY UPDATE 
                        code = VALUES(code), 
                        expires_at = VALUES(expires_at), 
                        created_at = NOW(),
                        used = 0";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'email' => $email,
                'code' => $code,
                'expires_at' => $expiresAt
            ]);
        } catch (PDOException $e) {
            error_log("Erreur savePasswordResetCode: " . $e->getMessage());
            return false;
        }
    }

    private function getPasswordResetByEmailAndCode(string $email, string $code): ?array {
        try {
            $sql = "SELECT id, user_id, expires_at, used 
                    FROM password_resets 
                    WHERE email = :email AND code = :code 
                    ORDER BY created_at DESC 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['email' => $email, 'code' => $code]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getPasswordResetByEmailAndCode: " . $e->getMessage());
            return null;
        }
    }

    private function getValidPasswordReset(string $email): ?array {
        try {
            $sql = "SELECT id, user_id 
                    FROM password_resets 
                    WHERE email = :email 
                    AND used = 0 
                    AND expires_at > NOW() 
                    ORDER BY created_at DESC 
                    LIMIT 1";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur getValidPasswordReset: " . $e->getMessage());
            return null;
        }
    }

    private function markPasswordResetAsUsed(int $resetId): bool {
        try {
            $sql = "UPDATE password_resets SET used = 1 WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $resetId]);
        } catch (PDOException $e) {
            error_log("Erreur markPasswordResetAsUsed: " . $e->getMessage());
            return false;
        }
    }

    private function createPasswordResetsTableIfNotExists(): void {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                email VARCHAR(255) NOT NULL,
                code VARCHAR(6) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                used TINYINT(1) DEFAULT 0,
                INDEX idx_email (email),
                INDEX idx_code (code),
                INDEX idx_expires (expires_at),
                INDEX idx_user_id (user_id),
                FOREIGN KEY (user_id) REFERENCES compte(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Erreur création table password_resets: " . $e->getMessage());
        }
    }

    private function sendResetEmail($toEmail, $userName, $code) {
        try {
            error_log("========================================");
            error_log("CODE DE RÉCUPÉRATION POUR: $toEmail");
            error_log("NOM: $userName");
            error_log("CODE: $code");
            error_log("Ce code expire dans 15 minutes");
            error_log("========================================");

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['debug_reset_code'] = $code;
            $_SESSION['debug_reset_email'] = $toEmail;

            return true;

        } catch (Exception $e) {
            error_log("Erreur sendResetEmail: " . $e->getMessage());
            return false;
        }
    }

    // ===============================================================
    // MÉTHODES POUR LA GESTION DU PROFIL UTILISATEUR
    // ===============================================================

    public function getProfile($userId) {
        try {
            $user = $this->getUserById($userId);
            if (!$user) {
                return ['success' => false, 'message' => 'Utilisateur non trouvé'];
            }

            return [
                'success' => true,
                'profile' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'profile_photo' => $user->getProfilePhoto(),
                    'created_at' => $user->getCreatedAt() ? $user->getCreatedAt()->format('Y-m-d H:i:s') : null
                ]
            ];
        } catch (Exception $e) {
            error_log("Erreur getProfile: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la récupération du profil'];
        }
    }

    public function updatePassword($userId, $currentPassword, $newPassword) {
        try {
            $user = $this->getUserById($userId);
            if (!$user) {
                return ['success' => false, 'message' => 'Utilisateur non trouvé'];
            }

            if (!$this->verifyPassword($userId, $currentPassword)) {
                return ['success' => false, 'message' => 'Mot de passe actuel incorrect'];
            }

            if (strlen($newPassword) < 8) {
                return ['success' => false, 'message' => 'Le nouveau mot de passe doit contenir au moins 8 caractères'];
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($this->updateUserPassword($userId, $hashedPassword)) {
                return ['success' => true, 'message' => 'Mot de passe mis à jour avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe'];
            }

        } catch (Exception $e) {
            error_log("Erreur updatePassword: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe'];
        }
    }

    public function updateProfilePhoto($userId, $photoPath) {
        try {
            $user = $this->getUserById($userId);
            if (!$user) {
                return ['success' => false, 'message' => 'Utilisateur non trouvé'];
            }

            if ($this->updateUserProfilePhoto($userId, $photoPath)) {
                return ['success' => true, 'message' => 'Photo de profil mise à jour avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour de la photo de profil'];
            }

        } catch (Exception $e) {
            error_log("Erreur updateProfilePhoto: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour de la photo de profil'];
        }
    }

    private function verifyPassword($userId, $password) {
        $sql = "SELECT password FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $userData = $stmt->fetch();

        if ($userData) {
            return password_verify($password, $userData['password']);
        }
        return false;
    }

    private function updateUserPassword($userId, $hashedPassword) {
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $userId,
            'password' => $hashedPassword
        ]);
    }

    private function updateUserProfilePhoto($userId, $photoPath) {
        $sql = "UPDATE {$this->table} SET profile_photo = :photo WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $userId,
            'photo' => $photoPath
        ]);
    }
}
?>