<?php
// views/auth/forgot-password.php

require_once __DIR__ . '/../../controllers/UserController.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

// Rediriger si déjà connecté
if ($userController->isLoggedIn()) {
    header("Location: ../dashboard/index.php");
    exit();
}

// IMPORTANT : Nettoyer la session si on arrive sans POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['continue'])) {
    unset($_SESSION['reset_email']);
    unset($_SESSION['reset_code_sent']);
    unset($_SESSION['reset_code_verified']);
}

$error = '';
$success = '';
$step = 'email'; // Par défaut : étape email

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Étape 1 : Envoi du code de vérification
    if (isset($_POST['send_code'])) {
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $error = 'Veuillez entrer votre adresse email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Adresse email invalide.';
        } else {
            $result = $userController->sendPasswordResetCode($email);
            
            if ($result['success']) {
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_code_sent'] = time();
                $success = $result['message'];
                $step = 'code';
            } else {
                $error = $result['message'];
            }
        }
    }
    
    // Étape 2 : Vérification du code
    elseif (isset($_POST['verify_code'])) {
        $code = trim($_POST['code'] ?? '');
        $email = $_SESSION['reset_email'] ?? '';
        
        if (empty($email)) {
            $error = 'Session expirée. Veuillez recommencer.';
            $step = 'email';
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_code_sent']);
        } elseif (empty($code)) {
            $error = 'Veuillez entrer le code de vérification.';
            $step = 'code';
        } else {
            $result = $userController->verifyResetCode($email, $code);
            
            if ($result['success']) {
                $_SESSION['reset_code_verified'] = true;
                $step = 'reset';
            } else {
                $error = $result['message'];
                $step = 'code';
            }
        }
    }
    
    // Étape 3 : Réinitialisation du mot de passe
    elseif (isset($_POST['reset_password'])) {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $email = $_SESSION['reset_email'] ?? '';
        
        if (empty($email) || !isset($_SESSION['reset_code_verified'])) {
            $error = 'Session expirée. Veuillez recommencer.';
            $step = 'email';
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_code_sent']);
            unset($_SESSION['reset_code_verified']);
        } elseif (empty($newPassword) || empty($confirmPassword)) {
            $error = 'Veuillez remplir tous les champs.';
            $step = 'reset';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
            $step = 'reset';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Les mots de passe ne correspondent pas.';
            $step = 'reset';
        } else {
            $result = $userController->resetPassword($email, $newPassword);
            
            if ($result['success']) {
                // Nettoyer la session
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_code_sent']);
                unset($_SESSION['reset_code_verified']);
                
                // Rediriger vers la page de connexion
                header("Location: login.php?reset=1&email=" . urlencode($email));
                exit();
            } else {
                $error = $result['message'];
                $step = 'reset';
            }
        }
    }
} else {
    // Déterminer l'étape en fonction de la session (seulement si POST avec continue)
    if (isset($_SESSION['reset_code_verified']) && $_SESSION['reset_code_verified']) {
        $step = 'reset';
    } elseif (isset($_SESSION['reset_email']) && isset($_SESSION['reset_code_sent'])) {
        $step = 'code';
    } else {
        $step = 'email';
    }
}
?>
<!DOCTYPE HTML>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - TalentMatch</title>
    <link rel="stylesheet" href="../../assets/css/login-style.css">
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .step {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .step-number {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: #5cd8d8;
            color: white;
            box-shadow: 0 0 20px rgba(92, 216, 216, 0.5);
        }
        
        .step.completed .step-number {
            background: #51cf66;
            color: white;
        }
        
        .step-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
        }
        
        .step.active .step-text {
            color: white;
            font-weight: 500;
        }
        
        .step-arrow {
            color: rgba(255, 255, 255, 0.3);
            font-size: 12px;
        }
        
        .code-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0 30px 0;
        }
        
        .code-input {
            width: 50px;
            height: 55px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            color: #1a2942;
            transition: all 0.3s ease;
        }
        
        .code-input:focus {
            border-color: #5cd8d8;
            box-shadow: 0 0 0 4px rgba(92, 216, 216, 0.15);
            outline: none;
        }
        
        .resend-code {
            text-align: center;
            margin: -15px 0 25px 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .resend-link {
            color: #ff9d5c;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }
        
        .resend-link:hover {
            text-decoration: underline;
        }
        
        .password-requirements {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .password-requirements ul {
            margin: 10px 0 0 20px;
            padding: 0;
        }
        
        .password-requirements li {
            margin: 5px 0;
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
        <a href="login.php" class="back-link">
            <span class="arrow">←</span> Retour à la connexion
        </a>

        <div class="login-card">
            <div class="card-header">
                <h1 class="logo">TalentMatch</h1>
                <h2 class="title">Mot de passe oublié ?</h2>
                <p class="subtitle">
                    <?php if ($step === 'email'): ?>
                        Entrez votre email pour recevoir un code de vérification
                    <?php elseif ($step === 'code'): ?>
                        Entrez le code envoyé à votre email
                    <?php else: ?>
                        Créez votre nouveau mot de passe
                    <?php endif; ?>
                </p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step <?php echo $step === 'email' ? 'active' : ($step !== 'email' ? 'completed' : ''); ?>">
                    <div class="step-number">1</div>
                    <div class="step-text">Email</div>
                </div>
                <div class="step-arrow">→</div>
                <div class="step <?php echo $step === 'code' ? 'active' : ($step === 'reset' ? 'completed' : ''); ?>">
                    <div class="step-number">2</div>
                    <div class="step-text">Code</div>
                </div>
                <div class="step-arrow">→</div>
                <div class="step <?php echo $step === 'reset' ? 'active' : ''; ?>">
                    <div class="step-number">3</div>
                    <div class="step-text">Nouveau mot de passe</div>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <span style="font-size: 20px;">⚠️</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <span style="font-size: 20px;">✅</span>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
                
                <?php 
                // MODE TEST : Afficher le code directement (à retirer en production)
                if (isset($_SESSION['debug_reset_code']) && isset($_SESSION['debug_reset_email'])): 
                ?>
                    <div class="alert" style="background: #fff3cd; border: 2px solid #ffc107; color: #856404; margin-top: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 20px;">🔧</span>
                            <div>
                                <strong>MODE TEST - Développement</strong><br>
                                Votre code de vérification : <strong style="font-size: 24px; letter-spacing: 3px;"><?php echo $_SESSION['debug_reset_code']; ?></strong><br>
                                <small>(En production, ce code sera envoyé par email)</small>
                            </div>
                        </div>
                    </div>
                <?php 
                    // Nettoyer après affichage
                    unset($_SESSION['debug_reset_code']);
                    unset($_SESSION['debug_reset_email']);
                endif; 
                ?>
            <?php endif; ?>

            <!-- Étape 1 : Saisie de l'email -->
            <?php if ($step === 'email'): ?>
                <form class="login-form" method="POST" action="">
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✉️</span>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="votre.email@example.com"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <button type="submit" name="send_code" class="btn-submit">
                        Envoyer le code de vérification
                    </button>

                    <div class="form-footer">
                        <p>Vous vous souvenez de votre mot de passe ? <a href="login.php" class="link">Se connecter</a></p>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Étape 2 : Vérification du code -->
            <?php if ($step === 'code'): ?>
                <form class="login-form" method="POST" action="?continue=1">
                    <div class="form-group">
                        <label for="code">Code de vérification (6 chiffres)</label>
                        <div class="code-inputs" id="codeInputs">
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                            <input type="text" class="code-input" maxlength="1" pattern="[0-9]" required>
                        </div>
                        <input type="hidden" id="code" name="code">
                    </div>

                    <div class="resend-code">
                        <span>Vous n'avez pas reçu le code ? </span>
                        <a href="forgot-password.php" class="resend-link">Recommencer</a>
                    </div>

                    <button type="submit" name="verify_code" class="btn-submit">
                        Vérifier le code
                    </button>

                    <div class="form-footer">
                        <p><a href="forgot-password.php" class="link">Changer d'adresse email</a></p>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Étape 3 : Nouveau mot de passe -->
            <?php if ($step === 'reset'): ?>
                <form class="login-form" method="POST" action="?continue=1">
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input 
                                type="password" 
                                id="new_password" 
                                name="new_password" 
                                placeholder="••••••••"
                                required
                                autofocus
                            >
                            <button type="button" class="toggle-password" onclick="togglePasswordField('new_password', 'eyeIcon1')">
                                <span id="eyeIcon1">👁️</span>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                placeholder="••••••••"
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePasswordField('confirm_password', 'eyeIcon2')">
                                <span id="eyeIcon2">👁️</span>
                            </button>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <strong>Votre mot de passe doit contenir :</strong>
                        <ul>
                            <li>Au moins 6 caractères</li>
                            <li>Des lettres et des chiffres (recommandé)</li>
                        </ul>
                    </div>

                    <button type="submit" name="reset_password" class="btn-submit">
                        Réinitialiser le mot de passe
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../assets/js/forgot-password-script.js"></script>
    <script>
        // Toggle password visibility
        function togglePasswordField(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            
            if (field && icon) {
                if (field.type === 'password') {
                    field.type = 'text';
                    icon.textContent = '🙈';
                } else {
                    field.type = 'password';
                    icon.textContent = '👁️';
                }
            }
        }

        // Code inputs handler - CORRECTION
        document.addEventListener('DOMContentLoaded', function() {
            const codeInputsContainer = document.getElementById('codeInputs');
            if (codeInputsContainer) {
                const inputs = codeInputsContainer.querySelectorAll('.code-input');
                const hiddenInput = document.getElementById('code');
                
                function updateHiddenCode() {
                    if (hiddenInput) {
                        let code = '';
                        inputs.forEach(inp => code += inp.value);
                        hiddenInput.value = code;
                        console.log('Code actuel:', code); // Debug
                    }
                }
                
                inputs.forEach((input, index) => {
                    // Auto-focus au suivant
                    input.addEventListener('input', function(e) {
                        // Accepter seulement les chiffres
                        this.value = this.value.replace(/[^0-9]/g, '');
                        
                        if (this.value.length === 1) {
                            if (index < inputs.length - 1) {
                                inputs[index + 1].focus();
                            }
                        }
                        
                        updateHiddenCode();
                    });
                    
                    // Retour arrière
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Backspace' && this.value === '') {
                            if (index > 0) {
                                inputs[index - 1].focus();
                                inputs[index - 1].value = '';
                            }
                        }
                        
                        // Flèches gauche/droite
                        if (e.key === 'ArrowLeft' && index > 0) {
                            inputs[index - 1].focus();
                        }
                        if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    });
                    
                    // Gestion du paste
                    input.addEventListener('paste', function(e) {
                        e.preventDefault();
                        const pasteData = e.clipboardData.getData('text');
                        const digits = pasteData.replace(/\D/g, '').split('');
                        
                        digits.forEach((digit, i) => {
                            if (inputs[i]) {
                                inputs[i].value = digit;
                            }
                        });
                        
                        if (digits.length > 0) {
                            const lastIndex = Math.min(digits.length, inputs.length) - 1;
                            inputs[lastIndex].focus();
                        }
                        
                        updateHiddenCode();
                    });
                    
                    // Sélectionner tout le contenu au focus
                    input.addEventListener('focus', function() {
                        this.select();
                    });
                });
                
                // Focus sur le premier input
                if (inputs.length > 0) {
                    inputs[0].focus();
                }
            }
        });
    </script>
</body>
</html>