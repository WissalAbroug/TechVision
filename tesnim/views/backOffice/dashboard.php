<?php
// Définir le chemin de base pour les assets
$base_url = dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))) . '/';

include '../../controller/EntretienController.php';
include '../../controller/DemandeController.php';

$entretienC = new EntretienController();
$demandeC = new DemandeController();

// Récupérer les statistiques
$stats = $entretienC->getStatistiques();
$recentDemandes = $demandeC->getRecentDemandes(5);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Entretiens</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assests/css/back.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <h2>🎯 Admin Panel</h2>
            <p>Gestion des Entretiens</p>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i>📊</i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="entretiens.php" class="nav-link">
                    <i>📅</i>
                    <span>Gestion Sessions</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="reservations.php" class="nav-link">
                    <i>📝</i>
                    <span>Réservations</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../frontOffice/quiz.php" class="nav-link">
                    <i>🧠</i>
                    <span>Quiz Entretien</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../frontOffice/index.php" class="nav-link">
                    <i>🌐</i>
                    <span>Site Public</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="admin-header">
            <div class="page-title">
                <h1>Tableau de Bord</h1>
                <p>Vue d'ensemble de l'activité</p>
            </div>
            <div class="admin-actions">
                <a href="entretiens.php?action=add" class="btn btn-primary">+ Nouvelle Session</a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-number"><?php echo $stats['total_sessions']; ?></div>
                <div class="stat-label">Total Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔜</div>
                <div class="stat-number"><?php echo $stats['sessions_avenir']; ?></div>
                <div class="stat-label">Sessions à Venir</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-number"><?php echo $stats['total_reservations']; ?></div>
                <div class="stat-label">Total Réservations</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🆕</div>
                <div class="stat-number"><?php echo $stats['reservations_aujourdhui']; ?></div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
        </div>

        <!-- Recent Reservations -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Réservations Récentes</h2>
                <a href="reservations.php" class="btn btn-sm btn-primary">Voir Tout</a>
            </div>
            <div class="table-responsive">
                <?php if($recentDemandes->rowCount() > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Candidat</th>
                            <th>Contact</th>
                            <th>Type Entretien</th>
                            <th>Date Session</th>
                            <th>Date Demande</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentDemandes as $demande): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($demande['nom']); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($demande['email']); ?><br>
                                <small><?php echo htmlspecialchars($demande['tel']); ?></small>
                            </td>
                            <td>
                                <?php 
                                $typeClass = 'badge-info';
                                if($demande['type'] == 'RH') $typeClass = 'badge-warning';
                                if($demande['type'] == 'Mixte') $typeClass = 'badge-success';
                                ?>
                                <span class="badge <?php echo $typeClass; ?>">
                                    <?php echo htmlspecialchars($demande['type']); ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $dateObj = new DateTime($demande['date']);
                                echo $dateObj->format('d/m/Y');
                                ?> 
                                à <?php echo substr($demande['heure'], 0, 5); ?>
                            </td>
                            <td>
                                <?php 
                                $dateDemande = new DateTime($demande['date_demande']);
                                echo $dateDemande->format('d/m/Y H:i');
                                ?>
                            </td>
                            <td>
                                <?php 
                                $statutClass = 'badge-warning';
                                if($demande['statut'] == 'Confirmé') $statutClass = 'badge-success';
                                if($demande['statut'] == 'Annulé') $statutClass = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $statutClass; ?>">
                                    <?php echo htmlspecialchars($demande['statut']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i>📭</i>
                    <h3>Aucune réservation récente</h3>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>