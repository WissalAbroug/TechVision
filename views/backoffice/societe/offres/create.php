<?php $pageTitle = __('offre.create'); require __DIR__ . '/../../layout/sidebar.php'; ?>

<div class="animate-fade-in-up" style="max-width:900px">
  <a href="<?= APP_URL ?>/?ctrl=societe&action=offres" class="btn btn-ghost btn-sm mb-3">← <?= __('societe.my_offers') ?></a>
  <h2 class="mb-4">➕ <?= __('offre.create') ?></h2>

  <form method="POST" action="<?= APP_URL ?>/?ctrl=societe&action=storeOffre" novalidate id="offreForm">
    <?= csrf_field() ?>

    <div class="card mb-3">
      <h3 class="mb-3">📋 <?= $lang==='fr'?'Informations générales':'General information' ?></h3>
      <div class="form-group">
        <label class="form-label">Titre du poste <span class="req">*</span></label>
        <input type="text" name="titre" class="form-control" placeholder="Ex: Développeur PHP Senior" id="offreTitre">
        <div class="form-error" id="titreErr"></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Métier</label>
          <select name="metier_id" class="form-control">
            <option value="">-- Sélectionner --</option>
            <?php foreach ($metiers as $m): ?><option value="<?= $m['id'] ?>"><?= e($m['nom']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('offre.contract') ?> <span class="req">*</span></label>
          <select name="type_contrat" class="form-control">
            <?php foreach(['CDI','CDD','Stage','Freelance','Alternance'] as $t): ?><option value="<?= $t ?>"><?= $t ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">📍 Localisation</label>
          <input type="text" name="localisation" class="form-control" placeholder="Ex: Casablanca">
        </div>
        <div class="form-group">
          <label class="form-label">💰 Salaire</label>
          <input type="text" name="salaire" class="form-control" placeholder="Ex: 10 000 - 15 000 MAD">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">📅 Expérience requise</label>
          <input type="text" name="experience_requise" class="form-control" placeholder="Ex: 2 ans minimum">
        </div>
        <div class="form-group">
          <label class="form-label">⏳ Date d'expiration</label>
          <input type="date" name="date_expiration" class="form-control" min="<?= date('Y-m-d') ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description <span class="req">*</span></label>
        <textarea name="description" class="form-control" rows="6" id="offreDesc" placeholder="<?= $lang==='fr'?'Décrivez le poste, les missions, l\'environnement de travail...':'Describe the position, tasks, work environment...' ?>"></textarea>
        <div class="form-error" id="descErr"></div>
      </div>
    </div>

    <!-- Requirements -->
    <div class="card mb-3">
      <div class="flex-between mb-3">
        <h3>🎯 <?= __('offre.requirements') ?></h3>
        <button type="button" class="btn btn-outline btn-sm" data-add-row="reqContainer">+ <?= $lang==='fr'?'Ajouter':'Add' ?></button>
      </div>
      <div id="reqContainer"></div>
      <template id="reqContainer_template">
        <div class="dynamic-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem">
          <div class="form-group" style="margin:0"><label class="form-label">Compétence</label><input type="text" name="req_competence[]" class="form-control" placeholder="Ex: PHP"></div>
          <div class="form-group" style="margin:0"><label class="form-label">Niveau</label>
            <select name="req_niveau[]" class="form-control">
              <option value="debutant">Débutant</option><option value="intermediaire" selected>Intermédiaire</option><option value="expert">Expert</option>
            </select>
          </div>
          <button type="button" data-remove-row class="btn btn-ghost btn-sm" style="color:var(--danger)">✕</button>
        </div>
      </template>
    </div>

    <div class="flex gap-2 justify-end">
      <a href="<?= APP_URL ?>/?ctrl=societe&action=offres" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
      <button type="submit" class="btn btn-primary btn-lg">📢 <?= __('offre.create') ?></button>
    </div>
  </form>
</div>

<script>
document.getElementById('offreForm').addEventListener('submit',function(e){
  let valid=true;
  document.getElementById('titreErr').textContent='';
  document.getElementById('descErr').textContent='';
  if(!document.getElementById('offreTitre').value.trim()){document.getElementById('titreErr').textContent='<?= __('gen.required') ?>';valid=false;}
  if(!document.getElementById('offreDesc').value.trim()){document.getElementById('descErr').textContent='<?= __('gen.required') ?>';valid=false;}
  if(!valid)e.preventDefault();
});
</script>

<?php require __DIR__ . '/../../layout/topbar.php'; ?>
