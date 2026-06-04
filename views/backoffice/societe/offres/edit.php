<?php $pageTitle = __('offre.edit'); require __DIR__ . '/../../layout/sidebar.php'; ?>
<div style="max-width:900px" class="animate-fade-in-up">
  <a href="<?= APP_URL ?>/?ctrl=societe&action=offres" class="btn btn-ghost btn-sm mb-3">← <?= __('societe.my_offers') ?></a>
  <h2 class="mb-4">✏️ <?= __('offre.edit') ?></h2>
  <form method="POST" action="<?= APP_URL ?>/?ctrl=societe&action=editOffre&id=<?= $offre['id'] ?>" novalidate>
    <?= csrf_field() ?>
    <div class="card mb-3">
      <div class="form-group">
        <label class="form-label">Titre <span class="req">*</span></label>
        <input type="text" name="titre" class="form-control" value="<?= e($offre['titre']) ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Métier</label>
          <select name="metier_id" class="form-control">
            <option value="">--</option>
            <?php foreach ($metiers as $m): ?><option value="<?= $m['id'] ?>" <?= $m['id']==$offre['metier_id']?'selected':'' ?>><?= e($m['nom']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Contrat</label>
          <select name="type_contrat" class="form-control">
            <?php foreach(['CDI','CDD','Stage','Freelance','Alternance'] as $t): ?><option value="<?= $t ?>" <?= $t===$offre['type_contrat']?'selected':'' ?>><?= $t ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Localisation</label><input type="text" name="localisation" class="form-control" value="<?= e($offre['localisation']??'') ?>"></div>
        <div class="form-group"><label class="form-label">Salaire</label><input type="text" name="salaire" class="form-control" value="<?= e($offre['salaire']??'') ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Expérience</label><input type="text" name="experience_requise" class="form-control" value="<?= e($offre['experience_requise']??'') ?>"></div>
        <div class="form-group"><label class="form-label">Statut</label>
          <select name="statut" class="form-control">
            <?php foreach(['active','inactive','cloturee'] as $s): ?><option value="<?= $s ?>" <?= $s===$offre['statut']?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Description <span class="req">*</span></label><textarea name="description" class="form-control" rows="6"><?= e($offre['description']) ?></textarea></div>
    </div>
    <div class="card mb-3">
      <div class="flex-between mb-3"><h3>🎯 Requirements</h3><button type="button" class="btn btn-outline btn-sm" data-add-row="reqEdit">+ Ajouter</button></div>
      <div id="reqEdit">
        <?php foreach ($requirements as $r): ?>
        <div class="dynamic-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem">
          <div><label class="form-label">Compétence</label><input type="text" name="req_competence[]" class="form-control" value="<?= e($r['competence']) ?>"></div>
          <div><label class="form-label">Niveau</label>
            <select name="req_niveau[]" class="form-control">
              <?php foreach(['debutant','intermediaire','expert'] as $nv): ?><option value="<?= $nv ?>" <?= $nv===$r['niveau']?'selected':'' ?>><?= ucfirst($nv) ?></option><?php endforeach; ?>
            </select>
          </div>
          <button type="button" data-remove-row class="btn btn-ghost btn-sm" style="color:var(--danger)">✕</button>
        </div>
        <?php endforeach; ?>
      </div>
      <template id="reqEdit_template">
        <div class="dynamic-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem">
          <div><label class="form-label">Compétence</label><input type="text" name="req_competence[]" class="form-control" placeholder="Ex: React"></div>
          <div><label class="form-label">Niveau</label><select name="req_niveau[]" class="form-control"><option value="debutant">Débutant</option><option value="intermediaire" selected>Intermédiaire</option><option value="expert">Expert</option></select></div>
          <button type="button" data-remove-row class="btn btn-ghost btn-sm" style="color:var(--danger)">✕</button>
        </div>
      </template>
    </div>
    <div class="flex gap-2 justify-end">
      <a href="<?= APP_URL ?>/?ctrl=societe&action=offres" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
      <button type="submit" class="btn btn-primary btn-lg">💾 <?= __('gen.save') ?></button>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../../layout/topbar.php'; ?>
