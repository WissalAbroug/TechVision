<?php
// views/auth/admin-login.php

require_once __DIR__ . '/../../controllers/AdminController.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$adminController = new AdminController();

// Rediriger si déjà connecté
if ($adminController->isAdminLoggedIn()) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $user = trim($_POST['user'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $adminController->login($user, $password);
    
    if ($result['success']) {
        header("Location: ../admin/dashboard.php");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Connexion Administrateur - TalentMatch</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #1a2942;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        /* Background Animation */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .bubble {
            position: absolute;
            bottom: -100px;
            background: rgba(255, 157, 92, 0.1);
            border-radius: 50%;
            animation: rise 15s infinite ease-in;
        }

        .bubble:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            animation-duration: 12s;
            animation-delay: 0s;
        }

        .bubble:nth-child(2) {
            width: 120px;
            height: 120px;
            left: 30%;
            animation-duration: 15s;
            animation-delay: 2s;
        }

        .bubble:nth-child(3) {
            width: 60px;
            height: 60px;
            left: 50%;
            animation-duration: 10s;
            animation-delay: 4s;
        }

        .bubble:nth-child(4) {
            width: 100px;
            height: 100px;
            left: 70%;
            animation-duration: 13s;
            animation-delay: 1s;
        }

        .bubble:nth-child(5) {
            width: 90px;
            height: 90px;
            left: 85%;
            animation-duration: 14s;
            animation-delay: 3s;
        }

        @keyframes rise {
            0% {
                bottom: -100px;
                transform: translateX(0) scale(1);
                opacity: 0;
            }
            10% {
                opacity: 0.3;
            }
            50% {
                transform: translateX(100px) scale(1.2);
                opacity: 0.2;
            }
            100% {
                bottom: 110vh;
                transform: translateX(-100px) scale(0.8);
                opacity: 0;
            }
        }

        /* Container */
        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Back Link */
        .back-link {
            display: inline-block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .back-link:hover {
            color: #ff9d5c;
        }

        .arrow {
            font-size: 18px;
        }

        /* Login Card */
        .login-card {
            background: rgba(45, 74, 124, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            padding: 45px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: cardFloat 3s ease-in-out infinite;
        }

        @keyframes cardFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        /* Admin Badge */
        .admin-badge {
            display: inline-block;
            background: #ff9d5c;
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        /* Card Header */
        .card-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo {
            color: #ff9d5c;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        .title {
            color: white;
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 15px;
        }

        /* Alert */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            overflow: hidden;
        }

        .alert-error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
            display: block;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            font-size: 18px;
            pointer-events: none;
        }

        .input-wrapper input {
            width: 100%;
            padding: 16px 50px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            color: #1a2942;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-wrapper input::placeholder {
            color: rgba(26, 41, 66, 0.5);
        }

        .input-wrapper input:focus {
            border-color: #ff9d5c;
            background: white;
            box-shadow: 0 0 0 4px rgba(255, 157, 92, 0.15);
            transform: translateY(-2px);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
            transition: all 0.3s ease;
        }

        .toggle-password:hover {
            transform: scale(1.2);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #ff9d5c 0%, #ff6b9d 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 8px 20px rgba(255, 157, 92, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 157, 92, 0.4);
            background: linear-gradient(135deg, #ff6b9d 0%, #ff9d5c 100%);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        /* Divider */
        .divider {
            text-align: center;
            position: relative;
            margin: 25px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .divider span {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
            background: rgba(45, 74, 124, 0.4);
            padding: 0 15px;
        }

        /* Form Footer */
        .form-footer {
            text-align: center;
            margin-top: 10px;
        }

        .form-footer p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .link {
            color: #ff9d5c;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .link:hover {
            color: #ffb380;
            text-decoration: underline;
        }

        /* Security Notice */
        .security-notice {
            background: rgba(255, 157, 92, 0.1);
            border: 1px solid rgba(255, 157, 92, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .security-notice .icon {
            font-size: 24px;
        }

        .security-notice .text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-card {
                padding: 30px 25px;
            }

            .title {
                font-size: 22px;
            }

            .logo {
                font-size: 24px;
            }

            .input-wrapper input {
                padding: 14px 45px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .login-card {
                padding: 25px 20px;
            }

            .btn-submit {
                padding: 12px;
            }
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
                <div class="admin-badge">
                    <span>🛡️</span>
                    <span>ADMINISTRATEUR</span>
                </div>
                <h1 class="logo">TalentMatch</h1>
                <h2 class="title">Espace Admin</h2>
                <p class="subtitle">Accès réservé aux administrateurs</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <span style="font-size: 20px;">⚠️</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form id="adminLoginForm" class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="user">Nom d'utilisateur</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input
                            type="text"
                            id="user"
                            name="user"
                            value="admin"
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
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <span id="eyeIcon">👁️</span>
                        </button>
                    </div>
                </div>

                <button type="submit" name="admin_login" class="btn-submit">
                    🔐 Connexion Admin
                </button>
            </form>

            <div class="security-notice">
                <span class="icon">🔐</span>
                <div class="text">
                    <strong>Accès sécurisé :</strong> Cette page est réservée aux administrateurs du système. Toutes les connexions sont enregistrées.
                </div>
            </div>

            <div class="divider">
                <span>•••</span>
            </div>

            <div class="form-footer">
                <p><a href="login.php" class="link">← Connexion utilisateur normale</a></p>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Toggle password visibility
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

        // Auto-focus first input
        window.addEventListener('load', () => {
            document.getElementById('user').focus();
        });

        // Handle Enter key
        document.querySelectorAll('input').forEach((input, index, inputs) => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this.id === 'user') {
                        document.getElementById('password').focus();
                    } else {
                        document.getElementById('adminLoginForm').submit();
                    }
                }
            });
        });

        // Security warning on page leave
        let formSubmitted = false;
        document.getElementById('adminLoginForm').addEventListener('submit', function() {
            formSubmitted = true;
        });

        console.log('🛡️ Page de connexion Administrateur - TalentMatch');
    </script>
</body>
</html>