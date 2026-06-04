<?php
include 'controller/FormationController.php';
include 'controller/DemandeController.php';

$formationC = new FormationController();
$demandeC = new DemandeController();

$stats = $formationC->getStatistiques();
$totalDemandes = $demandeC->countDemandes();
$recentDemandes = $demandeC->getRecentDemandes(5);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">

<head>
    <title>Administration - FormationPro</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/back.css">
</head>

<body>

    <aside class="sidebar-nav-wrapper">
        <div class="navbar-logo">
            <h2 class="admin-logo">FormationPro Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item active">
                    <a href="admin.php">
                        <span class="icon">📊</span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="views/backOffice/formationList.php">
                        <span class="icon">📚</span>
                        <span class="text">Gestion Formations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="views/backOffice/demandeList.php">
                        <span class="icon">📝</span>
                        <span class="text">Gestion Inscriptions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php">
                        <span class="icon">🏠</span>
                        <span class="text">Retour Site</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="overlay"></div>

    <main class="main-wrapper">
        <header class="header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="header-left">
                            <div class="menu-toggle-btn">
                                <button id="menu-toggle" class="main-btn">
                                    ☰ Menu
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="header-right">
                            <div class="profile-box">
                                <div class="profile-info">
                                    <h6>Administrateur</h6>
                                    <p>FormationPro</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section class="section">
            <div class="container-fluid">
                <div class="title-wrapper pt-30">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title">
                                <h2>Tableau de bord</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card stat-card-blue">
                            <div class="stat-icon">📚</div>
                            <div class="stat-content">
                                <h3><?php echo $stats['total_formations'] ?? 0; ?></h3>
                                <p>Formations actives</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card stat-card-green">
                            <div class="stat-icon">✅</div>
                            <div class="stat-content">
                                <h3><?php echo $stats['total_inscriptions'] ?? 0; ?></h3>
                                <p>Inscriptions totales</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card stat-card-orange">
                            <div class="stat-icon">👥</div>
                            <div class="stat-content">
                                <h3><?php echo $stats['places_disponibles'] ?? 0; ?></h3>
                                <p>Places disponibles</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card stat-card-purple">
                            <div class="stat-icon">📝</div>
                            <div class="stat-content">
                                <h3><?php echo $totalDemandes; ?></h3>
                                <p>Total demandes</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-30">
                    <div class="col-lg-12">
                        <div class="card-style">
                            <div class="card-header">
                                <h3>Inscriptions récentes</h3>
                                <a href="views/backOffice/demandeList.php" class="btn-link">Voir tout →</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Numéro</th>
                                            <th>Participant</th>
                                            <th>Formation</th>
                                            <th>Date inscription</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($recentDemandes):
                                            foreach ($recentDemandes as $demande):
                                        ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($demande['numero_demande']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($demande['nom']); ?></td>
                                                    <td><?php echo htmlspecialchars($demande['formation_nom']); ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($demande['date_inscription'])); ?></td>
                                                    <td><span class="badge badge-success"><?php echo htmlspecialchars($demande['statut']); ?></span></td>
                                                </tr>
                                            <?php
                                            endforeach;
                                        else:
                                            ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Aucune inscription récente</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-30">
                    <div class="col-lg-6">
                        <div class="card-style">
                            <div class="card-header">
                                <h3>Actions rapides</h3>
                            </div>
                            <div class="quick-actions">
                                <a href="views/backOffice/addFormation.php" class="action-btn action-btn-blue">
                                    <span class="icon">➕</span>
                                    <span class="text">Ajouter une formation</span>
                                </a>
                                <a href="views/backOffice/formationList.php" class="action-btn action-btn-green">
                                    <span class="icon">📋</span>
                                    <span class="text">Gérer les formations</span>
                                </a>
                                <a href="views/backOffice/demandeList.php" class="action-btn action-btn-orange">
                                    <span class="icon">👥</span>
                                    <span class="text">Voir les inscriptions</span>
                                </a>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="copyright text-center">
                            <p>&copy; 2024 FormationPro. Tous droits réservés.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script src="assets/js/back.js"></script>
</body>

</html>