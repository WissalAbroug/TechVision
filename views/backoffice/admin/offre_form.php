<?php 
$isEdit = isset($offre);
$pageTitle = $isEdit ? ($lang==='fr'?'Modifier l\'offre':'Edit offer') : ($lang==='fr'?'Créer une offre':'Create offer'); 
require __DIR__ . '/../layout/sidebar.php'; 
?>

<div class="container-sm animate-fade-in-up">
  <div class="flex-between mb-4">
    <h2>💼 <?= $pageTitle ?></h2>
    <a href="<?= APP_URL ?>/?ctrl=admin&action=offres" class="btn btn-ghost btn-sm">← <?= $lang==='fr'?'Retour':'Back' ?></a>
  </div>

  <div class="card">
    <form method="POST" action="">
      <?= csrf_field() ?>

      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Société émettrice':'Issuing Company' ?> *</label>
        <select name="societe_id" class="form-control" required>
          <option value=""><?= $lang==='fr'?'Choisir une entreprise...':'Choose a company...' ?></option>
          <?php foreach ($companies as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($offre['societe_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>>
              <?= e($c['nom_societe'] ? $c['nom_societe'] . ' (' . $c['prenom'] . ' ' . $c['nom'] . ')' : $c['prenom'] . ' ' . $c['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Titre du poste':'Job Title' ?> *</label>
        <input type="text" name="titre" class="form-control" value="<?= e($offre['titre'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label">Description *</label>
        <textarea name="description" class="form-control" rows="8" required><?= e($offre['description'] ?? '') ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= $lang==='fr'?'Localisation':'Location' ?></label>
          <input type="text" name="localisation" class="form-control" value="<?= e($offre['localisation'] ?? '') ?>" placeholder="Ex: Casablanca, à distance...">
        </div>
        <div class="form-group">
          <label class="form-label"><?= $lang==='fr'?'Type de contrat':'Contract type' ?> *</label>
          <select name="type_contrat" class="form-control" required>
            <?php foreach (['CDI','CDD','Stage','Freelance','Alternance'] as $t): ?>
              <option value="<?= $t ?>" <?= ($offre['type_contrat'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= $lang==='fr'?'Salaire':'Salary' ?></label>
          <input type="text" name="salaire" class="form-control" value="<?= e($offre['salaire'] ?? '') ?>" placeholder="Ex: 10 000 DH">
        </div>
        <div class="form-group">
          <label class="form-label"><?= $lang==='fr'?'Expérience requise':'Experience required' ?></label>
          <input type="text" name="experience_requise" class="form-control" value="<?= e($offre['experience_requise'] ?? '') ?>" placeholder="Ex: 2 ans, Senior...">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Statut</label>
          <select name="statut" class="form-control">
            <option value="active" <?= ($offre['statut'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= ($offre['statut'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="cloturee" <?= ($offre['statut'] ?? '') === 'cloturee' ? 'selected' : '' ?>>Clôturée</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label"><?= $lang==='fr'?'Date d\'expiration':'Expiration date' ?></label>
          <input type="date" name="date_expiration" class="form-control" value="<?= e($offre['date_expiration'] ?? '') ?>">
        </div>
      </div>

      <div class="flex gap-2 justify-end mt-4">
        <a href="<?= APP_URL ?>/?ctrl=admin&action=offres" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
        <button type="submit" class="btn btn-primary">💾 <?= __('gen.save') ?></button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
