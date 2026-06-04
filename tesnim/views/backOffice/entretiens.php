<?php
// Définir le chemin de base pour les assets
$base_url = dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))) . '/';

include '../../controller/EntretienController.php';
require_once __DIR__ . '/../../model/Entretien.php';

$error = "";
$success = "";
$entretienC = new EntretienController();

// Traitement ajout entretien
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    if (
        !empty($_POST["type"]) && !empty($_POST["date"]) && 
        !empty($_POST["heure"]) && !empty($_POST["places"])
    ) {
        $entretien = new Entretien(
            null,
            $_POST['type'],
            new DateTime($_POST['date']),
            $_POST['heure'],
            (int)$_POST['places'],
            0
        );
        $entretienC->addEntretien($entretien);
        $success = "Session ajoutée avec succès !";
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}

// Traitement modification entretien
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    if (
        !empty($_POST["id"]) && !empty($_POST["type"]) && 
        !empty($_POST["date"]) && !empty($_POST["heure"]) && 
        !empty($_POST["places"])
    ) {
        $entretien = new Entretien(
            (int)$_POST['id'],
            $_POST['type'],
            new DateTime($_POST['date']),
            $_POST['heure'],
            (int)$_POST['places'],
            (int)$_POST['places_prises']
        );
        $entretienC->updateEntretien($entretien, $_POST['id']);
        $success = "Session modifiée avec succès !";
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}

// Suppression
if (isset($_GET['delete'])) {
    $entretienC->deleteEntretien($_GET['delete']);
    $success = "Session supprimée avec succès !";
}

// Liste des entretiens
$list = $entretienC->listEntretiens();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Sessions - Admin</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assests/css/back.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <h2>🎯 Admin Panel</h2>
            <p>Gestion des Entretiens</p>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i>📊</i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="entretiens.php" class="nav-link active">
                    <i>📅</i>
                    <span>Gestion Sessions</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="reservations.php" class="nav-link">
                    <i>📝</i>
                    <span>Réservations</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../frontOffice/quiz.php" class="nav-link">
                    <i>🧠</i>
                    <span>Quiz Entretien</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../frontOffice/index.php" class="nav-link">
                    <i>🌐</i>
                    <span>Site Public</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="admin-header">
            <div class="page-title">
                <h1>Gestion des Sessions</h1>
                <p>Créez et gérez les sessions d'entretien</p>
            </div>
            <div class="admin-actions">
                <button onclick="openAddModal()" class="btn btn-primary">+ Nouvelle Session</button>
            </div>
        </div>

        <!-- Alerts -->
        <?php if($success): ?>
        <div class="card" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724;">
            <p><strong>✓</strong> <?php echo htmlspecialchars($success); ?></p>
        </div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="card" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">
            <p><strong>✗</strong> <?php echo htmlspecialchars($error); ?></p>
        </div>
        <?php endif; ?>

        <!-- Table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Liste des Sessions</h2>
            </div>
            <div class="table-responsive">
                <?php if($list->rowCount() > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Places Total</th>
                            <th>Réservées</th>
                            <th>Disponibles</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($list as $entretien): 
                            $placesRestantes = $entretien['places'] - $entretien['places_prises'];
                            $dateObj = new DateTime($entretien['date']);
                            $dateMaintenant = new DateTime();
                            $estPasse = $dateObj < $dateMaintenant;
                        ?>
                        <tr>
                            <td><?php echo $entretien['id']; ?></td>
                            <td>
                                <?php 
                                $typeClass = 'badge-info';
                                if($entretien['type'] == 'RH') $typeClass = 'badge-warning';
                                if($entretien['type'] == 'Mixte') $typeClass = 'badge-success';
                                ?>
                                <span class="badge <?php echo $typeClass; ?>">
                                    <?php echo htmlspecialchars($entretien['type']); ?>
                                </span>
                            </td>
                            <td><?php echo $dateObj->format('d/m/Y'); ?></td>
                            <td><?php echo substr($entretien['heure'], 0, 5); ?></td>
                            <td><?php echo $entretien['places']; ?></td>
                            <td><?php echo $entretien['places_prises']; ?></td>
                            <td><strong><?php echo $placesRestantes; ?></strong></td>
                            <td>
                                <?php if($estPasse): ?>
                                    <span class="badge badge-danger">Passé</span>
                                <?php elseif($placesRestantes <= 0): ?>
                                    <span class="badge badge-danger">Complet</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Disponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button onclick='openEditModal(<?php echo json_encode($entretien); ?>)' 
                                            class="btn btn-sm btn-edit">
                                        Modifier
                                    </button>
                                    <a href="?delete=<?php echo $entretien['id']; ?>" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette session ?')"
                                       class="btn btn-sm btn-delete">
                                        Supprimer
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i>📭</i>
                    <h3>Aucune session</h3>
                    <p>Cliquez sur "Nouvelle Session" pour commencer</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Ajout -->
    <div id="addModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Nouvelle Session</h3>
                <button class="close" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" id="addForm">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label class="form-label">Type d'Entretien *</label>
                    <select name="type" class="form-select">
                        <option value="">-- Choisir --</option>
                        <option value="Technique">Technique</option>
                        <option value="RH">RH</option>
                        <option value="Mixte">Mixte</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Heure *</label>
                    <input type="time" name="heure" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre de Places *</label>
                    <input type="number" name="places" class="form-control" min="1" value="10">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Modal Modification -->
    <div id="editModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Modifier Session</h3>
                <button class="close" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="places_prises" id="edit_places_prises">
                
                <div class="form-group">
                    <label class="form-label">Type d'Entretien *</label>
                    <select name="type" id="edit_type" class="form-select">
                        <option value="Technique">Technique</option>
                        <option value="RH">RH</option>
                        <option value="Mixte">Mixte</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" id="edit_date" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Heure *</label>
                    <input type="time" name="heure" id="edit_heure" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Nombre de Places *</label>
                    <input type="number" name="places" id="edit_places" class="form-control" min="1">
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%;">Enregistrer</button>
            </form>
        </div>
    </div>

    <script src="<?php echo $base_url; ?>assests/js/back.js"></script>
</body>
</html>