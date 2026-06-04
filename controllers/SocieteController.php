<?php
require_once __DIR__ . '/../models/Offre.php';
require_once __DIR__ . '/../models/Candidature.php';
require_once __DIR__ . '/../models/Entretien.php';
require_once __DIR__ . '/../models/Reclamation.php';
require_once __DIR__ . '/../models/Metier.php';
require_once __DIR__ . '/../models/Requirement.php';

class SocieteController
{
    private Offre       $offreModel;
    private Candidature $candModel;
    private Entretien   $entretienModel;
    private Reclamation $reclamationModel;
    private Metier      $metierModel;
    private Requirement $reqModel;

    public function __construct()
    {
        $this->offreModel       = new Offre();
        $this->candModel        = new Candidature();
        $this->entretienModel   = new Entretien();
        $this->reclamationModel = new Reclamation();
        $this->metierModel      = new Metier();
        $this->reqModel         = new Requirement();
    }

    /* ── Dashboard ─────────────────────────────────────────── */
    public function dashboard(): void
    {
        requireRole('societe');
        $user = auth();
        $offres = $this->offreModel->getBySociete($user['id']);

        $stats = [
            'offres'       => count($offres),
            'candidatures' => $this->candModel->countBySociete($user['id']),
            'entretiens'   => $this->entretienModel->countBySociete($user['id']),
            'reclamations' => $this->reclamationModel->count('user_id = ?', [$user['id']]),
        ];

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/societe/dashboard.php';
    }

    /* ── Offers List ───────────────────────────────────────── */
    public function offres(): void
    {
        requireRole('societe');
        $user   = auth();
        $offres = $this->offreModel->getBySociete($user['id']);
        $lang   = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/societe/offres/index.php';
    }

    /* ── Create Offer GET ──────────────────────────────────── */
    public function createOffre(): void
    {
        requireRole('societe');
        $metiers = $this->metierModel->findAll('nom ASC');
        $lang    = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/societe/offres/create.php';
    }

    /* ── Store Offer POST ──────────────────────────────────── */
    public function storeOffre(): void
    {
        requireRole('societe');
        verify_csrf();

        $userId = auth()['id'];
        $titre  = sanitize($_POST['titre'] ?? '');
        $desc   = sanitize($_POST['description'] ?? '');

        if (!$titre || !$desc) {
            flash('error', __('gen.required'));
            redirect('?ctrl=societe&action=createOffre');
            return;
        }

        $expiry = $_POST['date_expiration'] ?? '';
        $data = [
            'societe_id'        => $userId,
            'metier_id'         => intval($_POST['metier_id'] ?? 0) ?: null,
            'titre'             => $titre,
            'description'       => $desc,
            'type_contrat'      => sanitize($_POST['type_contrat'] ?? 'CDI'),
            'localisation'      => sanitize($_POST['localisation'] ?? ''),
            'salaire'           => sanitize($_POST['salaire'] ?? ''),
            'experience_requise'=> sanitize($_POST['experience_requise'] ?? ''),
            'date_expiration'   => $expiry ?: null,
            'statut'            => 'active',
        ];

        $offreId = $this->offreModel->insert($data);

        // Save requirements
        $competences = $_POST['req_competence'] ?? [];
        $niveaux     = $_POST['req_niveau']     ?? [];
        foreach ($competences as $i => $comp) {
            $comp = trim($comp);
            if (!$comp) continue;
            $this->reqModel->insert([
                'offre_id'    => $offreId,
                'competence'  => $comp,
                'niveau'      => sanitize($niveaux[$i] ?? 'intermediaire'),
                'obligatoire' => 1,
            ]);
        }

        flash('success', __('offre.created'));
        redirect('?ctrl=societe&action=offres');
    }

    /* ── Edit Offer GET + POST ─────────────────────────────── */
    public function editOffre(): void
    {
        requireRole('societe');
        $user    = auth();
        $id      = (int)($_GET['id'] ?? 0);
        $offre   = $this->offreModel->findById($id);

        if (!$offre || $offre['societe_id'] !== $user['id']) {
            flash('error', __('gen.forbidden'));
            redirect('?ctrl=societe&action=offres');
            return;
        }

        $metiers      = $this->metierModel->findAll('nom ASC');
        $requirements = $this->reqModel->findAllBy('offre_id', $id);
        $lang         = $_SESSION['lang'] ?? 'fr';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $titre = sanitize($_POST['titre'] ?? '');
            $desc  = sanitize($_POST['description'] ?? '');

            if (!$titre || !$desc) {
                flash('error', __('gen.required'));
                redirect("?ctrl=societe&action=editOffre&id={$id}");
                return;
            }

            $expiry = $_POST['date_expiration'] ?? '';
            $this->offreModel->update($id, [
                'metier_id'          => intval($_POST['metier_id'] ?? 0) ?: null,
                'titre'              => $titre,
                'description'        => $desc,
                'type_contrat'       => sanitize($_POST['type_contrat'] ?? 'CDI'),
                'localisation'       => sanitize($_POST['localisation'] ?? ''),
                'salaire'            => sanitize($_POST['salaire'] ?? ''),
                'experience_requise' => sanitize($_POST['experience_requise'] ?? ''),
                'date_expiration'    => $expiry ?: null,
                'statut'             => sanitize($_POST['statut'] ?? 'active'),
            ]);

            // Refresh requirements
            $this->reqModel->deleteByOffre($id);
            $competences = $_POST['req_competence'] ?? [];
            $niveaux     = $_POST['req_niveau']     ?? [];
            foreach ($competences as $i => $comp) {
                $comp = trim($comp);
                if (!$comp) continue;
                $this->reqModel->insert([
                    'offre_id'   => $id,
                    'competence' => $comp,
                    'niveau'     => sanitize($niveaux[$i] ?? 'intermediaire'),
                    'obligatoire'=> 1,
                ]);
            }

            flash('success', __('gen.saved'));
            redirect('?ctrl=societe&action=offres');
            return;
        }

        require __DIR__ . '/../views/backoffice/societe/offres/edit.php';
    }

    /* ── Delete Offer POST ─────────────────────────────────── */
    public function deleteOffre(): void
    {
        requireRole('societe');
        verify_csrf();
        $user = auth();
        $id   = (int)($_POST['id'] ?? 0);
        $offre = $this->offreModel->findById($id);

        if ($offre && $offre['societe_id'] === $user['id']) {
            $this->offreModel->delete($id);
            flash('success', __('gen.deleted'));
        }

        redirect('?ctrl=societe&action=offres');
    }

    /* ── Candidates ────────────────────────────────────────── */
    public function candidats(): void
    {
        requireRole('societe');
        $user     = auth();
        $offres   = $this->offreModel->getBySociete($user['id']);
        $offreId  = (int)($_GET['offre_id'] ?? 0);
        $candidatures = [];

        if ($offreId) {
            // Ensure the offre belongs to this societe
            $offre = $this->offreModel->findById($offreId);
            if ($offre && $offre['societe_id'] === $user['id']) {
                $candidatures = $this->candModel->getByOffreWithProfile($offreId);
            }
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/societe/candidats.php';
    }

    /* ── Update Candidature Status POST ────────────────────── */
    public function updateCandidature(): void
    {
        requireRole('societe');
        verify_csrf();
        $id     = (int)($_POST['id'] ?? 0);
        $statut = sanitize($_POST['statut'] ?? 'envoyee');
        $allowedStatuts = ['envoyee','vue','shortlistee','refusee','acceptee'];

        if ($id && in_array($statut, $allowedStatuts)) {
            $this->candModel->update($id, ['statut' => $statut]);
        }

        $offreId = (int)($_POST['offre_id'] ?? 0);
        redirect("?ctrl=societe&action=candidats&offre_id={$offreId}");
    }

    /* ── Interviews List ───────────────────────────────────── */
    public function societeIndex(): void
    {
        requireRole('societe');
        $user       = auth();
        $entretiens = $this->entretienModel->getBySociete($user['id']);
        $lang       = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/societe/entretiens/index.php';
    }
}
