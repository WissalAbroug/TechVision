<?php
// test_connexion.php - Placez ce fichier à la racine de votre projet

require_once __DIR__ . '/config/Database.php';

echo "<h2>🔍 TEST DE CONNEXION ET STRUCTURE</h2>";

try {
    // Test 1: Connexion
    $pdo = Database::getInstance()->getConnection();
    echo "✅ <strong>Connexion à la base de données réussie !</strong><br><br>";

    // Test 2: Vérifier la table
    $stmt = $pdo->query("SHOW TABLES LIKE 'compte'");
    $table = $stmt->fetch();
    
    if ($table) {
        echo "✅ <strong>Table 'compte' existe</strong><br><br>";
        
        // Test 3: Structure de la table
        echo "<h3>📊 Structure de la table 'compte' :</h3>";
        $stmt = $pdo->query("DESCRIBE compte");
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #4CAF50; color: white;'>";
        echo "<th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th>";
        echo "</tr>";
        
        while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td><strong>{$col['Field']}</strong></td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
        // Test 4: Compter les enregistrements
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM compte");
        $count = $stmt->fetch();
        echo "📈 <strong>Nombre d'utilisateurs dans la base : {$count['total']}</strong><br><br>";
        
        // Test 5: Tester l'insertion
        echo "<h3>🧪 Test d'insertion :</h3>";
        
        $testEmail = 'test_' . time() . '@example.com';
        $testUser = 'TestUser' . time();
        $testPassword = password_hash('Test1234', PASSWORD_DEFAULT);
        
        try {
            $sql = "INSERT INTO compte (user, email, password) VALUES (:user, :email, :password)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'user' => $testUser,
                'email' => $testEmail,
                'password' => $testPassword
            ]);
            
            if ($result) {
                $lastId = $pdo->lastInsertId();
                echo "✅ <strong style='color: green;'>TEST RÉUSSI ! Utilisateur créé avec l'ID : {$lastId}</strong><br>";
                echo "📧 Email: {$testEmail}<br>";
                echo "👤 Nom: {$testUser}<br><br>";
                
                // Nettoyer le test
                
                echo "🧹 Utilisateur test supprimé<br>";
            } else {
                echo "❌ <strong style='color: red;'>ÉCHEC de l'insertion</strong><br>";
                print_r($stmt->errorInfo());
            }
        } catch (Exception $e) {
            echo "❌ <strong style='color: red;'>ERREUR : " . $e->getMessage() . "</strong><br>";
        }
        
    } else {
        echo "❌ <strong style='color: red;'>Table 'compte' n'existe pas !</strong><br>";
        echo "<h3>Créez la table avec cette requête :</h3>";
        echo "<pre style='background: #f4f4f4; padding: 15px;'>";
        echo "CREATE TABLE compte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);";
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong style='color: red;'>ERREUR de connexion : " . $e->getMessage() . "</strong><br>";
    echo "<br><strong>Vérifiez votre fichier config/Database.php</strong>";
}

echo "<hr>";
echo "<h3>📝 Prochaines étapes :</h3>";
echo "<ol>";
echo "<li>Si tous les tests sont ✅, le problème vient du formulaire ou du controller</li>";
echo "<li>Si un test est ❌, corrigez d'abord la base de données</li>";
echo "<li>Activez les erreurs PHP dans votre signup.php : <code>error_reporting(E_ALL); ini_set('display_errors', 1);</code></li>";
echo "</ol>";
?>