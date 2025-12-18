/**
 * JavaScript pour Front-Office
 * Gestion des interactions utilisateur
 */

// Attendre le chargement complet du DOM
// FONCTION POUR LES DIALOGUES DE CONFIRMATION PERSONNALISÉS
function showConfirmDialog(message) {
    return new Promise((resolve) => {
        // Créer l'overlay
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        // Créer la boîte de dialogue
        const dialog = document.createElement('div');
        dialog.style.cssText = `
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 400px;
            width: 90%;
            text-align: center;
        `;
        
        dialog.innerHTML = `
            <div style="font-size: 60px; margin-bottom: 20px;">⚠️</div>
            <h3 style="margin-bottom: 15px; color: #1f2937;">Confirmation</h3>
            <p style="margin-bottom: 25px; color: #6b7280; line-height: 1.5;">${message}</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button id="confirmCancel" style="
                    padding: 12px 30px;
                    background: #e5e7eb;
                    color: #374151;
                    border: none;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: all 0.3s ease;
                ">Annuler</button>
                <button id="confirmOk" style="
                    padding: 12px 30px;
                    background: #ef4444;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-weight: bold;
                    cursor: pointer;
                    transition: all 0.3s ease;
                ">Supprimer</button>
            </div>
        `;
        
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);
        
        // Gérer les clics
        document.getElementById('confirmOk').onclick = () => {
            document.body.removeChild(overlay);
            resolve(true);
        };
        
        document.getElementById('confirmCancel').onclick = () => {
            document.body.removeChild(overlay);
            resolve(false);
        };
        
        // Fermer en cliquant sur l'overlay
        overlay.onclick = (e) => {
            if(e.target === overlay) {
                document.body.removeChild(overlay);
                resolve(false);
            }
        };
    });
}

// UTILISATION :
showConfirmDialog('Êtes-vous sûr de vouloir supprimer cet élément?').then((confirmed) => {
    if(confirmed) {
        // Action de suppression
    }
});
document.addEventListener('DOMContentLoaded', function() {
    
    // ===========================================
    // Smooth Scroll pour les liens d'ancrage
    // ===========================================
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]');
    smoothScrollLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if(href !== '#' && href !== '#0') {
                e.preventDefault();
                const target = document.querySelector(href);
                if(target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // ===========================================
    // Animation des cartes de formation au scroll
    // ===========================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if(entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                
                setTimeout(function() {
                    entry.target.style.transition = 'all 0.6s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const formationCards = document.querySelectorAll('.formation-card');
    formationCards.forEach(function(card) {
        observer.observe(card);
    });
    
    // ===========================================
    // Validation du formulaire d'inscription
    // ===========================================
    const inscriptionForm = document.getElementById('inscriptionForm');
    if(inscriptionForm) {
        
        // Validation en temps réel
        const nomInput = document.getElementById('nom');
        const emailInput = document.getElementById('email');
        const telInput = document.getElementById('tel');
        
        // Validation du nom
        if(nomInput) {
            nomInput.addEventListener('blur', function() {
                if(this.value.trim().length < 3) {
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.borderColor = '#10b981';
                }
            });
        }
        
        // Validation de l'email
        if(emailInput) {
            emailInput.addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if(!emailRegex.test(this.value)) {
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.borderColor = '#10b981';
                }
            });
        }
        
        // Validation du téléphone
        if(telInput) {
            telInput.addEventListener('input', function() {
                // Autoriser seulement les chiffres et certains caractères
                this.value = this.value.replace(/[^\d\s\-\+\(\)]/g, '');
            });
            
            telInput.addEventListener('blur', function() {
                const telDigits = this.value.replace(/[^\d]/g, '');
                if(telDigits.length < 8) {
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.borderColor = '#10b981';
                }
            });
        }
        
        // Validation à la soumission
        inscriptionForm.addEventListener('submit', function(e) {
            const errors = [];
            
            // Vérifier le nom
            if(nomInput && nomInput.value.trim().length < 3) {
                errors.push('Le nom doit contenir au moins 3 caractères');
                nomInput.style.borderColor = '#ef4444';
            }
            
            // Vérifier l'email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(emailInput && !emailRegex.test(emailInput.value)) {
                errors.push('Veuillez entrer une adresse email valide');
                emailInput.style.borderColor = '#ef4444';
            }
            
            // Vérifier le téléphone
            const telDigits = telInput.value.replace(/[^\d]/g, '');
            if(telInput && telDigits.length < 8) {
                errors.push('Le numéro de téléphone doit contenir au moins 8 chiffres');
                telInput.style.borderColor = '#ef4444';
            }
            
            // Vérifier la formation
            const formationSelect = document.getElementById('formation_id');
            if(formationSelect && !formationSelect.value) {
                errors.push('Veuillez sélectionner une formation');
                formationSelect.style.borderColor = '#ef4444';
            }
            
            // Si des erreurs existent, empêcher la soumission
            if(errors.length > 0) {
                e.preventDefault();
                showToast('error', 'Erreurs de validation', errors.join('<br>'), 8000);
                return false;
            }
            
            return true;
        });
    }
    
    // ===========================================
    // Afficher le message de succès avec animation
    // ===========================================
    const alertSuccess = document.querySelector('.alert-success');
    if(alertSuccess) {
        alertSuccess.style.opacity = '0';
        alertSuccess.style.transform = 'scale(0.9)';
        
        setTimeout(function() {
            alertSuccess.style.transition = 'all 0.5s ease';
            alertSuccess.style.opacity = '1';
            alertSuccess.style.transform = 'scale(1)';
        }, 100);
    }
    
    // ===========================================
    // Compteur de caractères pour textarea
    // ===========================================
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(function(textarea) {
        const maxLength = textarea.getAttribute('maxlength');
        if(maxLength) {
            const counter = document.createElement('div');
            counter.style.textAlign = 'right';
            counter.style.fontSize = '12px';
            counter.style.color = '#6b7280';
            counter.style.marginTop = '5px';
            
            function updateCounter() {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = remaining + ' caractères restants';
                if(remaining < 50) {
                    counter.style.color = '#ef4444';
                } else {
                    counter.style.color = '#6b7280';
                }
            }
            
            textarea.addEventListener('input', updateCounter);
            textarea.parentNode.appendChild(counter);
            updateCounter();
        }
    });
    
    // ===========================================
    // Confirmation avant de quitter la page avec formulaire non sauvegardé
    // ===========================================
    let formChanged = false;
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(function(input) {
            input.addEventListener('change', function() {
                formChanged = true;
            });
        });
        
        form.addEventListener('submit', function() {
            formChanged = false;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if(formChanged) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    });
    
    // ===========================================
    // Animation du nombre de places restantes
    // ===========================================
    const placesElements = document.querySelectorAll('.status-disponible, .status-complet');
    placesElements.forEach(function(element) {
        if(element.classList.contains('status-disponible')) {
            element.style.animation = 'pulse 2s infinite';
        }
    });
    
    // ===========================================
    // Message de confirmation pour les liens externes
    // ===========================================
    const externalLinks = document.querySelectorAll('a[href^="http"]');
    externalLinks.forEach(function(link) {
        if(!link.href.includes(window.location.hostname)) {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
        }
    });
    
    console.log('Front-Office JavaScript initialisé avec succès');
});

// ===========================================
// Fonction utilitaire pour formater le numéro de téléphone
// ===========================================
function formatPhoneNumber(input) {
    const cleaned = input.replace(/\D/g, '');
    const match = cleaned.match(/^(\d{2})(\d{3})(\d{3})$/);
    if(match) {
        return match[1] + ' ' + match[2] + ' ' + match[3];
    }
    return input;
}