<?php $pageTitle = $lang==='fr' ? 'Mot de passe oublié' : 'Forgot Password'; require __DIR__ . '/../layout/header.php'; ?>

<section class="split-layout">
  <div class="split-form-pane">
    <div class="split-form-container">
      <div class="card animate-scale-in" style="padding:2.5rem">
        <div style="text-align:center;margin-bottom:2rem">
          <div style="font-size:2.5rem;margin-bottom:.75rem">🔐</div>
          <h1 style="font-size:1.75rem"><?= $lang==='fr' ? 'Mot de passe oublié' : 'Forgot Password' ?></h1>
          <p style="margin-top:.4rem;font-size:.9rem"><?= $lang==='fr' ? 'Entrez votre email pour réinitialiser votre mot de passe.' : 'Enter your email to reset your password.' ?></p>
        </div>

        <form method="POST" action="<?= APP_URL ?>/?ctrl=auth&action=forgotPassword" novalidate id="forgotForm">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label" for="email"><?= __('auth.email') ?> <span class="req">*</span></label>
            <input type="email" id="email" name="email" class="form-control"
                   placeholder="exemple@email.com"
                   value="<?= e($_SESSION['form_old']['email'] ?? '') ?>"
                   autofocus>
            <div class="form-error" id="emailError"></div>
          </div>

          <button type="submit" class="btn btn-primary w-full btn-lg" id="forgotBtn">
            <?= $lang==='fr' ? 'Vérifier mon email →' : 'Verify my email →' ?>
          </button>

          <div style="text-align:center;margin-top:1.25rem">
            <a href="<?= APP_URL ?>/?ctrl=auth&action=login" class="text-primary text-sm" style="font-weight:600">
              ← <?= $lang==='fr' ? 'Retour à la connexion' : 'Back to login' ?>
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="split-illustration-pane">
    <div style="font-size:6rem;margin-bottom:1.5rem;animation:float 6s ease-in-out infinite">🛡️</div>
    <h2><?= $lang==='fr' ? 'Récupération de compte' : 'Account Recovery' ?></h2>
    <p><?= $lang==='fr' ? 'Nous allons vérifier votre identité pour vous permettre de définir un nouveau mot de passe en toute sécurité.' : 'We will verify your identity so you can set a new password securely.' ?></p>
    <div class="flex gap-2 mt-3" style="flex-wrap: wrap; justify-content: center;">
      <span class="badge badge-primary">🔒 <?= $lang==='fr' ? 'Sécurisé' : 'Secure' ?></span>
      <span class="badge badge-purple">⚡ <?= $lang==='fr' ? 'Rapide' : 'Fast' ?></span>
      <span class="badge badge-success">✅ <?= $lang==='fr' ? 'Simple' : 'Simple' ?></span>
    </div>
  </div>
</section>

<script>
document.getElementById('forgotForm').addEventListener('submit', function(e) {
  const email = document.getElementById('email').value.trim();
  document.getElementById('emailError').textContent = '';
  if (!email) {
    e.preventDefault();
    document.getElementById('emailError').textContent = '<?= __('gen.required') ?>';
    return;
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    e.preventDefault();
    document.getElementById('emailError').textContent = '<?= $lang==='fr' ? 'Email invalide' : 'Invalid email' ?>';
    return;
  }
  const btn = document.getElementById('forgotBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>';
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
