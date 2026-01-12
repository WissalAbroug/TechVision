<?php
require_once __DIR__ . '/../../Controller/OfferController.php';
require_once __DIR__ . '/../../Controller/CategoryController.php';
// Initialiser les contrôleurs
$offerController = new OfferController();
$categoryController = new CategoryController();
// Gérer les filtres de recherche
$filters = [];
if (isset($_GET['search'])) {
    if (!empty($_GET['metier'])) $filters['metier'] = $_GET['metier'];
    if (!empty($_GET['competence'])) $filters['competence'] = $_GET['competence'];
    if (!empty($_GET['localisation'])) $filters['localisation'] = $_GET['localisation'];
    if (!empty($_GET['typeContrat'])) $filters['typeContrat'] = $_GET['typeContrat'];
    if (!empty($_GET['category_id'])) $filters['category_id'] = $_GET['category_id'];
}
// Récupérer les offres (filtrées ou toutes)
if (!empty($filters)) {
    if (isset($filters['category_id'])) {
        $offers = $categoryController->getOffersByCategory($filters['category_id']);
    } else {
        $offers = $offerController->searchOffers($filters);
    }
} else {
    $offers = $offerController->getAllOffers();
}
// Récupérer toutes les catégories
$categories = $categoryController->getAllCategories();
// Filtrer uniquement les offres actives
$offers = array_filter($offers, function($o) { return $o->getStatut() === 'active'; });
// Gérer les actions POST (postuler, favoris, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'apply') {
        $offerId = $_POST['offer_id'] ?? 0;
        header('Location: ' . $_SERVER['PHP_SELF'] . '?applied=1');
        exit;
    }
}
// Simuler un score de matching
function calculateMatch($offer) {
    return rand(70, 95);
}
// Obtenir la catégorie sélectionnée
$selectedCategory = null;
if (!empty($_GET['category_id'])) {
    $selectedCategory = $categoryController->getCategoryById($_GET['category_id']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PathFinderAI - Espace Client</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="front-office.css">
</head>
<body>
        <!-- BOUTON POUR OUVRIR LE TIROIR - MODIFIÉ -->
    <button type="button" class="btn-toggle-drawer" id="btnToggleDrawer" onclick="openDrawer()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- NOUVEAU BOUTON IA -->
    <button type="button" class="btn-toggle-ai" id="btnToggleAI" onclick="openAIModal()">
        <i class="fas fa-robot"></i>
        <span class="ai-pulse"></span>
    </button>
    <!-- BOUTON POUR OUVRIR LE TIROIR - MODIFIÉ -->
    <button type="button" class="btn-toggle-drawer" id="btnToggleDrawer" onclick="openDrawer()">
        <i class="fas fa-bars"></i>
    </button>
<!-- TIROIR DE NAVIGATION MODIFIÉ -->
<div class="drawer-container">
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <h2><i class="fas fa-bars"></i> Navigation</h2>
            <button type="button" class="btn-drawer-close" onclick="closeDrawer()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="drawer-body">
            <!-- Section Utilisateur -->
            <div class="user-info-section" style="padding: 1.5rem; border-bottom: 1px solid rgba(116, 185, 255, 0.3); margin-bottom: 1.5rem;">
                <div class="user-info" style="display: flex; align-items: center; gap: 12px;">
                    <div class="user-avatar" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-cyan), var(--accent-pink)); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <h4 style="color: white; margin: 0; font-size: 1.1rem; font-weight: 600;">Nom de l'utilisateur</h4>
                        <p style="color: var(--accent-cyan); font-size: 0.85rem; margin: 5px 0 0 0; font-weight: 500;">utilisateur@email.com</p>
                    </div>
                </div>
            </div>
            
            <!-- 🔹 Mon Profil -->
            <div class="nav-section">
                <h3 class="nav-section-title">Mon Profil</h3>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="openModal('modalProfileFull'); closeDrawer();">
                            <i class="fas fa-user-circle"></i> Profil complet
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewSkills(); closeDrawer();">
                            <i class="fas fa-code"></i> Compétences
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewExperiences(); closeDrawer();">
                            <i class="fas fa-briefcase"></i> Expériences professionnelles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewFormations(); closeDrawer();">
                            <i class="fas fa-graduation-cap"></i> Formations
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="openModal('modalEditProfile'); closeDrawer();" style="background: none; border: none; width: 100%; text-align: left; color: var(--accent-green);">
                            <i class="fas fa-edit"></i> Modifier le profil
                        </button>
                    </li>
                </ul>
            </div>
            
            <!-- 🔹 Mon CV -->
            <div class="nav-section">
                <h3 class="nav-section-title">Mon CV</h3>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="openModal('modalCV'); closeDrawer();">
                            <i class="fas fa-file-alt"></i> Aperçu du CV actuel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewCVHistory(); closeDrawer();">
                            <i class="fas fa-history"></i> Historique des CV créés
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- 🔹 Mes Actions -->
            <div class="nav-section">
                <h3 class="nav-section-title">Mes Actions</h3>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewRecentOffers(); closeDrawer();">
                            <i class="fas fa-eye"></i> Offres consultées récemment
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewSavedOffers(); closeDrawer();">
                            <i class="fas fa-bookmark"></i> Offres sauvegardées
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewTests(); closeDrawer();">
                            <i class="fas fa-chart-bar"></i> Tests passés / Évaluations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewViewedCompanies(); closeDrawer();">
                            <i class="fas fa-building"></i> Entreprises vues
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewRecentActivity(); closeDrawer();">
                            <i class="fas fa-stream"></i> Activité récente (timeline)
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- 🔹 Favoris -->
            <div class="nav-section">
                <h3 class="nav-section-title">Favoris</h3>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewFavoritesDetailed(); closeDrawer();">
                            <i class="fas fa-heart"></i> Voir tous mes favoris
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- 🔹 Candidatures -->
            <div class="nav-section">
                <h3 class="nav-section-title">Candidatures</h3>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link" onclick="viewApplicationsDetailed(); closeDrawer();">
                            <i class="fas fa-paper-plane"></i> Voir mes candidatures
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- 🔹 Déconnexion -->
            <div class="nav-section">
                <h3 class="nav-section-title">Compte</h3>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <button class="nav-link" onclick="confirmLogout();" style="background: none; border: none; width: 100%; text-align: left; color: var(--accent-orange);">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

    <!-- HEADER - MODIFIÉ -->
    <div id="header">
        <div class="container header-content">
            <div class="logo">
                <i class="fas fa-compass"></i>
                <h1>TalentMatch</h1>
            </div>
            <div class="user-actions">
                <button type="button" class="btn btn-outline" onclick="openModal('modalProfile')">
                    <i class="fas fa-user"></i> Mon Profil
                </button>
                <button type="button" class="btn btn-primary" onclick="openModal('modalCV')">
                    <i class="fas fa-file-alt"></i> Mon CV
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div id="main">
        <div class="container">
            <!-- SECTION CATÉGORIES -->
            <div class="categories-section" style="margin-bottom: 3rem;">
                <h2 class="section-title">Explorer par Catégorie</h2>
                <div class="card">
                    <div class="job-grid">
                        <?php foreach ($categories as $category): ?>
                        <div class="card" style="cursor: pointer; transition: all 0.3s ease;" 
                             onclick="filterByCategory(<?= $category->getId() ?>)" 
                             onkeypress="if(event.keyCode==13) filterByCategory(<?= $category->getId() ?>)"
                             tabindex="0">
                            <div style="text-align: center;">
                                <?php if ($category->getIcone()): ?>
                                <i class="fas <?= htmlspecialchars($category->getIcone()) ?>" 
                                   style="font-size: 3rem; color: var(--accent-orange); margin-bottom: 1rem;"></i>
                                <?php else: ?>
                                <i class="fas fa-folder-open" 
                                   style="font-size: 3rem; color: var(--accent-orange); margin-bottom: 1rem;"></i>
                                <?php endif; ?>
                                <h3 style="color: var(--text-light); margin-bottom: 0.5rem; font-size: 1.2rem;">
                                    <?= htmlspecialchars($category->getNom()) ?>
                                </h3>
                                <p style="color: #a0aec0; font-size: 0.9rem; margin-bottom: 1rem;">
                                    <?= $category->getNbOffres() ?> offre<?= $category->getNbOffres() > 1 ? 's' : '' ?>
                                </p>
                                <button type="button" class="btn btn-outline" style="width: 100%;" 
                                        onclick="viewCategoryDetails(<?= $category->getId() ?>); event.stopPropagation();">
                                    <i class="fas fa-info-circle"></i> Détails
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- SECTION FILTRES -->
            <div class="filters-section">
                <h2 class="section-title">Rechercher des Offres</h2>
                <div class="card">
                    <form method="GET" action="">
                        <input type="hidden" name="search" value="1">
                        <div class="filters-grid">
                            <div class="filter-group">
                                <label for="filterMetier">Métier</label>
                                <input type="text" name="metier" id="filterMetier" 
                                       value="<?= htmlspecialchars($_GET['metier'] ?? '') ?>"
                                       placeholder="Ex: Développeur, Designer...">
                            </div>
                            <div class="filter-group">
                                <label for="filterCompetence">Compétence</label>
                                <input type="text" name="competence" id="filterCompetence" 
                                       value="<?= htmlspecialchars($_GET['competence'] ?? '') ?>"
                                       placeholder="Ex: JavaScript, Python...">
                            </div>
                            <div class="filter-group">
                                <label for="filterLocalisation">Localisation</label>
                                <input type="text" name="localisation" id="filterLocalisation" 
                                       value="<?= htmlspecialchars($_GET['localisation'] ?? '') ?>"
                                       placeholder="Ex: Paris, Lyon...">
                            </div>
                            <button type="submit" class="btn btn-primary" id="btnSearch">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                        
                        <?php if ($selectedCategory): ?>
                        <div style="margin-top: 1rem; padding: 1rem; background: rgba(255, 165, 0, 0.1); border-radius: 8px; border: 1px solid rgba(255, 165, 0, 0.3);">
                            <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                                <i class="fas fa-filter"></i> 
                                Filtré par catégorie: <strong><?= htmlspecialchars($selectedCategory->getNom()) ?></strong>
                            </p>
                            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-outline" style="font-size: 0.9rem;">
                                <i class="fas fa-times"></i> Retirer le filtre
                            </a>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- SECTION OFFRES -->
            <div class="jobs-section">
                <h2 class="section-title">
                    Offres Disponibles 
                    <span style="font-size: 1rem; color: var(--text-light); font-weight: normal;">
                        (<?= count($offers) ?> offre<?= count($offers) > 1 ? 's' : '' ?>)
                    </span>
                </h2>
                
                <?php if (count($offers) === 0): ?>
                    <div class="card">
                        <p style="text-align: center; padding: 2rem; color: var(--text-light);">
                            <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            Aucune offre ne correspond à vos critères de recherche.
                        </p>
                    </div>
                <?php else: ?>
                <div class="job-grid">
                    <?php foreach ($offers as $offer): 
                        $matchScore = calculateMatch($offer);
                    ?>
                    <div class="job-card" data-id="<?= $offer->getId() ?>">
                        <div class="job-card-header">
                            <h3 class="job-title"><?= htmlspecialchars($offer->getTitre()) ?></h3>
                            <span class="job-match"><?= $matchScore ?>% Match</span>
                        </div>
                        <p class="job-company">
                            <i class="fas fa-building"></i> <?= htmlspecialchars($offer->getNomSociete()) ?>
                        </p>
                        <p class="job-location">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($offer->getLocalisation()) ?>
                        </p>
                        <p class="job-salary">
                            <i class="fas fa-euro-sign"></i> 
                            <?= number_format($offer->getSalaireMin()) ?> - <?= number_format($offer->getSalaireMax()) ?>€
                        </p>
                        
                        <div class="job-skills">
                            <?php foreach ($offer->getCompetences() as $comp): ?>
                            <span class="job-skill"><?= htmlspecialchars($comp) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!empty($offer->getRequirements())): ?>
                        <div class="job-requirements">
                            <h4>Compétences requises:</h4>
                            <ul>
                                <?php foreach (array_slice($offer->getRequirements(), 0, 3) as $req): ?>
                                <li><?= htmlspecialchars($req) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <div class="job-actions">
                            <button type="button" class="action-btn btn-details" onclick="viewOfferDetails(<?= $offer->getId() ?>)">
                                <i class="fas fa-info-circle"></i> Détails
                            </button>
                            <button type="button" class="action-btn btn-apply" onclick="applyToOffer(<?= $offer->getId() ?>)">
                                <i class="fas fa-paper-plane"></i> Postuler
                            </button>
                            <button type="button" class="action-btn btn-favorite" onclick="toggleFavorite(<?= $offer->getId() ?>)">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- SECTION MON PROFIL (MODAL) -->
            <div class="modal" id="modalProfile">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="section-title">Mon Profil Professionnel</h2>
                        <button type="button" class="btn-close" onclick="closeModal('modalProfile')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="profileForm" onsubmit="return saveProfile(event)">
                            <div class="form-group">
                                <label>Nom Complet</label>
                                <input type="text" placeholder="Votre nom complet">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" placeholder="votre@email.com">
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="text" placeholder="+33 6 00 00 00 00">
                            </div>
                            <div class="form-group">
                                <label>Titre Professionnel</label>
                                <input type="text" placeholder="Ex: Développeur Full Stack">
                            </div>
                            <div class="form-group">
                                <label>Localisation</label>
                                <input type="text" placeholder="Ville, Pays">
                            </div>
                            <div class="form-group">
                                <label>Mes Compétences</label>
                                <div class="skills-input" id="profileSkills">
                                    <span class="skill-tag">JavaScript <i class="fas fa-times"></i></span>
                                    <span class="skill-tag">React <i class="fas fa-times"></i></span>
                                    <span class="skill-tag">Node.js <i class="fas fa-times"></i></span>
                                </div>
                                <div class="input-group">
                                    <input type="text" id="profileSkillInput" placeholder="Ajouter une compétence">
                                    <button type="button" class="btn btn-primary" onclick="addProfileSkill()">
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Bio / Présentation</label>
                                <textarea rows="4" placeholder="Parlez de vous..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-full">
                                <i class="fas fa-save"></i> Enregistrer le Profil
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- SECTION MON CV (MODAL MULTI-ÉTAPES) -->
<div class="modal" id="modalCV">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="section-title">Création de mon CV - Assistant</h2>
            <button type="button" class="btn-close" onclick="closeModal('modalCV')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <!-- Indicateur de progression -->
            <div class="cv-progress" style="margin-bottom: 2rem;">
                <div class="progress-steps" style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <div class="step" data-step="1">Étape 1</div>
                    <div class="step" data-step="2">Étape 2</div>
                    <div class="step" data-step="3">Étape 3</div>
                    <div class="step" data-step="4">Étape 4</div>
                    <div class="step" data-step="5">Étape 5</div>
                </div>
                <div class="progress-bar" style="height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px;">
                    <div class="progress-fill" style="width: 20%; height: 100%; background: var(--accent-cyan); border-radius: 2px;"></div>
                </div>
            </div>

            <!-- Étape 1 : Informations personnelles -->
            <div id="cvStep1" class="cv-step" style="display: block;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1.5rem;">
                    ÉTAPE 1 : Informations personnelles & Titre du CV
                </h3>
                
                <form id="cvFormStep1">
                    <div style="background: rgba(116, 185, 255, 0.1); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                        <h4 style="color: var(--accent-pink); margin-bottom: 1rem;">1.1 Informations personnelles</h4>
                        <div class="form-group">
                            <label>Nom et prénom *</label>
                            <input type="text" name="nom_complet" placeholder="Jean Dupont" required>
                        </div>
                        <div class="form-group">
                            <label>Adresse e-mail professionnelle *</label>
                            <input type="text" name="email" placeholder="jean.dupont@email.com" required>
                        </div>
                        <div class="form-group">
                            <label>Numéro de téléphone *</label>
                            <input type="text" name="telephone" placeholder="+33 6 00 00 00 00" required>
                        </div>
                        <div class="form-group">
                            <label>Adresse</label>
                            <input type="text" name="adresse" placeholder="Paris, France">
                        </div>
                        <div class="form-group">
                            <label>Lien LinkedIn</label>
                            <input type="text" name="linkedin" placeholder="https://linkedin.com/in/...">
                        </div>
                        <div class="form-group">
                            <label>Portfolio / GitHub / Behance</label>
                            <input type="text" name="portfolio" placeholder="https://github.com/...">
                        </div>
                        <div class="form-group">
                            <label>Photo professionnelle</label>
                            <input type="file" name="photo" accept="image/*" style="color: white;">
                        </div>
                    </div>

                    <div style="background: rgba(162, 155, 254, 0.1); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                        <h4 style="color: var(--accent-green); margin-bottom: 1rem;">1.2 Titre du CV</h4>
                        <div class="form-group">
                            <label>Quel est le poste ciblé ? *</label>
                            <input type="text" name="poste_cible" placeholder="Ex: Développeur Web Full-Stack" required>
                        </div>
                        <div class="form-group">
                            <label>Type de recherche *</label>
                            <select name="type_recherche" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(116,185,255,0.3);">
                                <option value="">Sélectionnez...</option>
                                <option value="stage">Stage</option>
                                <option value="emploi">Emploi</option>
                                <option value="alternance">Alternance</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="button" class="btn btn-outline" onclick="closeModal('modalCV')">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button type="button" class="btn btn-primary" onclick="saveCVStep(1)">
                            <i class="fas fa-arrow-right"></i> Suivant (Étape 2)
                        </button>
                    </div>
                </form>
            </div>

            <!-- Étape 2 : Profil, Compétences et Centres d'intérêt -->
            <div id="cvStep2" class="cv-step" style="display: none;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1.5rem;">
                    ÉTAPE 2 : Profil, Compétences et Centres d'intérêt
                </h3>
                
                <form id="cvFormStep2">
                    <div style="background: rgba(0, 210, 211, 0.1); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                        <h4 style="color: var(--accent-pink); margin-bottom: 1rem;">2.1 Profil / Résumé professionnel</h4>
                        <div class="form-group">
                            <label>Décris-toi en quelques lignes *</label>
                            <textarea name="profil" rows="4" placeholder="Développeur passionné avec 3 ans d'expérience..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Principales qualités professionnelles *</label>
                            <input type="text" name="qualites" placeholder="Ex: rigoureux, créatif, bon communicant" required>
                        </div>
                        <div class="form-group">
                            <label>Niveau d'expérience *</label>
                            <select name="niveau_experience" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(116,185,255,0.3);">
                                <option value="debutant">Débutant</option>
                                <option value="intermediaire">Intermédiaire</option>
                                <option value="avance">Avancé</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                    </div>

                    <div style="background: rgba(116, 185, 255, 0.1); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                        <h4 style="color: var(--accent-green); margin-bottom: 1rem;">2.2 Compétences</h4>
                        
                        <div class="form-group">
                            <label>Compétences techniques (hard skills)</label>
                            <div class="skills-input" id="skillsTechContainer">
                                <!-- Skills will be added here -->
                            </div>
                            <div class="input-group">
                                <input type="text" id="skillTechInput" placeholder="Ex: HTML, Python, React...">
                                <button type="button" class="btn btn-primary" onclick="addTechSkill()">Ajouter</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Compétences personnelles (soft skills)</label>
                            <div class="skills-input" id="skillsSoftContainer">
                                <!-- Skills will be added here -->
                            </div>
                            <div class="input-group">
                                <input type="text" id="skillSoftInput" placeholder="Ex: communication, organisation...">
                                <button type="button" class="btn btn-primary" onclick="addSoftSkill()">Ajouter</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Langues parlées</label>
                            <div id="languagesContainer">
                                <!-- Languages will be added here -->
                            </div>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <input type="text" id="languageInput" placeholder="Ex: Anglais" style="flex: 1;">
                                <select id="languageLevel" style="width: 150px; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(116,185,255,0.3);">
                                    <option value="a1">A1 (Débutant)</option>
                                    <option value="a2">A2</option>
                                    <option value="b1">B1</option>
                                    <option value="b2">B2</option>
                                    <option value="c1">C1</option>
                                    <option value="c2">C2 (Courant)</option>
                                </select>
                                <button type="button" class="btn btn-primary" onclick="addLanguage()">Ajouter</button>
                            </div>
                        </div>
                    </div>

                    <div style="background: rgba(162, 155, 254, 0.1); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                        <h4 style="color: var(--accent-orange); margin-bottom: 1rem;">2.3 Centres d'intérêt</h4>
                        <div class="form-group">
                            <label>Centres d'intérêt (séparez par des virgules)</label>
                            <input type="text" name="centres_interet" placeholder="Ex: Lecture, Sport, Voyages, Technologie...">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                        <button type="button" class="btn btn-outline" onclick="previousCVStep(2)">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <button type="button" class="btn btn-primary" onclick="saveCVStep(2)">
                            <i class="fas fa-arrow-right"></i> Suivant (Étape 3)
                        </button>
                    </div>
                </form>
            </div>

            <!-- Étape 3 : Expériences professionnelles -->
            <div id="cvStep3" class="cv-step" style="display: none;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1.5rem;">
                    ÉTAPE 3 : Expériences professionnelles
                </h3>
                
                <div id="experiencesContainer">
                    <!-- Les expériences seront ajoutées ici dynamiquement -->
                </div>

                <button type="button" class="btn btn-outline" onclick="addExperience()" style="margin-bottom: 2rem;">
                    <i class="fas fa-plus"></i> Ajouter une expérience
                </button>

                <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" onclick="previousCVStep(3)">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveCVStep(3)">
                        <i class="fas fa-arrow-right"></i> Suivant (Étape 4)
                    </button>
                </div>
            </div>

            <!-- Étape 4 : Formations, Projets et Certifications -->
            <div id="cvStep4" class="cv-step" style="display: none;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1.5rem;">
                    ÉTAPE 4 : Formations, Projets et Certifications
                </h3>
                
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-pink); margin-bottom: 1rem;">4.1 Formations</h4>
                    <div id="formationsContainer">
                        <!-- Les formations seront ajoutées ici -->
                    </div>
                    <button type="button" class="btn btn-outline" onclick="addFormation()">
                        <i class="fas fa-plus"></i> Ajouter une formation
                    </button>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-green); margin-bottom: 1rem;">4.2 Projets</h4>
                    <div id="projetsContainer">
                        <!-- Les projets seront ajoutés ici -->
                    </div>
                    <button type="button" class="btn btn-outline" onclick="addProjet()">
                        <i class="fas fa-plus"></i> Ajouter un projet
                    </button>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-orange); margin-bottom: 1rem;">4.3 Certifications</h4>
                    <div id="certificationsContainer">
                        <!-- Les certifications seront ajoutées ici -->
                    </div>
                    <button type="button" class="btn btn-outline" onclick="addCertification()">
                        <i class="fas fa-plus"></i> Ajouter une certification
                    </button>
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" onclick="previousCVStep(4)">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveCVStep(4)">
                        <i class="fas fa-arrow-right"></i> Suivant (Étape 5)
                    </button>
                </div>
            </div>

            <!-- Étape 5 : Options avancées -->
            <div id="cvStep5" class="cv-step" style="display: none;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1.5rem;">
                    ÉTAPE 5 : Options avancées
                </h3>
                
                <form id="cvFormStep5">
                    <div style="background: rgba(116, 185, 255, 0.1); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                        <h4 style="color: var(--accent-pink); margin-bottom: 1rem;">Personnalisation du CV</h4>
                        
                        <div class="form-group">
                            <label>Format souhaité</label>
                            <select name="format" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(116,185,255,0.3);">
                                <option value="pdf">PDF</option>
                                <option value="word">Word</option>
                                <option value="texte">Texte</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Style du CV</label>
                            <select name="style" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(116,185,255,0.3);">
                                <option value="moderne">Moderne</option>
                                <option value="minimaliste">Minimaliste</option>
                                <option value="classique">Classique</option>
                                <option value="creatif">Créatif</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Langue du CV</label>
                            <select name="langue" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(116,185,255,0.3);">
                                <option value="fr">Français</option>
                                <option value="en">Anglais</option>
                                <option value="es">Espagnol</option>
                                <option value="de">Allemand</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Version</label>
                            <select name="version" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(116,185,255,0.3);">
                                <option value="courte">Courte (1 page)</option>
                                <option value="detaillee">Détaillée (2+ pages)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Domaine spécifique (optionnel)</label>
                            <input type="text" name="domaine" placeholder="Ex: informatique, marketing, design...">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                        <button type="button" class="btn btn-outline" onclick="previousCVStep(5)">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <button type="button" class="btn btn-primary" onclick="generateCV()">
                            <i class="fas fa-magic"></i> Générer mon CV
                        </button>
                    </div>
                </form>
            </div>

            <!-- Aperçu du CV -->
            <div id="cvPreview" style="display: none;">
                <h3 style="color: var(--accent-green); margin-bottom: 1.5rem;">Aperçu de votre CV</h3>
                <div class="card" id="previewContent" style="padding: 2rem; background: white; color: black;">
                    <!-- L'aperçu sera généré ici -->
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" onclick="editCV()">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                    <button type="button" class="btn btn-primary" onclick="downloadCV()">
                        <i class="fas fa-download"></i> Télécharger PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- MODAL DÉTAILS OFFRE -->
            <div class="modal" id="modalOfferDetails">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="section-title" id="detailsTitle">Détails de l'Offre</h2>
                        <button type="button" class="btn-close" onclick="closeModal('modalOfferDetails')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body" id="detailsContent">
                        <!-- Le contenu sera chargé dynamiquement -->
                    </div>
                </div>
            </div>

            <!-- MODAL DÉTAILS CATÉGORIE -->
            <div class="modal" id="modalCategoryDetails">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="section-title" id="categoryDetailsTitle">Détails de la Catégorie</h2>
                        <button type="button" class="btn-close" onclick="closeModal('modalCategoryDetails')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body" id="categoryDetailsContent">
                        <!-- Le contenu sera chargé dynamiquement -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div id="footer">
        <div class="container footer-content">
            <p>&copy; 2024 TalentMatch. Tous droits réservés.</p>
            <div class="footer-links">
                <a href="#">À propos</a>
                <a href="#">Contact</a>
                <a href="#">Conditions</a>
            </div>
        </div>
    </div>

    <!-- NOTIFICATION -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span>Action réussie!</span>
    </div>
    <!-- 🔹 MODAL PROFIL COMPLET -->
<div class="modal" id="modalProfileFull">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="section-title">Mon Profil Complet</h2>
            <button type="button" class="btn-close" onclick="closeModal('modalProfileFull')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1rem;">Informations Personnelles</h3>
                <p><strong>Nom :</strong> Nom de l'utilisateur</p>
                <p><strong>Email :</strong> utilisateur@email.com</p>
                <p><strong>Téléphone :</strong> +33 6 00 00 00 00</p>
                <p><strong>Localisation :</strong> Paris, France</p>
            </div>
            
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1rem;">Compétences</h3>
                <div class="skills-input">
                    <span class="skill-tag">JavaScript</span>
                    <span class="skill-tag">React</span>
                    <span class="skill-tag">Node.js</span>
                    <span class="skill-tag">PHP</span>
                    <span class="skill-tag">MySQL</span>
                </div>
            </div>
            
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1rem;">Expériences Professionnelles</h3>
                <p>Aucune expérience professionnelle enregistrée.</p>
            </div>
            
            <div class="card" style="margin-bottom: 2rem;">
                <h3 style="color: var(--accent-cyan); margin-bottom: 1rem;">Formations</h3>
                <p>Aucune formation enregistrée.</p>
            </div>
            
            <button type="button" class="btn btn-primary btn-full" onclick="openModal('modalEditProfile')">
                <i class="fas fa-edit"></i> Modifier le profil
            </button>
        </div>
    </div>
</div>

<!-- 🔹 MODAL MODIFIER PROFIL -->
<div class="modal" id="modalEditProfile">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="section-title">Modifier mon Profil</h2>
            <button type="button" class="btn-close" onclick="closeModal('modalEditProfile')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editProfileForm">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" placeholder="Votre nom complet">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" placeholder="votre@email.com">
                </div>
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" placeholder="+33 6 00 00 00 00">
                </div>
                <div class="form-group">
                    <label>Localisation</label>
                    <input type="text" placeholder="Ville, Pays">
                </div>
                <div class="form-group">
                    <label>Compétences (séparées par des virgules)</label>
                    <textarea placeholder="JavaScript, React, Node.js, PHP..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 🔹 MODAL HISTORIQUE CV -->
<div class="modal" id="modalCVHistory">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="section-title">Historique des CV créés</h2>
            <button type="button" class="btn-close" onclick="closeModal('modalCVHistory')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="card">
                <p>Aucun CV créé pour le moment.</p>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 MODAL FAVORIS DÉTAILLÉS -->
<div class="modal" id="modalFavoritesDetailed">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="section-title">Mes Favoris</h2>
            <button type="button" class="btn-close" onclick="closeModal('modalFavoritesDetailed')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="card">
                <p>Aucune offre ajoutée aux favoris.</p>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 MODAL CANDIDATURES DÉTAILLÉES -->
<div class="modal" id="modalApplicationsDetailed">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="section-title">Mes Candidatures</h2>
            <button type="button" class="btn-close" onclick="closeModal('modalApplicationsDetailed')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="card">
                <p>Aucune candidature envoyée.</p>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 MODAL ACTIVITÉ RÉCENTE -->
<div class="modal" id="modalRecentActivity">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="section-title">Activité Récente</h2>
            <button type="button" class="btn-close" onclick="closeModal('modalRecentActivity')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-date">Aujourd'hui</div>
                    <div class="timeline-content">
                        <p>Aucune activité récente.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 MODAL CONFIRMATION DÉCONNEXION -->
<div class="modal" id="modalConfirmLogout">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2 class="section-title">Confirmation</h2>
            <button type="button" class="btn-close" onclick="closeModal('modalConfirmLogout')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-sign-out-alt" style="font-size: 4rem; color: var(--accent-orange); margin-bottom: 1.5rem;"></i>
                <h3 style="color: white; margin-bottom: 1rem;">Voulez-vous vraiment vous déconnecter ?</h3>
                <p style="color: #a0aec0; margin-bottom: 2rem;">Vous serez redirigé vers la page de connexion.</p>
                
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalConfirmLogout')" style="min-width: 120px;">
                        Annuler
                    </button>
                    <button type="button" class="btn btn-primary" onclick="performLogout()" style="min-width: 120px;">
                        Oui, me déconnecter
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL IA -->
<div class="modal" id="modalAI">
    <div class="ai-modal-content">
        <div class="ai-header">
            <h2>
                <i class="fas fa-robot"></i>
                Assistant IA PathFinder
            </h2>
            <button type="button" class="btn-close" onclick="closeAIModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="ai-body">
            <p style="color: #a0aec0; margin-bottom: 2rem; text-align: center;">
                Laissez-moi vous aider à optimiser votre recherche d'emploi et votre profil !
            </p>
            
            <div class="ai-feature-grid">
                <div class="ai-feature-card" onclick="generateCVWithAI()">
                    <i class="fas fa-file-alt"></i>
                    <h4>Générer un CV avec IA</h4>
                    <p>Créez un CV optimisé basé sur votre profil</p>
                </div>
                
                <div class="ai-feature-card" onclick="optimizeProfileWithAI()">
                    <i class="fas fa-user-edit"></i>
                    <h4>Optimiser mon profil</h4>
                    <p>Améliorez votre profil pour plus de matches</p>
                </div>
                
                <div class="ai-feature-card" onclick="analyzeOffersWithAI()">
                    <i class="fas fa-chart-line"></i>
                    <h4>Analyser les offres</h4>
                    <p>Obtenez des insights sur vos candidatures</p>
                </div>
                
                <div class="ai-feature-card" onclick="generateCoverLetter()">
                    <i class="fas fa-envelope"></i>
                    <h4>Lettre de motivation IA</h4>
                    <p>Générez une lettre personnalisée</p>
                </div>
                
                <div class="ai-feature-card" onclick="createNewDocument()">
                    <i class="fas fa-plus-circle"></i>
                    <h4>Créer un nouveau document</h4>
                    <p>Générez des documents professionnels</p>
                </div>
                
                <div class="ai-feature-card" onclick="showAIChat()">
                    <i class="fas fa-comments"></i>
                    <h4>Chat avec l'IA</h4>
                    <p>Posez-moi vos questions !</p>
                </div>
            </div>
            
            <div class="ai-input-section" id="aiChatSection" style="display: none;">
                <h4 style="color: var(--accent-cyan); margin-bottom: 1rem;">
                    <i class="fas fa-comment-dots"></i> Posez votre question
                </h4>
                <textarea class="ai-textarea" id="aiQuestion" 
                          placeholder="Ex: Comment améliorer mon CV pour un poste de développeur ?"></textarea>
                <div class="ai-actions">
                    <button type="button" class="btn-ai-secondary" onclick="clearAIChat()">
                        <i class="fas fa-times"></i> Effacer
                    </button>
                    <button type="button" class="btn-ai" onclick="submitAIQuestion()">
                        <i class="fas fa-paper-plane"></i> Envoyer
                    </button>
                </div>
            </div>
            
            <div id="aiResponse" style="display: none;">
                <!-- Les réponses de l'IA apparaîtront ici -->
            </div>
        </div>
    </div>
</div>
    <script type="text/javascript" src="CRUD.js"></script>
    <script type="text/javascript" src="CRUDcategory.js"></script>
    <script type="text/javascript" src="CRUDcv.js"></script>
    <script type="text/javascript" src="ai_functions.js"></script>
</body>
</html>