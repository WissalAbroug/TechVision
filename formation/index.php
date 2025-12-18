<?php
// Correction du chemin d'inclusion
include __DIR__ . '/controller/FormationController.php';
$formationC = new FormationController();
$list = $formationC->listFormationsDisponibles();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">

<head>
    <title>Formations Professionnelles - Plateforme de Recrutement</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="assets/css/front.css">
</head>

<body>
    <div id="header-wrap">
        <header id="header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="main-logo">
                            <h1 class="logo-text">FormationPro</h1>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <nav id="navbar">
                            <ul class="menu-list">
                                <li class="menu-item active"><a href="index.php">Accueil</a></li>
                                <li class="menu-item"><a href="#formations">Formations</a></li>
                                <li class="menu-item"><a href="#contact">Contact</a></li>
                                <li class="menu-item"><a href="admin.php">Administration</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
    </div>

    <section id="hero" class="hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hero-content">
                        <h1 class="h1">Formations Professionnelles</h1>
                        <p class="hero-desc">Développez vos compétences avec nos formations de qualité</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="formations" class="formations section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <h2 class="h2">Formations Disponibles</h2>
                        <p>Découvrez notre catalogue de formations professionnelles</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php
                if (!empty($list)):
                    foreach ($list as $formation):
                        $placesRestantes = $formationC->getPlacesRestantes($formation);
                        $isComplete = $formationC->isComplete($formation);
                        $statusClass = $isComplete ? 'complet' : 'disponible';
                        $statusText = $isComplete ? 'COMPLET' : $placesRestantes . ' places restantes';
                ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="formation-card">
                                <div class="formation-header">
                                    <h3 class="formation-title"><?php echo htmlspecialchars($formation['nom']); ?></h3>
                                    <!-- NIVEAU RÉAFFICHÉ ICI -->
                                    <span class="formation-niveau niveau-<?php echo strtolower($formation['niveau']); ?>">
                                        <?php echo htmlspecialchars($formation['niveau']); ?>
                                    </span>
                                </div>
                                <div class="formation-content">
                                    <div class="formation-info">
                                        <span class="info-icon">📅</span>
                                        <span class="info-text"><?php echo date('d/m/Y', strtotime($formation['date_formation'])); ?></span>
                                    </div>
                                    <div class="formation-info">
                                        <span class="info-icon">💰</span>
                                        <span class="info-text"><?php echo number_format($formation['prix'], 2); ?> TND</span>
                                    </div>
                                    <div class="formation-info">
                                        <span class="info-icon">👥</span>
                                        <span class="info-text status-<?php echo $statusClass; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </div>
                                    <?php if ($formation['description']): ?>
                                        <p class="formation-description"><?php echo htmlspecialchars(substr($formation['description'], 0, 100)); ?>...</p>
                                    <?php endif; ?>
                                </div>
                                <div class="formation-footer">
                                    <?php if (!$isComplete): ?>
                                        <a href="views/frontOffice/inscription.php?id=<?php echo $formation['id']; ?>" class="btn-inscription">
                                            S'inscrire
                                        </a>
                                    <?php else: ?>
                                        <button class="btn-complet" disabled>COMPLET</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    endforeach;
                else:
                    ?>
                    <div class="col-lg-12">
                        <div class="alert alert-info text-center">
                            Aucune formation disponible pour le moment.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <footer id="footer" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h3>FormationPro</h3>
                        <p>Votre partenaire pour le développement professionnel</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h4 class="footer-title">Liens Rapides</h4>
                        <ul class="footer-links">
                            <li><a href="index.php">Accueil</a></li>
                            <li><a href="#formations">Formations</a></li>
                            <li><a href="admin.php">Administration</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <h4 class="footer-title">Contact</h4>
                        <div class="contact-info">
                            <p>📞 +216 XX XXX XXX</p>
                            <p>📧 contact@formationpro.tn</p>
                            <p>📍 Tunis, Tunisie</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer-bottom">
                        <p>&copy; 2024 FormationPro. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/front.js"></script>
</body>

</html>