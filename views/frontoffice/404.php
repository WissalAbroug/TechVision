<?php $pageTitle = '404 — Page introuvable'; $lang = $_SESSION['lang'] ?? 'fr'; ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>404 — <?= APP_NAME ?></title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/main.css">
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/animations.css">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--primary-50),var(--purple-50))">
  <div style="text-align:center;padding:3rem" class="animate-scale-in">
    <div style="font-size:6rem;animation:float 3s ease-in-out infinite">🔍</div>
    <h1 style="font-size:4rem;font-weight:900;background:linear-gradient(135deg,var(--primary),var(--purple));-webkit-background-clip:text;-webkit-text-fill-color:transparent">404</h1>
    <h2 style="margin-bottom:.75rem"><?= $lang==='fr'?'Page introuvable':'Page not found' ?></h2>
    <p><?= $lang==='fr'?'La page que vous cherchez n\'existe pas ou a été déplacée.':'The page you\'re looking for doesn\'t exist or was moved.' ?></p>
    <div class="flex-center gap-2 mt-4">
      <a href="<?= APP_URL ?>/" class="btn btn-primary btn-lg">🏠 <?= $lang==='fr'?'Accueil':'Home' ?></a>
      <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="btn btn-outline btn-lg">🔍 <?= $lang==='fr'?'Voir les offres':'See offers' ?></a>
    </div>
  </div>
</div>
<script src="<?= APP_URL ?>/public/js/main.js"></script>
</body>
</html>
