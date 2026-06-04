<?php

require_once 'models/Quiz.php';
require_once 'models/QuizResult.php';
require_once 'models/CV.php';

class QuizController
{
    private Quiz $quizModel;
    private QuizResult $resultModel;
    private CV $cvModel;

    public function __construct()
    {
        requireRole('candidat');
        $this->quizModel = new Quiz();
        $this->resultModel = new QuizResult();
        $this->cvModel = new CV();
    }

    public function index()
    {
        $userId = auth()['id'];
        $quizzes = $this->quizModel->getAvailableForUser($userId);
        $results = $this->resultModel->getResultsByUser($userId);
        
        $lang = $_SESSION['lang'] ?? 'fr';
        require 'views/frontoffice/quiz/index.php';
    }

    public function generate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/?ctrl=quiz&action=index');
            exit;
        }

        $userId = auth()['id'];
        $type = $_POST['type'] ?? 'libre'; // 'cv' or 'libre'
        $cible = trim($_POST['cible'] ?? '');

        if ($type === 'cv') {
            $cv = $this->cvModel->findBy('user_id', $userId);
            if ($cv && !empty($cv['competences'])) {
                $competences = is_array($cv['competences']) ? $cv['competences'] : json_decode($cv['competences'], true);
                $cible = $competences[0] ?? 'Général'; // Pick the first hard skill as default target
            } else {
                $cible = 'Connaissances Générales';
            }
            $titre = "Évaluation sur base du CV (" . htmlspecialchars($cible) . ")";
        } else {
            if (empty($cible)) $cible = 'Culture Générale IT';
            $titre = "Test de Compétence : " . htmlspecialchars($cible);
        }

        // --- MOCK AI GENERATOR ---
        $quizId = $this->quizModel->insert([
            'titre' => $titre,
            'type' => $type,
            'niveau' => 'Intermédiaire',
            'cible' => $cible,
            'user_id' => $userId
        ]);

        $topic = strtolower(trim($cible));
        
        // Banque de questions par sujet
        $bank = [
            'php' => [
                ["q" => "Que signifie PHP ?", "options" => ["Personal Home Page", "Hypertext Preprocessor", "Pretext Hypertext Processor", "Private Hosting Platform"], "correct" => 1, "exp" => "PHP est l'acronyme récursif de PHP: Hypertext Preprocessor."],
                ["q" => "Quelle fonction permet d'afficher du texte ?", "options" => ["echo", "print_text", "display", "console.log"], "correct" => 0, "exp" => "echo (ou print) est utilisé en PHP pour l'affichage."],
                ["q" => "Comment déclare-t-on une variable en PHP ?", "options" => ["var name;", "$name;", "let name;", "name = "], "correct" => 1, "exp" => "En PHP, toutes les variables commencent par le signe dollar ($)."]
            ],
            'react' => [
                ["q" => "Qu'est-ce qu'un composant React ?", "options" => ["Une base de données", "Une fonction ou classe retournant du JSX", "Un serveur", "Un fichier CSS"], "correct" => 1, "exp" => "Un composant est la brique de base de React, retournant du code JSX."],
                ["q" => "Quel hook est utilisé pour gérer l'état local ?", "options" => ["useContext", "useEffect", "useState", "useReducer"], "correct" => 2, "exp" => "useState est le hook principal pour créer et modifier un état local."],
                ["q" => "Que signifie JSX ?", "options" => ["Java Syntax Extension", "JavaScript XML", "JSON XML", "JavaScript XHR"], "correct" => 1, "exp" => "JSX permet d'écrire une syntaxe similaire à HTML dans JavaScript."]
            ],
            'sql' => [
                ["q" => "Que signifie SQL ?", "options" => ["Structured Query Language", "Simple Query Logic", "System Query Link", "Standard Query List"], "correct" => 0, "exp" => "SQL est le langage standard des bases de données relationnelles."],
                ["q" => "Quelle commande permet de récupérer des données ?", "options" => ["FETCH", "GET", "SELECT", "PULL"], "correct" => 2, "exp" => "La commande SELECT est utilisée pour interroger les tables."],
                ["q" => "Comment supprimer une table entière ?", "options" => ["DELETE TABLE", "DROP TABLE", "REMOVE TABLE", "CLEAR TABLE"], "correct" => 1, "exp" => "DROP TABLE supprime la structure de la table et ses données."]
            ],
            'javascript' => [
                ["q" => "Lequel n'est PAS un type de variable en JS ?", "options" => ["let", "const", "var", "int"], "correct" => 3, "exp" => "int n'existe pas en tant que mot-clé de déclaration en JS."],
                ["q" => "Comment ajouter un élément à la fin d'un tableau ?", "options" => ["push()", "pop()", "append()", "add()"], "correct" => 0, "exp" => "La méthode push() ajoute un ou plusieurs éléments à la fin d'un tableau."],
                ["q" => "Qu'est-ce qu'une Promise ?", "options" => ["Une variable constante", "Un objet représentant l'achèvement éventuel d'une opération asynchrone", "Une erreur système", "Une boucle infinie"], "correct" => 1, "exp" => "Les Promises sont la base de la gestion asynchrone moderne en JS."]
            ],
            'html' => [
                ["q" => "Que signifie HTML ?", "options" => ["Hyper Text Markup Language", "High Text Machine Language", "Hyperlink and Text Markup Language", "Home Tool Markup Language"], "correct" => 0, "exp" => "HTML est le langage de balisage standard pour les documents web."],
                ["q" => "Quelle balise est utilisée pour créer un lien ?", "options" => ["<link>", "<a>", "<href>", "<url>"], "correct" => 1, "exp" => "La balise <a> (anchor) définit un lien hypertexte."],
                ["q" => "Comment insérer une image ?", "options" => ["<img src='...' />", "<image href='...' />", "<pic src='...' />", "<src img='...' />"], "correct" => 0, "exp" => "La balise <img> avec l'attribut src est la syntaxe correcte."]
            ],
            'java' => [
                ["q" => "Quel est le point d'entrée d'un programme Java ?", "options" => ["start()", "init()", "main()", "run()"], "correct" => 2, "exp" => "La méthode public static void main(String[] args) est le point d'entrée."],
                ["q" => "Java est un langage...", "options" => ["Purement fonctionnel", "Orienté objet", "Uniquement procédural", "Orienté document"], "correct" => 1, "exp" => "Java est un langage fortement orienté objet."],
                ["q" => "Qu'est-ce que le JVM ?", "options" => ["Java Visual Machine", "Java Virtual Machine", "Java Variable Manager", "Java Version Module"], "correct" => 1, "exp" => "La JVM permet d'exécuter le bytecode Java sur n'importe quel OS."]
            ]
        ];

        // Cherche une correspondance partielle (ex: 'react js' matchera 'react')
        $questionsBank = [];
        foreach ($bank as $key => $questions) {
            if (strpos($topic, $key) !== false) {
                $questionsBank = $questions;
                break;
            }
        }

        // Si aucun sujet connu, questions génériques adaptées au mot-clé
        if (empty($questionsBank)) {
            $questionsBank = [
                [
                    "q" => "Lequel de ces concepts est fondamental pour maitriser : $cible ?",
                    "options" => ["La compréhension de l'architecture", "Le design graphique", "L'obsolescence programmée", "Le marketing"],
                    "correct" => 0,
                    "exp" => "Toute technologie requiert une bonne compréhension de son architecture sous-jacente."
                ],
                [
                    "q" => "En général, quel est l'objectif principal de l'utilisation de : $cible ?",
                    "options" => ["Résoudre un problème technique ou métier spécifique", "Rendre le système plus lent", "Augmenter la taille du code", "Éviter de coder"],
                    "correct" => 0,
                    "exp" => "Les outils technologiques sont conçus pour résoudre des problématiques précises."
                ],
                [
                    "q" => "Quelle est la meilleure pratique lors de l'apprentissage de $cible ?",
                    "options" => ["Copier/coller sans comprendre", "Lire la documentation officielle", "Abandonner à la première erreur", "Ne jamais faire de mises à jour"],
                    "correct" => 1,
                    "exp" => "La documentation officielle est toujours la source de vérité la plus fiable."
                ]
            ];
        }

        foreach ($questionsBank as $q) {
            $this->quizModel->addQuestion($quizId, $q['q'], $q['options'], $q['correct'], $q['exp']);
        }

        header('Location: ' . APP_URL . '/?ctrl=quiz&action=take&id=' . $quizId);
        exit;
    }

    public function take()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . APP_URL . '/?ctrl=quiz&action=index');
            exit;
        }

        $quiz = $this->quizModel->findById($id);
        if (!$quiz || ($quiz['user_id'] !== null && $quiz['user_id'] !== auth()['id'])) {
            die("Test introuvable ou accès refusé.");
        }

        // Check if already taken
        $existingResult = $this->resultModel->findBy('quiz_id', $id); // Wait, this doesn't check user_id properly in findBy since it's just the first one. Let's do a custom query if needed. But let's just allow retakes for now or do a specific check.
        
        $questions = $this->quizModel->getQuestions($id);
        
        $lang = $_SESSION['lang'] ?? 'fr';
        require 'views/frontoffice/quiz/take.php';
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/?ctrl=quiz&action=index');
            exit;
        }

        $quizId = $_POST['quiz_id'] ?? null;
        $answers = $_POST['answers'] ?? []; // format: answers[question_id] = option_index

        if (!$quizId) die("Erreur de soumission");

        $questions = $this->quizModel->getQuestions($quizId);
        $score = 0;
        $total = count($questions);
        $missedTopics = [];

        foreach ($questions as $q) {
            $qId = $q['id'];
            $correct = (int)$q['correct_option'];
            if (isset($answers[$qId]) && (int)$answers[$qId] === $correct) {
                $score++;
            } else {
                $missedTopics[] = "Revoir la notion : " . substr($q['question'], 0, 30) . "...";
            }
        }

        // MOCK Recommendations
        $recommandations = [];
        if ($score === $total) {
            $recommandations[] = "Excellent ! Vos compétences sont solides.";
        } else {
            $recommandations = array_merge(["Vous devriez approfondir certains points :"], $missedTopics);
        }

        $resultId = $this->resultModel->saveResult(auth()['id'], $quizId, $score, $total, $recommandations);

        header('Location: ' . APP_URL . '/?ctrl=quiz&action=result&id=' . $resultId);
        exit;
    }

    public function result()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . APP_URL . '/?ctrl=quiz&action=index');
            exit;
        }

        $result = $this->resultModel->findById($id);
        if (!$result || $result['user_id'] !== auth()['id']) {
            die("Résultat introuvable.");
        }

        $quiz = $this->quizModel->findById($result['quiz_id']);
        
        $lang = $_SESSION['lang'] ?? 'fr';
        require 'views/frontoffice/quiz/result.php';
    }
}
