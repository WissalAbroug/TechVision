<?php
require_once __DIR__ . '/../models/Reclamation.php';

class ReclamationController
{
    private Reclamation $reclamationModel;

    public function __construct()
    {
        $this->reclamationModel = new Reclamation();
    }

    /* ── Index ─────────────────────────────────────────────── */
    public function index(): void
    {
        requireAuth();
        $user = auth();
        $lang = $_SESSION['lang'] ?? 'fr';

        if (hasRole('admin')) {
            $reclamations = $this->reclamationModel->getAllWithUser();
        } else {
            $reclamations = $this->reclamationModel->getByUser($user['id']);
        }

        require __DIR__ . '/../views/frontoffice/reclamations/index.php';
    }

    /* ── Create Form GET ───────────────────────────────────── */
    public function create(): void
    {
        requireAuth();
        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/reclamations/create.php';
    }

    /* ── Store POST ────────────────────────────────────────── */
    public function store(): void
    {
        requireAuth();
        verify_csrf();

        $userId = auth()['id'];
        $titre  = sanitize($_POST['titre'] ?? '');
        $desc   = sanitize($_POST['description'] ?? '');

        if (!$titre || !$desc) {
            flash('error', __('gen.required'));
            redirect('?ctrl=reclamation&action=create');
            return;
        }

        $this->reclamationModel->insert([
            'user_id'     => $userId,
            'titre'       => $titre,
            'description' => $desc,
            'categorie'   => sanitize($_POST['categorie'] ?? ''),
            'priorite'    => sanitize($_POST['priorite'] ?? 'normale'),
            'statut'      => 'ouvert',
        ]);

        flash('success', __('recl.sent'));
        redirect('?ctrl=reclamation&action=index');
    }

    /* ── Show Thread ───────────────────────────────────────── */
    public function show(): void
    {
        requireAuth();
        $user = auth();
        $id   = (int)($_GET['id'] ?? 0);
        $recl = $this->reclamationModel->getWithUser($id);

        if (!$recl) {
            http_response_code(404);
            require __DIR__ . '/../views/frontoffice/404.php';
            return;
        }

        // Only owner or admin can view
        if ($recl['user_id'] !== $user['id'] && !hasRole('admin')) {
            flash('error', __('gen.forbidden'));
            redirect('?ctrl=reclamation&action=index');
            return;
        }

        // Load replies
        $recl['reponses'] = $this->reclamationModel->getReplies($id);
        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/reclamations/show.php';
    }

    /* ── Reply POST ────────────────────────────────────────── */
    public function reply(): void
    {
        requireAuth();
        verify_csrf();
        $userId       = auth()['id'];
        $reclamationId = (int)($_POST['reclamation_id'] ?? 0);
        $message      = sanitize($_POST['message'] ?? '');

        if ($message && $reclamationId) {
            $this->reclamationModel->addReply([
                'reclamation_id' => $reclamationId,
                'auteur_id'      => $userId,
                'message'        => $message,
            ]);

            // Auto-change status to en_cours if was ouvert
            $recl = $this->reclamationModel->findById($reclamationId);
            if ($recl && $recl['statut'] === 'ouvert' && hasRole('admin', 'societe')) {
                $this->reclamationModel->update($reclamationId, ['statut' => 'en_cours']);
            }
        }
        redirect("?ctrl=reclamation&action=show&id={$reclamationId}");
    }

    /* ── Update Status POST ────────────────────────────────── */
    public function updateStatut(): void
    {
        requireAuth();
        verify_csrf();
        $id     = (int)($_POST['id'] ?? 0);
        $statut = sanitize($_POST['statut'] ?? '');
        $allowed = ['ouvert','en_cours','resolu','ferme'];

        if ($id && in_array($statut, $allowed)) {
            $recl = $this->reclamationModel->findById($id);
            $user = auth();
            if ($recl && ($recl['user_id'] === $user['id'] || hasRole('admin'))) {
                $this->reclamationModel->update($id, ['statut' => $statut]);
            }
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? "?ctrl=reclamation&action=index";
        redirect($referer);
    }

    /* ── Delete POST (admin) ───────────────────────────────── */
    public function delete(): void
    {
        requireRole('admin');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->reclamationModel->delete($id);
            flash('success', __('gen.deleted'));
        }
        redirect('?ctrl=reclamation&action=index');
    }

    /* ── Edit Form GET (admin) ─────────────────────────────── */
    public function edit(): void
    {
        requireRole('admin');
        $id = (int)($_GET['id'] ?? 0);
        $recl = $this->reclamationModel->findById($id);

        if (!$recl) {
            flash('error', __('gen.not_found'));
            redirect('?ctrl=reclamation&action=index');
            return;
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/reclamations/edit.php';
    }

    /* ── Update POST (admin) ───────────────────────────────── */
    public function update(): void
    {
        requireRole('admin');
        verify_csrf();

        $id = (int)($_POST['id'] ?? 0);
        $titre = sanitize($_POST['titre'] ?? '');
        $desc = sanitize($_POST['description'] ?? '');

        if (!$id || !$titre || !$desc) {
            flash('error', __('gen.required'));
            redirect("?ctrl=reclamation&action=edit&id={$id}");
            return;
        }

        $this->reclamationModel->update($id, [
            'titre'       => $titre,
            'description' => $desc,
            'categorie'   => sanitize($_POST['categorie'] ?? ''),
            'priorite'    => sanitize($_POST['priorite'] ?? 'normale'),
            'statut'      => sanitize($_POST['statut'] ?? 'ouvert'),
        ]);

        flash('success', __('gen.saved'));
        redirect('?ctrl=reclamation&action=index');
    }
}
