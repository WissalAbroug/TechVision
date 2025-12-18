/**
 * JavaScript pour Back-Office (Administration)
 * Gestion des interactions administrateur
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
    // Toggle Menu Sidebar (Mobile)
    // ===========================================
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar-nav-wrapper');
    const overlay = document.querySelector('.overlay');
    const mainWrapper = document.querySelector('.main-wrapper');
    
    if(menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            const isClosed = sidebar.style.left === '-260px' || !sidebar.style.left;
            
            if(isClosed) {
                sidebar.style.left = '0';
                overlay.style.display = 'block';
                setTimeout(function() {
                    overlay.style.opacity = '1';
                }, 10);
            } else {
                sidebar.style.left = '-260px';
                overlay.style.opacity = '0';
                setTimeout(function() {
                    overlay.style.display = 'none';
                }, 300);
            }
        });
        
        // Fermer le menu en cliquant sur l'overlay
        if(overlay) {
            overlay.addEventListener('click', function() {
                sidebar.style.left = '-260px';
                this.style.opacity = '0';
                setTimeout(function() {
                    overlay.style.display = 'none';
                }, 300);
            });
        }
    }
    
    // ===========================================
    // Confirmation de suppression
    // ===========================================
    const deleteLinks = document.querySelectorAll('.btn-delete, a[href*="delete"]');
    deleteLinks.forEach(function(link) {
        if(!link.hasAttribute('onclick')) {
            link.addEventListener('click', function(e) {
                if(!showConfirmDialog('Êtes-vous sûr de vouloir supprimer cet élément?')) {
    e.preventDefault();
    return false;
}
            });
        }
    });
    
    // ===========================================
    // Validation des formulaires
    // ===========================================
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let hasError = false;
            
            requiredFields.forEach(function(field) {
                if(!field.value.trim()) {
                    hasError = true;
                    field.style.borderColor = '#ef4444';
                    
                    // Créer un message d'erreur si il n'existe pas
                    let errorMsg = field.nextElementSibling;
                    if(!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = '#ef4444';
                        errorMsg.style.fontSize = '12px';
                        errorMsg.style.marginTop = '5px';
                        errorMsg.textContent = 'Ce champ est obligatoire';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.style.borderColor = '#e5e7eb';
                    const errorMsg = field.nextElementSibling;
                    if(errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            // Validation spécifique pour les champs numériques
            const numberFields = form.querySelectorAll('input[type="number"]');
            numberFields.forEach(function(field) {
                const value = parseFloat(field.value);
                const min = parseFloat(field.getAttribute('min'));
                const max = parseFloat(field.getAttribute('max'));
                
                if(field.value && (isNaN(value) || (min && value < min) || (max && value > max))) {
                    hasError = true;
                    field.style.borderColor = '#ef4444';
                    
                    let errorMsg = field.nextElementSibling;
                    if(!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = '#ef4444';
                        errorMsg.style.fontSize = '12px';
                        errorMsg.style.marginTop = '5px';
                        
                        if(min && value < min) {
                            errorMsg.textContent = 'La valeur doit être supérieure ou égale à ' + min;
                        } else if(max && value > max) {
                            errorMsg.textContent = 'La valeur doit être inférieure ou égale à ' + max;
                        } else {
                            errorMsg.textContent = 'Valeur numérique invalide';
                        }
                        
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                }
            });
            
            // Validation des dates
            const dateFields = form.querySelectorAll('input[type="date"]');
            dateFields.forEach(function(field) {
                if(field.value) {
                    const selectedDate = new Date(field.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    // Si c'est un champ de date de formation, vérifier qu'elle est future
                    if(field.name === 'date_formation' && selectedDate < today) {
                        hasError = true;
                        field.style.borderColor = '#ef4444';
                        
                        let errorMsg = field.nextElementSibling;
                        if(!errorMsg || !errorMsg.classList.contains('error-message')) {
                            errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            errorMsg.style.color = '#ef4444';
                            errorMsg.style.fontSize = '12px';
                            errorMsg.style.marginTop = '5px';
                            errorMsg.textContent = 'La date doit être future';
                            field.parentNode.insertBefore(errorMsg, field.nextSibling);
                        }
                    }
                }
            });
            
            if(hasError) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs dans le formulaire');
                return false;
            }
        });
        
        // Enlever le message d'erreur quand l'utilisateur commence à taper
        const allInputs = form.querySelectorAll('input, select, textarea');
        allInputs.forEach(function(input) {
            input.addEventListener('input', function() {
                this.style.borderColor = '#e5e7eb';
                const errorMsg = this.nextElementSibling;
                if(errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            });
        });
    });
    
    // ===========================================
    // Tri des colonnes du tableau
    // ===========================================
    const tableHeaders = document.querySelectorAll('.table thead th');
    tableHeaders.forEach(function(header, index) {
        // Ignorer la dernière colonne (Actions)
        if(header.textContent.trim() !== 'Actions' && index < tableHeaders.length - 1) {
            header.style.cursor = 'pointer';
            header.style.userSelect = 'none';
            
            header.addEventListener('click', function() {
                const table = this.closest('table');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                const isAscending = this.classList.contains('sort-asc');
                
                // Retirer toutes les classes de tri
                tableHeaders.forEach(function(th) {
                    th.classList.remove('sort-asc', 'sort-desc');
                });
                
                // Ajouter la classe appropriée
                if(isAscending) {
                    this.classList.add('sort-desc');
                } else {
                    this.classList.add('sort-asc');
                }
                
                // Trier les lignes
                rows.sort(function(a, b) {
                    const aValue = a.cells[index].textContent.trim();
                    const bValue = b.cells[index].textContent.trim();
                    
                    // Essayer de convertir en nombre
                    const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
                    const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
                    
                    if(!isNaN(aNum) && !isNaN(bNum)) {
                        return isAscending ? bNum - aNum : aNum - bNum;
                    } else {
                        return isAscending ? 
                            bValue.localeCompare(aValue) : 
                            aValue.localeCompare(bValue);
                    }
                });
                
                // Réinsérer les lignes triées
                rows.forEach(function(row) {
                    tbody.appendChild(row);
                });
            });
        }
    });
    
    // ===========================================
    // Animation des stats au chargement
    // ===========================================
    const statNumbers = document.querySelectorAll('.stat-content h3');
    statNumbers.forEach(function(stat) {
        const finalValue = parseInt(stat.textContent);
        if(!isNaN(finalValue)) {
            let currentValue = 0;
            const increment = Math.ceil(finalValue / 30);
            
            stat.textContent = '0';
            
            const timer = setInterval(function() {
                currentValue += increment;
                if(currentValue >= finalValue) {
                    stat.textContent = finalValue;
                    clearInterval(timer);
                } else {
                    stat.textContent = currentValue;
                }
            }, 30);
        }
    });
    
    // ===========================================
    // Recherche en temps réel (optionnel)
    // ===========================================
    const searchInput = document.querySelector('.search-input');
    if(searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchValue = this.value.toLowerCase();
            
            searchTimeout = setTimeout(function() {
                const tableRows = document.querySelectorAll('.table tbody tr');
                tableRows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    if(text.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }, 300);
        });
    }
    
    // ===========================================
    // Highlight des nouvelles entrées
    // ===========================================
    const tableRows = document.querySelectorAll('.table tbody tr');
    tableRows.forEach(function(row, index) {
        if(index < 3) {
            row.style.backgroundColor = '#fef3c7';
            setTimeout(function() {
                row.style.transition = 'background-color 2s ease';
                row.style.backgroundColor = '';
            }, 2000);
        }
    });
    
    // ===========================================
    // Auto-resize pour les textarea
    // ===========================================
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(function(textarea) {
        textarea.style.overflow = 'hidden';
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
    
    // ===========================================
    // Message de succès temporaire
    // ===========================================
    const successMessages = document.querySelectorAll('.alert-success');
    successMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    });
    
    console.log('Back-Office JavaScript initialisé avec succès');
});

// ===========================================
// Fonction utilitaire pour formater les prix
// ===========================================
function formatPrice(price) {
    return parseFloat(price).toFixed(2) + ' TND';
}

// ===========================================
// Fonction utilitaire pour formater les dates
// ===========================================
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return day + '/' + month + '/' + year;
}