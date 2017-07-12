<?php
$pageTitle = 'Gestion des conseillers';
$moreJs = array('formManager');


if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    //Si on demande la création d'un utilisateur
    if(!empty($_POST['createUser'])){
        //Si on a les droits de le faire
        if($rights->getCreateUser()){
            $data = $_POST['createUser'];

            createUser($data);
        }
    }

    //Si on demande la modification d'un utilisateur
    else if(!empty($_POST['modifyUser'])){
        $userId = (int)$_POST['modifyUser']['id'];
        if($rights->getCreateUser() || ($rights->getModifyUser() && $user->getId() == $userId)){
            $data = $_POST['modifyUser'];

            modifyUser($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour modifier cet utilisateur.');
        }
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function createUser(array $data):bool{
    //Instanciation de la connexion
    $db = databaseConnection();
    try{
        //Début de la transaction
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $lastName = (string)$data['lastName'];
        $firstName = (string)$data['firstName'];
        $password = (string)$data['password'];
        $confPass = (string)$data['confPass'];
        $phone = purgePhone((string)$data['phone']);
        $mobile = purgePhone((string)$data['mobile']);
        $email = $data['email'];
        $acceptNewsLetter = (bool)$data['acceptNewsLetter'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($lastName) || empty($firstName) || empty($password) || empty($confPass) || empty($email)){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPhone($phone)){
            throw new Exception('Le numéro de téléphone est incorrect.');
        }
        if($mobile != null && !verifyPhone($mobile)){
            throw new Exception('Le numéro de portable est incorrect.');
        }
        if(!verifyMail($email)){
            throw new Exception('Le mail est mal formaté.');
        }
        if($password !== $confPass){
            throw new Exception('Les mots de passe entrés ne correspondent pas.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée jusqu'ici ==> cohérence des données.
        /********************DEFINITION DES DROITS*************************/
        $administrateVehicleOnDemand = false;
        $viewVehicleOnDemand = true;
        $administrateVehicleOnStock = false;
        $viewVehicleOnStock = true;
        $administrateCarousel = false;
        $createStructure = false;
        $modifyStructure = false;
        $createUser = false;
        $modifyUser = true;
        /********************DEFINITION DES DROITS*************************/
        //Création des droits du futur utilisateur
        $rights = new \Users\Rights(0, $administrateVehicleOnDemand, $viewVehicleOnDemand, $administrateVehicleOnStock,
                                    $viewVehicleOnStock, $administrateCarousel, $createStructure, $modifyStructure,
                                    $createUser, $modifyUser);
        //Insertion des droits en base
        $insertRights = \Users\RightsManager::insertRights($db, $rights);
        if(is_a($insertRights, 'Exception')){
            throw new Exception($insertRights->getMessage(), $insertRights->getCode());
        }
        $rightsId = $insertRights->getId();
        $rights->setId($rightsId);

        //Récupération des informations de la structure
        // /!\ par défaut, on prend la structure actuellement en session, donc on ne peut pas créer un utilisateur
        // /!\ pour qqn d'autre
        /** @var \Users\User $user */
        $user = $_SESSION['user'];
        $structure = $user->getStructure();
        $structureId = $structure->getId();

        //Hashage du password
        $password = password_hash($password, PASSWORD_BCRYPT);
        //Création d'un nouvel utilisateur
        $user = new \Users\User(0, $email, $password, 2, $structure, $structureId, $firstName, $lastName, $phone,
                                $mobile, null, $acceptNewsLetter, $rights, $rightsId);

        //Insertion en base
        $insertUser = Users\UserManager::insertUser($db, $user);
        if(is_a($insertUser, 'Exception')){
            throw new Exception($insertUser->getMessage(), $insertUser->getCode());
        }

        $db->commit();
        $db = null;
        $_SESSION['returnAction'] = array(1, 'Création de l\'utilisateur effectuée avec succès !');
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
function modifyUser(array $data):bool{
    //Instanciation de la connexion
    $db = databaseConnection();
    try{
        //Début de la transaction
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $userId = (int)$data['id'];
        $lastName = (string)$data['lastName'];
        $firstName = (string)$data['firstName'];
        $password = (string)$data['password'];
        $confPass = (string)$data['confPass'];
        $phone = purgePhone((string)$data['phone']);
        $mobile = purgePhone((string)$data['mobile']);
        $email = $data['email'];
        $acceptNewsLetter = (bool)$data['acceptNewsLetter'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($userId) || empty($lastName) || empty($firstName) || empty($email)){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPhone($phone)){
            throw new Exception('Le numéro de téléphone est incorrect.');
        }
        if($mobile != null && !verifyPhone($mobile)){
            throw new Exception('Le numéro de portable est incorrect.');
        }
        if(!verifyMail($email)){
            throw new Exception('Le mail est mal formaté.');
        }
        if(!empty($password) && $password !== $confPass){
            throw new Exception('Les mots de passe entrés ne correspondent pas.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée jusqu'ici ==> cohérence des données.

        //Récupération des informations de l'utilisateur
        $user = Users\UserManager::fetchUser($db, $userId);
        if(is_a($user, '\Exception')){
            throw new Exception($user->getMessage(), $user->getCode());
        }
        //Update des infos en local
        $user->setLastName($lastName);
        $user->setFirstName($firstName);
        if(!empty($password)){
            $password = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($password);
        }
        $user->setPhone($phone);
        $user->setMobile($mobile);
        $user->setEmail($email);
        $user->setAcceptNewsLetter($acceptNewsLetter);

        //Modification en base
        $hydrateUser = \Users\UserManager::hydrateUser($db, $user);
        if(is_a($hydrateUser, 'Exception')){
            throw new Exception($hydrateUser->getMessage(), $hydrateUser->getCode());
        }

        $db->commit();
        $db = null;
        $_SESSION['returnAction'] = array(1, 'Création de l\'utilisateur effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;

        return false;
    }
}