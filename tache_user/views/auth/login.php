<?php
// views/auth/login.php

require_once __DIR__ . '/../../controllers/UserController.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

// Rediriger si déjà connecté
if ($userController->isLoggedIn()) {
    header("Location: ../dashboard/profile.php");
    exit();
}

$error = '';
$success = '';

// TRAITEMENT DU FORMULAIRE DE CONNEXION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cnx'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $result = $userController->login($email, $password);

        if ($result['success']) {
            // Connexion réussie - redirection vers le profil
            header("Location: ../dashboard/profile.php");
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

// Message de succès après inscription
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success = '✅ Inscription réussie ! Vous pouvez maintenant vous connecter.';
}

// Récupérer l'email si fourni dans l'URL
$savedEmail = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - TalentMatch</title>
    <link rel="stylesheet" href="../../assets/css/login-style.css">
</head>
<body>
    <div class="background-animation">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    <div class="container">
        <a href="../../index.php" class="back-link">
            <span class="arrow">←</span> Retour à l'accueil
        </a>

        <div class="login-card">
            <div class="card-header">
                <h1 class="logo">TalentMatch</h1>
                <h2 class="title">Bon retour !</h2>
                <p class="subtitle">Connectez-vous à votre compte</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="background: #fee; color: #c00; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fcc; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">⚠️</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">✅</span>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <form id="loginForm" class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉️</span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="votre.email@example.com"
                            value="<?php echo htmlspecialchars($savedEmail); ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <span id="eyeIcon">👁️</span>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember">
                        <span class="checkbox-label">Se souvenir de moi</span>
                    </label>
                    <a href="forgot-password.php" class="forgot-link">Mot de passe oublié ?</a>
                </div>

                <button type="submit" name="cnx" class="btn-submit">
                    Se connecter
                </button>

                <div class="divider">
                    <span>OU</span>
                </div>

                <div class="social-login">
                    <button type="button" class="btn-social google">
                        <span class="social-icon">G</span>
                        Continuer avec Google
                    </button>
                    <button type="button" class="btn-social linkedin">
                        <span class="social-icon">in</span>
                        Continuer avec LinkedIn
                    </button>
                </div>

                <div class="form-footer">
                    <p>Vous n'avez pas de compte ? <a href="signup.php" class="link">S'inscrire</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="../../assets/js/login-script.js"></script>
    <script>
        // Fonction pour basculer la visibilité du mot de passe
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>