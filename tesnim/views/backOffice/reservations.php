<?php
// Définir le chemin de base pour les assets
$base_url = dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))) . '/';

include '../../controller/DemandeController.php';

$demandeC = new DemandeController();
$success = "";

// Suppression
if (isset($_GET['delete'])) {
    $demandeC->deleteDemande($_GET['delete']);
    $success = "Réservation supprimée avec succès !";
}

// Liste des demandes
$list = $demandeC->listDemandes();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservations - Admin</title>
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
                <a href="dashboard.php" class="nav-link">
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
                <a href="reservations.php" class="nav-link active">
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
                <h1>Réservations</h1>
                <p>Gérez toutes les réservations des candidats</p>
            </div>
        </div>

        <!-- Alert Success -->
        <?php if($success): ?>
        <div class="card" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724;">
            <p><strong>✓</strong> <?php echo htmlspecialchars($success); ?></p>
        </div>
        <?php endif; ?>

        <!-- Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Liste des Réservations</h2>
                <div>
                    <span class="badge badge-info">Total: <?php echo $list->rowCount(); ?></span>
                </div>
            </div>
            <div class="table-responsive">
                <?php if($list->rowCount() > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Candidat</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Type Entretien</th>
                            <th>Date Session</th>
                            <th>Heure</th>
                            <th>Date Réservation</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $demande): 
                            $dateSession = new DateTime($demande['date']);
                            $dateDemande = new DateTime($demande['date_demande']);
                        ?>
                        <tr>
                            <td><?php echo $demande['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($demande['nom']); ?></strong></td>
                            <td><?php echo htmlspecialchars($demande['tel']); ?></td>
                            <td><?php echo htmlspecialchars($demande['email']); ?></td>
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
                            <td><?php echo $dateSession->format('d/m/Y'); ?></td>
                            <td><?php echo substr($demande['heure'], 0, 5); ?></td>
                            <td><?php echo $dateDemande->format('d/m/Y H:i'); ?></td>
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
                            <td>
                                <a href="?delete=<?php echo $demande['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i>📭</i>
                    <h3>Aucune réservation</h3>
                    <p>Les réservations apparaîtront ici.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>