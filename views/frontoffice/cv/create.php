<?php $pageTitle = __('cv.create'); require __DIR__ . '/../layout/header.php'; ?>

<style>
/* Wizard Styles */
.wizard-progress-container { margin-bottom: 2.5rem; }
.wizard-progress { display: flex; align-items: center; justify-content: space-between; position: relative; }
.wizard-progress::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 4px; background: var(--border); z-index: 1; transform: translateY(-50%); border-radius: 4px; }
.wizard-progress-bar { position: absolute; top: 50%; left: 0; height: 4px; background: var(--primary); z-index: 2; transform: translateY(-50%); transition: width 0.3s ease; border-radius: 4px; width: 0%; }
.wizard-step-node { width: 30px; height: 30px; border-radius: 50%; background: var(--bg-card); border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; z-index: 3; position: relative; font-weight: bold; color: var(--text-muted); font-size: .8rem; transition: all 0.3s ease; }
.wizard-step-node.active { border-color: var(--primary); color: var(--primary); box-shadow: 0 0 0 4px rgba(37,99,235,0.15); transform: scale(1.1); }
.wizard-step-node.completed { background: var(--primary); border-color: var(--primary); color: white; }
.step-text-indicator { text-align: center; margin-bottom: 1.5rem; font-weight: bold; color: var(--primary); font-size: 1.1rem; }

.wizard-step { display: none; animation: fadeIn 0.3s ease; }
.wizard-step.active { display: block; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.wizard-nav { display: flex; justify-content: space-between; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border); }
.wizard-nav .btn { min-width: 120px; }

/* Template picker existing styles */
.tpl-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; }
@media(max-width:900px) { .tpl-grid { grid-template-columns: repeat(3,1fr); } }
@media(max-width:580px) { .tpl-grid { grid-template-columns: repeat(2,1fr); } }
.tpl-card { position: relative; cursor: pointer; border-radius: 14px; overflow: hidden; border: 2px solid var(--border); background: var(--bg-card); transition: all .3s cubic-bezier(.34,1.56,.64,1); display: flex; flex-direction: column; user-select: none; }
.tpl-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: rgba(255,255,255,.18); }
.tpl-card.tpl-selected { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,240,255,.15), var(--shadow-lg); transform: translateY(-3px); }
body.theme-light .tpl-card.tpl-selected { box-shadow: 0 0 0 3px rgba(37,99,235,.15), var(--shadow-lg); }
.tpl-tag { position: absolute; top: 7px; left: 7px; z-index: 2; font-size: .58rem; font-weight: 800; padding: .15rem .5rem; border-radius: 99px; text-transform: uppercase; letter-spacing: .05em; backdrop-filter: blur(4px); }
.tpl-preview { height: 115px; position: relative; overflow: hidden; flex-shrink: 0; }
.tpl-preview-header { display: flex; align-items: center; gap: 6px; padding: 8px 10px; border-bottom: 1px solid rgba(255,255,255,.08); min-height: 40px; }
.tpl-avatar { width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0; }
.tpl-line { height: 4px; border-radius: 99px; display: block; }
.tpl-check { position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; background: #10b981; color: #fff; border-radius: 50%; font-size: .75rem; font-weight: 800; display: flex; align-items: center; justify-content: center; opacity: 0; transform: scale(.4); transition: all .25s cubic-bezier(.34,1.56,.64,1); box-shadow: 0 2px 8px rgba(16,185,129,.5); }
.tpl-card.tpl-selected .tpl-check { opacity: 1; transform: scale(1); }
.tpl-label-row { display: flex; align-items: center; gap: .5rem; padding: .65rem .8rem; }
.tpl-emoji { font-size: 1.1rem; }
.tpl-name { font-weight: 700; font-size: .82rem; color: var(--text-primary); line-height: 1.2; }
.tpl-desc { font-size: .67rem; color: var(--text-muted); }
.field-error { border-color: var(--danger) !important; box-shadow: 0 0 0 2px rgba(239,68,68,0.2) !important; }
</style>

<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container" style="max-width: 1100px; margin: 0 auto;">
  <div class="flex-between mb-4">
    <div>
      <h1 style="font-size:1.5rem">📄 <?= __('cv.create') ?></h1>
      <p><?= $lang==='fr'?'Remplissez votre CV étape par étape.':'Fill your resume step by step.' ?></p>
    </div>
  </div>

  <!-- Progress Bar -->
  <div class="wizard-progress-container">
    <div class="step-text-indicator" id="stepIndicatorText"><?= $lang==='fr'?'Étape 1 sur 14 : Choix du Style':'Step 1 of 14 : Style Selection' ?></div>
    <div class="wizard-progress">
      <div class="wizard-progress-bar" id="progressBar"></div>
      <?php for($i=1; $i<=14; $i++): ?>
        <div class="wizard-step-node" id="node-<?= $i ?>"><?= $i ?></div>
      <?php endfor; ?>
    </div>
  </div>

  <form method="POST" action="<?= APP_URL ?>/?ctrl=cv&action=store" novalidate id="cvForm">
    <?= csrf_field() ?>
    <input type="hidden" name="template" id="selectedTemplate" value="modern">

    <!-- STEP 1: Template -->
    <div class="wizard-step active" data-step="1" data-title="<?= $lang==='fr'?'Choix du Style':'Style Selection' ?>">
      <div class="card" style="padding:2rem">
        <div class="flex-between mb-3 flex-wrap gap-2">
          <div><h3 style="margin-bottom:.25rem">🎨 Choisissez votre style</h3></div>
          <span class="badge badge-primary" id="selectedBadge" style="font-size:.78rem;padding:.35rem .9rem">✨ Modern</span>
        </div>
        <div class="tpl-grid" id="templatePicker">
          <?php
          $templates = [
            'modern'    => ['label'=>'Modern', 'emoji'=>'🌐', 'desc'=>'Sombre & Gradient', 'tag'=>'Populaire', 'tagColor'=>'#00f0ff', 'bg'=>'linear-gradient(135deg,#06060c 0%,#1e1b4b 100%)', 'accent'=>'#00f0ff','accent2'=>'#8b5cf6','headerBg'=>'rgba(139,92,246,0.3)'],
            'classic'   => ['label'=>'Classic', 'emoji'=>'📋', 'desc'=>'Propre & Pro', 'tag'=>null, 'tagColor'=>'', 'bg'=>'linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%)', 'accent'=>'#1d4ed8','accent2'=>'#1e40af','headerBg'=>'#1d4ed8'],
            'creative'  => ['label'=>'Creative', 'emoji'=>'🎨', 'desc'=>'Coloré & Original', 'tag'=>'Tendance', 'tagColor'=>'#db2777', 'bg'=>'linear-gradient(135deg,#0f0022 0%,#1a0040 100%)', 'accent'=>'#fbbf24','accent2'=>'#db2777','headerBg'=>'linear-gradient(135deg,#7c3aed,#db2777)'],
            'minimal'   => ['label'=>'Minimal', 'emoji'=>'⬜', 'desc'=>'Épuré & Élégant', 'tag'=>null, 'tagColor'=>'', 'bg'=>'#f9fafb', 'accent'=>'#374151','accent2'=>'#6b7280','headerBg'=>'#f3f4f6'],
            'executive' => ['label'=>'Executive', 'emoji'=>'👔', 'desc'=>'Prestige & Or', 'tag'=>'Premium', 'tagColor'=>'#f59e0b', 'bg'=>'linear-gradient(135deg,#111827 0%,#1f2937 100%)', 'accent'=>'#f59e0b','accent2'=>'#d97706','headerBg'=>'#1f2937'],
          ];
          foreach ($templates as $key => $tpl): $isDefault = ($key === 'modern');
          ?>
          <label class="tpl-card <?= $isDefault ? 'tpl-selected' : '' ?>" data-template="<?= $key ?>" onclick="selectTemplate('<?= $key ?>','<?= htmlspecialchars($tpl['label']) ?>')">
            <?php if ($tpl['tag']): ?><div class="tpl-tag" style="background:<?= $tpl['tagColor'] ?>22;color:<?= $tpl['tagColor'] ?>;border:1px solid <?= $tpl['tagColor'] ?>55"><?= $tpl['tag'] ?></div><?php endif; ?>
            <div class="tpl-preview" style="background:<?= $tpl['bg'] ?>">
              <div class="tpl-preview-header" style="background:<?= $tpl['headerBg'] ?>">
                <div class="tpl-avatar" style="background:<?= $tpl['accent'] ?>;box-shadow:0 0 8px <?= $tpl['accent'] ?>66"></div>
                <div><div class="tpl-line" style="background:<?= $tpl['accent'] ?>;width:52px;margin-bottom:4px"></div><div class="tpl-line" style="background:<?= $tpl['accent2'] ?>88;width:36px;height:3px"></div></div>
              </div>
              <div style="padding:8px 10px;display:flex;flex-direction:column;gap:4px">
                <div class="tpl-line" style="background:<?= $tpl['accent'] ?>44;width:80%"></div><div class="tpl-line" style="background:<?= $tpl['accent'] ?>28;width:60%"></div><div class="tpl-line" style="background:<?= $tpl['accent'] ?>28;width:72%"></div>
                <div style="margin-top:3px;display:flex;gap:4px"><div style="height:12px;width:32px;border-radius:99px;background:<?= $tpl['accent'] ?>33"></div><div style="height:12px;width:28px;border-radius:99px;background:<?= $tpl['accent2'] ?>33"></div></div>
              </div>
              <div class="tpl-check">✓</div>
            </div>
            <div class="tpl-label-row">
              <span class="tpl-emoji"><?= $tpl['emoji'] ?></span>
              <div><div class="tpl-name"><?= $tpl['label'] ?></div><div class="tpl-desc"><?= $tpl['desc'] ?></div></div>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
        <div class="wizard-nav">
          <div></div> <!-- Empty div to push next to right -->
          <button type="button" class="btn btn-primary" onclick="nextStep(1)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 2: Infos -->
    <div class="wizard-step" data-step="2" data-title="<?= $lang==='fr'?'Informations Personnelles':'Personal Information' ?>">
      <div class="card">
        <h3 class="mb-3">📋 Informations personnelles</h3>
        <div class="form-row">
          <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Nom':'Last Name' ?> <span class="req">*</span></label><input type="text" name="nom" class="form-control" value="<?= e($user['nom']??'') ?>" required></div>
          <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Prénom':'First Name' ?> <span class="req">*</span></label><input type="text" name="prenom" class="form-control" value="<?= e($user['prenom']??'') ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Email <span class="req">*</span></label><input type="email" class="form-control" value="<?= e($user['email']??'') ?>" readonly style="background:var(--bg);color:var(--text-muted)"></div>
          <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Téléphone':'Phone' ?> <span class="req">*</span></label><input type="text" name="telephone" class="form-control" value="<?= e($profil['telephone']??'') ?>" required></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><?= $lang==='fr'?'Ville / Pays':'City / Country' ?> <span class="req">*</span></label>
            <div style="display:flex;gap:.5rem">
              <input type="text" name="ville" class="form-control" placeholder="<?= $lang==='fr'?'Ville':'City' ?>" value="<?= e($profil['ville']??'') ?>" required>
              <input type="text" name="pays" class="form-control" placeholder="<?= $lang==='fr'?'Pays':'Country' ?>" value="<?= e($profil['pays']??'Maroc') ?>" required>
            </div>
          </div>
          <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Titre professionnel':'Professional Title' ?> <span class="req">*</span></label><input type="text" name="titre" class="form-control" placeholder="<?= $lang==='fr'?'Ex: Développeur Web':'Ex: Web Developer' ?>" required></div>
        </div>
        <hr style="margin:1rem 0;border-top:1px dashed var(--border)"><h4 style="font-size:.9rem;color:var(--text-muted);margin-bottom:1rem"><?= $lang==='fr'?'Facultatif':'Optional' ?></h4>
        <div class="form-row">
          <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Date de naissance':'Date of Birth' ?></label><input type="date" name="date_naissance" class="form-control" value="<?= e($profil['date_naissance']??'') ?>"></div>
          <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Nationalité':'Nationality' ?></label><input type="text" name="nationalite" class="form-control" value="<?= e($profil['nationalite']??'') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Adresse':'Address' ?></label><input type="text" name="adresse" class="form-control" value="<?= e($profil['adresse']??'') ?>"></div>
          <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Permis':'Driver License' ?></label><input type="text" name="permis" class="form-control" value="<?= e($profil['permis']??'') ?>"></div>
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(2)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(2)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 3: Résumé -->
    <div class="wizard-step" data-step="3" data-title="<?= $lang==='fr'?'Résumé Profil':'Profile Summary' ?>">
      <div class="card">
        <h3 class="mb-3">📝 Résumé professionnel</h3>
        <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Description':'Description' ?> <span class="req">*</span></label><textarea name="resume" class="form-control" rows="5" placeholder="<?= $lang==='fr'?'Présentez-vous brièvement...':'Briefly introduce yourself...' ?>" required></textarea></div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(3)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(3)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 4: Formations -->
    <div class="wizard-step" data-step="4" data-title="<?= $lang==='fr'?'Formations Académiques':'Education' ?>">
      <div class="card">
        <div class="flex-between mb-3"><h3>🎓 <?= $lang==='fr'?'Formations':'Education' ?> <span class="req" style="font-size:.75rem">*</span></h3><button type="button" class="btn btn-outline btn-sm" data-add-row="eduContainer">+ <?= $lang==='fr'?'Ajouter':'Add' ?></button></div>
        <div id="eduContainer">
          <div class="dynamic-row" style="border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:.75rem;position:relative">
            <button type="button" data-remove-row style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;color:var(--danger)">✕</button>
            <div class="form-row">
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Diplôme':'Degree' ?> <span class="req">*</span></label><input type="text" name="edu_diplome[]" class="form-control" required></div>
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Établissement':'Institution' ?> <span class="req">*</span></label><input type="text" name="edu_etablissement[]" class="form-control" required></div>
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Année':'Year' ?> <span class="req">*</span></label><input type="text" name="edu_annee[]" class="form-control" required></div>
            </div>
          </div>
        </div>
        <template id="eduContainer_template">
          <div class="dynamic-row" style="border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:.75rem;position:relative">
            <button type="button" data-remove-row style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;color:var(--danger)">✕</button>
            <div class="form-row">
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Diplôme':'Degree' ?> <span class="req">*</span></label><input type="text" name="edu_diplome[]" class="form-control" required></div>
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Établissement':'Institution' ?> <span class="req">*</span></label><input type="text" name="edu_etablissement[]" class="form-control" required></div>
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Année':'Year' ?> <span class="req">*</span></label><input type="text" name="edu_annee[]" class="form-control" required></div>
            </div>
          </div>
        </template>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(4)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(4)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 5: Expériences -->
    <div class="wizard-step" data-step="5" data-title="<?= $lang==='fr'?'Expériences Pro.':'Experience' ?>">
      <div class="card">
        <div class="flex-between mb-3"><h3>💼 <?= $lang==='fr'?'Expériences':'Experience' ?> <span style="font-size:.75rem;color:var(--text-muted);font-weight:normal">🔹 <?= $lang==='fr'?'Facultatif':'Optional' ?></span></h3><button type="button" class="btn btn-outline btn-sm" data-add-row="expContainer">+ <?= $lang==='fr'?'Ajouter':'Add' ?></button></div>
        <div id="expContainer"></div>
        <template id="expContainer_template">
          <div class="dynamic-row" style="border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:.75rem;position:relative">
            <button type="button" data-remove-row style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;color:var(--danger)">✕</button>
            <div class="form-row">
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Poste':'Job Title' ?></label><input type="text" name="exp_poste[]" class="form-control"></div>
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Entreprise':'Company' ?></label><input type="text" name="exp_societe[]" class="form-control"></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Début':'Start Date' ?></label><input type="text" name="exp_debut[]" class="form-control" placeholder="<?= $lang==='fr'?'Ex: Jan 2020':'Ex: Jan 2020' ?>"></div>
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Fin':'End Date' ?></label><input type="text" name="exp_fin[]" class="form-control" placeholder="<?= $lang==='fr'?'Ex: Déc 2022':'Ex: Dec 2022' ?>"></div>
            </div>
            <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Description':'Description' ?></label><textarea name="exp_desc[]" class="form-control" rows="3"></textarea></div>
          </div>
        </template>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(5)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(5)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 6: Compétences (Hard) -->
    <div class="wizard-step" data-step="6" data-title="Hard Skills">
      <div class="card">
        <h3 class="mb-3">🛠️ Compétences techniques <span class="req" style="font-size:.75rem">*</span></h3>
        <div class="form-group">
          <div class="chip-input-container">
            <div class="chips-container" id="skillsChips">
              <input type="text" class="chip-text-input" placeholder="<?= $lang==='fr'?'Ex: PHP, React, Java... (Appuyez sur Entrée)':'Ex: PHP, React, Java... (Press Enter)' ?>">
            </div>
          </div>
          <input type="hidden" name="competences_raw" id="competencesRaw" required>
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(6)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(6)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 7: Soft Skills -->
    <div class="wizard-step" data-step="7" data-title="Soft Skills">
      <div class="card">
        <h3 class="mb-3">🤝 Compétences comportementales <span style="font-size:.75rem;color:var(--text-muted);font-weight:normal">🔹 <?= $lang==='fr'?'Facultatif':'Optional' ?></span></h3>
        <div class="form-group">
          <div class="chip-input-container">
            <div class="chips-container" id="softChips">
              <input type="text" class="chip-text-input" placeholder="<?= $lang==='fr'?'Ex: Leadership, Travail en équipe... (Appuyez sur Entrée)':'Ex: Leadership, Teamwork... (Press Enter)' ?>">
            </div>
          </div>
          <input type="hidden" name="soft_skills_raw" id="softSkillsRaw">
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(7)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(7)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 8: Langues -->
    <div class="wizard-step" data-step="8" data-title="<?= $lang==='fr'?'Langues':'Languages' ?>">
      <div class="card">
        <div class="flex-between mb-3"><h3>🌍 <?= $lang==='fr'?'Langues':'Languages' ?> <span class="req" style="font-size:.75rem">*</span></h3><button type="button" class="btn btn-outline btn-sm" data-add-row="langContainer">+ <?= $lang==='fr'?'Ajouter':'Add' ?></button></div>
        <div id="langContainer">
          <div class="dynamic-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem">
            <div style="margin:0"><label class="form-label"><?= $lang==='fr'?'Langue':'Language' ?></label><input type="text" name="langue_nom[]" class="form-control" required></div>
            <div style="margin:0"><label class="form-label"><?= $lang==='fr'?'Niveau':'Level' ?></label><select name="langue_niveau[]" class="form-control" required><option><?= $lang==='fr'?'Natif':'Native' ?></option><option><?= $lang==='fr'?'Bilingue':'Bilingual' ?></option><option><?= $lang==='fr'?'Courant':'Fluent' ?></option><option><?= $lang==='fr'?'Intermédiaire':'Intermediate' ?></option><option><?= $lang==='fr'?'Débutant':'Beginner' ?></option></select></div>
            <button type="button" data-remove-row class="btn btn-ghost btn-sm" style="color:var(--danger)">✕</button>
          </div>
        </div>
        <template id="langContainer_template">
          <div class="dynamic-row" style="display:grid;grid-template-columns:1fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem">
            <div style="margin:0"><label class="form-label"><?= $lang==='fr'?'Langue':'Language' ?></label><input type="text" name="langue_nom[]" class="form-control" required></div>
            <div style="margin:0"><label class="form-label"><?= $lang==='fr'?'Niveau':'Level' ?></label><select name="langue_niveau[]" class="form-control" required><option><?= $lang==='fr'?'Natif':'Native' ?></option><option><?= $lang==='fr'?'Bilingue':'Bilingual' ?></option><option><?= $lang==='fr'?'Courant':'Fluent' ?></option><option><?= $lang==='fr'?'Intermédiaire':'Intermediate' ?></option><option><?= $lang==='fr'?'Débutant':'Beginner' ?></option></select></div>
            <button type="button" data-remove-row class="btn btn-ghost btn-sm" style="color:var(--danger)">✕</button>
          </div>
        </template>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(8)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(8)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 9: Certifications -->
    <div class="wizard-step" data-step="9" data-title="<?= $lang==='fr'?'Certifications':'Certifications' ?>">
      <div class="card">
        <div class="flex-between mb-3"><h3>📜 <?= $lang==='fr'?'Certifications':'Certifications' ?> <span style="font-size:.75rem;color:var(--text-muted);font-weight:normal">🔹 <?= $lang==='fr'?'Facultatif':'Optional' ?></span></h3><button type="button" class="btn btn-outline btn-sm" data-add-row="certContainer">+ <?= $lang==='fr'?'Ajouter':'Add' ?></button></div>
        <div id="certContainer"></div>
        <template id="certContainer_template">
          <div class="dynamic-row" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:.75rem;align-items:end;margin-bottom:.75rem">
            <div style="margin:0"><label class="form-label"><?= $lang==='fr'?'Nom':'Name' ?></label><input type="text" name="cert_nom[]" class="form-control"></div>
            <div style="margin:0"><label class="form-label"><?= $lang==='fr'?'Organisme':'Organization' ?></label><input type="text" name="cert_organisme[]" class="form-control"></div>
            <div style="margin:0"><label class="form-label"><?= $lang==='fr'?'Date':'Date' ?></label><input type="text" name="cert_date[]" class="form-control"></div>
            <button type="button" data-remove-row class="btn btn-ghost btn-sm" style="color:var(--danger)">✕</button>
          </div>
        </template>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(9)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(9)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 10: Projets -->
    <div class="wizard-step" data-step="10" data-title="<?= $lang==='fr'?'Projets':'Projects' ?>">
      <div class="card">
        <div class="flex-between mb-3"><h3>🚀 <?= $lang==='fr'?'Projets':'Projects' ?> <span style="font-size:.75rem;color:var(--text-muted);font-weight:normal">🔹 <?= $lang==='fr'?'Facultatif':'Optional' ?></span></h3><button type="button" class="btn btn-outline btn-sm" data-add-row="projContainer">+ <?= $lang==='fr'?'Ajouter':'Add' ?></button></div>
        <div id="projContainer"></div>
        <template id="projContainer_template">
          <div class="dynamic-row" style="border:1px solid var(--border);border-radius:var(--radius);padding:1.25rem;margin-bottom:.75rem;position:relative">
            <button type="button" data-remove-row style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;color:var(--danger)">✕</button>
            <div class="form-row">
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Titre':'Title' ?></label><input type="text" name="proj_titre[]" class="form-control"></div>
              <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Rôle':'Role' ?></label><input type="text" name="proj_role[]" class="form-control"></div>
            </div>
            <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Technologies':'Technologies' ?></label><input type="text" name="proj_techs[]" class="form-control"></div>
            <div class="form-group"><label class="form-label"><?= $lang==='fr'?'Description':'Description' ?></label><textarea name="proj_desc[]" class="form-control" rows="2"></textarea></div>
          </div>
        </template>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(10)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(10)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 11: Réalisations -->
    <div class="wizard-step" data-step="11" data-title="<?= $lang==='fr'?'Réalisations':'Achievements' ?>">
      <div class="card">
        <div class="flex-between mb-3"><h3>🏆 <?= $lang==='fr'?'Réalisations':'Achievements' ?> <span style="font-size:.75rem;color:var(--text-muted);font-weight:normal">🔹 <?= $lang==='fr'?'Facultatif':'Optional' ?></span></h3><button type="button" class="btn btn-outline btn-sm" data-add-row="realContainer">+ <?= $lang==='fr'?'Ajouter':'Add' ?></button></div>
        <div id="realContainer"></div>
        <template id="realContainer_template">
          <div class="dynamic-row" style="display:grid;grid-template-columns:1fr 100px 2fr auto;gap:.75rem;align-items:start;margin-bottom:.75rem">
            <div style="margin:0"><input type="text" name="real_titre[]" class="form-control" placeholder="<?= $lang==='fr'?'Titre':'Title' ?>"></div>
            <div style="margin:0"><input type="text" name="real_annee[]" class="form-control" placeholder="<?= $lang==='fr'?'Année':'Year' ?>"></div>
            <div style="margin:0"><input type="text" name="real_desc[]" class="form-control" placeholder="<?= $lang==='fr'?'Description':'Description' ?>"></div>
            <button type="button" data-remove-row class="btn btn-ghost btn-sm" style="color:var(--danger)">✕</button>
          </div>
        </template>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(11)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(11)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 12: Formations Complémentaires -->
    <div class="wizard-step" data-step="12" data-title="<?= $lang==='fr'?'Formations Compl.':'Additional Training' ?>">
      <div class="card">
        <div class="flex-between mb-3"><h3>📚 <?= $lang==='fr'?'Formations complémentaires':'Additional Training' ?> <span style="font-size:.75rem;color:var(--text-muted);font-weight:normal">🔹 <?= $lang==='fr'?'Facultatif':'Optional' ?></span></h3><button type="button" class="btn btn-outline btn-sm" data-add-row="formcContainer">+ <?= $lang==='fr'?'Ajouter':'Add' ?></button></div>
        <div id="formcContainer"></div>
        <template id="formcContainer_template">
          <div class="dynamic-row" style="display:grid;grid-template-columns:1fr 1fr 100px auto;gap:.75rem;align-items:start;margin-bottom:.75rem">
            <div style="margin:0"><input type="text" name="formc_titre[]" class="form-control" placeholder="<?= $lang==='fr'?'Titre':'Title' ?>"></div>
            <div style="margin:0"><input type="text" name="formc_organisme[]" class="form-control" placeholder="<?= $lang==='fr'?'Organisme':'Organization' ?>"></div>
            <div style="margin:0"><input type="text" name="formc_annee[]" class="form-control" placeholder="<?= $lang==='fr'?'Année':'Year' ?>"></div>
            <button type="button" data-remove-row class="btn btn-ghost btn-sm" style="color:var(--danger)">✕</button>
          </div>
        </template>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(12)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(12)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 13: Réseaux -->
    <div class="wizard-step" data-step="13" data-title="<?= $lang==='fr'?'Réseaux Pro.':'Professional Networks' ?>">
      <div class="card">
        <h3 class="mb-3">🌐 Réseaux professionnels <span style="font-size:.75rem;color:var(--text-muted);font-weight:normal">🔹 <?= $lang==='fr'?'Facultatif':'Optional' ?></span></h3>
        <div class="form-row">
          <div class="form-group"><label class="form-label">LinkedIn</label><input type="url" name="res_linkedin" class="form-control" value="<?= e($profil['linkedin']??'') ?>"></div>
          <div class="form-group"><label class="form-label">GitHub</label><input type="url" name="res_github" class="form-control" value="<?= e($profil['github']??'') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Site Web</label><input type="url" name="site_web" class="form-control" value="<?= e($profil['site_web']??'') ?>"></div>
          <div class="form-group"><label class="form-label">Portfolio</label><input type="url" name="portfolio" class="form-control" value="<?= e($profil['portfolio']??'') ?>"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Kaggle</label><input type="url" name="res_kaggle" class="form-control"></div>
          <div class="form-group"><label class="form-label">Stack Overflow</label><input type="url" name="res_stackoverflow" class="form-control"></div>
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(13)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="button" class="btn btn-primary" onclick="nextStep(13)"><?= $lang==='fr'?'Suivant':'Next' ?> &rarr;</button>
        </div>
      </div>
    </div>

    <!-- STEP 14: Intérêts & Submit -->
    <div class="wizard-step" data-step="14" data-title="<?= $lang==='fr'?"Centres d'intérêt":"Interests" ?>">
      <div class="card">
        <h3 class="mb-3">❤️ Centres d'intérêt <span style="font-size:.75rem;color:var(--text-muted);font-weight:normal">🔹 <?= $lang==='fr'?'Facultatif':'Optional' ?></span></h3>
        <div class="form-group">
          <div class="chip-input-container">
            <div class="chips-container" id="interetsChips">
              <input type="text" class="chip-text-input" placeholder="<?= $lang==='fr'?'Ex: Lecture, Sport... (Appuyez sur Entrée)':'Ex: Reading, Sports... (Press Enter)' ?>">
            </div>
          </div>
          <input type="hidden" name="interets_raw" id="interetsRaw">
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-outline" onclick="prevStep(14)">&larr; <?= $lang==='fr'?'Précédent':'Previous' ?></button>
          <button type="submit" class="btn btn-primary btn-lg" onclick="return validateFinalStep()">💾 <?= $lang==='fr'?'Créer le CV':'Create Resume' ?></button>
        </div>
      </div>
    </div>

  </form>
</div>
</div>

<script>
// Wizard Logic
let currentStep = 1;
const totalSteps = 14;

function updateWizardUI() {
  // Update Steps visibility
  document.querySelectorAll('.wizard-step').forEach(el => {
    el.classList.remove('active');
    if (parseInt(el.getAttribute('data-step')) === currentStep) {
      el.classList.add('active');
      const title = el.getAttribute('data-title');
      const stepText = '<?= $lang==='fr'?'Étape':'Step' ?>';
      const ofText = '<?= $lang==='fr'?'sur':'of' ?>';
      document.getElementById('stepIndicatorText').textContent = `${stepText} ${currentStep} ${ofText} ${totalSteps} : ${title}`;
    }
  });

  // Update Progress Bar
  const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
  document.getElementById('progressBar').style.width = progressPercent + '%';

  // Update Nodes
  for (let i = 1; i <= totalSteps; i++) {
    const node = document.getElementById('node-' + i);
    node.classList.remove('active', 'completed');
    if (i < currentStep) node.classList.add('completed');
    if (i === currentStep) node.classList.add('active');
  }
}

function validateStep(step) {
  const stepEl = document.querySelector(`.wizard-step[data-step="${step}"]`);
  const inputs = stepEl.querySelectorAll('input[required], textarea[required], select[required]');
  let isValid = true;
  
  inputs.forEach(input => {
    if (!input.value.trim()) {
      isValid = false;
      input.classList.add('field-error');
    } else {
      input.classList.remove('field-error');
    }
  });

  // Special check for hidden chips required fields
  const hiddenRequired = stepEl.querySelectorAll('input[type="hidden"][required]');
  hiddenRequired.forEach(hidden => {
    if (!hidden.value.trim()) {
      isValid = false;
      const container = hidden.previousElementSibling;
      if(container) container.classList.add('field-error');
    } else {
      const container = hidden.previousElementSibling;
      if(container) container.classList.remove('field-error');
    }
  });

  if (!isValid) alert('<?= $lang==='fr'?"Veuillez remplir tous les champs obligatoires marqués d\'une étoile (*).":"Please fill all required fields marked with an asterisk (*)." ?>');
  return isValid;
}

function nextStep(step) {
  if (validateStep(step)) {
    if (currentStep < totalSteps) {
      currentStep++;
      updateWizardUI();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }
}

function prevStep(step) {
  if (currentStep > 1) {
    currentStep--;
    updateWizardUI();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

function validateFinalStep() {
  return validateStep(14);
}

// Remove error on input
document.addEventListener('input', e => {
  if(e.target.classList.contains('field-error')) {
    e.target.classList.remove('field-error');
  }
});

// Chips Logic
function initChipInput(containerId, hiddenInputId) {
  const container = document.getElementById(containerId);
  const hidden = document.getElementById(hiddenInputId);
  if(!container || !hidden) return;
  const input = container.querySelector('.chip-text-input');
  let tags = hidden.value ? hidden.value.split(',').map(t=>t.trim()).filter(Boolean) : [];

  function render() {
    container.querySelectorAll('.chip').forEach(c => c.remove());
    tags.forEach((tag, i) => {
      const el = document.createElement('div');
      el.className = 'chip';
      el.innerHTML = `${tag} <span class="chip-remove" data-index="${i}">&times;</span>`;
      container.insertBefore(el, input);
    });
    hidden.value = tags.join(',');
    if(hidden.value) container.parentElement.classList.remove('field-error');
  }

  input.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const val = input.value.trim();
      if (val && !tags.includes(val)) { tags.push(val); input.value = ''; render(); }
    } else if (e.key === 'Backspace' && !input.value && tags.length) {
      tags.pop(); render();
    }
  });

  container.addEventListener('click', e => {
    if (e.target.classList.contains('chip-remove')) {
      tags.splice(e.target.dataset.index, 1);
      render();
    } else {
      input.focus();
    }
  });
  render();
}
initChipInput('skillsChips', 'competencesRaw');
initChipInput('softChips', 'softSkillsRaw');
initChipInput('interetsChips', 'interetsRaw');

// Dynamic row logic
document.addEventListener('click', function(e) {
  if(e.target.closest('[data-add-row]')){
    const btn = e.target.closest('[data-add-row]');
    const targetId = btn.getAttribute('data-add-row');
    const container = document.getElementById(targetId);
    const tpl = document.getElementById(targetId + '_template');
    if(tpl && container){
      container.appendChild(tpl.content.cloneNode(true));
    }
  }
  if(e.target.closest('[data-remove-row]')){
    e.target.closest('.dynamic-row').remove();
  }
});

// Template Picker Logic
function selectTemplate(key, label) {
  document.getElementById('selectedTemplate').value = key;
  document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('tpl-selected'));
  const selected = document.querySelector(`.tpl-card[data-template="${key}"]`);
  if (selected) selected.classList.add('tpl-selected');
  const badge = document.getElementById('selectedBadge');
  if (badge && label) badge.textContent = '✨ ' + label;
}

// Init UI
updateWizardUI();
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
