<?php $pageTitle = $recl['titre']; require __DIR__ . '/../layout/header.php'; ?>
<div style="background:transparent;min-height:calc(100vh-68px);padding:2rem 0">
<div class="container-sm">
  <a href="<?= APP_URL ?>/?ctrl=reclamation&action=index" class="btn btn-ghost btn-sm mb-3">← <?= __('recl.title') ?></a>
  <div class="card animate-fade-in-up mb-3">
    <div class="flex-between flex-wrap gap-2 mb-2">
      <h2 style="font-size:1.2rem"><?= e($recl['titre']) ?></h2>
      <div class="flex gap-1">
        <span class="status-pill status-<?= $recl['statut'] ?>"><?= $recl['statut'] ?></span>
        <?php if (hasRole('admin','societe')): ?>
        <form method="POST" action="<?= APP_URL ?>/?ctrl=reclamation&action=updateStatut" style="display:inline">
          <?= csrf_field() ?><input type="hidden" name="id" value="<?= $recl['id'] ?>">
          <select name="statut" class="form-control" style="height:32px;padding:.2rem .6rem;font-size:.8rem" onchange="this.form.submit()">
            <?php foreach(['ouvert','en_cours','resolu','ferme'] as $s): ?><option value="<?= $s ?>" <?= $s===$recl['statut']?'selected':'' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
          </select>
        </form>
        <?php endif; ?>
        <?php if (hasRole('admin')): ?>
          <a href="<?= APP_URL ?>/?ctrl=reclamation&action=edit&id=<?= $recl['id'] ?>" class="btn btn-primary" style="height:32px;padding:.2rem .6rem;font-size:.8rem">✏️</a>
          <form method="POST" action="<?= APP_URL ?>/?ctrl=reclamation&action=delete" data-confirm-form="<?= __('gen.confirm_del') ?>" style="display:inline">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $recl['id'] ?>">
            <button class="btn btn-danger" style="height:32px;padding:.2rem .6rem;font-size:.8rem">🗑</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <div class="text-xs text-muted mb-2">👤 <?= e($recl['prenom'].' '.$recl['nom']) ?> · 📅 <?= date('d/m/Y H:i', strtotime($recl['created_at'])) ?></div>
    <p style="line-height:1.8"><?= nl2br(e($recl['description'])) ?></p>
  </div>
  <!-- Thread -->
  <?php if (!empty($recl['reponses'])): ?>
  <div style="display:flex;flex-direction:column;gap:.75rem;margin-bottom:1.5rem">
    <?php foreach ($recl['reponses'] as $rep): $isMe = $rep['auteur_id']===auth()['id']; ?>
    <div style="display:flex;gap:.75rem;flex-direction:<?= $isMe?'row-reverse':'row' ?>">
      <div style="width:36px;height:36px;border-radius:50%;background:<?= $isMe?'var(--primary)':'var(--purple)' ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0">
        <?= strtoupper(substr($rep['prenom'],0,1).substr($rep['nom'],0,1)) ?>
      </div>
      <div style="max-width:75%">
        <div class="card card-sm" style="background:<?= $isMe?'var(--primary-50)':'var(--bg)' ?>;border:1px solid <?= $isMe?'var(--primary-100)':'var(--border)' ?>">
          <div class="text-xs text-muted mb-1"><?= e($rep['prenom'].' '.$rep['nom']) ?> · <?= ucfirst($rep['role']) ?> · <?= date('d/m H:i', strtotime($rep['created_at'])) ?></div>
          <p style="font-size:.875rem;margin:0"><?= nl2br(e($rep['message'])) ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <!-- Reply -->
  <?php if (!in_array($recl['statut'], ['ferme','resolu'])): ?>
  <div class="card animate-fade-in-up delay-200">
    <h4 style="margin-bottom:1rem">💬 <?= __('recl.reply') ?></h4>
    <form method="POST" action="<?= APP_URL ?>/?ctrl=reclamation&action=reply" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="reclamation_id" value="<?= $recl['id'] ?>">
      <div class="form-group">
        <textarea name="message" class="form-control" rows="4" placeholder="<?= $lang==='fr'?'Votre message...':'Your message...' ?>"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">📨 <?= $lang==='fr'?'Envoyer':'Send' ?></button>
    </form>
  </div>
  <?php endif; ?>
</div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
