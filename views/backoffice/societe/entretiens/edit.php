<?php $pageTitle = __('entretien.schedule'); require __DIR__ . '/../../layout/sidebar.php'; ?>

<div style="max-width:600px" class="animate-fade-in-up">
  <a href="<?= APP_URL ?>/?ctrl=entretien&action=societeIndex" class="btn btn-ghost btn-sm mb-3">← <?= __('societe.interviews') ?></a>
  <div class="card">
    <h2 class="mb-4">✏️ <?= $lang==='fr'?'Modifier l\'entretien':'Edit interview' ?></h2>
    <div style="padding:.75rem 1rem;background:var(--primary-50);border-radius:var(--radius-sm);margin-bottom:1.5rem">
      <div style="font-weight:600"><?= e($cand['prenom'].' '.$cand['nom']) ?></div>
      <div class="text-sm text-muted"><?= e($cand['offre_titre']??'') ?></div>
    </div>
    <form method="POST" action="<?= APP_URL ?>/?ctrl=entretien&action=update" novalidate id="entretienForm">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $entretien['id'] ?>">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= __('entretien.date') ?> <span class="req">*</span></label>
          <input type="datetime-local" name="date_entretien" class="form-control" id="dateEntretien" value="<?= date('Y-m-d\TH:i', strtotime($entretien['date_entretien'])) ?>">
          <div class="form-error" id="dateErr"></div>
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('entretien.type') ?> <span class="req">*</span></label>
          <select name="type" class="form-control" id="typeSelect" onchange="toggleFields()">
            <option value="visio" <?= $entretien['type']==='visio'?'selected':'' ?>><?= __('entretien.visio') ?></option>
            <option value="presentiel" <?= $entretien['type']==='presentiel'?'selected':'' ?>><?= __('entretien.presentiel') ?></option>
            <option value="telephonique" <?= $entretien['type']==='telephonique'?'selected':'' ?>><?= __('entretien.phone') ?></option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Statut</label>
        <select name="statut" class="form-control">
            <option value="planifie" <?= $entretien['statut']==='planifie'?'selected':'' ?>>Planifié</option>
            <option value="termine" <?= $entretien['statut']==='termine'?'selected':'' ?>>Terminé</option>
            <option value="annule" <?= $entretien['statut']==='annule'?'selected':'' ?>>Annulé</option>
        </select>
      </div>
      <div class="form-group" id="visioField">
        <label class="form-label">🔗 Lien de visioconférence</label>
        <input type="text" name="lien_visio" class="form-control" value="<?= e($entretien['lien_visio']) ?>" placeholder="https://meet.google.com/...">
      </div>
      <div class="form-group" id="adresseField" style="display:none">
        <label class="form-label">📍 Adresse</label>
        <input type="text" name="adresse_entretien" class="form-control" value="<?= e($entretien['adresse_entretien']) ?>" placeholder="Adresse complète du bureau...">
      </div>
      <div class="form-group">
        <label class="form-label">📝 Notes pour le candidat</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="<?= $lang==='fr'?'Instructions, documents à apporter...':'Instructions, documents to bring...' ?>"><?= e($entretien['notes']) ?></textarea>
      </div>
      <div class="flex gap-2 justify-end">
        <a href="<?= APP_URL ?>/?ctrl=entretien&action=societeIndex" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
        <button type="submit" class="btn btn-primary btn-lg">💾 <?= $lang==='fr'?'Enregistrer':'Save' ?></button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleFields() {
  const t = document.getElementById('typeSelect').value;
  document.getElementById('visioField').style.display = t==='visio'?'':'none';
  document.getElementById('adresseField').style.display = t==='presentiel'?'':'none';
}
toggleFields(); // Init
document.getElementById('entretienForm').addEventListener('submit', function(e){
  if (!document.getElementById('dateEntretien').value){
    document.getElementById('dateErr').textContent='<?= __('gen.required') ?>';
    e.preventDefault();
  }
});
</script>

<?php require __DIR__ . '/../../layout/topbar.php'; ?>
