<?php
$user  = auth();
$flash = get_flash();
$lang  = $_SESSION['lang'] ?? 'fr';
$currentCtrl = preg_replace('/[^a-zA-Z]/', '', $_GET['ctrl'] ?? 'home');
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?= __('home.hero_sub') ?>">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?= APP_URL ?>/public/css/main.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/public/css/animations.css">
</head>
<body class="theme-dark">
<script>
  (function() {
    const t = localStorage.getItem('theme') || 'dark';
    document.body.className = 'theme-' + t;
    document.documentElement.style.background = t === 'dark' ? '#06060c' : '#f1f5f9';
  })();
</script>

<!-- Ambient Cyberpunk Particles Background -->
<canvas id="bg-neural-canvas" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:-2;pointer-events:none;"></canvas>

<?php require __DIR__ . '/nav.php'; ?>

<?php if ($flash): ?>
<div class="toast-container">
  <div class="toast toast-<?= e($flash['type']) ?>">
    <span><?= $flash['type'] === 'success' ? '✅' : ($flash['type'] === 'error' ? '❌' : '⚠️') ?></span>
    <?= e($flash['message']) ?>
  </div>
</div>
<?php endif; ?>

<?php
// Form errors
if (!empty($_SESSION['form_errors'])):
  $errs = $_SESSION['form_errors'];
  unset($_SESSION['form_errors']);
?>
<div class="toast-container">
  <?php foreach ($errs as $err): ?>
  <div class="toast toast-error">❌ <?= e($err) ?></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<main class="page-enter">
