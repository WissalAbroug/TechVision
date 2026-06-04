<?php $pageTitle = __('profile.edit'); require __DIR__ . '/../layout/header.php'; ?>
<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container-sm">
  <a href="<?= APP_URL ?>/?ctrl=user&action=profile" class="btn btn-ghost btn-sm mb-3">← <?= __('nav.profile') ?></a>
  <div class="card animate-fade-in-up">
    <h2 class="mb-4">✏️ <?= $lang==='fr'?'Modifier mon profil':'Edit my profile' ?></h2>
    <form method="POST" action="<?= APP_URL ?>/?ctrl=user&action=editProfile" enctype="multipart/form-data" novalidate>
      <?= csrf_field() ?>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= __('gen.prenom') ?></label>
          <input type="text" name="prenom" class="form-control" value="<?= e($user['prenom']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('gen.nom') ?></label>
          <input type="text" name="nom" class="form-control" value="<?= e($user['nom']) ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Titre professionnel':'Professional title' ?></label>
        <input type="text" name="titre" class="form-control" placeholder="Ex: Développeur Full Stack" value="<?= e($profil['titre']??'') ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= __('gen.phone') ?></label>
          <input type="text" name="telephone" class="form-control" value="<?= e($profil['telephone']??'') ?>">
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('gen.city') ?></label>
          <input type="text" name="ville" class="form-control" value="<?= e($profil['ville']??'') ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Bio</label>
        <textarea name="bio" class="form-control" rows="4"><?= e($profil['bio']??'') ?></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">LinkedIn URL</label>
          <input type="text" name="linkedin" class="form-control" value="<?= e($profil['linkedin']??'') ?>" placeholder="https://linkedin.com/in/...">
        </div>
        <div class="form-group">
          <label class="form-label">GitHub URL</label>
          <input type="text" name="github" class="form-control" value="<?= e($profil['github']??'') ?>" placeholder="https://github.com/...">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">⚡ <?= __('cv.skills') ?> <span class="form-hint">(séparées par des virgules)</span></label>
        <input type="text" name="competences" class="form-control" value="<?= e(implode(', ', json_decode($profil['competences']??'[]', true))) ?>" placeholder="PHP, JavaScript, MySQL, React...">
      </div>
      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Photo de profil':'Profile photo' ?></label>
        <?php if (!empty($profil['photo'])): ?>
        <div style="margin-bottom:.75rem"><img src="<?= APP_URL ?>/public/uploads/<?= e($profil['photo']) ?>" style="width:64px;height:64px;border-radius:50%;object-fit:cover"></div>
        <?php endif; ?>
        <input type="file" name="photo" class="form-control" accept="image/*">
      </div>
      <div class="flex gap-2 justify-end">
        <a href="<?= APP_URL ?>/?ctrl=user&action=profile" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
        <button type="submit" class="btn btn-primary btn-lg">💾 <?= __('gen.save') ?></button>
      </div>
    </form>
  </div>
</div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
