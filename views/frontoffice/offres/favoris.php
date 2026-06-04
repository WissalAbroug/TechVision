<?php $pageTitle = $lang==='fr'?'Mes Favoris':'My Favorites'; require __DIR__ . '/../layout/header.php'; ?>

<section class="page-hero-banner">
  <div class="container">
    <h1 class="page-hero-title" style="margin-bottom:.5rem">❤️ <?= $lang==='fr'?'Mes Offres Favorites':'My Favorite Jobs' ?></h1>
    <p style="color:var(--text-secondary)"><?= count($offres) ?> <?= $lang==='fr'?'offre(s) sauvegardée(s)':'saved offer(s)' ?></p>
  </div>
</section>

<style>
/* ── Page Hero Banner ─────────────────────────────── */
.page-hero-banner {
  background: linear-gradient(135deg, #06060c 0%, #0d0b26 100%);
  padding: 3rem 0;
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
</style>

<section class="section-sm">
  <div class="container">
    <?php if (empty($offres)): ?>
      <div class="empty-state" style="text-align:center;padding:4rem 2rem">
        <div style="font-size:4rem;margin-bottom:1rem">💔</div>
        <h3><?= $lang==='fr'?'Aucune offre sauvegardée':'No saved offers yet' ?></h3>
        <p style="color:var(--text-muted);margin-bottom:2rem"><?= $lang==='fr'?'Parcourez les offres et cliquez sur ❤️ pour les sauvegarder ici.':'Browse offers and click ❤️ to save them here.' ?></p>
        <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="btn btn-primary">🔍 <?= $lang==='fr'?'Voir les offres':'Browse Jobs' ?></a>
      </div>
    <?php else: ?>
    <div style="display:flex;flex-direction:column;gap:1.25rem">
      <?php foreach ($offres as $o): ?>
      <div class="card animate-on-scroll" id="fav-card-<?= $o['id'] ?>">
        <div class="flex-between flex-wrap gap-2">
          <div style="flex:1">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <h3><a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $o['id'] ?>" class="text-primary"><?= e($o['titre']) ?></a></h3>
              <span class="badge badge-primary"><?= e($o['type_contrat']) ?></span>
            </div>
            <div class="flex items-center gap-2 flex-wrap text-sm text-muted" style="margin-bottom:.75rem">
              <span>🏢 <?= e($o['nom_societe'] ?: $o['soc_nom']) ?></span>
              <?php if ($o['localisation']): ?><span>📍 <?= e($o['localisation']) ?></span><?php endif; ?>
              <?php if ($o['salaire']): ?><span>💰 <?= e($o['salaire']) ?></span><?php endif; ?>
              <span>🕐 <?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
            </div>
            <p style="font-size:.875rem;line-height:1.6"><?= e(substr($o['description'],0,200)) ?>...</p>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.75rem;min-width:130px">
            <a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $o['id'] ?>" class="btn btn-primary btn-sm"><?= $lang==='fr'?'Voir l\'offre':'View offer' ?></a>
            <button
              class="btn btn-sm fav-btn fav-btn-active"
              data-offre-id="<?= $o['id'] ?>"
              data-csrf="<?= csrf_token() ?>"
              style="background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3)"
              title="<?= $lang==='fr'?'Retirer des favoris':'Remove from favorites' ?>">
              ❤️ <?= $lang==='fr'?'Retirer':'Remove' ?>
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<script>
document.querySelectorAll('.fav-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const offreId = btn.dataset.offreId;
    const csrf = btn.dataset.csrf;
    const card = document.getElementById('fav-card-' + offreId);

    const fd = new FormData();
    fd.append('offre_id', offreId);
    fd.append('csrf_token', csrf);

    const res = await fetch('<?= APP_URL ?>/?ctrl=offre&action=toggleFavoris', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd
    });
    const data = await res.json();

    if (!data.favorited && card) {
      card.style.transition = 'all .4s ease';
      card.style.opacity = '0';
      card.style.transform = 'translateX(50px)';
      setTimeout(() => card.remove(), 400);
    }
  });
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
