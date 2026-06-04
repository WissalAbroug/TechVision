<?php
require_once __DIR__ . '/../models/Offre.php';
require_once __DIR__ . '/../models/Requirement.php';
require_once __DIR__ . '/../models/Candidature.php';
require_once __DIR__ . '/../models/CV.php';
require_once __DIR__ . '/../models/Favoris.php';

class OffreController
{
    private Offre       $offreModel;
    private Requirement $reqModel;
    private Candidature $candModel;
    private CV          $cvModel;
    private Favoris     $favorisModel;

    public function __construct()
    {
        $this->offreModel   = new Offre();
        $this->reqModel     = new Requirement();
        $this->candModel    = new Candidature();
        $this->cvModel      = new CV();
        $this->favorisModel = new Favoris();
    }

    /* ── Public job listing ────────────────────────────────── */
    public function index(): void
    {
        $filters = [
            'q'           => sanitize($_GET['q']           ?? ''),
            'type_contrat'=> sanitize($_GET['type_contrat']?? ''),
            'localisation'=> sanitize($_GET['localisation'] ?? ''),
        ];

        $offres = $this->offreModel->getActive($filters);

        // If candidate logged in, compute matching for each offer
        if (isLoggedIn() && hasRole('candidat')) {
            $cv = $this->cvModel->getByUserId(auth()['id']);
            $appliedIds = array_column(
                $this->candModel->getByUserWithOffre(auth()['id']),
                'offre_id'
            );

            $favIds = array_column(
                $this->favorisModel->findAllBy('user_id', auth()['id'], 'id DESC'),
                'offre_id'
            );

            foreach ($offres as &$o) {
                $reqs = $this->reqModel->findAllBy('offre_id', $o['id']);
                $match = $this->calculateMatching($cv, $reqs);
                $o['score_matching'] = $match['score'];
                $o['applied']        = in_array($o['id'], $appliedIds);
                $o['favorited']      = in_array($o['id'], $favIds);
            }
            unset($o);

            // Sort by matching score descending
            usort($offres, fn($a, $b) => $b['score_matching'] <=> $a['score_matching']);
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/offres/index.php';
    }

    /* ── Offer detail ──────────────────────────────────────── */
    public function show(): void
    {
        $id    = (int)($_GET['id'] ?? 0);
        $offre = $this->offreModel->getWithSociete($id);

        if (!$offre) {
            http_response_code(404);
            require __DIR__ . '/../views/frontoffice/404.php';
            return;
        }

        $requirements = $this->reqModel->findAllBy('offre_id', $id);
        $matching = ['score' => 0, 'found' => [], 'missing' => []];
        $applied  = false;

        $favorited = false;
        if (isLoggedIn() && hasRole('candidat')) {
            $cv        = $this->cvModel->getByUserId(auth()['id']);
            $matching  = $this->calculateMatching($cv, $requirements);
            $applied   = $this->candModel->hasApplied(auth()['id'], $id);
            $favorited = $this->favorisModel->isFavorited(auth()['id'], $id);
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/offres/show.php';
    }

    /* ── Apply form GET ────────────────────────────────────── */
    public function apply(): void
    {
        requireRole('candidat');
        $id    = (int)($_GET['id'] ?? 0);
        $offre = $this->offreModel->getWithSociete($id);

        if (!$offre) {
            flash('error', __('gen.not_found'));
            redirect('?ctrl=offre&action=index');
            return;
        }

        // Already applied?
        if ($this->candModel->hasApplied(auth()['id'], $id)) {
            flash('error', __('cand.already_applied'));
            redirect("?ctrl=offre&action=show&id={$id}");
            return;
        }

        $cv       = $this->cvModel->getByUserId(auth()['id']);
        $reqs     = $this->reqModel->findAllBy('offre_id', $id);
        $matching = $this->calculateMatching($cv, $reqs);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $this->storeApplication($id, $matching);
            return;
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/offres/apply.php';
    }

    /* ── Store Application ─────────────────────────────────── */
    private function storeApplication(int $offreId, array $matching): void
    {
        $userId = auth()['id'];
        $this->candModel->insert([
            'user_id'               => $userId,
            'offre_id'              => $offreId,
            'lettre_motivation'     => sanitize($_POST['lettre_motivation'] ?? ''),
            'statut'                => 'envoyee',
            'score_matching'        => $matching['score'],
            'competences_manquantes'=> json_encode($matching['missing']),
            'date_candidature'      => date('Y-m-d H:i:s'),
        ]);

        flash('success', __('cand.apply_success'));
        redirect('?ctrl=candidature&action=index');
    }

    /* ── Matching Algorithm ────────────────────────────────── */
    public function calculateMatching(?array $cv, array $requirements): array
    {
        if (!$cv || empty($requirements)) {
            return ['score' => 0, 'found' => [], 'missing' => []];
        }

        $userSkills = array_map('strtolower', $cv['competences'] ?? []);

        // Also extract skills from experience descriptions
        foreach ($cv['experiences'] ?? [] as $exp) {
            if (!empty($exp['desc'])) {
                preg_match_all('/\b([A-Za-z\+\#\.]{2,})\b/', $exp['desc'], $m);
                $userSkills = array_merge($userSkills, array_map('strtolower', $m[1]));
            }
        }
        $userSkills = array_unique($userSkills);

        $found   = [];
        $missing = [];
        $totalWeight   = 0;
        $matchedWeight = 0;

        foreach ($requirements as $req) {
            $skill      = strtolower($req['competence']);
            $weight     = $req['obligatoire'] ? 2 : 1;
            $levelBonus = ['debutant' => 1.0, 'intermediaire' => 1.5, 'expert' => 2.0][$req['niveau']] ?? 1.0;
            $w          = $weight * $levelBonus;

            $totalWeight += $w;
            if (in_array($skill, $userSkills)) {
                $matchedWeight += $w;
                $found[]        = $req['competence'];
            } else {
                $missing[] = $req['competence'];
            }
        }

        $score = $totalWeight > 0 ? (int)round(($matchedWeight / $totalWeight) * 100) : 0;
        return [
            'score'   => $score,
            'found'   => $found,
            'missing' => $missing,
        ];
    }

    /* ── Toggle Favoris POST (AJAX or redirect) ──────────────── */
    public function toggleFavoris(): void
    {
        requireRole('candidat');
        verify_csrf();
        $offreId = (int)($_POST['offre_id'] ?? 0);
        if (!$offreId) {
            if ($this->isAjax()) { echo json_encode(['error' => 'invalid']); exit; }
            redirect('?ctrl=offre&action=index');
            return;
        }

        $isFav = $this->favorisModel->toggle(auth()['id'], $offreId);

        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['favorited' => $isFav, 'offre_id' => $offreId]);
            exit;
        }

        $ref = $_SERVER['HTTP_REFERER'] ?? "?ctrl=offre&action=index";
        redirect($ref);
    }

    /* ── Mes Favoris ─────────────────────────────────────────── */
    public function favoris(): void
    {
        requireRole('candidat');
        $offres = $this->favorisModel->getByUser(auth()['id']);
        $lang   = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/offres/favoris.php';
    }

    private function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'
            || ($_SERVER['HTTP_ACCEPT'] ?? '') === 'application/json';
    }
}
