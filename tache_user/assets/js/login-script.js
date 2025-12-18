// js/login-script.js

/**
 * Toggle password visibility
 */
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (passwordInput && eyeIcon) {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            eyeIcon.textContent = '👁️';
        }
    }
}

/**
 * Email validation
 */
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Show error message
 */
function showError(inputId, message) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const inputWrapper = input.parentElement;
    inputWrapper.classList.add('error');
    
    // Remove existing error message
    const existingError = inputWrapper.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.cssText = 'color: #c00; font-size: 12px; margin-top: 5px;';
    inputWrapper.parentElement.appendChild(errorDiv);
}

/**
 * Clear error message
 */
function clearError(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const inputWrapper = input.parentElement;
    inputWrapper.classList.remove('error');
    
    const errorMessage = inputWrapper.parentElement.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

/**
 * Show success state
 */
function showSuccess(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const inputWrapper = input.parentElement;
    inputWrapper.classList.add('success');
}

/**
 * Input validation on blur
 */
function setupInputValidation() {
    const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            const inputId = this.id;
            const value = this.value.trim();
            
            clearError(inputId);
            
            if (value) {
                if (inputId === 'email' && !validateEmail(value)) {
                    showError(inputId, 'Adresse email invalide');
                } else if (inputId === 'password' && value.length < 6) {
                    showError(inputId, 'Le mot de passe doit contenir au moins 6 caractères');
                } else {
                    showSuccess(inputId);
                }
            }
        });
    });
}

/**
 * Clear error on input
 */
function setupInputClear() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            const inputWrapper = this.parentElement;
            inputWrapper.classList.remove('error');
            inputWrapper.classList.remove('success');
        });
    });
}

/**
 * Handle form submission - Client-side validation
 * CORRECTION: Ne pas empêcher la soumission si valide
 */
function setupFormValidation() {
    const loginForm = document.querySelector('.login-form');
    if (!loginForm) return;
    
    loginForm.addEventListener('submit', function(e) {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        
        if (!emailInput || !passwordInput) return;
        
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        
        let isValid = true;
        
        // Validate email
        if (!email || !validateEmail(email)) {
            showError('email', 'Adresse email invalide');
            isValid = false;
        }
        
        // Validate password (min 6 pour validation client, serveur vérifie 8)
        if (!password || password.length < 6) {
            showError('password', 'Le mot de passe doit contenir au moins 6 caractères');
            isValid = false;
        }
        
        // Si invalide, empêcher la soumission
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Si valide, montrer l'état de chargement mais laisser le formulaire se soumettre
        const submitBtn = this.querySelector('.btn-submit');
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Connexion en cours...';
            submitBtn.disabled = true;
        }
        
        // NE PAS EMPÊCHER LA SOUMISSION - Le formulaire continue normalement
        console.log('✅ Formulaire valide - Soumission au serveur...');
    });
}

/**
 * Social login handlers
 */
function setupSocialLogin() {
    const socialBtns = document.querySelectorAll('.btn-social');
    
    socialBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // Empêcher la soumission pour les boutons sociaux
            const provider = this.classList.contains('google') ? 'Google' : 'LinkedIn';
            
            // Add loading animation
            this.style.opacity = '0.7';
            this.style.pointerEvents = 'none';
            
            setTimeout(() => {
                alert(`Connexion avec ${provider} sera bientôt disponible!`);
                this.style.opacity = '1';
                this.style.pointerEvents = 'auto';
            }, 800);
        });
    });
}

/**
 * Forgot password handler
 */
function setupForgotPassword() {
    const forgotLink = document.querySelector('.forgot-link');
    if (!forgotLink) return;
    
    forgotLink.addEventListener('click', function(e) {
        // Ne pas empêcher la navigation - laisser le lien fonctionner
        // Juste valider l'email si on veut
        const emailInput = document.getElementById('email');
        if (emailInput && emailInput.value.trim()) {
            // Stocker l'email pour la page de récupération
            sessionStorage.setItem('resetEmail', emailInput.value.trim());
        }
    });
}

/**
 * Add ripple effect to button
 */
function setupRippleEffect() {
    const submitBtn = document.querySelector('.btn-submit');
    if (!submitBtn) return;
    
    submitBtn.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.style.position = 'absolute';
        ripple.style.borderRadius = '50%';
        ripple.style.background = 'rgba(255, 255, 255, 0.5)';
        ripple.style.transform = 'scale(0)';
        ripple.style.animation = 'ripple 0.6s ease-out';
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
}

/**
 * Handle Enter key
 * CORRECTION: Utiliser submit() au lieu de dispatchEvent
 */
function setupEnterKey() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const loginForm = document.querySelector('.login-form');
                
                if (this.id === 'email' && passwordInput) {
                    // Si on est sur l'email, passer au mot de passe
                    e.preventDefault();
                    passwordInput.focus();
                } else if (this.id === 'password' && loginForm) {
                    // Si on est sur le mot de passe, soumettre le formulaire
                    // Laisser l'événement Enter se propager naturellement
                    // Le formulaire sera soumis automatiquement
                }
            }
        });
    });
}

/**
 * Animate inputs on focus
 */
function setupInputAnimations() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            if (this.parentElement) {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            }
        });
        
        input.addEventListener('blur', function() {
            if (this.parentElement) {
                this.parentElement.style.transform = 'scale(1)';
            }
        });
    });
}

/**
 * Checkbox animation
 */
function setupCheckboxAnimation() {
    const checkboxWrapper = document.querySelector('.checkbox-wrapper');
    if (!checkboxWrapper) return;
    
    checkboxWrapper.addEventListener('click', function(e) {
        if (e.target.tagName !== 'INPUT') {
            const checkbox = this.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
            }
        }
    });
}

/**
 * Remember me functionality
 */
function setupRememberMe() {
    const emailInput = document.getElementById('email');
    const rememberCheckbox = document.getElementById('remember');
    
    if (!emailInput || !rememberCheckbox) return;
    
    // Check if email was remembered
    const rememberedEmail = localStorage.getItem('rememberedEmail');
    if (rememberedEmail) {
        emailInput.value = rememberedEmail;
        rememberCheckbox.checked = true;
        
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.focus();
        }
    } else {
        emailInput.focus();
    }
    
    // Save email when form is submitted
    const form = document.querySelector('.login-form');
    if (form) {
        form.addEventListener('submit', function() {
            if (rememberCheckbox.checked) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    }
}

/**
 * Auto-dismiss alerts
 */
function setupAlertAutoDismiss() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.style.cssText = 'position:absolute; top:10px; right:15px; background:none; border:none; font-size:24px; cursor:pointer; opacity:0.7; color:inherit;';
        closeBtn.onclick = () => {
            alert.style.animation = 'slideUp 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        };
        alert.style.position = 'relative';
        alert.appendChild(closeBtn);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    });
}

/**
 * Initialize all functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la page de connexion...');
    
    setupInputValidation();
    setupInputClear();
    setupFormValidation();
    setupSocialLogin();
    setupForgotPassword();
    setupRippleEffect();
    setupEnterKey();
    setupInputAnimations();
    setupCheckboxAnimation();
    setupRememberMe();
    setupAlertAutoDismiss();
    
    console.log('✅ Page de connexion TalentMatch chargée avec succès!');
});

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
    
    .input-wrapper.error input {
        border-color: #dc3545 !important;
    }
    
    .input-wrapper.success input {
        border-color: #28a745 !important;
    }
    
    .btn-submit.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }
`;
document.head.appendChild(style);