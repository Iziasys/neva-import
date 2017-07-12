<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

if(empty($_POST['vehicleId']) || !isset($_POST['margin'])){
    echo 'error';
    die();
}

$vehicleId = (int)$_POST['vehicleId'];
$margin = (float)$_POST['margin'];
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();

$db = databaseConnection();
$defineMargin = \Vehicle\VehicleInStockManager::defineStructureMargin($db, $vehicleId, $structure->getId(), $margin);
if(is_a($defineMargin, '\Exception')){
    echo 'error';
    die();
}
$db = null;
echo 'success';