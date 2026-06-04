<?php $pageTitle = __('entretien.title'); require __DIR__ . '/../layout/header.php'; ?>

<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container">
  <div class="flex-between mb-4 animate-fade-in-up">
    <h1 style="font-size:1.5rem">🎤 <?= __('entretien.title') ?></h1>
    <a href="<?= APP_URL ?>/?ctrl=candidature&action=index" class="btn btn-ghost btn-sm">← <?= __('cand.title') ?></a>
  </div>

  <?php if (empty($entretiens)): ?>
    <div class="empty-state card">
      <div class="icon">📅</div>
      <h3><?= $lang==='fr'?'Aucun entretien planifié':'No scheduled interviews' ?></h3>
      <p><?= $lang==='fr'?'Les entreprises planifient les entretiens après examen de votre candidature.':'Companies schedule interviews after reviewing your application.' ?></p>
    </div>
  <?php else: ?>
  <div style="display:flex;flex-direction:column;gap:1.25rem">
    <?php foreach ($entretiens as $e): ?>
    <div class="card animate-on-scroll">
      <div class="flex-between flex-wrap gap-2">
        <div style="flex:1">
          <div class="flex items-center gap-2 mb-1 flex-wrap">
            <h3 style="font-size:1rem"><?= e($e['offre_titre']) ?></h3>
            <span class="status-pill status-<?= e($e['statut']) ?>"><?= ucfirst($e['statut']) ?></span>
          </div>
          <div class="flex items-center gap-3 text-sm text-muted flex-wrap">
            <span>🏢 <?= e($e['nom_societe'] ?: $e['soc_nom']) ?></span>
            <span>📅 <?= date('d/m/Y à H:i', strtotime($e['date_entretien'])) ?></span>
            <span>📋 <?= __('entretien.'.$e['type']) ?></span>
            <?php if ($e['lien_visio'] && $e['type']==='visio'): ?><a href="<?= e($e['lien_visio']) ?>" target="_blank" class="btn btn-success btn-sm">📹 Rejoindre</a><?php endif; ?>
          </div>
          <?php if ($e['notes']): ?><p style="font-size:.85rem;margin-top:.5rem;color:var(--text-muted)"><?= e($e['notes']) ?></p><?php endif; ?>
          <?php if ($e['score_simulation']): ?>
          <div style="margin-top:.5rem" class="flex items-center gap-2">
            <span class="text-sm font-semibold"><?= __('entretien.score') ?> :</span>
            <span style="font-size:1.1rem;font-weight:800;color:var(--<?= $e['score_simulation']>=70?'success':($e['score_simulation']>=40?'warning':'danger') ?>)"><?= $e['score_simulation'] ?>%</span>
          </div>
          <?php endif; ?>
        </div>
        <div class="flex flex-col gap-1" style="align-items:flex-end">
          <?php if ($e['statut'] !== 'annule' && $e['statut'] !== 'effectue'): ?>
            <a href="<?= APP_URL ?>/?ctrl=entretien&action=simulate&id=<?= $e['id'] ?>" class="btn btn-purple btn-sm"><?= __('entretien.start_sim') ?></a>
          <?php elseif ($e['statut'] === 'effectue'): ?>
            <a href="<?= APP_URL ?>/?ctrl=entretien&action=result&id=<?= $e['id'] ?>" class="btn btn-ghost btn-sm">📊 <?= __('entretien.result') ?></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
