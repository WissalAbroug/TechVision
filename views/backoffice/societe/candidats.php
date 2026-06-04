<?php $pageTitle = __('societe.candidates'); require __DIR__ . '/../layout/sidebar.php'; ?>

<div class="flex-between mb-4 animate-fade-in-up">
  <h2>👥 <?= __('societe.candidates') ?></h2>
  <form method="GET" action="<?= APP_URL ?>/?ctrl=societe&action=candidats" class="flex gap-2">
    <input type="hidden" name="ctrl" value="societe"><input type="hidden" name="action" value="candidats">
    <select name="offre_id" class="form-control" style="min-width:200px" onchange="this.form.submit()">
      <option value=""><?= $lang==='fr'?'Sélectionner une offre':'Select an offer' ?></option>
      <?php foreach ($offres as $o): ?><option value="<?= $o['id'] ?>" <?= ($_GET['offre_id']??0)==$o['id']?'selected':'' ?>><?= e($o['titre']) ?></option><?php endforeach; ?>
    </select>
  </form>
</div>

<?php if (empty($candidatures)): ?>
  <div class="empty-state card"><div class="icon">📭</div><h3><?= $lang==='fr'?'Sélectionnez une offre pour voir les candidats':'Select an offer to see candidates' ?></h3></div>
<?php else: ?>
<div class="table-wrap animate-fade-in-up delay-100">
  <table>
    <thead>
      <tr><th>Candidat</th><th>Score matching</th><th><?= __('cand.missing') ?></th><th>Statut</th><th><?= __('gen.created_at') ?></th><th><?= __('gen.actions') ?></th></tr>
    </thead>
    <tbody>
      <?php foreach ($candidatures as $c):
        $score = (float)$c['score_matching'];
        $scoreClass = $score>=70?'success':($score>=40?'warning':'danger');
        $missing = json_decode($c['competences_manquantes']??'[]', true);
      ?>
      <tr>
        <td>
          <div class="flex items-center gap-2">
            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--purple));color:#fff;font-weight:700;font-size:.78rem;display:flex;align-items:center;justify-content:center;flex-shrink:0"><?= strtoupper(substr($c['prenom'],0,1).substr($c['nom'],0,1)) ?></div>
            <div>
              <div style="font-weight:600;font-size:.875rem"><?= e($c['prenom'].' '.$c['nom']) ?></div>
              <div class="text-xs text-muted"><?= e($c['email']) ?></div>
              <?php if ($c['profil_titre']): ?><div class="text-xs text-muted"><?= e($c['profil_titre']) ?></div><?php endif; ?>
            </div>
          </div>
        </td>
        <td>
          <div class="flex items-center gap-2">
            <span style="font-weight:700;color:var(--<?= $scoreClass ?>)"><?= $score ?>%</span>
            <div class="progress-bar" style="width:60px"><div class="progress-fill <?= $scoreClass==='success'?'green':($scoreClass==='warning'?'orange':'red') ?>" data-width="<?= $score ?>" style="width:0"></div></div>
          </div>
        </td>
        <td>
          <div class="flex flex-wrap gap-1">
            <?php foreach(array_slice($missing,0,3) as $m): ?><span class="skill-tag missing" style="font-size:.7rem"><?= e($m) ?></span><?php endforeach; ?>
            <?php if(count($missing)>3): ?><span class="text-xs text-muted">+<?= count($missing)-3 ?></span><?php endif; ?>
          </div>
        </td>
        <td>
          <form method="POST" action="<?= APP_URL ?>/?ctrl=societe&action=updateCandidature" class="flex gap-1 items-center">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $c['id'] ?>"><input type="hidden" name="offre_id" value="<?= $_GET['offre_id']??0 ?>">
            <select name="statut" class="form-control" style="height:30px;padding:.2rem .5rem;font-size:.8rem" onchange="this.form.submit()">
              <?php foreach(['envoyee','vue','shortlistee','refusee','acceptee'] as $s): ?><option value="<?= $s ?>" <?= $s===$c['statut']?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
            </select>
          </form>
        </td>
        <td class="text-sm text-muted"><?= date('d/m/Y', strtotime($c['date_candidature'])) ?></td>
        <td>
          <div class="flex gap-1">
            <?php if ($c['cv_id']): ?><a href="<?= APP_URL ?>/?ctrl=cv&action=show" class="btn btn-ghost btn-sm" title="CV">📄</a><?php endif; ?>
            <a href="<?= APP_URL ?>/?ctrl=entretien&action=create&candidature_id=<?= $c['id'] ?>" class="btn btn-purple btn-sm">🎤</a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
