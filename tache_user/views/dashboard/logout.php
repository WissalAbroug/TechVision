<?php
require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();
$result = $userController->logout();

// Rediriger vers la page de connexion
header("Location: ../auth/login.php?message=" . urlencode("Déconnexion réussie"));
exit();
?>