<?php
$pageTitle = 'Administration';
$moreJs = array('formManager');

if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    //Si on demande la création d'un pays
    if(!empty($_POST['createCountry'])){
        if($user->getType() === 1){
            $data = $_POST['createCountry'];

            createCountry($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour créer un pays.');
        }
    }

    //Si on demande la modification d'un pays
    else if(!empty($_POST['modifyCountry'])){
        if($user->getType() === 1){
            $data = $_POST['modifyCountry'];

            modifyCountry($data);
        }
        else{
            $_SESSION['returnAction'] = array(0, 'Erreur : Vous n\'avez pas les droits nécessaires pour modifier ce pays.');
        }
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function createCountry(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $name = (string)$data['name'];
        $abbreviation = (string)$data['abbreviation'];
        $currencyId = (int)$data['currency'];
        $vatAmount = (float)$data['vat'];
        $freightChargesAmount = (float)$data['freightCharges'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($currencyId) || $currencyId <= 0 || empty($name) || empty($abbreviation) || !isset($vatAmount)
            || $vatAmount < 0 || !isset($freightChargesAmount) || $freightChargesAmount < 0){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPureString($name)){
            throw new Exception('Le nom du pays est incorrect');
        }
        if(!verifyPureString($abbreviation)){
            throw new Exception('L\'abbreviation du pays est incorrecte');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, données cohérentes
        //On créé le pays en local
        $country = new \Prices\Country(0, $name, $abbreviation, null, $currencyId);

        //On l'insère en base
        $insertCountry = \Prices\CountryManager::insertCountry($db, $country);
        if(is_a($insertCountry, '\Exception')){
            throw new Exception($insertCountry->getMessage(), $insertCountry->getCode());
        }

        $country->setId($insertCountry->getId());

        //Puis on créé la TVA en local
        $vat = new \Prices\Vat(0, $vatAmount, new DateTime());
        //Puis insertion en base
        $insertVat = \Prices\VatManager::insertVat($db, $vat, $country->getId());
        if(is_a($insertVat, '\Exception')){
            throw new Exception($insertVat->getMessage(), $insertVat->getCode());
        }

        //Puis on créé les frais de transport
        $freightCharges = new \Prices\FreightCharges(0, $freightChargesAmount, new DateTime());
        //Et on insère en base
        $insertFreightCharges = \Prices\FreightChargesManager::insertFreightCharges($db, $freightCharges, $country->getId());
        if(is_a($insertFreightCharges, '\Exception')){
            throw new Exception($insertFreightCharges->getMessage(), $insertFreightCharges->getCode());
        }

        //On commit les changements
        $db->commit();
        $db = null;

        //Et message de succès
        $_SESSION['returnAction'] = array(1, 'Création du pays effectuée avec succès !');

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
function modifyCountry(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $countryId = (int)$data['id'];
        $name = (string)$data['name'];
        $abbreviation = (string)$data['abbreviation'];
        $currencyId = (int)$data['currency'];
        $vatAmount = (float)$data['vat'];
        $freightChargesAmount = (float)$data['freightCharges'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($countryId) || empty($currencyId) || $currencyId <= 0 || empty($name) || empty($abbreviation)
            || !isset($vatAmount) || $vatAmount < 0 || !isset($freightChargesAmount) || $freightChargesAmount < 0
        ){
            throw new Exception('Un ou plusieurs champs requis est(sont) vide(s)');
        }
        if(!verifyPureString($name)){
            throw new Exception('Le nom du pays est incorrect');
        }
        if(!verifyPureString($abbreviation)){
            throw new Exception('L\'abbreviation du pays est incorrecte');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //On récupère le pays en question
        $country = \Prices\CountryManager::fetchCountry($db, $countryId);
        //Si erreur lors de la recherche du pays
        if(is_a($country, '\Exception')){
            throw new Exception($country->getMessage(), $country->getCode());
        }

        //Si il y a eu un changement dans le cout du transport
        if($freightChargesAmount != $country->getFreightCharges()->getAmount()){
            //Alors on update l'objet en local
            $freightCharges = $country->getFreightCharges();
            $freightCharges->setAmount($freightChargesAmount);
            $freightCharges->setDate(new DateTime());

            //Et on insert un nouveau cout dans la base
            // /!\ NOTICE : On en insère un nouveau afin de garder un historique des coûts dans un éventuel but de faire des stats
            $insertFreightCharges = \Prices\FreightChargesManager::insertFreightCharges($db, $freightCharges, $countryId);
            if(is_a($insertFreightCharges, '\Exception')){
                throw new Exception($insertFreightCharges->getMessage(), $insertFreightCharges->getCode());
            }
        }
        //Si il y a eu un changement dans la TVA
        if($vatAmount != $country->getVat()->getAmount()){
            //On update l'objet en local
            $vat = $country->getVat();
            $vat->setAmount($vatAmount);
            $vat->setVatDate(new DateTime());

            //Et in insert une nouvelle TVA en base
            // /!\ NOTICE : On en insère un nouveau afin de garder un historique de TVA dans un éventuel but de faire des stats
            $insertVat = \Prices\VatManager::insertVat($db, $vat, $countryId);
            if(is_a($insertVat, '\Exception')){
                throw new Exception($insertVat->getMessage(), $insertVat->getCode());
            }
        }

        //On modifie les données du pays
        $country->setName($name);
        $country->setAbbreviation($abbreviation);
        $country->setCurrencyId($currencyId);

        //Et on hydrate la base
        $hydrateCountry = \Prices\CountryManager::hydrateCountry($db, $country);
        if(is_a($hydrateCountry, '\Exception')){
            throw new Exception($hydrateCountry->getMessage(), $hydrateCountry->getCode());
        }

        //On commit les changements
        $db->commit();
        $db = null;

        //Et message de succès
        $_SESSION['returnAction'] = array(1, 'Modification du pays effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;

        return false;
    }
}