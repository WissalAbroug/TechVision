<?php $pageTitle = __('societe.dashboard'); require __DIR__ . '/../layout/sidebar.php'; ?>

<!-- KPIs -->
<div class="kpi-grid animate-fade-in-up" style="grid-template-columns:repeat(4,1fr)">
  <div class="kpi-card blue">
    <div class="kpi-icon">💼</div>
    <div class="kpi-value" data-count="<?= $stats['offres'] ?>"><?= $stats['offres'] ?></div>
    <div class="kpi-label"><?= __('societe.my_offers') ?></div>
  </div>
  <div class="kpi-card purple">
    <div class="kpi-icon">📨</div>
    <div class="kpi-value" data-count="<?= $stats['candidatures'] ?>"><?= $stats['candidatures'] ?></div>
    <div class="kpi-label"><?= __('admin.total_apps') ?></div>
  </div>
  <div class="kpi-card green">
    <div class="kpi-icon">🎤</div>
    <div class="kpi-value" data-count="<?= $stats['entretiens'] ?>"><?= $stats['entretiens'] ?></div>
    <div class="kpi-label"><?= __('societe.interviews') ?></div>
  </div>
  <div class="kpi-card orange">
    <div class="kpi-icon">🎫</div>
    <div class="kpi-value" data-count="<?= $stats['reclamations'] ?>"><?= $stats['reclamations'] ?></div>
    <div class="kpi-label"><?= __('societe.complaints') ?></div>
  </div>
</div>

<!-- Offres -->
<div class="card animate-fade-in-up delay-100 mt-4">
  <div class="dash-section-title">
    💼 <?= __('societe.my_offers') ?>
    <a href="<?= APP_URL ?>/?ctrl=societe&action=createOffre" class="btn btn-primary btn-sm">+ <?= __('offre.create') ?></a>
  </div>
  <?php if (empty($offres)): ?>
    <div class="empty-state"><div class="icon">💼</div><p><?= $lang==='fr'?'Aucune offre publiée':'No offers published' ?></p><a href="<?= APP_URL ?>/?ctrl=societe&action=createOffre" class="btn btn-primary btn-sm mt-2"><?= __('offre.create') ?></a></div>
  <?php else: ?>
  <div class="table-wrap mt-2">
    <table>
      <thead><tr><th>Titre</th><th>Métier</th><th>Contrat</th><th>Candidatures</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach (array_slice($offres,0,5) as $o): ?>
        <tr>
          <td><a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $o['id'] ?>" class="text-primary font-semibold"><?= e($o['titre']) ?></a></td>
          <td class="text-sm text-muted"><?= e($o['metier_nom']??'—') ?></td>
          <td><span class="badge badge-primary"><?= e($o['type_contrat']) ?></span></td>
          <td><a href="<?= APP_URL ?>/?ctrl=societe&action=candidats&offre_id=<?= $o['id'] ?>" class="badge badge-purple"><?= $o['nb_candidatures'] ?></a></td>
          <td><span class="status-pill status-<?= $o['statut'] ?>"><?= ucfirst($o['statut']) ?></span></td>
          <td><div class="flex gap-1">
            <a href="<?= APP_URL ?>/?ctrl=societe&action=editOffre&id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm">✏️</a>
            <form method="POST" action="<?= APP_URL ?>/?ctrl=societe&action=deleteOffre" data-confirm-form="<?= __('gen.confirm_del') ?>">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $o['id'] ?>">
              <button class="btn btn-danger btn-sm">🗑</button>
            </form>
          </div></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <a href="<?= APP_URL ?>/?ctrl=societe&action=offres" class="btn btn-ghost btn-sm mt-2"><?= __('gen.view') ?> <?= $lang==='fr'?'toutes les offres':'all offers' ?> →</a>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
