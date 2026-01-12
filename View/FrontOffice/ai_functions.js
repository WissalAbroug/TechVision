// ai_functions.js
// Fonctions pour les fonctionnalités IA

function openAIModal() {
    closeDrawer(); // Fermer le tiroir si ouvert
    openModal('modalAI');
}

function closeAIModal() {
    closeModal('modalAI');
}

// 1. Générer un CV avec IA
function generateCVWithAI() {
    showAILoading('Analyse de votre profil en cours...');
    
    setTimeout(function() {
        showAINotification('CV généré avec IA !', 'success');
        
        // Récupérer les données du profil
        var userData = getUserProfileData();
        
        // Appeler une API IA (simulé ici)
        simulateAIGeneration('cv', userData, function(response) {
            displayAIResponse(response);
            
            // Créer un nouveau fichier CV
            createNewFile('cv_ia_' + Date.now() + '.html', 
                         response.cvContent, 
                         'text/html');
        });
    }, 1500);
}

// 2. Optimiser le profil avec IA
function optimizeProfileWithAI() {
    showAILoading('Analyse de votre profil...');
    
    setTimeout(function() {
        var suggestions = [
            "Ajoutez plus de mots-clés techniques dans votre profil",
            "Mettez à jour votre photo professionnelle",
            "Ajoutez des certifications récentes",
            "Optimisez votre titre professionnel",
            "Augmentez le détail de vos expériences"
        ];
        
        var response = {
            type: 'profile_optimization',
            suggestions: suggestions,
            score: 78,
            improvements: 5
        };
        
        displayAIResponse(response);
        showAINotification('Profil analysé avec succès', 'success');
    }, 2000);
}

// 3. Analyser les offres avec IA
function analyzeOffersWithAI() {
    showAILoading('Analyse des offres en cours...');
    
    setTimeout(function() {
        var analysis = {
            type: 'offer_analysis',
            totalOffers: 15,
            bestMatch: {
                title: "Développeur Full-Stack",
                company: "TechCorp",
                matchScore: 92,
                skills: ["JavaScript", "React", "Node.js"]
            },
            recommendations: [
                "Postulez à 3 offres similaires",
                "Améliorez vos compétences en DevOps",
                "Créez un projet open-source"
            ]
        };
        
        displayAIResponse(analysis);
        showAINotification('Analyse complète', 'info');
    }, 1800);
}

// 4. Générer une lettre de motivation
function generateCoverLetter() {
    showAILoading('Génération de la lettre...');
    
    setTimeout(function() {
        var letter = `Objet : Candidature spontanée

Madame, Monsieur,

Je me permets de vous adresser ma candidature pour un poste au sein de votre entreprise. 

Mon profil de développeur passionné avec 3 ans d'expérience en JavaScript et React correspond parfaitement aux exigences de votre équipe.

Je serais ravi de pouvoir contribuer à vos projets innovants.

Cordialement,
[Votre nom]`;
        
        createNewFile('lettre_motivation_' + Date.now() + '.txt', letter, 'text/plain');
        showAINotification('Lettre générée !', 'success');
    }, 2000);
}

// 5. Créer un nouveau document
function createNewDocument() {
    var docTypes = [
        { name: 'CV', icon: 'fa-file-alt' },
        { name: 'Lettre de motivation', icon: 'fa-envelope' },
        { name: 'Portfolio', icon: 'fa-briefcase' },
        { name: 'Présentation', icon: 'fa-chart-line' },
        { name: 'Rapport', icon: 'fa-chart-bar' },
        { name: 'Plan de carrière', icon: 'fa-road' }
    ];
    
    var html = '<h4 style="color: var(--accent-cyan); margin-bottom: 1rem;">' +
               '<i class="fas fa-plus-circle"></i> Choisissez un type de document' +
               '</h4>' +
               '<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">';
    
    docTypes.forEach(function(type) {
        html += `<div class="ai-feature-card" onclick="createDocumentType('${type.name}')">
                    <i class="fas ${type.icon}"></i>
                    <h4>${type.name}</h4>
                </div>`;
    });
    
    html += '</div>';
    
    document.getElementById('aiResponse').innerHTML = html;
    document.getElementById('aiResponse').style.display = 'block';
}

function createDocumentType(type) {
    var templates = {
        'CV': generateCVTemplate(),
        'Lettre de motivation': generateLetterTemplate(),
        'Portfolio': generatePortfolioTemplate(),
        'Présentation': generatePresentationTemplate(),
        'Rapport': generateReportTemplate(),
        'Plan de carrière': generateCareerPlanTemplate()
    };
    
    var content = templates[type] || 'Contenu par défaut';
    var filename = type.toLowerCase().replace(/ /g, '_') + '_' + Date.now();
    
    var extension = type === 'Présentation' ? '.html' : 
                   type === 'Rapport' ? '.docx' : '.txt';
    
    createNewFile(filename + extension, content, getMimeType(extension));
    showAINotification(type + ' créé avec succès', 'success');
}

// 6. Chat avec l'IA
function showAIChat() {
    document.getElementById('aiChatSection').style.display = 'block';
    document.getElementById('aiResponse').style.display = 'none';
}

function submitAIQuestion() {
    var question = document.getElementById('aiQuestion').value;
    if (!question.trim()) {
        showAINotification('Veuillez saisir une question', 'error');
        return;
    }
    
    showAILoading('L\'IA réfléchit...');
    
    setTimeout(function() {
        var answers = [
            "Pour améliorer votre CV, mettez en avant vos projets concrets avec des chiffres clés.",
            "Je recommande d'ajouter des certifications récentes dans votre domaine.",
            "Votre profil est bien structuré, mais ajoutez plus de mots-clés techniques.",
            "Pour un poste de développeur, montrez vos contributions open-source.",
            "Optimisez votre profil LinkedIn avec un titre accrocheur.",
            "Créez un portfolio en ligne pour montrer vos réalisations."
        ];
        
        var randomAnswer = answers[Math.floor(Math.random() * answers.length)];
        
        var response = {
            type: 'chat_response',
            question: question,
            answer: randomAnswer,
            timestamp: new Date().toLocaleString()
        };
        
        displayAIResponse(response);
        document.getElementById('aiQuestion').value = '';
    }, 1500);
}

// Fonctions utilitaires
function showAILoading(message) {
    var html = `<div class="ai-loading">
                    <div class="spinner"></div>
                    <p style="color: var(--accent-pink);">${message}</p>
                </div>`;
    
    document.getElementById('aiResponse').innerHTML = html;
    document.getElementById('aiResponse').style.display = 'block';
}

function displayAIResponse(response) {
    var html = '<div class="card" style="background: rgba(162, 155, 254, 0.1);">';
    
    if (response.type === 'profile_optimization') {
        html += `<h4 style="color: var(--accent-pink); margin-bottom: 1rem;">
                    <i class="fas fa-chart-line"></i> Score de profil: ${response.score}%
                </h4>`;
        
        html += `<p style="color: white; margin-bottom: 1rem;">
                    ${response.improvements} améliorations possibles
                </p>`;
        
        html += `<h5 style="color: var(--accent-cyan); margin-bottom: 0.5rem;">Suggestions:</h5>
                <ul style="color: #a0aec0; padding-left: 1.5rem;">`;
        
        response.suggestions.forEach(function(suggestion) {
            html += `<li style="margin-bottom: 0.5rem;">${suggestion}</li>`;
        });
        
        html += '</ul>';
    }
    else if (response.type === 'chat_response') {
        html += `<p style="color: white; margin-bottom: 1rem;">
                    <strong>Votre question:</strong> ${response.question}
                </p>`;
        html += `<div style="background: rgba(116, 185, 255, 0.1); padding: 1rem; border-radius: 8px;">
                    <p style="color: var(--accent-green); margin: 0;">
                        <i class="fas fa-robot"></i> ${response.answer}
                    </p>
                </div>`;
        html += `<p style="color: #a0aec0; font-size: 0.8rem; margin-top: 1rem;">
                    <i class="fas fa-clock"></i> ${response.timestamp}
                </p>`;
    }
    else {
        html += `<pre style="color: white; white-space: pre-wrap;">${JSON.stringify(response, null, 2)}</pre>`;
    }
    
    html += '</div>';
    document.getElementById('aiResponse').innerHTML = html;
}

function createNewFile(filename, content, mimeType) {
    var blob = new Blob([content], { type: mimeType });
    var url = URL.createObjectURL(blob);
    
    var a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.style.display = 'none';
    
    document.body.appendChild(a);
    a.click();
    
    setTimeout(function() {
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }, 100);
}

function getMimeType(extension) {
    var mimeTypes = {
        '.html': 'text/html',
        '.txt': 'text/plain',
        '.docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        '.pdf': 'application/pdf',
        '.json': 'application/json'
    };
    
    return mimeTypes[extension] || 'text/plain';
}

function getUserProfileData() {
    // Simuler la récupération des données du profil
    return {
        name: document.querySelector('[name="nom_complet"]')?.value || 'Utilisateur',
        skills: Array.from(document.querySelectorAll('#profileSkills .skill-tag span')).map(s => s.textContent),
        experience: document.querySelector('[name="niveau_experience"]')?.value || 'intermediaire',
        email: document.querySelector('[name="email"]')?.value || ''
    };
}

function simulateAIGeneration(type, data, callback) {
    // Simulation d'une réponse IA
    var responses = {
        'cv': {
            cvContent: generateAICVContent(data),
            suggestions: ["Ajoutez des projets GitHub", "Incluez des chiffres clés", "Optimisez les mots-clés"],
            generatedAt: new Date().toISOString()
        }
    };
    
    setTimeout(function() {
        callback(responses[type] || { error: 'Type non supporté' });
    }, 1000);
}

function generateAICVContent(data) {
    return `<!DOCTYPE html>
<html>
<head>
    <title>CV ${data.name}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #2c3e50; }
        .section { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>${data.name}</h1>
    <div class="section">
        <h2>Compétences</h2>
        <p>${data.skills.join(', ')}</p>
    </div>
    <!-- Généré par l'IA PathFinder -->
</body>
</html>`;
}

function clearAIChat() {
    document.getElementById('aiQuestion').value = '';
    document.getElementById('aiResponse').style.display = 'none';
}

function showAINotification(message, type) {
    showNotification('🤖 ' + message, type);
}

// Templates
function generateCVTemplate() {
    return `NOM : [Votre nom]
EMAIL : [votre@email.com]
TITRE : [Titre professionnel]

PROFIL
Expérience [X] ans dans [domaine]. Passionné(e) par [spécialité].

EXPÉRIENCES
- [Poste] chez [Entreprise] ([Dates])
  ○ [Réalisation 1]
  ○ [Réalisation 2]

COMPÉTENCES
- [Compétence 1]
- [Compétence 2]
- [Compétence 3]

FORMATIONS
- [Diplôme], [Établissement] ([Année])`;
}

function generateLetterTemplate() {
    return `[Votre Nom]
[Adresse]
[Email]
[Téléphone]

[Date]

[Entreprise]
[Adresse de l'entreprise]

Objet : Candidature pour le poste de [Poste]

Madame, Monsieur,

Par la présente, je vous soumets ma candidature...

Cordialement,
[Votre Nom]`;
}

function generatePortfolioTemplate() {
    return `<!DOCTYPE html>
<html>
<head>
    <title>Portfolio - [Votre Nom]</title>
</head>
<body>
    <h1>Portfolio Professionnel</h1>
    <section>
        <h2>Projets</h2>
        <!-- Ajoutez vos projets ici -->
    </section>
</body>
</html>`;
}

function generatePresentationTemplate() {
    return `# Présentation Professionnelle

## À propos
[Votre présentation]

## Compétences
- Compétence 1
- Compétence 2

## Projets
1. Projet 1
2. Projet 2

## Contact
[Informations de contact]`;
}

function generateReportTemplate() {
    return `RAPPORT PROFESSIONNEL

Date : [Date]
Auteur : [Votre Nom]

1. Introduction
[Texte]

2. Analyse
[Texte]

3. Conclusion
[Texte]`;
}

function generateCareerPlanTemplate() {
    return `PLAN DE CARRIÈRE

Objectif à court terme (1 an) :
- [Objectif 1]
- [Objectif 2]

Objectif à moyen terme (3 ans) :
- [Objectif 1]
- [Objectif 2]

Objectif à long terme (5 ans) :
- [Objectif 1]

Compétences à développer :
- [Compétence 1]
- [Compétence 2]`;
}