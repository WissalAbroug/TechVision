<?php
require_once __DIR__ . '/../../controllers/UserController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

// MODE DÉVELOPPEMENT : Désactiver la vérification de connexion
// Décommentez en PRODUCTION
/*
if (!$userController->isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}
*/

// Vérifier l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ID utilisateur manquant";
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Supprimer l'utilisateur
$result = $userController->deleteUser($id);

if ($result['success']) {
    $_SESSION['success_message'] = $result['message'];
} else {
    $_SESSION['error_message'] = $result['message'];
}

header("Location: index.php");
exit();
?>