<?php
$pageTitle = 'Administration';
$moreJs = array('formManager');

if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    //Si on demande la modification de frais de transport
    if(!empty($_POST['modifyFreightCharges'])){
        if($user->getType() === 1){
            $data = $_POST['modifyFreightCharges'];

            modifyFreightCharges($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour modifier les frais de transport.');
        }
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function modifyFreightCharges(array $data):bool{
    $db = \databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $departmentId = (int)$data['id'];
        $departmentName = (string)$data['name'];
        $amount = (float)$data['amount'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($departmentId) || $departmentId < 0 || empty($departmentName) || !isset($amount) || $amount < 0){
            throw new \Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPureString($departmentName)){
            throw new \Exception('Le nom du département est mal formaté.');
        }
        $freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightCharges($db, $departmentId);
        if(is_a($freightCharges, '\Exception')){
            throw new \Exception('Les frais de transport choisis n\'existent pas.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //On update l'objet fraichement récupéré
        $freightCharges->setDepartmentName($departmentName);
        $freightCharges->setAmount($amount);
        $freightCharges->setDate(new \DateTime());

        //Et on hydrate la base
        $hydrateFreightCharges = \Prices\FreightChargesInFranceManager::hydrateFreightCharges($db, $freightCharges);
        if(is_a($hydrateFreightCharges, '\Exception')){
            throw new \Exception($hydrateFreightCharges->getMessage(), $hydrateFreightCharges->getCode());
        }

        //On commit les changements
        $db->commit();
        $db = null;

        //Et message de succès
        $_SESSION['returnAction'] = array(1, 'Modification des frais de transport effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;

        return false;
    }
}