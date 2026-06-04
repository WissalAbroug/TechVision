// js/signup-script.js

/**
 * Toggle password visibility
 */
function setupPasswordToggle() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (!togglePassword || !passwordInput) return;
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        this.textContent = type === 'password' ? '👁️' : '🙈';
    });
}

/**
 * Email validation
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Show error message
 */
function showError(inputId, message) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const inputWrapper = input.parentElement;
    inputWrapper.classList.add('error');
    inputWrapper.classList.remove('success');
    
    // Remove existing error message
    const existingError = inputWrapper.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
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
    inputWrapper.classList.remove('error');
}

/**
 * Validate form before submission
 */
function setupFormValidation() {
    const signupForm = document.querySelector('form');
    if (!signupForm) return;
    
    signupForm.addEventListener('submit', function(e) {
        const fullnameInput = document.getElementById('fullname');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        
        if (!fullnameInput || !emailInput || !passwordInput) return;
        
        const fullname = fullnameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        
        let isValid = true;
        
        // Validate fullname
        if (!fullname || fullname.length < 3) {
            showError('fullname', 'Le nom doit contenir au moins 3 caractères');
            isValid = false;
        }
        
        // Validate email
        if (!email || !validateEmail(email)) {
            showError('email', 'Adresse email invalide');
            isValid = false;
        }
        
        // Validate password
        if (!password || password.length < 6) {
            showError('password', 'Le mot de passe doit contenir au moins 6 caractères');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('.btn-submit');
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Création en cours...';
            submitBtn.disabled = true;
        }
    });
}

/**
 * Real-time password validation
 */
function setupPasswordValidation() {
    const passwordInput = document.getElementById('password');
    if (!passwordInput) return;
    
    passwordInput.addEventListener('input', function() {
        const hintText = this.parentElement.parentElement.querySelector('.hint-text');
        if (!hintText) return;
        
        const length = this.value.length;
        
        if (length === 0) {
            hintText.textContent = 'Au moins 6 caractères';
            hintText.style.color = 'rgba(255, 255, 255, 0.6)';
        } else if (length < 6) {
            hintText.textContent = `⌛ ${6 - length} caractère(s) restant(s)`;
            hintText.style.color = '#ff6b9d';
        } else {
            hintText.textContent = '✅ Mot de passe valide';
            hintText.style.color = '#5cd8d8';
        }
    });
}

/**
 * Real-time email validation
 */
function setupEmailValidation() {
    const emailInput = document.getElementById('email');
    if (!emailInput) return;
    
    emailInput.addEventListener('blur', function() {
        const hintText = this.parentElement.parentElement.querySelector('.hint-text');
        if (!hintText) return;
        
        const email = this.value.trim();
        
        if (email && !validateEmail(email)) {
            hintText.textContent = '❌ Email invalide';
            hintText.style.color = '#ff6b9d';
            this.style.borderColor = '#ff6b9d';
        } else if (email) {
            hintText.textContent = '✅ Email valide';
            hintText.style.color = '#5cd8d8';
            this.style.borderColor = '#5cd8d8';
        } else {
            hintText.textContent = 'Nous ne partagerons jamais votre email';
            hintText.style.color = 'rgba(255, 255, 255, 0.6)';
            this.style.borderColor = 'rgba(255, 255, 255, 0.15)';
        }
    });
}

/**
 * Clear validation on input
 */
function setupInputClear() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.id) {
                clearError(this.id);
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
 * Animation de typing sur le placeholder
 */
function setupPlaceholderAnimation() {
    const fullnameInput = document.getElementById('fullname');
    if (!fullnameInput) return;
    
    let placeholderIndex = 0;
    const placeholders = [
        'Votre nom complet',
        'Ex: Jean Dupont',
        'Ex: Marie Martin',
        'Votre nom complet'
    ];

    setInterval(() => {
        if (!fullnameInput.value && document.activeElement !== fullnameInput) {
            placeholderIndex = (placeholderIndex + 1) % placeholders.length;
            fullnameInput.placeholder = placeholders[placeholderIndex];
        }
    }, 3000);
}

/**
 * Handle Enter key navigation
 */
function setupEnterKey() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach((input, index) => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                
                // Move to next input or submit
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                } else {
                    const form = document.querySelector('form');
                    if (form) {
                        form.submit();
                    }
                }
            }
        });
    });
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
 * Password strength indicator
 */
function setupPasswordStrength() {
    const passwordInput = document.getElementById('password');
    if (!passwordInput) return;
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        // Calculate strength
        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z\d]/.test(password)) strength++;
        
        // Update hint based on strength
        const hintText = this.parentElement.parentElement.querySelector('.hint-text');
        if (!hintText) return;
        
        if (password.length === 0) {
            return;
        }
        
        const strengthLabels = [
            '❌ Très faible',
            '⚠️ Faible',
            '🟡 Moyen',
            '✅ Fort',
            '🔒 Très fort'
        ];
        
        const strengthColors = [
            '#ff6b6b',
            '#ff9d5c',
            '#ffd93d',
            '#5cd8d8',
            '#51cf66'
        ];
        
        if (password.length >= 6) {
            hintText.textContent = strengthLabels[strength - 1] || strengthLabels[0];
            hintText.style.color = strengthColors[strength - 1] || strengthColors[0];
        }
    });
}

/**
 * Initialize all functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de la page d\'inscription...');
    
    setupPasswordToggle();
    setupFormValidation();
    setupPasswordValidation();
    setupEmailValidation();
    setupInputClear();
    setupInputAnimations();
    setupPlaceholderAnimation();
    setupEnterKey();
    setupAlertAutoDismiss();
    setupPasswordStrength();
    
    // Auto-focus on first input
    const firstInput = document.getElementById('fullname');
    if (firstInput) {
        firstInput.focus();
    }
    
    console.log('✅ TalentMatch - Page d\'inscription chargée avec succès!');
});

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
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
`;
document.head.appendChild(style);