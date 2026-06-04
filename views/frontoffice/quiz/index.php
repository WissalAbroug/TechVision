<?php $pageTitle = $lang==='fr'?'Tests de Compétences':'Skills Tests'; require __DIR__ . '/../layout/header.php'; ?>

<div class="container" style="max-width: 1000px; margin: 2rem auto;">
    <div class="flex-between mb-4">
        <div>
            <h1>🎯 <?= $lang==='fr'?'Mes Tests de Compétences':'My Skills Tests' ?></h1>
            <p class="text-muted"><?= $lang==='fr'?'Évaluez votre niveau et obtenez des recommandations.':'Evaluate your level and get recommendations.' ?></p>
        </div>
    </div>

    <div class="card mb-4" style="padding: 2rem; background: var(--bg-card); border: 2px dashed var(--primary); text-align: center;">
        <h3 class="mb-3">✨ <?= $lang==='fr'?'Générer un nouveau test sur-mesure':'Generate a new custom test' ?></h3>
        <form method="POST" action="<?= APP_URL ?>/?ctrl=quiz&action=generate" class="form-row" style="justify-content: center; align-items: end;">
            <?= csrf_field() ?>
            <div class="form-group" style="margin:0; min-width: 200px;">
                <label class="form-label"><?= $lang==='fr'?'Basé sur':'Based on' ?></label>
                <select name="type" class="form-control" id="quizTypeSelect" onchange="toggleCible()">
                    <option value="cv"><?= $lang==='fr'?'Mon CV (Mes compétences)':'My CV (My skills)' ?></option>
                    <option value="libre"><?= $lang==='fr'?'Thème Libre':'Free Theme' ?></option>
                </select>
            </div>
            <div class="form-group" style="margin:0; min-width: 250px; display: none;" id="cibleContainer">
                <label class="form-label"><?= $lang==='fr'?'Sujet (ex: PHP, React)':'Topic (ex: PHP, React)' ?></label>
                <input type="text" name="cible" class="form-control" placeholder="<?= $lang==='fr'?'Entrez un mot-clé...':'Enter a keyword...' ?>">
            </div>
            <button type="submit" class="btn btn-primary">🚀 <?= $lang==='fr'?'Générer le test':'Generate test' ?></button>
        </form>
    </div>

    <?php if (!empty($results)): ?>
    <h2 class="mb-3">🏆 <?= $lang==='fr'?'Mes Résultats':'My Results' ?></h2>
    <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 3rem;">
        <?php foreach ($results as $r): 
            $percent = round(($r['score'] / $r['total']) * 100);
            $color = $percent >= 80 ? '#10b981' : ($percent >= 50 ? '#f59e0b' : '#ef4444');
        ?>
        <div class="card" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <h4 style="margin:0"><?= htmlspecialchars($r['titre']) ?></h4>
                <div class="text-muted" style="font-size: 0.85rem;"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></div>
            </div>
            <div style="font-size: 2rem; font-weight: bold; color: <?= $color ?>; text-align: center;">
                <?= $percent ?>%
            </div>
            <div style="text-align: center; font-size: 0.9rem;">
                <?= $r['score'] ?> / <?= $r['total'] ?> <?= $lang==='fr'?'points':'points' ?>
            </div>
            <a href="<?= APP_URL ?>/?ctrl=quiz&action=result&id=<?= $r['id'] ?>" class="btn btn-outline btn-sm text-center"><?= $lang==='fr'?'Voir les détails':'View details' ?></a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<script>
function toggleCible() {
    const val = document.getElementById('quizTypeSelect').value;
    document.getElementById('cibleContainer').style.display = val === 'libre' ? 'block' : 'none';
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
