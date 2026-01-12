// Variables globales
var userProfile = {
    skills: ['JavaScript', 'React', 'Node.js'],
    favorites: []
};
// Initialiser le tiroir au chargement
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM chargé, initialisation du tiroir');
            
            // Initialiser le bouton du tiroir
            document.getElementById('btnToggleDrawer').onclick = function() {
                console.log('Bouton cliqué');
                openDrawer();
            };
            
            // Fonctions de base pour le tiroir
            window.openDrawer = function() {
                console.log('openDrawer appelé');
                var drawer = document.getElementById('drawer');
                var overlay = document.getElementById('drawerOverlay');
                
                if (drawer && overlay) {
                    drawer.classList.add('active');
                    overlay.classList.add('active');
                    document.body.classList.add('drawer-open');
                    console.log('Tiroir ouvert - état:', {
                        drawer: drawer.classList.contains('active'),
                        overlay: overlay.classList.contains('active')
                    });
                } else {
                    console.error('Éléments non trouvés:', {
                        drawer: !!drawer,
                        overlay: !!overlay
                    });
                }
            };
            
            window.closeDrawer = function() {
                console.log('closeDrawer appelé');
                var drawer = document.getElementById('drawer');
                var overlay = document.getElementById('drawerOverlay');
                
                if (drawer && overlay) {
                    drawer.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.classList.remove('drawer-open');
                }
            };
            
            // Fermer le tiroir en cliquant sur l'overlay
            document.getElementById('drawerOverlay').onclick = function() {
                closeDrawer();
            };
            
            // Fermer avec la touche Échap
            document.onkeydown = function(e) {
                if (e.key === 'Escape') {
                    closeDrawer();
                }
            };
            
            // Vérifier l'état initial
            console.log('État initial du tiroir:', {
                drawerVisible: document.getElementById('drawer').classList.contains('active'),
                boutonVisible: document.getElementById('btnToggleDrawer').style.display !== 'none'
            });
        });
// Fonction pour mettre à jour la progression du CV
function updateCVProgress(step) {
    var progressBar = document.querySelector('.progress-fill');
    var steps = document.querySelectorAll('.progress-steps .step');
    
    if (progressBar) {
        var percentage = (step / 5) * 100;
        progressBar.style.width = percentage + '%';
    }
    
    // Mettre à jour les étapes visuellement
    if (steps.length > 0) {
        for (var i = 0; i < steps.length; i++) {
            var stepElement = steps[i];
            var stepNumber = parseInt(stepElement.getAttribute('data-step') || stepElement.textContent.match(/\d+/)?.[0]) || i + 1;
            
            if (stepNumber <= step) {
                stepElement.style.color = 'var(--accent-cyan)';
                stepElement.style.fontWeight = 'bold';
                stepElement.innerHTML = 'Étape ' + stepNumber + ' ✓';
            } else {
                stepElement.style.color = 'rgba(255, 255, 255, 0.5)';
                stepElement.style.fontWeight = 'normal';
                stepElement.innerHTML = 'Étape ' + stepNumber;
            }
        }
    }
}
// Fonctions pour le tiroir de navigation - SIMPLIFIÉES
function openDrawer() {
    var drawer = document.getElementById('drawer');
    var overlay = document.getElementById('drawerOverlay');
    
    if (drawer && overlay) {
        drawer.classList.add('active');
        overlay.classList.add('active');
        document.body.classList.add('drawer-open');
    }
}

function closeDrawer() {
    var drawer = document.getElementById('drawer');
    var overlay = document.getElementById('drawerOverlay');
    
    if (drawer && overlay) {
        drawer.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('drawer-open');
    }
}

// Fonction générique pour ouvrir un modal
function openModal(modalId) {
    closeDrawer(); // Fermer le tiroir si ouvert
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

// Mettre à jour le compteur de favoris
function updateFavoritesCount() {
    var count = userProfile.favorites.length;
    var badge = document.getElementById('favoritesCount');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
}

// Voir les favoris
function viewFavorites() {
    showNotification('Affichage des favoris en développement', 'info');
}

// Voir les candidatures
function viewApplications() {
    showNotification('Affichage des candidatures en développement', 'info');
}

// Déconnexion
function logout() {
    if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
        localStorage.clear();
        showNotification('Déconnexion réussie', 'success');
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 1500);
    }
}

// Fonction pour sauvegarder le profil
function saveProfile(event) {
    event.preventDefault();
    
    // Validation côté client
    var inputs = document.querySelectorAll('#profileForm input, #profileForm textarea');
    var isValid = true;
    
    for (var i = 0; i < inputs.length; i++) {
        var input = inputs[i];
        
        if (input.placeholder.indexOf('Email') !== -1 && input.value) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                alert('Veuillez entrer une adresse email valide');
                input.focus();
                isValid = false;
                break;
            }
        }
        
        if (input.placeholder.indexOf('Téléphone') !== -1 && input.value) {
            var phoneRegex = /^[\d\s\-\+\(\)]+$/;
            if (!phoneRegex.test(input.value)) {
                alert('Veuillez entrer un numéro de téléphone valide');
                input.focus();
                isValid = false;
                break;
            }
        }
    }
    
    if (isValid) {
        showNotification('Profil enregistré avec succès !');
        closeModal('modalProfile');
    }
    
    return false;
}

// Ouvrir modal profil
document.getElementById('btnProfile') && document.getElementById('btnProfile').addEventListener('click', function() {
    document.getElementById('modalProfile') && document.getElementById('modalProfile').classList.add('active');
});

// Ouvrir modal CV
document.getElementById('btnCV') && document.getElementById('btnCV').addEventListener('click', function() {
    document.getElementById('modalCV') && document.getElementById('modalCV').classList.add('active');
});

// Fermer modal
function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    modal && modal.classList.remove('active');
}

// Fermer modal en cliquant à l'extérieur
var modals = document.querySelectorAll('.modal');
for (var i = 0; i < modals.length; i++) {
    modals[i].addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
}

// Voir les détails d'une offre (compatible HTML4)
function viewOfferDetails(offerId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'CRUD.php?action=getOffer&id=' + offerId, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    
                    if (!response.success) {
                        showNotification('Erreur: ' + response.message, 'error');
                        return;
                    }
                    
                    var offer = response.offer;
                    displayOfferDetails(offer);
                    
                } catch (e) {
                    console.error('Erreur parsing JSON:', e);
                    showNotification('Erreur lors du traitement des données', 'error');
                }
            } else {
                console.error('Erreur HTTP:', xhr.status);
                showNotification('Erreur de connexion au serveur', 'error');
            }
        }
    };
    
    xhr.onerror = function() {
        showNotification('Erreur réseau lors du chargement', 'error');
    };
    
    xhr.send();
}

// Fonction pour afficher les détails de l'offre
function displayOfferDetails(offer) {
    var detailsContent = document.getElementById('detailsContent');
    var detailsTitle = document.getElementById('detailsTitle');
    
    if (!detailsContent || !detailsTitle) return;
    
    detailsTitle.textContent = offer.titre;
    
    var competencesHtml = '';
    if (offer.competences && Array.isArray(offer.competences)) {
        for (var i = 0; i < offer.competences.length; i++) {
            competencesHtml += '<span class="job-skill">' + escapeHtml(offer.competences[i]) + '</span>';
        }
    }
    
    var requirementsHtml = '';
    if (offer.requirements && Array.isArray(offer.requirements)) {
        requirementsHtml = '<ul style="list-style-position: inside; padding-left: 1rem;">';
        for (var i = 0; i < offer.requirements.length; i++) {
            requirementsHtml += '<li style="margin-bottom: 0.5rem;">' + escapeHtml(offer.requirements[i]) + '</li>';
        }
        requirementsHtml += '</ul>';
    }
    
    var matchScore = calculateMatchScore(offer);
    var matchedSkills = getMatchedSkills(offer);
    var missingSkills = getMissingSkills(offer);
    
    // Formatage du salaire
    var salaireMin = offer.salaireMin ? Number(offer.salaireMin).toLocaleString() : '0';
    var salaireMax = offer.salaireMax ? Number(offer.salaireMax).toLocaleString() : '0';
    
    detailsContent.innerHTML = '<div class="card">' +
        '<h3 style="color: var(--accent-orange); margin-bottom: 1rem;">' + escapeHtml(offer.titre) + '</h3>' +
        
        '<div style="margin-bottom: 2rem;">' +
        '<p style="margin-bottom: 0.5rem;"><i class="fas fa-building"></i> <strong>Société:</strong> ' + (offer.nomSociete ? escapeHtml(offer.nomSociete) : 'Non spécifié') + '</p>' +
        '<p style="margin-bottom: 0.5rem;"><i class="fas fa-map-marker-alt"></i> <strong>Localisation:</strong> ' + (offer.localisation ? escapeHtml(offer.localisation) : 'Non spécifié') + '</p>' +
        '<p style="margin-bottom: 0.5rem;"><i class="fas fa-euro-sign"></i> <strong>Salaire:</strong> ' + salaireMin + ' - ' + salaireMax + '€</p>' +
        '<p style="margin-bottom: 0.5rem;"><i class="fas fa-briefcase"></i> <strong>Type de contrat:</strong> ' + (offer.typeContrat ? escapeHtml(offer.typeContrat) : 'Non spécifié') + '</p>' +
        '<p style="margin-bottom: 0.5rem;"><i class="fas fa-clock"></i> <strong>Expérience:</strong> ' + (offer.experienceRequise ? escapeHtml(offer.experienceRequise) : 'Non spécifié') + '</p>' +
        '<p style="margin-bottom: 0.5rem;"><i class="fas fa-users"></i> <strong>Postes disponibles:</strong> ' + (offer.nbPlace || 1) + '</p>' +
        '</div>' +
        
        '<div style="margin-bottom: 2rem;">' +
        '<h4 style="color: var(--accent-green); margin-bottom: 1rem;">Description du poste</h4>' +
        '<p style="line-height: 1.8;">' + (offer.description ? escapeHtml(offer.description) : 'Aucune description disponible') + '</p>' +
        '</div>' +
        
        (competencesHtml ? '<div style="margin-bottom: 2rem;">' +
        '<h4 style="color: var(--accent-green); margin-bottom: 1rem;">Compétences requises</h4>' +
        '<div class="job-skills">' + competencesHtml + '</div>' +
        '</div>' : '') +
        
        (requirementsHtml ? '<div style="margin-bottom: 2rem;">' +
        '<h4 style="color: var(--accent-green); margin-bottom: 1rem;">Exigences détaillées</h4>' +
        requirementsHtml +
        '</div>' : '') +
        
        '<div style="background: rgba(32, 201, 151, 0.1); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(32, 201, 151, 0.3);">' +
        '<h4 style="color: var(--accent-green); margin-bottom: 1rem;"><i class="fas fa-chart-line"></i> Votre compatibilité</h4>' +
        '<div style="display: flex; align-items: center; gap: 2rem;">' +
        '<div style="font-size: 3rem; font-weight: bold; color: var(--accent-green);">' + matchScore + '%</div>' +
        '<div style="flex: 1;">' +
        '<p style="margin-bottom: 0.5rem;"><i class="fas fa-check" style="color: var(--accent-green);"></i> Compétences acquises: ' + matchedSkills.length + '/' + (offer.competences ? offer.competences.length : 0) + '</p>' +
        '<p style="margin-bottom: 0.5rem;"><i class="fas fa-times" style="color: var(--accent-pink);"></i> Compétences manquantes: ' + (missingSkills.join(', ') || 'Aucune') + '</p>' +
        '</div></div></div>' +
        
        '<div style="margin-top: 2rem; display: flex; gap: 1rem;">' +
        '<button type="button" class="btn btn-primary btn-full" onclick="applyToOffer(' + offer.id + ')">' +
        '<i class="fas fa-paper-plane"></i> Postuler maintenant</button>' +
        '<button type="button" class="btn btn-outline" onclick="toggleFavorite(' + offer.id + ')">' +
        '<i class="far fa-heart"></i> Ajouter aux favoris</button>' +
        '</div></div>';
    
    document.getElementById('modalOfferDetails').classList.add('active');
}

// Calculer le score de matching
function calculateMatchScore(offer) {
    var matchedSkills = getMatchedSkills(offer);
    var totalSkills = (offer.competences || []).length;
    return totalSkills > 0 ? Math.round((matchedSkills.length / totalSkills) * 100) : 0;
}

// Obtenir les compétences correspondantes
function getMatchedSkills(offer) {
    var offerSkills = offer.competences || [];
    var matched = [];
    
    for (var i = 0; i < offerSkills.length; i++) {
        var offerSkill = offerSkills[i].toLowerCase();
        for (var j = 0; j < userProfile.skills.length; j++) {
            if (userProfile.skills[j].toLowerCase() === offerSkill) {
                matched.push(offerSkills[i]);
                break;
            }
        }
    }
    
    return matched;
}

// Obtenir les compétences manquantes
function getMissingSkills(offer) {
    var offerSkills = offer.competences || [];
    var missing = [];
    
    for (var i = 0; i < offerSkills.length; i++) {
        var offerSkill = offerSkills[i].toLowerCase();
        var found = false;
        
        for (var j = 0; j < userProfile.skills.length; j++) {
            if (userProfile.skills[j].toLowerCase() === offerSkill) {
                found = true;
                break;
            }
        }
        
        if (!found) {
            missing.push(offerSkills[i]);
        }
    }
    
    return missing;
}

// Postuler à une offre
function applyToOffer(offerId) {
    if (confirm('Êtes-vous sûr de vouloir postuler à cette offre ? Votre CV et profil seront envoyés à l\'entreprise.')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        form.innerHTML = '<input type="hidden" name="action" value="apply">' +
                         '<input type="hidden" name="offer_id" value="' + offerId + '">';
        document.body.appendChild(form);
        form.submit();
        
        showNotification('Candidature envoyée avec succès !');
    }
}

// Ajouter/retirer des favoris
function toggleFavorite(offerId) {
    var index = userProfile.favorites.indexOf(offerId);
    var btn = event && event.target && event.target.closest ? event.target.closest('.btn-favorite') : null;
    var icon = btn ? btn.querySelector('i') : null;
    
    if (index === -1) {
        userProfile.favorites.push(offerId);
        if (icon) {
            icon.classList.remove('far');
            icon.classList.add('fas');
        }
        showNotification('Ajouté aux favoris');
    } else {
        userProfile.favorites.splice(index, 1);
        if (icon) {
            icon.classList.remove('fas');
            icon.classList.add('far');
        }
        showNotification('Retiré des favoris');
    }
    
    localStorage.setItem('favorites', JSON.stringify(userProfile.favorites));
}

// Ajouter une compétence au profil
function addProfileSkill() {
    var input = document.getElementById('profileSkillInput');
    if (!input) return;
    
    var skill = input.value.trim();
    
    if (skill && userProfile.skills.indexOf(skill) === -1) {
        if (skill.length > 50) {
            alert('Une compétence ne peut pas dépasser 50 caractères');
            return;
        }
        userProfile.skills.push(skill);
        updateProfileSkillsDisplay();
        input.value = '';
        
        localStorage.setItem('skills', JSON.stringify(userProfile.skills));
    }
}

// Mettre à jour l'affichage des compétences du profil
function updateProfileSkillsDisplay() {
    var display = document.getElementById('profileSkills');
    if (display) {
        var html = '';
        for (var i = 0; i < userProfile.skills.length; i++) {
            html += '<span class="skill-tag">' +
                    escapeHtml(userProfile.skills[i]) +
                    ' <i class="fas fa-times" onclick="removeProfileSkill(\'' + escapeHtml(userProfile.skills[i]) + '\')"></i>' +
                    '</span>';
        }
        display.innerHTML = html;
    }
}

// Supprimer une compétence du profil
function removeProfileSkill(skill) {
    userProfile.skills = userProfile.skills.filter(function(s) {
        return s !== skill;
    });
    updateProfileSkillsDisplay();
    localStorage.setItem('skills', JSON.stringify(userProfile.skills));
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

// Charger les données du localStorage au démarrage
document.addEventListener('DOMContentLoaded', function() {
    var savedSkills = localStorage.getItem('skills');
    var savedFavorites = localStorage.getItem('favorites');
    
    if (savedSkills) {
        try {
            userProfile.skills = JSON.parse(savedSkills);
            updateProfileSkillsDisplay();
        } catch (e) {
            console.error('Erreur parsing skills:', e);
        }
    }
    
    if (savedFavorites) {
        try {
            userProfile.favorites = JSON.parse(savedFavorites);
            
            for (var i = 0; i < userProfile.favorites.length; i++) {
                var offerId = userProfile.favorites[i];
                var card = document.querySelector('[data-id="' + offerId + '"]');
                if (card) {
                    var icon = card.querySelector('.btn-favorite i');
                    if (icon) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    }
                }
            }
        } catch (e) {
            console.error('Erreur parsing favorites:', e);
        }
    }
    
    // Vérifier si une candidature a été envoyée
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('applied') === '1') {
        showNotification('Candidature envoyée avec succès !');
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
        // Permettre d'ajouter une compétence avec Enter
    var skillInput = document.getElementById('profileSkillInput');
    if (skillInput) {
        skillInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addProfileSkill();
            }
        });
    }
    
    // Initialiser le tiroir
    document.getElementById('btnToggleDrawer').addEventListener('click', openDrawer);
    
    // Mettre à jour le compteur de favoris au chargement
    updateFavoritesCount();
    
    // Fermer le tiroir avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDrawer();
        }
    });
   var modalCV = document.getElementById('modalCV');
    if (modalCV) {
        // Réinitialiser à l'étape 1 quand le modal s'ouvre
        modalCV.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('modalCV');
                // Réinitialiser pour la prochaine ouverture
                setTimeout(function() {
                    showCVStep(1);
                    document.getElementById('cvPreview').style.display = 'none';
                }, 300);
            }
        });
    }
}); // ← Fermeture du DOMContentLoaded
// ============================================
// FONCTIONS POUR LE TIROIR DE NAVIGATION
// ============================================

// 🔹 Mon Profil - Sous-fonctions
function viewSkills() {
    openModal('modalProfileFull');
    showNotification('Affichage des compétences', 'info');
}

function viewExperiences() {
    openModal('modalProfileFull');
    showNotification('Affichage des expériences professionnelles', 'info');
}

function viewFormations() {
    openModal('modalProfileFull');
    showNotification('Affichage des formations', 'info');
}

// 🔹 Mon CV
function viewCVHistory() {
    openModal('modalCVHistory');
}

// 🔹 Mes Actions - Sous-fonctions
function viewRecentOffers() {
    showNotification('Affichage des offres consultées récemment', 'info');
}

function viewSavedOffers() {
    showNotification('Affichage des offres sauvegardées', 'info');
}

function viewTests() {
    showNotification('Affichage des tests passés', 'info');
}

function viewViewedCompanies() {
    showNotification('Affichage des entreprises vues', 'info');
}

function viewRecentActivity() {
    openModal('modalRecentActivity');
}

// 🔹 Favoris
function viewFavoritesDetailed() {
    openModal('modalFavoritesDetailed');
}

// 🔹 Candidatures
function viewApplicationsDetailed() {
    openModal('modalApplicationsDetailed');
}

// 🔹 Déconnexion
function confirmLogout() {
    openModal('modalConfirmLogout');
}

function performLogout() {
    localStorage.clear();
    sessionStorage.clear();
    showNotification('Déconnexion réussie', 'success');
    setTimeout(function() {
        window.location.href = 'login.php';
    }, 1500);
}

// ============================================
// FONCTIONS POUR LES CATÉGORIES SPÉCIFIQUES
// ============================================

// Fonction pour filtrer par catégorie avec message spécifique
function filterByCategory(categoryId) {
    var categoryTitles = {
        1: "Data Science",
        2: "Design & UX",
        3: "Développeur",
        4: "DevOps & Cloud",
        5: "Gestion de Projet",
        6: "TalentMatch"
    };
    
    var categoryMessages = {
        1: "Aucune offre disponible pour le moment.",
        2: "Aucune offre disponible pour le moment.",
        3: "Aucune offre disponible pour le moment.",
        4: "Aucune offre disponible pour le moment.",
        5: "Aucune offre disponible pour le moment.",
        6: "Aucune recommandation disponible pour le moment."
    };
    
    var title = categoryTitles[categoryId] || "Catégorie";
    var message = categoryMessages[categoryId] || "Aucune offre disponible.";
    
    // Créer le contenu du modal
    var detailsContent = document.getElementById('categoryDetailsContent');
    var detailsTitle = document.getElementById('categoryDetailsTitle');
    
    if (!detailsContent || !detailsTitle) return;
    
    detailsTitle.textContent = title === "TalentMatch" ? "Offres recommandées pour vous" : "Offres " + title;
    
    var html = '<div class="card">' +
        '<div style="text-align: center; padding: 2rem;">' +
        '<i class="fas ' + (categoryId === 6 ? 'fa-star' : 'fa-folder-open') + 
        '" style="font-size: 4rem; color: var(--accent-orange); margin-bottom: 1rem;"></i>' +
        '<h3 style="color: var(--accent-orange); margin-bottom: 1rem;">' + 
        (categoryId === 6 ? "Offres recommandées pour vous" : "Offres " + title) + '</h3>' +
        '<p style="color: #a0aec0; font-size: 1.1rem; margin-bottom: 1.5rem;">' +
        '0 offre' + (categoryId === 6 ? ' recommandée' : '') + 
        '</p>' +
        '<div style="background: rgba(255, 71, 87, 0.1); padding: 1.5rem; border-radius: 8px; border: 1px solid rgba(255, 71, 87, 0.3);">' +
        '<p style="color: var(--text-light); margin: 0;">' +
        '<i class="fas fa-info-circle" style="color: var(--accent-orange); margin-right: 10px;"></i>' +
        message +
        '</p>' +
        '</div></div></div>';
    
    detailsContent.innerHTML = html;
    openModal('modalCategoryDetails');
}

// Initialisation des événements pour les catégories
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter les événements pour les cartes de catégories
    var categoryCards = document.querySelectorAll('.job-grid .card[onclick*="filterByCategory"]');
    categoryCards.forEach(function(card) {
        var onclickAttr = card.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes('filterByCategory')) {
            // Extraire l'ID de la catégorie
            var match = onclickAttr.match(/filterByCategory\((\d+)\)/);
            if (match) {
                var categoryId = match[1];
                // Ajouter l'événement de clic pour afficher les détails
                card.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('btn')) {
                        filterByCategory(parseInt(categoryId));
                    }
                });
            }
        }
    });
    
    // Gérer le formulaire de modification de profil
    var editProfileForm = document.getElementById('editProfileForm');
    if (editProfileForm) {
        editProfileForm.onsubmit = function(e) {
            e.preventDefault();
            showNotification('Profil modifié avec succès', 'success');
            closeModal('modalEditProfile');
            return false;
        };
    }
    document.getElementById('btnToggleAI').addEventListener('click', openAIModal);
});
console.log('CRUD.js (HTML4 compatible) chargé avec succès');