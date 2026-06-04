<?php $pageTitle = __('recl.new'); require __DIR__ . '/../layout/header.php'; ?>
<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container-sm">
  <a href="<?= APP_URL ?>/?ctrl=reclamation&action=index" class="btn btn-ghost btn-sm mb-3">← <?= __('recl.title') ?></a>
  <div class="card animate-scale-in" style="padding:2.5rem">
    <h1 style="font-size:1.4rem;margin-bottom:1.5rem">🎫 <?= __('recl.new') ?></h1>
    <form method="POST" action="<?= APP_URL ?>/?ctrl=reclamation&action=store" novalidate id="reclForm">
      <?= csrf_field() ?>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= __('recl.subject') ?> <span class="req">*</span></label>
          <input type="text" name="titre" id="reclTitre" class="form-control" placeholder="<?= $lang==='fr'?'Résumez votre problème...':'Summarize your issue...' ?>">
          <div class="form-error" id="titreErr"></div>
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('recl.priority') ?></label>
          <select name="priorite" class="form-control">
            <option value="basse"><?= $lang==='fr'?'Basse':'Low' ?></option>
            <option value="normale" selected><?= $lang==='fr'?'Normale':'Normal' ?></option>
            <option value="haute"><?= $lang==='fr'?'Haute':'High' ?></option>
            <option value="urgente"><?= $lang==='fr'?'Urgente':'Urgent' ?></option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Catégorie':'Category' ?></label>
        <select name="categorie" class="form-control">
          <option value=""><?= $lang==='fr'?'-- Sélectionner --':'-- Select --' ?></option>
          <option>Candidature</option><option>Offre d'emploi</option><option>Compte</option><option>Entretien</option><option>Technique</option><option>Autre</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label"><?= __('recl.desc') ?> <span class="req">*</span></label>
        <textarea name="description" id="reclDesc" class="form-control" rows="6" placeholder="<?= $lang==='fr'?'Décrivez votre problème en détail...':'Describe your issue in detail...' ?>"></textarea>
        <div class="form-error" id="descErr"></div>
      </div>
      <div class="flex gap-2 justify-end">
        <a href="<?= APP_URL ?>/?ctrl=reclamation&action=index" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
        <button type="submit" class="btn btn-primary">📨 <?= __('recl.sent') ?></button>
      </div>
    </form>
  </div>
</div>
</div>
<script>
document.getElementById('reclForm').addEventListener('submit',function(e){
  let valid=true;
  document.getElementById('titreErr').textContent='';
  document.getElementById('descErr').textContent='';
  if(!document.getElementById('reclTitre').value.trim()){document.getElementById('titreErr').textContent='<?= __('gen.required') ?>';valid=false;}
  if(!document.getElementById('reclDesc').value.trim()){document.getElementById('descErr').textContent='<?= __('gen.required') ?>';valid=false;}
  if(!valid)e.preventDefault();
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
