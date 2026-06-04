<?php $pageTitle = __('admin.offers'); require __DIR__ . '/../layout/sidebar.php'; ?>
<div class="flex-between mb-4 animate-fade-in-up">
  <h2>💼 <?= __('admin.offers') ?></h2>
  <a href="<?= APP_URL ?>/?ctrl=admin&action=adminCreateOffre" class="btn btn-primary btn-sm">+ <?= $lang==='fr'?'Ajouter':'Add' ?></a>
</div>
<div class="table-wrap animate-fade-in-up delay-100">
  <table>
    <thead><tr><th>Titre</th><th>Société</th><th>Type</th><th>Statut</th><th><?= __('gen.created_at') ?></th><th><?= __('gen.actions') ?></th></tr></thead>
    <tbody>
      <?php foreach (array_merge($offres, $offresInactive) as $o): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $o['id'] ?>" class="text-primary font-semibold"><?= e($o['titre']) ?></a></td>
        <td class="text-sm"><?= e($o['nom_societe']??$o['soc_nom']) ?></td>
        <td><span class="badge badge-primary"><?= e($o['type_contrat']) ?></span></td>
        <td><span class="status-pill status-<?= $o['statut'] ?>"><?= ucfirst($o['statut']) ?></span></td>
        <td class="text-sm text-muted"><?= date('d/m/Y', strtotime($o['created_at'])) ?></td>
        <td>
          <div class="flex gap-1">
            <a href="<?= APP_URL ?>/?ctrl=offre&action=show&id=<?= $o['id'] ?>" class="btn btn-success btn-sm" title="View" target="_blank">👁️</a>
            <a href="<?= APP_URL ?>/?ctrl=admin&action=adminEditOffre&id=<?= $o['id'] ?>" class="btn btn-primary btn-sm" title="Edit">✏️</a>
            <form method="POST" action="<?= APP_URL ?>/?ctrl=admin&action=adminDeleteOffre" data-confirm-form="<?= __('gen.confirm_del') ?>" style="display:inline">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $o['id'] ?>">
              <button type="submit" class="btn btn-danger btn-sm" title="Delete">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($offres) && empty($offresInactive)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)"><?= __('gen.no_data') ?></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../layout/topbar.php'; ?>
