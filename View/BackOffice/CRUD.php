<?php
// View/BackOffice/CRUD.php
require_once __DIR__ . '/../../Controller/OfferController.php';

header('Content-Type: application/json');

$offerController = new OfferController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getOffer':
        $id = $_GET['id'] ?? 0;
        $offer = $offerController->getOfferById($id);
        
        if ($offer) {
            echo json_encode([
                'success' => true,
                'offer' => $offer->toArray()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Offre non trouvée']);
        }
        break;

    case 'create':
        $offer = new Offer(
            null,
            $_POST['title'],
            $_POST['company'],
            $_POST['location'],
            $_POST['salary_min'],
            $_POST['salary_max'],
            $_POST['description'],
            $_POST['job_type'],
            $_POST['experience_level'],
            $_POST['skills_required'],
            $_POST['requirements'],
            'active'
        );
        
        if ($offerController->createOffer($offer)) {
            echo json_encode(['success' => true, 'message' => 'Offre créée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur création']);
        }
        break;

    case 'update':
    // Récupérer l'ID depuis POST
    $id = $_POST['id'] ?? 0;
    $offer = $offerController->getOfferById($id);
    
    if ($offer) {
        // Mettre à jour les propriétés
        $offer->setTitle($_POST['title'] ?? $_POST['titre'] ?? '');
        $offer->setCompany($_POST['company'] ?? $_POST['nomSociete'] ?? '');
        $offer->setLocation($_POST['location'] ?? $_POST['localisation'] ?? '');
        $offer->setSalaryMin($_POST['salary_min'] ?? $_POST['salaireMin'] ?? 0);
        $offer->setSalaryMax($_POST['salary_max'] ?? $_POST['salaireMax'] ?? 0);
        $offer->setDescription($_POST['description'] ?? '');
        $offer->setJobType($_POST['job_type'] ?? $_POST['typeContrat'] ?? 'CDI');
        $offer->setExperienceLevel($_POST['experience_level'] ?? $_POST['experienceRequise'] ?? '');
        
        // Gérer les compétences/requirements
        $skills = !empty($_POST['skills_required']) ? $_POST['skills_required'] : 
                 (!empty($_POST['competences']) ? $_POST['competences'] : '[]');
        $requirements = $_POST['requirements'] ?? '[]';
        
        $offer->setSkillsRequired($skills);
        $offer->setRequirements($requirements);
        $offer->setStatus($_POST['status'] ?? $_POST['statut'] ?? 'active');
        
        if ($offerController->updateOffer($offer)) {
            echo json_encode(['success' => true, 'message' => 'Offre mise à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur mise à jour']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Offre non trouvée']);
    }
    break;

    case 'delete':
        if ($offerController->deleteOffer($_POST['id'])) {
            echo json_encode(['success' => true, 'message' => 'Offre supprimée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur suppression']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        break;
}
?>