<?php
include '../../controller/DemandeController.php';

// Désactiver l'affichage des erreurs pour éviter les messages avant les toasts
error_reporting(0);

$demandeC = new DemandeController();
$demandeC->deleteDemande($_GET["id"]);
header('Location: demandeList.php');
exit;
