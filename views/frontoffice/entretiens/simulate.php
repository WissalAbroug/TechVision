<?php $pageTitle = __('entretien.start_sim'); require __DIR__ . '/../layout/header.php'; ?>

<div style="background:transparent;min-height:calc(100vh - 68px);padding:2rem 0">
<div class="container-sm">

  <!-- Header -->
  <div class="card animate-fade-in-up mb-3" style="background:linear-gradient(135deg,var(--purple),var(--primary));color:#fff">
    <div class="flex-between flex-wrap gap-2">
      <div>
        <h1 style="color:#fff;font-size:1.3rem">🎤 <?= __('entretien.simulate') ?></h1>
        <p style="color:rgba(255,255,255,.75);margin-top:.25rem"><?= e($entretien['offre_titre']) ?> — <?= e($entretien['nom_societe'] ?: $entretien['u_soc_nom'] ?? '') ?></p>
      </div>
      <div style="text-align:right">
        <div style="font-size:1.5rem;font-weight:800" id="timerDisplay">25:00</div>
        <div style="font-size:.75rem;opacity:.7"><?= $lang==='fr'?'Temps restant':'Time left' ?></div>
      </div>
    </div>
    <!-- Step progress -->
    <div class="progress-bar mt-2" style="background:rgba(255,255,255,.2)">
      <div class="progress-fill" id="questionProgress" style="background:#fff;width:0;transition:width .5s"></div>
    </div>
    <div style="font-size:.75rem;opacity:.7;margin-top:.35rem" id="questionCounter"><?= $lang==='fr'?'Question 0 sur':'Question 0 of' ?> <?= count($questions) ?></div>
  </div>

  <form method="POST" action="<?= APP_URL ?>/?ctrl=entretien&action=submitSimulation" novalidate id="simForm">
    <?= csrf_field() ?>
    <input type="hidden" name="entretien_id" value="<?= $entretien['id'] ?>">
    <input type="hidden" name="total_questions" value="<?= count($questions) ?>">

    <!-- Questions (one at a time) -->
    <?php foreach ($questions as $i => $q): ?>
    <div class="question-slide card animate-fade-in-up" id="q<?= $i ?>" style="display:<?= $i===0?'block':'none' ?>;margin-bottom:1rem">
      <div class="flex-between mb-2">
        <span class="badge badge-purple"><?= $q['categorie'] ?? 'Général' ?></span>
        <span class="badge badge-gray"><?= ucfirst($q['difficulte']) ?></span>
      </div>
      <h3 style="margin-bottom:1.25rem;line-height:1.6"><?= e($q['question']) ?></h3>
      <div class="form-group">
        <label class="form-label"><?= $lang==='fr'?'Votre réponse :':'Your answer:' ?></label>
        <textarea name="reponse_<?= $q['id'] ?>" class="form-control" rows="5"
                  placeholder="<?= $lang==='fr'?'Rédigez votre réponse ici...':'Write your answer here...' ?>"></textarea>
      </div>
      <div class="flex-between mt-2">
        <?php if ($i > 0): ?>
        <button type="button" class="btn btn-ghost btn-sm" onclick="goTo(<?= $i-1 ?>)">← <?= $lang==='fr'?'Précédent':'Previous' ?></button>
        <?php else: ?><div></div><?php endif; ?>
        <?php if ($i < count($questions)-1): ?>
        <button type="button" class="btn btn-primary btn-sm" onclick="goTo(<?= $i+1 ?>)"><?= $lang==='fr'?'Suivant':'Next' ?> →</button>
        <?php else: ?>
        <button type="submit" class="btn btn-success btn-sm" id="submitSimBtn">✅ <?= $lang==='fr'?'Terminer':'Finish' ?></button>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </form>

  <!-- Tips -->
  <div class="card animate-fade-in-up delay-300" style="background:var(--primary-50);border:1px solid var(--primary-100)">
    <h4 style="margin-bottom:.75rem;color:var(--primary)">💡 <?= $lang==='fr'?'Conseils':'Tips' ?></h4>
    <ul style="display:flex;flex-direction:column;gap:.4rem;font-size:.875rem;color:var(--text-secondary)">
      <li>✓ <?= $lang==='fr'?'Répondez clairement et de façon structurée (Situation, Tâche, Action, Résultat)':'Answer clearly and structured (Situation, Task, Action, Result)' ?></li>
      <li>✓ <?= $lang==='fr'?'Utilisez des exemples concrets de vos expériences':'Use concrete examples from your experience' ?></li>
      <li>✓ <?= $lang==='fr'?'Toutes les réponses sont optionnelles mais influencent votre score':'All answers are optional but influence your score' ?></li>
    </ul>
  </div>
</div>
</div>

<script>
let current = 0;
const total = <?= count($questions) ?>;
let timeLeft = 25 * 60;

function goTo(n) {
  document.getElementById('q' + current).style.display = 'none';
  document.getElementById('q' + n).style.display = 'block';
  current = n;
  document.getElementById('questionProgress').style.width = ((n+1)/total*100)+'%';
  document.getElementById('questionCounter').textContent = '<?= $lang==='fr'?'Question':'Question' ?> '+(n+1)+' <?= $lang==='fr'?'sur':'of' ?> '+total;
  window.scrollTo({top:0,behavior:'smooth'});
}

// Timer
const timer = setInterval(()=>{
  timeLeft--;
  if(timeLeft<=0){clearInterval(timer);document.getElementById('simForm').submit();return;}
  const m=Math.floor(timeLeft/60).toString().padStart(2,'0');
  const s=(timeLeft%60).toString().padStart(2,'0');
  document.getElementById('timerDisplay').textContent=m+':'+s;
  if(timeLeft<=300) document.getElementById('timerDisplay').style.color='#FCD34D';
  if(timeLeft<=60)  document.getElementById('timerDisplay').style.color='#F87171';
},1000);

goTo(0);
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
