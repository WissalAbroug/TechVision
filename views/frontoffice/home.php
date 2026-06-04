<?php $pageTitle = __('nav.home'); require __DIR__ . '/layout/header.php'; ?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
  </div>
  <div class="container">
    <div class="hero-content">
      <div class="animate-fade-in-up">
        <div class="hero-badge">
          <span>⚡</span> <?= $lang === 'fr' ? 'Plateforme RH propulsée par l\'IA' : 'AI-Powered HR Platform' ?>
        </div>
        <h1 class="animate-fade-in-up delay-100"><?= __('home.hero_title') ?></h1>
        <p class="animate-fade-in-up delay-200" style="margin-top:1.25rem"><?= __('home.hero_sub') ?></p>
        <div class="hero-actions animate-fade-in-up delay-300">
          <a href="<?= APP_URL ?>/?ctrl=<?= isLoggedIn() ? 'cv&action=index' : 'auth&action=register' ?>" class="btn btn-primary btn-xl">
            📄 <?= __('home.cta_cv') ?>
          </a>
          <a href="<?= APP_URL ?>/?ctrl=offre&action=index" class="btn btn-xl" style="background:rgba(255,255,255,.05);color:var(--text-primary);border:1px solid var(--border)">
            🔍 <?= __('home.cta_jobs') ?>
          </a>
        </div>
      </div>
      <div class="hero-illustration-container animate-fade-in-up delay-200">
        <img src="<?= APP_URL ?>/public/images/hero_illustration.png" alt="PathFinder AI Hero" style="max-height: 440px; width: auto; object-fit: contain;">
      </div>
    </div>

    <div class="hero-stat-row animate-fade-in-up delay-400">
      <div class="hero-stat">
        <div class="num" data-count="<?= (int)($stats['users'] ?? 1240) ?>"><?= (int)($stats['users'] ?? 1240) ?></div>
        <div class="lbl"><?= __('home.stats_users') ?></div>
      </div>
      <div class="hero-stat">
        <div class="num" data-count="<?= (int)($stats['offres'] ?? 380) ?>"><?= (int)($stats['offres'] ?? 380) ?></div>
        <div class="lbl"><?= __('home.stats_jobs') ?></div>
      </div>
      <div class="hero-stat">
        <div class="num" data-count="<?= (int)($stats['placed'] ?? 847) ?>"><?= (int)($stats['placed'] ?? 847) ?></div>
        <div class="lbl"><?= __('home.stats_placed') ?></div>
      </div>
      <div class="hero-stat">
        <div class="num" data-count="<?= (int)($stats['societes'] ?? 120) ?>"><?= (int)($stats['societes'] ?? 120) ?></div>
        <div class="lbl"><?= __('home.stats_comp') ?></div>
      </div>
    </div>
  </div>
</section>

<!-- ── Features ───────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <div class="section-tag"><?= __('home.features') ?></div>
      <h2><?= $lang === 'fr' ? 'Tout ce dont vous avez besoin' : 'Everything you need' ?></h2>
      <p><?= $lang === 'fr' ? 'Une plateforme complète pour trouver, postuler et réussir vos entretiens.' : 'A complete platform to find, apply and succeed in your interviews.' ?></p>
    </div>
    <div class="grid grid-4" style="margin-top:3rem">
      <div class="feature-card animate-on-scroll">
        <div class="feature-icon icon-blue">🎯</div>
        <h3><?= __('home.feat1_title') ?></h3>
        <p style="margin-top:.5rem;font-size:.9rem"><?= __('home.feat1_desc') ?></p>
      </div>
      <div class="feature-card animate-on-scroll delay-100">
        <div class="feature-icon icon-purple">📄</div>
        <h3><?= __('home.feat2_title') ?></h3>
        <p style="margin-top:.5rem;font-size:.9rem"><?= __('home.feat2_desc') ?></p>
      </div>
      <div class="feature-card animate-on-scroll delay-200">
        <div class="feature-icon icon-green">🎤</div>
        <h3><?= __('home.feat3_title') ?></h3>
        <p style="margin-top:.5rem;font-size:.9rem"><?= __('home.feat3_desc') ?></p>
      </div>
      <div class="feature-card animate-on-scroll delay-300">
        <div class="feature-icon icon-orange">📚</div>
        <h3><?= __('home.feat4_title') ?></h3>
        <p style="margin-top:.5rem;font-size:.9rem"><?= __('home.feat4_desc') ?></p>
      </div>
    </div>
  </div>
</section>

<?php if (!isLoggedIn()): ?>
<!-- ── How It Works ───────────────────────────────────────── -->
<section class="section" style="background:linear-gradient(135deg,rgba(0, 240, 255, 0.03),rgba(167, 139, 250, 0.03))">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <div class="section-tag"><?= $lang === 'fr' ? 'Comment ça marche' : 'How it works' ?></div>
      <h2><?= $lang === 'fr' ? '3 étapes vers votre emploi idéal' : '3 steps to your ideal job' ?></h2>
    </div>
    <div class="grid grid-3" style="margin-top:3rem;gap:2rem">
      <?php $steps = $lang === 'fr'
        ? [['🧑‍💻','Créez votre profil','Inscrivez-vous et construisez votre CV en quelques minutes grâce à notre assistant intelligent.'],
           ['🔍','Trouvez des offres','Parcourez les offres et voyez votre score de compatibilité en temps réel.'],
           ['🎯','Postulez & réussissez','Candidatez en un clic et préparez-vous avec notre simulateur d\'entretien.']]
        : [['🧑‍💻','Create your profile','Sign up and build your resume in minutes with our smart assistant.'],
           ['🔍','Find offers','Browse offers and see your compatibility score in real time.'],
           ['🎯','Apply & succeed','Apply with one click and prepare with our interview simulator.']];
      foreach ($steps as $i => $step): ?>
      <div class="card animate-on-scroll delay-<?= $i*100 ?>" style="text-align:center;padding:2.5rem 2rem">
        <div style="font-size:3rem;margin-bottom:1rem"><?= $step[0] ?></div>
        <div style="display:inline-block;background:var(--primary);color:#fff;width:32px;height:32px;border-radius:50%;font-weight:800;line-height:32px;margin-bottom:1rem"><?= $i+1 ?></div>
        <h3><?= $step[1] ?></h3>
        <p style="margin-top:.5rem;font-size:.9rem"><?= $step[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:3rem" class="animate-on-scroll">
      <a href="<?= APP_URL ?>/?ctrl=auth&action=register" class="btn btn-primary btn-xl">
        <?= $lang === 'fr' ? 'Commencer gratuitement →' : 'Get started for free →' ?>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>
