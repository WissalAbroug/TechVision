<?php $pageTitle = __('entretien.result'); require __DIR__ . '/../layout/header.php'; ?>

<div style="background:transparent;min-height:calc(100vh - 68px);padding:3rem 0">
<div class="container-sm">
  <?php $score = (float)$entretien['score_simulation']; $feedback = $entretien['feedback']; ?>

  <!-- Score Card -->
  <div class="card animate-scale-in" style="text-align:center;padding:3rem 2rem;margin-bottom:2rem;background:linear-gradient(135deg,<?= $score>=70?'var(--success-50),var(--primary-50)':($score>=40?'var(--warning-50),var(--primary-50)':'var(--danger-50),var(--primary-50)') ?>)">
    <div style="font-size:3rem;margin-bottom:1rem"><?= $score>=70?'🏆':($score>=40?'👍':'📚') ?></div>
    <div style="font-size:4rem;font-weight:900;color:var(--<?= $score>=70?'success':($score>=40?'warning':'danger') ?>)"><?= $score ?>%</div>
    <div style="font-size:1.1rem;font-weight:600;margin-top:.5rem"><?= __('entretien.score') ?></div>
    <div style="max-width:420px;margin:1.5rem auto 0">
      <div class="progress-bar" style="height:12px">
        <div class="progress-fill <?= $score>=70?'green':($score>=40?'orange':'red') ?>" data-width="<?= $score ?>" style="width:0"></div>
      </div>
    </div>
  </div>

  <!-- Feedback -->
  <div class="card animate-fade-in-up delay-200 mb-3" style="border-left:4px solid var(--<?= $score>=70?'success':($score>=40?'warning':'primary') ?>)">
    <h3 style="margin-bottom:.75rem">💬 <?= __('entretien.feedback') ?></h3>
    <p style="line-height:1.8;font-size:1rem"><?= e($feedback) ?></p>
  </div>

  <!-- Performance levels -->
  <div class="card animate-fade-in-up delay-300 mb-3">
    <h3 style="margin-bottom:1.25rem">📊 <?= $lang==='fr'?'Analyse de performance':'Performance analysis' ?></h3>
    <?php
      $levels = [
        ['label' => $lang==='fr'?'Excellente maîtrise':'Excellent mastery', 'range' => '85-100%', 'min'=>85, 'color'=>'success'],
        ['label' => $lang==='fr'?'Bon niveau':'Good level',  'range' => '65-84%', 'min'=>65, 'color'=>'success'],
        ['label' => $lang==='fr'?'Niveau moyen':'Average level','range' => '45-64%', 'min'=>45, 'color'=>'warning'],
        ['label' => $lang==='fr'?'À améliorer':'Needs improvement','range' => '0-44%', 'min'=>0, 'color'=>'danger'],
      ];
      foreach ($levels as $lv):
        $active = $score >= $lv['min'];
        $isCurrent = ($lv === $levels[0] && $score >= 85) || ($lv === $levels[1] && $score >= 65 && $score < 85) || ($lv === $levels[2] && $score >= 45 && $score < 65) || ($lv === $levels[3] && $score < 45);
    ?>
    <div style="display:flex;align-items:center;gap:1rem;padding:.6rem 0;border-bottom:1px solid var(--border)">
      <span style="font-size:1.1rem"><?= $isCurrent ? '👉' : ($active ? '✅' : '⬜') ?></span>
      <div style="flex:1"><span style="font-weight:<?= $isCurrent?'700':'400' ?>"><?= $lv['label'] ?></span></div>
      <span class="badge badge-<?= $lv['color'] ?>"><?= $lv['range'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Next Steps -->
  <div class="card animate-fade-in-up delay-400" style="background:var(--primary-50)">
    <h3 style="margin-bottom:1rem;color:var(--primary)">🚀 <?= $lang==='fr'?'Prochaines étapes':'Next steps' ?></h3>
    <div class="flex gap-2 flex-wrap">
      <a href="<?= APP_URL ?>/?ctrl=formation&action=index" class="btn btn-purple">📚 <?= __('dash.suggested') ?></a>
      <a href="<?= APP_URL ?>/?ctrl=entretien&action=index" class="btn btn-outline">← <?= __('entretien.title') ?></a>
      <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="btn btn-ghost">🔍 <?= __('offre.title') ?></a>
    </div>
  </div>
</div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
