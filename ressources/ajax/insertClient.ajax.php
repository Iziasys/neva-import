<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

if(!empty($_POST)){
    if(($_POST['isSociety'] && empty($_POST['societyName'])) || empty($_POST['civility']) || empty($_POST['lastName'])
        || !isset($_POST['isSociety'], $_POST['firstName'])
    ){
        die();
    }

    //Parsage des données
    $isSociety = (bool)$_POST['isSociety'];
    $societyName = (string)$_POST['societyName'];
    $civility = (string)$_POST['civility'];
    $lastName = (string)$_POST['lastName'];
    $firstName = (string)$_POST['firstName'];
    $email = (string)$_POST['email'];
    $acceptOffers = empty($_POST['acceptOffers']) ? false : (bool)$_POST['acceptOffers'];

    //Création du client
    if($isSociety){
        $client = new \Users\SocietyClient();
        $client->setName($societyName);
    }
    else{
        $client = new \Users\IndividualClient();
    }

    $structureId = $_SESSION['user']->getStructureId();

    $client->setCivility($civility);
    $client->setFirstName($firstName);
    $client->setLastName($lastName);
    $client->setEmail($email);
    $client->setAcceptNewsLetter($acceptOffers);
    $client->setOwnerId($structureId);

    //Puis insertion en base
    $db = databaseConnection();
    $insertClient = \Users\ClientManager::insertClient($db, $client);
    $db = null;
    //Si erreur :
    if(is_a($insertClient, '\Exception')){
        die();
    }

    //Puis stockage dans la variable de session
    $_SESSION['selectedClient'] = $insertClient;

    //Validation de l'appel
    echo 'ok';
}
else{
    die();
}