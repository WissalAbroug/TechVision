<?php
include '../../controller/DemandeController.php';
$demandeC = new DemandeController();

// Gestion de la recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$list = []; // Initialiser la variable

if ($search) {
    $list = $demandeC->searchDemandes($search);
} else {
    $list = $demandeC->listDemandes();
}

// Gestion de la modification du statut
if (isset($_GET['changer_statut'])) {
    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $nouveau_statut = isset($_GET['statut']) ? $_GET['statut'] : '';

    if ($id > 0 && !empty($nouveau_statut)) {
        // Utiliser la méthode du contrôleur pour mettre à jour le statut
        if ($demandeC->updateStatut($id, $nouveau_statut)) {
            // Rediriger pour éviter la resoumission
            header('Location: demandeList.php');
            exit;
        }
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">

<head>
    <title>Gestion des Inscriptions</title>
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

        .statut-option-confirmee {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        .statut-option-attente {
            background-color: #fef3c7;
            color: #92400e;
            border-color: #f59e0b;
        }

        .statut-option-annulee {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #ef4444;
        }

        /* Badges colorés pour les statuts */
        .badge-attente {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #f59e0b;
        }

        .badge-confirmee {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #10b981;
        }

        .badge-annulee {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .badge-niveau {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #5b21b6;
            border: 1px solid #8b5cf6;
        }

        .mode-paiement-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .mode-carte {
            background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
            color: #5b21b6;
            border: 1px solid #8b5cf6;
        }

        .mode-paypal {
            background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        .mode-mobile {
            background: linear-gradient(135deg, #86efac 0%, #4ade80 100%);
            color: #166534;
            border: 1px solid #22c55e;
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
                <li class="nav-item">
                    <a href="formationList.php">
                        <span class="icon">📚</span>
                        <span class="text">Gestion Formations</span>
                    </a>
                </li>
                <li class="nav-item active">
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
                                <h2>Liste des inscriptions</h2>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="breadcrumb-wrapper">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="../../admin.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Inscriptions</li>
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
                                    <h6>Toutes les inscriptions</h6>
                                </div>
                                <div class="right">
                                    <form method="GET" action="" class="search-form">
                                        <input type="text" name="search" class="search-input" placeholder="Rechercher par nom, email ou tél..." value="<?php echo htmlspecialchars($search); ?>">
                                        <button type="submit" class="btn-search">🔍 Rechercher</button>
                                        <?php if ($search): ?>
                                            <a href="demandeList.php" class="btn-reset">✖ Réinitialiser</a>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>N° Demande</th>
                                            <th>Participant</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Formation</th>
                                            <th>Niveau Participant</th>
                                            <th>Date Inscription</th>
                                            <th>Mode Paiement</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($list)):
                                            foreach ($list as $demande):
                                                // Déterminer la classe CSS pour le badge selon le statut
                                                $badge_class = '';
                                                if ($demande['statut'] == 'Confirmée') {
                                                    $badge_class = 'badge-confirmee';
                                                } elseif ($demande['statut'] == 'En attente') {
                                                    $badge_class = 'badge-attente';
                                                } elseif ($demande['statut'] == 'Annulée') {
                                                    $badge_class = 'badge-annulee';
                                                }

                                                // Déterminer la classe pour le select
                                                $select_class = '';
                                                if ($demande['statut'] == 'Confirmée') {
                                                    $select_class = 'statut-option-confirmee';
                                                } elseif ($demande['statut'] == 'En attente') {
                                                    $select_class = 'statut-option-attente';
                                                } elseif ($demande['statut'] == 'Annulée') {
                                                    $select_class = 'statut-option-annulee';
                                                }

                                                // Déterminer l'icône et classe pour le mode de paiement
                                                $mode_icon = '';
                                                $mode_class = '';
                                                switch ($demande['mode_paiement'] ?? 'Non spécifié') {
                                                    case 'Carte bancaire':
                                                        $mode_icon = '💳';
                                                        $mode_class = 'mode-carte';
                                                        break;
                                                    case 'PayPal':
                                                        $mode_icon = '🏦';
                                                        $mode_class = 'mode-paypal';
                                                        break;
                                                    case 'Paiement mobile':
                                                        $mode_icon = '📱';
                                                        $mode_class = 'mode-mobile';
                                                        break;
                                                    default:
                                                        $mode_icon = '💰';
                                                        $mode_class = '';
                                                }
                                        ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($demande['numero_demande']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($demande['nom']); ?></td>
                                                    <td><?php echo htmlspecialchars($demande['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($demande['tel']); ?></td>
                                                    <td><?php echo htmlspecialchars($demande['formation_nom']); ?></td>
                                                    <td>
                                                        <span class="badge badge-niveau">
                                                            <?php echo htmlspecialchars($demande['niveau'] ?? 'Non spécifié'); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($demande['date_inscription'])); ?></td>
                                                    <td>
                                                        <span class="mode-paiement-badge <?php echo $mode_class; ?>">
                                                            <?php echo $mode_icon . ' ' . htmlspecialchars($demande['mode_paiement'] ?? 'Non spécifié'); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form method="GET" action="" class="statut-form">
                                                            <input type="hidden" name="id" value="<?php echo $demande['id']; ?>">
                                                            <select name="statut" class="statut-select <?php echo $select_class; ?>"
                                                                onchange="changerStatut(this)">
                                                                <option value="Confirmée" <?php echo ($demande['statut'] == 'Confirmée') ? 'selected' : ''; ?>>Confirmée</option>
                                                                <option value="En attente" <?php echo ($demande['statut'] == 'En attente') ? 'selected' : ''; ?>>En attente</option>
                                                                <option value="Annulée" <?php echo ($demande['statut'] == 'Annulée') ? 'selected' : ''; ?>>Annulée</option>
                                                            </select>
                                                            <input type="hidden" name="changer_statut" value="1">
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <a href="deleteDemande.php?id=<?php echo $demande['id']; ?>"
                                                                class="btn-delete"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription?')"
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
                                                <td colspan="10" class="text-center">Aucune inscription trouvée</td>
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
        // Fonction pour changer le statut
        function changerStatut(selectElement) {
            // Ajouter une animation
            selectElement.classList.add('statut-changing');

            // Changer la couleur du sélecteur selon le statut choisi
            const statut = selectElement.value;
            selectElement.className = 'statut-select ';

            if (statut === 'Confirmée') {
                selectElement.classList.add('statut-option-confirmee');
            } else if (statut === 'En attente') {
                selectElement.classList.add('statut-option-attente');
            } else if (statut === 'Annulée') {
                selectElement.classList.add('statut-option-annulee');
            }

            // Afficher une confirmation
            const confirmation = confirm('Voulez-vous vraiment changer le statut de cette inscription ?');
            if (!confirmation) {
                // Recharger la page pour réinitialiser
                location.reload();
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
                if (statut === 'Confirmée') {
                    select.classList.add('statut-option-confirmee');
                } else if (statut === 'En attente') {
                    select.classList.add('statut-option-attente');
                } else if (statut === 'Annulée') {
                    select.classList.add('statut-option-annulee');
                }
            });
        });
    </script>
</body>

</html>