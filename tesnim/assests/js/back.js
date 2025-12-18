// JavaScript Back-Office - Gestion des modals et interactions

// ===== MODAL AJOUT =====
function openAddModal() {
    document.getElementById('addModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAddModal() {
    document.getElementById('addModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// ===== MODAL MODIFICATION =====
function openEditModal(entretien) {
    document.getElementById('edit_id').value = entretien.id;
    document.getElementById('edit_type').value = entretien.type;
    document.getElementById('edit_date').value = entretien.date;
    document.getElementById('edit_heure').value = entretien.heure;
    document.getElementById('edit_places').value = entretien.places;
    document.getElementById('edit_places_prises').value = entretien.places_prises;
    
    document.getElementById('editModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Fonction de validation pour l'ajout
function validateAddForm() {
    const type = document.querySelector('#addModal select[name="type"]').value;
    const date = document.querySelector('#addModal input[name="date"]').value;
    const heure = document.querySelector('#addModal input[name="heure"]').value;
    const places = document.querySelector('#addModal input[name="places"]').value;
    
    const errors = [];
    
    // Validation du type
    if (!type || !['Technique', 'RH', 'Mixte'].includes(type)) {
        errors.push('Veuillez sélectionner un type d\'entretien valide');
    }
    
    // Validation de la date (ne peut pas être dans le passé)
    if (!date) {
        errors.push('La date est obligatoire');
    } else {
        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            errors.push('La date ne peut pas être dans le passé');
        }
    }
    
    // Validation de l'heure
    if (!heure || !/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(heure)) {
        errors.push('L\'heure est invalide');
    }
    
    // Validation du nombre de places
    if (!places || places < 1) {
        errors.push('Le nombre de places doit être supérieur à 0');
    }
    
    return errors;
}

// Fonction de validation pour la modification
function validateEditForm() {
    const places = parseInt(document.getElementById('edit_places').value);
    const placesPrises = parseInt(document.getElementById('edit_places_prises').value);
    
    const errors = [];
    
    // Validation du nombre de places
    if (!places || places < 1) {
        errors.push('Le nombre de places doit être supérieur à 0');
    } else if (places < placesPrises) {
        errors.push('Le nombre de places ne peut pas être inférieur au nombre de places déjà réservées (' + placesPrises + ')');
    }
    
    return errors;
}

// ===== FERMETURE EN CLIQUANT À L'EXTÉRIEUR =====
document.addEventListener('DOMContentLoaded', function() {
    // Modal ajout
    const addModal = document.getElementById('addModal');
    if (addModal) {
        addModal.addEventListener('click', function(e) {
            if (e.target === addModal) {
                closeAddModal();
            }
        });
    }
    
    // Modal modification
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) {
                closeEditModal();
            }
        });
    }
    
    // Validation du formulaire d'ajout
    const addForm = document.getElementById('addForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Valider les données
            const errors = validateAddForm();
            
            if (errors.length > 0) {
                // Afficher les erreurs
                alert(errors.join('\n'));
                return false;
            }
            
            // Si validation réussie, soumettre le formulaire
            addForm.submit();
        });
    }
    
    // Validation du formulaire de modification
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Valider les données
            const errors = validateEditForm();
            
            if (errors.length > 0) {
                // Afficher les erreurs
                alert(errors.join('\n'));
                return false;
            }
            
            // Si validation réussie, soumettre le formulaire
            editForm.submit();
        });
    }
    
    // Animation des cartes de stats
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Masquer automatiquement les alertes après 5 secondes
    const alerts = document.querySelectorAll('.card[style*="background"]');
    alerts.forEach(alert => {
        if (alert.textContent.includes('✓') || alert.textContent.includes('✗')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        }
    });
});

// ===== CONFIRMATION DE SUPPRESSION =====
function confirmDelete(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément ?');
}

// ===== RECHERCHE ET FILTRAGE (Optionnel) =====
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = this.value.toUpperCase();
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell) {
                    const textValue = cell.textContent || cell.innerText;
                    if (textValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    });
}