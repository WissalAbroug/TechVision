<?php $pageTitle = __('auth.login'); $old = $_SESSION['form_old'] ?? []; unset($_SESSION['form_old']); require __DIR__ . '/../layout/header.php'; ?>

<section class="split-layout">
  <!-- Form Pane (Left) -->
  <div class="split-form-pane">
    <div class="split-form-container">
      <div class="card animate-scale-in" style="padding:2.5rem">
        <div style="text-align:center;margin-bottom:2rem">
          <div style="font-size:2.5rem;margin-bottom:.75rem">🔑</div>
          <h1 style="font-size:1.75rem"><?= __('auth.login') ?></h1>
          <p style="margin-top:.4rem;font-size:.9rem"><?= __('auth.have_account') ?> <a href="<?= APP_URL ?>/?ctrl=auth&action=register" class="text-primary font-semibold"><?= __('auth.register') ?></a></p>
        </div>

        <form method="POST" action="<?= APP_URL ?>/?ctrl=auth&action=login" novalidate id="loginForm">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label" for="email"><?= __('auth.email') ?> <span class="req">*</span></label>
            <input type="text" id="email" name="email" class="form-control"
                   placeholder="exemple@email.com"
                   value="<?= e($old['email'] ?? '') ?>">
            <div class="form-error" id="emailError"></div>
          </div>

          <div class="form-group">
            <label class="form-label" for="password"><?= __('auth.password') ?> <span class="req">*</span></label>
            <div style="position:relative">
              <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" style="padding-right:3rem">
              <button type="button" onclick="togglePass()" style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.1rem" id="eyeBtn">👁</button>
            </div>
            <div class="form-error" id="passError"></div>
          </div>

          <div class="flex-between" style="margin-bottom:1.5rem">
            <label style="display:flex;align-items:center;gap:.5rem;font-size:.875rem;cursor:pointer">
              <input type="checkbox" name="remember" style="width:16px;height:16px;accent-color:var(--primary)"> Se souvenir
            </label>
            <a href="<?= APP_URL ?>/?ctrl=auth&action=forgotPassword" class="text-primary text-sm"><?= __('auth.forgot') ?></a>
          </div>

          <button type="submit" class="btn btn-primary w-full btn-lg" id="loginBtn">
            <?= __('auth.login') ?> →
          </button>

        </form>
      </div>
    </div>
  </div>

  <!-- Illustration Pane (Right) -->
  <div class="split-illustration-pane">
    <img src="<?= APP_URL ?>/public/images/login_illustration.png" alt="PathFinder AI Security" class="split-illustration-img">
    <h2>Bon retour !</h2>
    <p>Connectez-vous pour accéder à votre espace personnalisé et trouver votre opportunité idéale.</p>
    <div class="flex gap-2 mt-3" style="flex-wrap: wrap; justify-content: center;">
      <span class="badge badge-primary">🛡️ Sécurité</span>
      <span class="badge badge-purple">⚡ Rapide</span>
      <span class="badge badge-success">🤖 Intelligent</span>
    </div>
  </div>
</section>

<script>
function togglePass() {
  const p = document.getElementById('password');
  const b = document.getElementById('eyeBtn');
  p.type = p.type === 'password' ? 'text' : 'password';
  b.textContent = p.type === 'password' ? '👁' : '🙈';
}
document.getElementById('loginForm').addEventListener('submit', function(e) {
  let valid = true;
  const email = document.getElementById('email').value.trim();
  const pass  = document.getElementById('password').value;
  document.getElementById('emailError').textContent = '';
  document.getElementById('passError').textContent  = '';
  if (!email) { document.getElementById('emailError').textContent = '<?= __('gen.required') ?>'; valid = false; }
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { document.getElementById('emailError').textContent = 'Email invalide'; valid = false; }
  if (!pass)  { document.getElementById('passError').textContent  = '<?= __('gen.required') ?>'; valid = false; }
  if (!valid) { e.preventDefault(); return; }
  const btn = document.getElementById('loginBtn');
  btn.disabled = true; btn.innerHTML = '<span class="spinner"></span>';
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
