/**
 * TalentMatch - Application de consultation des données (Version MVC)
 * Compatible avec l'architecture MVC et l'API JSON
 */

// ============================================
// FONCTIONS MODALES
// ============================================

/**
 * Ouvre la modal d'édition et remplit les champs
 */
function openEditModal(record) {
    document.getElementById('edit_id').value = record.id;
    document.getElementById('edit_nom').value = record.nom || '';
    document.getElementById('edit_email').value = record.email || '';
    document.getElementById('edit_telephone').value = record.telephone || '';
    document.getElementById('edit_statut').value = record.statut || 'en attente';

    document.getElementById('editModal').classList.add('show');
}

/**
 * Ferme la modal d'édition
 */
function closeEditModal() {
    document.getElementById('editModal').classList.remove('show');
}

/**
 * Ouvre la modal de suppression
 */
function openDeleteModal(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;

    document.getElementById('deleteModal').classList.add('show');
}

/**
 * Ferme la modal de suppression
 */
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

// ============================================
// CONFIGURATION
// ============================================

const CONFIG = {
    API_ENDPOINT: 'index.php?action=api', // Endpoint API JSON
    SEARCH_DEBOUNCE: 300,
};

// ============================================
// ÉTAT GLOBAL
// ============================================

let allData = [];
let filteredData = [];

// ============================================
// ÉLÉMENTS DOM
// ============================================

const elements = {
    tableBody: document.getElementById('tableBody'),
    messageZone: document.getElementById('messageZone'),
    searchInput: document.getElementById('searchInput'),
    searchBtn: document.getElementById('searchBtn'),
    visibleCount: document.getElementById('visibleCount'),
};

// ============================================
// MODE DE FONCTIONNEMENT
// ============================================

// Détection : utiliser JavaScript seulement si souhaité
const USE_JS_MODE = false; // Mettre à true pour mode JavaScript dynamique

// ============================================
// FONCTIONS PRINCIPALES
// ============================================

/**
 * Charge les données depuis l'API (mode JavaScript)
 */
async function loadData() {
    if (!USE_JS_MODE) return;
    
    showLoader(true);
    clearMessage();

    try {
        const response = await fetch(CONFIG.API_ENDPOINT, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.error || 'Erreur inconnue');
        }

        allData = result.data;
        filteredData = result.data;

        showLoader(false);

        if (result.data.length === 0) {
            showEmpty();
        } else {
            renderTable(result.data);
            showSuccess(`${result.data.length} enregistrement(s) chargé(s)`);
        }
    } catch (error) {
        showLoader(false);
        showError(`Erreur de chargement: ${error.message}`);
        console.error('Erreur:', error);
    }
}

/**
 * Affiche les données dans la table
 */
function renderTable(data) {
    elements.tableBody.innerHTML = '';

    if (data.length === 0) {
        showEmpty();
        updateCount(0);
        return;
    }

    data.forEach((record) => {
        const row = createTableRow(record);
        elements.tableBody.appendChild(row);
    });

    updateCount(data.length);
}

/**
 * Crée une ligne de table
 */
function createTableRow(record) {
    const tr = document.createElement('tr');
    tr.className = 'table__row table__row--body';

    tr.innerHTML = `
        <td class="table__cell table__cell--body">${escapeHtml(record.id || '-')}</td>
        <td class="table__cell table__cell--body">${escapeHtml(record.nom || '-')}</td>
        <td class="table__cell table__cell--body">${escapeHtml(record.email || '-')}</td>
        <td class="table__cell table__cell--body">${escapeHtml(record.telephone || '-')}</td>
        <td class="table__cell table__cell--body">
            <span class="badge badge--${getBadgeClass(record.statut)}">
                ${escapeHtml(record.statut || 'inconnu')}
            </span>
        </td>
        <td class="table__cell table__cell--body">${formatDate(record.date_creation || record.date)}</td>
    `;

    return tr;
}

/**
 * Applique le filtre de recherche (mode JavaScript)
 */
function applyFilter(query) {
    if (!USE_JS_MODE) return;
    
    const searchTerm = query.toLowerCase().trim();

    if (searchTerm === '') {
        filteredData = allData;
    } else {
        filteredData = allData.filter((record) => {
            return (
                (record.nom && record.nom.toLowerCase().includes(searchTerm)) ||
                (record.email && record.email.toLowerCase().includes(searchTerm)) ||
                (record.telephone && record.telephone.toLowerCase().includes(searchTerm)) ||
                (record.statut && record.statut.toLowerCase().includes(searchTerm)) ||
                (record.id && record.id.toString().includes(searchTerm))
            );
        });
    }

    renderTable(filteredData);

    if (filteredData.length === 0 && allData.length > 0) {
        showMessage('Aucun résultat ne correspond à votre recherche', 'empty');
    }
}

// ============================================
// GESTION DES MESSAGES
// ============================================

function showLoader(show) {
    if (show) {
        elements.messageZone.innerHTML = `
            <div class="message message--loader">
                <span class="spinner" aria-label="Chargement en cours"></span>
                <span>Chargement des données...</span>
            </div>
        `;
    }
}

function showError(msg) {
    showMessage(msg, 'error');
}

function showSuccess(msg) {
    showMessage(msg, 'success');
    setTimeout(clearMessage, 3000);
}

function showEmpty() {
    showMessage('Aucun enregistrement trouvé dans la base de données', 'empty');
}

function showMessage(msg, type) {
    elements.messageZone.innerHTML = `
        <div class="message message--${type}">
            ${type === 'error' ? '⚠️' : type === 'success' ? '✓' : 'ℹ️'} ${escapeHtml(msg)}
        </div>
    `;
}

function clearMessage() {
    elements.messageZone.innerHTML = '';
}

// ============================================
// UTILITAIRES
// ============================================

function escapeHtml(str) {
    if (str == null) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function getBadgeClass(statut) {
    if (!statut) return 'inactive';
    const s = statut.toLowerCase();
    if (s === 'actif' || s === 'active') return 'active';
    if (s === 'en attente' || s === 'pending') return 'pending';
    return 'inactive';
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    try {
        const date = new Date(dateStr);
        if (isNaN(date)) return dateStr;
        return date.toLocaleDateString('fr-FR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
        });
    } catch (e) {
        return dateStr;
    }
}

function updateCount(count) {
    elements.visibleCount.textContent = count;
}

function debounce(func, delay) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

// ============================================
// ÉVÉNEMENTS
// ============================================

function initEvents() {
    // En mode JavaScript dynamique
    if (USE_JS_MODE) {
        // Empêcher la soumission normale du formulaire
        const form = document.querySelector('.data-zone__controls');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                applyFilter(elements.searchInput.value);
            });
        }

        // Recherche avec debounce
        const debouncedSearch = debounce((query) => {
            applyFilter(query);
        }, CONFIG.SEARCH_DEBOUNCE);

        elements.searchInput.addEventListener('input', (e) => {
            debouncedSearch(e.target.value);
        });
    }
    
    // Auto-effacement des messages de succès après 3 secondes
    const successMessages = document.querySelectorAll('.message--success');
    successMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.3s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 300);
        }, 3000);
    });
}

// ============================================
// INITIALISATION
// ============================================

function init() {
    console.log('TalentMatch MVC - Initialisation');
    console.log('Mode JavaScript dynamique:', USE_JS_MODE ? 'Activé' : 'Désactivé (PHP)');

    // Vérification des éléments DOM
    const missingElements = Object.entries(elements).filter(([key, el]) => !el);
    if (missingElements.length > 0) {
        console.warn('Éléments DOM manquants:', missingElements.map(([key]) => key));
    }

    initEvents();

    // Charger les données uniquement en mode JavaScript
    if (USE_JS_MODE) {
        loadData();
    }
}

// Démarrage
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}