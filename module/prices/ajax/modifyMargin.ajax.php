<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}
if($_SESSION['user']->getType() !== 1){
    echo 'error';
    die();
}

$vehicleId = (int)$_POST['vehicleId'];
$margin = (float)$_POST['margin'];
$type = (string)$_POST['modifyType'];

if(empty($vehicleId) || !isset($margin) || empty($type)){
    echo 'error';
    die();
}

$db = databaseConnection();
//On récupère le véhicule concerné
$vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $vehicleId);
//Si il y a une erreur pour ce véhicule, on sort
if(is_a($vehicle, '\Exception')){
    echo 'error';
    $db = null;
    die();
}

//Selon la modification qu'on souhaite faire
switch($type){
    //Si on souhaite modifier uniquement ce véhicule
    case 'vehicle' :
        //On récupère le prix
        $price = $vehicle->getPrice();
        //On actualise la marge
        $price->setMargin($margin);
        //Puis on hydrate la base
        $hydratePrice = \Prices\PriceManager::hydratePrice($db, $price);
        if(is_a($hydratePrice, '\Exception')){
            echo 'error';
            $db = null;
            die();
        }
        break;
    //Si on souhaite modifier tous les prix pour cette finition
    case 'finish' :
        //On récupère tous les véhicules associés à cette finition
        $finishId = $vehicle->getFinishId();
        $vehicles = \Vehicle\DetailsManager::fetchVehicleListFromFinish($db, $finishId);
        if(is_a($vehicles, '\Exception')){
            echo 'error';
            $db = null;
            die();
        }
        //Pour chaque véhicule de cette finition
        foreach($vehicles as $vehicle){
            //On récupère le prix
            $price = $vehicle->getPrice();
            //On actualise la marge
            $price->setMargin($margin);
            //Puis on hydrate la base
            $hydratePrice = \Prices\PriceManager::hydratePrice($db, $price);
            if(is_a($hydratePrice, '\Exception')){
                echo 'error';
                $db = null;
                die();
            }
        }
        break;
    //Si on souhaite modifier tous les prix pour ce modèle
    case 'model' :
        //On récupère tous les véhicules associés à ce modèle
        $modelId = $vehicle->getFinish()->getModelId();
        $vehicles = \Vehicle\DetailsManager::fetchVehicleListFromModel($db, $modelId);
        if(is_a($vehicles, '\Exception')){
            echo 'error';
            $db = null;
            die();
        }
        //Pour chaque véhicule de ce modèle
        foreach($vehicles as $vehicle){
            //On récupère le prix
            $price = $vehicle->getPrice();
            //On actualise la marge
            $price->setMargin($margin);
            //Puis on hydrate la base
            $hydratePrice = \Prices\PriceManager::hydratePrice($db, $price);
            if(is_a($hydratePrice, '\Exception')){
                echo 'error';
                $db = null;
                die();
            }
        }
        break;
    default :
        echo 'error';
        $db = null;
        die();
        break;
}

$db = null;
die();