
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
$user = $userController->getUser($id);

if (!$user) {
    $_SESSION['error_message'] = "Utilisateur introuvable";
    header("Location: index.php");
    exit();
}

$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_user = trim($_POST['user'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';

    $result = $userController->updateUser($id, $new_user, $new_email, 
                                          !empty($new_password) ? $new_password : null);
    
    if ($result['success']) {
        $_SESSION['success_message'] = $result['message'];
        header("Location: index.php");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur - TalentMatch</title>
    <link rel="stylesheet" href="../../assets/css/dashboard-style.css">
    <style>
        .dev-badge {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ff9d5c, #ff6b9d);
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 11px;
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.4);
            z-index: 1000;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .form-container h1 {
            margin-bottom: 30px;
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-group input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .alert-error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
    </style>
</head>
<body>
    <div class="dev-badge">🔓 MODE DEV</div>
    
    <div class="form-container">
        <a href="menu.php" class="back-to-menu" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; color: #667eea; text-decoration: none; font-weight: 600;">
            <span>←</span>
            <span>Retour au menu</span>
        </a>
        
        <h1>✏️ Modifier Utilisateur</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="id">ID</label>
                <input type="text" id="id" value="<?php echo htmlspecialchars($user['id']); ?>" disabled>
            </div>

            <div class="form-group">
                <label for="user">Nom d'utilisateur</label>
                <input type="text" id="user" name="user"
                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" id="password" name="password" 
                       placeholder="Minimum 8 caractères">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    💾 Enregistrer
                </button>
                <a href="menu.php" class="btn btn-secondary" style="flex: 1; text-align: center;">
                    🏠 Menu
                </a>
                <a href="index.php" class="btn btn-secondary" style="flex: 1; text-align: center;">
                    📋 Liste
                </a>
            </div>
        </form>
    </div>
</body>
</html>