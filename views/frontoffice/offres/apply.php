<?php $pageTitle = __('offre.apply').' — '.e($offre['titre']); require __DIR__ . '/../layout/header.php'; ?>

<div style="background:transparent;min-height:calc(100vh-68px);padding:2rem 0">
<div class="container-sm">
  <a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $offre['id'] ?>" class="btn btn-ghost btn-sm mb-3">← <?= __('gen.back') ?></a>

  <!-- Offre summary -->
  <div class="card animate-fade-in-up mb-3" style="border-left:4px solid var(--primary)">
    <h2 style="font-size:1.2rem"><?= e($offre['titre']) ?></h2>
    <div class="flex items-center gap-3 text-sm text-muted mt-1 flex-wrap">
      <span>🏢 <?= e($offre['nom_societe']) ?></span>
      <?php if ($offre['localisation']): ?><span>📍 <?= e($offre['localisation']) ?></span><?php endif; ?>
      <span class="badge badge-primary"><?= e($offre['type_contrat']) ?></span>
    </div>
  </div>

  <!-- Matching Score -->
  <?php if ($matching['score'] > 0): ?>
  <div class="card animate-fade-in-up delay-100 mb-3" style="background:linear-gradient(135deg,var(--primary-50),var(--purple-50))">
    <div class="flex-between flex-wrap gap-2">
      <div>
        <h3>🤖 <?= __('offre.match') ?> : <span style="color:var(--<?= $matching['score']>=70?'success':($matching['score']>=40?'warning':'danger') ?>);font-size:1.5rem"><?= $matching['score'] ?>%</span></h3>
        <?php if (!empty($matching['found'])): ?>
        <div style="margin-top:.75rem">
          <div class="text-xs font-semibold text-muted mb-1">✅ <?= $lang==='fr'?'Compétences matchées':'Matched skills' ?> :</div>
          <div class="flex flex-wrap gap-1">
            <?php foreach($matching['found'] as $f): ?><span class="skill-tag found"><?= e($f) ?></span><?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($matching['missing'])): ?>
        <div style="margin-top:.75rem">
          <div class="text-xs font-semibold text-muted mb-1">❌ <?= $lang==='fr'?'Compétences manquantes':'Missing skills' ?> :</div>
          <div class="flex flex-wrap gap-1">
            <?php foreach($matching['missing'] as $m): ?><span class="skill-tag missing"><?= e($m) ?></span><?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Application form -->
  <div class="card animate-fade-in-up delay-200">
    <h2 style="margin-bottom:1.5rem">📨 <?= $lang==='fr'?'Envoyer ma candidature':'Send my application' ?></h2>
    <form method="POST" action="<?= APP_URL ?>/?ctrl=offre&action=apply&id=<?= $offre['id'] ?>" novalidate id="applyForm">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Lettre de motivation':'Cover letter' ?></label>
        <textarea name="lettre_motivation" class="form-control" rows="8" placeholder="<?= $lang==='fr'?'Présentez-vous et expliquez pourquoi ce poste vous intéresse...':'Introduce yourself and explain why you are interested in this position...' ?>"><?= e($_POST['lettre_motivation'] ?? '') ?></textarea>
        <div class="form-hint"><?= $lang==='fr'?'Optionnel mais recommandé. Soyez authentique !':'Optional but recommended. Be authentic!' ?></div>
      </div>
      <div class="flex gap-2 justify-end">
        <a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $offre['id'] ?>" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
        <button type="submit" class="btn btn-primary btn-lg" id="applyBtn">📨 <?= __('offre.apply') ?></button>
      </div>
    </form>
  </div>
</div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
