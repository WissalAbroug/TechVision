<?php $pageTitle = __('formation.title'); require __DIR__ . '/../layout/header.php'; ?>

<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container">
  <div class="flex-between mb-4 animate-fade-in-up">
    <div>
      <h1 style="font-size:1.5rem">📚 <?= __('formation.title') ?></h1>
      <p><?= $lang==='fr'?'Formations recommandées et catalogue complet':'Recommended trainings and full catalog' ?></p>
    </div>
  </div>

  <!-- Enrolled -->
  <?php if (!empty($enrolled)): ?>
  <div class="mb-4 animate-fade-in-up delay-100">
    <h2 style="font-size:1.1rem;margin-bottom:1rem">📖 <?= $lang==='fr'?'Mes formations en cours':'My ongoing trainings' ?></h2>
    <div class="grid grid-3" style="gap:1rem">
      <?php foreach ($enrolled as $f): ?>
      <div class="card hover-lift" style="border-top:3px solid var(--purple)">
        <div class="flex-between mb-2">
          <span class="badge badge-<?= $f['gratuit']?'success':'primary' ?>"><?= $f['gratuit']?__('formation.free'):__('formation.paid') ?></span>
          <span class="text-xs text-muted"><?= e($f['duree']) ?></span>
        </div>
        <h4 style="margin-bottom:.5rem;font-size:.95rem"><?= e($f['titre']) ?></h4>
        <div class="text-xs text-muted mb-2">🌐 <?= e($f['plateforme']) ?></div>
        <div style="margin-bottom:.5rem">
          <div class="flex-between text-xs mb-1">
            <span><?= __('formation.progress') ?></span>
            <span><?= $f['progression'] ?>%</span>
          </div>
          <div class="progress-bar"><div class="progress-fill <?= $f['progression']>=100?'green':'' ?>" data-width="<?= $f['progression'] ?>" style="width:0"></div></div>
        </div>
        <div class="flex gap-1 mt-2">
          <?php if ($f['url']): ?><a href="<?= e($f['url']) ?>" target="_blank" class="btn btn-primary btn-sm w-full"><?= __('formation.start') ?> ↗</a><?php endif; ?>
        </div>
        <!-- Progress Update -->
        <form method="POST" action="<?= APP_URL ?>/?ctrl=formation&action=updateProgress" class="mt-2 flex gap-1">
          <?= csrf_field() ?>
          <input type="hidden" name="formation_id" value="<?= $f['formation_id'] ?>">
          <input type="number" name="progression" value="<?= $f['progression'] ?>" min="0" max="100" class="form-control form-control" style="width:70px;padding:.35rem .5rem;font-size:.8rem">
          <button type="submit" class="btn btn-ghost btn-sm">✓</button>
        </form>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Catalog -->
  <div class="animate-fade-in-up delay-200">
    <h2 style="font-size:1.1rem;margin-bottom:1rem">🎓 <?= __('formation.catalog') ?></h2>
    <div class="grid grid-4" style="gap:1rem">
      <?php foreach ($all as $f):
        $isEnrolled = in_array($f['id'], $enrolledIds);
        $skills = json_decode($f['competences_cibles']??'[]', true);
      ?>
      <div class="card hover-lift animate-on-scroll" style="position:relative">
        <div class="flex-between mb-2" style="align-items:flex-start">
          <div style="display:flex;gap:.25rem;flex-wrap:wrap">
            <span class="badge badge-<?= ['debutant'=>'success','intermediaire'=>'primary','expert'=>'purple'][$f['niveau']] ?>"><?= ucfirst($f['niveau']) ?></span>
            <?php if ($isEnrolled): ?><span class="badge badge-success">✓ Inscrit</span><?php endif; ?>
          </div>
          <span class="badge badge-<?= $f['gratuit']?'success':'gray' ?>"><?= $f['gratuit']?__('formation.free'):__('formation.paid') ?></span>
        </div>
        <h4 style="margin-bottom:.4rem;font-size:.9rem"><?= e($f['titre']) ?></h4>
        <div class="text-xs text-muted mb-2">🌐 <?= e($f['plateforme']) ?> · ⏱ <?= e($f['duree']) ?></div>
        <div class="flex flex-wrap gap-1 mb-3">
          <?php foreach(array_slice($skills,0,3) as $s): ?><span class="skill-tag" style="font-size:.7rem;padding:.15rem .5rem"><?= e($s) ?></span><?php endforeach; ?>
          <?php if(count($skills)>3): ?><span class="text-xs text-muted">+<?= count($skills)-3 ?></span><?php endif; ?>
        </div>
        <?php if (!$isEnrolled): ?>
        <form method="POST" action="<?= APP_URL ?>/?ctrl=formation&action=enroll">
          <?= csrf_field() ?>
          <input type="hidden" name="formation_id" value="<?= $f['id'] ?>">
          <button type="submit" class="btn btn-primary btn-sm w-full"><?= __('formation.start') ?></button>
        </form>
        <?php else: ?>
          <?php if ($f['url']): ?><a href="<?= e($f['url']) ?>" target="_blank" class="btn btn-ghost btn-sm w-full"><?= __('formation.start') ?> ↗</a><?php endif; ?>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
