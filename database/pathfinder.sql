-- ============================================================
-- PathFinder AI — Schéma complet v2
-- Exécuter via : setup.php?key=pathfinder_setup_2024
-- ============================================================
CREATE DATABASE IF NOT EXISTS pathfinder_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pathfinder_ai;

SET FOREIGN_KEY_CHECKS = 0;

-- ── USERS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100)  NOT NULL,
    prenom      VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('admin','societe','candidat') NOT NULL DEFAULT 'candidat',
    statut      ENUM('actif','inactif','suspendu')  NOT NULL DEFAULT 'actif',
    nom_societe VARCHAR(200)  NULL,
    secteur     VARCHAR(150)  NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── PROFILS ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS profils (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT  NOT NULL UNIQUE,
    photo       VARCHAR(255) NULL,
    telephone   VARCHAR(20)  NULL,
    adresse     VARCHAR(255) NULL,
    ville       VARCHAR(100) NULL,
    pays        VARCHAR(100) DEFAULT 'Maroc',
    nationalite VARCHAR(100) NULL,
    date_naissance DATE NULL,
    permis      VARCHAR(100) NULL,
    titre       VARCHAR(200) NULL,
    bio         TEXT         NULL,
    linkedin    VARCHAR(255) NULL,
    github      VARCHAR(255) NULL,
    portfolio   VARCHAR(255) NULL,
    site_web    VARCHAR(255) NULL,
    competences JSON         NULL,
    score_profil INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── METIERS ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS metiers (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150) NOT NULL,
    categorie   VARCHAR(100) NOT NULL,
    description TEXT         NULL
) ENGINE=InnoDB;

-- ── CVS ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cvs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    user_id       INT          NOT NULL,
    titre         VARCHAR(200) NOT NULL,
    resume        TEXT         NULL,
    experiences   JSON         NULL,
    educations    JSON         NULL,
    competences   JSON         NULL,
    langues       JSON         NULL,
    soft_skills   JSON         NULL,
    projets       JSON         NULL,
    realisations  JSON         NULL,
    formations_complementaires JSON NULL,
    reseaux       JSON         NULL,
    centres_interet JSON       NULL,
    certifications JSON        NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── OFFRES ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS offres (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    societe_id          INT          NOT NULL,
    metier_id           INT          NULL,
    titre               VARCHAR(200) NOT NULL,
    description         TEXT         NOT NULL,
    localisation        VARCHAR(200) NULL,
    type_contrat        ENUM('CDI','CDD','Stage','Freelance','Alternance') NOT NULL,
    salaire             VARCHAR(100) NULL,
    experience_requise  VARCHAR(100) NULL,
    statut              ENUM('active','inactive','cloturee') NOT NULL DEFAULT 'active',
    date_expiration     DATE         NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (societe_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (metier_id)  REFERENCES metiers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── REQUIREMENTS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS requirements (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    offre_id    INT          NOT NULL,
    competence  VARCHAR(150) NOT NULL,
    niveau      ENUM('debutant','intermediaire','expert') NOT NULL DEFAULT 'intermediaire',
    obligatoire TINYINT(1)   DEFAULT 1,
    FOREIGN KEY (offre_id) REFERENCES offres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── CANDIDATURES ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS candidatures (
    id                      INT AUTO_INCREMENT PRIMARY KEY,
    user_id                 INT  NOT NULL,
    offre_id                INT  NOT NULL,
    cv_id                   INT  NULL,
    lettre_motivation       TEXT NULL,
    score_matching          DECIMAL(5,2) DEFAULT 0.00,
    competences_manquantes  JSON NULL,
    statut                  ENUM('envoyee','vue','shortlistee','refusee','acceptee') NOT NULL DEFAULT 'envoyee',
    date_candidature        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (offre_id) REFERENCES offres(id) ON DELETE CASCADE,
    FOREIGN KEY (cv_id)    REFERENCES cvs(id)    ON DELETE SET NULL,
    UNIQUE KEY uq_candidature (user_id, offre_id)
) ENGINE=InnoDB;

-- ── ENTRETIENS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS entretiens (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    candidature_id      INT          NOT NULL,
    date_entretien      DATETIME     NOT NULL,
    type                ENUM('visio','presentiel','telephonique') NOT NULL DEFAULT 'visio',
    lien_visio          VARCHAR(255) NULL,
    adresse_entretien   VARCHAR(255) NULL,
    notes               TEXT         NULL,
    statut              ENUM('planifie','confirme','annule','effectue') NOT NULL DEFAULT 'planifie',
    score_simulation    DECIMAL(5,2) NULL,
    feedback            TEXT         NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidature_id) REFERENCES candidatures(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── QUESTIONS ENTRETIEN ────────────────────────────────────
CREATE TABLE IF NOT EXISTS questions_entretien (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    metier_id       INT  NULL,
    categorie       VARCHAR(100) NULL,
    question        TEXT NOT NULL,
    reponse_modele  TEXT NULL,
    difficulte      ENUM('facile','moyen','difficile') DEFAULT 'moyen',
    FOREIGN KEY (metier_id) REFERENCES metiers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── RÉPONSES SIMULATION ────────────────────────────────────
CREATE TABLE IF NOT EXISTS reponses_simulation (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    entretien_id        INT  NOT NULL,
    question_id         INT  NOT NULL,
    reponse_candidat    TEXT NULL,
    score               DECIMAL(4,2) DEFAULT 0,
    FOREIGN KEY (entretien_id) REFERENCES entretiens(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id)  REFERENCES questions_entretien(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── RÉCLAMATIONS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reclamations (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    titre       VARCHAR(200) NOT NULL,
    description TEXT         NOT NULL,
    categorie   VARCHAR(100) NULL,
    priorite    ENUM('basse','normale','haute','urgente') DEFAULT 'normale',
    statut      ENUM('ouvert','en_cours','resolu','ferme') NOT NULL DEFAULT 'ouvert',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── RÉPONSES RÉCLAMATIONS ──────────────────────────────────
CREATE TABLE IF NOT EXISTS reponses (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    reclamation_id  INT  NOT NULL,
    auteur_id       INT  NOT NULL,
    message         TEXT NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reclamation_id) REFERENCES reclamations(id) ON DELETE CASCADE,
    FOREIGN KEY (auteur_id)      REFERENCES users(id)        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── FORMATIONS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS formations (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    titre               VARCHAR(200) NOT NULL,
    description         TEXT         NULL,
    url                 VARCHAR(500) NULL,
    plateforme          VARCHAR(100) NULL,
    competences_cibles  JSON         NULL,
    duree               VARCHAR(50)  NULL,
    niveau              ENUM('debutant','intermediaire','expert') DEFAULT 'intermediaire',
    gratuit             TINYINT(1)   DEFAULT 1,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── USER FORMATIONS ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS user_formations (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT  NOT NULL,
    formation_id    INT  NOT NULL,
    statut          ENUM('non_commence','en_cours','termine') DEFAULT 'non_commence',
    progression     INT  DEFAULT 0,
    date_debut      TIMESTAMP NULL,
    date_fin        TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)      REFERENCES users(id)     ON DELETE CASCADE,
    FOREIGN KEY (formation_id) REFERENCES formations(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_formation (user_id, formation_id)
) ENGINE=InnoDB;

-- ── FAVORIS ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS favoris (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    offre_id    INT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (offre_id) REFERENCES offres(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_offre_favoris (user_id, offre_id)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
