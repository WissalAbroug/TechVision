<?php $pageTitle = __('cv.title'); require __DIR__ . '/../layout/header.php'; ?>

<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container-sm">
  <div class="flex-between mb-4 animate-fade-in-up">
    <h1 style="font-size:1.5rem">📄 <?= __('cv.title') ?></h1>
    <div class="flex gap-2">
      <a href="<?= APP_URL ?>/?ctrl=cv&action=edit" class="btn btn-outline btn-sm">✏️ <?= __('cv.edit') ?></a>
      <a href="<?= APP_URL ?>/?ctrl=cv&action=download" class="btn btn-primary btn-sm">⬇️ <?= __('cv.download') ?></a>
    </div>
  </div>

  <?php 
    $tpl = $cv['template'] ?? 'modern'; 
    $styles = [
      'modern'    => ['bg' => 'var(--bg-card)', 'primary' => 'var(--primary)', 'accent' => 'var(--purple)', 'headerBg' => 'transparent'],
      'classic'   => ['bg' => '#ffffff', 'primary' => '#1d4ed8', 'accent' => '#1e40af', 'headerBg' => '#f8fafc', 'text' => '#1e293b'],
      'creative'  => ['bg' => 'var(--bg-card)', 'primary' => '#db2777', 'accent' => '#fbbf24', 'headerBg' => 'transparent'],
      'minimal'   => ['bg' => '#fafafa', 'primary' => '#374151', 'accent' => '#6b7280', 'headerBg' => 'transparent', 'text' => '#111827'],
      'executive' => ['bg' => '#111827', 'primary' => '#f59e0b', 'accent' => '#d97706', 'headerBg' => '#1f2937', 'text' => '#f3f4f6'],
    ];
    $s = $styles[$tpl] ?? $styles['modern'];
  ?>

  <!-- CV Card -->
  <div class="card animate-fade-in-up delay-100" style="padding:0; overflow:hidden; background: <?= $s['bg'] ?>; color: <?= $s['text'] ?? 'var(--text-primary)' ?>; --primary: <?= $s['primary'] ?>; --purple: <?= $s['accent'] ?>;">
    
    <!-- Header -->
    <div style="display:grid;grid-template-columns:auto 1fr;gap:1.5rem;align-items:center;padding:2.5rem;background: <?= $s['headerBg'] ?>;border-bottom:3px solid var(--primary)">
      <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--purple));color:#fff;font-size:1.75rem;font-weight:800;display:flex;align-items:center;justify-content:center">
        <?= strtoupper(substr(auth()['prenom'],0,1)) ?>
      </div>
      <div>
        <h2 style="font-size:1.5rem;margin:0"><?= e(auth()['prenom'].' '.auth()['nom']) ?></h2>
        <div style="color:var(--primary);font-weight:600;font-size:1.1rem;margin-bottom:.5rem"><?= e($cv['titre']) ?></div>
        <div class="flex items-center gap-3 flex-wrap text-sm" style="color:<?= isset($s['text']) ? $s['text'] : 'var(--text-muted)' ?>;opacity:0.8">
          <?php if ($profil['telephone']): ?><span>📞 <?= e($profil['telephone']) ?></span><?php endif; ?>
          <span>📧 <?= e(auth()['email']) ?></span>
          <?php if ($profil['ville']): ?><span>📍 <?= e($profil['ville'] . ($profil['pays'] ? ', '.$profil['pays'] : '')) ?></span><?php endif; ?>
          <?php if ($profil['date_naissance']): ?><span>🎂 <?= e($profil['date_naissance']) ?></span><?php endif; ?>
          <?php if ($profil['nationalite']): ?><span>🌍 <?= e($profil['nationalite']) ?></span><?php endif; ?>
          <?php if ($profil['permis']): ?><span>🚗 <?= e($profil['permis']) ?></span><?php endif; ?>
          <?php if ($profil['adresse']): ?><span>🏠 <?= e($profil['adresse']) ?></span><?php endif; ?>
        </div>
        
        <?php $res = current_user_reseaux($profil, $cv['reseaux'] ?? []); if(!empty($res)): ?>
        <div class="flex items-center gap-3 flex-wrap text-sm mt-2" style="color:var(--primary)">
          <?php foreach($res as $r): ?>
            <a href="<?= e($r['url']) ?>" target="_blank" style="color:inherit;text-decoration:none;font-weight:600">🔗 <?= e($r['name']) ?></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div style="padding: 2.5rem; display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
      <!-- Left Column (Main) -->
      <div>
        <!-- Résumé -->
        <?php if ($cv['resume']): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:.75rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">À propos</h3>
          <p style="line-height:1.8"><?= nl2br(e($cv['resume'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Expériences -->
        <?php if (!empty($cv['experiences'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:1rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">💼 Expérience professionnelle</h3>
          <div class="timeline">
            <?php foreach ($cv['experiences'] as $exp): ?>
            <div class="timeline-item">
              <div class="timeline-dot"></div>
              <div class="flex-between flex-wrap">
                <div>
                  <div style="font-weight:700"><?= e($exp['poste']) ?></div>
                  <div style="color:var(--primary);font-size:.9rem"><?= e($exp['societe']) ?></div>
                </div>
                <div style="font-size:.82rem;opacity:0.8"><?= e($exp['debut']) ?> — <?= e($exp['fin'] ?: 'Présent') ?></div>
              </div>
              <?php if ($exp['desc']): ?><p style="font-size:.88rem;margin-top:.5rem;line-height:1.6"><?= nl2br(e($exp['desc'])) ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Projets -->
        <?php if (!empty($cv['projets'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:1rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">🚀 Projets</h3>
          <div class="timeline">
            <?php foreach ($cv['projets'] as $proj): ?>
            <div class="timeline-item">
              <div class="timeline-dot" style="background:var(--purple)"></div>
              <div style="font-weight:700"><?= e($proj['titre']) ?> <?php if($proj['role']): ?><span style="font-weight:normal;opacity:0.8">| <?= e($proj['role']) ?></span><?php endif; ?></div>
              <?php if ($proj['techs']): ?><div style="font-size:.85rem;color:var(--primary);margin-bottom:.3rem"><?= e($proj['techs']) ?></div><?php endif; ?>
              <?php if ($proj['desc']): ?><p style="font-size:.88rem;line-height:1.6;margin:0"><?= nl2br(e($proj['desc'])) ?></p><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Réalisations -->
        <?php if (!empty($cv['realisations'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:1rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">🏆 Réalisations & Distinctions</h3>
          <?php foreach ($cv['realisations'] as $real): ?>
          <div style="margin-bottom:.75rem">
            <strong style="display:inline-block;margin-right:.5rem"><?= e($real['titre']) ?></strong> <span style="font-size:.8rem;opacity:0.8"><?= e($real['annee']) ?></span>
            <?php if ($real['desc']): ?><p style="font-size:.88rem;margin:0;opacity:0.9"><?= e($real['desc']) ?></p><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- Right Column (Sidebar) -->
      <div>
        <!-- Compétences techniques -->
        <?php if (!empty($cv['competences'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:.75rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">🛠️ Hard Skills</h3>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($cv['competences'] as $s): ?><span class="skill-tag" style="background:rgba(var(--primary-rgb, 37,99,235),0.1);color:var(--primary);padding:.25rem .75rem;border-radius:99px;font-size:.8rem;font-weight:600"><?= e($s) ?></span><?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Soft Skills -->
        <?php if (!empty($cv['soft_skills'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:.75rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">🤝 Soft Skills</h3>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($cv['soft_skills'] as $s): ?><span class="skill-tag" style="background:rgba(var(--purple-rgb, 139,92,246),0.1);color:var(--purple);padding:.25rem .75rem;border-radius:99px;font-size:.8rem;font-weight:600"><?= e($s) ?></span><?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Quiz / Tests de Compétences -->
        <?php if (!empty($quizResults)): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:.75rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">🎯 <?= $lang==='fr'?'Compétences Vérifiées':'Verified Skills' ?></h3>
          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <?php foreach ($quizResults as $r): 
                $percent = round(($r['score'] / $r['total']) * 100);
                $color = $percent >= 80 ? '#10b981' : ($percent >= 50 ? '#f59e0b' : '#ef4444');
            ?>
            <div style="background: rgba(var(--primary-rgb, 37,99,235),0.05); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border);">
                <div class="flex-between mb-1">
                    <span style="font-weight: 600; font-size: 0.85rem;"><?= htmlspecialchars($r['titre']) ?></span>
                    <span style="color: <?= $color ?>; font-weight: bold; font-size: 0.9rem;"><?= $percent ?>%</span>
                </div>
                <div style="background: var(--border); height: 4px; border-radius: 2px; overflow: hidden;">
                    <div style="width: <?= $percent ?>%; height: 100%; background: <?= $color ?>;"></div>
                </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Formations Académiques -->
        <?php if (!empty($cv['educations'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:1rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">🎓 Éducation</h3>
          <div class="timeline">
            <?php foreach ($cv['educations'] as $edu): ?>
            <div class="timeline-item" style="padding-bottom:1rem">
              <div class="timeline-dot" style="background:var(--purple)"></div>
              <div style="font-weight:700;font-size:.9rem"><?= e($edu['diplome']) ?></div>
              <div style="color:var(--purple);font-size:.85rem"><?= e($edu['etablissement']) ?></div>
              <div style="font-size:.8rem;opacity:0.8"><?= e($edu['annee']) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Formations Complémentaires -->
        <?php if (!empty($cv['formations_complementaires'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:1rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">📚 Formations Compl.</h3>
          <?php foreach ($cv['formations_complementaires'] as $fc): ?>
          <div style="margin-bottom:.75rem;font-size:.9rem">
            <strong style="display:block"><?= e($fc['titre']) ?></strong>
            <span style="opacity:0.9"><?= e($fc['organisme']) ?></span> <span style="font-size:.8rem;opacity:0.7">(<?= e($fc['annee']) ?>)</span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Certifications -->
        <?php if (!empty($cv['certifications'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:1rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">📜 Certifications</h3>
          <?php foreach ($cv['certifications'] as $cert): ?>
          <div style="margin-bottom:.75rem;font-size:.9rem">
            <strong style="display:block"><?= e($cert['nom']) ?></strong>
            <span style="opacity:0.9"><?= e($cert['organisme']) ?></span> <span style="font-size:.8rem;opacity:0.7">(<?= e($cert['date']) ?>)</span>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Langues -->
        <?php if (!empty($cv['langues'])): ?>
        <div style="margin-bottom:1.75rem">
          <h3 style="color:var(--primary);margin-bottom:.75rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">🌐 Langues</h3>
          <div class="flex flex-col gap-2">
            <?php foreach ($cv['langues'] as $l): ?>
            <div style="display:flex;justify-content:space-between;font-size:.9rem">
              <strong><?= e($l['langue']) ?></strong> <span style="opacity:0.8"><?= e($l['niveau']) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Centres d'intérêt -->
        <?php if (!empty($cv['centres_interet'])): ?>
        <div>
          <h3 style="color:var(--primary);margin-bottom:.75rem;font-size:1rem;text-transform:uppercase;letter-spacing:.06em">❤️ Intérêts</h3>
          <p style="font-size:.9rem;line-height:1.6;margin:0"><?= e(implode(' • ', $cv['centres_interet'])) ?></p>
        </div>
        <?php endif; ?>

      </div> <!-- END Right Column -->
    </div> <!-- END Grid -->
  </div>
</div>
</div>

<?php 
// Helper pour grouper les réseaux
function current_user_reseaux($profil, $cvRes) {
  $out = [];
  if(!empty($profil['linkedin'])) $out[] = ['name'=>'LinkedIn', 'url'=>$profil['linkedin']];
  if(!empty($profil['github'])) $out[] = ['name'=>'GitHub', 'url'=>$profil['github']];
  if(!empty($profil['site_web'])) $out[] = ['name'=>'Site Web', 'url'=>$profil['site_web']];
  if(!empty($profil['portfolio'])) $out[] = ['name'=>'Portfolio', 'url'=>$profil['portfolio']];
  if(!empty($cvRes['kaggle'])) $out[] = ['name'=>'Kaggle', 'url'=>$cvRes['kaggle']];
  if(!empty($cvRes['stackoverflow'])) $out[] = ['name'=>'Stack Overflow', 'url'=>$cvRes['stackoverflow']];
  return $out;
}
require __DIR__ . '/../layout/footer.php'; 
?>
