<?php $pageTitle = __('recl.title'); require __DIR__ . '/../layout/header.php'; ?>
<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container">
  <div class="flex-between mb-4">
    <h1 style="font-size:1.5rem">🎫 <?= __('recl.title') ?></h1>
    <a href="<?= APP_URL ?>/?ctrl=reclamation&action=create" class="btn btn-primary btn-sm">+ <?= __('recl.new') ?></a>
  </div>
  <?php if (empty($reclamations)): ?>
    <div class="empty-state card"><div class="icon">🎫</div><h3><?= __('gen.no_data') ?></h3></div>
  <?php else: ?>
  <div style="display:flex;flex-direction:column;gap:1rem">
    <?php foreach ($reclamations as $r): ?>
    <div class="card animate-on-scroll">
      <div class="flex-between flex-wrap gap-2">
        <div style="flex:1">
          <div class="flex items-center gap-2 mb-1 flex-wrap">
            <h3 style="font-size:.95rem"><a href="<?= APP_URL ?>/?ctrl=reclamation&action=show&id=<?= $r['id'] ?>" class="text-primary"><?= e($r['titre']) ?></a></h3>
            
            <?php if (hasRole('admin')): ?>
            <form method="POST" action="<?= APP_URL ?>/?ctrl=reclamation&action=updateStatut" style="display:inline">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
              <select name="statut" class="form-control" style="height:26px;padding:0 .5rem;font-size:.75rem;border-radius:1rem;background-color:var(--bg-card-sec);border-color:var(--border)" onchange="this.form.submit()">
                <?php foreach(['ouvert','en_cours','resolu','ferme'] as $s): ?>
                  <option value="<?= $s ?>" <?= $s===$r['statut']?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
            <?php else: ?>
            <span class="status-pill status-<?= e($r['statut']) ?>"><?= __('recl.'.str_replace('_','_',$r['statut'])) ?></span>
            <?php endif; ?>

            <?php $prColors=['basse'=>'gray','normale'=>'primary','haute'=>'warning','urgente'=>'danger']; ?>
            <span class="badge badge-<?= $prColors[$r['priorite']]??'gray' ?>"><?= ucfirst($r['priorite']) ?></span>
          </div>
          <p style="font-size:.875rem"><?= e(substr($r['description'],0,120)) ?>...</p>
          <div class="text-xs text-muted mt-1">📅 <?= date('d/m/Y H:i', strtotime($r['created_at'])) ?><?php if (isset($r['nom'])): ?> · 👤 <?= e($r['prenom'].' '.$r['nom']) ?><?php endif; ?></div>
        </div>
        <div class="flex gap-2 items-center">
          <a href="<?= APP_URL ?>/?ctrl=reclamation&action=show&id=<?= $r['id'] ?>" class="btn btn-ghost btn-sm"><?= __('gen.view') ?> →</a>
          <?php if (hasRole('admin')): ?>
            <a href="<?= APP_URL ?>/?ctrl=reclamation&action=edit&id=<?= $r['id'] ?>" class="btn btn-primary btn-sm">✏️</a>
            <form method="POST" action="<?= APP_URL ?>/?ctrl=reclamation&action=delete" data-confirm-form="<?= __('gen.confirm_del') ?>">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
              <button class="btn btn-danger btn-sm">🗑</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
