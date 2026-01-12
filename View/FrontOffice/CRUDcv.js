// ============================================
// CORRECTION DES FONCTIONS DE SAUVEGARDE CV
// ============================================

var cvData = {
    step1: {},
    step2: {
        competences_tech: [],
        competences_soft: [],
        langues: []
    },
    step3: {
        experiences: []
    },
    step4: {
        formations: [],
        projets: [],
        certifications: []
    },
    step5: {}
};

var currentCVStep = 1;

// Fonction améliorée pour naviguer entre les étapes
function showCVStep(step) {
    // Sauvegarder les données de l'étape actuelle avant de changer
    if (step > currentCVStep) {
        if (!saveCurrentStepData(currentCVStep)) {
            showNotification('Veuillez compléter les champs obligatoires de cette étape', 'error');
            return false;
        }
    }
    
    // Cacher toutes les étapes
    var steps = document.querySelectorAll('.cv-step');
    for (var i = 0; i < steps.length; i++) {
        steps[i].style.display = 'none';
    }
    
    // Afficher l'étape demandée
    var stepElement = document.getElementById('cvStep' + step);
    if (stepElement) {
        stepElement.style.display = 'block';
    }
    
    // Mettre à jour la progression
    updateCVProgress(step);
    currentCVStep = step;
    
    // Restaurer les données si elles existent
    restoreStepData(step);
    
    return true;
}

// Fonction pour sauvegarder les données de l'étape actuelle
function saveCurrentStepData(step) {
    switch(step) {
        case 1:
            return validateAndSaveStep1();
        case 2:
            return validateAndSaveStep2();
        case 3:
            return validateAndSaveStep3();
        case 4:
            return validateAndSaveStep4();
        case 5:
            return validateAndSaveStep5();
    }
    return true;
}

// Validation et sauvegarde étape 1
function validateAndSaveStep1() {
    var form = document.getElementById('cvFormStep1');
    var inputs = form.querySelectorAll('input[required], select[required]');
    
    // Validation des champs obligatoires
    for (var i = 0; i < inputs.length; i++) {
        if (!inputs[i].value.trim()) {
            showNotification('Le champ "' + inputs[i].placeholder + '" est obligatoire', 'error');
            inputs[i].focus();
            return false;
        }
    }
    
    // Validation spécifique pour l'email
    var emailInput = form.querySelector('input[name="email"]');
    if (emailInput && emailInput.value) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            showNotification('Veuillez entrer une adresse email valide', 'error');
            emailInput.focus();
            return false;
        }
    }
    
    // Sauvegarde des données
    var formData = {};
    var inputsAll = form.querySelectorAll('input, select');
    for (var i = 0; i < inputsAll.length; i++) {
        var input = inputsAll[i];
        if (input.type !== 'file') {
            formData[input.name] = input.value;
        }
    }
    
    cvData.step1 = formData;
    localStorage.setItem('cvData_step1', JSON.stringify(formData));
    return true;
}

// Validation et sauvegarde étape 2
function validateAndSaveStep2() {
    var form = document.getElementById('cvFormStep2');
    
    // Validation des champs obligatoires
    var profil = form.querySelector('textarea[name="profil"]');
    var qualites = form.querySelector('input[name="qualites"]');
    
    if (!profil.value.trim()) {
        showNotification('Le profil professionnel est obligatoire', 'error');
        profil.focus();
        return false;
    }
    
    if (!qualites.value.trim()) {
        showNotification('Les qualités professionnelles sont obligatoires', 'error');
        qualites.focus();
        return false;
    }
    
    // Sauvegarde des données de formulaire
    var formData = {};
    var inputs = form.querySelectorAll('input, textarea, select');
    for (var i = 0; i < inputs.length; i++) {
        var input = inputs[i];
        formData[input.name] = input.value;
    }
    
    // Sauvegarde des compétences techniques
    var techSkills = [];
    var techTags = document.querySelectorAll('#skillsTechContainer .skill-tag span:first-child');
    for (var i = 0; i < techTags.length; i++) {
        techSkills.push(techTags[i].textContent.trim());
    }
    
    // Sauvegarde des compétences personnelles
    var softSkills = [];
    var softTags = document.querySelectorAll('#skillsSoftContainer .skill-tag span:first-child');
    for (var i = 0; i < softTags.length; i++) {
        softSkills.push(softTags[i].textContent.trim());
    }
    
    // Sauvegarde des langues
    var languages = [];
    var langItems = document.querySelectorAll('#languagesContainer .skill-tag');
    for (var i = 0; i < langItems.length; i++) {
        var text = langItems[i].querySelector('span:first-child').textContent;
        var parts = text.split(' (');
        if (parts.length === 2) {
            languages.push({
                langue: parts[0].trim(),
                niveau: parts[1].replace(')', '').trim()
            });
        }
    }
    
    cvData.step2 = {
        ...formData,
        competences_tech: techSkills,
        competences_soft: softSkills,
        langues: languages
    };
    
    localStorage.setItem('cvData_step2', JSON.stringify(cvData.step2));
    return true;
}

// Fonctions pour les étapes 3, 4, 5 (similaires)
function validateAndSaveStep3() {
    // Sauvegarder les expériences existantes
    var experiences = [];
    var experienceCards = document.querySelectorAll('#experiencesContainer .card');
    
    for (var i = 0; i < experienceCards.length; i++) {
        var card = experienceCards[i];
        var experience = {
            poste: card.querySelector('.exp-poste')?.value || '',
            entreprise: card.querySelector('.exp-entreprise')?.value || '',
            ville: card.querySelector('.exp-ville')?.value || '',
            date_debut: card.querySelector('.exp-date-debut')?.value || '',
            date_fin: card.querySelector('.exp-date-fin')?.value || '',
            missions: card.querySelector('.exp-missions')?.value || '',
            competences: card.querySelector('.exp-competences')?.value || '',
            realisations: card.querySelector('.exp-realisations')?.value || ''
        };
        
        // Validation basique
        if (experience.poste && experience.entreprise) {
            experiences.push(experience);
        }
    }
    
    cvData.step3.experiences = experiences;
    localStorage.setItem('cvData_step3', JSON.stringify(cvData.step3));
    return true;
}

function validateAndSaveStep4() {
    // Cette étape peut être optionnelle
    localStorage.setItem('cvData_step4', JSON.stringify(cvData.step4));
    return true;
}

function validateAndSaveStep5() {
    var form = document.getElementById('cvFormStep5');
    if (!form) return true;
    
    var formData = {};
    var inputs = form.querySelectorAll('input, select');
    for (var i = 0; i < inputs.length; i++) {
        var input = inputs[i];
        formData[input.name] = input.value;
    }
    
    cvData.step5 = formData;
    localStorage.setItem('cvData_step5', JSON.stringify(formData));
    return true;
}

// Restaurer les données d'une étape
function restoreStepData(step) {
    var savedData = localStorage.getItem('cvData_step' + step);
    if (savedData) {
        try {
            var data = JSON.parse(savedData);
            
            switch(step) {
                case 1:
                    restoreStep1Data(data);
                    break;
                case 2:
                    restoreStep2Data(data);
                    break;
                case 3:
                    restoreStep3Data(data);
                    break;
                // ... autres étapes
            }
        } catch (e) {
            console.error('Erreur de restauration des données:', e);
        }
    }
}

// Restaurer les données étape 1
function restoreStep1Data(data) {
    var form = document.getElementById('cvFormStep1');
    if (!form) return;
    
    for (var key in data) {
        var input = form.querySelector('[name="' + key + '"]');
        if (input) {
            input.value = data[key];
        }
    }
}

// Restaurer les données étape 2
function restoreStep2Data(data) {
    var form = document.getElementById('cvFormStep2');
    if (!form) return;
    
    // Restaurer les champs de formulaire
    for (var key in data) {
        if (key !== 'competences_tech' && key !== 'competences_soft' && key !== 'langues') {
            var input = form.querySelector('[name="' + key + '"]');
            if (input) {
                input.value = data[key];
            }
        }
    }
    
    // Restaurer les compétences techniques
    if (data.competences_tech && Array.isArray(data.competences_tech)) {
        var container = document.getElementById('skillsTechContainer');
        if (container) {
            container.innerHTML = '';
            data.competences_tech.forEach(function(skill) {
                var tag = document.createElement('div');
                tag.className = 'skill-tag';
                tag.innerHTML = '<span>' + escapeHtml(skill) + '</span>' +
                               ' <i class="fas fa-times" onclick="removeSkill(this, \'tech\')"></i>';
                container.appendChild(tag);
            });
        }
    }
    
    // Restaurer les compétences personnelles
    if (data.competences_soft && Array.isArray(data.competences_soft)) {
        var container = document.getElementById('skillsSoftContainer');
        if (container) {
            container.innerHTML = '';
            data.competences_soft.forEach(function(skill) {
                var tag = document.createElement('div');
                tag.className = 'skill-tag';
                tag.innerHTML = '<span>' + escapeHtml(skill) + '</span>' +
                               ' <i class="fas fa-times" onclick="removeSkill(this, \'soft\')"></i>';
                container.appendChild(tag);
            });
        }
    }
    
    // Restaurer les langues
    if (data.langues && Array.isArray(data.langues)) {
        var container = document.getElementById('languagesContainer');
        if (container) {
            container.innerHTML = '';
            data.langues.forEach(function(lang) {
                var item = document.createElement('div');
                item.className = 'skill-tag';
                item.style.marginBottom = '8px';
                item.innerHTML = '<span>' + escapeHtml(lang.langue) + ' (' + escapeHtml(lang.niveau) + ')</span>' +
                                ' <i class="fas fa-times" onclick="removeLanguage(this)"></i>';
                container.appendChild(item);
            });
        }
    }
}

// Fonction modifiée pour sauvegarder une étape
function saveCVStep(step) {
    // Sauvegarder d'abord localement
    if (!saveCurrentStepData(step)) {
        return false;
    }
    
    // Ensuite envoyer au serveur
    sendCVStepToServer(step);
    
    // Passer à l'étape suivante
    if (step < 5) {
        showCVStep(step + 1);
    } else {
        // Si dernière étape, générer l'aperçu
        generatePreview();
    }
    
    return true;
}

// Fonction pour envoyer les données au serveur
function sendCVStepToServer(step) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'CRUDcv.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (!response.success) {
                        console.error('Erreur serveur:', response.message);
                        // On continue quand même car les données sont sauvegardées localement
                    }
                } catch (e) {
                    console.error('Erreur parsing JSON:', e);
                }
            } else {
                console.error('Erreur HTTP:', xhr.status);
            }
        }
    };
    
    var dataToSend = cvData['step' + step] || {};
    var params = 'action=saveCVStep&step=' + step + '&data=' + encodeURIComponent(JSON.stringify(dataToSend));
    xhr.send(params);
}

// Fonction précédente corrigée
function previousCVStep(step) {
    if (step > 1) {
        showCVStep(step - 1);
    }
}
// Dans CRUDcv.js, ajoutez cette fonction si elle n'existe pas :
function updateCVProgress(step) {
    var progressBar = document.querySelector('.progress-fill');
    var steps = document.querySelectorAll('.progress-steps .step');
    
    if (progressBar) {
        var percentage = (step / 5) * 100;
        progressBar.style.width = percentage + '%';
    }
    
    // Mettre à jour les étapes visuellement
    if (steps && steps.length > 0) {
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
// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Restaurer toutes les données du CV
    for (var i = 1; i <= 5; i++) {
        restoreStepData(i);
    }
    
    // S'assurer qu'on commence à l'étape 1
    showCVStep(1);
});

// Fonction pour réinitialiser le CV
function resetCV() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser votre CV ? Toutes les données seront perdues.')) {
        cvData = {
            step1: {},
            step2: { competences_tech: [], competences_soft: [], langues: [] },
            step3: { experiences: [] },
            step4: { formations: [], projets: [], certifications: [] },
            step5: {}
        };
        
        // Supprimer le stockage local
        for (var i = 1; i <= 5; i++) {
            localStorage.removeItem('cvData_step' + i);
        }
        
        // Réinitialiser les formulaires
        var forms = document.querySelectorAll('#modalCV form');
        for (var i = 0; i < forms.length; i++) {
            forms[i].reset();
        }
        
        // Vider les conteneurs de compétences
        var containers = ['skillsTechContainer', 'skillsSoftContainer', 'languagesContainer', 
                         'experiencesContainer', 'formationsContainer', 'projetsContainer', 'certificationsContainer'];
        
        for (var i = 0; i < containers.length; i++) {
            var container = document.getElementById(containers[i]);
            if (container) {
                container.innerHTML = '';
            }
        }
        
        // Revenir à l'étape 1
        showCVStep(1);
        showNotification('CV réinitialisé avec succès', 'success');
    }
}
// ============================================
// FONCTIONS MANQUANTES POUR LES ÉTAPES 3, 4, 5
// ============================================

// Fonctions pour l'étape 3 : Expériences professionnelles
function addExperience() {
    var container = document.getElementById('experiencesContainer');
    if (!container) return;
    
    var experienceId = 'exp_' + Date.now();
    var experienceHtml = `
        <div class="card" id="${experienceId}" style="margin-bottom: 1rem; background: rgba(255, 71, 87, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="color: var(--accent-orange);">Nouvelle expérience</h4>
                <button type="button" class="btn btn-outline" onclick="removeExperience('${experienceId}')" style="font-size: 0.8rem; padding: 5px 10px;">
                    <i class="fas fa-times"></i> Supprimer
                </button>
            </div>
            
            <div class="form-group">
                <label>Poste occupé *</label>
                <input type="text" class="exp-poste" placeholder="Ex: Développeur Web Full-Stack" required>
            </div>
            <div class="form-group">
                <label>Nom de l'entreprise *</label>
                <input type="text" class="exp-entreprise" placeholder="Ex: TechCorp SARL" required>
            </div>
            <div class="form-group">
                <label>Ville</label>
                <input type="text" class="exp-ville" placeholder="Ex: Paris">
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Date de début *</label>
                    <input type="date" class="exp-date-debut" required>
                </div>
                <div class="form-group">
                    <label>Date de fin</label>
                    <input type="date" class="exp-date-fin" placeholder="En cours si actuel">
                </div>
            </div>
            <div class="form-group">
                <label>Missions principales</label>
                <textarea class="exp-missions" rows="3" placeholder="Décrivez vos missions..."></textarea>
            </div>
            <div class="form-group">
                <label>Compétences utilisées</label>
                <textarea class="exp-competences" rows="2" placeholder="Ex: JavaScript, React, Node.js..."></textarea>
            </div>
            <div class="form-group">
                <label>Réalisations marquantes</label>
                <textarea class="exp-realisations" rows="2" placeholder="Qu'avez-vous accompli ?"></textarea>
            </div>
        </div>
    `;
    
    var div = document.createElement('div');
    div.innerHTML = experienceHtml;
    container.appendChild(div.firstElementChild);
}

function removeExperience(id) {
    var element = document.getElementById(id);
    if (element && confirm('Supprimer cette expérience ?')) {
        element.remove();
    }
}

// Fonctions pour l'étape 4 : Formations
function addFormation() {
    var container = document.getElementById('formationsContainer');
    if (!container) return;
    
    var formationId = 'formation_' + Date.now();
    var formationHtml = `
        <div class="card" id="${formationId}" style="margin-bottom: 1rem; background: rgba(116, 185, 255, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="color: var(--accent-cyan);">Nouvelle formation</h4>
                <button type="button" class="btn btn-outline" onclick="removeFormation('${formationId}')" style="font-size: 0.8rem; padding: 5px 10px;">
                    <i class="fas fa-times"></i> Supprimer
                </button>
            </div>
            
            <div class="form-group">
                <label>Diplôme / Certification *</label>
                <input type="text" class="formation-diplome" placeholder="Ex: Master Informatique" required>
            </div>
            <div class="form-group">
                <label>Établissement *</label>
                <input type="text" class="formation-etablissement" placeholder="Ex: Université Paris-Saclay" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Année d'obtention</label>
                    <input type="text" class="formation-annee" placeholder="Ex: 2022">
                </div>
                <div class="form-group">
                    <label>Ville</label>
                    <input type="text" class="formation-ville" placeholder="Ex: Paris">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea class="formation-description" rows="2" placeholder="Spécialisation, mentions..."></textarea>
            </div>
        </div>
    `;
    
    var div = document.createElement('div');
    div.innerHTML = formationHtml;
    container.appendChild(div.firstElementChild);
}

function removeFormation(id) {
    var element = document.getElementById(id);
    if (element && confirm('Supprimer cette formation ?')) {
        element.remove();
    }
}

// Fonctions pour l'étape 4 : Projets
function addProjet() {
    var container = document.getElementById('projetsContainer');
    if (!container) return;
    
    var projetId = 'projet_' + Date.now();
    var projetHtml = `
        <div class="card" id="${projetId}" style="margin-bottom: 1rem; background: rgba(0, 210, 211, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="color: var(--accent-green);">Nouveau projet</h4>
                <button type="button" class="btn btn-outline" onclick="removeProjet('${projetId}')" style="font-size: 0.8rem; padding: 5px 10px;">
                    <i class="fas fa-times"></i> Supprimer
                </button>
            </div>
            
            <div class="form-group">
                <label>Nom du projet *</label>
                <input type="text" class="projet-nom" placeholder="Ex: Application de gestion de tâches" required>
            </div>
            <div class="form-group">
                <label>Lien (URL)</label>
                <input type="text" class="projet-lien" placeholder="https://github.com/...">
            </div>
            <div class="form-group">
                <label>Rôle dans le projet</label>
                <input type="text" class="projet-role" placeholder="Ex: Développeur front-end">
            </div>
            <div class="form-group">
                <label>Description *</label>
                <textarea class="projet-description" rows="3" placeholder="Décrivez le projet, ses fonctionnalités..." required></textarea>
            </div>
            <div class="form-group">
                <label>Technologies utilisées</label>
                <textarea class="projet-technologies" rows="2" placeholder="Ex: React, Node.js, MongoDB..."></textarea>
            </div>
            <div class="form-group">
                <label>Date de réalisation</label>
                <input type="text" class="projet-date" placeholder="Ex: 2023">
            </div>
        </div>
    `;
    
    var div = document.createElement('div');
    div.innerHTML = projetHtml;
    container.appendChild(div.firstElementChild);
}

function removeProjet(id) {
    var element = document.getElementById(id);
    if (element && confirm('Supprimer ce projet ?')) {
        element.remove();
    }
}

// Fonctions pour l'étape 4 : Certifications
function addCertification() {
    var container = document.getElementById('certificationsContainer');
    if (!container) return;
    
    var certId = 'cert_' + Date.now();
    var certHtml = `
        <div class="card" id="${certId}" style="margin-bottom: 1rem; background: rgba(162, 155, 254, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="color: var(--accent-pink);">Nouvelle certification</h4>
                <button type="button" class="btn btn-outline" onclick="removeCertification('${certId}')" style="font-size: 0.8rem; padding: 5px 10px;">
                    <i class="fas fa-times"></i> Supprimer
                </button>
            </div>
            
            <div class="form-group">
                <label>Nom de la certification *</label>
                <input type="text" class="certification-nom" placeholder="Ex: AWS Certified Solutions Architect" required>
            </div>
            <div class="form-group">
                <label>Organisme émetteur</label>
                <input type="text" class="certification-organisme" placeholder="Ex: Amazon Web Services">
            </div>
            <div class="form-group">
                <label>Date d'obtention</label>
                <input type="text" class="certification-date" placeholder="Ex: Décembre 2023">
            </div>
            <div class="form-group">
                <label>Numéro de certification (ID)</label>
                <input type="text" class="certification-id" placeholder="Ex: AWS-123456">
            </div>
            <div class="form-group">
                <label>Lien de vérification</label>
                <input type="text" class="certification-lien" placeholder="https://verify.certification.com/...">
            </div>
        </div>
    `;
    
    var div = document.createElement('div');
    div.innerHTML = certHtml;
    container.appendChild(div.firstElementChild);
}

function removeCertification(id) {
    var element = document.getElementById(id);
    if (element && confirm('Supprimer cette certification ?')) {
        element.remove();
    }
}

// Fonctions pour l'étape 5 : Génération du CV
function generateCV() {
    // Sauvegarder l'étape 5 d'abord
    if (!saveCurrentStepData(5)) {
        showNotification('Veuillez vérifier les informations de l\'étape 5', 'error');
        return;
    }
    
    // Envoyer les données au serveur pour générer le PDF
    sendCVStepToServer(5);
    
    // Simuler la génération du CV
    showNotification('Génération de votre CV en cours...', 'success');
    
    setTimeout(function() {
        // Afficher l'aperçu du CV
        generatePreview();
    }, 1500);
}

function generatePreview() {
    var previewContent = document.getElementById('previewContent');
    var cvPreview = document.getElementById('cvPreview');
    var modalBody = document.querySelector('#modalCV .modal-body');
    
    if (!previewContent || !cvPreview || !modalBody) return;
    
    // Cacher toutes les étapes
    var steps = document.querySelectorAll('.cv-step');
    for (var i = 0; i < steps.length; i++) {
        steps[i].style.display = 'none';
    }
    
    // Générer l'aperçu à partir des données sauvegardées
    var previewHtml = '<h2 style="color: #000; border-bottom: 2px solid var(--accent-cyan); padding-bottom: 10px;">' +
                     (cvData.step1.nom_complet || 'Votre Nom') + '</h2>';
    
    // Informations de contact
    if (cvData.step1.email || cvData.step1.telephone || cvData.step1.linkedin) {
        previewHtml += '<div style="margin: 20px 0;">';
        if (cvData.step1.email) previewHtml += '<p><strong>Email:</strong> ' + escapeHtml(cvData.step1.email) + '</p>';
        if (cvData.step1.telephone) previewHtml += '<p><strong>Téléphone:</strong> ' + escapeHtml(cvData.step1.telephone) + '</p>';
        if (cvData.step1.linkedin) previewHtml += '<p><strong>LinkedIn:</strong> ' + escapeHtml(cvData.step1.linkedin) + '</p>';
        previewHtml += '</div>';
    }
    
    // Profil
    if (cvData.step2.profil) {
        previewHtml += '<h3 style="color: #000; margin: 20px 0 10px 0; border-bottom: 1px solid #ddd;">Profil</h3>' +
                      '<p>' + escapeHtml(cvData.step2.profil) + '</p>';
    }
    
    // Expériences
    if (cvData.step3.experiences && cvData.step3.experiences.length > 0) {
        previewHtml += '<h3 style="color: #000; margin: 20px 0 10px 0; border-bottom: 1px solid #ddd;">Expériences Professionnelles</h3>';
        cvData.step3.experiences.forEach(function(exp) {
            previewHtml += '<div style="margin: 15px 0;">' +
                         '<h4 style="color: #333; margin: 5px 0;">' + escapeHtml(exp.poste) + '</h4>' +
                         '<p style="color: #666; margin: 5px 0;">' + escapeHtml(exp.entreprise) + 
                         (exp.ville ? ' - ' + escapeHtml(exp.ville) : '') + '</p>' +
                         '<p style="color: #666; font-size: 0.9em;">' + exp.date_debut + 
                         (exp.date_fin ? ' - ' + exp.date_fin : ' - Présent') + '</p>';
            if (exp.missions) previewHtml += '<p><strong>Missions:</strong> ' + escapeHtml(exp.missions) + '</p>';
            if (exp.competences) previewHtml += '<p><strong>Compétences:</strong> ' + escapeHtml(exp.competences) + '</p>';
            previewHtml += '</div>';
        });
    }
    
    // Compétences
    var hasSkills = (cvData.step2.competences_tech && cvData.step2.competences_tech.length > 0) ||
                   (cvData.step2.competences_soft && cvData.step2.competences_soft.length > 0);
    
    if (hasSkills) {
        previewHtml += '<h3 style="color: #000; margin: 20px 0 10px 0; border-bottom: 1px solid #ddd;">Compétences</h3>';
        
        if (cvData.step2.competences_tech && cvData.step2.competences_tech.length > 0) {
            previewHtml += '<p><strong>Techniques:</strong> ' + cvData.step2.competences_tech.join(', ') + '</p>';
        }
        
        if (cvData.step2.competences_soft && cvData.step2.competences_soft.length > 0) {
            previewHtml += '<p><strong>Personnelles:</strong> ' + cvData.step2.competences_soft.join(', ') + '</p>';
        }
    }
    
    previewContent.innerHTML = previewHtml;
    cvPreview.style.display = 'block';
    
    // Faire défiler vers l'aperçu
    modalBody.scrollTop = 0;
}

function editCV() {
    // Revenir à la première étape
    showCVStep(1);
    document.getElementById('cvPreview').style.display = 'none';
}

function downloadCV() {
    // Récupérer toutes les données du CV
    var cvData = {};
    
    // Combiner toutes les étapes depuis localStorage
    for (var i = 1; i <= 5; i++) {
        var savedData = localStorage.getItem('cvData_step' + i);
        if (savedData) {
            try {
                cvData['step' + i] = JSON.parse(savedData);
            } catch (e) {
                console.error('Erreur parsing data step ' + i, e);
            }
        }
    }
    
    // Récupérer les options de format depuis l'étape 5
    var format = 'pdf';
    var style = 'moderne';
    var langue = 'fr';
    
    if (cvData.step5) {
        format = cvData.step5.format || 'pdf';
        style = cvData.step5.style || 'moderne';
        langue = cvData.step5.langue || 'fr';
    }
    
    showNotification('Préparation du téléchargement...', 'success');
    
    // Créer un formulaire pour envoyer les données
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'generate_cv.php';
    form.style.display = 'none';
    form.target = '_blank'; // Ouvrir dans un nouvel onglet
    
    // Ajouter les données
    var dataInput = document.createElement('input');
    dataInput.type = 'hidden';
    dataInput.name = 'data';
    dataInput.value = JSON.stringify(cvData);
    
    var formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    
    var styleInput = document.createElement('input');
    styleInput.type = 'hidden';
    styleInput.name = 'style';
    styleInput.value = style;
    
    var langueInput = document.createElement('input');
    langueInput.type = 'hidden';
    langueInput.name = 'langue';
    langueInput.value = langue;
    
    form.appendChild(dataInput);
    form.appendChild(formatInput);
    form.appendChild(styleInput);
    form.appendChild(langueInput);
    
    document.body.appendChild(form);
    form.submit();
    
    // Nettoyer après 3 secondes
    setTimeout(function() {
        document.body.removeChild(form);
    }, 3000);
}

// ============================================
// FONCTIONS POUR L'HISTORIQUE DES CV
// ============================================

function loadCVHistory() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'CRUDcv.php?action=getCVHistory', true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    displayCVHistory(response.history);
                }
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
            }
        }
    };
    
    xhr.send();
}

function displayCVHistory(history) {
    var container = document.getElementById('cvHistoryContent');
    if (!container) return;
    
    if (!history || history.length === 0) {
        container.innerHTML = '<div class="card"><p style="text-align: center; padding: 2rem; color: var(--text-light);">' +
            '<i class="fas fa-history" style="font-size: 3rem; margin-bottom: 1rem; display: block; color: var(--accent-cyan);"></i>' +
            'Aucun CV créé pour le moment.' +
            '</p></div>';
        return;
    }
    
    var html = '<div class="cv-history-list">';
    
    // Trier par date (du plus récent au plus ancien)
    history.sort(function(a, b) {
        return new Date(b.date) - new Date(a.date);
    });
    
    history.forEach(function(cv) {
        var date = new Date(cv.date);
        var formattedDate = date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        var formatIcon = cv.format === 'pdf' ? 'fa-file-pdf' : 
                        cv.format === 'word' ? 'fa-file-word' : 'fa-file-alt';
        
        html += '<div class="card cv-history-item" data-cv-id="' + cv.id + '" style="margin-bottom: 1rem; background: rgba(116, 185, 255, 0.1);">' +
            '<div style="display: flex; justify-content: space-between; align-items: flex-start;">' +
            '<div style="flex: 1;">' +
            '<h4 style="color: var(--accent-cyan); margin-bottom: 0.5rem;">' +
            '<i class="fas ' + formatIcon + '"></i> ' +
            cv.nom.replace(/_/g, ' ') +
            '</h4>' +
            '<p style="color: #a0aec0; font-size: 0.9rem; margin-bottom: 0.5rem;">' +
            '<i class="fas fa-calendar"></i> Créé le ' + formattedDate +
            '</p>' +
            '<p style="color: #a0aec0; font-size: 0.9rem; margin-bottom: 0.5rem;">' +
            '<i class="fas fa-paint-brush"></i> Style: ' + cv.style + ' | ' +
            '<i class="fas fa-language"></i> Langue: ' + cv.langue.toUpperCase() +
            '</p>' +
            '</div>' +
            '<div style="display: flex; flex-direction: column; gap: 5px;">' +
            '<button type="button" class="btn btn-primary" onclick="downloadCVFromHistory(\'' + cv.id + '\')" style="padding: 8px 15px; font-size: 0.9rem;">' +
            '<i class="fas fa-download"></i> Télécharger' +
            '</button>' +
            '<button type="button" class="btn btn-outline" onclick="deleteCVFromHistory(\'' + cv.id + '\')" style="padding: 8px 15px; font-size: 0.9rem;">' +
            '<i class="fas fa-trash"></i> Supprimer' +
            '</button>' +
            '</div>' +
            '</div>' +
            '</div>';
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function downloadCVFromHistory(cvId) {
    showNotification('Préparation du téléchargement...', 'info');
    
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'CRUDcv.php';
    form.style.display = 'none';
    
    var actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'downloadCVFromHistory';
    
    var idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'cv_id';
    idInput.value = cvId;
    
    form.appendChild(actionInput);
    form.appendChild(idInput);
    document.body.appendChild(form);
    form.submit();
    
    setTimeout(function() {
        document.body.removeChild(form);
    }, 3000);
}

function deleteCVFromHistory(cvId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce CV de l\'historique ?')) {
        return;
    }
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'CRUDcv.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    showNotification('CV supprimé de l\'historique', 'success');
                    loadCVHistory(); // Recharger la liste
                }
            } catch (e) {
                console.error('Erreur parsing JSON:', e);
            }
        }
    };
    
    var params = 'action=deleteCVFromHistory&cv_id=' + encodeURIComponent(cvId);
    xhr.send(params);
}

// Modifier la fonction viewCVHistory dans CRUD.js
function viewCVHistory() {
    openModal('modalCVHistory');
    // Charger l'historique quand le modal s'ouvre
    setTimeout(loadCVHistory, 100);
}

// Fonctions pour les compétences (étape 2) - déjà présentes dans votre code mais je les inclue au cas où
function addTechSkill() {
    var input = document.getElementById('skillTechInput');
    if (!input || !input.value.trim()) return;
    
    var container = document.getElementById('skillsTechContainer');
    if (!container) return;
    
    var skill = input.value.trim();
    var tag = document.createElement('div');
    tag.className = 'skill-tag';
    tag.innerHTML = '<span>' + escapeHtml(skill) + '</span>' +
                   ' <i class="fas fa-times" onclick="removeSkill(this, \'tech\')"></i>';
    container.appendChild(tag);
    
    input.value = '';
}

function addSoftSkill() {
    var input = document.getElementById('skillSoftInput');
    if (!input || !input.value.trim()) return;
    
    var container = document.getElementById('skillsSoftContainer');
    if (!container) return;
    
    var skill = input.value.trim();
    var tag = document.createElement('div');
    tag.className = 'skill-tag';
    tag.innerHTML = '<span>' + escapeHtml(skill) + '</span>' +
                   ' <i class="fas fa-times" onclick="removeSkill(this, \'soft\')"></i>';
    container.appendChild(tag);
    
    input.value = '';
}

function addLanguage() {
    var input = document.getElementById('languageInput');
    var select = document.getElementById('languageLevel');
    if (!input || !input.value.trim() || !select) return;
    
    var container = document.getElementById('languagesContainer');
    if (!container) return;
    
    var language = input.value.trim();
    var level = select.options[select.selectedIndex].text;
    
    var item = document.createElement('div');
    item.className = 'skill-tag';
    item.style.marginBottom = '8px';
    item.innerHTML = '<span>' + escapeHtml(language) + ' (' + escapeHtml(level) + ')</span>' +
                    ' <i class="fas fa-times" onclick="removeLanguage(this)"></i>';
    container.appendChild(item);
    
    input.value = '';
}

function removeSkill(element, type) {
    if (confirm('Supprimer cette compétence ?')) {
        var tag = element.closest('.skill-tag');
        if (tag) {
            tag.remove();
        }
    }
}

function removeLanguage(element) {
    if (confirm('Supprimer cette langue ?')) {
        var tag = element.closest('.skill-tag');
        if (tag) {
            tag.remove();
        }
    }
}

// Fonction d'échappement HTML
function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}