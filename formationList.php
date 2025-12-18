<?php
// Correction du chemin d'inclusion
include __DIR__ . '/../../controller/FormationController.php';
$formationC = new FormationController();

// Gestion de la modification du statut
if (isset($_GET['changer_statut'])) {
    $id = $_GET['id'];
    $nouveau_statut = $_GET['statut'];

    // Utiliser la méthode du contrôleur pour mettre à jour le statut
    if ($formationC->updateStatut($id, $nouveau_statut)) {
        // Rediriger pour éviter la resoumission
        header('Location: formationList.php');
        exit;
    }
}

$list = $formationC->listFormations();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">

<head>
    <title>Gestion des Formations</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../assets/css/back.css">
    <style>
        /* Styles pour le menu déroulant des statuts */
        .statut-select {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            min-width: 120px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .statut-option-active {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        .statut-option-complete {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }

        .statut-option-annulee {
            background-color: #9ca3af;
            color: #1f2937;
            border-color: #6b7280;
        }

        /* Badges colorés pour les statuts */
        .badge-active {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }

        .badge-complete {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .badge-annulee {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
            color: #1f2937;
            border: 1px solid #4b5563;
        }

        .badge-niveau {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #5b21b6;
            border: 1px solid #8b5cf6;
        }

        .niveau-débutant {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .niveau-moyen {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .niveau-expert {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        /* Animation pour le changement de statut */
        @keyframes statutChange {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .statut-changing {
            animation: statutChange 0.5s ease;
        }

        .places-info {
            font-weight: bold;
        }

        .text-success {
            color: #10b981;
        }

        .text-danger {
            color: #ef4444;
        }
    </style>
</head>

<body>

    <aside class="sidebar-nav-wrapper">
        <div class="navbar-logo">
            <h2 class="admin-logo">FormationPro Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item">
                    <a href="../../admin.php">
                        <span class="icon">📊</span>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="formationList.php">
                        <span class="icon">📚</span>
                        <span class="text">Gestion Formations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="demandeList.php">
                        <span class="icon">📝</span>
                        <span class="text">Gestion Inscriptions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../../index.php">
                        <span class="icon">🏠</span>
                        <span class="text">Retour Site</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <div class="overlay"></div>

    <main class="main-wrapper">
        <header class="header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="header-left">
                            <div class="menu-toggle-btn">
                                <button id="menu-toggle" class="main-btn">
                                    ☰ Menu
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="header-right">
                            <div class="profile-box">
                                <div class="profile-info">
                                    <h6>Administrateur</h6>
                                    <p>FormationPro</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section class="section">
            <div class="container-fluid">
                <div class="title-wrapper pt-30">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="title">
                                <h2>Liste des formations</h2>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="breadcrumb-wrapper">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="../../admin.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Formations</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-style">
                            <div class="title-actions">
                                <div class="left">
                                    <h6>Toutes les formations</h6>
                                </div>
                                <div class="right">
                                    <a href="addFormation.php" class="btn-primary">
                                        ➕ Ajouter une formation
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Date</th>
                                            <th>Niveau</th>
                                            <th>Places</th>
                                            <th>Prix (TND)</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($list)):
                                            foreach ($list as $formation):
                                                $placesRestantes = $formationC->getPlacesRestantes($formation);
                                                $isComplete = $formationC->isComplete($formation);
                                                $isPassee = $formationC->isPassee($formation);

                                                // Déterminer la classe pour le select
                                                $select_class = '';
                                                if ($formation['statut'] == 'Active') {
                                                    $select_class = 'statut-option-active';
                                                } elseif ($formation['statut'] == 'Complète') {
                                                    $select_class = 'statut-option-complete';
                                                } elseif ($formation['statut'] == 'Annulée') {
                                                    $select_class = 'statut-option-annulee';
                                                }
                                        ?>
                                                <tr>
                                                    <td><strong>#<?php echo $formation['id']; ?></strong></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($formation['nom']); ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($formation['date_formation'])); ?></td>
                                                    <td>
                                                        <span class="badge badge-niveau niveau-<?php echo strtolower($formation['niveau']); ?>">
                                                            <?php echo htmlspecialchars($formation['niveau']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="places-info">
                                                            <?php echo $formation['places_prises']; ?>/<?php echo $formation['places_max']; ?>
                                                            <br>
                                                            <small class="<?php echo $placesRestantes > 0 ? 'text-success' : 'text-danger'; ?>">
                                                                (<?php echo $placesRestantes; ?> restantes)
                                                            </small>
                                                        </span>
                                                    </td>
                                                    <td><?php echo number_format($formation['prix'], 2); ?></td>
                                                    <td>
                                                        <form method="GET" action="" class="statut-form">
                                                            <input type="hidden" name="id" value="<?php echo $formation['id']; ?>">
                                                            <select name="statut" class="statut-select <?php echo $select_class; ?>"
                                                                onchange="changerStatutFormation(this)">
                                                                <option value="Active" <?php echo ($formation['statut'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                                                <option value="Complète" <?php echo ($formation['statut'] == 'Complète') ? 'selected' : ''; ?>>Complète</option>
                                                                <option value="Annulée" <?php echo ($formation['statut'] == 'Annulée') ? 'selected' : ''; ?>>Annulée</option>
                                                            </select>
                                                            <input type="hidden" name="changer_statut" value="1">
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <form method="POST" action="updateFormation.php" style="display: inline;">
                                                                <input type="hidden" value="<?php echo $formation['id']; ?>" name="id">
                                                                <button type="submit" name="update" class="btn-edit" title="Modifier">
                                                                    ✏️
                                                                </button>
                                                            </form>
                                                            <a href="deleteFormation.php?id=<?php echo $formation['id']; ?>"
                                                                class="btn-delete"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formation?')"
                                                                title="Supprimer">
                                                                🗑️
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php
                                            endforeach;
                                        else:
                                            ?>
                                            <tr>
                                                <td colspan="8" class="text-center">Aucune formation disponible</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="copyright text-center">
                            <p>&copy; 2024 FormationPro. Tous droits réservés.</p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script src="../../assets/js/back.js"></script>
    <script>
        // Fonction pour changer le statut de la formation
        function changerStatutFormation(selectElement) {
            // Ajouter une animation
            selectElement.classList.add('statut-changing');

            // Changer la couleur du sélecteur selon le statut choisi
            const statut = selectElement.value;
            selectElement.className = 'statut-select ';

            if (statut === 'Active') {
                selectElement.classList.add('statut-option-active');
            } else if (statut === 'Complète') {
                selectElement.classList.add('statut-option-complete');
            } else if (statut === 'Annulée') {
                selectElement.classList.add('statut-option-annulee');
            }

            // Afficher une confirmation
            const confirmation = confirm('Voulez-vous vraiment changer le statut de cette formation ?');
            if (!confirmation) {
                selectElement.form.reset();
                selectElement.classList.remove('statut-changing');
                return false;
            }

            // Soumettre le formulaire
            selectElement.form.submit();

            // Retirer l'animation après 0.5s
            setTimeout(() => {
                selectElement.classList.remove('statut-changing');
            }, 500);

            return true;
        }

        // Initialiser les couleurs des sélecteurs au chargement
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.statut-select').forEach(function(select) {
                const statut = select.value;
                if (statut === 'Active') {
                    select.classList.add('statut-option-active');
                } else if (statut === 'Complète') {
                    select.classList.add('statut-option-complete');
                } else if (statut === 'Annulée') {
                    select.classList.add('statut-option-annulee');
                }
            });
        });
    </script>
</body>

</html>