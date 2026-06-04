<?php $pageTitle = __('offre.title'); require __DIR__ . '/../layout/header.php'; ?>

<section class="page-hero-banner">
  <div class="hero-orbs">
    <div class="orb orb-1" style="width: 300px; height: 300px; opacity: 0.12;"></div>
    <div class="orb orb-2" style="width: 200px; height: 200px; top: 20%; opacity: 0.12;"></div>
  </div>
  <div class="container" style="position: relative; z-index: 5;">
    <h1 class="page-hero-title" style="margin-bottom:.5rem">🔍 <?= __('offre.title') ?></h1>
    <p style="color:var(--text-secondary)"><?= count($offres) ?> <?= $lang==='fr'?'offres disponibles':'available offers' ?></p>

    <!-- Search Bar -->
    <form method="GET" action="<?= APP_URL ?>/?ctrl=offre&action=index" novalidate style="margin-top:1.5rem" class="card card-sm">
      <input type="hidden" name="ctrl" value="offre"><input type="hidden" name="action" value="index">
      <div style="display:grid;grid-template-columns:1fr auto auto auto;gap:.75rem;align-items:end" class="search-grid">
        <div>
          <label class="form-label" style="font-size:0.75rem"><?= $lang==='fr'?'Recherche':'Search' ?></label>
          <input type="text" name="q" class="form-control" placeholder="<?= __('offre.search') ?>" value="<?= e($filters['q']) ?>" style="height:48px">
        </div>
        <div>
          <label class="form-label" style="font-size:0.75rem"><?= $lang==='fr'?'Contrat':'Contract' ?></label>
          <select name="type_contrat" class="form-control" style="height:48px;min-width:160px">
            <option value=""><?= __('offre.all') ?></option>
            <?php foreach(['CDI','CDD','Stage','Freelance','Alternance'] as $t): ?>
            <option value="<?= $t ?>" <?= $filters['type_contrat']===$t?'selected':'' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label" style="font-size:0.75rem"><?= $lang==='fr'?'Localisation':'Location' ?></label>
          <input type="text" name="localisation" class="form-control" placeholder="📍 Ville..." value="<?= e($filters['localisation']) ?>" style="height:48px;min-width:160px">
        </div>
        <button type="submit" class="btn btn-primary" style="height:48px; min-width:130px">🔍 <?= __('gen.search') ?></button>
      </div>
    </form>
  </div>
</section>

<section class="section-sm">
  <div class="container">
    <?php if (empty($offres)): ?>
      <div class="empty-state"><div class="icon">🔍</div><h3><?= __('gen.no_data') ?></h3></div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:1.25rem">
      <?php foreach ($offres as $i => $o):
        $score  = (float)($o['score_matching'] ?? 0);
        $sColor = $score >= 70 ? 'success' : ($score >= 40 ? 'warning' : 'danger');
      ?>
      <div class="card animate-on-scroll" style="<?= $i===0?'border-left:4px solid var(--primary)':'' ?>">
        <div class="flex-between flex-wrap gap-2">
          <div style="flex:1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <h3><a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $o['id'] ?>" class="text-primary"><?= e($o['titre']) ?></a></h3>
              <?php if ($i===0): ?><span class="badge badge-purple">⭐ <?= $lang==='fr'?'Recommandé':'Recommended' ?></span><?php endif; ?>
            </div>
            <div class="flex items-center gap-2 flex-wrap text-sm text-muted" style="margin-bottom:.75rem">
              <span>🏢 <?= e($o['nom_societe'] ?: $o['soc_nom']) ?></span>
              <?php if ($o['localisation']): ?><span>📍 <?= e($o['localisation']) ?></span><?php endif; ?>
              <span>📋 <?= e($o['type_contrat']) ?></span>
              <?php if ($o['salaire']): ?><span>💰 <?= e($o['salaire']) ?></span><?php endif; ?>
              <span>🕐 <?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
            </div>
            <p style="font-size:.875rem;line-height:1.6"><?= e(substr($o['description'],0,200)) ?>...</p>
          </div>

          <!-- Score + Actions -->
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.75rem;min-width:140px">
            <?php if (isLoggedIn() && hasRole('candidat') && $score > 0): ?>
            <div class="score-ring" data-score="<?= $score ?>">
              <svg width="90" height="90" viewBox="0 0 90 90">
                <circle cx="45" cy="45" r="40" fill="none" stroke="var(--border)" stroke-width="6"/>
                <circle class="score-ring-circle" cx="45" cy="45" r="40"/>
              </svg>
              <div class="score-ring-val"><?= $score ?>%</div>
            </div>
            <div style="font-size:.72rem;color:var(--text-muted);text-align:center"><?= __('offre.match') ?></div>
            <?php endif; ?>
            <div class="flex gap-1 flex-wrap justify-end">
              <?php if (isLoggedIn() && hasRole('candidat')): ?>
              <button
                class="btn btn-sm fav-btn <?= ($o['favorited'] ?? false) ? 'fav-active' : '' ?>"
                data-offre-id="<?= $o['id'] ?>"
                data-csrf="<?= csrf_token() ?>"
                title="<?= $lang==='fr'?'Sauvegarder':'Save' ?>"
                style="<?= ($o['favorited'] ?? false) ? 'background:rgba(239,68,68,.15);color:#f87171;border-color:rgba(239,68,68,.4)' : 'color:var(--text-muted)' ?>">
                <?= ($o['favorited'] ?? false) ? '❤️' : '🤍' ?>
              </button>
              <?php endif; ?>
              <a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm"><?= __('gen.view') ?></a>
              <?php if ($o['applied'] ?? false): ?>
                <span class="btn btn-sm" style="background:var(--success-100);color:#15803D;cursor:default">✅ <?= __('offre.applied') ?></span>
              <?php elseif (isLoggedIn() && hasRole('candidat')): ?>
                <a href="<?= APP_URL ?>/?ctrl=offre&action=apply&id=<?= $o['id'] ?>" class="btn btn-primary btn-sm"><?= __('offre.apply') ?></a>
              <?php elseif (!isLoggedIn()): ?>
                <a href="<?= APP_URL ?>/?ctrl=auth&action=login" class="btn btn-primary btn-sm"><?= __('offre.apply') ?></a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<style>
/* ── Page Hero Banner ─────────────────────────────── */
.page-hero-banner {
  background: linear-gradient(135deg, #06060c 0%, #0d0b26 100%);
  padding: 4rem 0;
  border-bottom: 1px solid var(--border);
  position: relative;
  overflow: hidden;
}
body.theme-light .page-hero-banner {
  background: linear-gradient(135deg, #EFF6FF 0%, #EDE9FE 100%);
}
.page-hero-title {
  color: #ffffff;
}
body.theme-light .page-hero-title {
  color: #0f172a !important;
}
body.theme-light .page-hero-banner p {
  color: var(--text-secondary) !important;
}
@media(max-width:768px){.search-grid{grid-template-columns:1fr!important}}
.fav-btn{transition:all .3s ease;border:1px solid var(--border)}
.fav-btn:hover{transform:scale(1.15)}
.fav-btn.fav-active{background:rgba(239,68,68,.15);color:#f87171;border-color:rgba(239,68,68,.4)}
</style>

<script>
document.querySelectorAll('.fav-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const offreId = btn.dataset.offreId;
    const csrf = btn.dataset.csrf;

    const fd = new FormData();
    fd.append('offre_id', offreId);
    fd.append('csrf_token', csrf);

    try {
      const res = await fetch('<?= APP_URL ?>/?ctrl=offre&action=toggleFavoris', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
      });
      const data = await res.json();

      if (data.favorited) {
        btn.textContent = '❤️';
        btn.classList.add('fav-active');
        btn.style.cssText = 'background:rgba(239,68,68,.15);color:#f87171;border-color:rgba(239,68,68,.4);transition:all .3s ease;border:1px solid rgba(239,68,68,.4)';
        btn.style.transform = 'scale(1.25)';
        setTimeout(() => btn.style.transform = '', 300);
      } else {
        btn.textContent = '🤍';
        btn.classList.remove('fav-active');
        btn.style.cssText = 'color:var(--text-muted);transition:all .3s ease;border:1px solid var(--border)';
      }
    } catch(e) { console.error(e); }
  });
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
