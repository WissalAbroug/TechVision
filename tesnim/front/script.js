/* ============================================
   VALIDATION DE FORMULAIRE CÔTÉ CLIENT
   JavaScript vanilla - Pas de dépendances
   ============================================ */

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    
    // Récupération des éléments du formulaire
    const form = document.getElementById('contactForm');
    const nomInput = document.getElementById('nom');
    const emailInput = document.getElementById('email');
    const telInput = document.getElementById('tel');
    const submitBtn = form.querySelector('.btn-submit');
    const successMessage = document.getElementById('successMessage');

    // ============================================
    // RÈGLES DE VALIDATION
    // ============================================

    const validationRules = {
        nom: {
            required: true,
            minLength: 2,
            maxLength: 100,
            pattern: /^[a-zA-ZÀ-ÿ\s'-]+$/,
            messages: {
                required: 'Le nom est obligatoire',
                minLength: 'Le nom doit contenir au moins 2 caractères',
                maxLength: 'Le nom ne peut pas dépasser 100 caractères',
                pattern: 'Le nom contient des caractères non valides'
            }
        },
        email: {
            required: true,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            messages: {
                required: 'L\'email est obligatoire',
                pattern: 'Veuillez entrer une adresse email valide'
            }
        },
        tel: {
            required: true,
            pattern: /^[0-9]{8,15}$/,
            messages: {
                required: 'Le numéro de téléphone est obligatoire',
                pattern: 'Le téléphone doit contenir entre 8 et 15 chiffres uniquement'
            }
        }
    };

    // ============================================
    // FONCTION DE VALIDATION D'UN CHAMP
    // ============================================

    function validateField(input) {
        const fieldName = input.name;
        const value = input.value.trim();
        const rules = validationRules[fieldName];
        const errorElement = document.getElementById(`${fieldName}-error`);
        
        // Réinitialiser l'état
        input.classList.remove('invalid', 'valid');
        errorElement.textContent = '';
        
        // Vérification: champ requis
        if (rules.required && !value) {
            showError(input, errorElement, rules.messages.required);
            return false;
        }
        
        // Vérification: longueur minimale
        if (rules.minLength && value.length < rules.minLength) {
            showError(input, errorElement, rules.messages.minLength);
            return false;
        }
        
        // Vérification: longueur maximale
        if (rules.maxLength && value.length > rules.maxLength) {
            showError(input, errorElement, rules.messages.maxLength);
            return false;
        }
        
        // Vérification: pattern (regex)
        if (rules.pattern && !rules.pattern.test(value)) {
            showError(input, errorElement, rules.messages.pattern);
            return false;
        }
        
        // Le champ est valide
        input.classList.add('valid');
        input.setAttribute('aria-invalid', 'false');
        return true;
    }

    // ============================================
    // AFFICHER UNE ERREUR
    // ============================================

    function showError(input, errorElement, message) {
        input.classList.add('invalid');
        input.setAttribute('aria-invalid', 'true');
        errorElement.textContent = message;
    }

    // ============================================
    // VALIDATION EN TEMPS RÉEL (blur et input)
    // ============================================

    // Validation au blur (perte de focus)
    [nomInput, emailInput, telInput].forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });

    // Validation en temps réel pendant la saisie (après le premier blur)
    [nomInput, emailInput, telInput].forEach(input => {
        let hasBlurred = false;
        
        input.addEventListener('blur', function() {
            hasBlurred = true;
        });
        
        input.addEventListener('input', function() {
            if (hasBlurred) {
                validateField(this);
            }
        });
    });

    // ============================================
    // SOUMISSION DU FORMULAIRE
    // ============================================

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Empêcher la soumission par défaut
        
        // Masquer le message de succès précédent
        successMessage.classList.remove('show');
        
        // Valider tous les champs
        const isNomValid = validateField(nomInput);
        const isEmailValid = validateField(emailInput);
        const isTelValid = validateField(telInput);
        
        // Vérifier si tous les champs sont valides
        const isFormValid = isNomValid && isEmailValid && isTelValid;
        
        if (isFormValid) {
            // Tous les champs sont valides - Simuler l'envoi
            submitForm();
        } else {
            // Des erreurs existent - Déplacer le focus sur le premier champ invalide
            const firstInvalidField = form.querySelector('.invalid');
            if (firstInvalidField) {
                firstInvalidField.focus();
                // Scroll vers le champ pour la visibilité
                firstInvalidField.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
        }
    });

    // ============================================
    // SIMULER L'ENVOI DU FORMULAIRE
    // (En production, les données seront envoyées à PHP)
    // ============================================

    function submitForm() {
        // Désactiver le bouton pendant le "chargement"
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        
        // Récupérer les données du formulaire
        const formData = {
            nom: nomInput.value.trim(),
            email: emailInput.value.trim(),
            tel: telInput.value.trim()
        };
        
        // Simuler un délai réseau (1 seconde)
        setTimeout(() => {
            // Log des données dans la console
            console.log('=== DONNÉES DU FORMULAIRE ===');
            console.log('Nom:', formData.nom);
            console.log('Email:', formData.email);
            console.log('Téléphone:', formData.tel);
            console.log('============================');
            
            // Afficher le message de succès
            successMessage.classList.add('show');
            
            // Réinitialiser le formulaire
            form.reset();
            
            // Enlever les classes de validation
            [nomInput, emailInput, telInput].forEach(input => {
                input.classList.remove('valid', 'invalid');
            });
            
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            
            // Masquer le message après 5 secondes
            setTimeout(() => {
                successMessage.classList.remove('show');
            }, 5000);
            
            /* ============================================
               CONNEXION PHP - À DÉCOMMENTER PLUS TARD
               ============================================
               
               En production, remplacez le code ci-dessus par:
               
               1. Décommentez l'attribut action="traitement.php" dans index.html
               
               2. Supprimez e.preventDefault() pour permettre la soumission
               
               3. OU utilisez fetch() pour envoyer en AJAX:
               
               fetch('traitement.php', {
                   method: 'POST',
                   headers: {
                       'Content-Type': 'application/x-www-form-urlencoded',
                   },
                   body: new URLSearchParams(formData)
               })
               .then(response => response.json())
               .then(data => {
                   console.log('Réponse du serveur:', data);
                   successMessage.classList.add('show');
                   form.reset();
               })
               .catch(error => {
                   console.error('Erreur:', error);
                   alert('Une erreur est survenue lors de l\'envoi');
               })
               .finally(() => {
                   submitBtn.disabled = false;
                   submitBtn.classList.remove('loading');
               });
               
               ============================================ */
            
        }, 1000);
    }

    // ============================================
    // NETTOYAGE DES ESPACES AU BLUR
    // ============================================

    [nomInput, emailInput, telInput].forEach(input => {
        input.addEventListener('blur', function() {
            this.value = this.value.trim();
        });
    });

    // ============================================
    // EMPÊCHER LES CARACTÈRES NON-NUMÉRIQUES DANS TEL
    // ============================================

    telInput.addEventListener('keypress', function(e) {
        // Autoriser seulement les chiffres (0-9)
        const char = String.fromCharCode(e.which);
        if (!/[0-9]/.test(char)) {
            e.preventDefault();
        }
    });

    // Nettoyer les caractères non-numériques si copié-collé
    telInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // ============================================
    // ACCESSIBILITÉ: Annonce vocale pour les lecteurs d'écran
    // ============================================

    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('role', 'status');
        announcement.setAttribute('aria-live', 'polite');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    // Ajouter style pour lecteur d'écran uniquement
    const style = document.createElement('style');
    style.textContent = `
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    `;
    document.head.appendChild(style);

    console.log('✅ Script de validation chargé avec succès');
});