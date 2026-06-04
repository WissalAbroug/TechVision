<?php
// Définir le chemin de base pour les assets
$base_url = dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))) . '/';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrepAI Interview - Quiz d'Entretien</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assests/css/front.css">
    <style>
        .quiz-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
        }

        .btn-small {
            padding: 10px 20px;
            font-size: 0.9em;
            width: auto;
            margin-right: 10px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }

        .question-option {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .question-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .question-option.selected {
            border-color: #667eea;
            background: #f0f3ff;
        }

        .question-option.correct {
            border-color: #28a745;
            background: #d4edda;
        }

        .question-option.incorrect {
            border-color: #dc3545;
            background: #f8d7da;
        }

        .score-display {
            text-align: center;
            padding: 40px;
        }

        .score-number {
            font-size: 5em;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .trophy {
            font-size: 5em;
            margin-bottom: 20px;
        }

        .explanation-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .hidden {
            display: none;
        }

        .saved-quiz {
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .saved-quiz h4 {
            margin-bottom: 5px;
            color: #333;
        }

        .saved-quiz p {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 10px;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .category-btn {
            padding: 20px;
            margin: 10px 0;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: left;
        }

        .category-btn:hover {
            border-color: #667eea;
            background: #f8f9ff;
            transform: translateY(-2px);
        }

        .category-btn h3 {
            color: #667eea;
            margin-bottom: 5px;
        }

        .category-btn p {
            color: #666;
            font-size: 0.9em;
        }

        .back-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .back-nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            transition: all 0.3s;
        }

        .back-nav a:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <!-- Header Navigation -->
    <div class="back-nav">
        <a href="index.php">← Retour à l'accueil</a>
        <a href="../backOffice/dashboard.php">Dashboard Admin</a>
    </div>

    <div class="quiz-container">
        <div class="header">
            <h1>🎯 PrepAI Interview</h1>
            <p>Préparez vos entretiens avec des quiz professionnels</p>
        </div>

        <!-- Vue Accueil -->
        <div id="homeView">
            <div class="card">
                <h2 style="margin-bottom: 20px;">📚 Choisissez une Catégorie</h2>
                
                <div class="category-btn" onclick="generateQuiz('Développeur Full-Stack')">
                    <h3>💻 Développeur Full-Stack</h3>
                    <p>Questions sur HTML, CSS, JavaScript, bases de données, APIs</p>
                </div>

                <div class="category-btn" onclick="generateQuiz('Data Scientist')">
                    <h3>📊 Data Scientist</h3>
                    <p>Python, Machine Learning, statistiques, analyse de données</p>
                </div>

                <div class="category-btn" onclick="generateQuiz('Chef de Projet')">
                    <h3>📋 Chef de Projet / Product Manager</h3>
                    <p>Gestion d'équipe, méthodologies Agile, planification</p>
                </div>

                <div class="category-btn" onclick="generateQuiz('Marketing Digital')">
                    <h3>📱 Marketing Digital</h3>
                    <p>SEO, réseaux sociaux, publicité en ligne, analytics</p>
                </div>

                <div class="category-btn" onclick="generateQuiz('Designer UX/UI')">
                    <h3>🎨 Designer UX/UI</h3>
                    <p>Design thinking, prototypage, outils de design, ergonomie</p>
                </div>

                <div class="category-btn" onclick="generateQuiz('Ressources Humaines')">
                    <h3>👥 Ressources Humaines</h3>
                    <p>Recrutement, gestion des talents, droit du travail</p>
                </div>
            </div>

            <div id="savedQuizzesSection" class="card hidden">
                <h2 style="margin-bottom: 20px;">💾 Quiz Sauvegardés</h2>
                <div id="savedQuizzesList"></div>
            </div>
        </div>

        <!-- Vue Quiz -->
        <div id="quizView" class="hidden">
            <div class="card">
                <div style="margin-bottom: 20px;">
                    <button class="btn btn-secondary btn-small" id="backBtn">
                        ← Retour
                    </button>
                    <button class="btn btn-success btn-small" id="saveBtn">
                        💾 Sauvegarder
                    </button>
                </div>
                
                <h3 id="quizTitle" style="margin-bottom: 20px; color: #667eea;"></h3>
                
                <div id="progressInfo" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Question <span id="currentQ">1</span> sur <span id="totalQ">5</span></span>
                    <span id="progressPercent">20%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 20%;"></div>
                </div>

                <h2 id="questionText" style="margin-bottom: 30px;"></h2>
                <div id="optionsContainer"></div>

                <button class="btn btn-primary" id="nextBtn" disabled>
                    Suivant →
                </button>
            </div>
        </div>

        <!-- Vue Résultats -->
        <div id="resultsView" class="hidden">
            <div class="card">
                <div class="score-display">
                    <div class="trophy" id="trophyIcon">🏆</div>
                    <h2>Quiz Terminé !</h2>
                    <div class="score-number" id="scorePercent">0%</div>
                    <p style="font-size: 1.5em; color: #666;" id="scoreText">0 / 0 réponses correctes</p>
                    <div class="alert alert-info" id="feedbackText" style="margin-top: 20px; text-align: left;"></div>
                </div>

                <div class="grid-2">
                    <a href="quiz.php" class="btn btn-primary">Nouveau Quiz</a>
                    <button class="btn btn-secondary" id="restartBtn">
                        🔄 Recommencer
                    </button>
                </div>
            </div>

            <div class="card">
                <h2 style="margin-bottom: 20px;">📋 Correction Détaillée</h2>
                <div id="detailedResults"></div>
            </div>
        </div>
    </div>

    <script>
        // Base de données de questions par catégorie
        const questionDatabase = {
            'Développeur Full-Stack': [
                {
                    question: "Quelle est la différence principale entre let et var en JavaScript ?",
                    options: [
                        "let a une portée de bloc, var a une portée de fonction",
                        "var est plus moderne que let",
                        "let ne peut pas être réassigné",
                        "Il n'y a aucune différence"
                    ],
                    correctAnswer: 0,
                    explanation: "let a une portée de bloc (block scope) ce qui signifie qu'elle n'existe que dans le bloc où elle est déclarée, tandis que var a une portée de fonction (function scope) et peut causer des bugs inattendus."
                },
                {
                    question: "Qu'est-ce que le DOM (Document Object Model) ?",
                    options: [
                        "Un langage de programmation",
                        "Une interface de programmation pour manipuler HTML/XML",
                        "Un framework JavaScript",
                        "Une base de données"
                    ],
                    correctAnswer: 1,
                    explanation: "Le DOM est une interface de programmation qui représente la structure d'un document HTML ou XML sous forme d'arbre, permettant à JavaScript de manipuler le contenu, la structure et le style de la page."
                },
                {
                    question: "Quel est le rôle principal de SQL ?",
                    options: [
                        "Créer des interfaces graphiques",
                        "Gérer et interroger des bases de données relationnelles",
                        "Développer des applications mobiles",
                        "Sécuriser les sites web"
                    ],
                    correctAnswer: 1,
                    explanation: "SQL (Structured Query Language) est un langage standardisé utilisé pour gérer et interroger des bases de données relationnelles. Il permet de créer, lire, mettre à jour et supprimer des données."
                },
                {
                    question: "Qu'est-ce qu'une API REST ?",
                    options: [
                        "Un type de base de données",
                        "Une architecture pour créer des services web",
                        "Un framework JavaScript",
                        "Un protocole de sécurité"
                    ],
                    correctAnswer: 1,
                    explanation: "REST (Representational State Transfer) est une architecture pour créer des services web qui utilise les méthodes HTTP standard (GET, POST, PUT, DELETE) pour manipuler des ressources."
                },
                {
                    question: "Quelle est la différence entre '==' et '===' en JavaScript ?",
                    options: [
                        "Aucune différence",
                        "=== compare la valeur et le type, == compare seulement la valeur",
                        "== est plus rapide que ===",
                        "=== est obsolète"
                    ],
                    correctAnswer: 1,
                    explanation: "=== est l'opérateur de comparaison stricte qui vérifie à la fois la valeur et le type, tandis que == effectue une conversion de type avant la comparaison, ce qui peut mener à des résultats inattendus."
                }
            ],
            'Data Scientist': [
                {
                    question: "Qu'est-ce que le Machine Learning supervisé ?",
                    options: [
                        "Un apprentissage sans données d'entraînement",
                        "Un apprentissage avec des données étiquetées",
                        "Un apprentissage automatique sans intervention humaine",
                        "Un apprentissage par essai-erreur uniquement"
                    ],
                    correctAnswer: 1,
                    explanation: "Le Machine Learning supervisé utilise des données d'entraînement étiquetées (avec les réponses connues) pour apprendre à prédire les résultats sur de nouvelles données."
                },
                {
                    question: "Qu'est-ce qu'un DataFrame en Pandas ?",
                    options: [
                        "Une fonction mathématique",
                        "Une structure de données tabulaire bidimensionnelle",
                        "Un type de graphique",
                        "Un algorithme de tri"
                    ],
                    correctAnswer: 1,
                    explanation: "Un DataFrame est la structure de données principale de Pandas, similaire à une feuille Excel ou une table SQL, permettant de stocker et manipuler des données tabulaires avec des lignes et colonnes."
                },
                {
                    question: "Quelle est la différence entre corrélation et causalité ?",
                    options: [
                        "Il n'y a pas de différence",
                        "La corrélation mesure la relation, la causalité prouve qu'une variable cause l'autre",
                        "La corrélation est toujours plus forte",
                        "La causalité ne nécessite pas de données"
                    ],
                    correctAnswer: 1,
                    explanation: "La corrélation indique une relation statistique entre deux variables, mais ne prouve pas que l'une cause l'autre. La causalité nécessite des preuves plus rigoureuses qu'une variable influence directement l'autre."
                },
                {
                    question: "Qu'est-ce que l'overfitting en Machine Learning ?",
                    options: [
                        "Un modèle qui apprend trop bien les données d'entraînement",
                        "Un modèle trop simple",
                        "Une méthode d'optimisation",
                        "Un type de régression"
                    ],
                    correctAnswer: 0,
                    explanation: "L'overfitting se produit quand un modèle apprend trop bien les détails et le bruit des données d'entraînement, ce qui réduit sa capacité à généraliser sur de nouvelles données."
                },
                {
                    question: "Quel est le rôle de la validation croisée (cross-validation) ?",
                    options: [
                        "Nettoyer les données",
                        "Évaluer la performance du modèle de manière robuste",
                        "Augmenter la taille du dataset",
                        "Réduire le temps d'entraînement"
                    ],
                    correctAnswer: 1,
                    explanation: "La validation croisée divise les données en plusieurs sous-ensembles pour entraîner et tester le modèle plusieurs fois, fournissant une évaluation plus fiable de sa performance."
                }
            ],
            'Chef de Projet': [
                {
                    question: "Qu'est-ce que la méthodologie Agile ?",
                    options: [
                        "Une approche de gestion de projet linéaire et rigide",
                        "Une approche itérative et flexible de gestion de projet",
                        "Un logiciel de gestion de projet",
                        "Une méthode uniquement pour les développeurs"
                    ],
                    correctAnswer: 1,
                    explanation: "Agile est une approche itérative de gestion de projet qui favorise la collaboration, la flexibilité et la livraison incrémentale de valeur, plutôt qu'un plan rigide et séquentiel."
                },
                {
                    question: "Qu'est-ce qu'un Sprint en Scrum ?",
                    options: [
                        "Une réunion quotidienne",
                        "Une période de temps fixe (généralement 2-4 semaines) pour accomplir un ensemble de tâches",
                        "Un type de rétrospective",
                        "Un document de planification"
                    ],
                    correctAnswer: 1,
                    explanation: "Un Sprint est une itération de durée fixe (time-box) pendant laquelle l'équipe s'engage à accomplir un ensemble de fonctionnalités définies. C'est le cœur du framework Scrum."
                },
                {
                    question: "Quel est le rôle principal d'un Product Owner ?",
                    options: [
                        "Coder les fonctionnalités",
                        "Définir et prioriser les besoins du produit",
                        "Tester l'application",
                        "Gérer le budget uniquement"
                    ],
                    correctAnswer: 1,
                    explanation: "Le Product Owner est responsable de maximiser la valeur du produit en définissant clairement les besoins, en priorisant le backlog et en s'assurant que l'équipe travaille sur les fonctionnalités les plus importantes."
                },
                {
                    question: "Qu'est-ce qu'un diagramme de Gantt ?",
                    options: [
                        "Un graphique de performance",
                        "Un outil de visualisation de planning avec des barres horizontales",
                        "Un tableau de bord financier",
                        "Une méthode de brainstorming"
                    ],
                    correctAnswer: 1,
                    explanation: "Un diagramme de Gantt est un outil de gestion de projet qui représente visuellement les tâches d'un projet sur une ligne de temps, montrant les dépendances et la progression."
                },
                {
                    question: "Qu'est-ce que le MVP (Minimum Viable Product) ?",
                    options: [
                        "Le produit le moins cher possible",
                        "La version minimale d'un produit avec juste assez de fonctionnalités pour tester une hypothèse",
                        "Le produit final optimisé",
                        "Un prototype qui ne sera jamais lancé"
                    ],
                    correctAnswer: 1,
                    explanation: "Le MVP est la version la plus simple d'un produit qui permet de tester rapidement une hypothèse auprès d'utilisateurs réels avec un minimum d'effort et de ressources."
                }
            ],
            'Marketing Digital': [
                {
                    question: "Qu'est-ce que le SEO ?",
                    options: [
                        "Social Engagement Online",
                        "Search Engine Optimization - Optimisation pour les moteurs de recherche",
                        "Secure Email Operation",
                        "Sales Efficiency Optimization"
                    ],
                    correctAnswer: 1,
                    explanation: "Le SEO (Search Engine Optimization) est l'ensemble des techniques visant à améliorer le positionnement d'un site web dans les résultats des moteurs de recherche comme Google."
                },
                {
                    question: "Quelle est la différence entre SEO et SEA ?",
                    options: [
                        "Il n'y a pas de différence",
                        "SEO est gratuit et organique, SEA est payant (publicité)",
                        "SEO est pour les réseaux sociaux, SEA pour les moteurs de recherche",
                        "SEA est obsolète"
                    ],
                    correctAnswer: 1,
                    explanation: "Le SEO vise à améliorer le référencement naturel (organique) gratuitement, tandis que le SEA (Search Engine Advertising) consiste à payer pour apparaître dans les résultats sponsorisés."
                },
                {
                    question: "Qu'est-ce qu'un taux de conversion ?",
                    options: [
                        "Le nombre de visiteurs sur un site",
                        "Le pourcentage de visiteurs qui accomplissent une action désirée",
                        "Le prix d'un produit",
                        "Le nombre de clics sur une publicité"
                    ],
                    correctAnswer: 1,
                    explanation: "Le taux de conversion mesure le pourcentage de visiteurs qui effectuent l'action souhaitée (achat, inscription, téléchargement, etc.) par rapport au nombre total de visiteurs."
                },
                {
                    question: "Qu'est-ce que le marketing de contenu (Content Marketing) ?",
                    options: [
                        "Vendre des produits directement",
                        "Créer et partager du contenu de valeur pour attirer et fidéliser une audience",
                        "Envoyer des emails promotionnels",
                        "Acheter de la publicité"
                    ],
                    correctAnswer: 1,
                    explanation: "Le marketing de contenu consiste à créer et distribuer du contenu pertinent et utile pour attirer et engager une audience cible, plutôt que de promouvoir directement des produits."
                },
                {
                    question: "Qu'est-ce que le ROI (Return On Investment) en marketing ?",
                    options: [
                        "Le nombre de clients",
                        "Le rapport entre le bénéfice généré et l'investissement marketing",
                        "Le taux de clics",
                        "Le nombre de followers"
                    ],
                    correctAnswer: 1,
                    explanation: "Le ROI mesure l'efficacité d'un investissement marketing en comparant les revenus générés aux coûts engagés. Un ROI positif indique que la campagne est profitable."
                }
            ],
            'Designer UX/UI': [
                {
                    question: "Quelle est la différence entre UX et UI ?",
                    options: [
                        "Il n'y a pas de différence",
                        "UX concerne l'expérience globale, UI concerne l'interface visuelle",
                        "UX est obsolète",
                        "UI est seulement pour les applications mobiles"
                    ],
                    correctAnswer: 1,
                    explanation: "L'UX (User Experience) englobe l'expérience utilisateur complète, tandis que l'UI (User Interface) se concentre sur l'aspect visuel et interactif de l'interface."
                },
                {
                    question: "Qu'est-ce qu'un wireframe ?",
                    options: [
                        "Un code de sécurité",
                        "Un schéma simplifié de l'interface sans éléments visuels détaillés",
                        "Un type de police d'écriture",
                        "Une méthode de test utilisateur"
                    ],
                    correctAnswer: 1,
                    explanation: "Un wireframe est un schéma low-fidelity qui représente la structure et l'organisation du contenu d'une interface, sans se concentrer sur le design visuel détaillé."
                },
                {
                    question: "Qu'est-ce que le Design Thinking ?",
                    options: [
                        "Un logiciel de design",
                        "Une approche centrée sur l'utilisateur pour résoudre des problèmes complexes",
                        "Une technique de programmation",
                        "Un style graphique"
                    ],
                    correctAnswer: 1,
                    explanation: "Le Design Thinking est une méthodologie centrée sur l'humain qui utilise l'empathie, l'idéation et l'expérimentation pour développer des solutions innovantes aux problèmes complexes."
                },
                {
                    question: "Qu'est-ce qu'un prototype haute fidélité ?",
                    options: [
                        "Un simple croquis sur papier",
                        "Une maquette interactive détaillée proche du produit final",
                        "Un document de spécifications",
                        "Un test utilisateur"
                    ],
                    correctAnswer: 1,
                    explanation: "Un prototype haute fidélité est une représentation interactive et détaillée du produit final, incluant les éléments visuels, les interactions et parfois même du contenu réel."
                },
                {
                    question: "Qu'est-ce que le principe de Fitts en design d'interface ?",
                    options: [
                        "Plus une cible est grande et proche, plus elle est facile à atteindre",
                        "Les couleurs doivent toujours être contrastées",
                        "Le texte doit être centré",
                        "Les boutons doivent être ronds"
                    ],
                    correctAnswer: 0,
                    explanation: "La loi de Fitts stipule que le temps nécessaire pour atteindre une cible dépend de sa taille et de sa distance. C'est pourquoi les éléments importants doivent être grands et facilement accessibles."
                }
            ],
            'Ressources Humaines': [
                {
                    question: "Qu'est-ce que l'onboarding ?",
                    options: [
                        "Le processus de licenciement",
                        "Le processus d'intégration d'un nouveau salarié",
                        "Une méthode de recrutement",
                        "Un logiciel RH"
                    ],
                    correctAnswer: 1,
                    explanation: "L'onboarding est le processus d'accueil et d'intégration des nouveaux employés dans l'entreprise, incluant la formation, la présentation de la culture d'entreprise et l'accompagnement initial."
                },
                {
                    question: "Qu'est-ce qu'un entretien structuré ?",
                    options: [
                        "Un entretien très court",
                        "Un entretien avec des questions prédéfinies posées à tous les candidats",
                        "Un entretien informel",
                        "Un entretien uniquement par téléphone"
                    ],
                    correctAnswer: 1,
                    explanation: "Un entretien structuré utilise un ensemble de questions standardisées posées à tous les candidats de la même manière, permettant une comparaison plus objective et équitable."
                },
                {
                    question: "Qu'est-ce que la marque employeur ?",
                    options: [
                        "Le logo de l'entreprise",
                        "L'image et la réputation de l'entreprise comme employeur",
                        "Le salaire moyen proposé",
                        "Les bureaux de l'entreprise"
                    ],
                    correctAnswer: 1,
                    explanation: "La marque employeur est l'image et la réputation qu'une entreprise projette en tant qu'employeur auprès des candidats potentiels et des employés actuels."
                },
                {
                    question: "Qu'est-ce que le turnover ?",
                    options: [
                        "Le chiffre d'affaires de l'entreprise",
                        "Le taux de rotation du personnel (départs/arrivées)",
                        "Une technique de management",
                        "Un type de formation"
                    ],
                    correctAnswer: 1,
                    explanation: "Le turnover mesure le taux de renouvellement du personnel dans une entreprise. Un turnover élevé peut indiquer des problèmes de satisfaction des employés."
                },
                {
                    question: "Qu'est-ce que l'assessment center ?",
                    options: [
                        "Un centre de formation",
                        "Une méthode d'évaluation basée sur des mises en situation",
                        "Un bureau RH",
                        "Un logiciel de recrutement"
                    ],
                    correctAnswer: 1,
                    explanation: "L'assessment center est une méthode d'évaluation qui utilise des exercices pratiques, des jeux de rôle et des simulations pour évaluer les compétences et aptitudes des candidats dans des situations réelles."
                }
            ]
        };

        // Variables globales
        let questions = [];
        let currentQuestionIndex = 0;
        let userAnswers = {};
        let savedQuizzes = [];
        let currentCategory = '';

        // Chargement initial
        window.addEventListener('DOMContentLoaded', function() {
            loadSavedData();
            setupEventListeners();
        });

        function setupEventListeners() {
            document.getElementById('backBtn').addEventListener('click', goHome);
            document.getElementById('saveBtn').addEventListener('click', saveCurrentQuiz);
            document.getElementById('nextBtn').addEventListener('click', nextQuestion);
            document.getElementById('restartBtn').addEventListener('click', restartQuiz);
        }

        function loadSavedData() {
            try {
                const saved = localStorage.getItem('saved-quizzes');
                if (saved) {
                    savedQuizzes = JSON.parse(saved);
                    displaySavedQuizzes();
                }
            } catch (error) {
                console.log('Première utilisation');
            }
        }

        function generateQuiz(category) {
            currentCategory = category;
            questions = questionDatabase[category];
            currentQuestionIndex = 0;
            userAnswers = {};
            startQuiz();
        }

        function startQuiz() {
            document.getElementById('homeView').classList.add('hidden');
            document.getElementById('quizView').classList.remove('hidden');
            document.getElementById('resultsView').classList.add('hidden');
            document.getElementById('quizTitle').textContent = '📝 Quiz : ' + currentCategory;
            displayQuestion();
        }

        function displayQuestion() {
            const question = questions[currentQuestionIndex];
            document.getElementById('currentQ').textContent = currentQuestionIndex + 1;
            document.getElementById('totalQ').textContent = questions.length;
            
            const percent = Math.round(((currentQuestionIndex + 1) / questions.length) * 100);
            document.getElementById('progressPercent').textContent = percent + '%';
            document.getElementById('progressFill').style.width = percent + '%';

            document.getElementById('questionText').textContent = question.question;

            const container = document.getElementById('optionsContainer');
            container.innerHTML = '';

            question.options.forEach((option, index) => {
                const div = document.createElement('div');
                div.className = 'question-option';
                if (userAnswers[currentQuestionIndex] === index) {
                    div.classList.add('selected');
                }
                div.innerHTML = `<strong>${String.fromCharCode(65 + index)}.</strong> ${option}`;
                div.addEventListener('click', function() {
                    selectAnswer(index);
                });
                container.appendChild(div);
            });

            document.getElementById('nextBtn').disabled = userAnswers[currentQuestionIndex] === undefined;
        }

        function selectAnswer(index) {
            userAnswers[currentQuestionIndex] = index;
            displayQuestion();
        }

        function nextQuestion() {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                displayQuestion();
            } else {
                showResults();
            }
        }

        function showResults() {
            document.getElementById('quizView').classList.add('hidden');
            document.getElementById('resultsView').classList.remove('hidden');

            let correct = 0;
            questions.forEach((q, idx) => {
                if (userAnswers[idx] === q.correctAnswer) correct++;
            });

            const percentage = (correct / questions.length) * 100;
            document.getElementById('scorePercent').textContent = Math.round(percentage) + '%';
            document.getElementById('scoreText').textContent = `${correct} / ${questions.length} réponses correctes`;

            let feedback = '';
            let trophy = '🏆';
            if (percentage >= 80) {
                feedback = '🎉 Excellent travail ! Vous maîtrisez très bien le sujet. Continuez comme ça !';
                trophy = '🏆';
            } else if (percentage >= 60) {
                feedback = '👍 Bon résultat ! Avec un peu plus de pratique, vous serez au top.';
                trophy = '🥈';
            } else {
                feedback = '💪 Ne vous découragez pas ! Révisez les concepts clés et réessayez.';
                trophy = '📚';
            }
            document.getElementById('trophyIcon').textContent = trophy;
            document.getElementById('feedbackText').textContent = feedback;

            displayDetailedResults();
        }

        function displayDetailedResults() {
            const container = document.getElementById('detailedResults');
            container.innerHTML = '';

            questions.forEach((q, idx) => {
                const userAnswer = userAnswers[idx];
                const isCorrect = userAnswer === q.correctAnswer;

                const div = document.createElement('div');
                div.style.marginBottom = '20px';
                div.style.padding = '20px';
                div.style.border = '2px solid ' + (isCorrect ? '#28a745' : '#dc3545');
                div.style.borderRadius = '10px';
                div.style.background = isCorrect ? '#d4edda' : '#f8d7da';

                let optionsHTML = '';
                q.options.forEach((opt, optIdx) => {
                    const isUserAnswer = userAnswer === optIdx;
                    const isCorrectAnswer = q.correctAnswer === optIdx;
                    let className = '';
                    let label = '';
                    
                    if (isCorrectAnswer) {
                        className = 'correct';
                        label = ' ✓ Correct';
                    } else if (isUserAnswer) {
                        className = 'incorrect';
                        label = ' ✗ Votre choix';
                    }

                    optionsHTML += `
                        <div class="question-option ${className}" style="cursor: default;">
                            <strong>${String.fromCharCode(65 + optIdx)}.</strong> ${opt}
                            <span style="float: right; font-weight: bold;">${label}</span>
                        </div>
                    `;
                });

                div.innerHTML = `
                    <h3 style="margin-bottom: 15px;">
                        ${isCorrect ? '✅' : '❌'} Question ${idx + 1}
                    </h3>
                    <p style="margin-bottom: 15px; font-weight: 600;">${q.question}</p>
                    ${optionsHTML}
                    <div class="explanation-box" style="margin-top: 15px;">
                        <strong>💡 Explication :</strong>
                        <p style="margin-top: 5px;">${q.explanation}</p>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        function goHome() {
            document.getElementById('homeView').classList.remove('hidden');
            document.getElementById('quizView').classList.add('hidden');
            document.getElementById('resultsView').classList.add('hidden');
        }

        function restartQuiz() {
            currentQuestionIndex = 0;
            userAnswers = {};
            startQuiz();
        }

        function saveCurrentQuiz() {
            const quiz = {
                id: Date.now(),
                title: currentCategory,
                category: currentCategory,
                questions: questions,
                date: new Date().toLocaleDateString('fr-FR')
            };

            savedQuizzes.push(quiz);
            try {
                localStorage.setItem('saved-quizzes', JSON.stringify(savedQuizzes));
                alert('✅ Quiz sauvegardé avec succès !');
                displaySavedQuizzes();
            } catch (error) {
                console.error('Erreur sauvegarde:', error);
                alert('❌ Erreur lors de la sauvegarde');
            }
        }

        function displaySavedQuizzes() {
            if (savedQuizzes.length === 0) {
                document.getElementById('savedQuizzesSection').classList.add('hidden');
                return;
            }

            document.getElementById('savedQuizzesSection').classList.remove('hidden');
            const container = document.getElementById('savedQuizzesList');
            container.innerHTML = '';

            savedQuizzes.forEach(quiz => {
                const div = document.createElement('div');
                div.className = 'saved-quiz';
                div.innerHTML = `
                    <h4>${quiz.title}</h4>
                    <p>${quiz.category}</p>
                    <small style="color: #999;">${quiz.questions.length} questions • ${quiz.date}</small>
                    <div style="margin-top: 10px;">
                        <button class="btn btn-primary btn-small" onclick="loadSavedQuiz(${quiz.id})">
                            ▶️ Lancer
                        </button>
                        <button class="btn btn-secondary btn-small" onclick="deleteSavedQuiz(${quiz.id})">
                            🗑️ Supprimer
                        </button>
                    </div>
                `;
                container.appendChild(div);
            });
        }

        window.loadSavedQuiz = function(id) {
            const quiz = savedQuizzes.find(q => q.id === id);
            if (quiz) {
                questions = quiz.questions;
                currentCategory = quiz.category;
                currentQuestionIndex = 0;
                userAnswers = {};
                startQuiz();
            }
        }

        window.deleteSavedQuiz = function(id) {
            if (confirm('Voulez-vous vraiment supprimer ce quiz ?')) {
                savedQuizzes = savedQuizzes.filter(q => q.id !== id);
                try {
                    localStorage.setItem('saved-quizzes', JSON.stringify(savedQuizzes));
                    displaySavedQuizzes();
                } catch (error) {
                    console.error('Erreur suppression:', error);
                }
            }
        }
    </script>
</body>
</html>