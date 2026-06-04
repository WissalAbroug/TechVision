<?php $pageTitle = __('admin.users') . ' - ' . e($user['prenom'] . ' ' . $user['nom']); require __DIR__ . '/../layout/sidebar.php'; ?>

<div class="flex-between mb-4 animate-fade-in-up">
  <h2>👁️ Détails Utilisateur</h2>
  <a href="<?= APP_URL ?>/?ctrl=admin&action=users" class="btn btn-outline btn-sm">⬅ <?= $lang==='fr'?'Retour':'Back' ?></a>
</div>

<div class="grid grid-2 animate-fade-in-up delay-100" style="gap:2rem">
  <div class="card">
    <h3 style="margin-bottom:1.5rem;border-bottom:1px solid var(--border);padding-bottom:.5rem;">Informations Générales</h3>
    
    <div style="display:flex;gap:1.5rem;align-items:center;margin-bottom:2rem;">
      <?php if (!empty($profil['photo'])): ?>
        <img src="<?= APP_URL ?>/public/uploads/<?= e($profil['photo']) ?>" alt="Avatar" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--primary)">
      <?php else: ?>
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--purple));color:#fff;font-size:2rem;font-weight:700;display:flex;align-items:center;justify-content:center;">
          <?= strtoupper(substr($user['prenom'],0,1).substr($user['nom'],0,1)) ?>
        </div>
      <?php endif; ?>
      <div>
        <h3 style="margin-bottom:.2rem;"><?= e($user['prenom'].' '.$user['nom']) ?></h3>
        <div class="text-muted"><?= e($user['email']) ?></div>
        <div class="mt-1">
          <span class="badge badge-<?= $user['role']==='admin'?'danger':($user['role']==='societe'?'purple':'primary') ?>"><?= ucfirst($user['role']) ?></span>
          <span class="badge badge-<?= ($user['statut'] ?? 'actif')==='actif'?'success':'warning' ?>"><?= ucfirst($user['statut'] ?? 'actif') ?></span>
        </div>
      </div>
    </div>

    <table style="width:100%; border:none; margin-bottom:1rem;">
      <tbody>
        <tr><td style="padding:.5rem 0; color:var(--text-muted); width:40%;">Date d'inscription</td><td style="padding:.5rem 0; font-weight:600;"><?= date('d/m/Y H:i', strtotime($user['created_at'] ?? 'now')) ?></td></tr>
        <?php if($user['role']==='societe'): ?>
        <tr><td style="padding:.5rem 0; color:var(--text-muted);">Nom de Société</td><td style="padding:.5rem 0; font-weight:600;"><?= e($user['nom_societe'] ?? '-') ?></td></tr>
        <tr><td style="padding:.5rem 0; color:var(--text-muted);">Secteur</td><td style="padding:.5rem 0; font-weight:600;"><?= e($user['secteur'] ?? '-') ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    
    <div style="display:flex;gap:.5rem;margin-top:2rem;">
      <!-- Buttons removed as requested -->
    </div>
  </div>

  <?php if ($user['role'] === 'candidat'): ?>
  <div class="card">
    <h3 style="margin-bottom:1.5rem;border-bottom:1px solid var(--border);padding-bottom:.5rem;">Profil Candidat</h3>
    <?php if ($profil): ?>
      <table style="width:100%; border:none;">
        <tbody>
          <tr><td style="padding:.5rem 0; color:var(--text-muted); width:30%;">Titre</td><td style="padding:.5rem 0; font-weight:600;"><?= e($profil['titre']??'-') ?></td></tr>
          <tr><td style="padding:.5rem 0; color:var(--text-muted);">Téléphone</td><td style="padding:.5rem 0; font-weight:600;"><?= e($profil['telephone']??'-') ?></td></tr>
          <tr><td style="padding:.5rem 0; color:var(--text-muted);">Ville</td><td style="padding:.5rem 0; font-weight:600;"><?= e($profil['ville']??'-') ?></td></tr>
          <?php if(!empty($profil['linkedin'])): ?>
          <tr><td style="padding:.5rem 0; color:var(--text-muted);">LinkedIn</td><td style="padding:.5rem 0;"><a href="<?= e($profil['linkedin']) ?>" target="_blank" class="text-primary"><?= e($profil['linkedin']) ?></a></td></tr>
          <?php endif; ?>
          <?php if(!empty($profil['github'])): ?>
          <tr><td style="padding:.5rem 0; color:var(--text-muted);">GitHub</td><td style="padding:.5rem 0;"><a href="<?= e($profil['github']) ?>" target="_blank" class="text-primary"><?= e($profil['github']) ?></a></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      
      <?php if(!empty($profil['bio'])): ?>
      <div style="margin-top:1.5rem;">
        <div class="text-muted text-sm mb-1">Bio</div>
        <p style="font-size:.9rem; line-height:1.6;"><?= nl2br(e($profil['bio'])) ?></p>
      </div>
      <?php endif; ?>
      
      <?php 
        $competences = !empty($profil['competences']) ? json_decode($profil['competences'], true) : [];
        if (!empty($competences)): 
      ?>
      <div style="margin-top:1.5rem;">
        <div class="text-muted text-sm mb-1">Compétences</div>
        <div class="flex flex-wrap gap-1 mt-1">
          <?php foreach ($competences as $c): ?>
          <span class="badge badge-purple"><?= e($c) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    <?php else: ?>
      <div class="empty-state">
        <div class="icon">👤</div>
        <p>Ce candidat n'a pas encore rempli son profil.</p>
      </div>
    <?php endif; ?>
  </div>
  <?php else: ?>
  <div class="card flex-center flex-col text-center" style="color:var(--text-muted);">
    <div style="font-size:3rem;margin-bottom:1rem;">🏢</div>
    <p>Profil réservé aux candidats.</p>
  </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/topbar.php'; ?>
