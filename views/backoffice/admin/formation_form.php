<?php 
$isEdit = isset($formation);
$pageTitle = $isEdit ? ($lang==='fr'?'Modifier la formation':'Edit training') : ($lang==='fr'?'Ajouter une formation':'Add training'); 
require __DIR__ . '/../layout/sidebar.php'; 

$skillsArr = [];
if ($isEdit && !empty($formation['competences_cibles'])) {
  $skillsArr = json_decode($formation['competences_cibles'], true) ?? [];
}
?>

<div class="container-sm animate-fade-in-up">
  <div class="flex-between mb-4">
    <h2>📚 <?= $pageTitle ?></h2>
    <a href="<?= APP_URL ?>/?ctrl=formation&action=adminIndex" class="btn btn-ghost btn-sm">← <?= $lang==='fr'?'Retour':'Back' ?></a>
  </div>

  <div class="card">
    <form method="POST" action="">
      <?= csrf_field() ?>

      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Titre de la formation':'Training Title' ?> *</label>
        <input type="text" name="titre" class="form-control" value="<?= e($formation['titre'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="5"><?= e($formation['description'] ?? '') ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">URL</label>
          <input type="url" name="url" class="form-control" value="<?= e($formation['url'] ?? '') ?>" placeholder="https://...">
        </div>
        <div class="form-group">
          <label class="form-label"><?= $lang==='fr'?'Plateforme':'Platform' ?></label>
          <input type="text" name="plateforme" class="form-control" value="<?= e($formation['plateforme'] ?? '') ?>" placeholder="Ex: Coursera, Udemy...">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">⚡ <?= __('cv.skills') ?> <span class="form-hint">(séparées par des virgules)</span></label>
        <input type="text" name="competences_cibles" class="form-control" value="<?= e(implode(', ', $skillsArr)) ?>" placeholder="Python, TensorFlow, Deep Learning...">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= $lang==='fr'?'Durée':'Duration' ?></label>
          <input type="text" name="duree" class="form-control" value="<?= e($formation['duree'] ?? '') ?>" placeholder="Ex: 12h, 4 semaines...">
        </div>
        <div class="form-group">
          <label class="form-label"><?= $lang==='fr'?'Niveau':'Level' ?></label>
          <select name="niveau" class="form-control">
            <option value="debutant" <?= ($formation['niveau'] ?? '') === 'debutant' ? 'selected' : '' ?>>Débutant</option>
            <option value="intermediaire" <?= ($formation['niveau'] ?? 'intermediaire') === 'intermediaire' ? 'selected' : '' ?>>Intermédiaire</option>
            <option value="expert" <?= ($formation['niveau'] ?? '') === 'expert' ? 'selected' : '' ?>>Expert</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Tarification':'Pricing' ?></label>
        <select name="gratuit" class="form-control">
          <option value="1" <?= ($formation['gratuit'] ?? 1) == 1 ? 'selected' : '' ?>><?= __('formation.free') ?></option>
          <option value="0" <?= ($formation['gratuit'] ?? 1) == 0 ? 'selected' : '' ?>><?= __('formation.paid') ?></option>
        </select>
      </div>

      <div class="flex gap-2 justify-end mt-4">
        <a href="<?= APP_URL ?>/?ctrl=formation&action=adminIndex" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
        <button type="submit" class="btn btn-primary">💾 <?= __('gen.save') ?></button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
