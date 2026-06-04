<?php
// views/dashboard/index.php (VERSION DÉVELOPPEMENT)

require_once __DIR__ . '/../../controllers/UserController.php';

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userController = new UserController();

// MODE DÉVELOPPEMENT : Vérification désactivée
// Pour activer en PRODUCTION, décommentez les lignes ci-dessous :
/*
if (!$userController->isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}
*/

// Forcer le mode développement - créer une fausse session si nécessaire
if (!isset($_SESSION['dev_mode'])) {
    $_SESSION['dev_mode'] = true;
    $_SESSION['user_id'] = 999;
    $_SESSION['user_email'] = 'dev@talentmatch.com';
    $_SESSION['user_name'] = 'Développeur';
    $_SESSION['logged_in'] = true;
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

// Gestion de la recherche
$searchQuery = $_GET['q'] ?? '';
$users = [];

if (!empty($searchQuery)) {
    $users = $userController->searchUsers($searchQuery);
} else {
    $users = $userController->getAllUsers();
}

// Calculer les statistiques
$stats = $userController->getStats();

// Gérer les messages
$message = '';
$messageType = '';

if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $messageType = 'success';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    $messageType = 'error';
    unset($_SESSION['error_message']);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📋 Tableau de bord - TalentMatch</title>
    <link rel="stylesheet" href="../../assets/css/dashbord-style.css">
    <style>
        .dev-badge {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ff9d5c, #ff6b9d);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 12px;
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.4);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .back-to-menu {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .back-to-menu:hover {
            background: #5568d3;
            transform: translateX(-5px);
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
        <!-- Bouton retour au menu -->
        <a href="menu.php" class="back-to-menu">
            <span>←</span>
            <span>Retour au menu</span>
        </a>

        <!-- Header -->
        <div class="header">
            <h1>📋 Tableau de bord</h1>
            <div class="user-info">
                <span><?php echo htmlspecialchars($currentUser['name']); ?></span>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
                </div>
                <a href="profile.php" class="btn btn-small" title="Mon Profil" style="margin-left: 10px;">👤 Profil</a>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <h3>Total Comptes</h3>
                <div class="card-value"><?php echo $stats['total_users']; ?></div>
            </div>
            <div class="card">
                <h3>Résultats</h3>
                <div class="card-value"><?php echo count($users); ?></div>
            </div>
            <div class="card">
                <h3>Utilisateur</h3>
                <div class="card-value" style="font-size: 16px;">
                    <?php echo htmlspecialchars($currentUser['name']); ?>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Content Section -->
        <div class="content-section">
            <div class="section-header">
                <h2>Liste des Utilisateurs</h2>
                <div class="header-actions">
                    <form method="GET" style="display: inline;">
                        <input type="text" 
                               name="q" 
                               class="search-input"
                               placeholder="Rechercher..." 
                               value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="btn btn-secondary">🔍</button>
                    </form>
                    <a href="index.php" class="btn btn-secondary" title="Réinitialiser">🔄</a>
                   
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td>
                                    <div class="user-cell">
                                        <span class="user-avatar-small">
                                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                        </span>
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php 
                                    if (isset($user['created_at'])) {
                                        echo date('d/m/Y', strtotime($user['created_at']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-small btn-edit" 
                                           title="Modifier">✏️</a>
                                        <button onclick="deleteUser(<?php echo $user['id']; ?>)" 
                                                class="btn btn-small btn-delete" 
                                                title="Supprimer">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">
                                <?php if (!empty($searchQuery)): ?>
                                    Aucun compte trouvé pour "<?php echo htmlspecialchars($searchQuery); ?>"
                                <?php else: ?>
                                    Aucun compte enregistré
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Actions -->
        <div class="footer-actions">
            <a href="menu.php" class="btn btn-secondary">🏠 Menu Principal</a>
            <a href="logout.php" class="btn btn-logout">🚪 Déconnexion</a>
        </div>
    </div>

    <script>
    function deleteUser(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce compte ?\n\nCette action est irréversible.')) {
            window.location.href = 'delete.php?id=' + id;
        }
    }

    // Auto-dismiss des alertes après 5 secondes
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    });

    // Highlight de la recherche
    const searchQuery = "<?php echo addslashes($searchQuery); ?>";
    if (searchQuery) {
        document.querySelectorAll('tbody tr td').forEach(td => {
            const text = td.textContent;
            if (text.toLowerCase().includes(searchQuery.toLowerCase())) {
                td.style.backgroundColor = '#fff3cd';
            }
        });
    }
    </script>
</body>
</html>