
<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Controller/OfferController.php';
require_once __DIR__ . '/../../Controller/CategoryController.php';

// Initialiser les contrôleurs
$offerController = new OfferController();
$categoryController = new CategoryController();

// Gérer les actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Actions pour les CATÉGORIES
    if ($action === 'addCategory') {
        // Validation
        if (empty($_POST['nom'])) {
            $_SESSION['error'] = 'Le nom de la catégorie est obligatoire.';
        } else {
            $category = new Category(
                null,
                $_POST['nom'] ?? '',
                $_POST['description'] ?? '',
                $_POST['icone'] ?? null
            );
            
            if ($categoryController->addCategory($category)) {
                $_SESSION['success'] = 'Catégorie ajoutée avec succès !';
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'ajout de la catégorie.';
            }
        }
    } 
    elseif ($action === 'updateCategory') {
        $category = new Category(
            (int)$_POST['id'],
            $_POST['nom'] ?? '',
            $_POST['description'] ?? '',
            $_POST['icone'] ?? null
        );
        
        if ($categoryController->updateCategory($category)) {
            $_SESSION['success'] = 'Catégorie modifiée avec succès !';
        } else {
            $_SESSION['error'] = 'Erreur lors de la modification de la catégorie.';
        }
    } 
    elseif ($action === 'deleteCategory') {
        if ($categoryController->deleteCategory((int)$_POST['id'])) {
            $_SESSION['success'] = 'Catégorie supprimée avec succès !';
        } else {
            $_SESSION['error'] = 'Impossible de supprimer cette catégorie (des offres y sont liées).';
        }
    }
    // Actions pour les OFFRES
    elseif ($action === 'add') {
        $competences = !empty($_POST['competences']) ? json_decode($_POST['competences'], true) : [];
        $requirements = !empty($_POST['requirements']) ? json_decode($_POST['requirements'], true) : [];
        
        if (empty($_POST['titre']) || empty($_POST['nomSociete']) || empty($_POST['localisation'])) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (empty($competences)) {
            $_SESSION['error'] = 'Veuillez ajouter au moins une compétence.';
        } elseif (empty($requirements)) {
            $_SESSION['error'] = 'Veuillez ajouter au moins une exigence.';
        } else {
            $offer = new Offer(
                null,
                $_POST['titre'] ?? '',
                $_POST['description'] ?? '',
                $_POST['nomSociete'] ?? '',
                $_POST['localisation'] ?? '',
                (int)($_POST['salaireMin'] ?? 0),
                (int)($_POST['salaireMax'] ?? 0),
                $_POST['typeContrat'] ?? 'CDI',
                $_POST['experienceRequise'] ?? '',
                $competences,
                $requirements,
                (int)($_POST['nbPlace'] ?? 1),
                !empty($_POST['dateLimite']) ? $_POST['dateLimite'] : null,
                date('Y-m-d H:i:s'),
                'active'
            );
            
            if ($offerController->addOffer($offer)) {
                $_SESSION['success'] = 'Offre ajoutée avec succès !';
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'ajout de l\'offre.';
            }
        }
        
    } elseif ($action === 'update') {
        $competences = !empty($_POST['competences']) ? json_decode($_POST['competences'], true) : [];
        $requirements = !empty($_POST['requirements']) ? json_decode($_POST['requirements'], true) : [];
        
        $offer = new Offer(
            (int)$_POST['id'],
            $_POST['titre'] ?? '',
            $_POST['description'] ?? '',
            $_POST['nomSociete'] ?? '',
            $_POST['localisation'] ?? '',
            (int)($_POST['salaireMin'] ?? 0),
            (int)($_POST['salaireMax'] ?? 0),
            $_POST['typeContrat'] ?? 'CDI',
            $_POST['experienceRequise'] ?? '',
            $competences,
            $requirements,
            (int)($_POST['nbPlace'] ?? 1),
            !empty($_POST['dateLimite']) ? $_POST['dateLimite'] : null,
            null,
            $_POST['statut'] ?? 'active'
        );
        
        if ($offerController->updateOffer($offer)) {
            $_SESSION['success'] = 'Offre modifiée avec succès !';
        } else {
            $_SESSION['error'] = 'Erreur lors de la modification de l\'offre.';
        }
        
    } elseif ($action === 'delete') {
        if ($offerController->deleteOffer((int)$_POST['id'])) {
            $_SESSION['success'] = 'Offre supprimée avec succès !';
        } else {
            $_SESSION['error'] = 'Erreur lors de la suppression de l\'offre.';
        }
        
    } elseif ($action === 'archive') {
        if ($offerController->archiveOffer((int)$_POST['id'])) {
            $_SESSION['success'] = 'Offre archivée avec succès !';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'archivage de l\'offre.';
        }
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Récupérer toutes les offres et catégories
$offers = $offerController->getAllOffers();
$categories = $categoryController->getAllCategories();
$offersActive = array_filter($offers, function($o) { return $o->getStatut() === 'active'; });
$offersClosed = array_filter($offers, function($o) { return $o->getStatut() === 'closed'; });

// Afficher les messages de session
$successMessage = $_SESSION['success'] ?? '';
$errorMessage = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>PathFinderAI - Espace Société</title>
    <meta http-equiv="Content-Style-Type" content="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="back office.css" type="text/css">
</head>
<body>
    <!-- HEADER -->
    <div id="header">
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-building"></i>
                <h1>TalentMatch Pro</h1>
            </div>
            <div class="user-actions">
                <button class="btn btn-outline" type="button">
                    <i class="fas fa-chart-line"></i> Statistiques
                </button>
                <button class="btn btn-primary" id="btnAddOffer" type="button">
                    <i class="fas fa-plus"></i> Nouvelle Offre
                </button>
            </div>
        </div>
    </div>

    <!-- SIDEBAR -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-nav">
            <a href="#offres" class="nav-item active" onclick="showSection('offres'); return false;">
                <i class="fas fa-briefcase"></i>
                <span>Mes Offres</span>
            </a>
            <a href="#categories" class="nav-item" onclick="showSection('categories'); return false;">
                <i class="fas fa-folder-open"></i>
                <span>Catégories</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Candidats</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>Candidatures</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-star"></i>
                <span>Matchs</span>
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div id="main-content" class="main-content">
        <div class="container">
            <!-- SECTION MES OFFRES -->
            <div class="offers-section" id="offres-section">
                <h2 class="section-title">Gestion des Offres</h2>
                
                <!-- STATS CARDS -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= count($offersActive) ?></h3>
                            <p>Offres Actives</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= count($categories) ?></h3>
                            <p>Catégories</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>23</h3>
                            <p>Matchs Parfaits</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?= count($offersClosed) ?></h3>
                            <p>Fermées</p>
                        </div>
                    </div>
                </div>

                <!-- OFFERS TABLE -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Toutes les Offres (<?= count($offers) ?>)</h3>
                        <div class="card-actions">
                            <input type="text" class="search-input" id="searchInput" placeholder="Rechercher...">
                        </div>
                    </div>
                    
                    <div class="offers-list">
                        <?php if (count($offers) === 0): ?>
                            <p style="text-align: center; padding: 2rem; color: var(--text-light);">
                                Aucune offre pour le moment. Cliquez sur "Nouvelle Offre" pour commencer.
                            </p>
                        <?php else: ?>
                            <?php foreach ($offers as $offer): ?>
                            <div class="offer-item" data-id="<?= $offer->getId() ?>">
                                <div class="offer-main">
                                    <div class="offer-header">
                                        <h4 class="offer-title"><?= htmlspecialchars($offer->getTitre()) ?></h4>
                                        <span class="offer-status <?= $offer->getStatut() === 'active' ? 'status-active' : 'status-closed' ?>">
                                            <?= $offer->getStatut() === 'active' ? 'Active' : 'Fermée' ?>
                                        </span>
                                    </div>
                                    <div class="offer-meta">
                                        <span><i class="fas fa-building"></i> <?= htmlspecialchars($offer->getNomSociete()) ?></span>
                                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($offer->getLocalisation()) ?></span>
                                        <span><i class="fas fa-euro-sign"></i> <?= number_format($offer->getSalaireMin()) ?> - <?= number_format($offer->getSalaireMax()) ?>€</span>
                                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($offer->getDateCreation())) ?></span>
                                        <span><i class="fas fa-briefcase"></i> <?= htmlspecialchars($offer->getTypeContrat()) ?></span>
                                    </div>
                                    <div class="offer-skills">
                                        <?php 
                                        $competences = $offer->getCompetences();
                                        if (is_array($competences)):
                                            foreach ($competences as $comp): 
                                        ?>
                                        <span class="skill-badge"><?= htmlspecialchars($comp) ?></span>
                                        <?php 
                                            endforeach;
                                        endif;
                                        ?>
                                    </div>
                                </div>
                                <div class="offer-actions">
                                    <button class="action-btn btn-view" onclick="viewOffer(<?= $offer->getId() ?>); return false;" type="button">
                                        <i class="fas fa-eye"></i> Voir
                                    </button>
                                    <button class="action-btn btn-edit" onclick="editOffer(<?= $offer->getId() ?>); return false;" type="button">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <button class="action-btn btn-candidates" type="button">
                                        <i class="fas fa-users"></i> Candidats
                                    </button>
                                    <button class="action-btn btn-delete" onclick="deleteOffer(<?= $offer->getId() ?>); return false;" type="button">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>


<!-- SECTION CATÉGORIES -->
<div class="categories-section" id="categories-section" style="display:none;">
    <h2 class="section-title">Gestion des Catégories</h2>
    
    <!-- BLOC INFORMATIF AJOUTÉ ICI -->
    <div class="card" style="margin-bottom: 20px; background: rgba(20, 40, 80, 0.6); border-left: 4px solid var(--accent-orange);">
        <div class="card-header" style="border-bottom: 1px solid rgba(255,165,0,0.3);">
            <h3 class="card-title" style="color: var(--accent-orange);">
                <i class="fas fa-info-circle"></i> Information importante
            </h3>
        </div>
        <div class="modal-body">
            <p style="color: var(--text-light); margin-bottom: 10px;">
                <i class="fas fa-database"></i> 
                <strong>Statut actuel :</strong> 
                Vous avez <?= count($categories) ?> catégorie<?= count($categories) > 1 ? 's' : '' ?> dans votre base de données.
            </p>
            
            <div style="display: block; margin-bottom: 15px; padding: 10px; 
                       background: rgba(10, 26, 58, 0.5); border-radius: 6px;">
                <?php foreach ($categories as $category): ?>
                <span class="badge badge-info" style="padding: 6px 10px; font-size: 13px; margin: 0 5px 5px 0; display: inline-block;">
                    <i class="fas <?= htmlspecialchars($category->getIcone() ?: 'fa-folder') ?>"></i>
                    <?= htmlspecialchars($category->getNom()) ?>
                    <small style="margin-left: 4px;">(<?= $category->getNbOffres() ?>)</small>
                </span>
                <?php endforeach; ?>
            </div>
            
            <div style="background: rgba(255,165,0,0.1); padding: 10px; border-radius: 6px; border: 1px solid rgba(255,165,0,0.2);">
                <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Attention :</strong> Vous ne pouvez pas créer une catégorie avec un nom qui existe déjà.
                </p>
                <p style="margin: 8px 0 0 0; color: #a0aec0; font-size: 0.85rem;">
                    <i class="fas fa-lightbulb"></i> 
                    <strong>Suggestions :</strong> Essayez avec des noms uniques comme 
                    "Intelligence Artificielle", "Marketing Digital", "Cybersécurité", etc.
                </p>
            </div>
        </div>
    </div>
    
    <!-- CARD PRINCIPALE DES CATÉGORIES -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Toutes les Catégories (<?= count($categories) ?>)</h3>
            <div class="card-actions">
                <input type="text" class="search-input" id="searchCategoryInput" placeholder="Rechercher une catégorie...">
                <button class="btn btn-primary" id="btnAddCategory" type="button">
                    <i class="fas fa-plus"></i> Nouvelle Catégorie
                </button>
            </div>
        </div>
        
        <div class="offers-list">
            <?php if (count($categories) === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>Aucune catégorie</h3>
                    <p>Commencez par créer votre première catégorie</p>
                    <button class="btn btn-primary" onclick="document.getElementById('btnAddCategory').click(); return false;" type="button">
                        <i class="fas fa-plus"></i> Créer une catégorie
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                <div class="offer-item category-item" data-id="<?= $category->getId() ?>">
                    <div class="offer-main">
                        <div class="offer-header">
                            <div style="display: block;">
                                <div style="width: 50px; height: 50px; border-radius: 8px; 
                                            background: linear-gradient(135deg, var(--accent-orange), var(--accent-green));
                                            float: left; margin-right: 12px; display: table;">
                                    <i class="fas <?= htmlspecialchars($category->getIcone() ?: 'fa-folder') ?>" 
                                       style="font-size: 1.5rem; color: white; display: table-cell; vertical-align: middle; text-align: center;"></i>
                                </div>
                                <div style="overflow: hidden;">
                                    <h4 class="offer-title"><?= htmlspecialchars($category->getNom()) ?></h4>
                                    <div style="margin-top: 5px;">
                                        <span style="color: #a0aec0; font-size: 0.9rem; margin-right: 15px;">
                                            <i class="fas fa-briefcase"></i> <?= $category->getNbOffres() ?> offre(s)
                                        </span>
                                        <span style="color: #a0aec0; font-size: 0.9rem;">
                                            <i class="fas fa-calendar"></i> Créée le <?= date('d/m/Y', strtotime($category->getDateCreation())) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div style="clear: both;"></div>
                            <span class="badge <?= $category->getNbOffres() > 0 ? 'badge-success' : 'badge-warning' ?>" style="float: right;">
                                <?= $category->getNbOffres() > 0 ? 'Active' : 'Vide' ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($category->getDescription())): ?>
                        <div class="offer-meta" style="margin-top: 10px; padding: 10px; background: rgba(10, 26, 58, 0.5); border-radius: 6px;">
                            <p style="margin: 0; color: var(--text-light); font-size: 0.95rem;">
                                <i class="fas fa-align-left"></i> 
                                <?= htmlspecialchars($category->getDescription()) ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="offer-actions">
                        <button class="action-btn btn-view" onclick="viewCategory(<?= $category->getId() ?>); return false;" type="button">
                            <i class="fas fa-eye"></i> Voir
                        </button>
                        <button class="action-btn btn-edit" onclick="editCategory(<?= $category->getId() ?>); return false;" type="button">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button class="action-btn btn-delete" 
                                onclick="deleteCategory(<?= $category->getId() ?>, <?= $category->getNbOffres() ?>); return false;"
                                <?= $category->getNbOffres() > 0 ? 'disabled="disabled" style="opacity: 0.5; cursor: not-allowed;"' : '' ?> type="button">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

        </div>
    </div>

    <!-- MODAL AJOUTER/MODIFIER OFFRE -->
    <div class="modal" id="modalOffer">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="section-title" id="modalTitle">Créer une Offre</h2>
                <button class="btn-close" onclick="closeModal(); return false;" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form class="offer-form" id="offerForm" method="POST" action="" onsubmit="return validateForm()">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="offerId">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="titre">Titre du Poste *</label>
                            <input type="text" name="titre" id="titre" placeholder="Ex: Développeur Full Stack">
                        </div>
                        <div class="form-group">
                            <label for="nomSociete">Société *</label>
                            <input type="text" name="nomSociete" id="nomSociete" placeholder="Nom de la société">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="localisation">Localisation *</label>
                            <input type="text" name="localisation" id="localisation" placeholder="Ex: Paris, France">
                        </div>
                        <div class="form-group">
                            <label for="nbPlace">Nombre de Places *</label>
                            <input type="text" name="nbPlace" id="nbPlace" value="1" placeholder="1">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="salaireMin">Salaire Min (€) *</label>
                            <input type="text" name="salaireMin" id="salaireMin" placeholder="45000">
                        </div>
                        <div class="form-group">
                            <label for="salaireMax">Salaire Max (€) *</label>
                            <input type="text" name="salaireMax" id="salaireMax" placeholder="55000">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="typeContrat">Type de Contrat</label>
                            <select name="typeContrat" id="typeContrat">
                                <option value="CDI">CDI</option>
                                <option value="CDD">CDD</option>
                                <option value="Freelance">Freelance</option>
                                <option value="Stage">Stage</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="experienceRequise">Expérience Requise</label>
                            <select name="experienceRequise" id="experienceRequise">
                                <option value="Débutant">Débutant (0-1 an)</option>
                                <option value="Junior">Junior (1-3 ans)</option>
                                <option value="Intermédiaire">Intermédiaire (3-5 ans)</option>
                                <option value="Senior">Senior (5+ ans)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dateLimite">Date Limite</label>
                        <input type="text" name="dateLimite" id="dateLimite" placeholder="AAAA-MM-JJ">
                    </div>

                    <div class="form-group">
                        <label for="statut">Statut</label>
                        <select name="statut" id="statut">
                            <option value="active">Active</option>
                            <option value="closed">Fermée</option>
                            <option value="draft">Brouillon</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description du Poste *</label>
                        <textarea name="description" id="description" rows="4" placeholder="Décrivez le poste et les missions..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Compétences Requises *</label>
                        <div class="skills-input" id="skillsDisplay"></div>
                        <input type="hidden" name="competences" id="competencesInput" value="[]">
                        <div class="input-group">
                            <input type="text" id="skillInput" placeholder="Ajouter une compétence">
                            <button type="button" class="btn btn-primary" onclick="addSkill(); return false;">Ajouter</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Exigences / Requirements *</label>
                        <div class="requirements-list" id="requirementsList"></div>
                        <input type="hidden" name="requirements" id="requirementsInput" value="[]">
                        <button type="button" class="btn btn-outline btn-add-requirement" onclick="addRequirement(); return false;">
                            <i class="fas fa-plus"></i> Ajouter une exigence
                        </button>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="closeModal(); return false;">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Publier l'Offre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUTER/MODIFIER CATÉGORIE -->
    <div class="modal" id="modalCategory">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="section-title" id="modalCategoryTitle">Créer une Catégorie</h2>
                <button class="btn-close" onclick="closeCategoryModal(); return false;" type="button"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form class="offer-form" id="categoryForm" method="POST" action="" onsubmit="return validateCategoryForm()">
                    <input type="hidden" name="action" id="categoryFormAction" value="addCategory">
                    <input type="hidden" name="id" id="categoryId">
                    
                    <div class="form-group">
                        <label for="categoryNom">Nom de la Catégorie *</label>
                        <input type="text" name="nom" id="categoryNom" 
                               placeholder="Ex: Développement Web">
                    </div>

                    <div class="form-group">
                        <label for="categoryDescription">Description</label>
                        <textarea name="description" id="categoryDescription" 
                                  rows="4" 
                                  placeholder="Décrivez cette famille de métiers..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="categoryIcone">Icône Font Awesome (optionnel)</label>
                        <input type="text" name="icone" id="categoryIcone" 
                               placeholder="Ex: fa-code, fa-palette, fa-chart-line">
                        <small style="color: #a0aec0; margin-top: 5px; display: block;">
                            Exemples: fa-code, fa-palette, fa-chart-line, fa-tasks, fa-mobile-alt, fa-cloud
                        </small>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="closeCategoryModal(); return false;">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- NOTIFICATION -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span>Action réussie!</span>
    </div>

    <?php if (!empty($successMessage)): ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            showNotification('<?= addslashes($successMessage) ?>', 'success');
        });
    </script>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            showNotification('<?= addslashes($errorMessage) ?>', 'error');
        });
    </script>
    <?php endif; ?>

    <script type="text/javascript">
    // Fonction pour afficher/masquer les sections
    function showSection(section) {
        document.getElementById('offres-section').style.display = section === 'offres' ? 'block' : 'none';
        document.getElementById('categories-section').style.display = section === 'categories' ? 'block' : 'none';
        
        // Mettre à jour les éléments actifs du menu
        var navItems = document.querySelectorAll('.nav-item');
        for (var i = 0; i < navItems.length; i++) {
            navItems[i].className = navItems[i].className.replace(' active', '');
        }
        event.target.className += ' active';
    }

    function deleteOffer(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            form.innerHTML = '<input type="hidden" name="action" value="delete">' +
                            '<input type="hidden" name="id" value="' + id + '">';
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>

    <script type="text/javascript" src="CRUD.js"></script>
    <script type="text/javascript" src="CRUDcategory.js"></script>
</body>
</html>
