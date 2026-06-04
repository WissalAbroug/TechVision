<?php $pageTitle = __('admin.users'); require __DIR__ . '/../layout/sidebar.php'; ?>

<div class="flex-between mb-4 animate-fade-in-up">
  <h2>👥 <?= __('admin.users') ?> <span class="badge badge-gray"><?= count($users) ?></span></h2>
  <div class="flex gap-2 items-center">
    <div class="flex gap-1">
      <?php foreach ([''=>'Tous','candidat'=>'Candidats','societe'=>'Sociétés','admin'=>'Admins'] as $r=>$l): ?>
      <a href="<?= APP_URL ?>/?ctrl=admin&action=users<?= $r?'&role='.$r:'' ?>" class="btn btn-sm <?= ($_GET['role']??'')===$r?'btn-primary':'btn-ghost' ?>"><?= $l ?></a>
      <?php endforeach; ?>
    </div>
    <a href="<?= APP_URL ?>/?ctrl=admin&action=adminCreateUser" class="btn btn-primary btn-sm">+ <?= $lang==='fr'?'Ajouter':'Add' ?></a>
  </div>
</div>

<div class="table-wrap animate-fade-in-up delay-100">
  <table>
    <thead>
      <tr>
        <th>#</th><th><?= $lang==='fr'?'Utilisateur':'User' ?></th><th>Email</th><th>Rôle</th><th>Statut</th><th><?= __('gen.created_at') ?></th><th><?= __('gen.actions') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
      <tr>
        <td class="text-muted text-sm"><?= $u['id'] ?></td>
        <td>
          <div class="flex items-center gap-2">
            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--purple));color:#fff;font-size:.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0"><?= strtoupper(substr($u['prenom'],0,1).substr($u['nom'],0,1)) ?></div>
            <div>
              <div style="font-weight:600;font-size:.875rem"><?= e($u['prenom'].' '.$u['nom']) ?></div>
              <?php if ($u['nom_societe']): ?><div class="text-xs text-muted"><?= e($u['nom_societe']) ?></div><?php endif; ?>
            </div>
          </div>
        </td>
        <td class="text-sm"><?= e($u['email']) ?></td>
        <td><span class="badge badge-<?= $u['role']==='admin'?'danger':($u['role']==='societe'?'purple':'primary') ?>"><?= ucfirst($u['role']) ?></span></td>
        <td><span class="badge badge-<?= $u['statut']==='actif'?'success':'warning' ?>"><?= ucfirst($u['statut']) ?></span></td>
        <td class="text-sm text-muted"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
        <td>
          <div class="flex gap-1">
            <?php if ($u['id'] !== auth()['id']): ?>
            <a href="<?= APP_URL ?>/?ctrl=admin&action=adminShowUser&id=<?= $u['id'] ?>" class="btn btn-success btn-sm" title="View">👁️</a>
            <a href="<?= APP_URL ?>/?ctrl=admin&action=adminEditUser&id=<?= $u['id'] ?>" class="btn btn-primary btn-sm" title="Edit">✏️</a>
            <form method="POST" action="<?= APP_URL ?>/?ctrl=admin&action=toggleUser" style="display:inline">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $u['id'] ?>">
              <button type="submit" class="btn <?= $u['statut']==='actif'?'btn-danger':'btn-success' ?> btn-sm" title="Toggle"><?= $u['statut']==='actif'?'🔒':'🔓' ?></button>
            </form>
            <?php else: ?>
            <a href="<?= APP_URL ?>/?ctrl=admin&action=adminShowUser&id=<?= $u['id'] ?>" class="btn btn-success btn-sm" title="View">👁️</a>
            <a href="<?= APP_URL ?>/?ctrl=admin&action=adminEditUser&id=<?= $u['id'] ?>" class="btn btn-primary btn-sm" title="Edit">✏️</a>
            <span class="text-xs text-muted" style="align-self:center;margin-left:4px">(vous)</span>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($users)): ?><tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-muted)"><?= __('gen.no_data') ?></td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
