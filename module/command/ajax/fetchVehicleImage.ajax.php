<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

//On récupère l'Id du véhicule
$vehicleId = (int)$_POST['vehicleId'];
//Si ID inexistant
if(empty($vehicleId)){
    echo 'Erreur, Id non renseigné.';
    die();
}

$db = databaseConnection();
//On récupère d'abord le véhicule
$vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $vehicleId);
if(is_a($vehicle, '\Exception')){
    echo $vehicle->getMessage();
    die();
}
$db = null;

$finish = $vehicle->getFinish();
$finishName = $finish->getName();
$model = $finish->getModel();
$modelName = $model->getName();
$brand = $model->getBrand();
$brandName = $brand->getName();
$bodywork = $vehicle->getBodywork();
$bodyworkName = $bodywork->getName();
$doorsAmount = $vehicle->getDoorsAmount();

//On en déduit le chemin de l'image
$filePath = '/ressources/vehicleImages/'.$brandName.'/'.$modelName.'/'.$finishName.'/'.$bodyworkName.'_'.$doorsAmount.'.png';
if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$filePath)){
    echo '<img src="'.$filePath.'">';
}
else{
    echo 'Aucune image actuellement.';
    die();
}
die();