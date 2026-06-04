<?php $pageTitle = e($offre['titre']); require __DIR__ . '/../layout/header.php'; ?>

<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container">

  <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="btn btn-ghost btn-sm mb-3">← <?= __('gen.back') ?></a>

  <div class="grid" style="grid-template-columns:2fr 1fr;gap:2rem;align-items:start">

    <!-- Main -->
    <div>
      <div class="card animate-fade-in-up mb-3">
        <div class="flex-between flex-wrap gap-2 mb-3">
          <div>
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <span class="badge badge-primary"><?= e($offre['type_contrat']) ?></span>
              <?php if ($offre['date_expiration'] && strtotime($offre['date_expiration']) < time()): ?>
                <span class="badge badge-danger"><?= $lang==='fr'?'Expirée':'Expired' ?></span>
              <?php endif; ?>
            </div>
            <h1 style="font-size:1.5rem"><?= e($offre['titre']) ?></h1>
            <div class="flex items-center gap-3 flex-wrap text-sm text-muted mt-1">
              <span>🏢 <?= e($offre['nom_societe'] ?: $offre['soc_nom']) ?></span>
              <?php if ($offre['localisation']): ?><span>📍 <?= e($offre['localisation']) ?></span><?php endif; ?>
              <?php if ($offre['salaire']): ?><span>💰 <?= e($offre['salaire']) ?></span><?php endif; ?>
              <?php if ($offre['experience_requise']): ?><span>📅 <?= e($offre['experience_requise']) ?></span><?php endif; ?>
            </div>
          </div>
        </div>

        <div class="divider"></div>
        <h3 style="margin-bottom:.75rem"><?= $lang==='fr'?'Description du poste':'Job Description' ?></h3>
        <div style="line-height:1.8;white-space:pre-wrap"><?= e($offre['description']) ?></div>
      </div>

      <!-- Requirements -->
      <?php if (!empty($requirements)): ?>
      <div class="card animate-fade-in-up delay-100">
        <h3 style="margin-bottom:1.25rem">🎯 <?= __('offre.requirements') ?></h3>
        <div style="display:flex;flex-direction:column;gap:.75rem">
          <?php foreach ($requirements as $r):
            $found = in_array($r['competence'], $matching['found']);
            $miss  = in_array($r['competence'], $matching['missing']);
          ?>
          <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 1rem;border-radius:var(--radius-sm);background:<?= $found?'var(--success-50)':($miss?'var(--danger-50)':'var(--bg)') ?>;border:1px solid <?= $found?'var(--success-100)':($miss?'var(--danger-100)':'var(--border)') ?>">
            <div class="flex items-center gap-2">
              <span><?= $found ? '✅' : ($miss ? '❌' : '📌') ?></span>
              <span style="font-weight:600;font-size:.9rem"><?= e($r['competence']) ?></span>
              <?php if ($r['obligatoire']): ?><span class="badge badge-danger text-xs">Requis</span><?php endif; ?>
            </div>
            <span class="badge badge-gray"><?= ucfirst($r['niveau']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div>
      <!-- Matching Score -->
      <?php if (isLoggedIn() && hasRole('candidat') && $matching['score'] > 0): ?>
      <div class="card animate-fade-in-up" style="text-align:center;margin-bottom:1.5rem;box-shadow:var(--shadow-purple);border-color:rgba(167, 139, 250, 0.25)">
        <div style="font-weight:700;margin-bottom:1rem;color:var(--purple-light)">🤖 <?= __('offre.match') ?></div>
        <div class="score-ring" data-score="<?= $matching['score'] ?>" style="margin:0 auto">
          <svg width="90" height="90" viewBox="0 0 90 90">
            <circle cx="45" cy="45" r="40" fill="none" stroke="var(--border)" stroke-width="6"/>
            <circle class="score-ring-circle" cx="45" cy="45" r="40"/>
          </svg>
          <div class="score-ring-val"><?= $matching['score'] ?>%</div>
        </div>
        <?php if (!empty($matching['missing'])): ?>
        <div style="margin-top:1rem;text-align:left">
          <div class="text-xs text-muted font-semibold mb-1">Compétences manquantes :</div>
          <div class="flex flex-wrap gap-1">
            <?php foreach($matching['missing'] as $m): ?>
            <span class="skill-tag missing"><?= e($m) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- Apply Card -->
      <div class="card animate-fade-in-up delay-100">
        <?php if (!isLoggedIn()): ?>
          <a href="<?= APP_URL ?>/?ctrl=auth&action=login" class="btn btn-primary w-full btn-lg mb-2"><?= __('offre.apply') ?></a>
          <a href="<?= APP_URL ?>/?ctrl=auth&action=register" class="btn btn-outline w-full"><?= __('auth.register') ?></a>
        <?php elseif (hasRole('candidat')): ?>
          <?php if ($applied): ?>
            <div class="alert alert-success">✅ <?= __('cand.apply_success') ?></div>
          <?php else: ?>
            <a href="<?= APP_URL ?>/?ctrl=offre&action=apply&id=<?= $offre['id'] ?>" class="btn btn-primary w-full btn-lg"><?= __('offre.apply') ?> →</a>
          <?php endif; ?>

          <!-- Favorite button -->
          <button
            id="fav-toggle-btn"
            class="btn w-full mt-2 <?= $favorited ? 'fav-active' : '' ?>"
            data-offre-id="<?= $offre['id'] ?>"
            data-csrf="<?= csrf_token() ?>"
            style="<?= $favorited ? 'background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.4)' : 'border:1px solid var(--border);color:var(--text-muted)' ?>;transition:all .3s ease;margin-top:.5rem">
            <?= $favorited ? '❤️ ' . ($lang==='fr'?'Sauvegardé':'Saved') : '🤍 ' . ($lang==='fr'?'Sauvegarder':'Save') ?>
          </button>
        <?php endif; ?>

        <div class="divider"></div>
        <div style="font-size:.85rem;color:var(--text-muted)">
          <div style="margin-bottom:.5rem">📅 <?= __('offre.published') ?> <strong><?= date('d/m/Y', strtotime($offre['created_at'])) ?></strong></div>
          <?php if ($offre['date_expiration']): ?>
          <div>⏳ <?= __('offre.expires') ?> <strong><?= date('d/m/Y', strtotime($offre['date_expiration'])) ?></strong></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<script>
const favBtn = document.getElementById('fav-toggle-btn');
if (favBtn) {
  favBtn.addEventListener('click', async () => {
    const offreId = favBtn.dataset.offreId;
    const csrf = favBtn.dataset.csrf;
    const lang = '<?= $lang ?>';

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
        favBtn.textContent = '❤️ ' + (lang === 'fr' ? 'Sauvegardé' : 'Saved');
        favBtn.style.cssText = 'background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.4);transition:all .3s ease;width:100%;margin-top:.5rem';
        favBtn.style.transform = 'scale(1.05)';
        setTimeout(() => favBtn.style.transform = '', 300);
      } else {
        favBtn.textContent = '🤍 ' + (lang === 'fr' ? 'Sauvegarder' : 'Save');
        favBtn.style.cssText = 'border:1px solid var(--border);color:var(--text-muted);transition:all .3s ease;width:100%;margin-top:.5rem';
      }
    } catch(e) { console.error(e); }
  });
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
