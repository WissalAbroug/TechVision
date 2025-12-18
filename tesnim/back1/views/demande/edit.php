<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la demande - TalentMatch</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <header>
            <h1>
                <i class="fas fa-edit"></i> Modifier la demande
            </h1>
            <nav>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </nav>
        </header>

        <!-- Messages de succès/erreur -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'édition -->
        <div class="form-container">
            <form action="index.php?action=update" method="POST" class="form">
                <input type="hidden" name="id" value="<?= $record['id'] ?>">
                
                <div class="form-group">
                    <label for="nom">
                        <i class="fas fa-user"></i> Nom complet *
                    </label>
                    <input type="text" id="nom" name="nom" 
                           value="<?= htmlspecialchars($record['nom']) ?>" 
                           required placeholder="Ex: John Doe">
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email *
                    </label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($record['email']) ?>" 
                           required placeholder="exemple@email.com">
                </div>

                <div class="form-group">
                    <label for="telephone">
                        <i class="fas fa-phone"></i> Téléphone
                    </label>
                    <input type="tel" id="telephone" name="telephone" 
                           value="<?= htmlspecialchars($record['telephone']) ?>" 
                           placeholder="+33 1 23 45 67 89">
                </div>

                <div class="form-group">
                    <label for="statut">
                        <i class="fas fa-tag"></i> Statut *
                    </label>
                    <select id="statut" name="statut" required>
                        <option value="en attente" <?= $record['statut'] == 'en attente' ? 'selected' : '' ?>>
                            En attente
                        </option>
                        <option value="actif" <?= $record['statut'] == 'actif' ? 'selected' : '' ?>>
                            Actif
                        </option>
                        <option value="validé" <?= $record['statut'] == 'validé' ? 'selected' : '' ?>>
                            Validé
                        </option>
                        <option value="annulé" <?= $record['statut'] == 'annulé' ? 'selected' : '' ?>>
                            Annulé
                        </option>
                        <option value="inactif" <?= $record['statut'] == 'inactif' ? 'selected' : '' ?>>
                            Inactif
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <small class="form-text">* Champs obligatoires</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!nom) {
                e.preventDefault();
                alert('Le nom est obligatoire');
                return false;
            }
            
            if (!email) {
                e.preventDefault();
                alert('L\'email est obligatoire');
                return false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Veuillez saisir un email valide');
                return false;
            }
        });
    </script>
</body>
</html>