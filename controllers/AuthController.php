<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Profil.php';

class AuthController
{
    private User $userModel;
    private Profil $profilModel;

    public function __construct()
    {
        $this->userModel   = new User();
        $this->profilModel = new Profil();
    }

    public function login(): void
    {
        if (isLoggedIn()) {
            redirect($this->dashboardUrl());
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
            return;
        }
        require __DIR__ . '/../views/frontoffice/auth/login.php';
    }

    private function handleLogin(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Requête invalide.');
            redirect(APP_URL . '/?ctrl=auth&action=login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors   = [];

        if (empty($email))    $errors[] = __('gen.required') . ' (email)';
        if (empty($password)) $errors[] = __('gen.required') . ' (password)';

        if (empty($errors)) {
            $user = $this->userModel->findByEmail($email);
            if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
                $errors[] = __('auth.invalid_creds');
            } elseif ($user['statut'] !== 'actif') {
                $errors[] = __('auth.account_inactive');
            }
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = ['email' => $email];
            redirect(APP_URL . '/?ctrl=auth&action=login');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'          => $user['id'],
            'nom'         => $user['nom'],
            'prenom'      => $user['prenom'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'nom_societe' => $user['nom_societe'],
        ];

        flash('success', __('auth.welcome_back', ['name' => $user['prenom']]));
        redirect($this->dashboardUrl($user['role']));
    }

    public function register(): void
    {
        if (isLoggedIn()) redirect(APP_URL . '/');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
            return;
        }
        require __DIR__ . '/../views/frontoffice/auth/register.php';
    }

    private function handleRegister(): void
    {
        if (!csrf_verify()) {
            flash('error', 'Requête invalide.');
            redirect(APP_URL . '/?ctrl=auth&action=register');
        }

        $data   = $_POST;
        $errors = [];

        if (empty($data['nom']))      $errors[] = __('gen.required') . ' (nom)';
        if (empty($data['prenom']))   $errors[] = __('gen.required') . ' (prenom)';
        if (empty($data['email']))    $errors[] = __('gen.required') . ' (email)';
        if (empty($data['password'])) $errors[] = __('gen.required') . ' (password)';
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        if ($data['password'] !== ($data['password_confirm'] ?? '')) {
            $errors[] = __('auth.pass_mismatch');
        }
        if (!empty($data['email']) && $this->userModel->findByEmail($data['email'])) {
            $errors[] = __('auth.email_taken');
        }
        if (!in_array($data['role'] ?? '', ['candidat','societe'])) {
            $errors[] = 'Rôle invalide.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $data;
            redirect(APP_URL . '/?ctrl=auth&action=register');
            return;
        }

        $userId = $this->userModel->register($data);
        $this->profilModel->createOrUpdate($userId, []);

        session_regenerate_id(true);
        $user = $this->userModel->findById($userId);
        $_SESSION['user'] = [
            'id'          => $user['id'],
            'nom'         => $user['nom'],
            'prenom'      => $user['prenom'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'nom_societe' => $user['nom_societe'],
        ];

        flash('success', __('auth.register_success'));
        redirect($this->dashboardUrl($user['role']));
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect(APP_URL . '/');
    }

    private function dashboardUrl(string $role = ''): string
    {
        if (!$role && isLoggedIn()) $role = auth()['role'];
        return match($role) {
            'admin'   => APP_URL . '/?ctrl=admin&action=dashboard',
            'societe' => APP_URL . '/?ctrl=societe&action=dashboard',
            default   => APP_URL . '/?ctrl=user&action=dashboard',
        };
    }

    /* ── Forgot Password ─────────────────────────────────── */
    public function forgotPassword(): void
    {
        if (isLoggedIn()) redirect($this->dashboardUrl());

        $lang = $_SESSION['lang'] ?? 'fr';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                flash('error', 'Requête invalide.');
                redirect(APP_URL . '/?ctrl=auth&action=forgotPassword');
            }

            $email = strtolower(trim($_POST['email'] ?? ''));
            if (empty($email)) {
                flash('error', $lang === 'fr' ? 'Veuillez saisir votre adresse e-mail.' : 'Please enter your email address.');
                redirect(APP_URL . '/?ctrl=auth&action=forgotPassword');
            }

            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                flash('error', $lang === 'fr' ? 'Aucun compte trouvé avec cet e-mail.' : 'No account found with this email.');
                redirect(APP_URL . '/?ctrl=auth&action=forgotPassword');
            }

            // Generate a secure token and store in session
            $token = bin2hex(random_bytes(32));
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_user_id'] = $user['id'];
            $_SESSION['reset_expires'] = time() + 900; // 15 minutes

            // Redirect to reset page with token
            flash('success', $lang === 'fr' ? 'Vérification réussie ! Définissez votre nouveau mot de passe.' : 'Verification successful! Set your new password.');
            redirect(APP_URL . '/?ctrl=auth&action=resetPassword&token=' . $token);
            return;
        }

        require __DIR__ . '/../views/frontoffice/auth/forgot_password.php';
    }

    /* ── Reset Password ──────────────────────────────────── */
    public function resetPassword(): void
    {
        if (isLoggedIn()) redirect($this->dashboardUrl());

        $lang = $_SESSION['lang'] ?? 'fr';
        $token = $_GET['token'] ?? '';

        // Validate token
        if (
            empty($token) ||
            !isset($_SESSION['reset_token']) ||
            !hash_equals($_SESSION['reset_token'], $token) ||
            !isset($_SESSION['reset_expires']) ||
            time() > $_SESSION['reset_expires']
        ) {
            flash('error', $lang === 'fr' ? 'Lien de réinitialisation invalide ou expiré.' : 'Invalid or expired reset link.');
            redirect(APP_URL . '/?ctrl=auth&action=forgotPassword');
            return;
        }

        $userId = $_SESSION['reset_user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                flash('error', 'Requête invalide.');
                redirect(APP_URL . '/?ctrl=auth&action=resetPassword&token=' . $token);
            }

            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['password_confirm'] ?? '';
            $errors   = [];

            if (empty($password)) {
                $errors[] = $lang === 'fr' ? 'Le mot de passe est requis.' : 'Password is required.';
            } elseif (strlen($password) < 6) {
                $errors[] = $lang === 'fr' ? 'Le mot de passe doit contenir au moins 6 caractères.' : 'Password must be at least 6 characters.';
            }
            if ($password !== $confirm) {
                $errors[] = $lang === 'fr' ? 'Les mots de passe ne correspondent pas.' : 'Passwords do not match.';
            }

            if (!empty($errors)) {
                $_SESSION['form_errors'] = $errors;
                redirect(APP_URL . '/?ctrl=auth&action=resetPassword&token=' . $token);
                return;
            }

            // Update password
            $this->userModel->updatePassword($userId, $password);

            // Clear reset session data
            unset($_SESSION['reset_token'], $_SESSION['reset_user_id'], $_SESSION['reset_expires']);

            flash('success', $lang === 'fr' ? 'Mot de passe modifié avec succès ! Connectez-vous.' : 'Password changed successfully! Please log in.');
            redirect(APP_URL . '/?ctrl=auth&action=login');
            return;
        }

        require __DIR__ . '/../views/frontoffice/auth/reset_password.php';
    }
}
