<?php
// views/dashboard/menu.php
// Page de menu pour accéder aux différentes sections du dashboard

require_once __DIR__ . '/../../controllers/UserController.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

// Option : Désactiver la vérification de connexion pour le développement
// Commentez cette section si vous voulez forcer la connexion
/*
if (!$userController->isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}
*/

// Récupérer l'utilisateur connecté (ou utilisateur fictif pour le dev)
$currentUser = $userController->getCurrentUser();
if (!$currentUser) {
    // Utilisateur fictif pour le développement
    $currentUser = [
        'id' => 1,
        'name' => 'Développeur',
        'email' => 'dev@example.com'
    ];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Dashboard - TalentMatch</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
        }

        .header h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 15px;
        }

        .header p {
            color: #666;
            font-size: 18px;
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            margin-top: 15px;
            font-weight: 600;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .menu-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
            border: 2px solid #e0e0e0;
            border-radius: 16px;
            padding: 30px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }

        .menu-card:hover::before {
            opacity: 0.1;
        }

        .menu-card:hover {
            transform: translateY(-8px);
            border-color: #667eea;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
        }

        .menu-card-content {
            position: relative;
            z-index: 1;
        }

        .menu-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .menu-card h2 {
            color: #333;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .menu-card p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .menu-card .badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }

        .auth-section {
            border-top: 2px solid #f0f0f0;
            padding-top: 30px;
            margin-top: 20px;
        }

        .auth-section h3 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .auth-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .auth-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 25px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .auth-link:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: scale(1.05);
        }

        .info-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .info-box .icon {
            font-size: 32px;
        }

        .info-box .text {
            flex: 1;
        }

        .info-box .text h4 {
            color: #856404;
            margin-bottom: 5px;
        }

        .info-box .text p {
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 28px;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Dashboard TalentMatch</h1>
            <p>Choisissez la section que vous souhaitez accéder</p>
            <div class="user-badge">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                </div>
                <span><?php echo htmlspecialchars($currentUser['name']); ?></span>
            </div>
        </div>

        <div class="info-box">
            <span class="icon">💡</span>
            <div class="text">
                <h4>Mode Développement</h4>
                <p>Cette page vous permet d'accéder directement aux différentes sections du dashboard sans authentification. En production, décommentez la vérification de connexion dans le fichier menu.php.</p>
            </div>
        </div>

        <div class="menu-grid">
            <a href="index.php" class="menu-card">
                <div class="menu-card-content">
                    <span class="menu-icon">📋</span>
                    <h2>Liste des Utilisateurs</h2>
                    <p>Voir tous les comptes utilisateurs, rechercher et gérer les comptes.</p>
                    <span class="badge">CRUD Complet</span>
                </div>
            </a>

            <a href="edit.php?id=1" class="menu-card">
                <div class="menu-card-content">
                    <span class="menu-icon">✏️</span>
                    <h2>Modifier un Utilisateur</h2>
                    <p>Modifier les informations d'un compte utilisateur existant.</p>
                    <span class="badge">Update</span>
                </div>
            </a>

            <a href="#" onclick="showDeleteInfo(); return false;" class="menu-card">
                <div class="menu-card-content">
                    <span class="menu-icon">🗑️</span>
                    <h2>Supprimer un Utilisateur</h2>
                    <p>Supprimer un compte utilisateur de la base de données.</p>
                    <span class="badge">Delete</span>
                </div>
            </a>

            <a href="stats.php" class="menu-card">
                <div class="menu-card-content">
                    <span class="menu-icon">📊</span>
                    <h2>Statistiques</h2>
                    <p>Voir les statistiques et analyses des utilisateurs.</p>
                    <span class="badge">Bientôt</span>
                </div>
            </a>
        </div>

        <div class="auth-section">
            <h3>🔐 Authentification</h3>
            <div class="auth-grid">
                <a href="../auth/login.php" class="auth-link">
                    <span>🔑</span>
                    <span>Connexion</span>
                </a>
                <a href="../auth/signup.php" class="auth-link">
                    <span>📝</span>
                    <span>Inscription</span>
                </a>
                <a href="logout.php" class="auth-link">
                    <span>🚪</span>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </div>

    <script>
        function showDeleteInfo() {
            alert('ℹ️ Information\n\nLa suppression se fait depuis la liste des utilisateurs.\n\n1. Allez dans "Liste des Utilisateurs"\n2. Cliquez sur le bouton 🗑️ à côté d\'un utilisateur\n3. Confirmez la suppression');
        }

        // Animation au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.menu-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.animation = `fadeIn 0.6s ease ${index * 0.1}s both`;
                }, 0);
            });
        });

        // Effet de particules au survol
        document.querySelectorAll('.menu-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            });
        });
    </script>
</body>
</html>