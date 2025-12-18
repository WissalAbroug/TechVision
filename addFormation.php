<?php
include '../../controller/FormationController.php';
require_once __DIR__ . '/../../model/Formation.php';

$error = "";
$formationC = new FormationController();

if (
    isset($_POST["nom"]) && isset($_POST["date_formation"]) && isset($_POST["niveau"]) &&
    isset($_POST["places_max"]) && isset($_POST["prix"])
) {
    if (
        !empty($_POST["nom"]) && !empty($_POST["date_formation"]) && !empty($_POST["niveau"]) &&
        !empty($_POST["places_max"]) && !empty($_POST["prix"])
    ) {
        $formation = new Formation(
            null,
            $_POST['nom'],
            new DateTime($_POST['date_formation']),
            $_POST['niveau'],
            (int)$_POST['places_max'],
            0,
            (float)$_POST['prix'],
            $_POST['description'] ?? '',
            'Active'
        );
        $formationC->addFormation($formation);
        header('Location: formationList.php');
        exit;
    } else {
        $error = "Informations manquantes";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="fr">

<head>
    <title>Ajouter une Formation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../assets/css/back.css">
    <style>
        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .success-border {
            border-color: #10b981 !important;
        }

        .error-border {
            border-color: #ef4444 !important;
        }

        .char-count {
            text-align: right;
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }

        .char-count.warning {
            color: #f59e0b;
        }

        .char-count.error {
            color: #ef4444;
        }

        /* Styles pour les toasts */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
        }

        .toast {
            background: white;
            border-radius: 10px;
            padding: 16px 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 15px;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-left: 5px solid;
            animation: slideInRight 0.5s forwards;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(400px);
            opacity: 0;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-icon {
            font-size: 24px;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #9ca3af;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .toast-close:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .toast-error {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }

        .toast-error .toast-icon {
            color: #ef4444;
        }

        .toast-error .toast-title {
            color: #991b1b;
        }

        .toast-success {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .toast-success .toast-icon {
            color: #10b981;
        }

        .toast-success .toast-title {
            color: #065f46;
        }

        .toast-warning {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        }

        .toast-warning .toast-icon {
            color: #f59e0b;
        }

        .toast-warning .toast-title {
            color: #92400e;
        }

        .toast-info {
            border-left-color: #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }

        .toast-info .toast-icon {
            color: #3b82f6;
        }

        .toast-info .toast-title {
            color: #1e40af;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
            border-radius: 0 0 0 5px;
            animation: progress 5s linear forwards;
        }

        @keyframes progress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>
</head>

<body>
    <!-- Container pour les toasts -->
    <div class="toast-container" id="toastContainer"></div>

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
                                <h2>Ajouter une formation</h2>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="breadcrumb-wrapper">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="../../admin.php">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="formationList.php">Formations</a></li>
                                        <li class="breadcrumb-item active">Ajouter</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <div class="card-style">
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="" method="POST" class="form-style" id="formationForm">
                                <div class="form-group">
                                    <label for="nom">Nom de la formation *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: Communication Professionnelle">
                                    <small class="error-message" id="nom-error"></small>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="date_formation">Date de la formation *</label>
                                        <input type="date" class="form-control" id="date_formation" name="date_formation">
                                        <small class="error-message" id="date_formation-error"></small>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="niveau">Niveau *</label>
                                        <select class="form-control" id="niveau" name="niveau">
                                            <option value="">-- Sélectionnez --</option>
                                            <option value="Débutant">Débutant</option>
                                            <option value="Moyen">Moyen</option>
                                            <option value="Expert">Expert</option>
                                        </select>
                                        <small class="error-message" id="niveau-error"></small>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="places_max">Nombre de places max *</label>
                                        <input type="number" class="form-control" id="places_max" name="places_max" min="1" placeholder="Ex: 20">
                                        <small class="error-message" id="places_max-error"></small>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="prix">Prix (TND) *</label>
                                        <input type="number" step="0.01" class="form-control" id="prix" name="prix" min="0" placeholder="Ex: 350.00">
                                        <small class="error-message" id="prix-error"></small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Description de la formation..."></textarea>
                                    <div class="char-count" id="description-count">0/500 caractères</div>
                                    <small class="error-message" id="description-error"></small>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">
                                        ➕ Ajouter la formation
                                    </button>
                                    <a href="formationList.php" class="btn-secondary">Annuler</a>
                                </div>
                            </form>
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
        // FONCTION POUR AFFICHER LES TOASTS
        function showToast(type, title, message, duration = 5000) {
            const toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) return;

            // Créer le toast
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            // Icône selon le type
            let icon = 'ℹ️';
            if (type === 'success') icon = '✅';
            if (type === 'error') icon = '❌';
            if (type === 'warning') icon = '⚠️';
            if (type === 'info') icon = 'ℹ️';

            toast.innerHTML = `
                <div class="toast-icon">${icon}</div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.classList.add('hide'); setTimeout(() => this.parentElement.remove(), 400);">×</button>
                <div class="toast-progress" style="animation-duration: ${duration}ms"></div>
            `;

            // Ajouter le toast au container
            toastContainer.appendChild(toast);

            // Animation d'entrée
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);

            // Suppression automatique après la durée
            setTimeout(() => {
                toast.classList.add('hide');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 400);
            }, duration);

            // Limiter le nombre de toasts à 3
            const toasts = toastContainer.querySelectorAll('.toast');
            if (toasts.length > 3) {
                toasts[0].classList.add('hide');
                setTimeout(() => {
                    if (toasts[0].parentNode) {
                        toasts[0].remove();
                    }
                }, 400);
            }
        }

        // CONTROLES DE SAISIE POUR LE FORMULAIRE D'AJOUT DE FORMATION (UNIQUEMENT JAVASCRIPT)
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('formationForm');

            if (form) {
                // Contrôle du nom de formation
                var nomInput = document.getElementById('nom');
                if (nomInput) {
                    nomInput.addEventListener('input', function() {
                        if (this.value.length < 3) {
                            this.classList.remove('success-border');
                            this.classList.add('error-border');
                            document.getElementById('nom-error').textContent = 'Le nom doit contenir au moins 3 caractères';
                        } else if (this.value.length > 100) {
                            this.classList.remove('success-border');
                            this.classList.add('error-border');
                            document.getElementById('nom-error').textContent = 'Le nom ne doit pas dépasser 100 caractères';
                        } else {
                            this.classList.remove('error-border');
                            this.classList.add('success-border');
                            document.getElementById('nom-error').textContent = '';
                        }
                    });
                }

                // Contrôle de la date de formation (doit être future)
                var dateInput = document.getElementById('date_formation');
                if (dateInput) {
                    // Définir la date minimale à aujourd'hui
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0');
                    var yyyy = today.getFullYear();
                    dateInput.min = yyyy + '-' + mm + '-' + dd;

                    dateInput.addEventListener('change', function() {
                        var selectedDate = new Date(this.value);
                        var today = new Date();
                        today.setHours(0, 0, 0, 0);

                        if (selectedDate < today) {
                            this.classList.remove('success-border');
                            this.classList.add('error-border');
                            document.getElementById('date_formation-error').textContent = 'La date de formation doit être future';
                        } else {
                            this.classList.remove('error-border');
                            this.classList.add('success-border');
                            document.getElementById('date_formation-error').textContent = '';
                        }
                    });
                }

                // Contrôle du niveau
                var niveauSelect = document.getElementById('niveau');
                if (niveauSelect) {
                    niveauSelect.addEventListener('change', function() {
                        if (!this.value) {
                            this.classList.remove('success-border');
                            this.classList.add('error-border');
                            document.getElementById('niveau-error').textContent = 'Veuillez sélectionner un niveau';
                        } else {
                            this.classList.remove('error-border');
                            this.classList.add('success-border');
                            document.getElementById('niveau-error').textContent = '';
                        }
                    });
                }

                // Contrôle du nombre de places
                var placesInput = document.getElementById('places_max');
                if (placesInput) {
                    placesInput.addEventListener('input', function() {
                        var value = parseInt(this.value);
                        if (isNaN(value) || value < 1 || value > 100) {
                            this.classList.remove('success-border');
                            this.classList.add('error-border');
                            document.getElementById('places_max-error').textContent = 'Le nombre de places doit être entre 1 et 100';
                        } else {
                            this.classList.remove('error-border');
                            this.classList.add('success-border');
                            document.getElementById('places_max-error').textContent = '';
                        }
                    });
                }

                // Contrôle du prix
                var prixInput = document.getElementById('prix');
                if (prixInput) {
                    prixInput.addEventListener('input', function() {
                        var value = parseFloat(this.value);
                        if (isNaN(value) || value < 0 || value > 10000) {
                            this.classList.remove('success-border');
                            this.classList.add('error-border');
                            document.getElementById('prix-error').textContent = 'Le prix doit être entre 0 et 10000 TND';
                        } else {
                            this.classList.remove('error-border');
                            this.classList.add('success-border');
                            document.getElementById('prix-error').textContent = '';
                        }
                    });
                }

                // Contrôle de la description avec compteur de caractères
                var descriptionInput = document.getElementById('description');
                var descriptionCount = document.getElementById('description-count');
                if (descriptionInput && descriptionCount) {
                    descriptionInput.addEventListener('input', function() {
                        var length = this.value.length;
                        descriptionCount.textContent = length + '/500 caractères';

                        if (length > 500) {
                            this.classList.remove('success-border');
                            this.classList.add('error-border');
                            descriptionCount.classList.remove('warning');
                            descriptionCount.classList.add('error');
                            document.getElementById('description-error').textContent = 'La description ne doit pas dépasser 500 caractères';
                        } else if (length > 450) {
                            this.classList.remove('error-border');
                            this.classList.add('success-border');
                            descriptionCount.classList.add('warning');
                            descriptionCount.classList.remove('error');
                            document.getElementById('description-error').textContent = '';
                        } else {
                            this.classList.remove('error-border');
                            this.classList.add('success-border');
                            descriptionCount.classList.remove('warning', 'error');
                            document.getElementById('description-error').textContent = '';
                        }
                    });
                }

                // Validation à la soumission
                form.addEventListener('submit', function(e) {
                    var errors = [];

                    // Vérifier le nom
                    if (!nomInput.value.trim()) {
                        errors.push('Le nom de la formation est obligatoire');
                        nomInput.classList.add('error-border');
                    } else if (nomInput.value.length < 3 || nomInput.value.length > 100) {
                        errors.push('Le nom doit contenir entre 3 et 100 caractères');
                        nomInput.classList.add('error-border');
                    }

                    // Vérifier la date
                    if (!dateInput.value) {
                        errors.push('La date de formation est obligatoire');
                        dateInput.classList.add('error-border');
                    } else if (dateInput.value) {
                        var selectedDate = new Date(dateInput.value);
                        var today = new Date();
                        today.setHours(0, 0, 0, 0);
                        if (selectedDate < today) {
                            errors.push('La date de formation doit être future');
                            dateInput.classList.add('error-border');
                        }
                    }

                    // Vérifier le niveau
                    if (!niveauSelect.value) {
                        errors.push('Le niveau est obligatoire');
                        niveauSelect.classList.add('error-border');
                    }

                    // Vérifier les places
                    if (!placesInput.value) {
                        errors.push('Le nombre de places est obligatoire');
                        placesInput.classList.add('error-border');
                    } else {
                        var placesValue = parseInt(placesInput.value);
                        if (isNaN(placesValue) || placesValue < 1 || placesValue > 100) {
                            errors.push('Le nombre de places doit être entre 1 et 100');
                            placesInput.classList.add('error-border');
                        }
                    }

                    // Vérifier le prix
                    if (!prixInput.value) {
                        errors.push('Le prix est obligatoire');
                        prixInput.classList.add('error-border');
                    } else {
                        var prixValue = parseFloat(prixInput.value);
                        if (isNaN(prixValue) || prixValue < 0 || prixValue > 10000) {
                            errors.push('Le prix doit être entre 0 et 10000 TND');
                            prixInput.classList.add('error-border');
                        }
                    }

                    // Vérifier la description
                    if (descriptionInput && descriptionInput.value.length > 500) {
                        errors.push('La description ne doit pas dépasser 500 caractères');
                        descriptionInput.classList.add('error-border');
                    }

                    if (errors.length > 0) {
                        e.preventDefault();
                        // Afficher un toast d'erreur au lieu d'une alerte
                        showToast('error', 'Erreurs de validation', errors.join('<br>'), 8000);
                        return false;
                    }

                    return true;
                });
            }
        });
    </script>
</body>

</html>