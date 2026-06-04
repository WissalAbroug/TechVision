<?php $pageTitle = __('admin.dashboard'); require __DIR__ . '/../layout/sidebar.php'; ?>

<!-- KPIs -->
<div class="kpi-grid animate-fade-in-up">
  <div class="kpi-card blue">
    <div class="kpi-icon">👥</div>
    <div class="kpi-value" data-count="<?= array_sum($stats['users']) ?>"><?= array_sum($stats['users']) ?></div>
    <div class="kpi-label"><?= __('admin.total_users') ?></div>
  </div>
  <div class="kpi-card purple">
    <div class="kpi-icon">💼</div>
    <div class="kpi-value" data-count="<?= (int)($stats['offres']['total']??0) ?>"><?= (int)($stats['offres']['total']??0) ?></div>
    <div class="kpi-label"><?= __('admin.total_offers') ?></div>
  </div>
  <div class="kpi-card green">
    <div class="kpi-icon">📨</div>
    <div class="kpi-value" data-count="<?= $stats['candidatures'] ?>"><?= $stats['candidatures'] ?></div>
    <div class="kpi-label"><?= __('admin.total_apps') ?></div>
  </div>
  <div class="kpi-card orange">
    <div class="kpi-icon">🎫</div>
    <div class="kpi-value" data-count="<?= array_sum($stats['reclamations']) ?>"><?= array_sum($stats['reclamations']) ?></div>
    <div class="kpi-label"><?= __('admin.complaints') ?></div>
  </div>
</div>

<div class="grid grid-2 animate-fade-in-up delay-100" style="gap:1.5rem;margin-top:1.5rem">

  <!-- User breakdown -->
  <div class="card">
    <div class="dash-section-title">👥 <?= __('admin.users') ?></div>
    <?php foreach ([
      ['label'=>'Candidats','icon'=>'🧑‍💻','key'=>'candidat','color'=>'primary'],
      ['label'=>'Sociétés','icon'=>'🏢','key'=>'societe','color'=>'purple'],
      ['label'=>'Admins','icon'=>'🛡️','key'=>'admin','color'=>'warning'],
    ] as $row): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.7rem 0;border-bottom:1px solid var(--border)">
      <div class="flex items-center gap-2">
        <span><?= $row['icon'] ?></span>
        <span style="font-weight:500"><?= $row['label'] ?></span>
      </div>
      <div class="flex items-center gap-2">
        <span style="font-weight:700;font-size:1.1rem"><?= $stats['users'][$row['key']]??0 ?></span>
        <div class="progress-bar" style="width:80px"><div class="progress-fill" data-width="<?= array_sum($stats['users'])>0?round(($stats['users'][$row['key']]??0)/array_sum($stats['users'])*100):0 ?>" style="width:0;background:var(--<?= $row['color'] ?>)"></div></div>
      </div>
    </div>
    <?php endforeach; ?>
    <a href="<?= APP_URL ?>/?ctrl=admin&action=users" class="btn btn-ghost btn-sm mt-2 w-full"><?= __('gen.view') ?> <?= __('admin.users') ?></a>
  </div>

  <!-- Offre stats -->
  <div class="card">
    <div class="dash-section-title">💼 <?= __('admin.offers') ?></div>
    <?php foreach ([
      ['label'=>'Actives','key'=>'actives','color'=>'success'],
      ['label'=>'Inactives','key'=>'inactives','color'=>'warning'],
      ['label'=>'Clôturées','key'=>'cloturees','color'=>'danger'],
    ] as $row): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.7rem 0;border-bottom:1px solid var(--border)">
      <span style="font-weight:500"><?= $row['label'] ?></span>
      <span class="badge badge-<?= $row['color'] ?>"><?= $stats['offres'][$row['key']]??0 ?></span>
    </div>
    <?php endforeach; ?>
    <a href="<?= APP_URL ?>/?ctrl=admin&action=offres" class="btn btn-ghost btn-sm mt-2 w-full"><?= __('gen.view') ?> <?= __('admin.offers') ?></a>
  </div>

  <!-- Reclamations -->
  <div class="card">
    <div class="dash-section-title">🎫 <?= __('admin.complaints') ?></div>
    <?php foreach ([
      ['label'=>__('recl.open'),'key'=>'ouvert','color'=>'primary'],
      ['label'=>__('recl.in_prog'),'key'=>'en_cours','color'=>'warning'],
      ['label'=>__('recl.resolved'),'key'=>'resolu','color'=>'success'],
    ] as $row): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.6rem 0;border-bottom:1px solid var(--border)">
      <span><?= $row['label'] ?></span>
      <span class="badge badge-<?= $row['color'] ?>"><?= $stats['reclamations'][$row['key']]??0 ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Recent users -->
  <div class="card">
    <div class="dash-section-title">🆕 <?= $lang==='fr'?'Nouveaux inscrits':'New registrations' ?> <a href="<?= APP_URL ?>/?ctrl=admin&action=users" class="btn btn-ghost btn-sm"><?= __('gen.view') ?></a></div>
    <?php foreach ($recentUsers as $u): ?>
    <div style="display:flex;align-items:center;gap:.75rem;padding:.6rem 0;border-bottom:1px solid var(--border)">
      <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--purple));color:#fff;font-size:.75rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0"><?= strtoupper(substr($u['prenom'],0,1).substr($u['nom'],0,1)) ?></div>
      <div style="flex:1">
        <div style="font-weight:600;font-size:.85rem"><?= e($u['prenom'].' '.$u['nom']) ?></div>
        <div class="text-xs text-muted"><?= e($u['email']) ?></div>
      </div>
      <span class="badge badge-<?= $u['role']==='admin'?'danger':($u['role']==='societe'?'purple':'primary') ?>"><?= ucfirst($u['role']) ?></span>
    </div>
    <?php endforeach; ?>
  </div>

</div>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
