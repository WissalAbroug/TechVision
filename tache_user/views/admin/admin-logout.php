<?php
// views/admin/admin-logout.php

require_once __DIR__ . '/../../controllers/AdminController.php';

$adminController = new AdminController();
$result = $adminController->logout();

// Rediriger vers la page de connexion admin
header("Location: ../auth/admin-login.php?message=" . urlencode("Déconnexion administrateur réussie"));
exit();