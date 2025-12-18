<?php
// Définir le chemin de base pour les assets
$base_url = dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))) . '/';
include '../../controller/EntretienController.php';
$entretienC = new EntretienController();
$list = $entretienC->listEntretiensDisponibles();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Préparation aux Entretiens - Plateforme de Recrutement</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assests/css/front.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <h1>🎯 Interview Prep</h1>
                <p>Préparez-vous pour votre prochain entretien</p>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="quiz.php">📝 Quiz Entretien</a></li>
                <li><a href="../backOffice/dashboard.php">Dashboard Admin</a></li>
            </ul>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <div class="container">
            <h1>Sessions de Préparation</h1>
            <p>Réservez votre session de simulation d'entretien avec nos coachs professionnels</p>
        </div>
    </div>

    <!-- Section Entretiens -->
    <div class="container">
        <div class="entretiens-section">
            <div class="section-title">
                <h2>Entretiens Disponibles</h2>
                <p>Choisissez le type d'entretien qui correspond à vos besoins</p>
            </div>

            <?php if($list->rowCount() > 0): ?>
            <div class="entretiens-grid">
                <?php
                foreach($list as $entretien) {
                    $placesRestantes = $entretien['places'] - $entretien['places_prises'];
                    $estComplet = $placesRestantes <= 0;
                    $dateObj = new DateTime($entretien['date']);
                    $dateFormatee = $dateObj->format('d/m/Y');
                    $heureFormatee = substr($entretien['heure'], 0, 5);
                    
                    // Classe du type
                    $typeClass = 'type-technique';
                    if($entretien['type'] == 'RH') $typeClass = 'type-rh';
                    if($entretien['type'] == 'Mixte') $typeClass = 'type-mixte';
                ?>
                <div class="entretien-card">
                    <div class="entretien-header">
                        <span class="type-badge <?php echo $typeClass; ?>">
                            <?php echo htmlspecialchars($entretien['type']); ?>
                        </span>
                        <?php if($estComplet): ?>
                        <span class="status-badge status-complet">COMPLET</span>
                        <?php else: ?>
                        <span class="status-badge status-disponible">DISPONIBLE</span>
                        <?php endif; ?>
                    </div>

                    <div class="entretien-info">
                        <div class="info-row">
                            <i>📅</i>
                            <span><strong>Date:</strong> <?php echo $dateFormatee; ?></span>
                        </div>
                        <div class="info-row">
                            <i>🕐</i>
                            <span><strong>Heure:</strong> <?php echo $heureFormatee; ?></span>
                        </div>
                    </div>

                    <div class="places-info">
                        <div class="places-number"><?php echo $placesRestantes; ?></div>
                        <div class="places-label">place(s) restante(s)</div>
                    </div>

                    <button class="btn-reserver" 
                            onclick="openModal(<?php echo $entretien['id']; ?>, '<?php echo htmlspecialchars($entretien['type']); ?>', '<?php echo $dateFormatee; ?>', '<?php echo $heureFormatee; ?>')"
                            <?php echo $estComplet ? 'disabled' : ''; ?>>
                        <?php echo $estComplet ? 'Complet' : 'Réserver'; ?>
                    </button>
                </div>
                <?php } ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i>📭</i>
                <h3>Aucun entretien disponible</h3>
                <p>Revenez plus tard pour découvrir de nouvelles sessions</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Réservation -->
    <div id="reservationModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h2 class="modal-title">Réserver un Entretien</h2>
            
            <div id="modalAlert"></div>
            
            <form id="reservationForm" action="reserver.php" method="POST">
                <input type="hidden" id="entretienId" name="entretien_id">
                
                <div class="form-group">
                    <label class="form-label">Type d'entretien</label>
                    <input type="text" class="form-control" id="modalType" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Date et Heure</label>
                    <input type="text" class="form-control" id="modalDateTime" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Nom Complet *</label>
                    <input type="text" class="form-control" name="nom" id="nom" placeholder="Ex: Ahmed Ben Salem">
                </div>

                <div class="form-group">
                    <label class="form-label">Téléphone *</label>
                    <input type="tel" class="form-control" name="tel" id="tel" placeholder="Ex: 22123456">
                </div>

                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Ex: exemple@email.com">
                </div>

                <button type="submit" class="btn-submit">Confirmer la Réservation</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <p>&copy; 2025 Interview Prep Platform. Tous droits réservés.</p>
            <p>Développé par Esprit Student</p>
        </div>
    </div>

    <script src="<?php echo $base_url; ?>assests/js/front.js"></script>
</body>
</html>