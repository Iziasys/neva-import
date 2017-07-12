<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}
$db = databaseConnection();
try{
    if(!isset($_POST['newAmount']) || ((float)$_POST['newAmount'] != $_POST['newAmount'])){
        throw new Exception('Montant inexistant ou mal renseigné');
    }
    $newAmount = (float)$_POST['newAmount'];

    $db->beginTransaction();

    //Insertion du montant en base
    $insertAmount = \Prices\HorsepowerPriceManager::insertFixAmount($db, $newAmount);
    if(is_a($insertAmount, '\Exception')){
        throw $insertAmount;
    }

    $db->commit();
    $db = null;

    echo 'ok';
}
catch(Exception $e){
    echo $e->getMessage();
}