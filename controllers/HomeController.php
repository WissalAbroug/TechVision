<?php
require_once __DIR__ . '/../models/Offre.php';
require_once __DIR__ . '/../models/User.php';

class HomeController
{
    public function index(): void
    {
        require_once __DIR__ . '/../models/Offre.php';
        $offreModel = new Offre();
        $userModel  = new User();
        $stats = [
            'users'   => $userModel->count(),
            'offres'  => $offreModel->countActive(),
            'placed'  => 847,
            'societes'=> $userModel->count("role = 'societe'"),
        ];
        require __DIR__ . '/../views/frontoffice/home.php';
    }
}
