<?php
$pageTitle = 'Administration';
$moreJs = array('formManager');

if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    //Si on demande la création d'un concessionnaire
    if(!empty($_POST['createDealer'])){
        if($user->getType() === 1){
            $data = $_POST['createDealer'];

            createDealer($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour créer un concessionnaire.');
        }
    }

    //Si on demande la modification d'un concessionnaire
    else if(!empty($_POST['modifyDealer'])){
        if($user->getType() === 1){
            $data = $_POST['modifyDealer'];

            modifyDealer($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour modifier ce concessionnaire.');
        }
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function createDealer(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $name = (string)$data['name'];
        $countryId = (int)$data['country'];
        $addressNumber = (int)$data['addressNumber'];
        $addressExtension = (string)$data['addressExtension'];
        $streetType = (string)$data['streetType'];
        $wording = (string)$data['addressWording'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        $societyPhone = (string)$data['societyPhone'];
        $societyFax = (string)$data['societyFax'];
        $societyEmail = (string)$data['societyEmail'];
        $acceptOffers = (bool)$data['acceptOffers'];
        $civility = (string)$data['civility'];
        $lastName = (string)$data['lastName'];
        $firstName = (string)$data['firstName'];
        $phone = (string)$data['phone'];
        $mobile = (string)$data['mobile'];
        $email = (string)$data['email'];
        $comments = (string)$data['comments'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($countryId) || empty($name)){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPureString($name)){
            throw new Exception('Le nom du concessionnaire est mal formaté');
        }
        //On regarde aussi si le pays existe en base
        if(is_a(\Prices\CountryManager::fetchCountry($db, $countryId), '\Exception')){
            throw new Exception('Le pays choisi n\'existe pas...');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        //On créé l'objet en local
        $address = new \Users\Address($addressNumber, $addressExtension, $streetType, $wording, $postalCode, $town);
        $contact = new \Users\Person(0, $email, $lastName, $firstName, $civility, $phone, $mobile, '', $acceptOffers);
        $dealer = new \Prices\Dealer(0, $name, null, $countryId, $address, $societyPhone, $societyFax, $societyEmail,
                                     $contact, $comments, $acceptOffers);
        //Puis on l'insère en base
        $insertDealer = \Prices\DealerManager::insertDealer($db, $dealer);
        if(is_a($insertDealer, '\Exception')){
            throw new Exception($insertDealer->getMessage(), $insertDealer->getCode());
        }

        //On commit les changements
        $db->commit();
        $db = null;

        //Et message de succès
        $_SESSION['returnAction'] = array(1, 'Création du concessionnaire effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;

        return false;
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function modifyDealer(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $id = (int)$data['id'];
        $name = (string)$data['name'];
        $countryId = (int)$data['country'];
        $addressNumber = (int)$data['addressNumber'];
        $addressExtension = (string)$data['addressExtension'];
        $streetType = (string)$data['streetType'];
        $wording = (string)$data['addressWording'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        $societyPhone = (string)$data['societyPhone'];
        $societyFax = (string)$data['societyFax'];
        $societyEmail = (string)$data['societyEmail'];
        $acceptOffers = (bool)$data['acceptOffers'];
        $civility = (string)$data['civility'];
        $lastName = (string)$data['lastName'];
        $firstName = (string)$data['firstName'];
        $phone = (string)$data['phone'];
        $mobile = (string)$data['mobile'];
        $email = (string)$data['email'];
        $comments = (string)$data['comments'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($id) || empty($countryId) || empty($name)){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPureString($name)){
            throw new Exception('Le nom du concessionnaire est mal formaté');
        }
        $dealer = \Prices\DealerManager::fetchDealer($db, $id);
        if(is_a($dealer, '\Exception')){
            throw new Exception($dealer->getMessage(), $dealer->getCode());
        }
        //On regarde aussi si le pays existe en base
        if(is_a(\Prices\CountryManager::fetchCountry($db, $countryId), '\Exception')){
            throw new Exception('Le pays choisi n\'existe pas...');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //On modifie l'objet en local
        $dealer->setName($name);
        $dealer->setCountryId($countryId);
        $address = $dealer->getAddress();
        $address->setNumber($addressNumber);
        $address->setExtension($addressExtension);
        $address->setStreetType($streetType);
        $address->setWording($wording);
        $address->setPostalCode($postalCode);
        $address->setTown($town);
        $dealer->setAddress($address);
        $dealer->setPhone($societyPhone);
        $dealer->setFax($societyFax);
        $dealer->setEmail($societyEmail);
        $contact = $dealer->getContact();
        $contact->setCivility($civility);
        $contact->setLastName($lastName);
        $contact->setFirstName($firstName);
        $contact->setPhone($phone);
        $contact->setMobile($mobile);
        $contact->setEmail($email);
        $contact->setAcceptNewsLetter($acceptOffers);
        $dealer->setContact($contact);
        $dealer->setAcceptNewsLetter($acceptOffers);
        $dealer->setComments($comments);

        //Et on hydrate la base
        $hydrateDealer = \Prices\DealerManager::hydrateDealer($db, $dealer);
        if(is_a($hydrateDealer, '\Exception')){
            throw new Exception($hydrateDealer->getMessage(), $hydrateDealer->getCode());
        }

        //On commit les changements
        $db->commit();
        $db = null;

        //Et message de succès
        $_SESSION['returnAction'] = array(1, 'Modification du concessionnaire effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;

        return false;
    }
}