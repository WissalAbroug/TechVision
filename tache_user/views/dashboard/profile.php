<?php
// views/dashboard/profile.php

require_once __DIR__ . '/../../controllers/UserController.php';

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

// Vérification d'authentification activée
if (!$userController->isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}
    

// Récupérer l'utilisateur actuel (ou créer un utilisateur fictif pour le dev)
$currentUser = $userController->getCurrentUser();
if (!$currentUser) {
    // Utilisateur fictif pour le développement
    $currentUser = [
        'id' => 1,
        'name' => 'Développeur',
        'email' => 'dev@example.com'
    ];
}

$userId = $_SESSION['user_id'];

// Récupérer les informations du profil
$profileResult = $userController->getProfile($userId);
$profile = $profileResult['success'] ? $profileResult['profile'] : null;

// Gestion des formulaires
$passwordError = '';
$passwordSuccess = '';
$photoError = '';
$photoSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Changement de mot de passe
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $passwordError = "Tous les champs sont requis.";
        } elseif ($newPassword !== $confirmPassword) {
            $passwordError = "Les nouveaux mots de passe ne correspondent pas.";
        } elseif (strlen($newPassword) < 8) {
            $passwordError = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
        } else {
            $result = $userController->updatePassword($userId, $currentPassword, $newPassword);
            if ($result['success']) {
                $passwordSuccess = $result['message'];
            } else {
                $passwordError = $result['message'];
            }
        }
    }

    // Upload de photo de profil
    if (isset($_POST['upload_photo'])) {
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];

            // Vérifier le type de fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                $photoError = "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.";
            } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB max
                $photoError = "Le fichier est trop volumineux (max 2MB).";
            } else {
                // Créer le dossier uploads s'il n'existe pas
                $uploadDir = __DIR__ . '/../../assets/uploads/profiles/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Générer un nom de fichier unique
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    // Mettre à jour la base de données
                    $relativePath = 'assets/uploads/profiles/' . $filename;
                    $result = $userController->updateProfilePhoto($userId, $relativePath);

                    if ($result['success']) {
                        $photoSuccess = $result['message'];
                        // Mettre à jour la session
                        $_SESSION['profile_photo'] = $relativePath;
                        // Recharger le profil
                        $profileResult = $userController->getProfile($userId);
                        $profile = $profileResult['success'] ? $profileResult['profile'] : null;
                    } else {
                        $photoError = $result['message'];
                    }
                } else {
                    $photoError = "Erreur lors de l'upload du fichier.";
                }
            }
        } else {
            $photoError = "Aucun fichier sélectionné ou erreur lors de l'upload.";
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👤 Mon Profil - TalentMatch</title>
    <link rel="stylesheet" href="../../assets/css/dashbord-style.css">
    <style>
        .profile-photo-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            margin: 0 auto 20px;
            display: block;
        }

        .profile-photo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            margin: 0 auto 20px;
            border: 4px solid #007bff;
        }

        .profile-info-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .profile-info-card h3 {
            margin-top: 0;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #007bff;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .form-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .form-group input[type="password"],
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="password"]:focus,
        .form-group input[type="file"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .file-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .back-to-dashboard {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-to-dashboard:hover {
            background: #5a6268;
            transform: translateX(-3px);
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Badge Mode Développement -->
    <div class="dev-badge">
        <span>🔓</span>
        <span>MODE DEV</span>
    </div>

    <div class="container">
        <!-- Bouton retour au tableau de bord -->
        <a href="index.php" class="back-to-dashboard">
            <span>←</span>
            <span>Retour au tableau de bord</span>
        </a>

        <!-- Header -->
        <div class="header">
            <h1>👤 Mon Profil</h1>
            <div class="user-info">
                <span><?php echo htmlspecialchars($currentUser['name']); ?></span>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <?php if (!empty($passwordError) || !empty($passwordSuccess) || !empty($photoError) || !empty($photoSuccess)): ?>
            <div class="alert alert-<?php echo (!empty($passwordError) || !empty($photoError)) ? 'error' : 'success'; ?>">
                <?php
                if (!empty($passwordError)) echo htmlspecialchars($passwordError);
                if (!empty($passwordSuccess)) echo htmlspecialchars($passwordSuccess);
                if (!empty($photoError)) echo htmlspecialchars($photoError);
                if (!empty($photoSuccess)) echo htmlspecialchars($photoSuccess);
                ?>
            </div>
        <?php endif; ?>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Informations du profil -->
            <div class="profile-info-card">
                <h3>Informations personnelles</h3>

                <?php if ($profile): ?>
                    <div style="text-align: center; margin-bottom: 30px;">
                        <?php if ($profile['profile_photo']): ?>
                            <img src="../../<?php echo htmlspecialchars($profile['profile_photo']); ?>"
                                 alt="Photo de profil" class="profile-photo-large">
                        <?php else: ?>
                            <div class="profile-photo-placeholder">
                                <?php echo strtoupper(substr($profile['username'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nom d'utilisateur</div>
                            <div class="info-value"><?php echo htmlspecialchars($profile['username']); ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Adresse email</div>
                            <div class="info-value"><?php echo htmlspecialchars($profile['email']); ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Membre depuis</div>
                            <div class="info-value">
                                <?php echo $profile['created_at'] ? date('d/m/Y', strtotime($profile['created_at'])) : 'N/A'; ?>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Statut</div>
                            <div class="info-value">Actif</div>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #dc3545;">Erreur lors du chargement du profil.</p>
                <?php endif; ?>
            </div>

            <!-- Formulaire de changement de mot de passe -->
            <div class="form-card">
                <h3>🔐 Changer le mot de passe</h3>

                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe</label>
                            <input type="password" id="new_password" name="new_password" required minlength="8">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>

                    <button type="submit" name="change_password" class="btn-primary">Changer le mot de passe</button>
                </form>
            </div>

            <!-- Formulaire d'upload de photo de profil -->
            <div class="form-card">
                <h3>📸 Photo de profil</h3>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_photo">Sélectionner une nouvelle photo</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" required>
                        <div class="file-info">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB.</div>
                    </div>

                    <button type="submit" name="upload_photo" class="btn-primary">Uploader la photo</button>
                </form>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="footer-actions">
            <a href="index.php" class="btn btn-secondary">📊 Tableau de bord</a>
            <a href="logout.php" class="btn btn-logout">🚪 Déconnexion</a>
        </div>
    </div>

    <script>
        // Validation côté client pour le changement de mot de passe
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');

            function validatePasswords() {
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            newPassword.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
        });

        // Auto-dismiss des alertes après 5 secondes
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
