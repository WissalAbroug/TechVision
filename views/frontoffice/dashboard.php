<?php $pageTitle = __('dash.welcome') . ' ' . auth()['prenom']; require __DIR__ . '/layout/header.php'; ?>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/dashboard.css">

<div style="padding:2rem 0;background:transparent;min-height:calc(100vh - 68px)">
<div class="container">

  <!-- Page Header -->
  <div class="flex-between mb-4 animate-fade-in-up">
    <div>
      <h1 style="font-size:1.75rem"><?= __('dash.welcome') ?> <span class="gradient-text-2"><?= e(auth()['prenom']) ?></span> 👋</h1>
      <p style="margin-top:.25rem"><?= $lang === 'fr' ? 'Voici un aperçu de votre activité' : 'Here\'s an overview of your activity' ?></p>
    </div>
    <div class="flex gap-2">
      <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="btn btn-primary btn-sm">🔍 <?= __('offre.title') ?></a>
      <a href="<?= APP_URL ?>/?ctrl=cv&action=<?= $cv ? 'show' : 'create' ?>" class="btn btn-outline btn-sm">📄 <?= __('nav.my_cv') ?></a>
    </div>
  </div>

  <!-- KPI Row -->
  <div class="kpi-grid animate-fade-in-up delay-100">
    <div class="kpi-card blue">
      <div class="kpi-icon">🎯</div>
      <div class="kpi-value" data-count="<?= $profileScore ?>"><?= $profileScore ?></div>
      <div class="kpi-label"><?= __('dash.profile_score') ?> %</div>
    </div>
    <div class="kpi-card purple">
      <div class="kpi-icon">📨</div>
      <div class="kpi-value" data-count="<?= count($candidatures) ?>"><?= count($candidatures) ?></div>
      <div class="kpi-label"><?= __('dash.my_apps') ?></div>
    </div>
    <div class="kpi-card green">
      <div class="kpi-icon">🎤</div>
      <div class="kpi-value" data-count="<?= count($upcomingIntvw) ?>"><?= count($upcomingIntvw) ?></div>
      <div class="kpi-label"><?= __('dash.interviews') ?></div>
    </div>
    <div class="kpi-card orange">
      <div class="kpi-icon">📚</div>
      <div class="kpi-value" data-count="<?= count($userFormations) ?>"><?= count($userFormations) ?></div>
      <div class="kpi-label"><?= __('dash.suggested') ?></div>
    </div>
  </div>

  <div class="grid grid-3 animate-fade-in-up delay-200" style="grid-template-columns:1fr 2fr">

    <!-- Profile Score Widget -->
    <div>
      <div class="profile-score-widget mb-2">
        <div style="font-size:.8rem;opacity:.7;margin-bottom:.5rem;font-weight:600"><?= __('dash.profile_score') ?></div>
        <div class="psw-score"><?= $profileScore ?>%</div>
        <div class="psw-label"><?= $profileScore < 50 ? ($lang==='fr'?'Profil incomplet':'Incomplete profile') : ($lang==='fr'?'Bon profil !':'Good profile!') ?></div>
        <div class="psw-bar"><div class="psw-fill" style="width:<?= $profileScore ?>%"></div></div>
        <a href="<?= APP_URL ?>/?ctrl=user&action=editProfile" style="display:block;margin-top:1.25rem;text-align:center;background:rgba(255,255,255,.15);padding:.6rem;border-radius:var(--radius-sm);color:#fff;font-size:.85rem;font-weight:600">✏️ <?= $lang==='fr'?'Améliorer mon profil':'Improve my profile' ?></a>
      </div>
      <?php if (!$cv): ?>
      <div class="card" style="border:2px dashed var(--primary);background:var(--primary-50);text-align:center;padding:1.5rem">
        <div style="font-size:2rem;margin-bottom:.5rem">📄</div>
        <h4><?= $lang==='fr'?'Pas encore de CV':'No resume yet' ?></h4>
        <a href="<?= APP_URL ?>/?ctrl=cv&action=create" class="btn btn-primary btn-sm mt-2"><?= __('cv.create') ?></a>
      </div>
      <?php endif; ?>
    </div>

    <!-- Right Column -->
    <div style="display:flex;flex-direction:column;gap:1.5rem">

      <!-- Candidatures récentes -->
      <div class="card">
        <div class="dash-section-title">
          📨 <?= __('dash.my_apps') ?>
          <a href="<?= APP_URL ?>/?ctrl=candidature&action=index" class="btn btn-ghost btn-sm"><?= __('gen.view') ?> →</a>
        </div>
        <?php if (empty($candidatures)): ?>
          <div class="empty-state"><div class="icon">📭</div><p><?= $lang==='fr'?'Aucune candidature envoyée':'No applications sent' ?></p></div>
        <?php else: foreach (array_slice($candidatures,0,4) as $c): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid var(--border)">
          <div>
            <div style="font-weight:600;font-size:.9rem"><?= e($c['offre_titre']) ?></div>
            <div style="font-size:.8rem;color:var(--text-muted)"><?= e($c['nom_societe'] ?: $c['soc_nom']) ?> · <?= e($c['type_contrat']) ?></div>
          </div>
          <div style="display:flex;align-items:center;gap:.75rem">
            <?php
              $score = (float)$c['score_matching'];
              $scoreClass = $score>=70?'success':($score>=40?'warning':'danger');
            ?>
            <span style="font-weight:700;font-size:.85rem;color:var(--<?= $scoreClass ?>)"><?= $score ?>%</span>
            <span class="status-pill status-<?= e($c['statut']) ?>"><?= __('cand.'.$c['statut']) ?></span>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>

      <!-- Entretiens à venir -->
      <?php if (!empty($upcomingIntvw)): ?>
      <div class="card">
        <div class="dash-section-title">🎤 <?= __('dash.interviews') ?></div>
        <?php foreach ($upcomingIntvw as $e): ?>
        <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 0;border-bottom:1px solid var(--border)">
          <div style="width:48px;height:48px;background:var(--primary-100);border-radius:var(--radius);display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0">
            <div style="font-size:.7rem;font-weight:700;color:var(--primary)"><?= date('d', strtotime($e['date_entretien'])) ?></div>
            <div style="font-size:.65rem;color:var(--primary-dark)"><?= strtoupper(date('M', strtotime($e['date_entretien']))) ?></div>
          </div>
          <div style="flex:1">
            <div style="font-weight:600;font-size:.88rem"><?= e($e['offre_titre']) ?></div>
            <div style="font-size:.78rem;color:var(--text-muted)"><?= e($e['nom_societe']) ?> · <?= date('H:i', strtotime($e['date_entretien'])) ?></div>
          </div>
          <a href="<?= APP_URL ?>/?ctrl=entretien&action=simulate&id=<?= $e['id'] ?>" class="btn btn-purple btn-sm">Simuler</a>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Formations suggérées -->
      <?php if (!empty($recommendedFormations)): ?>
      <div class="card">
        <div class="dash-section-title">📚 <?= __('dash.suggested') ?> <a href="<?= APP_URL ?>/?ctrl=formation&action=index" class="btn btn-ghost btn-sm"><?= __('gen.view') ?> →</a></div>
        <div class="grid grid-2" style="gap:.75rem;margin-top:.5rem">
          <?php foreach (array_slice($recommendedFormations,0,4) as $f): ?>
          <div class="card card-sm hover-lift" style="padding:1rem">
            <div class="flex-between mb-1">
              <span class="badge badge-<?= $f['gratuit']?'success':'primary' ?>"><?= $f['gratuit']?__('formation.free'):__('formation.paid') ?></span>
              <span class="text-xs text-muted"><?= e($f['duree']) ?></span>
            </div>
            <div style="font-weight:600;font-size:.85rem;margin-bottom:.25rem"><?= e($f['titre']) ?></div>
            <div style="font-size:.75rem;color:var(--text-muted)"><?= e($f['plateforme']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
