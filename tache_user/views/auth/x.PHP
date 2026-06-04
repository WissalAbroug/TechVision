<?php
// views/auth/login-debug-final.php
// Remplacez temporairement votre login.php par ce fichier

error_reporting(E_ALL);
ini_set('display_errors', 1);

// IMPORTANT: Aucun espace ou caractère avant cette ligne
session_start();

require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();
$debugLog = [];
$debugLog[] = "Page chargée à " . date('H:i:s');

// Vérifier si déjà connecté
if ($userController->isLoggedIn()) {
    $debugLog[] = "Utilisateur déjà connecté, tentative de redirection...";
    header("Location: ../dashboard/profile.php");
    exit();
}

$error = '';
$success = '';

// TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cnx'])) {
    $debugLog[] = "=== FORMULAIRE SOUMIS ===";
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $debugLog[] = "Email: " . $email;
    $debugLog[] = "Password length: " . strlen($password);
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
        $debugLog[] = "ERREUR: Champs vides";
    } else {
        $debugLog[] = "Appel de userController->login()...";
        
        try {
            $result = $userController->login($email, $password);
            $debugLog[] = "Résultat login: " . json_encode($result);

            if ($result['success']) {
                $debugLog[] = "✅ LOGIN RÉUSSI";
                $debugLog[] = "Session ID: " . session_id();
                $debugLog[] = "Session user_id: " . ($_SESSION['user_id'] ?? 'NON DÉFINI');
                $debugLog[] = "Session logged_in: " . ($_SESSION['logged_in'] ?? 'NON DÉFINI');
                $debugLog[] = "isLoggedIn(): " . ($userController->isLoggedIn() ? 'TRUE' : 'FALSE');
                
                // Vérifier si les headers peuvent être envoyés
                if (headers_sent($file, $line)) {
                    $error = "ERREUR CRITIQUE: Headers déjà envoyés dans $file:$line";
                    $debugLog[] = "❌ Headers déjà envoyés: $file:$line";
                } else {
                    $debugLog[] = "Headers OK, tentative de redirection...";
                    
                    // Essayer plusieurs méthodes de redirection
                    
                    // Méthode 1: Header classique
                    header("Location: ../dashboard/profile.php");
                    exit();
                    
                    // Si on arrive ici, la redirection a échoué
                    $debugLog[] = "❌ La redirection header() a échoué";
                    
                    // Méthode 2: JavaScript
                    echo "<script>window.location.href='../dashboard/profile.php';</script>";
                    exit();
                }
            } else {
                $error = $result['message'];
                $debugLog[] = "❌ LOGIN ÉCHOUÉ: " . $error;
            }
        } catch (Exception $e) {
            $error = "Erreur système: " . $e->getMessage();
            $debugLog[] = "❌ EXCEPTION: " . $e->getMessage();
            $debugLog[] = "Stack trace: " . $e->getTraceAsString();
        }
    }
}

// Message de succès après inscription
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success = '✅ Inscription réussie ! Vous pouvez maintenant vous connecter.';
}

$savedEmail = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - TalentMatch [DEBUG]</title>
    <link rel="stylesheet" href="../../assets/css/login-style.css">
    <style>
        .debug-console {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1a1a1a;
            color: #0f0;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 400px;
            overflow-y: auto;
            border-top: 3px solid #0f0;
            z-index: 10000;
        }
        .debug-console h3 {
            color: #0ff;
            margin-bottom: 10px;
        }
        .debug-console .log-entry {
            margin: 5px 0;
            padding: 5px;
            border-left: 2px solid #0f0;
            padding-left: 10px;
        }
        .debug-console .log-success {
            color: #0f0;
        }
        .debug-console .log-error {
            color: #f00;
        }
        .debug-console .log-warning {
            color: #ff0;
        }
        .close-debug {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #f00;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
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
                <p style="color: #ff0; font-size: 12px; margin-top: 10px;">🔧 MODE DEBUG ACTIVÉ</p>
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

    <!-- CONSOLE DE DEBUG -->
    <div class="debug-console">
        <button class="close-debug" onclick="this.parentElement.style.display='none'">✕</button>
        <h3>🔍 CONSOLE DE DEBUG</h3>
        
        <div class="log-entry">
            <strong>REQUEST_METHOD:</strong> <?php echo $_SERVER['REQUEST_METHOD']; ?>
        </div>
        <div class="log-entry">
            <strong>SESSION_ID:</strong> <?php echo session_id(); ?>
        </div>
        <div class="log-entry">
            <strong>IS_LOGGED_IN:</strong> <?php echo $userController->isLoggedIn() ? 'OUI' : 'NON'; ?>
        </div>
        
        <?php if (!empty($_SESSION)): ?>
            <div class="log-entry">
                <strong>SESSION DATA:</strong>
                <pre><?php print_r($_SESSION); ?></pre>
            </div>
        <?php endif; ?>
        
        <div class="log-entry">
            <strong>LOGS:</strong>
        </div>
        
        <?php foreach ($debugLog as $log): ?>
            <div class="log-entry <?php 
                if (strpos($log, '✅') !== false || strpos($log, 'RÉUSSI') !== false) {
                    echo 'log-success';
                } elseif (strpos($log, '❌') !== false || strpos($log, 'ERREUR') !== false) {
                    echo 'log-error';
                } elseif (strpos($log, '⚠️') !== false || strpos($log, 'WARNING') !== false) {
                    echo 'log-warning';
                }
            ?>">
                <?php echo htmlspecialchars($log); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- NE PAS CHARGER login-script.js pour ce test -->
    <script>
        console.log('🔧 MODE DEBUG - JavaScript minimal');
        
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

        // Logger la soumission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            console.log('📤 Formulaire en cours de soumission...');
            console.log('Email:', document.getElementById('email').value);
            console.log('Password length:', document.getElementById('password').value.length);
            // NE PAS EMPÊCHER - Laisser le formulaire se soumettre normalement
        });

        // Empêcher les boutons sociaux de soumettre le formulaire
        document.querySelectorAll('.btn-social').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Fonctionnalité bientôt disponible');
            });
        });
    </script>
</body>
</html>