<?php $pageTitle = __('entretien.schedule'); require __DIR__ . '/../../layout/sidebar.php'; ?>

<div style="max-width:600px" class="animate-fade-in-up">
  <a href="<?= APP_URL ?>/?ctrl=societe&action=candidats" class="btn btn-ghost btn-sm mb-3">← <?= __('societe.candidates') ?></a>
  <div class="card">
    <h2 class="mb-4">🗓️ <?= __('entretien.schedule') ?></h2>
    <div style="padding:.75rem 1rem;background:var(--primary-50);border-radius:var(--radius-sm);margin-bottom:1.5rem">
      <div style="font-weight:600"><?= e($cand['prenom'].' '.$cand['nom']) ?></div>
      <div class="text-sm text-muted"><?= e($cand['offre_titre']??'') ?> · Score: <strong><?= $cand['score_matching'] ?>%</strong></div>
    </div>
    <form method="POST" action="<?= APP_URL ?>/?ctrl=entretien&action=store" novalidate id="entretienForm">
      <?= csrf_field() ?>
      <input type="hidden" name="candidature_id" value="<?= $cand['id'] ?>">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label"><?= __('entretien.date') ?> <span class="req">*</span></label>
          <input type="datetime-local" name="date_entretien" class="form-control" id="dateEntretien" min="<?= date('Y-m-d\TH:i') ?>">
          <div class="form-error" id="dateErr"></div>
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('entretien.type') ?> <span class="req">*</span></label>
          <select name="type" class="form-control" id="typeSelect" onchange="toggleFields()">
            <option value="visio"><?= __('entretien.visio') ?></option>
            <option value="presentiel"><?= __('entretien.presentiel') ?></option>
            <option value="telephonique"><?= __('entretien.phone') ?></option>
          </select>
        </div>
      </div>
      <div class="form-group" id="visioField">
        <label class="form-label">🔗 Lien de visioconférence</label>
        <input type="text" name="lien_visio" class="form-control" placeholder="https://meet.google.com/...">
      </div>
      <div class="form-group" id="adresseField" style="display:none">
        <label class="form-label">📍 Adresse</label>
        <input type="text" name="adresse_entretien" class="form-control" placeholder="Adresse complète du bureau...">
      </div>
      <div class="form-group">
        <label class="form-label">📝 Notes pour le candidat</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="<?= $lang==='fr'?'Instructions, documents à apporter...':'Instructions, documents to bring...' ?>"></textarea>
      </div>
      <div class="flex gap-2 justify-end">
        <a href="<?= APP_URL ?>/?ctrl=societe&action=candidats" class="btn btn-ghost"><?= __('gen.cancel') ?></a>
        <button type="submit" class="btn btn-primary btn-lg">📅 <?= $lang==='fr'?'Planifier':'Schedule' ?></button>
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
document.getElementById('entretienForm').addEventListener('submit', function(e){
  if (!document.getElementById('dateEntretien').value){
    document.getElementById('dateErr').textContent='<?= __('gen.required') ?>';
    e.preventDefault();
  }
});
</script>

<?php require __DIR__ . '/../../layout/topbar.php'; ?>
