<?php
$pageTitle = 'Gestion des structures';
$moreJs = array('formManager');


if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    //Si on demande la création d'une structure
    if(!empty($_POST['createStructure'])){
        //Si on a les droits de le faire
        if($rights->getCreateStructure()){
            $data = $_POST['createStructure'];

            createStructure($data, $_FILES);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour créer une structure.');
        }
    }

    //Si on demande la modification d'une structure
    else if(!empty($_POST['modifyStructure'])){
        $structureId = (int)$_POST['modifyStructure']['id'];
        //Et qu'on a les droits
        if($rights->getCreateStructure() || ($rights->getModifyStructure() && $structure->getId() == $structureId)){
            $data = $_POST['modifyStructure'];

            modifyStructure($data, $_FILES);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour modifier cette structure.');
        }
    }
}

/**
 * @param array $data
 * @param array $files
 *
 * @return bool
 * @throws Exception|\Users\Rights
 * @throws Exception|\Users\Structure
 * @throws Exception|\Users\User
 */
function createStructure(array $data, array $files):bool{
    //Instanciation de la connexion
    $db = databaseConnection();
    try{
        //Début de la transaction
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $name = (string)$data['name'];
        $address = (string)$data['address'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        $phone = purgePhone((string)$data['phone']);
        $fax = purgePhone((string)$data['fax']);
        $email = $data['email'];
        $siret = (string)$data['siret'];
        $ape = (string)$data['ape'];
        $isPartner = (bool)$data['isPartner'];
        $acceptNewsLetter = (bool)$data['acceptNewsLetter'];
        $packageContent = (string)$data['packageContent'];
        $packageProvision = (float)$data['packageProvision'];
        $defaultMargin = (int)$data['defaultMargin'];
        $freightCharges = $data['freightCharges'] == '' ? null : (float)$data['freightCharges'];
        $defaultWarranty = (string)$data['warranty'];
        $defaultFunding = (string)$data['funding'];
        //$civility = (bool)$data['civility'];
        $firstName = (string)$data['firstName'];
        $lastName = (string)$data['lastName'];
        $ownerEmail = (string)$data['email'];
        $password = (string)$data['password'];
        $confPass = (string)$data['confPass'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($name) || empty($address) || empty($postalCode) || empty($phone) || empty($email)
            || empty($packageProvision) || empty($defaultMargin) || !isset($files, $packageContent)
            || empty($firstName) || empty($lastName) || empty($ownerEmail) || empty($password) || empty($confPass)
        ){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!preg_match('/^[0-9]{5}$/', $postalCode)){
            throw new Exception('Le code postal est mal formaté.');
        }
        if(!verifyPureString($town)){
            throw new Exception('Le nom de la ville est incorrect');
        }
        if(!verifyPhone($phone)){
            throw new Exception('Le numéro de téléphone est incorrect.');
        }
        if($fax != null && !verifyPhone($fax)){
            throw new Exception('Le numéro de fax est incorrect.');
        }
        if(!verifyMail($email) || !verifyMail($ownerEmail)){
            throw new Exception('Le mail est mal formaté.');
        }
        if(!empty($siret) && !verifySiret($siret)){
            throw new Exception('Le numéro de SIRET est mal formaté.');
        }
        if(!empty($ape) && !verifyApe($ape)){
            throw new Exception('Le code APE entré est mal formaté.');
        }
        if($password !== $confPass){
            throw new Exception('Les pass entrés ne correspondent pas.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée jusqu'ici ==> cohérence des données.
        //On créé l'objet en local
        $structure = new \Users\Structure(0, $name, $address, $postalCode, $town, $phone, '', $fax, $email, $isPartner,
                                          $acceptNewsLetter, '', '', $siret, $ape, $packageContent, $packageProvision,
                                          false, $defaultMargin, $freightCharges, $defaultWarranty, $defaultFunding);

        //Création en base
        $createStructure = \Users\StructureManager::insertStructure($db, $structure);
        if(is_a($createStructure, 'Exception')){
            throw $createStructure;
        }

        //On créé les droits du l'user
        $rights = new \Users\Rights(0, false, true, false, true, false, false, true, true, true);
        //Et on insère en base
        $createRights = \Users\RightsManager::insertRights($db, $rights);
        if(is_a($createRights, '\Exception')){
            throw $createRights;
        }
        $rights = $createRights;
        //Hashage du password
        $password = password_hash($password, PASSWORD_BCRYPT);
        //Puis on créé l'utilisateur principal de la structure
        $user = new \Users\User(0, $ownerEmail, $password, 2, $createStructure, $createStructure->getId(), $firstName,
                                $lastName, '', '', new DateTime(), $acceptNewsLetter, $rights, $rights->getId(),
                                '', '');
        //Et on insère en base
        $createUser = \Users\UserManager::insertUser($db, $user);
        if(is_a($createUser, '\Exception')){
            throw $createUser;
        }

        //On valide la transaction
        $db->commit();
        //Fermeture de la connexion
        $db = null;
        $_SESSION['returnAction'] = array(1, 'Création de la nouvelle structure effectuée avec succès !');
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
 * @param array $files
 *
 * @return bool
 * @throws array|Exception
 */
function modifyStructure(array $data, array $files):bool{
    //Instanciation de la connexion
    $db = databaseConnection();
    try{
        //Début de la transaction
        $db->beginTransaction();

        //var_dump($data);

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $structureId = (int)$data['id'];
        $name = (string)$data['name'];
        $address = (string)$data['address'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        $phone = purgePhone((string)$data['phone']);
        $fax = purgePhone((string)$data['fax']);
        $email = $data['email'];
        $siret = (string)$data['siret'];
        $ape = (string)$data['ape'];
        $isPartner = !isset($data['isPartner']) ? null : (bool)$data['isPartner'];
        $acceptNewsLetter = (bool)$data['acceptNewsLetter'];
        $packageContent = (string)$data['packageContent'];
        $packageProvision = (float)$data['packageProvision'];
        $defaultMargin = (int)$data['defaultMargin'];
        $freightCharges = $data['freightCharges'] == '' ? null : (float)$data['freightCharges'];
        $defaultWarranty = (string)$data['warranty'];
        $defaultFunding = (string)$data['funding'];
        $firstName = (string)$data['firstName'];
        $lastName = (string)$data['lastName'];
        $ownerEmail = (string)$data['ownerEmail'];
        $password = (string)$data['password'];
        $confPass = (string)$data['confPass'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($name) || empty($address) || empty($postalCode) || empty($phone) || empty($email)
            || empty($packageProvision) || empty($defaultMargin) || !isset($files, $packageContent)){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!preg_match('/^[0-9]{5}$/', $postalCode)){
            throw new Exception('Le code postal est mal formaté.');
        }
        if(!verifyPureString($town)){
            throw new Exception('Le nom de la ville est incorrect');
        }
        if(!verifyPhone($phone)){
            throw new Exception('Le numéro de téléphone est incorrect.');
        }
        if($fax != null && !verifyPhone($fax)){
            throw new Exception('Le numéro de fax est incorrect.');
        }
        if(!verifyMail($email)){
            throw new Exception('Le mail est mal formaté.');
        }
        if(!empty($siret) && !verifySiret($siret)){
            throw new Exception('Le numéro de SIRET est mal formaté.');
        }
        if(!empty($ape) && !verifyApe($ape)){
            throw new Exception('Le code APE entré est mal formaté.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée jusqu'ici ==> cohérence des données.
        //On récupère les données de la structure actuellement en base
        $structure = \Users\StructureManager::fetchStructure($db, $structureId);

        if(is_a($structure, '\Exception')){
            throw new Exception($structure->getMessage(), $structure->getCode());
        }

        $firstUser = \Users\UserManager::fetchAdminOfStructure($db, $structureId);
        if(is_a($firstUser, '\Exception')){
            throw $firstUser;
        }

        //Upload de l'image si il y en a une
        if(!empty($files['modifyStructureImage']['name'])){
            $files = $files['modifyStructureImage'];
            //$extension = $files['name'];
            //$searchExt = explode('.', $extension);
            //$ext = $searchExt[count($searchExt) - 1];

            $pathOnServer = $_SERVER["DOCUMENT_ROOT"].getAppPath().'/theme/images/bani/';
            $fileName = $files['name'];
            $type = $files['type'];
            $tmp_name = $files['tmp_name'];
            $error = $files['error'];
            $size = (int)$files['size'];

            $fileToUpload = new FileToUpload($pathOnServer, $fileName, $type, $tmp_name, $error, $size);
            $uploadFile = FileToUploadManager::uploadFile($fileToUpload);
            if(is_a($uploadFile, 'Exception')){
                throw $uploadFile;
            }

            $structure->setImageName($fileName);
        }

        //Modification de l'objet local
        $structure->setStructureName($name);
        $structure->setAddress($address);
        $structure->setPostalCode($postalCode);
        $structure->setTown($town);
        $structure->setPhone($phone);
        $structure->setFax($fax);
        $structure->setEmail($email);
        $structure->setSiret($siret);
        $structure->setApe($ape);
        if($isPartner != null)
            $structure->setIsPartner($isPartner);
        $structure->setAcceptNewsLetter($acceptNewsLetter);
        $structure->setFreightCharges($freightCharges);
        $structure->setDefaultWarranty($defaultWarranty);
        $structure->setDefaultFunding($defaultFunding);
        $structure->setDefaultMargin($defaultMargin);

        //Update des informations en base
        $hydrateStructure = \Users\StructureManager::hydrateStructure($db, $structure);

        if(is_a($hydrateStructure, 'Exception')){
            throw new Exception('Erreur lors de la modification de la structure en base.');
        }

        $firstUser->setFirstName($firstName);
        $firstUser->setLastName($lastName);
        $firstUser->setEmail($ownerEmail);
        if(!empty($password) && !empty($confPass) && $password == $confPass){
            $password = password_hash($password, PASSWORD_BCRYPT);
            $firstUser->setPassword($password);
        }

        $hydrateUser = \Users\UserManager::hydrateUser($db, $firstUser);

        if(is_a($hydrateUser, '\Exception')){
            throw $hydrateUser;
        }

        //Si on vient de modifier sa propre structure, on refresh la variable de session
        if($_SESSION['user']->getStructureId() === $structure->getId()){
            $_SESSION['user']->setStructure($structure);
        }

        //On valide la transaction
        $db->commit();
        //Fermeture de la connexion
        $db = null;
        $_SESSION['returnAction'] = array(1, 'Modification de la structure effectuée avec succès !');
        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;
        return false;
    }
}