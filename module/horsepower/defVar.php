<?php
$pageTitle = 'Administration';
$moreJs = array('formManager');

if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    //Si on demande la modification de frais de transport
    if(!empty($_POST['modifyHorsePower'])){
        if($user->getType() === 1){
            $data = $_POST['modifyHorsePower'];

            modifyHorsePower($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour modifier les frais de cartes grises.');
        }
    }
}

/**
 * @param array $data
 *
 * @return bool
 * @throws bool|Exception
 */
function modifyHorsePower(array $data):bool{
    $db = \databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $departmentId = (int)$data['id'];
        $amount = (float)$data['amount'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($departmentId) || $departmentId < 0 || !isset($amount) || $amount < 0){
            throw new \Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        $horsePower = \Prices\HorsepowerPriceManager::fetchPrice($db, $departmentId);
        if(is_a($horsePower, '\Exception')){
            throw new \Exception('Les frais de CG choisis n\'existent pas.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //On update en local le montant
        $horsePower->setAmount($amount);
        //Puis hydrate de la BDD
        $hydrateHorsePower = \Prices\HorsepowerPriceManager::hydratePrice($db, $horsePower);
        if(is_a($hydrateHorsePower, '\Exception')){
            throw $hydrateHorsePower;
        }

        //On commit les changements
        $db->commit();
        $db = null;

        //Et message de succès
        $_SESSION['returnAction'] = array(1, 'Modification des frais de carte grise effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;

        return false;
    }
}