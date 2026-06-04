<?php 
$pageTitle = __('societe.interviews'); 
require __DIR__ . '/../layout/sidebar.php'; 
?>

<div class="flex-between mb-4 animate-fade-in-up">
  <h2>🎤 <?= __('societe.interviews') ?> <span class="badge badge-gray"><?= count($entretiens) ?></span></h2>
</div>

<?php if (empty($entretiens)): ?>
  <div class="empty-state card">
    <div class="icon">📅</div>
    <h3><?= $lang==='fr' ? 'Aucun entretien planifié' : 'No interviews scheduled' ?></h3>
  </div>
<?php else: ?>
<div class="table-wrap animate-fade-in-up delay-100">
  <table>
    <thead>
      <tr>
        <th>Candidat</th>
        <th>Offre</th>
        <th>Société</th>
        <th>Date & Heure</th>
        <th>Type</th>
        <th>Score simulation</th>
        <th>Statut</th>
        <th><?= __('gen.actions') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($entretiens as $e): ?>
      <tr>
        <td>
          <div style="font-weight:600;font-size:.875rem"><?= e($e['candidat_prenom'].' '.$e['candidat_nom']) ?></div>
          <div class="text-xs text-muted"><?= e($e['candidat_email']) ?></div>
        </td>
        <td class="text-sm"><?= e($e['offre_titre']) ?></td>
        <td class="text-sm" style="font-weight: 600; color: var(--primary)"><?= e($e['nom_entreprise']) ?></td>
        <td class="text-sm"><?= date('d/m/Y à H:i', strtotime($e['date_entretien'])) ?></td>
        <td><span class="badge badge-primary"><?= __('entretien.'.$e['type']) ?></span></td>
        <td>
          <?php if ($e['score_simulation']): ?>
            <span style="font-weight:700;color:var(--<?= $e['score_simulation']>=70?'success':($e['score_simulation']>=40?'warning':'danger') ?>)"><?= $e['score_simulation'] ?>%</span>
          <?php else: ?>
            <span class="text-muted text-xs">—</span>
          <?php endif; ?>
        </td>
        <td><span class="status-pill status-<?= $e['statut'] ?>"><?= ucfirst($e['statut']) ?></span></td>
        <td>
          <div class="flex gap-1">
            <?php if ($e['lien_visio']): ?>
              <a href="<?= e($e['lien_visio']) ?>" target="_blank" class="btn btn-success btn-sm">📹</a>
            <?php endif; ?>
            <form method="POST" action="<?= APP_URL ?>/?ctrl=entretien&action=delete" data-confirm-form="<?= __('gen.confirm_del') ?>">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $e['id'] ?>">
              <button class="btn btn-danger btn-sm">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
