<?php
// check-password.php
// Placez ce fichier à la racine et accédez-y pour vérifier le hash

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/Database.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification Password Hash</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1a1a1a;
            color: #0f0;
            padding: 40px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #2a2a2a;
            padding: 30px;
            border-radius: 10px;
            border: 2px solid #0f0;
        }
        h1 { color: #0ff; }
        .success { color: #0f0; }
        .error { color: #f00; }
        .warning { color: #ff0; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #444;
            padding: 10px;
            text-align: left;
        }
        th { background: #333; color: #0ff; }
        .hash {
            font-size: 10px;
            word-break: break-all;
        }
        button {
            background: #0f0;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
            margin: 5px;
        }
        button:hover { background: #0ff; }
        .test-section {
            background: #333;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Vérification des Mots de Passe</h1>

        <?php
        try {
            $pdo = Database::getInstance()->getConnection();
            
            // Récupérer tous les utilisateurs
            $stmt = $pdo->query("SELECT id, user, email, password, created_at FROM compte ORDER BY id DESC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h2 class='success'>✅ Connexion à la base réussie</h2>";
            echo "<p>Nombre d'utilisateurs: " . count($users) . "</p>";
            
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Password Hash</th><th>Type</th><th>Test</th></tr>";
            
            foreach ($users as $user) {
                $hash = $user['password'];
                $isHashed = (strlen($hash) > 50 && strpos($hash, '$2y$') === 0);
                
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . htmlspecialchars($user['user']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td class='hash'>" . htmlspecialchars(substr($hash, 0, 60)) . "...</td>";
                
                if ($isHashed) {
                    echo "<td class='success'>✅ HASHÉ</td>";
                    echo "<td>-</td>";
                } else {
                    echo "<td class='error'>❌ EN CLAIR</td>";
                    echo "<td><button onclick='fixPassword(" . $user['id'] . ", \"" . htmlspecialchars($user['email']) . "\")'>Corriger</button></td>";
                }
                
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Section de test de connexion
            echo "<div class='test-section'>";
            echo "<h2>🧪 Test de connexion manuelle</h2>";
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
                $testEmail = $_POST['test_email'];
                $testPassword = $_POST['test_password'];
                
                echo "<h3>Test pour: " . htmlspecialchars($testEmail) . "</h3>";
                
                // Récupérer l'utilisateur
                $stmt = $pdo->prepare("SELECT * FROM compte WHERE email = ?");
                $stmt->execute([$testEmail]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    echo "<p class='success'>✅ Utilisateur trouvé</p>";
                    echo "<p>Hash en BD: <span class='hash'>" . htmlspecialchars($user['password']) . "</span></p>";
                    
                    $match = password_verify($testPassword, $user['password']);
                    
                    if ($match) {
                        echo "<p class='success'>✅✅✅ PASSWORD MATCH - La connexion devrait fonctionner!</p>";
                    } else {
                        echo "<p class='error'>❌ PASSWORD NO MATCH - Le mot de passe est incorrect ou non hashé</p>";
                        
                        // Test si c'est en clair
                        if ($testPassword === $user['password']) {
                            echo "<p class='warning'>⚠️ Le mot de passe est stocké EN CLAIR dans la base!</p>";
                            echo "<p class='warning'>Il faut le hasher avec reset-my-password.php</p>";
                        }
                    }
                } else {
                    echo "<p class='error'>❌ Utilisateur non trouvé</p>";
                }
            }
            
            echo "<form method='POST'>";
            echo "<h4>Tester une connexion:</h4>";
            echo "<p><input type='email' name='test_email' placeholder='Email' value='mohamedkhattali123@gmail.com' style='width:100%;padding:10px;margin:5px 0;'></p>";
            echo "<p><input type='text' name='test_password' placeholder='Mot de passe' value='mohamed123' style='width:100%;padding:10px;margin:5px 0;'></p>";
            echo "<button type='submit' name='test_login'>Tester</button>";
            echo "</form>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<h2 class='error'>❌ Erreur</h2>";
            echo "<p>" . $e->getMessage() . "</p>";
        }
        ?>

        <h2>📋 Instructions</h2>
        <ol>
            <li>Si un mot de passe est "EN CLAIR", utilisez <code>reset-my-password.php</code></li>
            <li>Testez la connexion avec le formulaire ci-dessus</li>
            <li>Si "PASSWORD MATCH", le problème est ailleurs (redirection, session, etc.)</li>
            <li>Si "NO MATCH", le mot de passe doit être réinitialisé</li>
        </ol>
    </div>

    <script>
        function fixPassword(userId, email) {
            alert('Utilisez le fichier reset-my-password.php pour corriger le mot de passe de: ' + email);
        }
    </script>
</body>
</html>