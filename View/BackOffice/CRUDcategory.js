
// Variables globales pour les catégories
var currentCategoryData = {};
var existingCategories = [];

// Fonction d'initialisation une fois le DOM chargé
function initCategoryCRUD() {
    // Ouvrir modal pour ajouter une catégorie
    var btnAddCategory = document.getElementById('btnAddCategory');
    if (btnAddCategory) {
        btnAddCategory.onclick = function() {
            resetCategoryForm();
            document.getElementById('modalCategoryTitle').textContent = 'Créer une Catégorie';
            document.getElementById('categoryFormAction').value = 'addCategory';
            document.getElementById('modalCategory').className = 'modal active';
            
            loadExistingCategories();
            return false;
        };
    }

    // Fermer modal catégorie
    var modalCategory = document.getElementById('modalCategory');
    if (modalCategory) {
        modalCategory.onclick = function(e) {
            if (e.target && e.target.id === 'modalCategory') {
                closeCategoryModal();
            }
        };
    }

    // Fermer modal avec le bouton X
    var btnCloseModal = document.querySelector('#modalCategory .btn-close');
    if (btnCloseModal) {
        btnCloseModal.onclick = function() {
            closeCategoryModal();
            return false;
        };
    }

    // Recherche en temps réel pour les catégories
    var searchCategoryInput = document.getElementById('searchCategoryInput');
    if (searchCategoryInput) {
        searchCategoryInput.onkeyup = function(e) {
            var searchTerm = e.target.value.toLowerCase();
            var categories = document.querySelectorAll('.category-item');
            
            for (var i = 0; i < categories.length; i++) {
                var category = categories[i];
                var text = category.textContent.toLowerCase();
                category.style.display = text.indexOf(searchTerm) !== -1 ? 'block' : 'none';
            }
        };
    }

    // Validation en temps réel du nom
    var categoryNom = document.getElementById('categoryNom');
    if (categoryNom) {
        categoryNom.onkeyup = function() {
            validateCategoryNameLive(this);
        };
    }

    // Gestion de la soumission du formulaire
    var categoryForm = document.getElementById('categoryForm');
    if (categoryForm) {
        categoryForm.onsubmit = function(e) {
            e.preventDefault();
            return submitCategoryForm(this);
        };
    }

    // Initialiser le sélecteur d'icônes
    initIconSelector();

    // Charger les catégories existantes
    loadExistingCategories();
}

// Fermer modal catégorie
function closeCategoryModal() {
    document.getElementById('modalCategory').className = 'modal';
    resetCategoryForm();
}

// Réinitialiser le formulaire catégorie
function resetCategoryForm() {
    var form = document.getElementById('categoryForm');
    if (form) {
        form.reset();
        document.getElementById('categoryId').value = '';
        currentCategoryData = {};
        
        var nomField = document.getElementById('categoryNom');
        if (nomField) {
            nomField.style.borderColor = '';
            nomField.style.backgroundColor = '';
        }
        
        var suggestionsDiv = document.getElementById('nameSuggestions');
        if (suggestionsDiv && suggestionsDiv.parentNode) {
            suggestionsDiv.parentNode.removeChild(suggestionsDiv);
        }
    }
}

// Fonction pour charger et afficher les catégories existantes
function loadExistingCategories() {
    console.log('Chargement des catégories existantes...');
    
    // Créer une requête XMLHttpRequest pour la compatibilité
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'CRUDcategory.php?action=getAll', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var result = JSON.parse(xhr.responseText);
                    console.log('Réponse reçue:', result);
                    
                    if (result.success && result.categories) {
                        existingCategories = [];
                        for (var i = 0; i < result.categories.length; i++) {
                            var cat = result.categories[i];
                            existingCategories.push({
                                name: cat.nom,
                                nameLower: cat.nom.toLowerCase(),
                                id: cat.id
                            });
                        }
                        
                        displayExistingCategories(result.categories);
                    } else {
                        console.error('Erreur dans la réponse:', result.message);
                    }
                } catch (e) {
                    console.error('Erreur parsing JSON:', e, 'Réponse:', xhr.responseText);
                }
            } else {
                console.error('Erreur HTTP:', xhr.status, xhr.statusText);
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('Erreur réseau lors du chargement des catégories');
    };
    
    xhr.send();
}

// Afficher les catégories existantes
function displayExistingCategories(categories) {
    var container = document.getElementById('existingCategoriesContainer');
    if (!container) {
        var modalBody = document.querySelector('#modalCategory .modal-body');
        if (modalBody) {
            var newContainer = document.createElement('div');
            newContainer.id = 'existingCategoriesContainer';
            newContainer.style.marginBottom = '20px';
            newContainer.style.padding = '15px';
            newContainer.style.background = 'rgba(10, 26, 58, 0.5)';
            newContainer.style.borderRadius = '8px';
            newContainer.style.border = '1px solid #346';
            
            modalBody.insertBefore(newContainer, modalBody.firstChild);
        }
    }
    
    var containerToUse = document.getElementById('existingCategoriesContainer');
    if (containerToUse && categories && categories.length > 0) {
        var html = '<div style="margin-bottom: 10px; font-weight: 600; color: #ffa500;">' +
            '<i class="fas fa-info-circle"></i> Catégories existantes (' + categories.length + ')' +
            '</div>' +
            '<div style="max-height: 150px; overflow-y: auto; padding: 5px;">';
        
        for (var i = 0; i < categories.length; i++) {
            var cat = categories[i];
            html += '<span class="badge badge-info" style="padding: 6px 12px; font-size: 13px; margin: 0 5px 5px 0; display: inline-block;">' +
                '<i class="fas ' + (cat.icone || 'fa-folder') + '"></i> ' +
                escapeHtml(cat.nom) +
                '<small style="margin-left: 4px; opacity: 0.7;">(' + (cat.nbOffres || 0) + ')</small>' +
                '</span>';
        }
        
        html += '</div>' +
            '<div style="margin-top: 10px; font-size: 12px; color: #a0aec0;">' +
            '<i class="fas fa-exclamation-triangle"></i> ' +
            'Vous ne pouvez pas ajouter une catégorie avec un nom qui existe déjà.' +
            '</div>';
        
        containerToUse.innerHTML = html;
    }
}

// Fonction pour échapper le HTML
function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Soumettre le formulaire de catégorie
function submitCategoryForm(form) {
    console.log('Soumission du formulaire catégorie...');
    
    if (!validateCategoryForm()) {
        return false;
    }
    
    var submitBtn = form.querySelector('button[type="submit"]');
    var originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
    submitBtn.disabled = true;
    
    // Récupérer les données du formulaire
    var formData = {
        action: document.getElementById('categoryFormAction').value,
        id: document.getElementById('categoryId').value || '',
        nom: document.getElementById('categoryNom').value || '',
        description: document.getElementById('categoryDescription').value || '',
        icone: document.getElementById('categoryIcone').value || ''
    };
    
    console.log('Données à envoyer:', formData);
    
    // Utiliser XMLHttpRequest pour une meilleure compatibilité
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'CRUDcategory.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (xhr.status === 200) {
                try {
                    var result = JSON.parse(xhr.responseText);
                    console.log('Réponse du serveur:', result);
                    
                    if (result.success) {
                        showNotification(result.message || 'Catégorie enregistrée avec succès', 'success');
                        closeCategoryModal();
                        
                        // Recharger la page après un délai
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        var errorMessage = result.message || 'Erreur inconnue';
                        
                        if (result.message && result.message.indexOf('existe déjà') !== -1) {
                            errorMessage = '❌ ' + result.message + '\n\nVeuillez choisir un nom différent.';
                            
                            var nomField = document.getElementById('categoryNom');
                            nomField.style.borderColor = '#ff6b9d';
                            nomField.style.backgroundColor = 'rgba(255, 107, 157, 0.1)';
                            nomField.focus();
                            
                            suggestAlternativeNames(nomField.value.trim());
                        }
                        
                        showNotification(errorMessage, 'error');
                    }
                } catch (e) {
                    console.error('Erreur parsing JSON:', e, 'Réponse:', xhr.responseText);
                    showNotification('Erreur de traitement de la réponse', 'error');
                }
            } else {
                console.error('Erreur HTTP:', xhr.status, xhr.statusText);
                showNotification('Erreur de connexion au serveur (HTTP ' + xhr.status + ')', 'error');
            }
        }
    };
    
    xhr.onerror = function() {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Erreur réseau');
        showNotification('Erreur de connexion au serveur', 'error');
    };
    
    // Préparer les données à envoyer
    var params = [];
    for (var key in formData) {
        if (formData.hasOwnProperty(key)) {
            params.push(encodeURIComponent(key) + '=' + encodeURIComponent(formData[key]));
        }
    }
    
    xhr.send(params.join('&'));
    return false;
}

// Suggérer des noms alternatifs
function suggestAlternativeNames(originalName) {
    var suggestionsDiv = document.getElementById('nameSuggestions');
    if (!suggestionsDiv) {
        var nomField = document.getElementById('categoryNom');
        var parent = nomField.parentNode;
        
        var newDiv = document.createElement('div');
        newDiv.id = 'nameSuggestions';
        newDiv.style.marginTop = '10px';
        newDiv.style.padding = '10px';
        newDiv.style.background = 'rgba(255, 165, 0, 0.1)';
        newDiv.style.borderRadius = '6px';
        newDiv.style.border = '1px solid rgba(255, 165, 0, 0.3)';
        newDiv.style.fontSize = '13px';
        
        parent.appendChild(newDiv);
    }
    
    var suggestions = generateNameSuggestions(originalName);
    
    if (suggestions.length > 0) {
        var html = '<div style="color: #ffa500; font-weight: 600; margin-bottom: 5px;">' +
            '<i class="fas fa-lightbulb"></i> Suggestions :' +
            '</div>' +
            '<div>';
        
        for (var i = 0; i < suggestions.length; i++) {
            var name = suggestions[i];
            html += '<span style="cursor: pointer; padding: 4px 8px; background: rgba(255, 165, 0, 0.2); ' +
                   'border-radius: 4px; border: 1px solid rgba(255, 165, 0, 0.3); margin: 0 5px 5px 0; display: inline-block;" ' +
                   'onclick="selectSuggestedName(\'' + escapeHtml(name) + '\')">' +
                   escapeHtml(name) +
                   '</span>';
        }
        
        html += '</div>';
        document.getElementById('nameSuggestions').innerHTML = html;
    }
}

// Sélectionner un nom suggéré
function selectSuggestedName(name) {
    document.getElementById('categoryNom').value = name;
    var suggestionsDiv = document.getElementById('nameSuggestions');
    if (suggestionsDiv && suggestionsDiv.parentNode) {
        suggestionsDiv.parentNode.removeChild(suggestionsDiv);
    }
}

// Générer des suggestions de noms
function generateNameSuggestions(originalName) {
    var suggestions = [];
    var suffixes = [
        'Avancé', 'Expert', 'Professionnel', 'Spécialisé', 
        '& Management', '& Stratégie', 'Digital', 'Moderne',
        '2024', 'Nouvelle Génération', 'Innovation'
    ];
    
    for (var i = 0; i < suffixes.length; i++) {
        var suggestion = originalName + ' ' + suffixes[i];
        if (suggestions.indexOf(suggestion) === -1) {
            suggestions.push(suggestion);
        }
    }
    
    if (originalName.indexOf('Web') !== -1) {
        suggestions.push('Développement Frontend', 'Développement Backend', 'Full Stack');
    }
    if (originalName.indexOf('Design') !== -1) {
        suggestions.push('UI Design', 'UX Research', 'Design Thinking');
    }
    if (originalName.indexOf('Data') !== -1) {
        suggestions.push('Big Data', 'Analytics', 'Machine Learning');
    }
    
    // Filtrer les suggestions qui existent déjà
    var filteredSuggestions = [];
    for (var j = 0; j < suggestions.length; j++) {
        var suggestion = suggestions[j];
        var suggestionLower = suggestion.toLowerCase();
        var exists = false;
        
        for (var k = 0; k < existingCategories.length; k++) {
            if (existingCategories[k].nameLower === suggestionLower) {
                exists = true;
                break;
            }
        }
        
        if (!exists) {
            filteredSuggestions.push(suggestion);
        }
    }
    
    return filteredSuggestions.slice(0, 5);
}

// Éditer une catégorie
function editCategory(id) {
    console.log('Édition catégorie ID:', id);
    
    // Créer une requête XMLHttpRequest
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'CRUDcategory.php?action=getCategory&id=' + id, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var data = JSON.parse(xhr.responseText);
                
                if (!data.success) {
                    showNotification('Erreur: ' + data.message, 'error');
                    return;
                }
                
                var category = data.category;
                document.getElementById('modalCategoryTitle').textContent = 'Modifier la Catégorie';
                document.getElementById('categoryFormAction').value = 'update';
                document.getElementById('categoryId').value = category.id;
                document.getElementById('categoryNom').value = category.nom || '';
                document.getElementById('categoryDescription').value = category.description || '';
                document.getElementById('categoryIcone').value = category.icone || '';
                
                currentCategoryData = category;
                document.getElementById('modalCategory').className = 'modal active';
                
                loadExistingCategories();
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                showNotification('Erreur lors du chargement de la catégorie', 'error');
            }
        } else if (xhr.readyState === 4) {
            showNotification('Erreur HTTP ' + xhr.status, 'error');
        }
    };
    
    xhr.send();
}

// Voir les détails d'une catégorie
function viewCategory(id) {
    console.log('Voir catégorie ID:', id);
    
    // Créer une requête XMLHttpRequest
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'CRUDcategory.php?action=getCategory&id=' + id, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var data = JSON.parse(xhr.responseText);
                
                if (!data.success) {
                    showNotification('Erreur: ' + data.message, 'error');
                    return;
                }
                
                var category = data.category;
                var details = 'Détails de la catégorie:\n\n' +
                    'Nom: ' + category.nom + '\n' +
                    'Description: ' + (category.description || 'Aucune') + '\n' +
                    'Icône: ' + (category.icone || 'Aucune') + '\n' +
                    'Nombre d\'offres: ' + category.nbOffres + '\n' +
                    'Créée le: ' + formatDate(category.dateCreation) + '\n' +
                    'Modifiée le: ' + formatDate(category.dateModification);
                
                alert(details);
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
                showNotification('Erreur lors du chargement des détails', 'error');
            }
        } else if (xhr.readyState === 4) {
            showNotification('Erreur HTTP ' + xhr.status, 'error');
        }
    };
    
    xhr.send();
}

// Formater une date
function formatDate(dateString) {
    if (!dateString) return 'Non spécifiée';
    
    try {
        var date = new Date(dateString);
        return date.toLocaleDateString('fr-FR');
    } catch (e) {
        return dateString;
    }
}

// Supprimer une catégorie
function deleteCategory(id, nbOffres) {
    if (nbOffres > 0) {
        alert('Impossible de supprimer cette catégorie car ' + nbOffres + ' offre(s) y sont associées.\n\nVeuillez d\'abord réassigner ou supprimer ces offres.');
        return;
    }

    if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?\n\nCette action est irréversible.')) {
        console.log('Suppression catégorie ID:', id);
        
        // Créer une requête XMLHttpRequest
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'CRUDcategory.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var result = JSON.parse(xhr.responseText);
                    
                    if (result.success) {
                        showNotification(result.message || 'Catégorie supprimée', 'success');
                        
                        // Recharger la page après un délai
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showNotification(result.message || 'Erreur lors de la suppression', 'error');
                    }
                } catch (e) {
                    console.error('Erreur parsing JSON:', e);
                    showNotification('Erreur de traitement de la réponse', 'error');
                }
            } else if (xhr.readyState === 4) {
                showNotification('Erreur HTTP ' + xhr.status, 'error');
            }
        };
        
        xhr.send('action=delete&id=' + encodeURIComponent(id));
    }
}

// Valider le formulaire catégorie
function validateCategoryForm() {
    var nomField = document.getElementById('categoryNom');
    var nom = nomField ? nomField.value.trim() : '';
    var nomLower = nom.toLowerCase();
    
    // Réinitialiser les styles
    if (nomField) {
        nomField.style.borderColor = '';
        nomField.style.backgroundColor = '';
    }
    
    // Supprimer les suggestions précédentes
    var suggestionsDiv = document.getElementById('nameSuggestions');
    if (suggestionsDiv && suggestionsDiv.parentNode) {
        suggestionsDiv.parentNode.removeChild(suggestionsDiv);
    }
    
    if (nom.length < 3) {
        alert('Le nom de la catégorie doit contenir au moins 3 caractères');
        if (nomField) nomField.focus();
        return false;
    }
    
    if (nom.length > 100) {
        alert('Le nom de la catégorie ne peut pas dépasser 100 caractères');
        if (nomField) nomField.focus();
        return false;
    }
    
    // Vérifier les caractères autorisés
    if (!/^[a-zA-Z0-9À-ÿ\s\-&]+$/.test(nom)) {
        alert('Le nom ne peut contenir que des lettres, chiffres, espaces, tirets et "&"');
        if (nomField) nomField.focus();
        return false;
    }
    
    // Vérifier si le nom existe déjà
    var categoryExists = false;
    var categoryId = document.getElementById('categoryId') ? document.getElementById('categoryId').value : '';
    
    for (var i = 0; i < existingCategories.length; i++) {
        var cat = existingCategories[i];
        if (cat.nameLower === nomLower && cat.id.toString() !== categoryId) {
            categoryExists = true;
            break;
        }
    }
    
    if (categoryExists) {
        var message = 'Une catégorie avec ce nom existe déjà.\n\nVeuillez choisir un nom différent.';
        
        if (!confirm(message)) {
            if (nomField) nomField.focus();
            return false;
        }
    }
    
    var descriptionField = document.getElementById('categoryDescription');
    var description = descriptionField ? descriptionField.value : '';
    if (description.length > 1000) {
        alert('La description ne peut pas dépasser 1000 caractères');
        return false;
    }
    
    var iconeField = document.getElementById('categoryIcone');
    var icone = iconeField ? iconeField.value : '';
    if (icone.length > 100) {
        alert('Le nom de l\'icône ne peut pas dépasser 100 caractères');
        return false;
    }
    
    return true;
}

// Validation en temps réel du nom
function validateCategoryNameLive(input) {
    var nom = input.value.trim();
    var nomLower = nom.toLowerCase();
    
    var suggestionsDiv = document.getElementById('nameSuggestions');
    if (suggestionsDiv && suggestionsDiv.parentNode) {
        suggestionsDiv.parentNode.removeChild(suggestionsDiv);
    }
    
    if (nom.length >= 3) {
        var categoryExists = false;
        var categoryId = document.getElementById('categoryId') ? document.getElementById('categoryId').value : '';
        
        for (var i = 0; i < existingCategories.length; i++) {
            var cat = existingCategories[i];
            if (cat.nameLower === nomLower && cat.id.toString() !== categoryId) {
                categoryExists = true;
                break;
            }
        }
        
        if (categoryExists) {
            input.style.borderColor = '#ff6b9d';
            input.style.backgroundColor = 'rgba(255, 107, 157, 0.1)';
            
            var self = input;
            setTimeout(function() {
                if (self.value.trim() === nom) {
                    suggestAlternativeNames(nom);
                }
            }, 500);
        } else {
            input.style.borderColor = '#20c997';
            input.style.backgroundColor = 'rgba(32, 201, 151, 0.1)';
        }
    } else {
        input.style.borderColor = '';
        input.style.backgroundColor = '';
    }
}

// Liste des icônes Font Awesome disponibles
var availableIcons = [
    'fa-code', 'fa-palette', 'fa-chart-line', 'fa-tasks', 'fa-mobile-alt',
    'fa-cloud', 'fa-database', 'fa-cogs', 'fa-laptop-code', 'fa-graduation-cap',
    'fa-briefcase', 'fa-lightbulb', 'fa-users', 'fa-chart-bar', 'fa-rocket',
    'fa-robot', 'fa-shield-alt', 'fa-network-wired', 'fa-search-dollar',
    'fa-brain', 'fa-magic', 'fa-bolt', 'fa-infinity', 'fa-cube'
];

// Initialiser le sélecteur d'icônes
function initIconSelector() {
    var iconeField = document.getElementById('categoryIcone');
    if (iconeField) {
        // Créer un wrapper
        var wrapper = document.createElement('div');
        wrapper.style.marginBottom = '15px';
        
        iconeField.parentNode.insertBefore(wrapper, iconeField);
        
        // Ajouter le champ
        var fieldContainer = document.createElement('div');
        fieldContainer.style.float = 'left';
        fieldContainer.style.width = '70%';
        wrapper.appendChild(fieldContainer);
        fieldContainer.appendChild(iconeField);
        
        // Ajouter le bouton
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-outline';
        button.style.float = 'left';
        button.style.marginLeft = '10px';
        button.innerHTML = '<i class="fas fa-icons"></i> Choisir';
        button.onclick = showIconPicker;
        wrapper.appendChild(button);
        
        // Ajouter l'affichage
        var display = document.createElement('div');
        display.id = 'currentIconDisplay';
        display.style.clear = 'both';
        display.style.fontSize = '14px';
        display.style.color = '#a0aec0';
        display.style.marginTop = '10px';
        wrapper.appendChild(display);
        
        // Mettre à jour l'affichage
        function updateIconDisplay() {
            if (iconeField.value) {
                display.innerHTML = '<i class="fas ' + iconeField.value + '"></i> ' + iconeField.value;
            } else {
                display.innerHTML = '<i class="fas fa-question-circle"></i> Aucune icône sélectionnée';
            }
        }
        
        iconeField.onkeyup = updateIconDisplay;
        iconeField.onchange = updateIconDisplay;
        
        updateIconDisplay();
    }
}

// Afficher un sélecteur d'icônes
function showIconPicker() {
    var input = document.getElementById('categoryIcone');
    if (!input) return;
    
    var currentIcon = input.value || 'fa-folder';
    
    var modal = document.createElement('div');
    modal.className = 'modal active';
    modal.style.zIndex = '9999';
    modal.style.backgroundColor = 'rgba(0,0,0,0.7)';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.display = 'table';
    
    var modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    modalContent.style.maxWidth = '600px';
    modalContent.style.display = 'table-cell';
    modalContent.style.verticalAlign = 'middle';
    
    var content = '<div class="modal-header">' +
        '<h2 class="section-title">Sélectionner une icône</h2>' +
        '<button class="btn-close" type="button" onclick="closeIconPicker()">' +
            '<i class="fas fa-times"></i>' +
        '</button>' +
    '</div>' +
    '<div class="modal-body">' +
        '<div style="margin-bottom: 15px;">' +
            '<input type="text" id="iconSearch" placeholder="Rechercher une icône..." ' +
                   'style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #346; ' +
                          'background: rgba(20, 40, 80, 0.8); color: #cbd5e0;">' +
        '</div>' +
        '<div id="iconGrid" style="max-height: 400px; overflow-y: auto; padding: 10px; text-align: center;">';
    
    // Ajouter les icônes
    for (var i = 0; i < availableIcons.length; i++) {
        var icon = availableIcons[i];
        var isSelected = icon === currentIcon;
        content += '<div class="icon-selector" ' +
                   'data-icon="' + icon + '" ' +
                   'style="cursor: pointer; padding: 15px 10px; border-radius: 8px; ' +
                          'transition: all 0.3s; border: 2px solid ' + (isSelected ? '#ffa500' : 'transparent') + ';' +
                          'background: ' + (isSelected ? 'rgba(255,165,0,0.1)' : 'transparent') + '; ' +
                          'display: inline-block; width: 30%; margin: 5px 1%; text-align: center;" ' +
                   'onclick="selectIcon(\'' + icon + '\')">' +
                    '<i class="fas ' + icon + '" style="font-size: 24px; margin-bottom: 8px; ' +
                        'color: ' + (isSelected ? '#ffa500' : '#cbd5e0') + '"></i>' +
                    '<div style="font-size: 11px; color: #a0aec0;">' + icon.replace('fa-', '') + '</div>' +
                '</div>';
    }
    
    content += '</div></div>';
    
    modalContent.innerHTML = content;
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Gestion de la recherche
    setTimeout(function() {
        var searchInput = document.getElementById('iconSearch');
        if (searchInput) {
            searchInput.focus();
            searchInput.onkeyup = function(e) {
                var searchTerm = e.target.value.toLowerCase();
                var icons = modal.querySelectorAll('.icon-selector');
                
                for (var j = 0; j < icons.length; j++) {
                    var icon = icons[j];
                    var iconName = icon.getAttribute('data-icon').toLowerCase();
                    icon.style.display = iconName.indexOf(searchTerm) !== -1 ? 'inline-block' : 'none';
                }
            };
        }
    }, 100);
}

// Fermer le sélecteur d'icônes
function closeIconPicker() {
    var modal = document.querySelector('.modal[style*="z-index: 9999"]');
    if (modal && modal.parentNode) {
        modal.parentNode.removeChild(modal);
    }
}

// Sélectionner une icône
function selectIcon(icon) {
    var iconeField = document.getElementById('categoryIcone');
    if (iconeField) {
        iconeField.value = icon;
        
        var iconDisplay = document.getElementById('currentIconDisplay');
        if (iconDisplay) {
            iconDisplay.innerHTML = '<i class="fas ' + icon + '"></i> ' + icon;
        }
        
        closeIconPicker();
    }
}

// Afficher une notification
function showNotification(message, type) {
    if (!type) type = 'success';
    
    var notification = document.getElementById('notification');
    if (!notification) {
        // Créer la notification si elle n'existe pas
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.className = 'notification';
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.padding = '15px 25px';
        notification.style.borderRadius = '8px';
        notification.style.boxShadow = '0 8px 25px rgba(255, 165, 0, 0.3)';
        notification.style.zIndex = '3000';
        notification.style.display = 'flex';
        notification.style.alignItems = 'center';
        notification.style.gap = '10px';
        notification.style.transform = 'translateX(150%)';
        notification.style.transition = 'transform 0.3s ease';
        
        var icon = document.createElement('i');
        notification.appendChild(icon);
        
        var span = document.createElement('span');
        notification.appendChild(span);
        
        document.body.appendChild(notification);
    }
    
    var icon = notification.querySelector('i');
    var span = notification.querySelector('span');
    
    span.textContent = message;
    
    if (type === 'error') {
        icon.className = 'fas fa-exclamation-circle';
        notification.style.background = 'linear-gradient(90deg, #ff6b9d, #ff4757)';
        notification.style.color = 'white';
    } else {
        icon.className = 'fas fa-check-circle';
        notification.style.background = 'linear-gradient(90deg, #ffa500, #20c997)';
        notification.style.color = 'white';
    }
    
    notification.style.transform = 'translateX(0)';
    
    setTimeout(function() {
        notification.style.transform = 'translateX(150%)';
    }, 3000);
}

// Initialiser quand le DOM est chargé
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCategoryCRUD);
} else {
    initCategoryCRUD();
}

console.log('CRUDcategory.js (Back Office) chargé avec succès');
