<?php
// views/admin/dashboard.php

require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../controllers/UserController.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$adminController = new AdminController();
$userController = new UserController();

// Vérifier si l'admin est connecté
if (!$adminController->isAdminLoggedIn()) {
    header("Location: ../auth/admin-login.php");
    exit();
}

// Récupérer l'admin actuel
$currentAdmin = $adminController->getCurrentAdmin();

// Récupérer tous les utilisateurs
$users = $userController->getAllUsers();

// Récupérer les statistiques
$stats = [
    'total_users' => $userController->countUsers(),
    'total_admins' => $adminController->countAdmins(),
    'admin_user' => $currentAdmin['username']
];

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
    <title>🛡️ Dashboard Administrateur - TalentMatch</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 30px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Admin Badge */
        .admin-badge {
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

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .header h1 {
            color: #333;
            font-size: 32px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #ff9d5c, #ff6b9d);
            border-radius: 50px;
            color: white;
        }

        .user-info span {
            font-weight: 500;
            font-size: 14px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: linear-gradient(135deg, #ff9d5c 0%, #ff6b9d 100%);
            color: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(255, 157, 92, 0.3);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 157, 92, 0.4);
        }

        .card h3 {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 36px;
            font-weight: 700;
        }

        /* Alert */
        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

        /* Content Section */
        .content-section {
            background: white;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-header h2 {
            color: #333;
            font-size: 22px;
            font-weight: 600;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 20px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: linear-gradient(135deg, #ff9d5c 0%, #ff6b9d 100%);
            color: white;
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
            font-size: 14px;
        }

        tbody tr {
            transition: all 0.2s ease;
        }

        tbody tr:hover {
            background-color: #fff8f5;
            transform: translateX(3px);
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        tbody tr:nth-child(even):hover {
            background-color: #fff8f5;
        }

        .no-data {
            text-align: center;
            padding: 40px !important;
            color: #999;
            font-style: italic;
        }

        /* User Cell */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-small {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #ff9d5c, #ff6b9d);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        /* Buttons */
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 13px;
        }

        .btn-edit {
            background: #4CAF50;
            color: white;
        }

        .btn-delete {
            background: #f44336;
            color: white;
        }

        .btn-logout {
            background: linear-gradient(135deg, #ff9d5c, #ff6b9d);
            color: white;
        }

        /* Footer */
        .footer-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Badge Admin -->
    <div class="admin-badge">
        <span>🛡️</span>
        <span>ADMIN MODE</span>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🛡️ Dashboard Administrateur</h1>
            <div class="user-info">
                <span>Admin: <?php echo htmlspecialchars($currentAdmin['username']); ?></span>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($currentAdmin['username'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <h3>👥 Total Utilisateurs</h3>
                <div class="card-value"><?php echo $stats['total_users']; ?></div>
            </div>
            <div class="card">
                <h3>🛡️ Administrateurs</h3>
                <div class="card-value"><?php echo $stats['total_admins']; ?></div>
            </div>
            <div class="card">
                <h3>👤 Connecté en tant que</h3>
                <div class="card-value" style="font-size: 18px;">
                    <?php echo htmlspecialchars($currentAdmin['username']); ?>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="content-section">
            <div class="section-header">
                <h2>🚀 Actions Rapides</h2>
            </div>
            <div class="quick-actions">
                <a href="../../views/dashboard/index.php" class="action-btn">
                    <span>📋</span>
                    <span>Voir tous les users</span>
                </a>
                <a href="../../views/auth/signup.php" class="action-btn">
                    <span>➕</span>
                    <span>Ajouter un user</span>
                </a>
                <a href="../../views/dashboard/menu.php" class="action-btn">
                    <span>🏠</span>
                    <span>Menu principal</span>
                </a>
                <a href="admin-logout.php" class="action-btn">
                    <span>🚪</span>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="content-section">
            <div class="section-header">
                <h2>👥 Liste des Utilisateurs</h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Date création</th>
                        <th>Actions Admin</th>
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
                                        echo date('d/m/Y H:i', strtotime($user['created_at']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="../../views/dashboard/edit.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-small btn-edit" 
                                           title="Modifier">✏️ Modifier</a>
                                        <button onclick="deleteUser(<?php echo $user['id']; ?>)" 
                                                class="btn btn-small btn-delete" 
                                                title="Supprimer">🗑️ Supprimer</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">
                                Aucun utilisateur enregistré
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Actions -->
        <div class="footer-actions">
            <a href="admin-logout.php" class="btn btn-logout">🚪 Déconnexion Admin</a>
        </div>
    </div>

    <script>
    function deleteUser(id) {
        if (confirm('⚠️ ATTENTION ADMIN\n\nÊtes-vous sûr de vouloir supprimer ce compte utilisateur ?\n\nCette action est irréversible et vous avez les droits admin pour effectuer cette opération.')) {
            window.location.href = '../../views/dashboard/delete.php?id=' + id;
        }
    }

    // Auto-dismiss des alertes
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    });

    console.log('🛡️ Dashboard Administrateur - Accès complet au système');
    </script>
</body>
</html>