<?php
// ─── Bootstrap ────────────────────────────────────────────
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

session_start();

// ─── Captcha Verification Gate ──────────────────────────────
if (!isset($_SESSION['captcha_verified'])) {
    if (!isset($_SESSION['captcha_num1']) || !isset($_SESSION['captcha_num2'])) {
        $_SESSION['captcha_num1'] = rand(2, 9);
        $_SESSION['captcha_num2'] = rand(2, 9);
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['captcha_answer'])) {
        $answer = (int)$_POST['captcha_answer'];
        $expected = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2'];
        if ($answer === $expected) {
            $_SESSION['captcha_verified'] = true;
            unset($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $captcha_error = true;
            $_SESSION['captcha_num1'] = rand(2, 9);
            $_SESSION['captcha_num2'] = rand(2, 9);
        }
    }
    require __DIR__ . '/views/frontoffice/captcha_gate.php';
    exit;
}

// ─── Langue ────────────────────────────────────────────────
if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS)) {
    $_SESSION['lang'] = $_GET['lang'];
    // Rester sur la même page (sans param lang)
    $back = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $back . (isset($_GET['ctrl']) ? '?ctrl=' . $_GET['ctrl'] . '&action=' . ($_GET['action'] ?? 'index') : ''));
    exit;
}
$lang = $_SESSION['lang'] ?? DEFAULT_LANG;
$translations = require __DIR__ . '/lang/' . $lang . '.php';

// ─── Router ────────────────────────────────────────────────
$ctrl   = preg_replace('/[^a-zA-Z]/', '', $_GET['ctrl']   ?? 'home');
$action = preg_replace('/[^a-zA-Z]/', '', $_GET['action'] ?? 'index');
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Map ctrl → controller class
$controllerMap = [
    'home'       => 'HomeController',
    'auth'       => 'AuthController',
    'user'       => 'UserController',
    'offre'      => 'OffreController',
    'cv'         => 'CVController',
    'candidature'=> 'CandidatureController',
    'entretien'  => 'EntretienController',
    'reclamation'=> 'ReclamationController',
    'formation'  => 'FormationController',
    'admin'      => 'AdminController',
    'societe'    => 'SocieteController',
    'quiz'       => 'QuizController',
    'lang'       => null,
];

if (!array_key_exists($ctrl, $controllerMap)) {
    http_response_code(404);
    require __DIR__ . '/views/frontoffice/404.php';
    exit;
}

$controllerClass = $controllerMap[$ctrl];
$controllerFile  = __DIR__ . '/controllers/' . $controllerClass . '.php';

if ($ctrl === 'lang' || $controllerClass === null) {
    // handled above via redirect
    exit;
}

if (!file_exists($controllerFile)) {
    http_response_code(404);
    require __DIR__ . '/views/frontoffice/404.php';
    exit;
}

require_once $controllerFile;
$controller = new $controllerClass();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    require __DIR__ . '/views/frontoffice/404.php';
    exit;
}

$controller->$action();
