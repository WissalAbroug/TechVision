<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Offre.php';
require_once __DIR__ . '/../models/Candidature.php';
require_once __DIR__ . '/../models/Reclamation.php';
require_once __DIR__ . '/../models/Formation.php';

class AdminController
{
    private User       $userModel;
    private Offre      $offreModel;
    private Candidature $candModel;
    private Reclamation $reclamationModel;
    private Formation  $formationModel;

    public function __construct()
    {
        $this->userModel        = new User();
        $this->offreModel       = new Offre();
        $this->candModel        = new Candidature();
        $this->reclamationModel = new Reclamation();
        $this->formationModel   = new Formation();
    }

    /* ── Dashboard ─────────────────────────────────────────── */
    public function dashboard(): void
    {
        requireRole('admin');

        $stats = [
            'users' => [
                'candidat' => $this->userModel->count('role = ?', ['candidat']),
                'societe'  => $this->userModel->count('role = ?', ['societe']),
                'admin'    => $this->userModel->count('role = ?', ['admin']),
            ],
            'offres' => [
                'total'     => $this->offreModel->count(),
                'actives'   => $this->offreModel->count('statut = ?', ['active']),
                'inactives' => $this->offreModel->count('statut = ?', ['inactive']),
                'cloturees' => $this->offreModel->count('statut = ?', ['cloturee']),
            ],
            'candidatures' => $this->candModel->count(),
            'reclamations' => [
                'ouvert'   => $this->reclamationModel->count('statut = ?', ['ouvert']),
                'en_cours' => $this->reclamationModel->count('statut = ?', ['en_cours']),
                'resolu'   => $this->reclamationModel->count('statut = ?', ['resolu']),
            ],
        ];

        $recentUsers = $this->userModel->findAll('created_at DESC', 5);
        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/admin/dashboard.php';
    }

    /* ── Users List ────────────────────────────────────────── */
    public function users(): void
    {
        requireRole('admin');
        $roleFilter = sanitize($_GET['role'] ?? '');
        $users = $roleFilter
            ? $this->userModel->findAllBy('role', $roleFilter, 'created_at DESC')
            : $this->userModel->findAll('created_at DESC');
        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/admin/users.php';
    }

    /* ── Toggle User Status POST ───────────────────────────── */
    public function toggleUser(): void
    {
        requireRole('admin');
        verify_csrf();
        $id   = (int)($_POST['id'] ?? 0);
        $user = $this->userModel->findById($id);
        if ($user && $user['id'] !== auth()['id']) {
            $newStatut = $user['statut'] === 'actif' ? 'inactif' : 'actif';
            $this->userModel->update($id, ['statut' => $newStatut]);
            flash('success', __('gen.saved'));
        }
        redirect('?ctrl=admin&action=users');
    }

    /* ── Delete User POST ──────────────────────────────────── */
    public function deleteUser(): void
    {
        requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id && $id !== auth()['id']) {
            $this->userModel->delete($id);
            flash('success', __('gen.deleted'));
        }
        redirect('?ctrl=admin&action=users');
    }

    /* ── Offers List ───────────────────────────────────────── */
    public function offres(): void
    {
        requireRole('admin');
        $offres         = $this->offreModel->getAllWithSociete('active');
        $offresInactive = $this->offreModel->getAllWithSociete('inactive');
        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/admin/offres.php';
    }

    /* ── Create User GET/POST ──────────────────────────────── */
    public function adminCreateUser(): void
    {
        requireRole('admin');
        $lang = $_SESSION['lang'] ?? 'fr';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $email = sanitize($_POST['email'] ?? '');
            
            // Check uniqueness
            $existing = $this->userModel->findBy('email', $email);
            if ($existing) {
                flash('error', $lang === 'fr' ? 'Cet email est déjà utilisé.' : 'This email is already in use.');
                redirect('?ctrl=admin&action=adminCreateUser');
                return;
            }

            $password = $_POST['password'] ?? 'Password123!';
            $this->userModel->insert([
                'prenom'      => sanitize($_POST['prenom'] ?? ''),
                'nom'         => sanitize($_POST['nom'] ?? ''),
                'email'       => $email,
                'password'    => password_hash($password, PASSWORD_BCRYPT),
                'role'        => sanitize($_POST['role'] ?? 'candidat'),
                'statut'      => sanitize($_POST['statut'] ?? 'actif'),
                'nom_societe' => sanitize($_POST['nom_societe'] ?? null),
                'secteur'     => sanitize($_POST['secteur'] ?? null),
            ]);

            flash('success', __('gen.saved'));
            redirect('?ctrl=admin&action=users');
            return;
        }

        require __DIR__ . '/../views/backoffice/admin/user_form.php';
    }

    /* ── Edit User GET/POST ────────────────────────────────── */
    public function adminShowUser(): void
    {
        requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            require __DIR__ . '/../views/frontoffice/404.php';
            return;
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        
        // Fetch profile details if it's a candidat
        $profil = null;
        if ($user['role'] === 'candidat') {
            require_once __DIR__ . '/../models/Profil.php';
            $profilModel = new Profil();
            $profil = $profilModel->getByUserId($user['id']);
        }

        require __DIR__ . '/../views/backoffice/admin/user_show.php';
    }

    public function adminEditUser(): void
    {
        requireRole('admin');
        $lang = $_SESSION['lang'] ?? 'fr';
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            http_response_code(404);
            require __DIR__ . '/../views/frontoffice/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $email = sanitize($_POST['email'] ?? '');

            // Check email unique for other users
            $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE email = ? AND id != ? LIMIT 1");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                flash('error', $lang === 'fr' ? 'Cet email est déjà utilisé par un autre utilisateur.' : 'This email is already in use by another user.');
                redirect('?ctrl=admin&action=adminEditUser&id=' . $id);
                return;
            }

            $data = [
                'prenom'      => sanitize($_POST['prenom'] ?? ''),
                'nom'         => sanitize($_POST['nom'] ?? ''),
                'email'       => $email,
                'role'        => sanitize($_POST['role'] ?? 'candidat'),
                'statut'      => sanitize($_POST['statut'] ?? 'actif'),
                'nom_societe' => sanitize($_POST['nom_societe'] ?? null),
                'secteur'     => sanitize($_POST['secteur'] ?? null),
            ];

            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }

            $this->userModel->update($id, $data);
            flash('success', __('gen.saved'));
            redirect('?ctrl=admin&action=users');
            return;
        }

        require __DIR__ . '/../views/backoffice/admin/user_form.php';
    }

    /* ── Create Offre GET/POST ─────────────────────────────── */
    public function adminCreateOffre(): void
    {
        requireRole('admin');
        $lang = $_SESSION['lang'] ?? 'fr';
        $companies = $this->userModel->findAllBy('role', 'societe');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $this->offreModel->insert([
                'societe_id'        => (int)($_POST['societe_id'] ?? 0),
                'titre'             => sanitize($_POST['titre'] ?? ''),
                'description'       => $_POST['description'] ?? '',
                'localisation'      => sanitize($_POST['localisation'] ?? ''),
                'type_contrat'      => sanitize($_POST['type_contrat'] ?? 'CDI'),
                'salaire'           => sanitize($_POST['salaire'] ?? ''),
                'experience_requise'=> sanitize($_POST['experience_requise'] ?? ''),
                'statut'            => sanitize($_POST['statut'] ?? 'active'),
                'date_expiration'   => !empty($_POST['date_expiration']) ? $_POST['date_expiration'] : null,
            ]);

            flash('success', __('gen.saved'));
            redirect('?ctrl=admin&action=offres');
            return;
        }

        require __DIR__ . '/../views/backoffice/admin/offre_form.php';
    }

    /* ── Edit Offre GET/POST ───────────────────────────────── */
    public function adminEditOffre(): void
    {
        requireRole('admin');
        $lang = $_SESSION['lang'] ?? 'fr';
        $id = (int)($_GET['id'] ?? 0);
        $offre = $this->offreModel->findById($id);

        if (!$offre) {
            http_response_code(404);
            require __DIR__ . '/../views/frontoffice/404.php';
            return;
        }

        $companies = $this->userModel->findAllBy('role', 'societe');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $this->offreModel->update($id, [
                'societe_id'        => (int)($_POST['societe_id'] ?? 0),
                'titre'             => sanitize($_POST['titre'] ?? ''),
                'description'       => $_POST['description'] ?? '',
                'localisation'      => sanitize($_POST['localisation'] ?? ''),
                'type_contrat'      => sanitize($_POST['type_contrat'] ?? 'CDI'),
                'salaire'           => sanitize($_POST['salaire'] ?? ''),
                'experience_requise'=> sanitize($_POST['experience_requise'] ?? ''),
                'statut'            => sanitize($_POST['statut'] ?? 'active'),
                'date_expiration'   => !empty($_POST['date_expiration']) ? $_POST['date_expiration'] : null,
            ]);

            flash('success', __('gen.saved'));
            redirect('?ctrl=admin&action=offres');
            return;
        }

        require __DIR__ . '/../views/backoffice/admin/offre_form.php';
    }

    /* ── Delete Offre POST ─────────────────────────────────── */
    public function adminDeleteOffre(): void
    {
        requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->offreModel->delete($id);
            flash('success', __('gen.deleted'));
        }
        redirect('?ctrl=admin&action=offres');
    }
}
