<?php $pageTitle = $lang==='fr'?'Résultat du Test':'Test Result'; require __DIR__ . '/../layout/header.php'; ?>

<div class="container" style="max-width: 800px; margin: 3rem auto;">
    
    <div class="card" style="padding: 3rem; text-align: center;">
        <h2 class="mb-2"><?= htmlspecialchars($quiz['titre']) ?></h2>
        <p class="text-muted mb-4"><?= $lang==='fr'?'Test passé le':'Test taken on' ?> <?= date('d/m/Y', strtotime($result['created_at'])) ?></p>
        
        <?php 
            $percent = round(($result['score'] / $result['total']) * 100);
            $color = $percent >= 80 ? '#10b981' : ($percent >= 50 ? '#f59e0b' : '#ef4444');
            $emoji = $percent >= 80 ? '🏆' : ($percent >= 50 ? '👍' : '📚');
        ?>
        
        <div style="font-size: 5rem; margin-bottom: 1rem;"><?= $emoji ?></div>
        <div style="font-size: 3rem; font-weight: 800; color: <?= $color ?>; margin-bottom: 0.5rem;">
            <?= $percent ?>%
        </div>
        <div style="font-size: 1.2rem; margin-bottom: 2rem;">
            <?= $lang==='fr'?'Score :':'Score:' ?> <strong><?= $result['score'] ?> / <?= $result['total'] ?></strong>
        </div>

        <?php 
            $recos = is_array($result['recommandations']) ? $result['recommandations'] : json_decode($result['recommandations'], true);
            if (!empty($recos)):
        ?>
        <div style="background: rgba(37,99,235,0.05); border: 1px solid var(--border); padding: 1.5rem; border-radius: 8px; text-align: left; margin-bottom: 2rem;">
            <h4 class="mb-3" style="color: var(--primary);">💡 <?= $lang==='fr'?'Recommandations personnalisées':'Personalized recommendations' ?></h4>
            <ul style="margin:0; padding-left: 1.5rem; line-height: 1.6;">
                <?php foreach ($recos as $r): ?>
                    <li><?= htmlspecialchars($r) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="flex-between" style="justify-content: center; gap: 1rem;">
            <a href="<?= APP_URL ?>/?ctrl=quiz&action=index" class="btn btn-outline"><?= $lang==='fr'?'&larr; Retour aux tests':'&larr; Back to tests' ?></a>
            <a href="<?= APP_URL ?>/?ctrl=cv&action=show" class="btn btn-primary"><?= $lang==='fr'?'Voir mon CV':'View my CV' ?></a>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
