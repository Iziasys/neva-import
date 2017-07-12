<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

if(empty($_POST['vehicleId'])){
    echo 'error';
    die();
}

$vehicleId = (int)$_POST['vehicleId'];

$db = databaseConnection();
$vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
if(is_a($vehicle, '\Exception')){
    $db = null;
    echo 'error';
    die();
}

$vehicle->setReserved(false);
$vehicle->setReservedBy(0);
$hydrateVehicle = \Vehicle\VehicleInStockManager::hydrateVehicle($db, $vehicle);
if(is_a($hydrateVehicle, '\Exception')){
    $db = null;
    echo 'error';
    die();
}

$db = null;
echo 'success';