<?php $pageTitle = $lang==='fr' ? 'Nouveau mot de passe' : 'New Password'; require __DIR__ . '/../layout/header.php'; ?>

<section class="split-layout">
  <div class="split-form-pane">
    <div class="split-form-container">
      <div class="card animate-scale-in" style="padding:2.5rem">
        <div style="text-align:center;margin-bottom:2rem">
          <div style="font-size:2.5rem;margin-bottom:.75rem">✨</div>
          <h1 style="font-size:1.75rem"><?= $lang==='fr' ? 'Nouveau mot de passe' : 'New Password' ?></h1>
          <p style="margin-top:.4rem;font-size:.9rem"><?= $lang==='fr' ? 'Définissez votre nouveau mot de passe.' : 'Set your new password.' ?></p>
        </div>

        <form method="POST" action="<?= APP_URL ?>/?ctrl=auth&action=resetPassword&token=<?= htmlspecialchars($token) ?>" novalidate id="resetForm">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="form-label" for="password"><?= $lang==='fr' ? 'Nouveau mot de passe' : 'New password' ?> <span class="req">*</span></label>
            <div style="position:relative">
              <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" style="padding-right:3rem" autofocus>
              <button type="button" onclick="togglePass('password', 'eyeBtn1')" style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.1rem" id="eyeBtn1">👁</button>
            </div>
            <div class="form-error" id="passError"></div>
          </div>

          <div class="form-group">
            <label class="form-label" for="password_confirm"><?= $lang==='fr' ? 'Confirmez le mot de passe' : 'Confirm password' ?> <span class="req">*</span></label>
            <div style="position:relative">
              <input type="password" id="password_confirm" name="password_confirm" class="form-control" placeholder="••••••••" style="padding-right:3rem">
              <button type="button" onclick="togglePass('password_confirm', 'eyeBtn2')" style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.1rem" id="eyeBtn2">👁</button>
            </div>
            <div class="form-error" id="confirmError"></div>
          </div>

          <button type="submit" class="btn btn-primary w-full btn-lg" id="resetBtn">
            <?= $lang==='fr' ? 'Sauvegarder et se connecter →' : 'Save & Login →' ?>
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="split-illustration-pane">
    <div style="font-size:6rem;margin-bottom:1.5rem;animation:float 6s ease-in-out infinite">🎉</div>
    <h2><?= $lang==='fr' ? 'Presque terminé !' : 'Almost there!' ?></h2>
    <p><?= $lang==='fr' ? 'Choisissez un mot de passe fort que vous n\'utilisez nulle part ailleurs.' : 'Choose a strong password that you do not use anywhere else.' ?></p>
  </div>
</section>

<script>
function togglePass(inputId, btnId) {
  const p = document.getElementById(inputId);
  const b = document.getElementById(btnId);
  p.type = p.type === 'password' ? 'text' : 'password';
  b.textContent = p.type === 'password' ? '👁' : '🙈';
}
document.getElementById('resetForm').addEventListener('submit', function(e) {
  const pass = document.getElementById('password').value;
  const conf = document.getElementById('password_confirm').value;
  let valid = true;
  document.getElementById('passError').textContent = '';
  document.getElementById('confirmError').textContent = '';

  if (!pass) {
    document.getElementById('passError').textContent = '<?= __('gen.required') ?>';
    valid = false;
  } else if (pass.length < 6) {
    document.getElementById('passError').textContent = '<?= $lang==='fr' ? 'Au moins 6 caractères.' : 'At least 6 characters.' ?>';
    valid = false;
  }

  if (pass !== conf) {
    document.getElementById('confirmError').textContent = '<?= $lang==='fr' ? 'Les mots de passe ne correspondent pas.' : 'Passwords do not match.' ?>';
    valid = false;
  }

  if (!valid) {
    e.preventDefault();
  } else {
    const btn = document.getElementById('resetBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span>';
  }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
