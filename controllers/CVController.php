<?php
require_once __DIR__ . '/../models/CV.php';
require_once __DIR__ . '/../models/Profil.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/QuizResult.php';

class CVController
{
    private CV     $cvModel;
    private Profil $profilModel;

    public function __construct()
    {
        $this->cvModel     = new CV();
        $this->profilModel = new Profil();
    }

    /* ── Index : redirect to show or create ───────────────── */
    public function index(): void
    {
        requireRole('candidat');
        $cv = $this->cvModel->getByUserId(auth()['id']);
        if ($cv) {
            redirect('?ctrl=cv&action=show');
        } else {
            redirect('?ctrl=cv&action=create');
        }
    }

    /* ── Create Form ───────────────────────────────────────── */
    public function create(): void
    {
        requireRole('candidat');
        $user   = auth();
        $profil = $this->profilModel->getByUserId($user['id']) ?? [];
        $lang   = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/cv/create.php';
    }

    /* ── Store POST ────────────────────────────────────────── */
    public function store(): void
    {
        requireRole('candidat');
        verify_csrf();

        $userId = auth()['id'];
        $parsed = $this->parseCVDataFromPost($userId);

        if (!$parsed['cv']['titre']) {
            flash('error', __('gen.required'));
            redirect('?ctrl=cv&action=create');
            return;
        }

        // Delete old CV if exists
        $old = $this->cvModel->getByUserId($userId);
        if ($old) {
            $this->cvModel->delete($old['id']);
        }

        // Insert new CV
        $this->cvModel->insert($parsed['cv']);

        // Update Profil
        $profil = $this->profilModel->getByUserId($userId);
        if ($profil) {
            $this->profilModel->update($profil['id'], $parsed['profil']);
        } else {
            $parsed['profil']['user_id'] = $userId;
            $this->profilModel->insert($parsed['profil']);
        }

        // Update User (nom, prenom)
        if ($parsed['user']['nom'] && $parsed['user']['prenom']) {
            $userModel = new User();
            $userModel->update($userId, $parsed['user']);
            // update session partially
            $_SESSION['user']['nom'] = $parsed['user']['nom'];
            $_SESSION['user']['prenom'] = $parsed['user']['prenom'];
        }

        flash('success', __('cv.saved'));
        redirect('?ctrl=cv&action=show');
    }

    /* ── Show ──────────────────────────────────────────────── */
    public function show(): void
    {
        requireRole('candidat');
        $user   = auth();
        $cv     = $this->cvModel->getByUserId($user['id']);
        $profil = $this->profilModel->getByUserId($user['id']) ?? [];

        if (!$cv) {
            flash('error', __('cv.none'));
            redirect('?ctrl=cv&action=create');
            return;
        }

        $quizResultModel = new QuizResult();
        $quizResults = $quizResultModel->getResultsByUser($user['id']);

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/cv/show.php';
    }

    /* ── Edit Form GET ─────────────────────────────────────── */
    public function edit(): void
    {
        requireRole('candidat');
        $user   = auth();
        $cv     = $this->cvModel->getByUserId($user['id']);
        $profil = $this->profilModel->getByUserId($user['id']) ?? [];

        if (!$cv) {
            redirect('?ctrl=cv&action=create');
            return;
        }

        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/cv/edit.php';
    }

    /* ── Update POST ───────────────────────────────────────── */
    public function update(): void
    {
        requireRole('candidat');
        verify_csrf();
        $userId = auth()['id'];

        $cv = $this->cvModel->getByUserId($userId);
        if (!$cv) {
            redirect('?ctrl=cv&action=create');
            return;
        }

        $parsed = $this->parseCVDataFromPost($userId);

        if (!$parsed['cv']['titre']) {
            flash('error', __('gen.required'));
            redirect('?ctrl=cv&action=edit');
            return;
        }

        // Update CV
        $cvData = $parsed['cv'];
        unset($cvData['user_id']); // don't update user_id
        $this->cvModel->update($cv['id'], $cvData);

        // Update Profil
        $profil = $this->profilModel->getByUserId($userId);
        if ($profil) {
            $this->profilModel->update($profil['id'], $parsed['profil']);
        } else {
            $parsed['profil']['user_id'] = $userId;
            $this->profilModel->insert($parsed['profil']);
        }

        // Update User (nom, prenom)
        if ($parsed['user']['nom'] && $parsed['user']['prenom']) {
            $userModel = new User();
            $userModel->update($userId, $parsed['user']);
            $_SESSION['user']['nom'] = $parsed['user']['nom'];
            $_SESSION['user']['prenom'] = $parsed['user']['prenom'];
        }

        flash('success', __('cv.saved'));
        redirect('?ctrl=cv&action=show');
    }

    /* ── Download PDF ──────────────────────────────────────── */
    public function download(): void
    {
        requireRole('candidat');
        $user   = auth();
        $cv     = $this->cvModel->getByUserId($user['id']);
        $profil = $this->profilModel->getByUserId($user['id']) ?? [];

        if (!$cv) {
            flash('error', __('cv.none'));
            redirect('?ctrl=cv&action=create');
            return;
        }

        // Try Dompdf
        $composerAutoload = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($composerAutoload)) {
            require_once $composerAutoload;
            ob_start();
            require __DIR__ . '/../views/frontoffice/cv/pdf_template.php';
            $html = ob_get_clean();

            $dompdf = new Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'CV_' . sanitize($user['prenom']) . '_' . sanitize($user['nom']) . '.pdf';
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;
        }

        // Fallback: render HTML for browser print
        ob_start();
        require __DIR__ . '/../views/frontoffice/cv/pdf_template.php';
        $html = ob_get_clean();
        echo $html . '<script>window.print();</script>';
        exit;
    }

    /* ── Helper: Parse repeated POST fields ────────────────── */
    private function parseRepeatedField(array $fields): array
    {
        $firstKey = array_key_first($fields);
        $count    = count($fields[$firstKey]);
        $result   = [];

        for ($i = 0; $i < $count; $i++) {
            $row = [];
            foreach ($fields as $key => $values) {
                $row[$key] = sanitize($values[$i] ?? '');
            }
            // Skip entirely empty rows
            $vals = array_filter($row);
            if (!empty($vals)) {
                $result[] = $row;
            }
        }
        return $result;
    }

    private function parseCVDataFromPost(int $userId): array
    {
        $titre = sanitize($_POST['titre'] ?? '');
        $resume = sanitize($_POST['resume'] ?? '');
        $template = sanitize($_POST['template'] ?? 'modern');

        // Chips
        $compRaw = sanitize($_POST['competences_raw'] ?? '');
        $competences = array_values(array_filter(array_map('trim', explode(',', $compRaw))));

        $softRaw = sanitize($_POST['soft_skills_raw'] ?? '');
        $softSkills = array_values(array_filter(array_map('trim', explode(',', $softRaw))));

        $intRaw = sanitize($_POST['interets_raw'] ?? '');
        $centresInteret = array_values(array_filter(array_map('trim', explode(',', $intRaw))));

        // Repeated fields
        $experiences = $this->parseRepeatedField([
            'poste'   => $_POST['exp_poste']   ?? [],
            'societe' => $_POST['exp_societe'] ?? [],
            'debut'   => $_POST['exp_debut']   ?? [],
            'fin'     => $_POST['exp_fin']     ?? [],
            'desc'    => $_POST['exp_desc']    ?? [],
        ]);
        $educations = $this->parseRepeatedField([
            'diplome'       => $_POST['edu_diplome']       ?? [],
            'etablissement' => $_POST['edu_etablissement'] ?? [],
            'annee'         => $_POST['edu_annee']         ?? [],
        ]);
        $langues = $this->parseRepeatedField([
            'langue' => $_POST['langue_nom']    ?? [],
            'niveau' => $_POST['langue_niveau'] ?? [],
        ]);
        $certifications = $this->parseRepeatedField([
            'nom'       => $_POST['cert_nom']       ?? [],
            'organisme' => $_POST['cert_organisme'] ?? [],
            'date'      => $_POST['cert_date']      ?? [],
        ]);
        $projets = $this->parseRepeatedField([
            'titre' => $_POST['proj_titre'] ?? [],
            'desc'  => $_POST['proj_desc']  ?? [],
            'techs' => $_POST['proj_techs'] ?? [],
            'role'  => $_POST['proj_role']  ?? [],
        ]);
        $realisations = $this->parseRepeatedField([
            'titre' => $_POST['real_titre'] ?? [],
            'annee' => $_POST['real_annee'] ?? [],
            'desc'  => $_POST['real_desc']  ?? [],
        ]);
        $formationsComp = $this->parseRepeatedField([
            'titre'     => $_POST['formc_titre']     ?? [],
            'organisme' => $_POST['formc_organisme'] ?? [],
            'annee'     => $_POST['formc_annee']     ?? [],
        ]);

        $reseaux = [
            'linkedin'      => sanitize($_POST['res_linkedin'] ?? ''),
            'github'        => sanitize($_POST['res_github'] ?? ''),
            'behance'       => sanitize($_POST['res_behance'] ?? ''),
            'dribbble'      => sanitize($_POST['res_dribbble'] ?? ''),
            'kaggle'        => sanitize($_POST['res_kaggle'] ?? ''),
            'stackoverflow' => sanitize($_POST['res_stackoverflow'] ?? ''),
        ];

        return [
            'cv' => [
                'user_id'     => $userId,
                'titre'       => $titre,
                'resume'      => $resume,
                'competences' => json_encode($competences),
                'soft_skills' => json_encode($softSkills),
                'experiences' => json_encode($experiences),
                'educations'  => json_encode($educations),
                'langues'     => json_encode($langues),
                'certifications' => json_encode($certifications),
                'projets'     => json_encode($projets),
                'realisations'=> json_encode($realisations),
                'formations_complementaires' => json_encode($formationsComp),
                'reseaux'     => json_encode($reseaux),
                'centres_interet' => json_encode($centresInteret),
                'template'    => $template,
            ],
            'profil' => [
                'competences'    => json_encode($competences),
                'telephone'      => sanitize($_POST['telephone'] ?? ''),
                'adresse'        => sanitize($_POST['adresse'] ?? ''),
                'ville'          => sanitize($_POST['ville'] ?? ''),
                'pays'           => sanitize($_POST['pays'] ?? ''),
                'nationalite'    => sanitize($_POST['nationalite'] ?? ''),
                'date_naissance' => !empty($_POST['date_naissance']) ? sanitize($_POST['date_naissance']) : null,
                'permis'         => sanitize($_POST['permis'] ?? ''),
                'site_web'       => sanitize($_POST['site_web'] ?? ''),
                'portfolio'      => sanitize($_POST['portfolio'] ?? ''),
                'titre'          => sanitize($_POST['titre_pro'] ?? ''),
                'bio'            => $resume,
                'linkedin'       => sanitize($_POST['res_linkedin'] ?? ''),
                'github'         => sanitize($_POST['res_github'] ?? ''),
            ],
            'user' => [
                'nom'    => sanitize($_POST['nom'] ?? ''),
                'prenom' => sanitize($_POST['prenom'] ?? ''),
            ]
        ];
    }
}
