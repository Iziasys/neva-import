<?php
$pageTitle = 'Clients';
$moreJs = array('formManager');

if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    //Si on demande la création d'un pays
    if(!empty($_POST['createClient']))
        createClient($_POST['createClient']);

    //Si on demande la modification d'un pays
    else if(!empty($_POST['modifyClient']))
        modifyClient($_POST['modifyClient']);
}

/**
 * @param array $data
 *
 * @return bool
 * @throws Exception|\Users\IndividualClient|\Users\SocietyClient
 */
function createClient(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /**************************RECUPERATION DES DONNEES BRUTES************************/
        $ownerId = (int)$data['structureId'];
        $isSociety = (bool)$data['isSociety'];
        $societyName = (string)$data['societyName'];
        $siren = (string)$data['siren'];
        $siret = (string)$data['siret'];
        $civility = (string)$data['civility'];
        $lastName = (string)$data['lastName'];
        $firstName = (string)$data['firstName'];
        $phone = (string)$data['phone'];
        $mobile = (string)$data['mobile'];
        $fax = (string)$data['fax'];
        $email = (string)$data['email'];
        $acceptOffers = (bool)$data['acceptOffers'];
        $addressNumber = (int)$data['addressNumber'];
        $addressExtension = (string)$data['addressExtension'];
        $streetType = (string)$data['streetType'];
        $addressWording = (string)$data['addressWording'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        /**************************RECUPERATION DES DONNEES BRUTES************************/

        /**************************CHECK DE LA COHERENCE DES DONNEES************************/
        if(!isset($isSociety, $siren, $siret, $civility, $firstName, $phone, $mobile, $fax, $email, $acceptOffers,
                $addressNumber, $addressExtension, $streetType, $addressWording, $postalCode, $town)
            || ($isSociety && empty($societyName))
            || empty($lastName)
            || empty($ownerId)
        ){
            throw new Exception('Erreur, un ou plusieurs champs sont manquants.');
        }
        if(!verifyPureString($lastName)){
            throw new Exception('Erreur, le nom doit être valide');
        }
        /**************************CHECK DE LA COHERENCE DES DONNEES************************/

        //Arrivée ici, cohérence des données
        //On instancie un nouveau client selon le îsSociety
        if($isSociety){
            $client = new \Users\SocietyClient(0, $email, $lastName, $firstName, $civility, $phone, $mobile, $fax,
                                               $acceptOffers, $isSociety, $ownerId, $addressNumber, $addressExtension,
                                               $streetType, $addressWording, $postalCode, $town, $societyName, $siren, $siret);
        }
        else{
            $client = new \Users\IndividualClient(0, $email, $lastName, $firstName, $civility, $phone, $mobile, $fax,
                                               $acceptOffers, $isSociety, $ownerId, $addressNumber, $addressExtension,
                                               $streetType, $addressWording, $postalCode, $town);
        }

        //Puis on l'insère en base
        $insertClient = \Users\ClientManager::insertClient($db, $client);
        //Si erreur lors de l'insertion
        if(is_a($insertClient, '\Exception')){
            throw $insertClient;
        }

        //On valide le tout
        $db->commit();
        $db = null;
        //Et message de retour OK
        $_SESSION['returnAction'] = array(1, 'Création du client effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}


function modifyClient(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /**************************RECUPERATION DES DONNEES BRUTES************************/
        $clientId = (int)$data['clientId'];
        $ownerId = (int)$data['structureId'];
        $isSociety = (bool)$data['isSociety'];
        $societyName = (string)$data['societyName'];
        $siren = (string)$data['siren'];
        $siret = (string)$data['siret'];
        $civility = (string)$data['civility'];
        $lastName = (string)$data['lastName'];
        $firstName = (string)$data['firstName'];
        $phone = (string)$data['phone'];
        $mobile = (string)$data['mobile'];
        $fax = (string)$data['fax'];
        $email = (string)$data['email'];
        $acceptOffers = (bool)$data['acceptOffers'];
        $addressNumber = (int)$data['addressNumber'];
        $addressExtension = (string)$data['addressExtension'];
        $streetType = (string)$data['streetType'];
        $addressWording = (string)$data['addressWording'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        /**************************RECUPERATION DES DONNEES BRUTES************************/

        /**************************CHECK DE LA COHERENCE DES DONNEES************************/
        if(!isset($isSociety, $siren, $siret, $civility, $firstName, $phone, $mobile, $fax, $email, $acceptOffers,
                $addressNumber, $addressExtension, $streetType, $addressWording, $postalCode, $town)
            || ($isSociety && empty($societyName))
            || empty($lastName)
            || empty($ownerId)
            || empty($clientId)
        ){
            throw new Exception('Erreur, un ou plusieurs champs sont manquants.');
        }
        if(!verifyPureString($lastName)){
            throw new Exception('Erreur, le nom doit être valide');
        }
        $client = \Users\ClientManager::fetchClient($db, $clientId);
        if(is_a($client, '\Exception')){
            throw $client;
        }
        /**************************CHECK DE LA COHERENCE DES DONNEES************************/

        //Arrivée ici, cohérence des données
        //On update l'objet en local
        $client->setIsSociety($isSociety);
        if($isSociety){
            $client->setName($societyName);
            $client->setSiren($siren);
            $client->setSiret($siret);
        }
        $client->setCivility($civility);
        $client->setLastName($lastName);
        $client->setFirstName($firstName);
        $client->setPhone($phone);
        $client->setMobile($mobile);
        $client->setFax($fax);
        $client->setEmail($email);
        $client->setAcceptNewsLetter($acceptOffers);
        $client->setAddressNumber($addressNumber);
        $client->setAddressExtension($addressExtension);
        $client->setStreetType($streetType);
        $client->setAddressWording($addressWording);
        $client->setPostalCode($postalCode);
        $client->setTown($town);

        //Puis on hydrate la base
        $hydrateClient = \Users\ClientManager::hydrateClient($db, $client);
        if(is_a($hydrateClient, '\Exception')){
            throw $hydrateClient;
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Modification du client effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}