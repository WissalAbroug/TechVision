// JavaScript Front-Office - Gestion du modal et validation

// Ouvrir le modal de réservation
function openModal(entretienId, type, date, heure) {
    document.getElementById('entretienId').value = entretienId;
    document.getElementById('modalType').value = type;
    document.getElementById('modalDateTime').value = date + ' à ' + heure;
    document.getElementById('reservationModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Fermer le modal
function closeModal() {
    document.getElementById('reservationModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('reservationForm').reset();
    document.getElementById('modalAlert').innerHTML = '';
}

// Fonction de validation
function validateReservationForm() {
    const nom = document.getElementById('nom').value.trim();
    const tel = document.getElementById('tel').value.trim();
    const email = document.getElementById('email').value.trim();
    
    const errors = [];
    
    // Validation du nom (minimum 3 caractères)
    if (nom.length < 3) {
        errors.push('Le nom doit contenir au moins 3 caractères');
    }
    
    // Validation du téléphone (8 chiffres minimum)
    if (!/^[0-9]{8,}$/.test(tel)) {
        errors.push('Le numéro de téléphone doit contenir au moins 8 chiffres');
    }
    
    // Validation de l'email
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.push('Veuillez entrer une adresse email valide');
    }
    
    return errors;
}

// Afficher les erreurs
function showErrors(errors) {
    const alertDiv = document.getElementById('modalAlert');
    
    if (errors.length > 0) {
        alertDiv.innerHTML = '<div class="alert alert-error">' + 
            '<strong>Erreurs :</strong><ul>' + 
            errors.map(error => '<li>' + error + '</li>').join('') + 
            '</ul></div>';
        
        // Supprimer automatiquement après 10 secondes
        setTimeout(function() {
            if (alertDiv.firstChild) {
                alertDiv.firstChild.style.transition = 'opacity 0.5s ease';
                alertDiv.firstChild.style.opacity = '0';
                setTimeout(() => {
                    if (alertDiv.firstChild) {
                        alertDiv.firstChild.remove();
                    }
                }, 500);
            }
        }, 10000);
    } else {
        alertDiv.innerHTML = '';
    }
}

// Fermer le modal en cliquant en dehors
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('reservationModal');
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Validation du formulaire
    const form = document.getElementById('reservationForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Valider les données
        const errors = validateReservationForm();
        
        if (errors.length > 0) {
            // Afficher les erreurs
            showErrors(errors);
            return false;
        }
        
        // Si validation réussie, soumettre le formulaire
        form.submit();
    });
});

// Animation des cartes au scroll
window.addEventListener('load', function() {
    const cards = document.querySelectorAll('.entretien-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
    });
});