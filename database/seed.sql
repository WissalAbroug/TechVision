-- ============================================================
-- PathFinder AI — Seed Data (INSERT INTO toutes les tables)
-- Mot de passe : Admin123!
-- ============================================================
USE pathfinder_ai;
SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ── VIDER LES TABLES (DELETE FROM respecte InnoDB) ─────────
DELETE FROM reponses;
DELETE FROM reclamations;
DELETE FROM reponses_simulation;
DELETE FROM entretiens;
DELETE FROM candidatures;
DELETE FROM user_formations;
DELETE FROM formations;
DELETE FROM requirements;
DELETE FROM offres;
DELETE FROM cvs;
DELETE FROM profils;
DELETE FROM metiers;
DELETE FROM users;

-- Réinitialiser les auto-increments
ALTER TABLE users             AUTO_INCREMENT = 1;
ALTER TABLE profils           AUTO_INCREMENT = 1;
ALTER TABLE metiers           AUTO_INCREMENT = 1;
ALTER TABLE cvs               AUTO_INCREMENT = 1;
ALTER TABLE offres            AUTO_INCREMENT = 1;
ALTER TABLE requirements      AUTO_INCREMENT = 1;
ALTER TABLE candidatures      AUTO_INCREMENT = 1;
ALTER TABLE entretiens        AUTO_INCREMENT = 1;
ALTER TABLE formations        AUTO_INCREMENT = 1;
ALTER TABLE user_formations   AUTO_INCREMENT = 1;
ALTER TABLE reclamations      AUTO_INCREMENT = 1;
ALTER TABLE reponses          AUTO_INCREMENT = 1;
ALTER TABLE questions_entretien AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- ── USERS ───────────────────────────────────────────────────
-- password = Admin123! => $2y$10$...  (généré via password_hash)
INSERT INTO users (id, nom, prenom, email, password, role, statut, nom_societe, secteur) VALUES
(1,  'Admin',     'Super',    'admin@pathfinder.ma',      '$2y$10$8mDTLK7gn6zszLnuNO4rS.C9HeDMUqxXX27CiTcZ55.Z4Ki5DlI82', 'admin',    'actif', NULL,              NULL),
(2,  'TechCorp',  'DRH',      'rh@techcorp.ma',           '$2y$10$8mDTLK7gn6zszLnuNO4rS.C9HeDMUqxXX27CiTcZ55.Z4Ki5DlI82', 'societe',  'actif', 'TechCorp Maroc',  'IT'),
(3,  'DigitalHub','Recruteur','contact@digitalhub.ma',    '$2y$10$8mDTLK7gn6zszLnuNO4rS.C9HeDMUqxXX27CiTcZ55.Z4Ki5DlI82', 'societe',  'actif', 'DigitalHub',      'Marketing'),
(4,  'Alami',     'Youssef',  'youssef@gmail.com',        '$2y$10$8mDTLK7gn6zszLnuNO4rS.C9HeDMUqxXX27CiTcZ55.Z4Ki5DlI82', 'candidat', 'actif', NULL,              NULL),
(5,  'Benmoussa', 'Sara',     'sara@gmail.com',           '$2y$10$8mDTLK7gn6zszLnuNO4rS.C9HeDMUqxXX27CiTcZ55.Z4Ki5DlI82', 'candidat', 'actif', NULL,              NULL),
(6,  'Chakir',    'Mehdi',    'mehdi@gmail.com',          '$2y$10$8mDTLK7gn6zszLnuNO4rS.C9HeDMUqxXX27CiTcZ55.Z4Ki5DlI82', 'candidat', 'actif', NULL,              NULL),
(7,  'Fassi',     'Nadia',    'nadia@gmail.com',          '$2y$10$8mDTLK7gn6zszLnuNO4rS.C9HeDMUqxXX27CiTcZ55.Z4Ki5DlI82', 'candidat', 'actif', NULL,              NULL),
(8,  'InnovateSA','RH',       'jobs@innovate.ma',         '$2y$10$8mDTLK7gn6zszLnuNO4rS.C9HeDMUqxXX27CiTcZ55.Z4Ki5DlI82', 'societe',  'actif', 'Innovate SA',     'Finance');

-- ── PROFILS ─────────────────────────────────────────────────
INSERT INTO profils (user_id, titre, telephone, ville, bio, linkedin, github, competences) VALUES
(4, 'Développeur PHP Full Stack', '0661234567', 'Casablanca', 'Passionné de développement web avec 3 ans d expérience en PHP/Laravel et Vue.js.', 'https://linkedin.com/in/youssef', 'https://github.com/youssef', '["PHP","Laravel","MySQL","JavaScript","Vue.js","Git"]'),
(5, 'Data Scientist Junior',      '0662345678', 'Rabat',      'Diplômée en Data Science, spécialisée en Machine Learning et Python.', 'https://linkedin.com/in/sara', NULL, '["Python","Pandas","Scikit-learn","SQL","TensorFlow","Jupyter"]'),
(6, 'DevOps Engineer',            '0663456789', 'Marrakech',  'Expert en conteneurisation Docker/Kubernetes et pipelines CI/CD.', 'https://linkedin.com/in/mehdi', 'https://github.com/mehdi', '["Docker","Kubernetes","Jenkins","Linux","AWS","Python"]'),
(7, 'Designer UX/UI',             '0664567890', 'Casablanca', 'Créative et passionnée de design centrée utilisateur avec Figma.', 'https://linkedin.com/in/nadia', NULL, '["Figma","Adobe XD","HTML","CSS","Prototypage","UX Research"]');

-- ── METIERS ─────────────────────────────────────────────────
INSERT INTO metiers (id, nom, categorie, description) VALUES
(1,  'Développeur Web Full Stack', 'IT',         'Développement front et back-end'),
(2,  'Data Scientist',             'IT',         'Analyse de données et machine learning'),
(3,  'Chef de Projet IT',          'Management', 'Gestion de projets informatiques'),
(4,  'Designer UX/UI',             'Design',     'Conception d interfaces utilisateur'),
(5,  'DevOps Engineer',            'IT',         'Intégration et déploiement continus'),
(6,  'Développeur Mobile',         'IT',         'Applications iOS et Android'),
(7,  'Ingénieur Cybersécurité',    'IT',         'Sécurité des systèmes'),
(8,  'Commercial B2B',             'Ventes',     'Vente aux entreprises'),
(9,  'Analyste Business',          'Business',   'Analyse des besoins métier'),
(10, 'Comptable',                  'Finance',    'Gestion comptable');

-- ── CVS ─────────────────────────────────────────────────────
INSERT INTO cvs (user_id, titre, resume, competences, experiences, educations, langues) VALUES
(4, 'Développeur PHP Full Stack',
   'Développeur passionné avec 3 ans d expérience en PHP/MySQL et frameworks modernes.',
   '["PHP","Laravel","MySQL","JavaScript","Vue.js","Git","Docker","REST API"]',
   '[{"poste":"Développeur PHP","societe":"WebAgency Casablanca","debut":"2022-01","fin":"","desc":"Développement d applications web PHP/Laravel, intégration API REST, optimisation MySQL."},{"poste":"Stagiaire Développeur","societe":"StartupMa","debut":"2021-06","fin":"2021-12","desc":"Développement de modules PHP, maintenance et débogage."}]',
   '[{"diplome":"Licence Informatique","etablissement":"FSJES Casablanca","annee":"2021"},{"diplome":"Baccalauréat Sciences","etablissement":"Lycée Mohammed V","annee":"2018"}]',
   '[{"langue":"Arabe","niveau":"Natif"},{"langue":"Français","niveau":"Courant"},{"langue":"Anglais","niveau":"Intermédiaire"}]'),

(5, 'Data Scientist',
   'Diplômée en Data Science, expérience en ML et visualisation de données.',
   '["Python","Pandas","Scikit-learn","TensorFlow","SQL","Jupyter","Power BI","R"]',
   '[{"poste":"Analyste Data Junior","societe":"DataMaroc","debut":"2023-01","fin":"","desc":"Analyse de données clients, création de modèles prédictifs ML, rapports Power BI."}]',
   '[{"diplome":"Master Data Science","etablissement":"EMI Rabat","annee":"2022"},{"diplome":"Licence Maths-Informatique","etablissement":"FS Rabat","annee":"2020"}]',
   '[{"langue":"Arabe","niveau":"Natif"},{"langue":"Français","niveau":"Courant"},{"langue":"Anglais","niveau":"Courant"}]'),

(6, 'DevOps Engineer',
   'Ingénieur DevOps avec expertise Docker, Kubernetes et cloud AWS.',
   '["Docker","Kubernetes","Jenkins","Linux","AWS","Terraform","Python","CI/CD","Git","Nginx"]',
   '[{"poste":"DevOps Engineer","societe":"CloudSystems","debut":"2021-09","fin":"","desc":"Mise en place pipelines CI/CD Jenkins, orchestration Kubernetes, infrastructure AWS."},{"poste":"Administrateur Systèmes","societe":"TechSupport","debut":"2020-01","fin":"2021-08","desc":"Administration serveurs Linux, monitoring, automatisation scripts Bash/Python."}]',
   '[{"diplome":"Ingénieur Réseaux & Télécoms","etablissement":"INPT Rabat","annee":"2020"}]',
   '[{"langue":"Arabe","niveau":"Natif"},{"langue":"Français","niveau":"Courant"},{"langue":"Anglais","niveau":"Courant"}]'),

(7, 'Designer UX/UI Senior',
   'Designer créative spécialisée UX/UI avec 4 ans d expérience produit.',
   '["Figma","Adobe XD","Photoshop","HTML","CSS","Prototypage","UX Research","Zeplin"]',
   '[{"poste":"UX/UI Designer","societe":"DesignStudio","debut":"2020-03","fin":"","desc":"Conception interfaces Figma, user research, tests utilisateurs, design system."}]',
   '[{"diplome":"Licence Arts Appliqués & Design","etablissement":"ESAV Marrakech","annee":"2019"}]',
   '[{"langue":"Arabe","niveau":"Natif"},{"langue":"Français","niveau":"Courant"},{"langue":"Anglais","niveau":"Intermédiaire"}]');

-- ── OFFRES ──────────────────────────────────────────────────
INSERT INTO offres (id, societe_id, metier_id, titre, description, localisation, type_contrat, salaire, experience_requise, statut, date_expiration) VALUES
(1, 2, 1, 'Développeur PHP Laravel Senior',
   'TechCorp Maroc recherche un développeur PHP Laravel senior pour rejoindre notre équipe technique. Vous travaillerez sur des projets e-commerce et SaaS de grande envergure.',
   'Casablanca', 'CDI', '12 000 - 18 000 MAD', '3 ans minimum', 'active', '2026-08-31'),

(2, 2, 5, 'DevOps Engineer AWS',
   'Nous recrutons un ingénieur DevOps pour orchestrer notre infrastructure cloud AWS et mettre en place des pipelines CI/CD robustes.',
   'Casablanca', 'CDI', '15 000 - 22 000 MAD', '2 ans minimum', 'active', '2026-07-31'),

(3, 3, 2, 'Data Scientist Machine Learning',
   'DigitalHub cherche un Data Scientist passionné pour analyser de grands volumes de données et développer des modèles prédictifs.',
   'Rabat', 'CDI', '14 000 - 20 000 MAD', '2 ans', 'active', '2026-09-30'),

(4, 3, 4, 'Designer UX/UI Figma',
   'Nous recherchons un designer UX/UI talentueux pour concevoir des expériences utilisateur pour nos produits digitaux.',
   'Casablanca', 'CDI', '10 000 - 15 000 MAD', '2 ans', 'active', '2026-08-15'),

(5, 8, 1, 'Développeur Full Stack React/Node',
   'Innovate SA recrute un développeur full stack pour ses nouvelles applications FinTech.',
   'Casablanca', 'CDI', '16 000 - 24 000 MAD', '3 ans', 'active', '2026-09-01'),

(6, 8, 6, 'Développeur Mobile Flutter',
   'Rejoignez notre équipe mobile pour développer des applications cross-platform Flutter innovantes.',
   'Rabat', 'CDD', '10 000 - 14 000 MAD', '1 an', 'active', '2026-07-15'),

(7, 2, 1, 'Stagiaire Développeur Web PHP',
   'Stage de fin d études en développement web PHP. Formation et encadrement assurés.',
   'Casablanca', 'Stage', '3 000 - 4 000 MAD', 'Débutant', 'active', '2026-06-30'),

(8, 3, 3, 'Chef de Projet IT',
   'DigitalHub cherche un chef de projet IT expérimenté pour piloter des projets digitaux complexes.',
   'Rabat', 'CDI', '18 000 - 25 000 MAD', '5 ans', 'inactive', '2026-10-01');

-- ── REQUIREMENTS ────────────────────────────────────────────
INSERT INTO requirements (offre_id, competence, niveau, obligatoire) VALUES
-- Offre 1: PHP Laravel Senior
(1, 'PHP',        'expert',         1),
(1, 'Laravel',    'expert',         1),
(1, 'MySQL',      'intermediaire',  1),
(1, 'JavaScript', 'intermediaire',  1),
(1, 'Git',        'intermediaire',  1),
(1, 'REST API',   'intermediaire',  0),
(1, 'Docker',     'debutant',       0),
-- Offre 2: DevOps
(2, 'Docker',      'expert',        1),
(2, 'Kubernetes',  'expert',        1),
(2, 'AWS',         'intermediaire', 1),
(2, 'Linux',       'intermediaire', 1),
(2, 'CI/CD',       'intermediaire', 1),
(2, 'Python',      'debutant',      0),
(2, 'Terraform',   'debutant',      0),
-- Offre 3: Data Scientist
(3, 'Python',       'expert',        1),
(3, 'Scikit-learn', 'intermediaire', 1),
(3, 'Pandas',       'intermediaire', 1),
(3, 'SQL',          'intermediaire', 1),
(3, 'TensorFlow',   'intermediaire', 0),
(3, 'Power BI',     'debutant',      0),
-- Offre 4: UX/UI
(4, 'Figma',       'expert',        1),
(4, 'Adobe XD',    'intermediaire', 1),
(4, 'Prototypage', 'intermediaire', 1),
(4, 'HTML',        'debutant',      0),
(4, 'CSS',         'debutant',      0),
-- Offre 5: Full Stack
(5, 'JavaScript', 'expert',        1),
(5, 'React',      'expert',        1),
(5, 'Node.js',    'intermediaire', 1),
(5, 'MySQL',      'intermediaire', 1),
(5, 'Git',        'intermediaire', 1),
(5, 'Docker',     'debutant',      0),
-- Offre 6: Flutter
(6, 'Flutter',   'intermediaire', 1),
(6, 'Dart',      'intermediaire', 1),
(6, 'Git',       'intermediaire', 1),
(6, 'REST API',  'debutant',      0),
-- Offre 7: Stage PHP
(7, 'PHP',        'debutant',      1),
(7, 'HTML',       'debutant',      1),
(7, 'CSS',        'debutant',      1),
(7, 'MySQL',      'debutant',      0),
-- Offre 8: Chef de Projet
(8, 'Gestion de projet', 'expert',        1),
(8, 'Agile',             'intermediaire', 1),
(8, 'Jira',              'intermediaire', 0);

-- ── CANDIDATURES ────────────────────────────────────────────
INSERT INTO candidatures (id, user_id, offre_id, lettre_motivation, score_matching, competences_manquantes, statut, date_candidature) VALUES
(1, 4, 1, 'Je suis très motivé pour rejoindre TechCorp. Mon expérience en PHP/Laravel me permettra de contribuer rapidement à vos projets.', 78.50, '["REST API"]', 'shortlistee', '2026-05-10 09:30:00'),
(2, 4, 7, 'Je souhaite effectuer mon stage dans votre entreprise réputée.', 100.00, '[]', 'acceptee',    '2026-05-15 14:20:00'),
(3, 5, 3, 'Diplômée en Data Science, je suis passionnée par le machine learning et l analyse prédictive.', 91.67, '[]', 'vue',         '2026-05-12 11:00:00'),
(4, 6, 2, 'Fort de mon expérience DevOps, je suis prêt à optimiser votre infrastructure cloud.', 85.71, '["Terraform"]', 'shortlistee', '2026-05-08 16:45:00'),
(5, 7, 4, 'Passionnée de design UX/UI, j aimerais apporter ma créativité à DigitalHub.', 100.00, '[]', 'vue',         '2026-05-18 10:15:00'),
(6, 4, 5, 'Je souhaite élargir mes compétences full stack React/Node chez Innovate SA.', 33.33, '["React","Node.js"]', 'envoyee',     '2026-05-20 08:00:00'),
(7, 5, 5, 'Intéressée par le développement full stack, je souhaite élargir mes compétences backend chez Innovate SA.', 25.00, '["React","Node.js","JavaScript"]', 'envoyee', '2026-05-22 10:00:00');

-- ── ENTRETIENS ───────────────────────────────────────────────
INSERT INTO entretiens (id, candidature_id, date_entretien, type, lien_visio, notes, statut, score_simulation, feedback) VALUES
(1, 1, '2026-06-05 10:00:00', 'visio', 'https://meet.google.com/abc-defg-hij', 'Préparez un cas pratique Laravel. Apportez vos réalisations GitHub.', 'planifie', NULL, NULL),
(2, 3, '2026-06-08 14:30:00', 'visio', 'https://meet.google.com/xyz-uvwx-yz1', 'Test technique Python et ML à l avance. Préparez un projet end-to-end.', 'planifie', NULL, NULL),
(3, 4, '2026-06-03 09:00:00', 'presentiel', NULL, 'Entretien dans nos bureaux à Casablanca. Infrastructure review.', 'effectue', 74.00, 'Bonne maîtrise DevOps globale. Approfondissez Terraform et l IaC. Score solide pour un profil junior senior.'),
(4, 2, '2026-06-01 11:00:00', 'telephonique', NULL, 'Premier contact de cadrage pour le stage de développement PHP.', 'effectue', 85.00, 'Très bon contact téléphonique. Candidat motivé et curieux. Bases théoriques en PHP confirmées. Candidature acceptée pour le stage.'),
(5, 5, '2026-06-12 15:00:00', 'visio', 'https://meet.google.com/figma-uxui-review', 'Présentation du portfolio de design UI/UX. Préparez un cas d usage Figma.', 'planifie', NULL, NULL),
(6, 6, '2026-06-10 10:00:00', 'visio', 'https://meet.google.com/react-node-interview', 'Entretien technique Full Stack JS. Questions sur React hooks et Express middleware.', 'planifie', NULL, NULL),
(7, 7, '2026-06-04 16:00:00', 'visio', 'https://meet.google.com/innovatesa-hr-screening', 'Entretien de screening RH avec l équipe recrutement.', 'planifie', NULL, NULL);

-- ── FORMATIONS ──────────────────────────────────────────────
INSERT INTO formations (id, titre, description, url, plateforme, competences_cibles, duree, niveau, gratuit) VALUES
(1,  'PHP 8 & MVC Avancé',            'Maîtrisez PHP 8 et le pattern MVC professionnel',                      'https://udemy.com',          'Udemy',          '["PHP","MVC","PDO","OOP","Design Patterns"]',        '20h', 'intermediaire', 0),
(2,  'Laravel 11 Complet',             'Framework PHP le plus populaire, de zéro à expert',                   'https://udemy.com',          'Udemy',          '["Laravel","PHP","MySQL","REST API","Blade"]',        '35h', 'intermediaire', 0),
(3,  'React.js 2024',                  'Développez des SPA modernes avec React et Redux',                     'https://coursera.org',       'Coursera',       '["React","JavaScript","Redux","Hooks","API REST"]',   '30h', 'intermediaire', 0),
(4,  'Python & Machine Learning',      'IA et ML avec Python, scikit-learn et TensorFlow',                    'https://coursera.org',       'Coursera',       '["Python","Machine Learning","TensorFlow","Pandas"]', '40h', 'expert',        0),
(5,  'MySQL & BDD Relationnelles',     'Conception et optimisation de bases de données',                       'https://openclassrooms.com', 'OpenClassrooms', '["MySQL","SQL","Modélisation","Performances"]',       '15h', 'debutant',      1),
(6,  'Docker & Kubernetes',            'DevOps et conteneurisation moderne',                                   'https://udemy.com',          'Udemy',          '["Docker","Kubernetes","DevOps","Linux","CI/CD"]',    '25h', 'expert',        0),
(7,  'UI/UX Design avec Figma',        'Créez des interfaces modernes et accessibles',                        'https://youtube.com',        'YouTube',        '["Figma","UI/UX","Design","Prototypage","UX Research"]','12h','debutant',      1),
(8,  'Git & GitHub Pro',               'Contrôle de version et collaboration efficace',                       'https://youtube.com',        'YouTube',        '["Git","GitHub","CI/CD","Branching","Merge"]',        '8h',  'debutant',      1),
(9,  'Node.js & Express API',          'Backend JavaScript performant et API REST',                           'https://udemy.com',          'Udemy',          '["Node.js","Express","JavaScript","MongoDB","REST API"]','30h','intermediaire',0),
(10, 'Flutter & Dart',                 'Applications mobiles cross-platform iOS et Android',                  'https://udemy.com',          'Udemy',          '["Flutter","Dart","Mobile","iOS","Android","Firebase"]','28h','intermediaire', 0),
(11, 'AWS Cloud Practitioner',         'Les fondamentaux du cloud AWS certifié',                              'https://aws.amazon.com',     'AWS',            '["AWS","Cloud","S3","EC2","IAM","Lambda"]',           '20h', 'debutant',      0),
(12, 'Cybersécurité Fondamentaux',     'Sécurité réseaux, systèmes et bonnes pratiques',                      'https://coursera.org',       'Coursera',       '["Cybersécurité","Réseaux","Linux","Cryptographie"]', '45h', 'intermediaire', 0),
(13, 'JavaScript ES6+',                'Maîtrisez JavaScript moderne de A à Z',                               'https://youtube.com',        'YouTube',        '["JavaScript","ES6","DOM","Async","Fetch API"]',      '16h', 'debutant',      1),
(14, 'Power BI & Data Viz',            'Créez des tableaux de bord interactifs',                              'https://microsoft.com',      'Microsoft Learn','["Power BI","DAX","SQL","Data Viz","Excel"]',         '10h', 'debutant',      1),
(15, 'Terraform Infrastructure as Code','Automatisez votre infrastructure cloud',                             'https://udemy.com',          'Udemy',          '["Terraform","AWS","DevOps","CI/CD","Linux"]',        '18h', 'intermediaire', 0);

-- ── USER FORMATIONS ──────────────────────────────────────────
INSERT INTO user_formations (user_id, formation_id, statut, progression, date_debut) VALUES
(4, 5,  'termine',     100, '2026-03-01 10:00:00'),
(4, 8,  'termine',     100, '2026-03-15 09:00:00'),
(4, 13, 'en_cours',     65, '2026-04-01 10:00:00'),
(5, 4,  'en_cours',     80, '2026-04-10 14:00:00'),
(5, 14, 'non_commence',  0, NULL),
(6, 6,  'en_cours',     55, '2026-04-20 09:00:00'),
(6, 11, 'non_commence',  0, NULL),
(7, 7,  'termine',     100, '2026-03-10 11:00:00');

-- ── RECLAMATIONS ─────────────────────────────────────────────
INSERT INTO reclamations (id, user_id, titre, description, categorie, priorite, statut) VALUES
(1, 4, 'Impossible de télécharger mon CV PDF', 'Quand je clique sur Télécharger PDF, rien ne se passe. J ai essayé sur Chrome et Firefox.', 'Technique', 'haute',   'en_cours'),
(2, 5, 'Erreur lors de la candidature',        'J ai reçu une erreur 500 en postulant à l offre Data Scientist chez DigitalHub.', 'Candidature', 'urgente', 'resolu'),
(3, 7, 'Score de matching incorrect',          'Mon score de matching pour le poste UX/UI semble trop bas malgré mes compétences Figma.', 'Offre d emploi', 'normale', 'ouvert'),
(4, 6, 'Question sur le process d entretien',  'Comment se déroule la simulation d entretien ? Y a-t-il un chronomètre ?', 'Entretien', 'basse', 'resolu');

-- ── REPONSES RECLAMATIONS ────────────────────────────────────
INSERT INTO reponses (reclamation_id, auteur_id, message) VALUES
(1, 1, 'Bonjour Youssef, nous avons identifié le problème. Dompdf doit être installé via Composer. Nous mettons à jour le serveur. Merci de patienter 24h.'),
(2, 1, 'Bonjour Sara, le bug a été corrigé. Vous pouvez retenter votre candidature. Désolé pour le désagrément.'),
(2, 5, 'Merci pour la réponse rapide ! J ai pu postuler avec succès.'),
(4, 1, 'Bonjour Mehdi, oui la simulation a un chronomètre de 25 minutes. Vous pouvez naviguer entre les questions librement. Bon courage !'),
(4, 6, 'Parfait, merci pour l information !');

-- ── QUESTIONS ENTRETIEN ──────────────────────────────────────
INSERT INTO questions_entretien (metier_id, categorie, question, reponse_modele, difficulte) VALUES
(1,'PHP',      'Qu est-ce que le pattern MVC et pourquoi l utiliser ?',            'MVC sépare Model, View et Controller pour améliorer la maintenabilité.',            'moyen'),
(1,'PHP',      'Expliquez la différence entre PDO et MySQLi.',                      'PDO supporte plusieurs SGBD avec prepared statements. MySQLi est spécifique MySQL.', 'facile'),
(1,'PHP',      'Comment sécuriser contre les injections SQL ?',                    'Utiliser PDO avec requêtes préparées, jamais de concaténation directe.',             'moyen'),
(1,'Web',      'Expliquez GET et POST.',                                           'GET envoie dans l URL (lecture). POST dans le corps (écriture/modification).',        'facile'),
(2,'Python',   'Différence entre liste et tuple en Python ?',                      'Liste mutable, tuple immuable. Les tuples sont plus rapides.',                       'facile'),
(2,'ML',       'Expliquez l overfitting et comment l éviter.',                     'Mémorisation des données train. Éviter avec régularisation, dropout, cross-val.',    'moyen'),
(3,'Management','Comment gérez-vous les conflits dans une équipe ?',               'Identifier la source, faciliter la communication, chercher une solution win-win.',   'moyen'),
(4,'Design',   'Quelle est la différence entre UX et UI ?',                       'UX = expérience globale. UI = aspect visuel, couleurs, typographie.',                'facile'),
(5,'DevOps',   'Qu est-ce que Docker et pourquoi l utiliser ?',                   'Conteneurisation pour empaqueter une app avec ses dépendances de façon portable.',   'moyen'),
(5,'DevOps',   'Expliquez CI/CD.',                                                 'CI = tests automatiques à chaque commit. CD = déploiement automatique en prod.',     'moyen'),
(1,'PHP',      'Qu est-ce qu un trait en PHP et quand l utiliser ?',               'Un trait est un mécanisme de réutilisation de code en héritage simple. Utile pour partager des méthodes entre classes indépendantes.', 'moyen'),
(1,'Web',      'Qu est-ce que le protocole HTTP et la différence entre HTTP/1.1 et HTTP/2 ?', 'HTTP est le protocole de transfert hypertexte. HTTP/2 améliore les performances via le multiplexage et la compression des en-têtes.', 'moyen'),
(2,'Python',   'Qu est-ce qu un générateur en Python et quel est son intérêt ?',   'Un générateur produit des valeurs à la demande (yield). Il économise la mémoire pour les grands jeux de données.', 'moyen'),
(4,'Design',   'Qu est-ce que l accessibilité web (a11y) et comment la concevoir ?', 'Concevoir des interfaces utilisables par tous, y compris les personnes handicapées (contrastes, attributs alt, navigation clavier).', 'moyen'),
(5,'DevOps',   'Quelle est la différence entre Kubernetes et Docker Swarm ?',      'Docker Swarm est plus simple à configurer. Kubernetes est plus robuste, évolutif et offre plus de fonctionnalités pour les grands clusters.', 'difficile'),
(1,'Laravel',  'Expliquez le cycle de vie d une requête dans Laravel (Request Lifecycle).', 'La requête commence dans public/index.php, passe par le HTTP Kernel, les Service Providers, les Middlewares, le Routeur, puis le Contrôleur.', 'difficile');

-- ── RÉPONSES SIMULATION ──────────────────────────────────────
INSERT INTO reponses_simulation (entretien_id, question_id, reponse_candidat, score) VALUES
(3, 9, 'Docker permet de créer des conteneurs isolés qui regroupent l application et toutes ses dépendances. Cela évite les conflits de version de PHP ou de librairies entre le développement et la production. C est beaucoup plus léger qu une machine virtuelle car les conteneurs partagent le noyau du système hôte.', 8.50),
(3, 10, 'La CI/CD permet d automatiser le déploiement. L intégration continue lance automatiquement les tests unitaires et vérifie la qualité du code à chaque push sur Git. Le déploiement continu déploie l application sur les serveurs si tous les tests passent.', 6.30);

SELECT 'Seed data inserted successfully!' AS status;
