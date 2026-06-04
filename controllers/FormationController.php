<?php
require_once __DIR__ . '/../models/Formation.php';
require_once __DIR__ . '/../models/CV.php';

class FormationController
{
    private Formation $formationModel;
    private CV        $cvModel;

    public function __construct()
    {
        $this->formationModel = new Formation();
        $this->cvModel        = new CV();
    }

    /* ── Catalog (candidat) ────────────────────────────────── */
    public function index(): void
    {
        requireRole('candidat');
        $user = auth();
        $all  = $this->formationModel->findAll('created_at DESC');

        $enrolled    = $this->formationModel->getEnrolled($user['id']);
        $enrolledIds = array_column($enrolled, 'formation_id');

        // Merge progress info into $all
        $progressMap = [];
        foreach ($enrolled as $e) {
            $progressMap[$e['formation_id']] = $e;
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/formations/index.php';
    }

    /* ── Enroll POST ───────────────────────────────────────── */
    public function enroll(): void
    {
        requireRole('candidat');
        verify_csrf();
        $userId      = auth()['id'];
        $formationId = (int)($_POST['formation_id'] ?? 0);

        if ($formationId) {
            $exists = $this->formationModel->isEnrolled($userId, $formationId);
            if (!$exists) {
                $this->formationModel->enroll($userId, $formationId);
                flash('success', __('formation.enrolled'));
            }
        }
        redirect('?ctrl=formation&action=index');
    }

    /* ── Update Progress POST ──────────────────────────────── */
    public function updateProgress(): void
    {
        requireRole('candidat');
        verify_csrf();
        $userId      = auth()['id'];
        $formationId = (int)($_POST['formation_id'] ?? 0);
        $progression = max(0, min(100, (int)($_POST['progression'] ?? 0)));
        $statut      = $progression >= 100 ? 'termine' : ($progression > 0 ? 'en_cours' : 'non_commence');

        if ($formationId) {
            $updates = ['progression' => $progression, 'statut' => $statut];
            if ($statut === 'en_cours' && !$this->formationModel->hasStartDate($userId, $formationId)) {
                $updates['date_debut'] = date('Y-m-d H:i:s');
            }
            if ($statut === 'termine') {
                $updates['date_fin'] = date('Y-m-d H:i:s');
            }
            $this->formationModel->updateProgress($userId, $formationId, $updates);
        }
        redirect('?ctrl=formation&action=index');
    }

    /* ── Admin: List ───────────────────────────────────────── */
    public function adminIndex(): void
    {
        requireRole('admin');
        $formations = $this->formationModel->findAll('created_at DESC');
        $lang       = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/admin/formations.php';
    }

    /* ── Admin: Create GET/POST ────────────────────────────── */
    public function adminCreate(): void
    {
        requireRole('admin');
        $lang = $_SESSION['lang'] ?? 'fr';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $titre  = sanitize($_POST['titre'] ?? '');
            if (!$titre) {
                flash('error', __('gen.required'));
                redirect('?ctrl=formation&action=adminCreate');
                return;
            }
            $skills = array_filter(array_map('trim', explode(',', $_POST['competences_cibles'] ?? '')));
            $this->formationModel->insert([
                'titre'             => $titre,
                'description'       => sanitize($_POST['description'] ?? ''),
                'url'               => sanitize($_POST['url'] ?? ''),
                'plateforme'        => sanitize($_POST['plateforme'] ?? ''),
                'competences_cibles'=> json_encode(array_values($skills)),
                'duree'             => sanitize($_POST['duree'] ?? ''),
                'niveau'            => sanitize($_POST['niveau'] ?? 'intermediaire'),
                'gratuit'           => (int)($_POST['gratuit'] ?? 1),
            ]);
            flash('success', __('gen.saved'));
            redirect('?ctrl=formation&action=adminIndex');
            return;
        }

        require __DIR__ . '/../views/backoffice/admin/formation_form.php';
    }

    /* ── Admin: Edit GET/POST ──────────────────────────────── */
    public function adminEdit(): void
    {
        requireRole('admin');
        $lang = $_SESSION['lang'] ?? 'fr';
        $id = (int)($_GET['id'] ?? 0);
        $formation = $this->formationModel->findById($id);

        if (!$formation) {
            http_response_code(404);
            require __DIR__ . '/../views/frontoffice/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $titre = sanitize($_POST['titre'] ?? '');
            if (!$titre) {
                flash('error', __('gen.required'));
                redirect("?ctrl=formation&action=adminEdit&id={$id}");
                return;
            }
            $skills = array_filter(array_map('trim', explode(',', $_POST['competences_cibles'] ?? '')));
            $this->formationModel->update($id, [
                'titre'             => $titre,
                'description'       => sanitize($_POST['description'] ?? ''),
                'url'               => sanitize($_POST['url'] ?? ''),
                'plateforme'        => sanitize($_POST['plateforme'] ?? ''),
                'competences_cibles'=> json_encode(array_values($skills)),
                'duree'             => sanitize($_POST['duree'] ?? ''),
                'niveau'            => sanitize($_POST['niveau'] ?? 'intermediaire'),
                'gratuit'           => (int)($_POST['gratuit'] ?? 1),
            ]);
            flash('success', __('gen.saved'));
            redirect('?ctrl=formation&action=adminIndex');
            return;
        }

        require __DIR__ . '/../views/backoffice/admin/formation_form.php';
    }

    /* ── Admin: Delete POST ────────────────────────────────── */
    public function adminDelete(): void
    {
        requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->formationModel->delete($id);
            flash('success', __('gen.deleted'));
        }
        redirect('?ctrl=formation&action=adminIndex');
    }
}
