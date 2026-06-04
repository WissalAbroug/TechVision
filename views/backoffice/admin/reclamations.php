<?php $pageTitle = __('admin.complaints'); require __DIR__ . '/../layout/sidebar.php'; ?>
<div class="flex-between mb-4 animate-fade-in-up"><h2>🎫 <?= __('admin.complaints') ?></h2></div>
<div class="table-wrap animate-fade-in-up delay-100">
  <table>
    <thead><tr><th>Sujet</th><th>Utilisateur</th><th>Priorité</th><th>Statut</th><th>Date</th><th><?= __('gen.actions') ?></th></tr></thead>
    <tbody>
      <?php foreach ($reclamations as $r): ?>
      <tr>
        <td><a href="<?= APP_URL ?>/?ctrl=reclamation&action=show&id=<?= $r['id'] ?>" class="text-primary font-semibold"><?= e($r['titre']) ?></a></td>
        <td class="text-sm"><?= e($r['prenom'].' '.$r['nom']) ?><br><span class="text-xs text-muted"><?= e($r['email']) ?></span></td>
        <td><?php $c=['basse'=>'gray','normale'=>'primary','haute'=>'warning','urgente'=>'danger']; ?><span class="badge badge-<?= $c[$r['priorite']]??'gray' ?>"><?= ucfirst($r['priorite']) ?></span></td>
        <td><span class="status-pill status-<?= $r['statut'] ?>"><?= $r['statut'] ?></span></td>
        <td class="text-sm text-muted"><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
        <td>
          <div class="flex gap-1">
            <a href="<?= APP_URL ?>/?ctrl=reclamation&action=show&id=<?= $r['id'] ?>" class="btn btn-success btn-sm" title="View">👁️</a>
            <form method="POST" action="<?= APP_URL ?>/?ctrl=reclamation&action=delete" data-confirm-form="<?= __('gen.confirm_del') ?>">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-danger btn-sm">🗑</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../layout/topbar.php'; ?>
