<?php $pageTitle = __('profile.title'); require __DIR__ . '/../layout/header.php'; ?>

<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container-sm">
  <div class="flex-between mb-4 animate-fade-in-up">
    <h1 style="font-size:1.5rem">👤 <?= __('profile.title') ?></h1>
    <a href="<?= APP_URL ?>/?ctrl=user&action=editProfile" class="btn btn-outline btn-sm">✏️ <?= __('profile.edit') ?></a>
  </div>

  <!-- Profile Card -->
  <div class="card animate-fade-in-up mb-3" style="padding:2.5rem">
    <div style="display:grid;grid-template-columns:auto 1fr;gap:1.75rem;align-items:start">
      <!-- Avatar -->
      <div>
        <?php if (!empty($profil['photo'])): ?>
          <img src="<?= APP_URL ?>/public/uploads/<?= e($profil['photo']) ?>" style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:4px solid var(--primary-100)">
        <?php else: ?>
          <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--purple));color:#fff;font-size:2rem;font-weight:800;display:flex;align-items:center;justify-content:center">
            <?= strtoupper(substr(auth()['prenom'],0,1).substr(auth()['nom'],0,1)) ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Info -->
      <div>
        <h2 style="font-size:1.4rem"><?= e(auth()['prenom'].' '.auth()['nom']) ?></h2>
        <?php if (!empty($profil['titre'])): ?>
          <div style="color:var(--primary);font-weight:600;font-size:1rem;margin-top:.25rem"><?= e($profil['titre']) ?></div>
        <?php endif; ?>
        <div class="flex items-center gap-3 flex-wrap text-sm text-muted mt-2">
          <span>📧 <?= e(auth()['email']) ?></span>
          <?php if (!empty($profil['telephone'])): ?><span>📞 <?= e($profil['telephone']) ?></span><?php endif; ?>
          <?php if (!empty($profil['ville'])): ?><span>📍 <?= e($profil['ville']) ?></span><?php endif; ?>
          <?php if (!empty($profil['linkedin'])): ?><a href="<?= e($profil['linkedin']) ?>" target="_blank" style="color:var(--primary)">🔗 LinkedIn</a><?php endif; ?>
          <?php if (!empty($profil['github'])): ?><a href="<?= e($profil['github']) ?>" target="_blank" style="color:var(--text-secondary)">⚙️ GitHub</a><?php endif; ?>
        </div>

        <?php if (!empty($profil['bio'])): ?>
          <p style="margin-top:1rem;line-height:1.7"><?= nl2br(e($profil['bio'])) ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Skills -->
  <?php if (!empty($profil['competences'])): ?>
  <?php $skills = json_decode($profil['competences'], true) ?: []; ?>
  <?php if (!empty($skills)): ?>
  <div class="card animate-fade-in-up delay-100 mb-3">
    <h3 class="mb-3">⚡ <?= __('cv.skills') ?></h3>
    <div class="flex flex-wrap gap-2">
      <?php foreach ($skills as $s): ?><span class="skill-tag"><?= e($s) ?></span><?php endforeach; ?>
    </div>
  </div>
  <?php endif; endif; ?>

  <!-- Quick Links -->
  <div class="grid grid-3 animate-fade-in-up delay-200" style="gap:1rem">
    <a href="<?= APP_URL ?>/?ctrl=cv&action=<?= $cv ? 'show' : 'create' ?>" class="card hover-lift" style="text-align:center;padding:1.5rem;text-decoration:none">
      <div style="font-size:2rem;margin-bottom:.5rem">📄</div>
      <div style="font-weight:600"><?= __('nav.my_cv') ?></div>
      <div class="text-xs text-muted mt-1"><?= $cv ? ($lang==='fr'?'Voir mon CV':'View resume') : ($lang==='fr'?'Créer mon CV':'Create resume') ?></div>
    </a>
    <a href="<?= APP_URL ?>/?ctrl=candidature&action=index" class="card hover-lift" style="text-align:center;padding:1.5rem;text-decoration:none">
      <div style="font-size:2rem;margin-bottom:.5rem">📨</div>
      <div style="font-weight:600"><?= __('nav.my_apps') ?></div>
      <div class="text-xs text-muted mt-1"><?= $lang==='fr'?'Voir mes candidatures':'View applications' ?></div>
    </a>
    <a href="<?= APP_URL ?>/?ctrl=formation&action=index" class="card hover-lift" style="text-align:center;padding:1.5rem;text-decoration:none">
      <div style="font-size:2rem;margin-bottom:.5rem">📚</div>
      <div style="font-weight:600"><?= __('nav.trainings') ?></div>
      <div class="text-xs text-muted mt-1"><?= $lang==='fr'?'Mes formations':'My trainings' ?></div>
    </a>
  </div>
</div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
