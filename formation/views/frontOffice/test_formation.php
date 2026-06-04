<?php
// Système FORMEL sans base de données

// Désactiver l'affichage des erreurs pour éviter les messages avant les toasts
error_reporting(0);

// Vérifier si on a un numéro de demande (même si c'est formel)
$numero_demande = isset($_GET['numero']) ? $_GET['numero'] : 'DEM-' . date('Y') . '-TEST';

// Données simulées pour l'affichage
$participant_nom = isset($_GET['nom']) ? $_GET['nom'] : 'Participant';
$formation_nom = isset($_GET['formation']) ? $_GET['formation'] : 'Formation Professionnelle';
$niveau = isset($_GET['niveau']) ? $_GET['niveau'] : 'Débutant';

// Variables pour le test
$score = 0;
$reponse_correcte = '';
$message = '';
$test_soumis = false;

// Questions selon le niveau
$questions_par_niveau = [
    'Débutant' => [
        'question' => 'Dans Excel, quel outil utilise-t-on pour résumer rapidement des données avec des totaux et sous-totaux ?',
        'options' => [
            'a' => 'Les graphiques à barres',
            'b' => 'Les tableaux croisés dynamiques',
            'c' => 'Les formules de base',
            'd' => 'Le tri des colonnes'
        ],
        'reponse' => 'b',
        'explication' => 'Les tableaux croisés dynamiques permettent de résumer et analyser rapidement de grandes quantités de données.'
    ],
    'Moyen' => [
        'question' => 'Quelle bibliothèque Python est principalement utilisée pour manipuler des DataFrames (tableaux de données) ?',
        'options' => [
            'a' => 'NumPy',
            'b' => 'Matplotlib',
            'c' => 'Pandas',
            'd' => 'TensorFlow'
        ],
        'reponse' => 'c',
        'explication' => 'Pandas est la bibliothèque Python spécialisée pour la manipulation de données tabulaires (DataFrames).'
    ],
    'Expert' => [
        'question' => 'Pourquoi utilise-t-on Docker pour déployer des modèles ML en production ?',
        'options' => [
            'a' => 'Pour créer de meilleurs graphiques',
            'b' => 'Pour que le modèle fonctionne de la même façon partout',
            'c' => 'Pour réduire le coût du cloud',
            'd' => 'Pour accélérer l\'entraînement du modèle'
        ],
        'reponse' => 'b',
        'explication' => 'Docker permet de conteneuriser les applications pour garantir leur fonctionnement identique dans tous les environnements.'
    ]
];

// Récupérer la question selon le niveau
$question_data = $questions_par_niveau[$niveau] ?? $questions_par_niveau['Débutant'];

// Traitement du formulaire (formel)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_test'])) {
    $test_soumis = true;
    $reponse_utilisateur = $_POST['reponse'] ?? '';
    $reponse_correcte = $question_data['reponse'];

    // Vérifier la réponse
    if ($reponse_utilisateur === $reponse_correcte) {
        $score = 1;
        $message = "✅ Excellent ! Vous maîtrisez ce niveau. Passez au niveau suivant !";
    } else {
        $score = 0;
        $message = "📚 Continue à pratiquer ce niveau pour mieux maîtriser les concepts.";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">

<head>
    <title>Test de Formation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../assets/css/front.css">
    <style>
        .test-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
        }

        .test-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .header-test {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-test h1 {
            color: #1e40af;
            margin-bottom: 10px;
        }

        .info-badge {
            display: inline-block;
            padding: 8px 20px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }

        .question-card {
            background: #f8fafc;
            padding: 25px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }

        .option-label {
            display: block;
            padding: 15px;
            margin: 10px 0;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .option-label:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }

        .option-label input[type="radio"] {
            margin-right: 10px;
        }

        .btn-submit-test {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin: 30px 0;
            transition: all 0.3s ease;
        }

        .btn-submit-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }

        .result-card {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 2px solid #3b82f6;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .certificat-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #d97706;
            padding: 40px;
            border-radius: 15px;
            margin: 30px 0;
            text-align: center;
        }

        .rating-stars {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .star {
            font-size: 30px;
            cursor: pointer;
            color: #d1d5db;
            transition: color 0.3s ease;
        }

        .star:hover,
        .star.active {
            color: #f59e0b;
        }

        .btn-print {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 10px;
        }

        .explication-box {
            background: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #0ea5e9;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .certificat-card {
                border: 3px solid #000;
                box-shadow: none;
                page-break-inside: avoid;
            }
        }

        .domaine-badge {
            display: inline-block;
            padding: 6px 12px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            border-radius: 15px;
            font-size: 14px;
            margin: 5px;
        }
    </style>
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
                                <li class="menu-item"><a href="../../index.php">Accueil</a></li>
                                <li class="menu-item active"><a href="#">Test de Formation</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
    </div>

    <section class="inscription-section">
        <div class="container">
            <div class="test-container">
                <div class="test-card">
                    <div class="header-test">
                        <h1>📝 Test de Connaissances</h1>
                        <p>Validez vos compétences pour la formation</p>

                        <div class="info-badge">
                            <?php echo htmlspecialchars($formation_nom); ?> - Niveau <?php echo htmlspecialchars($niveau); ?>
                        </div>

                        <div>
                            <span class="domaine-badge"><?php echo $niveau == 'Débutant' ? 'DATA SCIENCE' : ($niveau == 'Moyen' ? 'CYBERSÉCURITÉ' : 'DÉVELOPPEMENT WEB'); ?></span>
                            <span class="domaine-badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                Participant : <?php echo htmlspecialchars($participant_nom); ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!$test_soumis): ?>
                        <div class="question-card">
                            <h3 style="color: #1f2937; margin-bottom: 20px;">Question unique :</h3>
                            <p style="font-size: 18px; color: #4b5563; margin-bottom: 25px;">
                                <?php echo $question_data['question']; ?>
                            </p>

                            <form method="POST" action="">
                                <?php foreach ($question_data['options'] as $key => $option): ?>
                                    <label class="option-label">
                                        <input type="radio" name="reponse" value="<?php echo $key; ?>" required>
                                        <strong><?php echo $key; ?>)</strong> <?php echo $option; ?>
                                    </label>
                                <?php endforeach; ?>

                                <button type="submit" name="submit_test" class="btn-submit-test">
                                    ✅ Soumettre ma réponse
                                </button>
                            </form>
                        </div>

                        <div class="no-print" style="text-align: center; margin-top: 30px;">
                            <a href="../../index.php" style="color: #6b7280; text-decoration: none;">
                                ← Retour à l'accueil
                            </a>
                        </div>

                    <?php else: ?>
                        <!-- Résultats du test -->
                        <div class="result-card">
                            <h2 style="color: #065f46; margin-bottom: 15px;">
                                <?php echo $score == 1 ? '🎉 Félicitations !' : '📝 Résultat du Test'; ?>
                            </h2>
                            <p style="font-size: 20px; margin-bottom: 10px;">
                                Score : <strong><?php echo $score; ?>/1</strong>
                            </p>
                            <p style="font-size: 18px; color: <?php echo $score == 1 ? '#047857' : '#92400e'; ?>; font-weight: bold;">
                                <?php echo $message; ?>
                            </p>

                            <?php if ($score == 0): ?>
                                <div class="explication-box">
                                    <p><strong>💡 Explication :</strong> <?php echo $question_data['explication']; ?></p>
                                    <p><strong>✅ Bonne réponse :</strong> <?php echo strtoupper($reponse_correcte); ?>) <?php echo $question_data['options'][$reponse_correcte]; ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Certificat de participation -->
                        <div class="certificat-card" id="certificat">
                            <div style="border: 2px solid #b45309; padding: 20px; border-radius: 10px;">
                                <h2 style="color: #92400e; margin-bottom: 20px; font-size: 28px;">🏆 CERTIFICAT DE FORMATION</h2>
                                <p style="font-size: 22px; color: #78350f; margin-bottom: 10px; font-weight: bold;">
                                    <?php echo htmlspecialchars($participant_nom); ?>
                                </p>
                                <p style="font-size: 18px; color: #b45309; margin-bottom: 20px;">
                                    a <?php echo $score == 1 ? 'réussi' : 'participé à'; ?> la formation
                                </p>
                                <p style="font-size: 24px; color: #1e40af; font-weight: bold; margin-bottom: 10px;">
                                    <?php echo htmlspecialchars($formation_nom); ?>
                                </p>
                                <p style="color: #6b7280; margin-bottom: 15px;">
                                    Niveau : <strong><?php echo htmlspecialchars($niveau); ?></strong> |
                                    Score : <strong><?php echo $score; ?>/1</strong>
                                </p>
                                <p style="color: #4b5563; margin-bottom: 20px;">
                                    Date d'émission : <?php echo date('d/m/Y'); ?>
                                </p>
                                <div style="border-top: 1px solid #d97706; padding-top: 15px;">
                                    <p style="color: #78350f; font-style: italic; font-size: 14px;">
                                        Numéro de certificat : CERT-<?php echo strtoupper(substr(md5($numero_demande . date('Ymd')), 0, 10)); ?>
                                    </p>
                                    <p style="color: #6b7280; font-size: 12px; margin-top: 10px;">
                                        FormationPro - Centre de Formation Agréé
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Système de rating (formel) -->
                        <div class="no-print" style="text-align: center; margin-top: 40px;">
                            <h3 style="color: #1f2937; margin-bottom: 15px;">Évaluez votre expérience de test</h3>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                    <span class="star" onclick="setRating(<?php echo $i; ?>)">★</span>
                                <?php endfor; ?>
                            </div>
                            <p id="rating-message" style="color: #6b7280; margin-top: 10px;"></p>

                            <!-- Boutons d'action -->
                            <div style="margin-top: 30px;">
                                <button onclick="window.print()" class="btn-print">
                                    🖨️ Imprimer le certificat
                                </button>
                                <a href="../../index.php" class="btn-print" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                                    🏠 Retour à l'accueil
                                </a>
                                <a href="test_formation.php?numero=<?php echo $numero_demande; ?>&nom=<?php echo urlencode($participant_nom); ?>&formation=<?php echo urlencode($formation_nom); ?>&niveau=<?php echo urlencode($niveau); ?>"
                                    class="btn-print" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                                    🔁 Refaire le test
                                </a>
                            </div>
                        </div>

                        <script>
                            let currentRating = 0;

                            function setRating(rating) {
                                currentRating = rating;
                                const stars = document.querySelectorAll('.star');
                                const messages = [
                                    "Merci pour votre retour !",
                                    "Nous apprécions votre avis !",
                                    "Merci pour cette bonne note !",
                                    "Excellent ! Merci beaucoup !"
                                ];

                                stars.forEach((star, index) => {
                                    if (index < rating) {
                                        star.classList.add('active');
                                    } else {
                                        star.classList.remove('active');
                                    }
                                });

                                document.getElementById('rating-message').textContent = messages[rating - 1] || '';

                                // Message temporaire
                                setTimeout(() => {
                                    document.getElementById('rating-message').textContent = "Évaluation enregistrée (formel)";
                                }, 2000);
                            }

                            // Animation pour le certificat
                            document.addEventListener('DOMContentLoaded', function() {
                                const certificat = document.getElementById('certificat');
                                certificat.style.opacity = '0';
                                certificat.style.transform = 'scale(0.9)';

                                setTimeout(() => {
                                    certificat.style.transition = 'all 0.6s ease';
                                    certificat.style.opacity = '1';
                                    certificat.style.transform = 'scale(1)';
                                }, 300);
                            });
                        </script>
                    <?php endif; ?>
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
</body>

</html>