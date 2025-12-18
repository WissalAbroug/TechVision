<?php
require_once __DIR__ . '/../../controller/FormationController.php';
require_once __DIR__ . '/../../controller/DemandeController.php';
require_once __DIR__ . '/../../model/DemandeFormation.php';

// Désactiver l'affichage des erreurs pour éviter les messages avant les toasts
error_reporting(0);

$error = "";
$success = "";
$numeroDemande = "";
$detailsInscription = [];

$formationC = new FormationController();
$demandeC = new DemandeController();

// Récupérer l'ID de la formation
$formationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$formation = null;

if ($formationId > 0) {
    $formation = $formationC->showFormation($formationId);
}

// Traiter le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nom'], $_POST['email'], $_POST['tel'], $_POST['formation_id'], $_POST['mode_paiement'])) {

        $nom = trim($_POST['nom']);
        $email = trim($_POST['email']);
        $tel = trim($_POST['tel']);
        $formation_id = (int)$_POST['formation_id'];
        $mode_paiement = trim($_POST['mode_paiement']);

        // Récupérer la formation pour obtenir son niveau
        $formationSelected = $formationC->showFormation($formation_id);
        $niveau = $formationSelected ? $formationSelected['niveau'] : 'Débutant';

        if (!empty($nom) && !empty($email) && !empty($tel) && $formation_id > 0 && !empty($mode_paiement)) {

            // Valider l'email et le téléphone en utilisant les méthodes du contrôleur
            if (!$demandeC->validateEmail($email)) {
                $error = "Adresse email invalide";
            } elseif (!$demandeC->validateTel($tel)) {
                $error = "Numéro de téléphone invalide (minimum 8 chiffres)";
            } else {
                // Créer la demande en utilisant la méthode du contrôleur
                $demande = $demandeC->createDemandeFromArray([
                    'nom' => $nom,
                    'email' => $email,
                    'tel' => $tel,
                    'formation_id' => $formation_id,
                    'statut' => 'Confirmée',
                    'niveau' => $niveau, // Niveau automatique de la formation
                    'mode_paiement' => $mode_paiement
                ]);

                if ($demandeC->addDemande($demande)) {
                    $success = "Inscription réussie! 😊";
                    $numeroDemande = $demande->getNumeroDemande();

                    // Stocker les détails pour l'affichage
                    $detailsInscription = [
                        'nom' => $nom,
                        'email' => $email,
                        'tel' => $tel,
                        'numero_demande' => $numeroDemande,
                        'niveau' => $niveau,
                        'mode_paiement' => $mode_paiement,
                        'formation_nom' => $formationSelected['nom'],
                        'formation_date' => date('d/m/Y', strtotime($formationSelected['date_formation'])),
                        'formation_prix' => number_format($formationSelected['prix'], 2),
                        'date_inscription' => date('d/m/Y H:i'),
                        'places_prises' => $formationSelected['places_prises'] + 1,
                        'places_max' => $formationSelected['places_max']
                    ];
                } else {
                    $error = "Vous êtes déjà inscrit à cette formation ou les places sont complètes";
                }
            }
        } else {
            $error = "Veuillez remplir tous les champs";
        }
    }
}

// Récupérer toutes les formations pour la liste déroulante
$listeFormations = $formationC->listFormationsDisponibles();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">

<head>
    <title>Inscription Formation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../assets/css/front.css">
    <style>
        /* Styles supplémentaires pour la carte de confirmation */
        .confirmation-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 15px;
            padding: 30px;
            margin-top: 20px;
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);
            position: relative;
            overflow: hidden;
        }

        .confirmation-header {
            margin-bottom: 30px;
            position: relative;
            padding-left: 180px;
            /* Espace pour le logo */
            min-height: 150px;
        }

        .confirmation-logo-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 150px;
            height: 150px;
        }

        .confirmation-logo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #0ea5e9;
            padding: 5px;
            background: white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.4s ease;
        }

        .confirmation-logo:hover {
            transform: rotate(5deg) scale(1.05);
            border-color: #3b82f6;
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.25);
        }

        .logo-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #10b981;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            border: 2px solid white;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.4);
            z-index: 2;
        }

        .header-text-section {
            padding-top: 20px;
        }

        .confirmation-title {
            color: #065f46;
            font-size: 28px;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .confirmation-subtitle {
            color: #4b5563;
            font-size: 16px;
            font-weight: 500;
        }

        .confirmation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .confirmation-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .confirmation-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .confirmation-value {
            color: #111827;
            font-size: 16px;
        }

        .niveau-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .niveau-débutant {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }

        .niveau-moyen {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .niveau-expert {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .mode-paiement-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .mode-carte {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #5b21b6;
            border: 1px solid #8b5cf6;
        }

        .mode-paypal {
            background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        .mode-mobile {
            background: linear-gradient(135deg, #86efac 0%, #4ade80 100%);
            color: #166534;
            border: 1px solid #22c55e;
        }

        .numero-demande {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #5b21b6;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            margin: 20px 0;
            display: inline-block;
            border: 2px solid #8b5cf6;
            box-shadow: 0 4px 6px rgba(139, 92, 246, 0.2);
        }

        .confirmation-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }

        .confirmation-message {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn-imprimer {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            margin-right: 10px;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
        }

        .btn-imprimer:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-retour {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(107, 114, 128, 0.3);
        }

        .btn-retour:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(107, 114, 128, 0.4);
        }

        /* Styles pour l'impression - Ne pas masquer le logo */
        @media print {
            body * {
                visibility: hidden;
            }

            .confirmation-card,
            .confirmation-card * {
                visibility: visible;
            }

            .confirmation-card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                border: 2px solid #000;
                border-radius: 0;
                padding: 20px;
            }

            .confirmation-logo {
                border: 2px solid #000;
                width: 120px;
                height: 120px;
            }

            .confirmation-header {
                padding-left: 140px;
                min-height: 120px;
            }

            .confirmation-logo-section {
                width: 120px;
                height: 120px;
            }

            .confirmation-footer,
            .btn-imprimer,
            .btn-retour,
            .confirmation-message br {
                display: none !important;
            }

            .confirmation-message {
                margin-top: 20px;
                text-align: center;
                font-size: 12px;
                color: #000;
            }

            .confirmation-title {
                color: #000;
            }

            .confirmation-grid {
                margin-bottom: 20px;
            }
        }

        /* Styles pour la section cours et test */
        .section-cours-test {
            margin-top: 40px;
            padding: 30px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 15px;
            border: 2px solid #dbeafe;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cours-content {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .cours-content h4 {
            color: #059669;
            margin-bottom: 15px;
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cours-content p {
            font-size: 16px;
            line-height: 1.7;
            color: #4b5563;
        }

        .domaine-badge {
            display: inline-block;
            padding: 8px 15px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .objectifs-box {
            margin-top: 20px;
            padding: 15px;
            background: #f0f9ff;
            border-radius: 8px;
            border-left: 4px solid #0ea5e9;
        }

        .objectifs-box h5 {
            color: #0369a1;
            margin-bottom: 10px;
        }

        .objectifs-box ul {
            color: #475569;
            padding-left: 20px;
        }

        .objectifs-box li {
            margin-bottom: 5px;
        }

        .btn-test {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(16, 185, 129, 0.4);
        }

        .test-info {
            margin-top: 15px;
            color: #6b7280;
            font-size: 14px;
        }

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .success-border {
            border-color: #10b981 !important;
        }

        .error-border {
            border-color: #ef4444 !important;
        }

        /* Styles pour les toasts - IMPORTANT: Positionner correctement */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
        }

        .toast {
            background: white;
            border-radius: 10px;
            padding: 16px 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 15px;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-left: 5px solid;
            animation: slideInRight 0.5s forwards;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(400px);
            opacity: 0;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #9ca3af;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .toast-close:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .toast-error {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }

        .toast-error .toast-icon {
            color: #ef4444;
        }

        .toast-error .toast-title {
            color: #991b1b;
        }

        .toast-success {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .toast-success .toast-icon {
            color: #10b981;
        }

        .toast-success .toast-title {
            color: #065f46;
        }

        .toast-warning {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        }

        .toast-warning .toast-icon {
            color: #f59e0b;
        }

        .toast-warning .toast-title {
            color: #92400e;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
            border-radius: 0 0 0 5px;
            animation: progress 5s linear forwards;
        }

        @keyframes progress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* Style pour cacher les messages PHP qui pourraient apparaître avant le HTML */
        body {
            margin: 0;
            padding: 0;
        }

        /* Style pour les alertes PHP existantes */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        /* Responsive design pour le logo */
        @media (max-width: 768px) {
            .confirmation-header {
                padding-left: 0;
                text-align: center;
                min-height: auto;
            }

            .confirmation-logo-section {
                position: relative;
                margin: 0 auto 20px;
                width: 120px;
                height: 120px;
            }

            .confirmation-logo {
                width: 120px;
                height: 120px;
            }

            .header-text-section {
                padding-top: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Container pour les toasts -->
    <div class="toast-container" id="toastContainer"></div>

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
                                <li class="menu-item"><a href="../../index.php">Accueil</a></li>
                                <li class="menu-item active"><a href="#">Inscription</a></li>
                                <li class="menu-item"><a href="../../admin.php">Administration</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
    </div>

    <section class="inscription-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">
                    <div class="inscription-card">
                        <h2 class="inscription-title">Formulaire d'inscription</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger" style="display: none;" id="php-error">
                                <strong>Erreur:</strong> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <!-- FORMULAIRE ET CONFIRMATION SUR LA MÊME PAGE -->
                        <?php if ($success): ?>
                            <!-- Affichage de la confirmation -->
                            <div class="confirmation-card">
                                <div class="confirmation-header">
                                    <!-- Logo à gauche -->
                                    <div class="confirmation-logo-section">
                                        <img src="../../assets/logo.jpg" alt="Logo FormationPro" class="confirmation-logo">
                                        <div class="logo-badge">✓</div>
                                    </div>

                                    <!-- Texte à droite -->
                                    <div class="header-text-section">
                                        <h2 class="confirmation-title">Votre inscription a été confirmée avec succès😊</h2>
                                        <p class="confirmation-subtitle">FormationPro - Centre de Formation Agréé</p>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <div class="numero-demande">
                                        N°: <?php echo $detailsInscription['numero_demande']; ?>
                                    </div>
                                </div>

                                <div class="confirmation-grid">
                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Nom complet</div>
                                        <div class="confirmation-value"><?php echo htmlspecialchars($detailsInscription['nom']); ?></div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Email</div>
                                        <div class="confirmation-value"><?php echo htmlspecialchars($detailsInscription['email']); ?></div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Téléphone</div>
                                        <div class="confirmation-value"><?php echo htmlspecialchars($detailsInscription['tel']); ?></div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Formation</div>
                                        <div class="confirmation-value">
                                            <strong><?php echo htmlspecialchars($detailsInscription['formation_nom']); ?></strong>
                                        </div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Date de la formation</div>
                                        <div class="confirmation-value">📅 <?php echo $detailsInscription['formation_date']; ?></div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Prix</div>
                                        <div class="confirmation-value">💰 <?php echo $detailsInscription['formation_prix']; ?> TND</div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Niveau</div>
                                        <div class="confirmation-value">
                                            <span class="niveau-badge niveau-<?php echo strtolower($detailsInscription['niveau']); ?>">
                                                <?php echo htmlspecialchars($detailsInscription['niveau']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Mode de paiement</div>
                                        <div class="confirmation-value">
                                            <?php
                                            $mode_class = '';
                                            $mode_icon = '';
                                            switch ($detailsInscription['mode_paiement']) {
                                                case 'Carte bancaire':
                                                    $mode_class = 'mode-carte';
                                                    $mode_icon = '💳';
                                                    break;
                                                case 'PayPal':
                                                    $mode_class = 'mode-paypal';
                                                    $mode_icon = '🏦';
                                                    break;
                                                case 'Paiement mobile':
                                                    $mode_class = 'mode-mobile';
                                                    $mode_icon = '📱';
                                                    break;
                                            }
                                            ?>
                                            <span class="mode-paiement-badge <?php echo $mode_class; ?>">
                                                <?php echo $mode_icon . ' ' . htmlspecialchars($detailsInscription['mode_paiement']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Date d'inscription</div>
                                        <div class="confirmation-value">📝 <?php echo $detailsInscription['date_inscription']; ?></div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Statut</div>
                                        <div class="confirmation-value">
                                            <span style="color: #10b981; font-weight: bold;">✅ Confirmée</span>
                                        </div>
                                    </div>

                                    <div class="confirmation-item">
                                        <div class="confirmation-label">Places</div>
                                        <div class="confirmation-value">
                                            👥 <?php echo $detailsInscription['places_prises']; ?>/<?php echo $detailsInscription['places_max']; ?> inscrits
                                        </div>
                                    </div>
                                </div>

                                <div class="confirmation-footer">
                                    <p class="confirmation-message">
                                        <strong>📧 Un email de confirmation a été envoyé à votre adresse.</strong><br>
                                        Conservez ce numéro de demande pour toute correspondance future.
                                    </p>

                                    <button onclick="window.print()" class="btn-imprimer">
                                        🖨️ Imprimer cette confirmation
                                    </button>
                                    <a href="../../index.php" class="btn-retour">
                                        🏠 Retour à l'accueil
                                    </a>
                                </div>
                            </div>

                            <!-- NOUVELLE SECTION : Cours et Test -->
                            <div class="section-cours-test">
                                <h3 style="text-align: center; color: #1e40af; margin-bottom: 20px;">
                                    🎓 Votre Formation : <?php echo htmlspecialchars($detailsInscription['formation_nom']); ?>
                                </h3>

                                <!-- Affichage du cours selon le niveau -->
                                <div class="cours-content">
                                    <h4>📚 Cours <?php echo htmlspecialchars($detailsInscription['niveau']); ?></h4>

                                    <?php
                                    // Définir les cours par domaine et niveau
                                    $cours_par_domaine = [
                                        'DATA SCIENCE' => [
                                            'Débutant' => "Apprends Excel avancé avec les tableaux croisés dynamiques et Power Query pour transformer des données brutes. Maîtrise SQL avec SELECT, WHERE et JOIN pour extraire des informations des bases de données. Crée des dashboards visuels qui répondent aux questions métier des entreprises.",
                                            'Moyen' => "Passe à Python avec les bibliothèques Pandas et NumPy pour automatiser l'analyse de données. Utilise Scikit-learn pour implémenter des algorithmes de machine learning comme la régression et la classification. Applique les tests statistiques pour valider scientifiquement tes hypothèses business.",
                                            'Expert' => "Industrialise les modèles ML avec Docker pour créer des conteneurs reproductibles. Construis des pipelines de données avec Airflow et déploie des APIs avec FastAPI. Monitor la performance des modèles en production et gère le versioning avec MLflow."
                                        ],
                                        'CYBERSÉCURITÉ' => [
                                            'Débutant' => "Comprends les fondamentaux avec la triade CIA (Confidentialité, Intégrité, Disponibilité). Apprends les bases du réseau TCP/IP et les menaces courantes comme les malware. Analyse des logs simples et configure des pare-feux basiques.",
                                            'Moyen' => "Utilise Kali Linux avec des outils comme Nmap pour scanner les réseaux. Teste les applications web contre les failles OWASP Top 10 avec Burp Suite. Rédige des rapports professionnels détaillant les vulnérabilités trouvées et les solutions.",
                                            'Expert' => "Sécurise les environnements AWS/Azure avec IAM pour gérer les accès. Configure les VPC (Virtual Private Cloud) et implémente le chiffrement des données. Applique les frameworks de compliance et architecte des solutions cloud sécurisées."
                                        ],
                                        'DÉVELOPPEMENT WEB' => [
                                            'Débutant' => "Apprends HTML5 pour structurer le contenu des pages web sémantiquement. Maîtrise CSS3 avec Flexbox et Grid pour créer des layouts responsives. Utilise JavaScript pour rendre les pages interactives et dynamiques.",
                                            'Moyen' => "Développe des applications front-end avec React en utilisant les hooks et composants. Crée des APIs back-end avec Node.js et Express pour gérer la logique métier. Connecte des bases de données et implémente l'authentification utilisateur.",
                                            'Expert' => "Conçois des architectures microservices avec Docker et Kubernetes. Optimise les performances avec le caching, les CDN et la base de données. Implémente la sécurité avancée avec les bonnes pratiques OWASP et les tests de pénétration."
                                        ]
                                    ];

                                    // Déterminer le domaine
                                    $formation_nom = $detailsInscription['formation_nom'];
                                    $domaine = 'DATA SCIENCE'; // Par défaut

                                    if (stripos($formation_nom, 'cyber') !== false || stripos($formation_nom, 'sécurité') !== false) {
                                        $domaine = 'CYBERSÉCURITÉ';
                                    } elseif (stripos($formation_nom, 'web') !== false || stripos($formation_nom, 'développement') !== false) {
                                        $domaine = 'DÉVELOPPEMENT WEB';
                                    }

                                    $niveau = $detailsInscription['niveau'];
                                    $cours = $cours_par_domaine[$domaine][$niveau] ?? "Cours détaillé disponible bientôt pour ce niveau.";
                                    ?>

                                    <div class="domaine-badge">
                                        Domaine : <?php echo $domaine; ?>
                                    </div>

                                    <p><?php echo $cours; ?></p>

                                    <div class="objectifs-box">
                                        <h5>🎯 Objectifs d'apprentissage :</h5>
                                        <ul>
                                            <?php
                                            $objectifs = [
                                                'Débutant' => ['Maîtriser les bases', 'Appliquer les concepts fondamentaux', 'Réaliser des exercices pratiques'],
                                                'Moyen' => ['Développer des projets complets', 'Résoudre des problèmes complexes', 'Travailler en équipe'],
                                                'Expert' => ['Concevoir des architectures', 'Manager des projets', 'Former d\'autres développeurs']
                                            ];

                                            foreach ($objectifs[$niveau] as $objectif) {
                                                echo "<li>✓ $objectif</li>";
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Bouton accéder au test -->
                                <div style="text-align: center; margin-top: 30px;">
                                    <a href="test_formation.php?numero=<?php echo urlencode($detailsInscription['numero_demande']); ?>&nom=<?php echo urlencode($detailsInscription['nom']); ?>&formation=<?php echo urlencode($detailsInscription['formation_nom']); ?>&niveau=<?php echo urlencode($detailsInscription['niveau']); ?>"
                                        class="btn-test">
                                        📝 Accéder au Test de Niveau
                                    </a>
                                    <p class="test-info">
                                        Testez vos connaissances et obtenez votre attestation de réussite !
                                    </p>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Affichage du formulaire d'inscription -->
                            <form action="" method="POST" class="inscription-form" id="inscriptionForm">
                                <div class="form-group">
                                    <label for="nom">Nom complet *</label>
                                    <input type="text" class="form-control" id="nom" name="nom"
                                        value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>"
                                        placeholder="Votre nom complet">
                                    <small class="error-message" id="nom-error"></small>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                        placeholder="votre.email@exemple.com">
                                    <small class="error-message" id="email-error"></small>
                                </div>

                                <div class="form-group">
                                    <label for="tel">Téléphone *</label>
                                    <input type="tel" class="form-control" id="tel" name="tel"
                                        value="<?php echo isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : ''; ?>"
                                        placeholder="Ex: 20123456">
                                    <small>Format: minimum 8 chiffres</small>
                                    <small class="error-message" id="tel-error"></small>
                                </div>

                                <div class="form-group">
                                    <label for="mode_paiement">Mode de paiement *</label>
                                    <select class="form-control" id="mode_paiement" name="mode_paiement">
                                        <option value="">-- Choisissez un mode de paiement --</option>
                                        <option value="Carte bancaire" <?php echo (isset($_POST['mode_paiement']) && $_POST['mode_paiement'] == 'Carte bancaire') ? 'selected' : ''; ?>>Carte bancaire</option>
                                        <option value="PayPal" <?php echo (isset($_POST['mode_paiement']) && $_POST['mode_paiement'] == 'PayPal') ? 'selected' : ''; ?>>PayPal</option>
                                        <option value="Paiement mobile" <?php echo (isset($_POST['mode_paiement']) && $_POST['mode_paiement'] == 'Paiement mobile') ? 'selected' : ''; ?>>Paiement mobile</option>
                                    </select>
                                    <small class="error-message" id="mode_paiement-error"></small>
                                </div>

                                <div class="form-group">
                                    <label for="formation_id">Formation *</label>
                                    <select class="form-control" id="formation_id" name="formation_id">
                                        <option value="">-- Sélectionnez une formation --</option>
                                        <?php
                                        if (!empty($listeFormations)):
                                            foreach ($listeFormations as $f):
                                                $placesRestantes = $formationC->getPlacesRestantes($f);
                                                if ($placesRestantes > 0):
                                                    $selected = ($formationId == $f['id'] || (isset($_POST['formation_id']) && $_POST['formation_id'] == $f['id'])) ? 'selected' : '';
                                        ?>
                                                    <option value="<?php echo $f['id']; ?>" <?php echo $selected; ?> data-niveau="<?php echo htmlspecialchars($f['niveau']); ?>">
                                                        <?php echo htmlspecialchars($f['nom']); ?> - Niveau: <?php echo htmlspecialchars($f['niveau']); ?> - <?php echo number_format($f['prix'], 2); ?> TND (<?php echo $placesRestantes; ?> places restantes)
                                                    </option>
                                        <?php
                                                endif;
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                    <small class="error-message" id="formation_id-error"></small>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-submit">
                                        Confirmer l'inscription
                                    </button>
                                    <a href="../../index.php" class="btn-cancel">Annuler</a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="footer" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer-bottom">
                        <p>&copy; 2024 FormationPro. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="../../assets/js/front.js"></script>
    <script>
        // FONCTION POUR AFFICHER LES TOASTS
        function showToast(type, title, message, duration = 5000) {
            const toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) return;

            // Créer le toast
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            // Icône selon le type
            let icon = 'ℹ️';
            if (type === 'success') icon = '✅';
            if (type === 'error') icon = '❌';
            if (type === 'warning') icon = '⚠️';

            toast.innerHTML = `
                <div class="toast-icon">${icon}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 400);">×</button>
                <div class="toast-progress" style="animation-duration: ${duration}ms"></div>
            `;

            // Ajouter le toast au container
            toastContainer.appendChild(toast);

            // Animation d'entrée
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            // Suppression automatique après la durée
            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 400);
            }, duration);

            // Limiter le nombre de toasts à 3
            const toasts = toastContainer.querySelectorAll('.toast');
            if (toasts.length > 3) {
                toasts[0].classList.add('hide');
                setTimeout(() => {
                    if (toasts[0].parentNode) {
                        toasts[0].remove();
                    }
                }, 400);
            }
        }

        // Afficher le message d'erreur PHP comme un toast
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier s'il y a une erreur PHP
            const phpError = document.getElementById('php-error');
            if (phpError) {
                // Masquer l'alerte PHP et afficher un toast à la place
                const errorText = phpError.textContent || phpError.innerText;
                showToast('error', 'Erreur', errorText.replace('Erreur:', '').trim(), 8000);
                phpError.style.display = 'none';
            }

            // Vérifier s'il y a un message de succès (pour cohérence)
            const successMessages = document.querySelectorAll('.alert-success');
            successMessages.forEach(function(msg) {
                msg.style.display = 'none';
            });

            // Contrôle du nom
            var nomInput = document.getElementById('nom');
            if (nomInput) {
                nomInput.addEventListener('input', function() {
                    if (this.value.length < 3) {
                        this.classList.remove('success-border');
                        this.classList.add('error-border');
                        document.getElementById('nom-error').textContent = 'Le nom doit contenir au moins 3 caractères';
                    } else if (this.value.length > 50) {
                        this.classList.remove('success-border');
                        this.classList.add('error-border');
                        document.getElementById('nom-error').textContent = 'Le nom ne doit pas dépasser 50 caractères';
                    } else {
                        this.classList.remove('error-border');
                        this.classList.add('success-border');
                        document.getElementById('nom-error').textContent = '';
                    }
                });
            }

            // Contrôle de l'email
            var emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('input', function() {
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(this.value)) {
                        this.classList.remove('success-border');
                        this.classList.add('error-border');
                        document.getElementById('email-error').textContent = 'Format d\'email invalide';
                    } else if (this.value.length > 100) {
                        this.classList.remove('success-border');
                        this.classList.add('error-border');
                        document.getElementById('email-error').textContent = 'L\'email ne doit pas dépasser 100 caractères';
                    } else {
                        this.classList.remove('error-border');
                        this.classList.add('success-border');
                        document.getElementById('email-error').textContent = '';
                    }
                });
            }

            // Contrôle du téléphone
            var telInput = document.getElementById('tel');
            if (telInput) {
                telInput.addEventListener('input', function() {
                    // N'autoriser que les chiffres
                    this.value = this.value.replace(/[^0-9]/g, '');

                    var telDigits = this.value.replace(/[^0-9]/g, '');
                    if (telDigits.length < 8) {
                        this.classList.remove('success-border');
                        this.classList.add('error-border');
                        document.getElementById('tel-error').textContent = 'Minimum 8 chiffres';
                    } else if (telDigits.length > 15) {
                        this.classList.remove('success-border');
                        this.classList.add('error-border');
                        document.getElementById('tel-error').textContent = 'Maximum 15 chiffres';
                    } else {
                        this.classList.remove('error-border');
                        this.classList.add('success-border');
                        document.getElementById('tel-error').textContent = '';
                    }
                });
            }

            // Contrôle de la sélection de formation
            var formationSelect = document.getElementById('formation_id');
            if (formationSelect) {
                formationSelect.addEventListener('change', function() {
                    if (!this.value) {
                        this.classList.remove('success-border');
                        this.classList.add('error-border');
                        document.getElementById('formation_id-error').textContent = 'Veuillez sélectionner une formation';
                    } else {
                        this.classList.remove('error-border');
                        this.classList.add('success-border');
                        document.getElementById('formation_id-error').textContent = '';
                    }
                });
            }

            // Contrôle du mode de paiement
            var modePaiementSelect = document.getElementById('mode_paiement');
            if (modePaiementSelect) {
                modePaiementSelect.addEventListener('change', function() {
                    if (!this.value) {
                        this.classList.remove('success-border');
                        this.classList.add('error-border');
                        document.getElementById('mode_paiement-error').textContent = 'Veuillez sélectionner un mode de paiement';
                    } else {
                        this.classList.remove('error-border');
                        this.classList.add('success-border');
                        document.getElementById('mode_paiement-error').textContent = '';
                    }
                });
            }

            // Validation à la soumission
            var inscriptionForm = document.getElementById('inscriptionForm');
            if (inscriptionForm) {
                inscriptionForm.addEventListener('submit', function(e) {
                    var errors = [];

                    // Vérifier le nom
                    if (!nomInput.value.trim()) {
                        errors.push('Le nom est obligatoire');
                        nomInput.classList.add('error-border');
                    } else if (nomInput.value.length < 3 || nomInput.value.length > 50) {
                        errors.push('Le nom doit contenir entre 3 et 50 caractères');
                        nomInput.classList.add('error-border');
                    }

                    // Vérifier l'email
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailInput.value.trim()) {
                        errors.push('L\'email est obligatoire');
                        emailInput.classList.add('error-border');
                    } else if (!emailRegex.test(emailInput.value) || emailInput.value.length > 100) {
                        errors.push('Veuillez entrer une adresse email valide (max 100 caractères)');
                        emailInput.classList.add('error-border');
                    }

                    // Vérifier le téléphone
                    var telDigits = telInput.value.replace(/[^0-9]/g, '');
                    if (!telInput.value.trim()) {
                        errors.push('Le téléphone est obligatoire');
                        telInput.classList.add('error-border');
                    } else if (telDigits.length < 8 || telDigits.length > 15) {
                        errors.push('Le numéro de téléphone doit contenir entre 8 et 15 chiffres');
                        telInput.classList.add('error-border');
                    }

                    // Vérifier la formation
                    if (!formationSelect.value) {
                        errors.push('Veuillez sélectionner une formation');
                        formationSelect.classList.add('error-border');
                    }

                    // Vérifier le mode de paiement
                    if (!modePaiementSelect.value) {
                        errors.push('Veuillez sélectionner un mode de paiement');
                        modePaiementSelect.classList.add('error-border');
                    }

                    if (errors.length > 0) {
                        e.preventDefault();
                        // Afficher un toast d'erreur au lieu d'une alerte
                        showToast('error', 'Erreurs de validation', errors.join('<br>'), 8000);
                        return false;
                    }

                    // Afficher un toast de chargement
                    showToast('warning', 'Traitement en cours', 'Votre inscription est en cours de traitement...', 3000);
                    return true;
                });
            }
        });
    </script>
</body>

</html>