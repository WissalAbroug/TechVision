// Variables globales
let currentSkills = [];
let currentRequirements = [];

// Ouvrir modal pour ajouter une offre
document.getElementById('btnAddOffer')?.addEventListener('click', () => {
    resetForm();
    document.getElementById('modalTitle').textContent = 'Créer une Offre';
    document.getElementById('formAction').value = 'add';
    document.getElementById('modalOffer').classList.add('active');
});

// Fermer modal
function closeModal() {
    document.getElementById('modalOffer').classList.remove('active');
    resetForm();
}

// Fermer modal en cliquant à l'extérieur
document.getElementById('modalOffer')?.addEventListener('click', (e) => {
    if (e.target.id === 'modalOffer') {
        closeModal();
    }
});

// Réinitialiser le formulaire
function resetForm() {
    const form = document.getElementById('offerForm');
    if (form) {
        form.reset();
        document.getElementById('offerId').value = '';
        currentSkills = [];
        currentRequirements = [];
        updateSkillsDisplay();
        updateRequirementsDisplay();
    }
}

// Ajouter une compétence
function addSkill() {
    const input = document.getElementById('skillInput');
    const skill = input.value.trim();
    
    if (skill && !currentSkills.includes(skill)) {
        if (skill.length > 50) {
            alert('Une compétence ne peut pas dépasser 50 caractères');
            return;
        }
        currentSkills.push(skill);
        updateSkillsDisplay();
        input.value = '';
    }
}

// Supprimer une compétence
function removeSkill(skill) {
    currentSkills = currentSkills.filter(s => s !== skill);
    updateSkillsDisplay();
}

// Mettre à jour l'affichage des compétences
function updateSkillsDisplay() {
    const display = document.getElementById('skillsDisplay');
    if (display) {
        display.innerHTML = currentSkills.map(skill => `
            <span class="skill-tag">
                ${escapeHtml(skill)}
                <i class="fas fa-times" onclick="removeSkill('${escapeHtml(skill)}')"></i>
            </span>
        `).join('');
        
        // Mettre à jour le champ caché avec le JSON
        const competencesInput = document.getElementById('competencesInput');
        if (competencesInput) {
            competencesInput.value = JSON.stringify(currentSkills);
        }
    }
}

// Ajouter une exigence
function addRequirement() {
    if (currentRequirements.length >= 20) {
        alert('Maximum 20 exigences autorisées');
        return;
    }
    currentRequirements.push('');
    updateRequirementsDisplay();
    
    // Focus sur le nouveau champ
    setTimeout(() => {
        const inputs = document.querySelectorAll('.requirement-item input');
        if (inputs.length > 0) {
            inputs[inputs.length - 1].focus();
        }
    }, 100);
}

// Supprimer une exigence
function removeRequirement(index) {
    currentRequirements.splice(index, 1);
    updateRequirementsDisplay();
}

// Mettre à jour l'affichage des exigences
function updateRequirementsDisplay() {
    const list = document.getElementById('requirementsList');
    if (list) {
        list.innerHTML = currentRequirements.map((req, index) => `
            <div class="requirement-item">
                <input type="text" 
                       placeholder="Ex: React.js - 3 ans d'expérience" 
                       value="${escapeHtml(req)}"
                       onchange="updateRequirement(${index}, this.value)">
                <button type="button" class="btn-remove" onclick="removeRequirement(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
        
        // Mettre à jour le champ caché avec le JSON
        const requirementsInput = document.getElementById('requirementsInput');
        if (requirementsInput) {
            requirementsInput.value = JSON.stringify(currentRequirements);
        }
    }
}

// Mettre à jour une exigence
function updateRequirement(index, value) {
    if (value.length > 200) {
        alert('Une exigence ne peut pas dépasser 200 caractères');
        return;
    }
    currentRequirements[index] = value;
    document.getElementById('requirementsInput').value = JSON.stringify(currentRequirements);
}

// Échapper les caractères HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Voir les détails d'une offre
function viewOffer(id) {
    fetch(`CRUD.php?action=getOffer&id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'Erreur inconnue');
            }
            
            const offer = data.offer;
            let details = `DÉTAILS DE L'OFFRE\n\n`;
            details += `Titre: ${offer.titre || 'Non spécifié'}\n`;
            details += `Société: ${offer.nomSociete || 'Non spécifié'}\n`;
            details += `Localisation: ${offer.localisation || 'Non spécifié'}\n`;
            details += `Salaire: ${offer.salaireMin || 0}€ - ${offer.salaireMax || 0}€\n`;
            details += `Type de contrat: ${offer.typeContrat || 'Non spécifié'}\n`;
            details += `Expérience requise: ${offer.experienceRequise || 'Non spécifié'}\n`;
            details += `Nombre de places: ${offer.nbPlace || 1}\n`;
            details += `Statut: ${offer.statut || 'active'}\n`;
            details += `Date limite: ${offer.dateLimite ? new Date(offer.dateLimite).toLocaleDateString('fr-FR') : 'Non spécifiée'}\n`;
            details += `Date création: ${offer.dateCreation ? new Date(offer.dateCreation).toLocaleDateString('fr-FR') : 'Non spécifiée'}\n\n`;
            
            details += `Description:\n${offer.description || 'Aucune description'}\n\n`;
            
            // Compétences
            if (offer.competences) {
                details += `Compétences requises:\n`;
                try {
                    const skills = Array.isArray(offer.competences) ? offer.competences : JSON.parse(offer.competences);
                    skills.forEach(skill => {
                        details += `• ${skill}\n`;
                    });
                } catch (e) {
                    details += `${offer.competences}\n`;
                }
                details += `\n`;
            }
            
            // Exigences
            if (offer.requirements) {
                details += `Exigences:\n`;
                try {
                    const requirements = Array.isArray(offer.requirements) ? offer.requirements : JSON.parse(offer.requirements);
                    requirements.forEach(req => {
                        details += `• ${req}\n`;
                    });
                } catch (e) {
                    details += `${offer.requirements}\n`;
                }
            }
            
            alert(details);
        })
        .catch(error => {
            console.error('Erreur complète:', error);
            showNotification('Erreur lors du chargement de l\'offre', 'error');
        });
}

// Éditer une offre
function editOffer(id) {
    fetch(`CRUD.php?action=getOffer&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showNotification('Erreur: ' + data.message, 'error');
                return;
            }
            
            const offer = data.offer;
            document.getElementById('modalTitle').textContent = 'Modifier l\'Offre';
            document.getElementById('formAction').value = 'update';
            document.getElementById('offerId').value = offer.id;
            document.getElementById('titre').value = offer.titre || '';
            document.getElementById('nomSociete').value = offer.nomSociete || '';
            document.getElementById('localisation').value = offer.localisation || '';
            document.getElementById('salaireMin').value = offer.salaireMin || '';
            document.getElementById('salaireMax').value = offer.salaireMax || '';
            document.getElementById('description').value = offer.description || '';
            document.getElementById('typeContrat').value = offer.typeContrat || 'CDI';
            document.getElementById('experienceRequise').value = offer.experienceRequise || 'Junior';
            document.getElementById('nbPlace').value = offer.nbPlace || 1;
            document.getElementById('dateLimite').value = offer.dateLimite || '';
            document.getElementById('statut').value = offer.statut || 'active';
            
            // Charger les compétences
            currentSkills = [];
            if (offer.competences) {
                try {
                    currentSkills = Array.isArray(offer.competences) 
                        ? offer.competences 
                        : JSON.parse(offer.competences);
                } catch (e) {
                    currentSkills = [];
                }
            }
            
            // Charger les requirements
            currentRequirements = [];
            if (offer.requirements) {
                try {
                    currentRequirements = Array.isArray(offer.requirements) 
                        ? offer.requirements 
                        : JSON.parse(offer.requirements);
                } catch (e) {
                    currentRequirements = [];
                }
            }
            
            updateSkillsDisplay();
            updateRequirementsDisplay();
            
            document.getElementById('modalOffer').classList.add('active');
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors du chargement de l\'offre', 'error');
        });
}

// Supprimer une offre
function deleteOffer(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette offre ?\n\nCette action est irréversible.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Archiver une offre
function archiveOffer(id) {
    if (confirm('Êtes-vous sûr de vouloir archiver cette offre ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="archive">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Afficher une notification
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    if (!notification) return;
    
    const icon = notification.querySelector('i');
    const span = notification.querySelector('span');
    
    span.textContent = message;
    
    if (type === 'error') {
        icon.className = 'fas fa-exclamation-circle';
        notification.style.background = 'linear-gradient(90deg, #ff6b9d, #ff4757)';
    } else {
        icon.className = 'fas fa-check-circle';
        notification.style.background = 'linear-gradient(90deg, var(--accent-orange), var(--accent-green))';
    }
    
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

// Recherche en temps réel
document.getElementById('searchInput')?.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    const offers = document.querySelectorAll('.offer-item');
    
    offers.forEach(offer => {
        const text = offer.textContent.toLowerCase();
        offer.style.display = text.includes(searchTerm) ? 'flex' : 'none';
    });
});

// Valider le formulaire avant soumission
function validateForm() {
    // Vérifier le titre
    const titre = document.getElementById('titre')?.value.trim();
    if (!titre) {
        alert('Le titre du poste est obligatoire');
        return false;
    }
    if (titre.length > 200) {
        alert('Le titre ne peut pas dépasser 200 caractères');
        return false;
    }
    
    // Vérifier la société
    const societe = document.getElementById('nomSociete')?.value.trim();
    if (!societe) {
        alert('Le nom de la société est obligatoire');
        return false;
    }
    
    // Vérifier la localisation
    const localisation = document.getElementById('localisation')?.value.trim();
    if (!localisation) {
        alert('La localisation est obligatoire');
        return false;
    }
    
    // Vérifier le salaire
    const salaireMin = parseInt(document.getElementById('salaireMin')?.value) || 0;
    const salaireMax = parseInt(document.getElementById('salaireMax')?.value) || 0;
    
    if (salaireMin < 0) {
        alert('Le salaire minimum ne peut pas être négatif');
        return false;
    }
    if (salaireMax < salaireMin) {
        alert('Le salaire maximum doit être supérieur au salaire minimum');
        return false;
    }
    if (salaireMax > 1000000) {
        alert('Le salaire maximum est trop élevé');
        return false;
    }
    
    // Vérifier le nombre de places
    const nbPlace = parseInt(document.getElementById('nbPlace')?.value) || 0;
    if (nbPlace < 1 || nbPlace > 100) {
        alert('Le nombre de places doit être entre 1 et 100');
        return false;
    }
    
    // Vérifier la description
    const description = document.getElementById('description')?.value.trim();
    if (!description) {
        alert('La description est obligatoire');
        return false;
    }
    
    // Vérifier les compétences
    if (currentSkills.length === 0) {
        alert('Veuillez ajouter au moins une compétence');
        return false;
    }
    if (currentSkills.length > 20) {
        alert('Maximum 20 compétences autorisées');
        return false;
    }
    
    // Vérifier les requirements
    if (currentRequirements.length === 0) {
        alert('Veuillez ajouter au moins une exigence');
        return false;
    }
    
    // Vérifier que tous les requirements sont remplis
    const emptyRequirements = currentRequirements.filter(req => req.trim() === '');
    if (emptyRequirements.length > 0) {
        alert('Veuillez remplir toutes les exigences ou les supprimer');
        return false;
    }
    
    // Vérifier la longueur des requirements
    for (const req of currentRequirements) {
        if (req.length > 200) {
            alert('Une exigence ne peut pas dépasser 200 caractères');
            return false;
        }
    }
    
    // Mettre à jour les champs cachés
    document.getElementById('competencesInput').value = JSON.stringify(currentSkills);
    document.getElementById('requirementsInput').value = JSON.stringify(currentRequirements);
    
    return true;
}

// Permettre d'ajouter une compétence avec Enter
document.getElementById('skillInput')?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        addSkill();
    }
});

// Log pour debug
console.log('CRUD.js (Back Office) chargé avec succès');