<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>CV — <?= e($user['prenom'].' '.$user['nom']) ?></title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; color: #1E293B; background: #fff; line-height: 1.4; }
  .cv-header { background: #2563EB; color: #fff; padding: 25px 30px; }
  .cv-header h1 { font-size: 24pt; margin-bottom: 4px; font-weight: bold; }
  .cv-header .title { font-size: 13pt; opacity: .9; margin-bottom: 12px; }
  .cv-header .contacts { display: flex; gap: 14px; font-size: 9pt; flex-wrap: wrap; }
  .cv-header .contacts span { white-space: nowrap; }
  .cv-body { display: grid; grid-template-columns: 2.2fr 1fr; gap: 0; min-height: 800px; }
  .main-col { padding: 25px 30px; }
  .side-col { background: #F8FAFC; padding: 25px 20px; border-left: 1px solid #E2E8F0; }
  
  h2 { font-size: 12pt; text-transform: uppercase; letter-spacing: 1px; color: #2563EB; border-bottom: 2px solid #2563EB; padding-bottom: 4px; margin: 20px 0 12px; }
  h2:first-child { margin-top: 0; }
  
  .section-block { margin-bottom: 16px; }
  .item-title { font-weight: bold; font-size: 10.5pt; color: #0F172A; }
  .item-subtitle { color: #3B82F6; font-size: 9.5pt; margin-bottom: 2px; }
  .item-meta { font-size: 8.5pt; color: #64748B; margin-bottom: 4px; font-style: italic; }
  .item-desc { font-size: 9.5pt; text-align: justify; }

  .skill-badge { display: inline-block; background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; padding: 3px 10px; border-radius: 4px; font-size: 8.5pt; margin: 3px 2px; font-weight: bold; }
  .lang-item { margin-bottom: 8px; display: flex; justify-content: space-between; font-size: 9.5pt; }
  .lang-level { font-size: 8.5pt; color: #64748B; }
  
  .side-item { margin-bottom: 10px; font-size: 9pt; }
  .side-title { font-weight: bold; color: #1E293B; margin-bottom: 2px; }
  .side-meta { font-size: 8pt; color: #64748B; }
</style>
</head>
<body>

<div class="cv-header">
  <h1><?= e($user['prenom'].' '.$user['nom']) ?></h1>
  <div class="title"><?= e($cv['titre']) ?></div>
  <div class="contacts">
    <span>✉ <?= e($user['email']) ?></span>
    <?php if ($profil['telephone']): ?><span>📞 <?= e($profil['telephone']) ?></span><?php endif; ?>
    <?php if ($profil['ville']): ?><span>📍 <?= e($profil['ville'] . ($profil['pays'] ? ', '.$profil['pays'] : '')) ?></span><?php endif; ?>
    <?php if ($profil['date_naissance']): ?><span>🎂 <?= e($profil['date_naissance']) ?></span><?php endif; ?>
    <?php if ($profil['nationalite']): ?><span>🌍 <?= e($profil['nationalite']) ?></span><?php endif; ?>
  </div>
</div>

<div class="cv-body">
  <div class="main-col">
    <!-- Résumé -->
    <?php if ($cv['resume']): ?>
    <h2>Profil</h2>
    <p class="item-desc" style="margin-bottom: 20px;"><?= nl2br(e($cv['resume'])) ?></p>
    <?php endif; ?>

    <!-- Expériences -->
    <?php if (!empty($cv['experiences'])): ?>
    <h2>Expériences professionnelles</h2>
    <?php foreach ($cv['experiences'] as $exp): ?>
    <div class="section-block">
      <div class="item-title"><?= e($exp['poste']) ?></div>
      <div class="item-subtitle"><?= e($exp['societe']) ?></div>
      <div class="item-meta"><?= e($exp['debut']) ?> — <?= e($exp['fin'] ?: 'Présent') ?></div>
      <?php if ($exp['desc']): ?><div class="item-desc"><?= nl2br(e($exp['desc'])) ?></div><?php endif; ?>
    </div>
    <?php endforeach; endif; ?>

    <!-- Projets -->
    <?php if (!empty($cv['projets'])): ?>
    <h2>Projets</h2>
    <?php foreach ($cv['projets'] as $proj): ?>
    <div class="section-block">
      <div class="item-title"><?= e($proj['titre']) ?> <?php if($proj['role']): ?><span style="font-weight:normal; color:#64748B">| <?= e($proj['role']) ?></span><?php endif; ?></div>
      <?php if ($proj['techs']): ?><div class="item-subtitle" style="font-size: 8.5pt; color: #2563EB"><?= e($proj['techs']) ?></div><?php endif; ?>
      <?php if ($proj['desc']): ?><div class="item-desc"><?= nl2br(e($proj['desc'])) ?></div><?php endif; ?>
    </div>
    <?php endforeach; endif; ?>

    <!-- Formation Académique -->
    <?php if (!empty($cv['educations'])): ?>
    <h2>Formation</h2>
    <?php foreach ($cv['educations'] as $edu): ?>
    <div class="section-block">
      <div class="item-title"><?= e($edu['diplome']) ?></div>
      <div class="item-subtitle"><?= e($edu['etablissement']) ?></div>
      <div class="item-meta"><?= e($edu['annee']) ?></div>
    </div>
    <?php endforeach; endif; ?>
  </div>

  <div class="side-col">
    <!-- Hard Skills -->
    <?php if (!empty($cv['competences'])): ?>
    <h2>Hard Skills</h2>
    <div style="margin-bottom: 20px;">
      <?php foreach ($cv['competences'] as $s): ?><span class="skill-badge"><?= e($s) ?></span><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Soft Skills -->
    <?php if (!empty($cv['soft_skills'])): ?>
    <h2>Soft Skills</h2>
    <div style="margin-bottom: 20px;">
      <?php foreach ($cv['soft_skills'] as $s): ?><span class="skill-badge" style="background:#F3F4F6; color:#475569; border-color:#E2E8F0"><?= e($s) ?></span><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Langues -->
    <?php if (!empty($cv['langues'])): ?>
    <h2>Langues</h2>
    <div style="margin-bottom: 20px;">
      <?php foreach ($cv['langues'] as $l): ?>
      <div class="lang-item"><strong><?= e($l['langue']) ?></strong><span class="lang-level"><?= e($l['niveau']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Certifications -->
    <?php if (!empty($cv['certifications'])): ?>
    <h2>Certifications</h2>
    <div style="margin-bottom: 20px;">
      <?php foreach ($cv['certifications'] as $cert): ?>
      <div class="side-item">
        <div class="side-title"><?= e($cert['nom']) ?></div>
        <div><?= e($cert['organisme']) ?></div>
        <div class="side-meta"><?= e($cert['date']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Formations Complémentaires -->
    <?php if (!empty($cv['formations_complementaires'])): ?>
    <h2>Formations Compl.</h2>
    <div style="margin-bottom: 20px;">
      <?php foreach ($cv['formations_complementaires'] as $fc): ?>
      <div class="side-item">
        <div class="side-title"><?= e($fc['titre']) ?></div>
        <div><?= e($fc['organisme']) ?></div>
        <div class="side-meta"><?= e($fc['annee']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Réalisations & Distinctions -->
    <?php if (!empty($cv['realisations'])): ?>
    <h2>Réalisations</h2>
    <div style="margin-bottom: 20px;">
      <?php foreach ($cv['realisations'] as $real): ?>
      <div class="side-item">
        <div class="side-title"><?= e($real['titre']) ?> <span style="font-weight:normal; font-size:8pt">(<?= e($real['annee']) ?>)</span></div>
        <?php if ($real['desc']): ?><div class="side-meta" style="color:#475569"><?= e($real['desc']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Centres d'intérêt -->
    <?php if (!empty($cv['centres_interet'])): ?>
    <h2>Intérêts</h2>
    <div style="margin-bottom: 20px; font-size: 9pt;">
      <?= e(implode(' • ', $cv['centres_interet'])) ?>
    </div>
    <?php endif; ?>

    <!-- Réseaux et Liens -->
    <?php 
    $cvRes = $cv['reseaux'] ?? [];
    $links = [];
    if(!empty($profil['linkedin'])) $links['LinkedIn'] = $profil['linkedin'];
    if(!empty($profil['github'])) $links['GitHub'] = $profil['github'];
    if(!empty($profil['portfolio'])) $links['Portfolio'] = $profil['portfolio'];
    if(!empty($profil['site_web'])) $links['Site Web'] = $profil['site_web'];
    if(!empty($cvRes['kaggle'])) $links['Kaggle'] = $cvRes['kaggle'];
    if(!empty($cvRes['stackoverflow'])) $links['Stack Overflow'] = $cvRes['stackoverflow'];
    
    if (!empty($links)): 
    ?>
    <h2>Liens & Réseaux</h2>
    <div style="font-size: 8.5pt; word-break: break-all;">
      <?php foreach ($links as $name => $url): ?>
      <div style="margin-bottom: 6px;">
        <strong><?= $name ?>:</strong><br>
        <a href="<?= e($url) ?>" style="color: #2563EB; text-decoration: none;"><?= e($url) ?></a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

</body>
</html>
