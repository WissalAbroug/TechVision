<?php $pageTitle = __('cand.title'); require __DIR__ . '/../layout/header.php'; ?>

<div style="padding:2rem 0;background:transparent;min-height:calc(100vh - 68px)">
<div class="container">
  <div class="flex-between mb-4">
    <h1 style="font-size:1.5rem">📨 <?= __('cand.title') ?></h1>
    <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="btn btn-primary btn-sm">🔍 <?= __('offre.title') ?></a>
  </div>

  <?php if (empty($candidatures)): ?>
    <div class="empty-state card">
      <div class="icon">📭</div>
      <h3><?= $lang==='fr'?'Aucune candidature encore':'No applications yet' ?></h3>
      <p><?= $lang==='fr'?'Commencez par parcourir les offres disponibles.':'Start by browsing available offers.' ?></p>
      <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="btn btn-primary mt-2">Voir les offres</a>
    </div>
  <?php else: ?>
  <div style="display:flex;flex-direction:column;gap:1rem">
    <?php foreach ($candidatures as $c):
      $score = (float)$c['score_matching'];
      $missing = json_decode($c['competences_manquantes'] ?? '[]', true);
    ?>
    <div class="card animate-on-scroll">
      <div class="flex-between flex-wrap gap-2">
        <div style="flex:1">
          <div class="flex items-center gap-2 mb-1 flex-wrap">
            <h3 style="font-size:1rem"><a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $c['offre_id'] ?>" class="text-primary"><?= e($c['offre_titre']) ?></a></h3>
            <span class="status-pill status-<?= e($c['statut']) ?>"><?= __('cand.'.$c['statut']) ?></span>
          </div>
          <div class="flex items-center gap-3 text-sm text-muted flex-wrap">
            <span>🏢 <?= e($c['nom_societe'] ?: $c['soc_nom']) ?></span>
            <span>📋 <?= e($c['type_contrat']) ?></span>
            <span>🕐 <?= date('d/m/Y', strtotime($c['date_candidature'])) ?></span>
          </div>
          <?php if (!empty($missing)): ?>
          <div style="margin-top:.75rem">
            <span class="text-xs text-muted font-semibold"><?= __('cand.missing') ?> : </span>
            <?php foreach(array_slice($missing,0,5) as $m): ?><span class="skill-tag missing" style="margin-right:.25rem"><?= e($m) ?></span><?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <div style="text-align:center;min-width:90px">
          <?php $sColor = $score>=70?'success':($score>=40?'warning':'danger'); ?>
          <div style="font-size:1.5rem;font-weight:800;color:var(--<?= $sColor ?>)"><?= $score ?>%</div>
          <div class="text-xs text-muted"><?= __('cand.match_score') ?></div>
          <div class="progress-bar mt-1"><div class="progress-fill <?= $sColor==='success'?'green':($sColor==='warning'?'orange':'red') ?>" data-width="<?= $score ?>" style="width:0"></div></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
