<?php $pageTitle = __('auth.register'); $old = $_SESSION['form_old'] ?? []; unset($_SESSION['form_old']); require __DIR__ . '/../layout/header.php'; ?>

<section class="split-layout">
  <!-- Form Pane (Left) -->
  <div class="split-form-pane">
    <div class="split-form-container" style="max-width: 520px;">
      <div class="card animate-scale-in" style="padding:2.2rem">
        <div style="text-align:center;margin-bottom:1.5rem">
          <div style="font-size:2.2rem;margin-bottom:.5rem">🚀</div>
          <h1 style="font-size:1.6rem"><?= __('auth.register') ?></h1>
          <p style="margin-top:.4rem;font-size:.9rem"><?= __('auth.no_account') ?> <a href="<?= APP_URL ?>/?ctrl=auth&action=login" class="text-primary font-semibold"><?= __('auth.login') ?></a></p>
        </div>

        <!-- Role Selector -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.5rem" id="roleSelector">
          <label id="roleCandidat" class="role-option active" style="display:flex;align-items:center;gap:.75rem;padding:0.75rem 1rem;border:2px solid var(--primary);border-radius:var(--radius);cursor:pointer;transition:var(--transition);background:var(--primary-50)">
            <input type="radio" name="role_choice" value="candidat" checked style="display:none">
            <span style="font-size:1.5rem">🧑‍💻</span>
            <div><div style="font-weight:600;font-size:.85rem"><?= $lang === 'fr' ? 'Candidat' : 'Candidate' ?></div><div style="font-size:.72rem;color:var(--text-muted)"><?= $lang === 'fr' ? 'Je cherche un emploi' : 'I\'m looking for a job' ?></div></div>
          </label>
          <label id="roleSociete" class="role-option" style="display:flex;align-items:center;gap:.75rem;padding:0.75rem 1rem;border:2px solid var(--border);border-radius:var(--radius);cursor:pointer;transition:var(--transition)">
            <input type="radio" name="role_choice" value="societe" style="display:none">
            <span style="font-size:1.5rem">🏢</span>
            <div><div style="font-weight:600;font-size:.85rem"><?= $lang === 'fr' ? 'Entreprise' : 'Company' ?></div><div style="font-size:.72rem;color:var(--text-muted)"><?= $lang === 'fr' ? 'Je recrute des talents' : 'I\'m hiring talents' ?></div></div>
          </label>
        </div>

        <form method="POST" action="<?= APP_URL ?>/?ctrl=auth&action=register" novalidate id="regForm">
          <?= csrf_field() ?>
          <input type="hidden" name="role" id="roleInput" value="<?= e($old['role'] ?? 'candidat') ?>">

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="prenom"><?= __('gen.prenom') ?> <span class="req">*</span></label>
              <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Prénom" value="<?= e($old['prenom'] ?? '') ?>">
              <div class="form-error" id="prenomErr"></div>
            </div>
            <div class="form-group">
              <label class="form-label" for="nom"><?= __('gen.nom') ?> <span class="req">*</span></label>
              <input type="text" id="nom" name="nom" class="form-control" placeholder="Nom" value="<?= e($old['nom'] ?? '') ?>">
              <div class="form-error" id="nomErr"></div>
            </div>
          </div>

          <!-- Société name (shown only for societe) -->
          <div class="form-group" id="societeNameGroup" style="display:none">
            <label class="form-label" for="nom_societe"><?= $lang === 'fr' ? 'Nom de l\'entreprise' : 'Company name' ?> <span class="req">*</span></label>
            <input type="text" id="nom_societe" name="nom_societe" class="form-control" placeholder="Ex: TechCorp SA" value="<?= e($old['nom_societe'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label class="form-label" for="email"><?= __('auth.email') ?> <span class="req">*</span></label>
            <input type="text" id="regEmail" name="email" class="form-control" placeholder="vous@email.com" value="<?= e($old['email'] ?? '') ?>">
            <div class="form-error" id="emailErr"></div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="password"><?= __('auth.password') ?> <span class="req">*</span></label>
              <input type="password" id="regPassword" name="password" class="form-control" placeholder="Min. 6 caractères">
              <div class="form-error" id="passErr"></div>
            </div>
            <div class="form-group">
              <label class="form-label" for="password_confirm"><?= __('auth.confirm_pass') ?> <span class="req">*</span></label>
              <input type="password" id="passConfirm" name="password_confirm" class="form-control" placeholder="Répéter">
              <div class="form-error" id="passConfErr"></div>
            </div>
          </div>

          <!-- Password strength -->
          <div style="margin-bottom:1.25rem">
            <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:.35rem" id="passStrengthLabel"></div>
            <div class="progress-bar" style="height:4px"><div class="progress-fill" id="passStrengthBar" style="width:0;transition:width .3s"></div></div>
          </div>

          <button type="submit" class="btn btn-primary w-full btn-lg" id="regBtn">
            <?= __('auth.register') ?> →
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Illustration Pane (Right) -->
  <div class="split-illustration-pane">
    <img src="<?= APP_URL ?>/public/images/register_illustration.png" alt="PathFinder AI Future" class="split-illustration-img">
    <h2>Rejoignez l'avenir</h2>
    <p>Créez votre compte en quelques secondes et commencez à utiliser notre IA de matching d'opportunités en temps réel.</p>
    
  </div>
</section>

<script>
// Role selector
const roles = document.querySelectorAll('.role-option');
const roleInput = document.getElementById('roleInput');
const societeGrp = document.getElementById('societeNameGroup');
roles.forEach(label => {
  label.addEventListener('click', () => {
    roles.forEach(r => { r.style.border='2px solid var(--border)'; r.style.background=''; });
    label.style.border = '2px solid var(--primary)';
    label.style.background = 'var(--primary-50)';
    const val = label.querySelector('input').value;
    roleInput.value = val;
    societeGrp.style.display = val === 'societe' ? '' : 'none';
  });
});
// Restore role
if ('<?= e($old['role'] ?? 'candidat') ?>' === 'societe') {
  document.getElementById('roleSociete').click();
}

// Password strength
document.getElementById('regPassword').addEventListener('input', function() {
  const v = this.value;
  let s = 0;
  if (v.length >= 6) s++;
  if (v.length >= 10) s++;
  if (/[A-Z]/.test(v)) s++;
  if (/[0-9]/.test(v)) s++;
  if (/[^A-Za-z0-9]/.test(v)) s++;
  const colors = ['','var(--danger)','var(--warning)','var(--warning)','var(--success)','var(--success)'];
  const labels = ['','Très faible','Faible','Moyen','Fort','Très fort'];
  document.getElementById('passStrengthBar').style.width = (s*20)+'%';
  document.getElementById('passStrengthBar').style.background = colors[s];
  document.getElementById('passStrengthLabel').textContent = labels[s] ? 'Sécurité : '+labels[s] : '';
});

// Form validation
document.getElementById('regForm').addEventListener('submit', function(e) {
  let valid = true;
  const clear = id => document.getElementById(id).textContent = '';
  ['prenomErr','nomErr','emailErr','passErr','passConfErr'].forEach(clear);
  if (!document.getElementById('prenom').value.trim()) { document.getElementById('prenomErr').textContent='<?= __('gen.required') ?>'; valid=false; }
  if (!document.getElementById('nom').value.trim()) { document.getElementById('nomErr').textContent='<?= __('gen.required') ?>'; valid=false; }
  const em = document.getElementById('regEmail').value.trim();
  if (!em) { document.getElementById('emailErr').textContent='<?= __('gen.required') ?>'; valid=false; }
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) { document.getElementById('emailErr').textContent='Email invalide'; valid=false; }
  const pw = document.getElementById('regPassword').value;
  if (!pw) { document.getElementById('passErr').textContent='<?= __('gen.required') ?>'; valid=false; }
  else if (pw.length < 6) { document.getElementById('passErr').textContent='Min. 6 caractères'; valid=false; }
  if (pw !== document.getElementById('passConfirm').value) { document.getElementById('passConfErr').textContent='<?= __('auth.pass_mismatch') ?>'; valid=false; }
  if (!valid) e.preventDefault();
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
