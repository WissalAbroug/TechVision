<?php
/**
 * Setup Script — PathFinder AI
 * URL : http://localhost/pathfinder ai/setup.php?key=pathfinder_setup_2024
 * ⚠️  Supprimer ce fichier après installation !
 */

define('SETUP_KEY', 'pathfinder_setup_2024');
if (!isset($_GET['key']) || $_GET['key'] !== SETUP_KEY) {
    die('<h2>403 Forbidden</h2><p>Ajoutez <code>?key=pathfinder_setup_2024</code> à l\'URL.</p>');
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$pdo     = Database::getInstance();
$errors  = [];
$success = [];

// ── 1. Créer les tables ────────────────────────────────────
try {
    $sql = file_get_contents(__DIR__ . '/database/pathfinder.sql');
    $pdo->exec($sql);
    $success[] = '✅ Tables créées / vérifiées.';
} catch (PDOException $e) {
    $errors[] = '❌ Schéma SQL : ' . $e->getMessage();
}

// ── 2. Insérer les données seed ────────────────────────────
try {
    $seedFile = __DIR__ . '/database/seed.sql';
    if (file_exists($seedFile)) {
        $seed = file_get_contents($seedFile);
        $pdo->exec($seed);
        $success[] = '✅ Données de démonstration insérées.';
    } else {
        $errors[] = '⚠️ Fichier seed.sql introuvable.';
    }
} catch (PDOException $e) {
    $errors[] = '❌ Seed SQL : ' . $e->getMessage();
}

// ── 3. Créer l'administrateur (si seed échoue) ─────────────
try {
    $existing = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
    if ($existing == 0) {
        $hash = password_hash('Admin@2024', PASSWORD_BCRYPT);
        $pdo->prepare(
            "INSERT INTO users (nom, prenom, email, password, role, statut) VALUES (?,?,?,?,?,?)"
        )->execute(['Admin', 'PathFinder', 'admin@pathfinder.ma', $hash, 'admin', 'actif']);
        $success[] = '✅ Admin créé : admin@pathfinder.ma / Admin@2024';
    } else {
        $success[] = '✅ Admin déjà présent.';
    }
} catch (PDOException $e) {
    $errors[] = '❌ Admin : ' . $e->getMessage();
}

// ── 4. Dossier uploads ─────────────────────────────────────
$uploadDir = __DIR__ . '/public/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
    $success[] = '✅ Dossier uploads/ créé.';
} else {
    $success[] = '✅ Dossier uploads/ existe déjà.';
}

// ── 5. Afficher les comptes créés ──────────────────────────
try {
    $users = $pdo->query(
        "SELECT prenom, nom, email, role FROM users ORDER BY id"
    )->fetchAll();
} catch (PDOException $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>PathFinder AI — Setup</title>
  <style>
    *{box-sizing:border-box}
    body{font-family:'Segoe UI',sans-serif;margin:0;padding:2rem;background:linear-gradient(135deg,#EFF6FF,#F5F3FF);min-height:100vh}
    .wrap{max-width:760px;margin:0 auto}
    h1{color:#2563EB;font-size:1.8rem;margin-bottom:0}
    .sub{color:#64748B;margin-bottom:1.5rem;font-size:.95rem}
    .box{background:#fff;border-radius:16px;padding:1.5rem 2rem;box-shadow:0 4px 24px #2563eb15;margin-bottom:1.5rem}
    ul{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.4rem}
    li{padding:.55rem 1rem;border-radius:8px;font-size:.9rem}
    .ok{background:#DCFCE7;color:#166534}
    .err{background:#FEE2E2;color:#991B1B}
    table{width:100%;border-collapse:collapse;font-size:.875rem}
    th{text-align:left;padding:.6rem .75rem;background:#F8FAFC;color:#64748B;border-bottom:2px solid #E2E8F0}
    td{padding:.55rem .75rem;border-bottom:1px solid #F1F5F9}
    .role{display:inline-block;padding:.2rem .7rem;border-radius:99px;font-size:.75rem;font-weight:700}
    .admin{background:#FEE2E2;color:#DC2626}
    .societe{background:#F3E8FF;color:#7C3AED}
    .candidat{background:#DBEAFE;color:#1D4ED8}
    .btn{display:inline-block;margin-top:1.5rem;padding:.85rem 2.5rem;background:linear-gradient(135deg,#2563EB,#7C3AED);color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:1rem;letter-spacing:.02em}
    .warn{background:#FFFBEB;border:1px solid #FCD34D;color:#92400E;padding:1rem;border-radius:10px;margin-top:1rem;font-size:.875rem}
  </style>
</head>
<body>
<div class="wrap">
  <h1>🚀 PathFinder AI — Installation</h1>
  <p class="sub">Script d'initialisation de la base de données</p>

  <div class="box">
    <h3 style="margin-top:0">Résultat de l'installation</h3>
    <ul>
      <?php foreach ($success as $s): ?>
        <li class="ok"><?= htmlspecialchars($s) ?></li>
      <?php endforeach; ?>
      <?php foreach ($errors as $err): ?>
        <li class="err"><?= htmlspecialchars($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <?php if (!empty($users)): ?>
  <div class="box">
    <h3 style="margin-top:0">👥 Comptes disponibles</h3>
    <table>
      <thead>
        <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Mot de passe</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
          <td><code><?= htmlspecialchars($u['email']) ?></code></td>
          <td><span class="role <?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
          <td><code>Admin123!</code> <small style="color:#94A3B8">(hash bcrypt)</small></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="warn">
      ⚠️ <strong>Sécurité :</strong> Le mot de passe dans seed.sql est un hash bcrypt de <code>password</code> (Laravel default).
      Utilisez <strong>setup.php</strong> pour générer les vrais hash.<br>
      Après installation, <strong>supprimez ce fichier</strong> setup.php.
    </div>
  </div>
  <?php endif; ?>

  <?php if (empty($errors)): ?>
    <a class="btn" href="<?= APP_URL ?>/">🎯 Accéder à PathFinder AI</a>
  <?php else: ?>
    <p style="color:#DC2626">⚠️ Corrigez les erreurs ci-dessus puis relancez.</p>
    <a class="btn" href="?key=<?= SETUP_KEY ?>" style="background:#DC2626">🔄 Relancer</a>
  <?php endif; ?>
</div>
</body>
</html>
