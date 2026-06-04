<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Profil.php';
require_once __DIR__ . '/../models/CV.php';
require_once __DIR__ . '/../models/Candidature.php';
require_once __DIR__ . '/../models/Entretien.php';
require_once __DIR__ . '/../models/Formation.php';

class UserController
{
    private User       $userModel;
    private Profil     $profilModel;
    private CV         $cvModel;
    private Candidature $candModel;
    private Entretien  $entretienModel;
    private Formation  $formationModel;

    public function __construct()
    {
        $this->userModel      = new User();
        $this->profilModel    = new Profil();
        $this->cvModel        = new CV();
        $this->candModel      = new Candidature();
        $this->entretienModel = new Entretien();
        $this->formationModel = new Formation();
    }

    /* ── Dashboard ─────────────────────────────────────────── */
    public function dashboard(): void
    {
        requireRole('candidat');
        $user = auth();

        // CV
        $cv = $this->cvModel->getByUserId($user['id']);

        // Profile score (0-100)
        $profil = $this->profilModel->getByUserId($user['id']);
        $profileScore = $this->calculateProfileScore($user, $profil, $cv);

        // Recent candidatures (with offre info)
        $candidatures = $this->candModel->getByUserWithOffre($user['id'], 6);

        // Upcoming interviews
        $upcomingIntvw = $this->entretienModel->getUpcomingForUser($user['id']);

        // Recommended formations
        $recommendedFormations = $this->formationModel->getRecommended($user['id'], 4);

        // User formations enrolled
        $userFormations = $this->formationModel->getEnrolled($user['id']);

        require __DIR__ . '/../views/frontoffice/dashboard.php';
    }

    /* ── Profile Show ──────────────────────────────────────── */
    public function profile(): void
    {
        requireRole('candidat');
        $user   = auth();
        $profil = $this->profilModel->getByUserId($user['id']) ?? [];
        $cv     = $this->cvModel->getByUserId($user['id']);

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/profile/show.php';
    }

    /* ── Profile Edit GET ──────────────────────────────────── */
    public function editProfile(): void
    {
        requireRole('candidat');
        $user   = auth();
        $profil = $this->profilModel->getByUserId($user['id']) ?? [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $this->handleProfileUpdate($user['id'], $profil);
            return;
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/profile/edit.php';
    }

    /* ── Private Helpers ───────────────────────────────────── */
    private function handleProfileUpdate(int $userId, array $profil): void
    {
        $prenom = sanitize($_POST['prenom'] ?? '');
        $nom    = sanitize($_POST['nom']    ?? '');
        $titre  = sanitize($_POST['titre']  ?? '');
        $phone  = sanitize($_POST['telephone'] ?? '');
        $ville  = sanitize($_POST['ville']  ?? '');
        $bio    = sanitize($_POST['bio']    ?? '');
        $linkedin = sanitize($_POST['linkedin'] ?? '');
        $github   = sanitize($_POST['github']   ?? '');

        // Parse competences
        $compStr = sanitize($_POST['competences'] ?? '');
        $competences = array_filter(array_map('trim', explode(',', $compStr)));

        // Photo upload
        $photo = $profil['photo'] ?? null;
        if (!empty($_FILES['photo']['name'])) {
            $ext   = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed) && $_FILES['photo']['size'] < 2 * 1024 * 1024) {
                $dir  = __DIR__ . '/../public/uploads/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $name = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $name)) {
                    $photo = $name;
                }
            }
        }

        // Update user name
        $this->userModel->update($userId, ['prenom' => $prenom, 'nom' => $nom]);

        // Upsert profil
        $profilData = [
            'titre'       => $titre,
            'telephone'   => $phone,
            'ville'       => $ville,
            'bio'         => $bio,
            'linkedin'    => $linkedin,
            'github'      => $github,
            'competences' => json_encode(array_values($competences)),
            'photo'       => $photo,
        ];

        if (!empty($profil['id'])) {
            $this->profilModel->update($profil['id'], $profilData);
        } else {
            $profilData['user_id'] = $userId;
            $this->profilModel->insert($profilData);
        }

        // Refresh session
        $updated = $this->userModel->findById($userId);
        $_SESSION['user'] = $updated;

        flash('success', __('gen.saved'));
        redirect('?ctrl=user&action=profile');
    }

    private function calculateProfileScore(array $user, ?array $profil, ?array $cv): int
    {
        $score = 0;
        if (!empty($user['prenom']))          $score += 10;
        if (!empty($user['nom']))             $score += 10;
        if (!empty($user['email']))           $score += 10;
        if (!empty($profil['titre']))         $score += 10;
        if (!empty($profil['telephone']))     $score += 5;
        if (!empty($profil['ville']))         $score += 5;
        if (!empty($profil['bio']))           $score += 10;
        if (!empty($profil['linkedin']))      $score += 5;
        if ($cv) {
            $score += 15; // has CV
            if (!empty($cv['experiences']))   $score += 10;
            if (!empty($cv['competences']))   $score += 10;
        }
        return min(100, $score);
    }
}
