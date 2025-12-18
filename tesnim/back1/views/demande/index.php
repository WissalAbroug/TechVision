<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Interface de consultation des enregistrements">
    <title>TalentMatch - Consultation des données</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>TALENTMATCH</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="nav-link">📊 Dashboard</a></li>
            <li><a href="#" class="nav-link active">📋 Demandes</a></li>
            <li><a href="#" class="nav-link">👥 Candidats</a></li>
            <li><a href="#" class="nav-link">📅 Calendrier</a></li>
            <li><a href="#" class="nav-link">⚙️ Paramètres</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <h1>Consultation des Demandes d'Entretien</h1>
            <div class="user-info">
                <div class="user-avatar">AD</div>
            </div>
        </header>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <h3>Total</h3>
                <div class="card-value"><?php echo $totalRecords; ?></div>
            </div>
            <div class="card">
                <h3>Actifs</h3>
                <div class="card-value" style="color: var(--accent-green);">
                    <?php 
                    $actifs = array_filter($records ?? [], fn($r) => strtolower($r['statut'] ?? '') === 'actif');
                    echo count($actifs);
                    ?>
                </div>
            </div>
            <div class="card">
                <h3>En attente</h3>
                <div class="card-value" style="color: var(--accent-orange);">
                    <?php 
                    $enAttente = array_filter($records ?? [], fn($r) => strtolower($r['statut'] ?? '') === 'en attente');
                    echo count($enAttente);
                    ?>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">Liste des Demandes</h2>
                <div style="display: flex; gap: 10px;">
                    <a href="?action=create_page" class="btn btn-primary btn-small">
                        <span>➕</span> Nouvelle demande
                    </a>
                    <a href="<?php echo DemandeEntretienController::escapeHtml($_SERVER['PHP_SELF']); ?>" class="btn btn-secondary btn-small">
                        <span>↻</span> Rafraîchir
                    </a>
                </div>
            </div>

            <!-- Messages dynamiques -->
            <?php if (isset($error)): ?>
                <div class="alert alert-error show">
                    <strong>⚠️ Erreur</strong>
                    <?php echo DemandeEntretienController::escapeHtml($error); ?>
                </div>
            <?php elseif ($totalRecords === 0 && !empty($searchQuery)): ?>
                <div class="alert alert-warning show">
                    <strong>ℹ️ Aucun résultat</strong>
                    Aucun résultat ne correspond à votre recherche
                </div>
            <?php elseif ($totalRecords === 0): ?>
                <div class="alert alert-warning show">
                    <strong>ℹ️ Base vide</strong>
                    Aucun enregistrement trouvé dans la base de données
                </div>
            <?php elseif (!empty($searchQuery)): ?>
                <div class="alert alert-success show">
                    <strong>✓ Recherche réussie</strong>
                    <?php echo $totalRecords; ?> résultat(s) trouvé(s) pour "<?php echo DemandeEntretienController::escapeHtml($searchQuery); ?>"
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success show">
                    <strong>✓ Succès</strong>
                    <?php 
                        switch($_GET['success']) {
                            case 'created': echo 'Enregistrement créé avec succès'; break;
                            case 'updated': echo 'Enregistrement mis à jour avec succès'; break;
                            case 'deleted': echo 'Enregistrement supprimé avec succès'; break;
                            default: echo 'Opération réussie';
                        }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error show">
                    <strong>⚠️ Erreur</strong>
                    <?php echo DemandeEntretienController::escapeHtml($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Search Bar -->
            <form method="GET" action="" class="search-wrapper">
                <input 
                    type="text" 
                    name="search"
                    class="search-input" 
                    placeholder="Rechercher par nom, email, téléphone, statut..."
                    value="<?php echo DemandeEntretienController::escapeHtml($searchQuery ?? ''); ?>"
                >
                <span class="search-icon">🔍</span>
            </form>

            <!-- Table -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($totalRecords > 0): ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo DemandeEntretienController::escapeHtml($record['id'] ?? '-'); ?></td>
                                    <td><?php echo DemandeEntretienController::escapeHtml($record['nom'] ?? '-'); ?></td>
                                    <td><?php echo DemandeEntretienController::escapeHtml($record['email'] ?? '-'); ?></td>
                                    <td><?php echo DemandeEntretienController::escapeHtml($record['telephone'] ?? '-'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo DemandeEntretienController::getBadgeClass($record['statut'] ?? ''); ?>">
                                            <?php echo DemandeEntretienController::escapeHtml($record['statut'] ?? 'inconnu'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo DemandeEntretienController::formatDate($record['date_creation'] ?? $record['date'] ?? ''); ?></td>
                                    <td>
                                        <button 
                                            class="btn btn-secondary btn-small" 
                                            onclick="openEditModal(<?php echo htmlspecialchars(json_encode($record), ENT_QUOTES, 'UTF-8'); ?>)"
                                            title="Modifier"
                                        >
                                            ✏️
                                        </button>
                                        <button 
                                            class="btn btn-danger btn-small" 
                                            onclick="openDeleteModal(<?php echo $record['id']; ?>, '<?php echo DemandeEntretienController::escapeHtml($record['nom'] ?? ''); ?>')"
                                            title="Supprimer"
                                        >
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px; opacity: 0.6;">
                                    Aucun enregistrement à afficher
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer with count -->
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid var(--border-color); text-align: center; color: var(--text-light); opacity: 0.7; font-size: 13px;">
                <span style="color: var(--accent-orange); font-weight: 700;"><?php echo $totalRecords; ?></span> enregistrement(s) affiché(s)
                <?php if (!empty($searchQuery)): ?>
                    sur un total disponible
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Modification -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Modifier l'enregistrement</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" action="?action=update" id="editForm">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_nom">Nom *</label>
                    <input type="text" id="edit_nom" name="nom" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email *</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_telephone">Téléphone</label>
                    <input type="text" id="edit_telephone" name="telephone">
                </div>
                
                <div class="form-group">
                    <label for="edit_statut">Statut</label>
                    <select id="edit_statut" name="statut">
                        <option value="en attente">En attente</option>
                        <option value="actif">Actif</option>
                        <option value="inactive">Inactif</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>🗑️ Confirmer la suppression</h2>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div style="padding: 20px 0;">
                <p style="margin-bottom: 15px;">Êtes-vous sûr de vouloir supprimer cet enregistrement ?</p>
                <p style="color: var(--accent-orange); font-weight: 600;" id="delete_name"></p>
                <p style="margin-top: 15px; color: var(--accent-pink); font-size: 13px;">
                    ⚠️ Cette action est irréversible
                </p>
            </div>
            <form method="POST" action="?action=delete" id="deleteForm">
                <input type="hidden" name="id" id="delete_id">
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Annuler</button>
                    <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/app.js/app.js"></script>
</body>
</html>