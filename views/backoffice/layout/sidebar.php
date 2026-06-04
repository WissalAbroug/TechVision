<?php
require_once __DIR__ . '/../../../config/config.php';
$user  = auth();
$lang  = $_SESSION['lang'] ?? 'fr';
$flash = get_flash();
$currentCtrl = preg_replace('/[^a-zA-Z]/', '', $_GET['ctrl'] ?? 'home');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= isset($pageTitle)?e($pageTitle).' — ':'' ?><?= APP_NAME ?> Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?= APP_URL ?>/public/css/main.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/public/css/dashboard.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/public/css/animations.css">
</head>
<body class="theme-dark">
<script>
  (function() {
    const t = localStorage.getItem('theme') || 'dark';
    document.body.className = 'theme-' + t;
  })();
</script>

<!-- Ambient Cyberpunk Particles Background -->
<canvas id="bg-neural-canvas" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:-2;pointer-events:none;"></canvas>

<?php require __DIR__ . '/../../frontoffice/layout/nav.php'; ?>

<?php if ($flash): ?>
<div class="toast-container">
  <div class="toast toast-<?= e($flash['type']) ?>">
    <span><?= $flash['type'] === 'success' ? '✅' : ($flash['type'] === 'error' ? '❌' : '⚠️') ?></span>
    <?= e($flash['message']) ?>
  </div>
</div>
<?php endif; ?>

<main class="container page-enter" style="padding-top: 3rem; padding-bottom: 5rem; min-height: 80vh; max-width: 1400px;">
  <!-- Optional Page Title equivalent to what was in topbar -->
  <?php if(isset($pageTitle)): ?>
  <div class="mb-4">
    <h1 style="font-size: 2rem; font-family: 'Outfit', sans-serif;"><?= e($pageTitle) ?></h1>
  </div>
  <?php endif; ?>
