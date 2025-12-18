<?php
// dashboard.php - Redirection vers le tableau de bord

require_once __DIR__ . '/controllers/UserController.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

// Vérifier si l'utilisateur est connecté
if ($userController->isLoggedIn()) {
    // Rediriger vers le tableau de bord
    header("Location: views/dashboard/index.php");
    exit();
} else {
    // Rediriger vers la page de connexion
    header("Location: views/auth/login.php");
    exit();
}
?>
