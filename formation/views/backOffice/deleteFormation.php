<?php
include '../../controller/FormationController.php';
$formationC = new FormationController();
$formationC->deleteFormation($_GET["id"]);
header('Location: formationList.php');
exit;
