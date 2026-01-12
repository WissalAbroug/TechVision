<?php
// View/FrontOffice/CRUDcv.php
session_start();
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'saveCVStep':
            $step = isset($_POST['step']) ? intval($_POST['step']) : 1;
            $data = isset($_POST['data']) ? $_POST['data'] : '{}';
            
            // Vérifier si c'est du JSON
            $decodedData = json_decode($data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Si ce n'est pas du JSON, utiliser tel quel
                $decodedData = ['raw' => $data];
            }
            
            // Sauvegarder dans la session
            $_SESSION['cv_step_' . $step] = $decodedData;
            
            echo json_encode([
                'success' => true,
                'message' => 'Étape sauvegardée avec succès',
                'step' => $step
            ]);
            break;

        case 'generatePDF':
        case 'downloadCV':
            // Préparer la réponse pour le téléchargement
            $cvData = [];
            for ($i = 1; $i <= 5; $i++) {
                if (isset($_SESSION['cv_step_' . $i])) {
                    $cvData['step' . $i] = $_SESSION['cv_step_' . $i];
                }
            }
            
            // Options de format
            $format = $_POST['format'] ?? $_GET['format'] ?? 'pdf';
            $style = $_POST['style'] ?? $_GET['style'] ?? 'moderne';
            
            echo json_encode([
                'success' => true,
                'message' => 'CV prêt pour téléchargement',
                'download_url' => 'generate_cv.php?format=' . urlencode($format) . 
                                 '&style=' . urlencode($style) . 
                                 '&data=' . urlencode(json_encode($cvData)),
                'format' => $format
            ]);
            break;

        case 'getCVData':
            $cvData = [];
            for ($i = 1; $i <= 5; $i++) {
                if (isset($_SESSION['cv_step_' . $i])) {
                    $cvData['step' . $i] = $_SESSION['cv_step_' . $i];
                }
            }
            
            echo json_encode([
                'success' => true,
                'cvData' => $cvData
            ]);
            break;
        case 'getCVHistory':
            $history = isset($_SESSION['cv_history']) ? $_SESSION['cv_history'] : [];
            
            echo json_encode([
                'success' => true,
                'history' => $history
            ]);
            break;
        
            
        case 'deleteCVFromHistory':
            $cvId = $_POST['cv_id'] ?? '';
            
            if (isset($_SESSION['cv_history'])) {
                $_SESSION['cv_history'] = array_filter($_SESSION['cv_history'], function($cv) use ($cvId) {
                    return $cv['id'] !== $cvId;
                });
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'CV supprimé de l\'historique'
            ]);
            break;
            
        case 'downloadCVFromHistory':
            $cvId = $_POST['cv_id'] ?? '';
            
            if (isset($_SESSION['cv_history'])) {
                foreach ($_SESSION['cv_history'] as $cv) {
                    if ($cv['id'] === $cvId) {
                        // Générer le CV à partir des données sauvegardées
                        $cvData = $cv['data'];
                        $format = $cv['format'];
                        $style = $cv['style'];
                        $langue = $cv['langue'];
                        $nomFichier = $cv['nom'];
                        
                        // Appeler la fonction de génération appropriée
                        switch ($format) {
                            case 'pdf':
                                generateHTMLCV($cvData, $style, $langue, $nomFichier);
                                break;
                            case 'word':
                                generateWordCV($cvData, $style, $langue, $nomFichier);
                                break;
                            case 'texte':
                                generateTextCV($cvData, $langue, $nomFichier);
                                break;
                        }
                        exit;
                    }
                }
            }
            
            echo json_encode([
                'success' => false,
                'message' => 'CV non trouvé dans l\'historique'
            ]);
            break;

        default:
            echo json_encode([
                'success' => false, 
                'message' => 'Action non reconnue: ' . $action
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur interne: ' . $e->getMessage()
    ]);
}
?>