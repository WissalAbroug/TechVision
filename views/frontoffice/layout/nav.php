<nav class="navbar">
  <a href="<?= APP_URL ?>/" class="navbar-brand">
    <img src="<?= APP_URL ?>/public/images/pathfinder logo.jpeg" alt="<?= APP_NAME ?>">
  </a>

  <div class="navbar-nav">
    <a href="<?= APP_URL ?>/" class="<?= $currentCtrl === 'home' ? 'active' : '' ?>"><?= __('nav.home') ?></a>
    <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="<?= $currentCtrl === 'offre' ? 'active' : '' ?>"><?= __('nav.jobs') ?></a>
    <?php if (isLoggedIn()): ?>
      <?php if (hasRole('candidat')): ?>
        <a href="<?= APP_URL ?>/?ctrl=user&action=dashboard" class="<?= $currentCtrl === 'user' ? 'active' : '' ?>"><?= __('nav.dashboard') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=cv&action=index"       class="<?= $currentCtrl === 'cv'   ? 'active' : '' ?>"><?= __('nav.my_cv') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=candidature&action=index" class="<?= $currentCtrl === 'candidature' ? 'active' : '' ?>"><?= __('nav.my_apps') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=entretien&action=index" class="<?= $currentCtrl === 'entretien' ? 'active' : '' ?>"><?= __('nav.interviews') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=formation&action=index" class="<?= $currentCtrl === 'formation' ? 'active' : '' ?>"><?= __('nav.trainings') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=quiz&action=index" class="<?= $currentCtrl === 'quiz' ? 'active' : '' ?>">🎯 <?= $lang==='fr'?'Tests':'Tests' ?></a>
        <a href="<?= APP_URL ?>/?ctrl=offre&action=favoris" class="<?= ($_GET['action'] ?? '') === 'favoris' ? 'active' : '' ?>" style="color:#f87171">❤️ <?= $lang==='fr'?'Favoris':'Favorites' ?></a>
        <a href="<?= APP_URL ?>/?ctrl=reclamation&action=index" class="<?= $currentCtrl === 'reclamation' ? 'active' : '' ?>"><?= __('nav.complaints') ?></a>
      <?php elseif (hasRole('societe')): ?>
        <a href="<?= APP_URL ?>/?ctrl=societe&action=dashboard"><?= __('societe.dashboard') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=societe&action=offres"><?= __('societe.my_offers') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=societe&action=candidats"><?= __('societe.candidates') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=entretien&action=societeIndex"><?= __('societe.interviews') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=reclamation&action=index" class="<?= $currentCtrl === 'reclamation' ? 'active' : '' ?>"><?= __('societe.complaints') ?></a>
      <?php elseif (hasRole('admin')): ?>
        <a href="<?= APP_URL ?>/?ctrl=admin&action=dashboard" class="<?= $currentCtrl === 'admin' ? 'active' : '' ?>"><?= __('admin.dashboard') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=admin&action=users" class="<?= $currentCtrl === 'admin' && ($_GET['action']??'') === 'users' ? 'active' : '' ?>"><?= __('admin.users') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=admin&action=offres" class="<?= $currentCtrl === 'admin' && ($_GET['action']??'') === 'offres' ? 'active' : '' ?>"><?= __('admin.offers') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=formation&action=adminIndex" class="<?= $currentCtrl === 'formation' ? 'active' : '' ?>"><?= __('admin.trainings') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=entretien&action=adminIndex" class="<?= $currentCtrl === 'entretien' ? 'active' : '' ?>"><?= __('societe.interviews') ?></a>
        <a href="<?= APP_URL ?>/?ctrl=reclamation&action=index" class="<?= $currentCtrl === 'reclamation' ? 'active' : '' ?>"><?= __('admin.complaints') ?></a>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <div class="navbar-actions">
    <!-- Theme Toggle -->
    <button class="theme-toggle-btn" id="themeToggle" onclick="toggleTheme()" aria-label="Toggle Theme" style="background:none;border:none;cursor:pointer;font-size:1.2rem;padding:0.4rem;display:flex;align-items:center;justify-content:center">
      🌙
    </button>

    <!-- Lang Toggle Switch -->
    <div class="lang-switch" id="langSwitch" onclick="switchLang('<?= $lang === 'fr' ? 'en' : 'fr' ?>')" title="<?= $lang==='fr'?'Switch to English':'Passer en français' ?>">
      <span class="lang-label lang-left <?= $lang==='en'?'lang-active':'' ?>">EN</span>
      <div class="lang-track">
        <div class="lang-thumb <?= $lang==='fr'?'lang-thumb-right':'' ?>"></div>
      </div>
      <span class="lang-label lang-right <?= $lang==='fr'?'lang-active':'' ?>">FR</span>
    </div>

    <?php if (isLoggedIn()): ?>
    <!-- Notification Bell -->
    <div class="notif-wrap" id="notifWrap">
      <button class="notif-btn" id="notifBtn" aria-label="Notifications">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span class="notif-badge" id="notifBadge" style="display:none">0</span>
      </button>
      <div class="notif-dropdown" id="notifDropdown">
        <div class="notif-header">
          <span><?= $lang==='fr'?'Notifications':'Notifications' ?></span>
          <button class="notif-clear" id="notifClear"><?= $lang==='fr'?'Tout lire':'Mark all read' ?></button>
        </div>
        <div class="notif-list" id="notifList">
          <div class="notif-empty">
            <div style="font-size:2rem;margin-bottom:.5rem">🔔</div>
            <div><?= $lang==='fr'?'Aucune notification':'No notifications yet' ?></div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!isLoggedIn()): ?>
      <a href="<?= APP_URL ?>/?ctrl=auth&action=login" class="btn btn-ghost btn-sm"><?= __('auth.login') ?></a>
      <a href="<?= APP_URL ?>/?ctrl=auth&action=register" class="btn btn-primary btn-sm"><?= __('auth.register') ?></a>
    <?php else: ?>
      <div class="avatar" tabindex="0">
        <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
        <div class="avatar-menu">
          <div style="padding:.5rem .9rem;border-bottom:1px solid var(--border);margin-bottom:.25rem">
            <div style="font-weight:700;font-size:.88rem"><?= e($user['prenom'] . ' ' . $user['nom']) ?></div>
            <div style="font-size:.75rem;color:var(--text-muted)"><?= e($user['email']) ?></div>
          </div>
          <?php if (hasRole('candidat')): ?>
            <a href="<?= APP_URL ?>/?ctrl=user&action=profile">👤 <?= __('nav.profile') ?></a>
            <a href="<?= APP_URL ?>/?ctrl=cv&action=index">📄 <?= __('nav.my_cv') ?></a>
            <a href="<?= APP_URL ?>/?ctrl=offre&action=favoris" style="color:#f87171">❤️ <?= $lang==='fr'?'Mes Favoris':'My Favorites' ?></a>
          <?php elseif (hasRole('societe')): ?>
            <a href="<?= APP_URL ?>/?ctrl=societe&action=dashboard">🏢 <?= __('societe.dashboard') ?></a>
          <?php elseif (hasRole('admin')): ?>
            <a href="<?= APP_URL ?>/?ctrl=admin&action=dashboard">🛡️ <?= __('admin.dashboard') ?></a>
          <?php endif; ?>
          <a href="<?= APP_URL ?>/?ctrl=auth&action=logout" style="color:var(--danger)!important">🚪 <?= __('auth.logout') ?></a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Hamburger -->
    <div class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </div>
  </div>
</nav>

<!-- Mobile Nav -->
<div class="mobile-nav" id="mobileNav">
  <a href="<?= APP_URL ?>/"><?= __('nav.home') ?></a>
  <a href="<?= APP_URL ?>/?ctrl=offre&action=index"><?= __('nav.jobs') ?></a>
  <?php if (isLoggedIn()): ?>
    <a href="<?= APP_URL ?>/?ctrl=user&action=dashboard"><?= __('nav.dashboard') ?></a>
    <a href="<?= APP_URL ?>/?ctrl=user&action=profile"><?= __('nav.profile') ?></a>
    <?php if (hasRole('candidat')): ?>
    <a href="<?= APP_URL ?>/?ctrl=offre&action=favoris" style="color:#f87171">❤️ <?= $lang==='fr'?'Mes Favoris':'My Favorites' ?></a>
    <?php endif; ?>
    <a href="<?= APP_URL ?>/?ctrl=reclamation&action=index"><?= __('nav.complaints') ?></a>
    <a href="<?= APP_URL ?>/?ctrl=auth&action=logout" style="color:var(--danger)"><?= __('auth.logout') ?></a>
  <?php else: ?>
    <a href="<?= APP_URL ?>/?ctrl=auth&action=login"><?= __('auth.login') ?></a>
    <a href="<?= APP_URL ?>/?ctrl=auth&action=register"><?= __('auth.register') ?></a>
  <?php endif; ?>
  <a href="javascript:void(0)" onclick="switchLang('<?= $lang === 'fr' ? 'en' : 'fr' ?>')">🌐 <?= __('nav.lang_toggle') ?></a>
</div>
