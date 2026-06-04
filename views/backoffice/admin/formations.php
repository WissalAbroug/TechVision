<?php $pageTitle = __('admin.trainings'); require __DIR__ . '/../layout/sidebar.php'; ?>
<div class="flex-between mb-4 animate-fade-in-up">
  <h2>📚 <?= __('admin.trainings') ?></h2>
  <a href="<?= APP_URL ?>/?ctrl=formation&action=adminCreate" class="btn btn-primary btn-sm">+ <?= $lang==='fr'?'Ajouter':'Add' ?></a>
</div>
<div class="grid grid-3 animate-fade-in-up delay-100" style="gap:1rem">
  <?php foreach ($formations as $f): $skills=json_decode($f['competences_cibles']??'[]',true); ?>
  <div class="card hover-lift">
    <div class="flex-between mb-2">
      <span class="badge badge-<?= $f['gratuit']?'success':'primary' ?>"><?= $f['gratuit']?__('formation.free'):__('formation.paid') ?></span>
      <span class="text-xs text-muted"><?= e($f['duree']) ?></span>
    </div>
    <h4 style="font-size:.9rem;margin-bottom:.35rem"><?= e($f['titre']) ?></h4>
    <div class="text-xs text-muted mb-2">🌐 <?= e($f['plateforme']) ?></div>
    <div class="flex flex-wrap gap-1 mb-3">
      <?php foreach(array_slice($skills,0,3) as $s): ?><span class="skill-tag" style="font-size:.7rem;padding:.15rem .5rem"><?= e($s) ?></span><?php endforeach; ?>
    </div>
    <div class="flex gap-1 mt-auto">
      <a href="<?= e($f['url']) ?>" class="btn btn-success btn-sm" style="flex:1; text-align:center;" target="_blank" title="View">👁️</a>
      <a href="<?= APP_URL ?>/?ctrl=formation&action=adminEdit&id=<?= $f['id'] ?>" class="btn btn-primary btn-sm" style="flex:1; text-align:center;" title="Edit">✏️</a>
      <form method="POST" action="<?= APP_URL ?>/?ctrl=formation&action=adminDelete" data-confirm-form="<?= __('gen.confirm_del') ?>" style="flex:1; display:inline;">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $f['id'] ?>">
        <button class="btn btn-danger btn-sm w-full" style="padding:.4rem;" title="Delete">🗑</button>
      </form>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../layout/topbar.php'; ?>
