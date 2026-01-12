// Fonctions pour les catégories - FrontOffice (HTML4 compatible)

// Filtrer les offres par catégorie
function filterByCategory(categoryId) {
    var url = new URL(window.location.href);
    url.searchParams.set('category_id', categoryId);
    url.searchParams.set('search', '1');
    window.location.href = url.toString();
}

// Afficher les détails d'une catégorie
function viewCategoryDetails(categoryId) {
    fetch('CRUDcategory.php?action=getCategory&id=' + categoryId)
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (!data.success) {
                showNotification('Erreur: ' + data.message, 'error');
                return;
            }
            
            var category = data.category;
            var offers = data.offers || [];
            var offersHtml = '';
            
            if (offers.length > 0) {
                for (var i = 0; i < offers.length; i++) {
                    var offer = offers[i];
                    offersHtml += '<div class="job-card" style="margin-bottom: 15px;">' +
                        '<h4>' + escapeHtml(offer.titre) + '</h4>' +
                        '<p>' + escapeHtml(offer.nomSociete) + ' - ' + escapeHtml(offer.localisation) + '</p>' +
                        '<button type="button" class="btn btn-primary btn-full" onclick="viewOfferDetails(' + offer.id + ')">' +
                        '<i class="fas fa-eye"></i> Voir l\'offre</button>' +
                        '</div>';
                }
            } else {
                offersHtml = '<p>Aucune offre disponible</p>';
            }
            
            var detailsContent = document.getElementById('categoryDetailsContent');
            var detailsTitle = document.getElementById('categoryDetailsTitle');
            
            if (!detailsContent || !detailsTitle) return;
            
            detailsTitle.textContent = category.nom;
            
            var iconHtml = category.icone ? '<i class="fas ' + escapeHtml(category.icone) + '" style="font-size: 4rem; color: var(--accent-orange); margin-bottom: 1rem;"></i>' : '';
            
            detailsContent.innerHTML = '<div class="card">' +
                '<div style="text-align: center; margin-bottom: 2rem;">' +
                iconHtml +
                '<h3 style="color: var(--accent-orange); margin-bottom: 1rem;">' +
                escapeHtml(category.nom) + '</h3>' +
                '<p style="color: #a0aec0; font-size: 1.1rem;">' +
                (category.description ? escapeHtml(category.description) : 'Aucune description disponible') +
                '</p></div>' +
                
                '<div style="background: rgba(32, 201, 151, 0.1); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(32, 201, 151, 0.3); margin-bottom: 2rem;">' +
                '<h4 style="color: var(--accent-green); margin-bottom: 1rem;">' +
                '<i class="fas fa-briefcase"></i> ' + category.nbOffres + ' offre' + (category.nbOffres > 1 ? 's' : '') + ' active' + (category.nbOffres > 1 ? 's' : '') +
                '</h4></div>' +
                
                '<div style="margin-top: 2rem;">' +
                '<h4 style="color: var(--accent-orange); margin-bottom: 1rem;">Offres dans cette catégorie</h4>' +
                offersHtml + '</div>' +
                
                '<div style="margin-top: 2rem;">' +
                '<button type="button" class="btn btn-primary btn-full" onclick="filterByCategory(' + category.id + ')">' +
                '<i class="fas fa-filter"></i> Voir toutes les offres de cette catégorie</button>' +
                '</div></div>';
            
            document.getElementById('modalCategoryDetails').classList.add('active');
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            showNotification('Erreur lors du chargement des détails', 'error');
        });
}

// Recommander des offres basées sur la catégorie préférée
function getRecommendationsByCategory() {
    showNotification('Fonctionnalité de recommandation en développement');
}

// Échapper les caractères HTML
function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Afficher une notification
function showNotification(message, type) {
    type = type || 'success';
    var notification = document.getElementById('notification');
    if (!notification) return;
    
    var icon = notification.querySelector('i');
    
    notification.querySelector('span').textContent = message;
    
    if (type === 'error') {
        icon.className = 'fas fa-exclamation-circle';
    } else {
        icon.className = 'fas fa-check-circle';
    }
    
    notification.classList.add('show');
    
    setTimeout(function() {
        notification.classList.remove('show');
    }, 3000);
}
// Fonction pour afficher toutes les catégories dans un modal
function showAllCategories() {
    fetch('CRUDcategory.php?action=getAll')
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                // Créer un contenu HTML pour afficher toutes les catégories
                var html = '<div class="categories-grid">';
                data.categories.forEach(function(category) {
                    html += '<div class="category-card" onclick="filterByCategory(' + category.id + '); closeModal(\'allCategoriesModal\')">' +
                           '<i class="fas ' + (category.icone || 'fa-folder') + '"></i>' +
                           '<h4>' + escapeHtml(category.nom) + '</h4>' +
                           '<p>' + category.nbOffres + ' offre(s)</p>' +
                           '</div>';
                });
                html += '</div>';
                
                // Vous devrez créer un modal pour afficher ce contenu
                showNotification('Fonctionnalité en développement', 'info');
            }
        })
        .catch(function(error) {
            console.error('Erreur:', error);
        });
}

console.log('CRUDcategory.js (HTML4 compatible) chargé avec succès');