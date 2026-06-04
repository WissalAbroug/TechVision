<?php
// views/auth/signup.php

// ACTIVER LES ERREURS
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!-- DEBUG: Début du script -->\n";

// Vérifier le chemin du controller
$controllerPath = __DIR__ . '/../../controllers/UserController.php';
echo "<!-- DEBUG: Chemin du controller: $controllerPath -->\n";
echo "<!-- DEBUG: Fichier existe? " . (file_exists($controllerPath) ? 'OUI' : 'NON') . " -->\n";

if (!file_exists($controllerPath)) {
    die("❌ ERREUR: Le fichier UserController.php n'existe pas au chemin: $controllerPath");
}

require_once $controllerPath;
echo "<!-- DEBUG: UserController chargé -->\n";

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "<!-- DEBUG: Session démarrée -->\n";

$userController = new UserController();
echo "<!-- DEBUG: UserController instancié -->\n";

// Rediriger si déjà connecté
if ($userController->isLoggedIn()) {
    header("Location: ../dashboard/index.php");
    exit();
}

$error = '';
$success = '';
$formData = [
    'user' => '',
    'email' => ''
];

echo "<!-- DEBUG: REQUEST_METHOD = " . $_SERVER['REQUEST_METHOD'] . " -->\n";
echo "<!-- DEBUG: POST isset(aj) = " . (isset($_POST['aj']) ? 'OUI' : 'NON') . " -->\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<!-- DEBUG: Formulaire soumis en POST -->\n";
    echo "<!-- DEBUG: POST data: " . print_r($_POST, true) . " -->\n";
    
    if (isset($_POST['aj'])) {
        echo "<!-- DEBUG: Bouton 'aj' détecté -->\n";
        
        $user = trim($_POST['user'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        echo "<!-- DEBUG: user='$user', email='$email', password_length=" . strlen($password) . " -->\n";

        // Sauvegarder les données du formulaire
        $formData['user'] = $user;
        $formData['email'] = $email;

        // Validation côté serveur
        if (empty($user)) {
            $error = "Le nom complet est requis.";
            echo "<!-- DEBUG: Erreur - nom vide -->\n";
        } elseif (empty($email)) {
            $error = "L'adresse email est requise.";
            echo "<!-- DEBUG: Erreur - email vide -->\n";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "L'adresse email n'est pas valide.";
            echo "<!-- DEBUG: Erreur - email invalide -->\n";
        } elseif (empty($password)) {
            $error = "Le mot de passe est requis.";
            echo "<!-- DEBUG: Erreur - password vide -->\n";
        } elseif (strlen($password) < 8) {
            $error = "Le mot de passe doit contenir au moins 8 caractères.";
            echo "<!-- DEBUG: Erreur - password trop court -->\n";
        } else {
            echo "<!-- DEBUG: Validation OK, appel du controller -->\n";
            
            // Appel du controller
            try {
                $result = $userController->register($user, $email, $password);
                echo "<!-- DEBUG: Résultat = " . json_encode($result) . " -->\n";
                
                if ($result['success']) {
                    echo "<!-- DEBUG: SUCCÈS - Redirection vers login.php -->\n";
                    // Rediriger vers la page de connexion
                    header("Location: login.php?registered=1");
                    exit();
                } else {
                    $error = $result['message'];
                    echo "<!-- DEBUG: ÉCHEC - " . $result['message'] . " -->\n";
                }
            } catch (Exception $e) {
                $error = "Erreur technique: " . $e->getMessage();
                echo "<!-- DEBUG: EXCEPTION - " . $e->getMessage() . " -->\n";
                echo "<!-- DEBUG: TRACE - " . $e->getTraceAsString() . " -->\n";
            }
        }
    } else {
        echo "<!-- DEBUG: POST reçu mais pas de bouton 'aj' -->\n";
    }
} else {
    echo "<!-- DEBUG: Pas de soumission POST -->\n";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentMatch - Créer un compte</title>
    <link rel="stylesheet" href="../../assets/css/signup-style.css">
</head>
<body>
    <div class="signup-container">
        <a href="../../index.php" class="back-link">
            <span>←</span>
            <span>Retour à l'accueil</span>
        </a>

        <div class="header">
            <div class="logo">TalentMatch</div>
            <h1>Créer un compte</h1>
            <p>Rejoignez notre communauté de talents</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" style="background: #fee; color: #c00; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fcc; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">⚠️</span>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success" style="background: #efe; color: #0a0; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #cfc; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">✓</span>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>
        
        
        
        <form id="signupForm" action="" method="POST" onsubmit="console.log('Formulaire soumis'); return true;">
            <div class="form-group">
                <label for="fullname">Nom complet</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input
                        type="text"
                        id="fullname"
                        name="user"
                        placeholder="Votre nom complet"
                        value="<?php echo htmlspecialchars($formData['user']); ?>"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="email">Adresse email</label>
                <div class="input-wrapper">
                    <span class="input-icon">📧</span>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="exemple@email.com"
                        value="<?php echo htmlspecialchars($formData['email']); ?>"
                    >
                </div>
                <p class="hint-text">Nous ne partagerons jamais votre email</p>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Minimum 8 caractères" 
                        required
                        minlength="8"
                    >
                    <span class="password-toggle" id="togglePassword">👁️</span>
                </div>
                <p class="hint-text">Au moins 8 caractères</p>
            </div>

            <div class="benefits">
                <h3>✨ Les avantages</h3>
                <ul class="benefits-list">
                    <li>
                        <span class="check-icon">✓</span>
                        <span>Accès à toutes les fonctionnalités</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span>Support client 24/7</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span>Matching intelligent avec l'IA</span>
                    </li>
                    <li>
                        <span class="check-icon">✓</span>
                        <span>Génération CV automatique</span>
                    </li>
                </ul>
            </div>

            <button type="submit" class="btn-submit" name="aj" value="1">
                Créer mon compte 🚀
            </button>

            <div class="divider">
                <span>OU</span>
            </div>

            <a href="login.php" class="btn-login">Se connecter</a>

            <div class="terms">
                En créant un compte, vous acceptez nos 
                <a href="#">conditions d'utilisation</a> et notre 
                <a href="#">politique de confidentialité</a>.
            </div>
        </form>
    </div>

    <script>
        console.log('Script signup.js chargé');
        
        // Vérifier si le script externe existe
        var script = document.createElement('script');
        script.src = '../../assets/js/signup-script.js';
        script.onerror = function() {
            console.error('❌ Impossible de charger signup-script.js');
        };
        script.onload = function() {
            console.log('✅ signup-script.js chargé');
        };
        document.head.appendChild(script);
    </script>
</body>
</html>