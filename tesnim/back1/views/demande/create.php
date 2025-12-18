<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Créer une nouvelle demande d'entretien">
    <title>TalentMatch - Nouvelle Demande</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>TALENTMATCH</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="nav-link">📊 Dashboard</a></li>
            <li><a href="index.php" class="nav-link">📋 Demandes</a></li>
            <li><a href="#" class="nav-link active">➕ Nouvelle Demande</a></li>
            <li><a href="#" class="nav-link">👥 Candidats</a></li>
            <li><a href="#" class="nav-link">📅 Calendrier</a></li>
            <li><a href="#" class="nav-link">⚙️ Paramètres</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <h1>Nouvelle Demande d'Entretien</h1>
            <div class="user-info">
                <div class="user-avatar">AD</div>
            </div>
        </header>

        <!-- Content Section -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">➕ Ajouter un enregistrement</h2>
                <a href="index.php" class="btn btn-secondary btn-small">
                    <span>←</span> Retour à la liste
                </a>
            </div>

            <!-- Messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error show">
                    <strong>⚠️ Erreur</strong>
                    <?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire de création -->
            <form method="POST" action="?action=create" style="max-width: 600px;">
                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input 
                        type="text" 
                        id="nom" 
                        name="nom" 
                        placeholder="Ex: Ahmed Ben Ali"
                        required
                        value="<?php echo isset($_GET['nom']) ? htmlspecialchars($_GET['nom'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="email">Adresse email *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Ex: ahmed.benali@email.tn"
                        required
                        value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="telephone">Numéro de téléphone</label>
                    <input 
                        type="text" 
                        id="telephone" 
                        name="telephone" 
                        placeholder="Ex: +216 98 123 456"
                        value="<?php echo isset($_GET['telephone']) ? htmlspecialchars($_GET['telephone'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="statut">Statut de la demande</label>
                    <select id="statut" name="statut">
                        <option value="en attente" selected>En attente</option>
                        <option value="actif">Actif</option>
                        <option value="inactive">Inactif</option>
                    </select>
                </div>

                <div class="form-group">
                    <small style="color: var(--text-light); opacity: 0.7;">
                        * Champs obligatoires
                    </small>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">💾 Créer la demande</button>
                </div>
            </form>

            <!-- Informations complémentaires -->
            <div style="margin-top: 40px; padding: 20px; background: rgba(255, 165, 0, 0.1); border-left: 4px solid var(--accent-orange); border-radius: 8px;">
                <h3 style="color: var(--accent-orange); font-size: 16px; margin-bottom: 10px;">
                    💡 Informations
                </h3>
                <ul style="margin-left: 20px; color: var(--text-light); opacity: 0.8; line-height: 1.8;">
                    <li>La date de création sera automatiquement ajoutée</li>
                    <li>L'email doit être unique et valide</li>
                    <li>Le statut "En attente" est recommandé pour les nouvelles demandes</li>
                    <li>Vous pourrez modifier ces informations ultérieurement</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus sur le premier champ
        document.getElementById('nom')?.focus();

        // Validation du formulaire côté client
        document.querySelector('form')?.addEventListener('submit', function(e) {
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();

            if (!nom || !email) {
                e.preventDefault();
                alert('⚠️ Le nom et l\'email sont obligatoires');
                return false;
            }

            // Validation email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('⚠️ L\'email n\'est pas valide');
                return false;
            }
        });
    </script>
</body>
</html>