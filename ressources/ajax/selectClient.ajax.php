<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

if(!empty($_POST['clientId'])){
    $clientId = (int)$_POST['clientId'];

    $db = databaseConnection();
    $client = \Users\ClientManager::fetchClient($db, $clientId);
    $db = null;

    if(is_a($client, '\Exception')){
        $_SESSION['selectedClient'] = null;
        die();
    }

    $_SESSION['selectedClient'] = $client;

    echo 'ok';
}
else{
    die();
}