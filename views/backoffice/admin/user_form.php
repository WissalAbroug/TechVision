<?php 
$isEdit = isset($user);
$pageTitle = $isEdit ? ($lang==='fr'?'Modifier l\'utilisateur':'Edit user') : ($lang==='fr'?'Ajouter un utilisateur':'Add user'); 
require __DIR__ . '/../layout/sidebar.php'; 
?>

<div class="container-sm animate-fade-in-up">
  <div class="flex-between mb-4">
    <h2>👤 <?= $pageTitle ?></h2>
    <a href="<?= APP_URL ?>/?ctrl=admin&action=users" class="btn btn-ghost btn-sm">← <?= $lang==='fr'?'Retour':'Back' ?></a>
  </div>

  <div class="card">
    <form method="POST" action="">
      <?= csrf_field() ?>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= __('gen.prenom') ?> *</label>
          <input type="text" name="prenom" class="form-control" value="<?= e($user['prenom'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('gen.nom') ?> *</label>
          <input type="text" name="nom" class="form-control" value="<?= e($user['nom'] ?? '') ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label class="form-label"><?= __('auth.password') ?> <?= $isEdit ? ($lang==='fr'?'(laisser vide pour ne pas modifier)':'(leave empty to keep unchanged)') : '*' ?></label>
        <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?> placeholder="••••••••">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Rôle</label>
          <select name="role" id="user-role-select" class="form-control">
            <option value="candidat" <?= ($user['role'] ?? '') === 'candidat' ? 'selected' : '' ?>>Candidat</option>
            <option value="societe" <?= ($user['role'] ?? '') === 'societe' ? 'selected' : '' ?>>Société</option>
            <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Statut</label>
          <select name="statut" class="form-control">
            <option value="actif" <?= ($user['statut'] ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
            <option value="inactif" <?= ($user['statut'] ?? '') === 'inactif' ? 'selected' : '' ?>>Inactif</option>
            <option value="suspendu" <?= ($user['statut'] ?? '') === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
          </select>
        </div>
      </div>

      <div id="company-fields" style="display: <?= ($user['role'] ?? '') === 'societe' ? 'block' : 'none' ?>;">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><?= $lang==='fr'?'Nom de la société':'Company name' ?></label>
            <input type="text" name="nom_societe" class="form-control" value="<?= e($user['nom_societe'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label"><?= $lang==='fr'?'Secteur d\'activité':'Industry' ?></label>
            <input type="text" name="secteur" class="form-control" value="<?= e($user['secteur'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="flex gap-2 justify-end mt-4">
        <a href="<?= APP_URL ?>/?ctrl=admin&action=users" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
        <button type="submit" class="btn btn-primary">💾 <?= __('gen.save') ?></button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.getElementById('user-role-select');
    const companyFields = document.getElementById('company-fields');
    
    if (roleSelect && companyFields) {
      roleSelect.addEventListener('change', () => {
        if (roleSelect.value === 'societe') {
          companyFields.style.display = 'block';
        } else {
          companyFields.style.style = ''; // Reset display
          companyFields.style.display = 'none';
        }
      });
    }
  });
</script>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
