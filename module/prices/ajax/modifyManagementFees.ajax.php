<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}
if($_SESSION['user']->getType() !== 1){
    echo 'error';
    die();
}

$vehicleId = (int)$_POST['vehicleId'];
$managementFees = (float)$_POST['managementFees'];

if(empty($vehicleId)){
    echo 'error';
    die();
}

$db = databaseConnection();
$vehicle = \Vehicle\DetailsManager::fetchDetails($db, $vehicleId);
if(is_a($vehicle, '\Exception')){
    echo 'error';
    $db = null;
    die();
}
//On récupère le prix
$price = \Prices\PriceManager::fetchPrice($db, $vehicle->getPriceId());

//On actualise les frais de gestion/commission
$price->setManagementFees((float)$managementFees);
//Puis on hydrate la base
$hydratePrice = \Prices\PriceManager::hydratePrice($db, $price);
if(is_a($hydratePrice, '\Exception')){
    echo 'error';
    die();
}

$db = null;