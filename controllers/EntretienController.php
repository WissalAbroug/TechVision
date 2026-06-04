<?php
require_once __DIR__ . '/../models/Entretien.php';
require_once __DIR__ . '/../models/Candidature.php';
require_once __DIR__ . '/../models/Offre.php';

class EntretienController
{
    private Entretien   $entretienModel;
    private Candidature $candModel;
    private Offre       $offreModel;

    // Bank of simulation questions by category
    private array $questionBank = [
        ['question'=>'Parlez-moi de vous et de votre parcours professionnel.', 'categorie'=>'Présentation', 'difficulte'=>'facile'],
        ['question'=>'Pourquoi êtes-vous intéressé(e) par ce poste ?', 'categorie'=>'Motivation', 'difficulte'=>'facile'],
        ['question'=>'Quelles sont vos principales forces et faiblesses ?', 'categorie'=>'Personnalité', 'difficulte'=>'facile'],
        ['question'=>'Décrivez un projet technique dont vous êtes particulièrement fier(e).', 'categorie'=>'Expérience', 'difficulte'=>'intermédiaire'],
        ['question'=>'Comment gérez-vous les conflits au sein d\'une équipe ?', 'categorie'=>'Soft skills', 'difficulte'=>'intermédiaire'],
        ['question'=>'Quelle est votre approche pour résoudre un bug complexe en production ?', 'categorie'=>'Technique', 'difficulte'=>'difficile'],
        ['question'=>'Où vous voyez-vous dans 5 ans ?', 'categorie'=>'Ambitions', 'difficulte'=>'facile'],
        ['question'=>'Décrivez une situation où vous avez dû apprendre rapidement une nouvelle technologie.', 'categorie'=>'Adaptabilité', 'difficulte'=>'intermédiaire'],
        ['question'=>'Comment priorisez-vous vos tâches lorsque vous avez plusieurs urgences ?', 'categorie'=>'Organisation', 'difficulte'=>'intermédiaire'],
        ['question'=>'Avez-vous des questions sur le poste ou l\'entreprise ?', 'categorie'=>'Curiosité', 'difficulte'=>'facile'],
    ];

    public function __construct()
    {
        $this->entretienModel = new Entretien();
        $this->candModel      = new Candidature();
        $this->offreModel     = new Offre();
    }

    /* ── Candidate: List Interviews ────────────────────────── */
    public function index(): void
    {
        requireRole('candidat');
        $user       = auth();
        $entretiens = $this->entretienModel->getForUser($user['id']);
        $lang       = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/entretiens/index.php';
    }

    /* ── Simulate GET ──────────────────────────────────────── */
    public function simulate(): void
    {
        requireRole('candidat');
        $user       = auth();
        $id         = (int)($_GET['id'] ?? 0);
        $entretien  = $this->entretienModel->getWithDetails($id);

        if (!$entretien || $entretien['candidat_user_id'] !== $user['id']) {
            flash('error', __('gen.forbidden'));
            redirect('?ctrl=entretien&action=index');
            return;
        }

        // Use question bank (shuffle a subset)
        $questions = $this->questionBank;
        shuffle($questions);
        $questions = array_slice($questions, 0, 8);
        // Add id for form fields
        foreach ($questions as $i => &$q) {
            $q['id'] = $i + 1;
        }
        unset($q);

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/entretiens/simulate.php';
    }

    /* ── Submit Simulation POST ────────────────────────────── */
    public function submitSimulation(): void
    {
        requireRole('candidat');
        verify_csrf();

        $user          = auth();
        $entretienId   = (int)($_POST['entretien_id']    ?? 0);
        $totalQuestions = (int)($_POST['total_questions'] ?? 8);
        $entretien     = $this->entretienModel->getWithDetails($entretienId);

        if (!$entretien || $entretien['candidat_user_id'] !== $user['id']) {
            flash('error', __('gen.forbidden'));
            redirect('?ctrl=entretien&action=index');
            return;
        }

        // Score calculation: count non-empty answers × weight
        $answeredCount = 0;
        $totalLength   = 0;
        foreach ($_POST as $key => $value) {
            if (str_starts_with($key, 'reponse_') && !empty(trim($value))) {
                $answeredCount++;
                $totalLength += strlen(trim($value));
            }
        }

        // Formula: % answers given (50%) + quality proxy via avg length (50%)
        $completionScore = $totalQuestions > 0 ? ($answeredCount / $totalQuestions) * 50 : 0;
        $avgLength       = $answeredCount > 0 ? $totalLength / $answeredCount : 0;
        $qualityScore    = min(50, ($avgLength / 300) * 50); // 300 chars = perfect quality
        $score           = (int)round($completionScore + $qualityScore);
        $score           = max(10, min(100, $score));

        // Generate feedback
        $feedback = $this->generateFeedback($score, $answeredCount, $totalQuestions, $user['lang'] ?? 'fr');

        // Update entretien
        $this->entretienModel->update($entretienId, [
            'statut'           => 'effectue',
            'score_simulation' => $score,
            'feedback'         => $feedback,
        ]);

        flash('success', __('entretien.sim_done'));
        redirect("?ctrl=entretien&action=result&id={$entretienId}");
    }

    /* ── Result ────────────────────────────────────────────── */
    public function result(): void
    {
        requireRole('candidat');
        $user      = auth();
        $id        = (int)($_GET['id'] ?? 0);
        $entretien = $this->entretienModel->getWithDetails($id);

        if (!$entretien || $entretien['candidat_user_id'] !== $user['id']) {
            flash('error', __('gen.forbidden'));
            redirect('?ctrl=entretien&action=index');
            return;
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/entretiens/result.php';
    }

    /* ── Societe: Schedule Interview Form ──────────────────── */
    public function create(): void
    {
        requireRole('societe');
        $candId = (int)($_GET['candidature_id'] ?? 0);
        $cand   = $this->candModel->queryOne(
            "SELECT c.*, o.titre AS offre_titre FROM candidatures c
             JOIN offres o ON c.offre_id = o.id WHERE c.id = ?",
            [$candId]
        );

        if (!$cand) {
            flash('error', __('gen.not_found'));
            redirect('?ctrl=societe&action=candidats');
            return;
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/societe/entretiens/create.php';
    }

    /* ── Societe: Store Interview POST ─────────────────────── */
    public function store(): void
    {
        requireRole('societe');
        verify_csrf();

        $candId = (int)($_POST['candidature_id'] ?? 0);
        $date   = sanitize($_POST['date_entretien'] ?? '');

        if (!$candId || !$date) {
            flash('error', __('gen.required'));
            redirect("?ctrl=entretien&action=create&candidature_id={$candId}");
            return;
        }

        $this->entretienModel->insert([
            'candidature_id'     => $candId,
            'date_entretien'     => $date,
            'type'               => sanitize($_POST['type'] ?? 'visio'),
            'lien_visio'         => sanitize($_POST['lien_visio'] ?? ''),
            'adresse_entretien'  => sanitize($_POST['adresse_entretien'] ?? ''),
            'notes'              => sanitize($_POST['notes'] ?? ''),
            'statut'             => 'planifie',
        ]);

        // Update candidature status to shortlistee
        $this->candModel->update($candId, ['statut' => 'shortlistee']);

        flash('success', __('entretien.scheduled'));
        redirect('?ctrl=entretien&action=societeIndex');
    }

    /* ── Societe: Edit Interview GET ───────────────────────── */
    public function edit(): void
    {
        requireRole('societe');
        $id = (int)($_GET['id'] ?? 0);
        $entretien = $this->entretienModel->getById($id);

        if (!$entretien) {
            flash('error', __('gen.not_found'));
            redirect('?ctrl=entretien&action=societeIndex');
            return;
        }

        $cand = $this->candModel->queryOne(
            "SELECT c.*, o.titre AS offre_titre, u.nom, u.prenom 
             FROM candidatures c
             JOIN offres o ON c.offre_id = o.id 
             JOIN utilisateurs u ON c.candidat_id = u.id
             WHERE c.id = ?",
            [$entretien['candidature_id']]
        );

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/societe/entretiens/edit.php';
    }

    /* ── Societe: Update Interview POST ────────────────────── */
    public function update(): void
    {
        requireRole('societe');
        verify_csrf();

        $id = (int)($_POST['id'] ?? 0);
        $date = sanitize($_POST['date_entretien'] ?? '');

        if (!$id || !$date) {
            flash('error', __('gen.required'));
            redirect("?ctrl=entretien&action=edit&id={$id}");
            return;
        }

        $this->entretienModel->update($id, [
            'date_entretien'     => $date,
            'type'               => sanitize($_POST['type'] ?? 'visio'),
            'lien_visio'         => sanitize($_POST['lien_visio'] ?? ''),
            'adresse_entretien'  => sanitize($_POST['adresse_entretien'] ?? ''),
            'notes'              => sanitize($_POST['notes'] ?? ''),
            'statut'             => sanitize($_POST['statut'] ?? 'planifie'),
        ]);

        flash('success', __('gen.saved'));
        redirect('?ctrl=entretien&action=societeIndex');
    }

    /* ── Delete Interview POST ─────────────────────────────── */
    public function delete(): void
    {
        requireRole('societe');
        verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $this->entretienModel->delete($id);
            flash('success', __('gen.deleted'));
        }
        redirect('?ctrl=entretien&action=societeIndex');
    }

    /* ── Societe: interviews list ──────────────────────────── */
    public function societeIndex(): void
    {
        requireRole('societe');
        $user       = auth();
        $entretiens = $this->entretienModel->getBySociete($user['id']);
        $lang       = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/societe/entretiens/index.php';
    }

    /* ── Admin: all interviews list ────────────────────────── */
    public function adminIndex(): void
    {
        requireRole('admin');
        // Fetch all interviews across all societies
        $entretiens = $this->entretienModel->getAllAdmin();
        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/backoffice/admin/entretiens.php';
    }

    /* ── Generate feedback text ────────────────────────────── */
    private function generateFeedback(int $score, int $answered, int $total, string $lang): string
    {
        $fr = [
            'high'   => "Excellente performance ! Vous avez répondu à {$answered}/{$total} questions avec des réponses détaillées et structurées. Votre score de {$score}% démontre une très bonne préparation. Continuez ainsi pour votre entretien réel.",
            'medium' => "Bonne tentative ! Vous avez répondu à {$answered}/{$total} questions. Avec un score de {$score}%, quelques améliorations sont possibles. Essayez d'approfondir vos réponses avec des exemples concrets (méthode STAR) et pratiquez davantage.",
            'low'    => "Vous avez répondu à {$answered}/{$total} questions et obtenu {$score}%. Il y a place à amélioration. Consultez nos formations recommandées, entraînez-vous avec la méthode STAR (Situation, Tâche, Action, Résultat) et retentez la simulation.",
        ];
        $en = [
            'high'   => "Excellent performance! You answered {$answered}/{$total} questions with detailed, structured responses. Your score of {$score}% shows great preparation. Keep it up for your real interview.",
            'medium' => "Good attempt! You answered {$answered}/{$total} questions. With a score of {$score}%, there is room for improvement. Try to deepen your answers with concrete examples (STAR method) and practice more.",
            'low'    => "You answered {$answered}/{$total} questions and scored {$score}%. There is significant room for improvement. Check our recommended trainings, practice the STAR method (Situation, Task, Action, Result) and retry.",
        ];
        $map  = $lang === 'fr' ? $fr : $en;
        $key  = $score >= 70 ? 'high' : ($score >= 45 ? 'medium' : 'low');
        return $map[$key];
    }
}
