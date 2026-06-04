<?php
require_once __DIR__ . '/../models/Candidature.php';
require_once __DIR__ . '/../models/Offre.php';

class CandidatureController
{
    private Candidature $candModel;
    private Offre       $offreModel;

    public function __construct()
    {
        $this->candModel  = new Candidature();
        $this->offreModel = new Offre();
    }

    /* ── My Applications ───────────────────────────────────── */
    public function index(): void
    {
        requireRole('candidat');
        $user = auth();
        $candidatures = $this->candModel->getByUserWithOffre($user['id']);
        $lang = $_SESSION['lang'] ?? 'fr';
        require __DIR__ . '/../views/frontoffice/candidatures/index.php';
    }
}
