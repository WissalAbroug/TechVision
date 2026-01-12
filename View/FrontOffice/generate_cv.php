<?php
// View/FrontOffice/generate_cv.php
session_start();

// FONCTION POUR LIRE L'HISTORIQUE
function getCVHistory() {
    if (!isset($_SESSION['cv_history']) || empty($_SESSION['cv_history'])) {
        return [];
    }
    
    // Trier par date (plus récent d'abord)
    $history = $_SESSION['cv_history'];
    usort($history, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $history;
}
// Récupérer les données
$cvData = $_GET['data'] ?? $_POST['data'] ?? '';
if (isset($_SESSION['cv_full_data'])) {
    $cvData = json_encode($_SESSION['cv_full_data']);
}

if (empty($cvData)) {
    // Essayer de reconstruire depuis les étapes
    $cvData = [];
    for ($i = 1; $i <= 5; $i++) {
        if (isset($_SESSION['cv_step_' . $i])) {
            $cvData['step' . $i] = $_SESSION['cv_step_' . $i];
        }
    }
    $cvData = json_encode($cvData);
} else {
    $cvData = urldecode($cvData);
}

$cvData = json_decode($cvData, true);
if (!$cvData) {
    die('Erreur: Données CV invalides ou manquantes');
}

$format = $_GET['format'] ?? $_POST['format'] ?? 'pdf';
$style = $_GET['style'] ?? $_POST['style'] ?? 'moderne';
$langue = $_GET['langue'] ?? $_POST['langue'] ?? 'fr';

// Nettoyer le nom pour le fichier
$nom = isset($cvData['step1']['nom_complet']) ? $cvData['step1']['nom_complet'] : 'utilisateur';
$nomFichier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $nom);

// ============================================
// AJOUT: SAUVEGARDER DANS L'HISTORIQUE
// ============================================
if (!isset($_SESSION['cv_history'])) {
    $_SESSION['cv_history'] = [];
}

$cvHistoryItem = [
    'id' => uniqid('cv_'),
    'nom' => $nomFichier,
    'date' => date('Y-m-d H:i:s'),
    'format' => $format,
    'style' => $style,
    'langue' => $langue,
    'data' => $cvData
];

// Limiter l'historique à 10 CV maximum
$_SESSION['cv_history'] = array_slice($_SESSION['cv_history'], -9);
array_push($_SESSION['cv_history'], $cvHistoryItem);
// Définir les noms de fichiers pour toutes les fonctions
$htmlFilename = 'CV_' . $nomFichier . '_' . date('Ymd') . '.html';
$wordFilename = 'CV_' . $nomFichier . '_' . date('Ymd') . '.doc';
$textFilename = 'CV_' . $nomFichier . '_' . date('Ymd') . '.txt';

switch ($format) {
    case 'pdf':
        generateHTMLCV($cvData, $style, $langue, $htmlFilename);
        break;
    case 'word':
        generateWordCV($cvData, $style, $langue, $wordFilename);
        break;
    case 'texte':
        generateTextCV($cvData, $langue, $textFilename);
        break;
    default:
        generateHTMLCV($cvData, $style, $langue, $htmlFilename);
}

function generateHTMLCV($cvData, $style, $langue, $filename) {
    
    // Téléchargement direct
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>CV - ' . htmlspecialchars($cvData['step1']['nom_complet'] ?? '') . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
            .header { text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; }
            h1 { color: #2c3e50; margin-bottom: 5px; }
            .contact-info { color: #666; margin-bottom: 20px; }
            .section { margin-bottom: 25px; }
            .section-title { color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 15px; }
            .experience-item, .formation-item { margin-bottom: 15px; }
            .date { color: #666; font-style: italic; }
            ul { padding-left: 20px; }
            .skills span { display: inline-block; background: #f0f0f0; padding: 5px 10px; margin: 2px; border-radius: 3px; }
        </style>
    </head>
    <body>';
    
    // En-tête
    echo '<div class="header">';
    echo '<h1>' . htmlspecialchars($cvData['step1']['nom_complet'] ?? 'Nom Complet') . '</h1>';
    echo '<h2>' . htmlspecialchars($cvData['step1']['poste_cible'] ?? 'Poste cible') . '</h2>';
    
    $contact = [];
    if (!empty($cvData['step1']['email'])) $contact[] = htmlspecialchars($cvData['step1']['email']);
    if (!empty($cvData['step1']['telephone'])) $contact[] = htmlspecialchars($cvData['step1']['telephone']);
    if (!empty($cvData['step1']['linkedin'])) $contact[] = htmlspecialchars($cvData['step1']['linkedin']);
    
    echo '<div class="contact-info">' . implode(' | ', $contact) . '</div>';
    echo '</div>';
    
    // Profil
    if (!empty($cvData['step2']['profil'])) {
        echo '<div class="section">';
        echo '<h3 class="section-title">Profil Professionnel</h3>';
        echo '<p>' . nl2br(htmlspecialchars($cvData['step2']['profil'])) . '</p>';
        echo '</div>';
    }
    
    // Expériences
    if (!empty($cvData['step3']['experiences']) && is_array($cvData['step3']['experiences'])) {
        echo '<div class="section">';
        echo '<h3 class="section-title">Expériences Professionnelles</h3>';
        
        foreach ($cvData['step3']['experiences'] as $exp) {
            if (!empty($exp['poste'])) {
                echo '<div class="experience-item">';
                echo '<h4>' . htmlspecialchars($exp['poste']) . '</h4>';
                echo '<p><strong>' . htmlspecialchars($exp['entreprise'] ?? '') . '</strong>';
                if (!empty($exp['ville'])) echo ' - ' . htmlspecialchars($exp['ville']);
                echo '</p>';
                
                if (!empty($exp['date_debut'])) {
                    echo '<p class="date">' . htmlspecialchars($exp['date_debut']);
                    echo ' - ' . (!empty($exp['date_fin']) ? htmlspecialchars($exp['date_fin']) : 'Présent') . '</p>';
                }
                
                if (!empty($exp['missions'])) {
                    echo '<p>' . nl2br(htmlspecialchars($exp['missions'])) . '</p>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
    
    // Compétences
    echo '<div class="section">';
    echo '<h3 class="section-title">Compétences</h3>';
    
    if (!empty($cvData['step2']['competences_tech']) && is_array($cvData['step2']['competences_tech'])) {
        echo '<p><strong>Techniques:</strong><br>';
        echo '<div class="skills">';
        foreach ($cvData['step2']['competences_tech'] as $skill) {
            echo '<span>' . htmlspecialchars($skill) . '</span> ';
        }
        echo '</div></p>';
    }
    
    if (!empty($cvData['step2']['competences_soft']) && is_array($cvData['step2']['competences_soft'])) {
        echo '<p><strong>Personnelles:</strong><br>';
        echo '<div class="skills">';
        foreach ($cvData['step2']['competences_soft'] as $skill) {
            echo '<span>' . htmlspecialchars($skill) . '</span> ';
        }
        echo '</div></p>';
    }
    echo '</div>';
    
    // Formations
    if (!empty($cvData['step4']['formations']) && is_array($cvData['step4']['formations'])) {
        echo '<div class="section">';
        echo '<h3 class="section-title">Formations</h3>';
        
        foreach ($cvData['step4']['formations'] as $formation) {
            if (!empty($formation['diplome'])) {
                echo '<div class="formation-item">';
                echo '<h4>' . htmlspecialchars($formation['diplome']) . '</h4>';
                echo '<p><strong>' . htmlspecialchars($formation['etablissement'] ?? '') . '</strong>';
                if (!empty($formation['ville'])) echo ' - ' . htmlspecialchars($formation['ville']);
                echo '</p>';
                if (!empty($formation['annee'])) {
                    echo '<p class="date">Année: ' . htmlspecialchars($formation['annee']) . '</p>';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
    
    echo '</body></html>';
}

function generateWordCV($cvData, $style, $langue, $filename) {
    
    header('Content-Type: application/msword');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:w="urn:schemas-microsoft-com:office:word"
          xmlns="http://www.w3.org/TR/REC-html40">
          <head>
          <meta charset="UTF-8">
          <title>CV</title>
          <style>
              body { font-family: Arial, sans-serif; margin: 2cm; }
              h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
              h2 { color: #34495e; margin-top: 20px; }
              .section { margin-bottom: 20px; }
          </style>
          </head><body>';
    
    echo '<h1>' . htmlspecialchars($cvData['step1']['nom_complet'] ?? '') . '</h1>';
    echo '<h2>' . htmlspecialchars($cvData['step1']['poste_cible'] ?? '') . '</h2>';
    
    $contact = [];
    if (!empty($cvData['step1']['email'])) $contact[] = htmlspecialchars($cvData['step1']['email']);
    if (!empty($cvData['step1']['telephone'])) $contact[] = htmlspecialchars($cvData['step1']['telephone']);
    if (!empty($cvData['step1']['linkedin'])) $contact[] = htmlspecialchars($cvData['step1']['linkedin']);
    
    echo '<p>' . implode(' | ', $contact) . '</p>';
    echo '<hr>';
    
    if (!empty($cvData['step2']['profil'])) {
        echo '<h2>Profil</h2>';
        echo '<p>' . nl2br(htmlspecialchars($cvData['step2']['profil'])) . '</p>';
    }
    
    if (!empty($cvData['step3']['experiences']) && is_array($cvData['step3']['experiences'])) {
        echo '<h2>Expériences Professionnelles</h2>';
        foreach ($cvData['step3']['experiences'] as $exp) {
            if (!empty($exp['poste'])) {
                echo '<h3>' . htmlspecialchars($exp['poste']) . '</h3>';
                echo '<p><strong>' . htmlspecialchars($exp['entreprise'] ?? '') . '</strong>';
                if (!empty($exp['ville'])) echo ' - ' . htmlspecialchars($exp['ville']);
                echo '</p>';
                
                if (!empty($exp['date_debut'])) {
                    echo '<p><em>' . htmlspecialchars($exp['date_debut']);
                    echo ' - ' . (!empty($exp['date_fin']) ? htmlspecialchars($exp['date_fin']) : 'Présent') . '</em></p>';
                }
                
                if (!empty($exp['missions'])) {
                    echo '<p>' . nl2br(htmlspecialchars($exp['missions'])) . '</p>';
                }
                echo '<br>';
            }
        }
    }
    
    echo '<h2>Compétences</h2>';
    if (!empty($cvData['step2']['competences_tech']) && is_array($cvData['step2']['competences_tech'])) {
        echo '<p><strong>Techniques:</strong> ' . htmlspecialchars(implode(', ', $cvData['step2']['competences_tech'])) . '</p>';
    }
    if (!empty($cvData['step2']['competences_soft']) && is_array($cvData['step2']['competences_soft'])) {
        echo '<p><strong>Personnelles:</strong> ' . htmlspecialchars(implode(', ', $cvData['step2']['competences_soft'])) . '</p>';
    }
    
    echo '</body></html>';
}

function generateTextCV($cvData, $langue, $filename) {
    
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo "========================================\n";
    echo strtoupper($cvData['step1']['nom_complet'] ?? 'NOM COMPLET') . "\n";
    echo "========================================\n\n";
    
    echo "Poste cible: " . ($cvData['step1']['poste_cible'] ?? '') . "\n\n";
    
    echo "INFORMATIONS DE CONTACT\n";
    echo "=======================\n";
    if (!empty($cvData['step1']['email'])) echo "Email: " . $cvData['step1']['email'] . "\n";
    if (!empty($cvData['step1']['telephone'])) echo "Téléphone: " . $cvData['step1']['telephone'] . "\n";
    if (!empty($cvData['step1']['linkedin'])) echo "LinkedIn: " . $cvData['step1']['linkedin'] . "\n";
    echo "\n";
    
    if (!empty($cvData['step2']['profil'])) {
        echo "PROFIL\n";
        echo "======\n";
        echo wordwrap($cvData['step2']['profil'], 80) . "\n\n";
    }
    
    if (!empty($cvData['step3']['experiences']) && is_array($cvData['step3']['experiences'])) {
        echo "EXPÉRIENCES PROFESSIONNELLES\n";
        echo "============================\n";
        foreach ($cvData['step3']['experiences'] as $exp) {
            if (!empty($exp['poste'])) {
                echo "- " . $exp['poste'] . "\n";
                echo "  " . ($exp['entreprise'] ?? '');
                if (!empty($exp['ville'])) echo " - " . $exp['ville'];
                echo "\n";
                
                if (!empty($exp['date_debut'])) {
                    echo "  " . $exp['date_debut'] . ' - ' . (!empty($exp['date_fin']) ? $exp['date_fin'] : 'Présent') . "\n";
                }
                
                if (!empty($exp['missions'])) {
                    echo "  " . wordwrap($exp['missions'], 78, "\n  ") . "\n";
                }
                echo "\n";
            }
        }
    }
    
    echo "COMPÉTENCES\n";
    echo "===========\n";
    if (!empty($cvData['step2']['competences_tech']) && is_array($cvData['step2']['competences_tech'])) {
        echo "Techniques: " . implode(', ', $cvData['step2']['competences_tech']) . "\n";
    }
    if (!empty($cvData['step2']['competences_soft']) && is_array($cvData['step2']['competences_soft'])) {
        echo "Personnelles: " . implode(', ', $cvData['step2']['competences_soft']) . "\n";
    }
    echo "\n";
    
    if (!empty($cvData['step4']['formations']) && is_array($cvData['step4']['formations'])) {
        echo "FORMATIONS\n";
        echo "==========\n";
        foreach ($cvData['step4']['formations'] as $formation) {
            if (!empty($formation['diplome'])) {
                echo "- " . $formation['diplome'] . "\n";
                echo "  " . ($formation['etablissement'] ?? '');
                if (!empty($formation['ville'])) echo " - " . $formation['ville'];
                echo "\n";
                if (!empty($formation['annee'])) {
                    echo "  Année: " . $formation['annee'] . "\n";
                }
                echo "\n";
            }
        }
    }
}
?>