<?php
require_once __DIR__ . '/models/Model.php';

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';

$pdo = Database::getInstance();

try {
    // Table: quizzes
    $pdo->exec("CREATE TABLE IF NOT EXISTS quizzes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        type VARCHAR(50) NOT NULL, -- 'cv', 'metier', 'libre'
        niveau VARCHAR(50) DEFAULT 'Intermédiaire',
        cible VARCHAR(255) DEFAULT NULL, -- ex: 'PHP', 'React'
        user_id INT NULL, -- Null si générique, ou ID si généré pour un utilisateur spécifique
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Table: quiz_questions
    $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        quiz_id INT NOT NULL,
        question TEXT NOT NULL,
        options JSON NOT NULL, -- Array of 4 options
        correct_option INT NOT NULL, -- Index of correct option (0 to 3)
        explication TEXT NULL,
        FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Table: quiz_results
    $pdo->exec("CREATE TABLE IF NOT EXISTS quiz_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        quiz_id INT NOT NULL,
        score INT NOT NULL,
        total INT NOT NULL,
        recommandations JSON NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    echo "Tables quiz créées avec succès.\n";

} catch (PDOException $e) {
    echo "Erreur lors de la création des tables: " . $e->getMessage() . "\n";
}
