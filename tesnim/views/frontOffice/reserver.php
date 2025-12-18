<?php
// Définir le chemin de base pour les assets
$base_url = dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))) . '/';

include '../../controller/DemandeController.php';
require_once __DIR__ . '/../../model/DemandeEntretien.php';

$error = "";
$success = "";
$demandeC = new DemandeController();

if (
    isset($_POST["nom"]) && isset($_POST["tel"]) && 
    isset($_POST["email"]) && isset($_POST["entretien_id"])
) {
    if (
        !empty($_POST["nom"]) && !empty($_POST["tel"]) && 
        !empty($_POST["email"]) && !empty($_POST["entretien_id"])
    ) {
        // Validation côté serveur (toujours nécessaire pour la sécurité)
        $errors = [];
        
        // Validation email
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide";
        } 
        
        // Validation téléphone (8 chiffres minimum)
        if (!preg_match('/^[0-9]{8,}$/', $_POST["tel"])) {
            $errors[] = "Le numéro de téléphone doit contenir au moins 8 chiffres";
        }
        
        // Validation nom (minimum 3 caractères)
        if (strlen(trim($_POST["nom"])) < 3) {
            $errors[] = "Le nom doit contenir au moins 3 caractères";
        }
        
        if (empty($errors)) {
            $demande = new DemandeEntretien(
                null,
                $_POST['nom'],
                $_POST['tel'],
                $_POST['email'],
                (int)$_POST['entretien_id']
            );
            
            $result = $demandeC->addDemande($demande);
            
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        } else {
            $error = implode("<br>", $errors);
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Réservation</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assests/css/front.css">
    <style>
        .confirmation-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .icon-success {
            font-size: 64px;
            margin-bottom: 20px;
        }
        .icon-error {
            font-size: 64px;
            margin-bottom: 20px;
        }
        .btn-retour {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
        .btn-retour:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <?php if($success): ?>
            <div class="icon-success">✅</div>
            <h2>Réservation Confirmée !</h2>
            <p><?php echo htmlspecialchars($success); ?></p>
            <p>Vous recevrez un email de confirmation sous peu.</p>
        <?php else: ?>
            <div class="icon-error">❌</div>
            <h2>Erreur de Réservation</h2>
            <p class="alert-error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <a href="index.php" class="btn-retour">Retour à l'accueil</a>
    </div>
</body>
</html>