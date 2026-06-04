<?php $pageTitle = __('societe.my_offers'); require __DIR__ . '/../../layout/sidebar.php'; ?>

<div class="flex-between mb-4 animate-fade-in-up">
  <h2>💼 <?= __('societe.my_offers') ?> <span class="badge badge-gray"><?= count($offres) ?></span></h2>
  <a href="<?= APP_URL ?>/?ctrl=societe&action=createOffre" class="btn btn-primary">+ <?= __('offre.create') ?></a>
</div>

<?php if (empty($offres)): ?>
  <div class="empty-state card"><div class="icon">💼</div><h3><?= $lang==='fr'?'Aucune offre':'No offers yet' ?></h3><a href="<?= APP_URL ?>/?ctrl=societe&action=createOffre" class="btn btn-primary mt-2"><?= __('offre.create') ?></a></div>
<?php else: ?>
<div class="table-wrap animate-fade-in-up delay-100">
  <table>
    <thead><tr><th>Titre</th><th>Métier</th><th>Contrat</th><th>Localisation</th><th>Candidatures</th><th>Statut</th><th><?= __('gen.created_at') ?></th><th><?= __('gen.actions') ?></th></tr></thead>
    <tbody>
      <?php foreach ($offres as $o): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $o['id'] ?>" class="text-primary font-semibold"><?= e($o['titre']) ?></a></td>
        <td class="text-sm text-muted"><?= e($o['metier_nom']??'—') ?></td>
        <td><span class="badge badge-primary"><?= e($o['type_contrat']) ?></span></td>
        <td class="text-sm"><?= e($o['localisation']??'—') ?></td>
        <td><a href="<?= APP_URL ?>/?ctrl=societe&action=candidats&offre_id=<?= $o['id'] ?>" class="badge badge-purple"><?= $o['nb_candidatures'] ?> 👤</a></td>
        <td><span class="status-pill status-<?= $o['statut'] ?>"><?= ucfirst($o['statut']) ?></span></td>
        <td class="text-sm text-muted"><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
        <td><div class="flex gap-1">
          <a href="<?= APP_URL ?>/?ctrl=societe&action=editOffre&id=<?= $o['id'] ?>" class="btn btn-outline btn-sm">✏️</a>
          <a href="<?= APP_URL ?>/?ctrl=societe&action=candidats&offre_id=<?= $o['id'] ?>" class="btn btn-ghost btn-sm">👥</a>
          <form method="POST" action="<?= APP_URL ?>/?ctrl=societe&action=deleteOffre" data-confirm-form="<?= __('gen.confirm_del') ?>" style="display:inline">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $o['id'] ?>">
            <button class="btn btn-danger btn-sm">🗑</button>
          </form>
        </div></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../../layout/topbar.php'; ?>
