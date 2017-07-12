<?php
$pageTitle = 'Commandes de véhicules';
$moreCss = array('bootstrap-multiselect', 'jquery-ui.min', 'jquery-ui.structure.min', 'jquery-ui.theme.min');
$moreJs = array('formManager', 'typeAhead', 'sortTable', 'bootstrap-multiselect', 'jquery-ui.min');
if( (!empty($_GET['category']) && (
            $_GET['category'] == 'offers'
            || $_GET['category'] == 'orderForms'))
    && (!empty($_GET['action']) && (
            $_GET['action'] == 'view'
            || $_GET['action'] == 'transform')))
    $moreJs[] = 'offers_function';

if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    if($user->getType() === 1){
        //Si on demande la création d'un véhicule générique
        if(!empty($_POST['createGenericVehicle']))
            createGenericVehicle($_POST['createGenericVehicle']);
        //Si on demande la desactivation d'un véhicule générique
        else if(!empty($_POST['disableFinish']))
            disableFinish((int)$_POST['disableFinish']['id']);
        //Si on demande la réactivation d'un véhicule générique
        else if(!empty($_POST['enableFinish']))
            enableFinish((int)$_POST['enableFinish']['id']);
        //Si on demande la modification d'un véhicule générique
        else if(!empty($_POST['modifyGenericVehicle']))
            modifyGenericVehicle($_POST['modifyGenericVehicle']);
        //Si on demande la création d'un véhicule
        else if(!empty($_POST['createVehicle']))
            createVehicle($_POST['createVehicle']);
        //Si on demande la désactivation d'un véhicule
        else if(!empty($_POST['disableVehicle']))
            disableVehicle((int)$_POST['disableVehicle']['id']);
        //Si on demande l'activation d'un véhicule
        else if(!empty($_POST['enableVehicle']))
            enableVehicle((int)$_POST['enableVehicle']['id']);
        //Si on demande la modification d'un véhicule
        else if(!empty($_POST['modifyVehicle']))
            modifyVehicle($_POST['modifyVehicle']);
        //Si on demande la création d'une couleur
        else if(!empty($_POST['createColor']))
            createColor($_POST['createColor']);
        //Si on demande la modification d'une couleur
        else if(!empty($_POST['modifyColor']))
            modifyColor($_POST['modifyColor']);
        //Si on demande la création de jantes
        else if(!empty($_POST['createRim']))
            createRim($_POST['createRim']);
        //Si on demande la modification de jantes
        else if(!empty($_POST['modifyRim']))
            modifyRim($_POST['modifyRim']);
        //Si on demande la création d'un pack
        else if(!empty($_POST['createPack']))
            createPack($_POST['createPack']);
        //Si on demande la modification d'un pack
        else if(!empty($_POST['modifyPack']))
            modifyPack($_POST['modifyPack']);
        //Si on demande l'ajout d'une image
        else if(!empty($_POST['createVehicleImage']) && !empty($_FILES))
            addImage((int)$_POST['createVehicleImage']['vehicleId'], $_FILES);
        else if(!empty($_POST['createEquipment']))
            createEquipment($_POST['createEquipment']);

        //Si on demande la suppression d'une couleur
        if($_GET['category'] == 'color' && $_GET['action'] == 'delete' && !empty($_GET['colorId']))
            deleteColor((int)$_GET['colorId']);
        //Si on demande la suppression de jantes
        else if($_GET['category'] == 'rim' && $_GET['action'] == 'delete' && !empty($_GET['rimId']))
            deleteRims((int)$_GET['rimId']);
        //Si on demande la suppression d'un pack
        else if($_GET['category'] == 'pack' && $_GET['action'] == 'delete' && !empty($_GET['packId']))
            deletePack((int)$_GET['packId']);
        else if($_GET['category'] == 'vehicle' && $_GET['action'] == 'delete' && !empty($_GET['vehicleId']))
            deleteVehicle((int)$_GET['vehicleId']);
        else if($_GET['category'] == 'genericVehicle' && $_GET['action'] == 'delete' && !empty($_GET['finishId']))
            deleteGenericVehicle((int)$_GET['finishId']);
    }

    if(!empty($_POST['createOffer']))
        createOffer($_POST['createOffer']);

    if(!empty($_GET['category'])){
        switch($_GET['category']){
            case 'offers' :
                if(!empty($_GET['action'])){
                    switch($_GET['action']){
                        case 'transform' :
                            if(!empty($_GET['offerReference'])
                                && !empty($_POST['transformOffer'])){
                                transformOfferToCommand($_GET['offerReference'], $_POST['transformOffer']);
                            }
                            break;
                        default :
                            break;
                    }
                }
                break;
            default :
                break;
        }
    }
}

/**
 * @param array $data
 *
 * @return bool
 * @throws Exception|\Prices\Currency
 * @throws Exception|\Prices\Dealer
 * @throws Exception|\Prices\Price
 * @throws Exception|\Vehicle\Brand
 * @throws Exception|\Vehicle\Finish
 * @throws Exception|\Vehicle\Model
 * @throws bool|Exception
 */
function createGenericVehicle(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $brandId = (int)$data['brandId'];
        $modelName = (string)$data['model'];
        $finishName = (string)$data['finish'];
        $serialEquipments = $data['serialEquipment'];
        $optionalEquipments = empty($data['optionalEquipment']) ? array() : $data['optionalEquipment'];
        $optionalEquipmentsPrices = $data['optionalPrice'];
        $dealerId = (int)$data['dealerId'];
        $currencyId = (int)$data['currencyId'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($brandId) || empty($modelName) || empty($finishName) || empty($dealerId) || empty($currencyId)){
            throw new \Exception('Un ou plusieurs champs requis sont manquants.');
        }
        $brand = \Vehicle\BrandManager::fetchBrand($db, $brandId);
        if(is_a($brand, '\Exception')){
            throw $brand;
            //throw new \Exception('Impossible de trouver la marque demandée.');
        }
        $dealer = \Prices\DealerManager::fetchDealer($db, $dealerId);
        if(is_a($dealer, '\Exception')){
            throw $dealer;
            //throw new \Exception('Impossible de trouver le concessionnaire demandé.');
        }
        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception')){
            throw $currency;
            //throw new \Exception('Impossible de trouver la devise demandée.');
        }
        //Check de la présence du prix pour chaque équipement optionnel choisi
        foreach($optionalEquipments as $optionalEquipment){
            if(empty($optionalEquipmentsPrices[(int)$optionalEquipment])){
                throw new \Exception('Un équipement optionnel choisi n\'a pas de prix assigné.');
            }
        }
        if(!\Vehicle\EquipmentManager::doTheyExist($db, $serialEquipments)){
            throw new \Exception('Un ou plusieurs equipements de série choisis n\'existent pas.');
        }
        if(!\Vehicle\EquipmentManager::doTheyExist($db, $optionalEquipments)){
            throw new \Exception('Un ou plusieurs equipements en option choisis n\'existent pas.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, cohérence des données entrées
        //On créé le véhicule générique
        /********************GESTION DES INFORMATION DU MODELE**********************/
        //A commencer par le modèle
        //On regarde déjà si le modèle existe déjà en base
        $model = \Vehicle\ModelManager::fetchModelByName($db, $modelName, $brandId);
        //Si le modèle n'existe pas encore
        if(is_a($model, '\Exception')){
            //On le créé en local
            $model = new \Vehicle\Model(0, $modelName, $brand, $brandId);
            //Et on l'insère en base
            $insertModel = \Vehicle\ModelManager::insertModel($db, $model);
            if(is_a($insertModel, '\Exception')){
                throw $insertModel;
            }
            $model = $insertModel;
        }
        /********************GESTION DES INFORMATION DU MODELE**********************/

        /********************GESTION DES INFORMATION DE LA FINITION**********************/
        //On regarde si une telle finition existe déjà en base
        $finish = \Vehicle\FinishManager::fetchFinishByName($db, $finishName, $model->getId());
        //Si une telle finition existe déjà
        if(!is_a($finish, '\Exception')){
            throw new Exception('Erreur, cette finition existe déjà');
        }
        //Si cette finition n'existe pas encore
        //On la créé en local
        $finish = new \Vehicle\Finish(0, $finishName, $model, $model->getId(), $dealer, $dealerId, true);
        //Et on l'insère en base
        $insertFinish = \Vehicle\FinishManager::insertFinish($db, $finish);
        if(is_a($insertFinish, '\Exception')){
            throw $insertFinish;
        }
        $finish = $insertFinish;
        /********************GESTION DES INFORMATION DE LA FINITION**********************/

        /********************GESTION DES EQUIPEMENTS DE SERIE**********************/
        //On insère ensuite ses équipements de série
        $equipmentsDetails = array();
        foreach($serialEquipments as $equipment){
            $equipmentsDetails[] = array('equipment' => $equipment, 'price' => null);
        }
        $insertEquipments = \Vehicle\FinishManager::addEquipments($db, $finish->getId(), $equipmentsDetails);
        if(is_a($insertEquipments, '\Exception')){
            throw $insertEquipments;
        }
        /********************GESTION DES EQUIPEMENTS DE SERIE**********************/

        /********************GESTION DES EQUIPEMENTS EN OPTION**********************/
        //On insère ensuite ses équipements en option
        //On va chercher la tva du pays concerné
        if(!empty($optionalEquipments)){
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $equipmentsDetails = array();
            foreach($optionalEquipments as $equipment){
                //On insère d'abord le prix correspondant
                $postTaxesPrice = (float)$optionalEquipmentsPrices[(int)$equipment];
                $pretaxPrice = \Prices\VatManager::convertToPretax($postTaxesPrice, $vat->getAmount());
                $price = new \Prices\Price(0, $pretaxPrice, 0.0, null, $dealer->getCountryId(), null, $currencyId, 0.0, 0.0, new DateTime());
                //On insère le prix en base
                $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
                if(is_a($insertPrice, '\Exception')){
                    throw $insertPrice;
                }
                $price = $insertPrice;
                $equipmentsDetails[] = array('equipment' => $equipment, 'price' => $price->getId());
            }
            $insertEquipments = \Vehicle\FinishManager::addEquipments($db, $finish->getId(), $equipmentsDetails);
            if(is_a($insertEquipments, '\Exception')){
                throw $insertEquipments;
            }
        }
        /********************GESTION DES EQUIPEMENTS EN OPTION**********************/

        //Puis on valide les requetes si tout s'est bien passé
        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Création du véhicule générique effectuée avec succès !');

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
 * @param int $finishId
 *
 * @return bool
 */
function disableFinish(int $finishId):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        $finish = \Vehicle\FinishManager::fetchFinish($db, $finishId);
        if(is_a($finish, '\Exception')){
            throw new \Exception('Impossible de trouver le véhicule générique demandé.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Désactivation de la finition
        $finish->disableFinish();

        //Hydrate de la BDD
        $hydrateFinish = \Vehicle\FinishManager::hydrateFinish($db, $finish);
        if(is_a($hydrateFinish, '\Exception')){
            throw new \Exception($hydrateFinish->getMessage(), $hydrateFinish->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Desactivation du véhicule générique effectuée avec succès !');

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
 * @param int $finishId
 *
 * @return bool
 */
function enableFinish(int $finishId):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        $finish = \Vehicle\FinishManager::fetchFinish($db, $finishId);
        if(is_a($finish, '\Exception')){
            throw new \Exception('Impossible de trouver le véhicule générique demandé.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Désactivation de la finition
        $finish->enableFinish();

        //Hydrate de la BDD
        $hydrateFinish = \Vehicle\FinishManager::hydrateFinish($db, $finish);
        if(is_a($hydrateFinish, '\Exception')){
            throw new \Exception($hydrateFinish->getMessage(), $hydrateFinish->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Réactivation du véhicule générique effectuée avec succès !');

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
 * @param int $finishId
 *
 * @return bool
 * @throws Exception|\Vehicle\Finish
 * @throws bool|Exception
 */
function deleteGenericVehicle(int $finishId){
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        //On récupère la finition demandée si elle n'est pas nulle
        if(empty($finishId)){
            throw new Exception('Erreur, la finition ne doit pas être nulle.');
        }

        $finish = \Vehicle\FinishManager::fetchFinish($db, $finishId, true);
        if(is_a($finish, '\Exception')){
            throw $finish;
        }

        //Arrivée ici, la finition existe,donc on la supprime
        $deleteVehicle = \Vehicle\FinishManager::deleteFinish($db, $finishId);
        if(is_a($deleteVehicle, '\Exception')){
            throw $deleteVehicle;
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Suppression du véhicule générique effectuée avec succès !');

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
function modifyGenericVehicle(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $finishId = (int)$data['id'];
        $finishName = (string)$data['finish'];
        $serialEquipments = $data['serialEquipment'];
        $optionalEquipments = empty($data['optionalEquipment']) ? array() : $data['optionalEquipment'];
        $optionalEquipmentsPrices = $data['optionalPrice'];
        $dealerId = (int)$data['dealerId'];
        $currencyId = (int)$data['currencyId'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($finishId) || empty($finishName) || empty($dealerId) || empty($currencyId)){
            throw new \Exception('Un ou plusieurs champs requis sont manquants.');
        }
        $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
        if(is_a($finish, '\Exception')){
            throw new \Exception('Impossible de trouver la finition demandée.');
        }
        $dealer = \Prices\DealerManager::fetchDealer($db, $dealerId);
        if(is_a($dealer, '\Exception')){
            throw new \Exception('Impossible de trouver le concessionnaire demandé.');
        }
        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception')){
            throw new \Exception('Impossible de trouver la devise demandée.');
        }
        //Check de la présence du prix pour chaque équipement optionnel choisi
        foreach($optionalEquipments as $optionalEquipment){
            if(empty($optionalEquipmentsPrices[(int)$optionalEquipment])){
                throw new \Exception('Un équipement optionnel choisi n\'a pas de prix assigné.');
            }
        }
        if(!\Vehicle\EquipmentManager::doTheyExist($db, $serialEquipments)){
            throw new \Exception('Un ou plusieurs equipements de série choisis n\'existent pas.');
        }
        if(!\Vehicle\EquipmentManager::doTheyExist($db, $optionalEquipments)){
            throw new \Exception('Un ou plusieurs equipements en option choisis n\'existent pas.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        /********************GESTION DES INFORMATION DE LA FINITION**********************/
        //On modifie les informations simples sur la finition
        $finish->setName($finishName);
        $finish->setDealerId($dealer->getId());

        //Et on hydrate la base
        $hydrateFinish = \Vehicle\FinishManager::hydrateFinish($db, $finish);
        if(is_a($hydrateFinish, '\Exception')){
            throw new \Exception($hydrateFinish->getMessage(), $hydrateFinish->getCode());
        }
        /********************GESTION DES INFORMATION DE LA FINITION**********************/

        /********************GESTION DES EQUIPEMENTS DE SERIE**********************/
        //On récupère ensuite les différences dans les équipements
        $actualSerialEquipments = \Vehicle\FinishManager::fetchSerialEquipments($db, $finishId);
        $actualSerialEquipmentsIds = array();
        foreach($actualSerialEquipments as $equipment){
            $actualSerialEquipmentsIds[] = $equipment->getId();
        }

        $serialEquipmentsToAdd = array();
        //On regarde les équipements de série à ajouter
        foreach($serialEquipments as $equipmentId){
            if(!in_array((int)$equipmentId, $actualSerialEquipmentsIds)){
                $serialEquipmentsToAdd[] = (int)$equipmentId;
            }
        }

        //Et on les ajoute
        if(!empty($serialEquipmentsToAdd)){
            $equipmentsDetails = array();
            foreach($serialEquipmentsToAdd as $equipment){
                $equipmentsDetails[] = array('equipment' => $equipment, 'price' => null);
            }
            $addEquipments = \Vehicle\FinishManager::addEquipments($db, $finishId, $equipmentsDetails);
            if(is_a($addEquipments, '\Exception')){
                throw new \Exception($addEquipments->getMessage(), $addEquipments->getCode());
            }
        }

        $serialEquipmentsToRemove = array();
        //Et les equipements de série à retirer
        foreach($actualSerialEquipmentsIds as $equipmentId){
            if(!in_array((int)$equipmentId, $serialEquipments)){
                $serialEquipmentsToRemove[] = (int)$equipmentId;
            }
        }
        if(!empty($serialEquipmentsToRemove)){
            $removeEquipments = \Vehicle\FinishManager::removeEquipments($db, $finishId, $serialEquipmentsToRemove);
            if(is_a($removeEquipments, '\Exception')){
                throw new \Exception($removeEquipments->getMessage(), $removeEquipments->getCode());
            }
        }
        /********************GESTION DES EQUIPEMENTS DE SERIE**********************/

        /********************GESTION DES EQUIPEMENTS EN OPTION**********************/
        $actualOptionalEquipments = \Vehicle\FinishManager::fetchOptionalEquipments($db, $finishId);
        $actualOptionalEquipmentsIds = array();
        $actualOptionalEquipmentsPrices = array();
        $vat = null;
        foreach($actualOptionalEquipments as $equipment){
            $actualOptionalEquipmentsIds[] = $equipment->getId();
            if($vat == null){
                $vat = \Prices\VatManager::fetchFrenchVat($db);
            }
            $actualOptionalEquipmentsPrices[$equipment->getId()] = \Prices\VatManager::convertToPostTaxes($equipment->getPrice()->getPretaxBuyingPrice(), $vat->getAmount());
        }

        $optionalEquipmentsToAdd = array();
        //On regarde les équipements en option à ajouter
        foreach($optionalEquipments as $equipmentId){
            if(!in_array((int)$equipmentId, $actualOptionalEquipmentsIds)){
                $optionalEquipmentsToAdd[] = (int)$equipmentId;
            }
        }
        //Et on les ajoute
        if(!empty($optionalEquipmentsToAdd)){
            $equipmentsDetails = array();
            foreach($optionalEquipmentsToAdd as $equipment){
                //On insère d'abord le prix correspondant
                $postTaxesPrice = (float)$optionalEquipmentsPrices[(int)$equipment];
                $pretaxPrice = \Prices\VatManager::convertToPretax($postTaxesPrice, $vat->getAmount());
                $price = new \Prices\Price(0, $pretaxPrice, 0.0, null, $dealer->getCountryId(), null, $currencyId, 0.0, 0.0, new DateTime());
                //On insère le prix en base
                $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
                if(is_a($insertPrice, '\Exception')){
                    throw new \Exception($insertPrice->getMessage(), $insertPrice->getCode());
                }
                $price = $insertPrice;
                $equipmentsDetails[] = array('equipment' => $equipment, 'price' => $price->getId());
            }
            $addEquipments = \Vehicle\FinishManager::addEquipments($db, $finishId, $equipmentsDetails);
            if(is_a($addEquipments, '\Exception')){
                throw new \Exception($addEquipments->getMessage(), $addEquipments->getCode());
            }
        }

        $optionalEquipmentsToRemove = array();
        //Et les equipements de série à retirer
        foreach($actualOptionalEquipmentsIds as $equipmentId){
            if(!in_array((int)$equipmentId, $optionalEquipments)){
                $optionalEquipmentsToRemove[] = (int)$equipmentId;
            }
        }
        if(!empty($optionalEquipmentsToRemove)){
            $removeEquipments = \Vehicle\FinishManager::removeEquipments($db, $finishId, $optionalEquipmentsToRemove);
            if(is_a($removeEquipments, '\Exception')){
                throw new \Exception($removeEquipments->getMessage(), $removeEquipments->getCode());
            }
        }

        $optionalEquipmentsToModify = array();
        //Et les équipements dont le prix est à modifier
        foreach($optionalEquipmentsPrices as $equipmentId => $equipmentPrice){
            if(in_array($equipmentId, $optionalEquipments) && in_array($equipmentId, $actualOptionalEquipmentsIds)){
                $optionalEquipmentsToModify[] = array((int)$equipmentId, (float)$equipmentPrice);
            }
        }
        if(!empty($optionalEquipmentsToModify)){
            $equipmentsDetails = array();
            //Pour chaque équipement, on créé un nouveau Prix
            foreach($optionalEquipmentsToModify as $equipmentInformations){
                $equipment = $equipmentInformations[0];
                $amount = $equipmentInformations[1];
                //On insère d'abord le prix correspondant
                $postTaxesPrice = (float)$amount;
                $pretaxPrice = \Prices\VatManager::convertToPretax($postTaxesPrice, $vat->getAmount());
                $price = new \Prices\Price(0, $pretaxPrice, 0.0, null, $dealer->getCountryId(), null, $currencyId, 0.0, 0.0, new DateTime());
                //On insère le prix en base
                $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
                if(is_a($insertPrice, '\Exception')){
                    throw new \Exception($insertPrice->getMessage(), $insertPrice->getCode());
                }
                $price = $insertPrice;
                $equipmentsDetails[] = array('equipment' => $equipment, 'price' => $price->getId());
            }
            $updateEquipments = \Vehicle\FinishManager::updateEquipments($db, $finishId, $equipmentsDetails);
            if(is_a($updateEquipments, '\Exception')){
                throw new \Exception($updateEquipments->getMessage(), $updateEquipments->getCode());
            }
        }
        /********************GESTION DES EQUIPEMENTS EN OPTION**********************/

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Modification du véhicule générique effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * Création d'un véhicule en base
 * /!\ ATTENTION, définition en dur d'une variable par défaut :
 * $margin : 7 (%)
 * $managementFees : 250 € HT
 *
 * @param array $data
 *
 * @return bool
 */
function createVehicle(array $data):bool{
    $margin = 7;
    $managementFees = 250;

    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $finishId = (int)$data['finishId'];
        $engineSize = (float)$data['engineSize'];
        $engineName = (string)$data['engine'];
        $dynamicalPower = (int)$data['dynamicalPower'];
        $co2 = (int)$data['co2'];
        $fiscalPower = (int)$data['fiscalPower'];
        $transmissionName = (string)$data['transmission'];
        $doorsAmount = (int)$data['doorsAmount'];
        $sitsAmount = (int)$data['sitsAmount'];
        $gearboxId = (int)$data['gearbox'];
        $bodyworkId = (int)$data['bodywork'];
        $fuelId = (int)$data['fuel'];
        $buyingPrice = (float)$data['buyingPrice'];
        $currencyId = (int)$data['currencyId'];
        $publicPrice = (float)$data['publicPrice'];
        $maximumDiscount = (float)$data['maximumDiscount'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($finishId) || empty($engineSize) || empty($engineName) || empty($dynamicalPower) || empty($co2)
            || empty($fiscalPower) || empty($transmissionName) || empty($doorsAmount) || empty($sitsAmount) || empty($gearboxId)
            || empty($bodyworkId) || empty($fuelId) || empty($buyingPrice) || empty($currencyId) || !isset($publicPrice) || !isset($maximumDiscount)){
            throw new Exception('Erreur, un des champs requis n\'est pas présent.');
        }

        $gearbox = \Vehicle\GearboxManager::fetchGearbox($db, $gearboxId);
        if(is_a($gearbox, '\Exception')){
            throw new Exception('La boite de vitesse sélectionnée n\'est pas valide.');
        }
        $bodywork = \Vehicle\BodyworkManager::fetchBodywork($db, $bodyworkId);
        if(is_a($bodywork, '\Exception')){
            throw new Exception('La carrosserie sélectionnée n\'est pas valide.');
        }
        $fuel = \Vehicle\FuelManager::fetchFuel($db, $fuelId);
        if(is_a($fuel, '\Exception')){
            throw new Exception('Le carburant sélectionnée n\'est pas valide.');
        }
        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception')){
            throw new \Exception('Impossible de trouver la devise demandée.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, cohérence des données
        //On va créer les objets nécessaires pour le véhicule
        //La motorisation :
        $engine = \Vehicle\EngineManager::fetchEngineByName($db, $engineName);
        //Si elle n'existe pas encore, on la créé
        if(is_a($engine, '\Exception')){
            $engine = new \Vehicle\Engine(0, $engineName);
            $insertEngine = \Vehicle\EngineManager::insertEngine($db, $engine);
            if(is_a($insertEngine, '\Exception')){
                throw new \Exception($insertEngine->getMessage(), $insertEngine->getCode());
            }
            //Et on le récupère
            $engine = $insertEngine;
        }

        //Puis la transmission
        $transmission = \Vehicle\TransmissionManager::fetchTransmissionByName($db, $transmissionName);
        //Si elle n'existe pas encore, on la créé
        if(is_a($transmission, '\Exception')){
            $transmission = new \Vehicle\Transmission(0, $transmissionName);
            $insertTransmission = \Vehicle\TransmissionManager::insertTransmission($db, $transmission);
            if(is_a($insertTransmission, '\Exception')){
                throw new \Exception($insertTransmission->getMessage(), $insertTransmission->getCode());
            }
            //Et on le récupère
            $transmission = $insertTransmission;
        }

        //On créé le prix du véhicule en local
        //on récupère le pays d'origine du véhicule via sa finition
        $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
        $country = $finish->getDealer()->getCountry();
        //Calcul du prix HT
        $vat = \Prices\VatManager::fetchVatFromCountry($db, $country->getId());
        $pretaxBuyingPrice = \Prices\VatManager::convertToPretax($buyingPrice, $vat->getAmount());
        $price = new \Prices\Price(0, $pretaxBuyingPrice, $publicPrice, null, $country->getId(), null, $currencyId,
                                   $margin, $maximumDiscount, new DateTime(), $managementFees);

        //On insère le prix en base
        $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
        if(is_a($insertPrice, '\Exception')){
            throw new Exception($insertPrice->getMessage(), $insertPrice->getCode());
        }
        $price = $insertPrice;


        //On créé ensuite le véhicule en local
        $vehicle = new \Vehicle\Details(0, $dynamicalPower, $fiscalPower, $engineSize, $co2, $sitsAmount, $doorsAmount,
                                        $engine, $transmission, $bodywork, null, $finishId, $gearbox, $fuel, null,
                                        $price->getId(), true);

        //Et on l'insère en base
        $insertDetails = \Vehicle\DetailsManager::insertDetails($db, $vehicle);
        if(is_a($insertDetails, '\Exception')){
            throw new Exception($insertDetails->getMessage(), $insertDetails->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Création du véhicule effectuée avec succès !');

        return true;
    }
    catch(\Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param int $detailsId
 *
 * @return bool
 */
function disableVehicle(int $detailsId):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        $details = \Vehicle\DetailsManager::fetchDetails($db, $detailsId);
        if(is_a($details, '\Exception')){
            throw new \Exception('Impossible de trouver le véhicule demandé.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Désactivation de la finition
        $details->disableVehicle();

        //Hydrate de la BDD
        $hydrateDetails = \Vehicle\DetailsManager::hydrateDetails($db, $details);
        if(is_a($hydrateDetails, '\Exception')){
            throw new \Exception($hydrateDetails->getMessage(), $hydrateDetails->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Desactivation du véhicule effectuée avec succès !');

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
 * @param int $detailsId
 *
 * @return bool
 */
function enableVehicle(int $detailsId):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        $details = \Vehicle\DetailsManager::fetchDetails($db, $detailsId);
        if(is_a($details, '\Exception')){
            throw new \Exception('Impossible de trouver le véhicule demandé.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Désactivation de la finition
        $details->enableVehicle();

        //Hydrate de la BDD
        $hydrateDetails = \Vehicle\DetailsManager::hydrateDetails($db, $details);
        if(is_a($hydrateDetails, '\Exception')){
            throw new \Exception($hydrateDetails->getMessage(), $hydrateDetails->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Activation du véhicule effectuée avec succès !');

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
 * Modification d'un véhicule en base
 * /!\ ATTENTION, définition en dur d'une variable par défaut :
 * $margin : 7 (%)
 * $managementFees : 250 € HT
 *
 * @param array $data
 *
 * @return bool
 */
function modifyVehicle(array $data):bool{
    $margin = 7;
    $managementFees = 250;

    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $detailsId = (int)$data['id'];
        $engineSize = (float)$data['engineSize'];
        $engineName = (string)$data['engine'];
        $dynamicalPower = (int)$data['dynamicalPower'];
        $co2 = (int)$data['co2'];
        $fiscalPower = (int)$data['fiscalPower'];
        $transmissionName = (string)$data['transmission'];
        $doorsAmount = (int)$data['doorsAmount'];
        $sitsAmount = (int)$data['sitsAmount'];
        $gearboxId = (int)$data['gearbox'];
        $bodyworkId = (int)$data['bodywork'];
        $fuelId = (int)$data['fuel'];
        $buyingPrice = (float)$data['buyingPrice'];
        $currencyId = (int)$data['currencyId'];
        $publicPrice = (float)$data['publicPrice'];
        $maximumDiscount = (float)$data['maximumDiscount'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($detailsId) || empty($engineSize) || empty($engineName) || empty($dynamicalPower) || empty($co2)
            || empty($fiscalPower) || empty($transmissionName) || empty($doorsAmount) || empty($sitsAmount) || empty($gearboxId)
            || empty($bodyworkId) || empty($fuelId) || empty($buyingPrice) || empty($currencyId) || !isset($publicPrice) || !isset($maximumDiscount)){
            throw new Exception('Erreur, un des champs requis n\'est pas présent.');
        }

        $vehicle = \Vehicle\DetailsManager::fetchDetails($db, $detailsId);
        if(is_a($vehicle, '\Exception')){
            throw new Exception('Le véhicule sélectionné n\'est pas valide');
        }
        $gearbox = \Vehicle\GearboxManager::fetchGearbox($db, $gearboxId);
        if(is_a($gearbox, '\Exception')){
            throw new Exception('La boite de vitesse sélectionnée n\'est pas valide.');
        }
        $bodywork = \Vehicle\BodyworkManager::fetchBodywork($db, $bodyworkId);
        if(is_a($bodywork, '\Exception')){
            throw new Exception('La carrosserie sélectionnée n\'est pas valide.');
        }
        $fuel = \Vehicle\FuelManager::fetchFuel($db, $fuelId);
        if(is_a($fuel, '\Exception')){
            throw new Exception('Le carburant sélectionnée n\'est pas valide.');
        }
        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception')){
            throw new \Exception('Impossible de trouver la devise demandée.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, données cohérentes
        //On va créer ou récupérer les objets nécessaires pour le véhicule
        //La motorisation :
        $engine = \Vehicle\EngineManager::fetchEngineByName($db, $engineName);
        //Si elle n'existe pas encore, on la créé
        if(is_a($engine, '\Exception')){
            $engine = new \Vehicle\Engine(0, $engineName);
            $insertEngine = \Vehicle\EngineManager::insertEngine($db, $engine);
            if(is_a($insertEngine, '\Exception')){
                throw new \Exception($insertEngine->getMessage(), $insertEngine->getCode());
            }
            //Et on le récupère
            $engine = $insertEngine;
        }

        //Puis la transmission
        $transmission = \Vehicle\TransmissionManager::fetchTransmissionByName($db, $transmissionName);
        //Si elle n'existe pas encore, on la créé
        if(is_a($transmission, '\Exception')){
            $transmission = new \Vehicle\Transmission(0, $transmissionName);
            $insertTransmission = \Vehicle\TransmissionManager::insertTransmission($db, $transmission);
            if(is_a($insertTransmission, '\Exception')){
                throw new \Exception($insertTransmission->getMessage(), $insertTransmission->getCode());
            }
            //Et on le récupère
            $transmission = $insertTransmission;
        }

        //On récupère le prix en base
        $actualPrice = \Prices\PriceManager::fetchPrice($db, $vehicle->getPriceId());
        //On converti le prix entré en HT
        //on récupère le pays d'origine du véhicule via sa finition
        $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $vehicle->getFinishId());
        $country = $finish->getDealer()->getCountry();
        //Calcul du prix HT
        $vat = \Prices\VatManager::fetchVatFromCountry($db, $country->getId());
        $pretaxBuyingPrice = \Prices\VatManager::convertToPretax($buyingPrice, $vat->getAmount());
        //Si une donnée a changé
        //l'arrondi des prix HT est là pour éviter de croire a un changement a cause de la virgule
        //Exemple : 13.66669 (en base) != 13.6666666666669 (fraichement calculé) alors que le prix n'a surement pas changé
        if( round($actualPrice->getPretaxBuyingPrice(), 2) != round($pretaxBuyingPrice, 2)
            || $actualPrice->getCurrencyId() != $currencyId
            || $actualPrice->getPostTaxesPublicPrice() != $publicPrice
            || $actualPrice->getMaximumDiscount() != $maximumDiscount
        ){
            //On recréé un nouveau prix
            $newPrice = new \Prices\Price(0, $pretaxBuyingPrice, $publicPrice, null, $country->getId(), null, $currencyId,
                                          $margin, $maximumDiscount, new DateTime(), $managementFees);
            //On insère le prix en base
            $insertPrice = \Prices\PriceManager::insertPrice($db, $newPrice);
            if(is_a($insertPrice, '\Exception')){
                throw new Exception($insertPrice->getMessage(), $insertPrice->getCode());
            }
            $price = $insertPrice;
        }
        else{
            $price = $actualPrice;
        }

        //On modifie le véhicule en local
        $vehicle->setEngineSize($engineSize);
        $vehicle->setEngine($engine);
        $vehicle->setDynamicalPower($dynamicalPower);
        $vehicle->setCo2($co2);
        $vehicle->setFiscalPower($fiscalPower);
        $vehicle->setTransmission($transmission);
        $vehicle->setDoorsAmount($doorsAmount);
        $vehicle->setSitsAmount($sitsAmount);
        $vehicle->setGearbox($gearbox);
        $vehicle->setBodywork($bodywork);
        $vehicle->setFuel($fuel);
        $vehicle->setPrice($price);
        $vehicle->setPriceId($price->getId());

        //Et on hydrate la base
        $hydrateDetails = \Vehicle\DetailsManager::hydrateDetails($db, $vehicle);
        if(is_a($hydrateDetails, '\Exception')){
            throw new Exception($hydrateDetails->getMessage(), $hydrateDetails->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Modification du véhicule effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

function deleteVehicle(int $vehicleId):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        $vehicle = \Vehicle\DetailsManager::fetchDetails($db, $vehicleId);
        if(is_a($vehicle, '\Exception')){
            throw $vehicle;
        }

        $deleteVehicle = \Vehicle\DetailsManager::deleteDetails($db, $vehicleId);
        if(is_a($deleteVehicle, '\Exception')){
            throw $deleteVehicle;
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Suppression du véhicule effectuée avec succès !');

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
function createColor(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $finishId = (int)$data['finishId'];
        $name = (string)$data['name'];
        $denomination = (string)$data['denomination'];
        $biTone = (bool)$data['bi-tone'];
        $postTaxesBuyingPrice = (float)$data['buyingPrice'];
        $currencyId = (int)$data['currencyId'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($finishId) || empty($name) || !isset($denomination) || !isset($biTone) || !isset($postTaxesBuyingPrice) || !isset($currencyId)){
            throw new Exception('Erreur, un des champs requis n\'est pas renseigné.');
        }

        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception')){
            throw new \Exception('Impossible de trouver la devise demandée.');
        }

        $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
        if(is_a($finish, '\Exception')){
            throw new \Exception('Impossible de trouver la finition demandée.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, cohérence des données
        //Création de la couleur en local
        $color = new \Vehicle\ExternalColor(0, $biTone, $name, $denomination);
        //On regarde si la couleur existe déjà en base
        $searchColor = \Vehicle\ExternalColorManager::fetchColorByInformation($db, $color);
        //Si elle n'existe pas
        if(is_a($searchColor, '\Exception')){
            //On la créé en base
            $insertColor = \Vehicle\ExternalColorManager::insertColor($db, $color);
            if(is_a($insertColor, '\Exception')){
                throw new \Exception($insertColor->getMessage(), $insertColor->getCode());
            }
            $color = $insertColor;
        }
        //Si elle existe
        else{
            $color = $searchColor;
        }

        //On regarde si la couleur existe déjà pour cette finition
        $finishHasThisColor = \Vehicle\FinishManager::doesFinishHaveThisColor($db, $finishId, $color->getId());
        if($finishHasThisColor){
            throw new Exception('Erreur, cette couleur est déjà disponible pour cette finition.');
        }

        //On créé le prix de la couleur en local si il n'est pas null
        if($postTaxesBuyingPrice != 0){
            //on récupère le pays d'origine du véhicule via sa finition
            $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
            $country = $finish->getDealer()->getCountry();
            //Calcul du prix HT
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $pretaxBuyingPrice = \Prices\VatManager::convertToPretax($postTaxesBuyingPrice, $vat->getAmount());
            $price = new \Prices\Price(0, $pretaxBuyingPrice, 0.0, null, $country->getId(), null, $currencyId,
                                       0.0, 0.0, new DateTime());

            //On insère le prix en base
            $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
            if(is_a($insertPrice, '\Exception')){
                throw new Exception($insertPrice->getMessage(), $insertPrice->getCode());
            }
            $price = $insertPrice;
            $priceId = $price->getId();
        }
        else{
            $price = null;
            $priceId = null;
        }

        //Puis on insère la couleur pour cette finition en base
        $insertColorForFinish = \Vehicle\FinishManager::addExternalColor($db, $finishId, $color->getId(), $priceId);
        if(is_a($insertColorForFinish, '\Exception')){
            throw new Exception($insertColorForFinish->getMessage(), $insertColorForFinish->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Création de la couleur effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function modifyColor(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $assocId = (int)$data['id'];
        $name = (string)$data['name'];
        $denomination = (string)$data['denomination'];
        $biTone = (bool)$data['bi-tone'];
        $postTaxesBuyingPrice = (float)$data['buyingPrice'];
        $currencyId = (int)$data['currencyId'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($assocId) || empty($name) || !isset($denomination) || !isset($biTone) || !isset($postTaxesBuyingPrice) || !isset($currencyId)){
            throw new Exception('Erreur, un des champs requis n\'est pas renseigné.');
        }

        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception')){
            throw new \Exception('Impossible de trouver la devise demandée.');
        }

        $assoc = \Vehicle\FinishManager::fetchAssocExternalColor($db, $assocId);
        if(is_a($assoc, '\Exception')){
            throw new \Exception('Impossible de trouver l\'association demandée.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, cohérence des données
        //Création de la couleur en local
        $color = new \Vehicle\ExternalColor(0, $biTone, $name, $denomination);
        //On regarde si la couleur existe déjà en base
        $searchColor = \Vehicle\ExternalColorManager::fetchColorByInformation($db, $color);
        //Si elle n'existe pas
        if(is_a($searchColor, '\Exception')){
            //On la créé en base
            $insertColor = \Vehicle\ExternalColorManager::insertColor($db, $color);
            if(is_a($insertColor, '\Exception')){
                throw new \Exception($insertColor->getMessage(), $insertColor->getCode());
            }
            $color = $insertColor;
        }
        //Si elle existe
        else{
            $color = $searchColor;
        }

        //On créé le prix de la couleur en local si il n'est pas null
        if($postTaxesBuyingPrice != 0){
            //on récupère le pays d'origine du véhicule via sa finition
            /** @var \Vehicle\Finish $finish */
            $finish = $assoc['finish'];
            /** @var \Prices\Price $price */
            $price = $assoc['price'];
            $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finish->getId());
            $country = $finish->getDealer()->getCountry();
            //Calcul du prix HT
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $pretaxBuyingPrice = \Prices\VatManager::convertToPretax($postTaxesBuyingPrice, $vat->getAmount());
            //Si le prix a changé
            if(round($pretaxBuyingPrice, 4) != round($price->getPretaxBuyingPrice(), 4)){
                $price = new \Prices\Price(0, $pretaxBuyingPrice, 0.0, null, $country->getId(), null, $currencyId,
                                           0.0, 0.0, new DateTime());

                //On insère le prix en base
                $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
                if(is_a($insertPrice, '\Exception')){
                    throw new Exception($insertPrice->getMessage(), $insertPrice->getCode());
                }

                $price = $insertPrice;
                $priceId = $price->getId();
            }
            //Si le prix est identique
            else{
                $priceId = $price->getId();
            }
        }
        //Si pas de prix entré
        else{
            $price = null;
            $priceId = null;
        }

        //On update ensuite l'association
        $updateAssoc = \Vehicle\FinishManager::updateExternalColor($db, $assocId, $color->getId(), $priceId);
        if(is_a($updateAssoc, '\Exception')){
            throw new Exception($updateAssoc->getMessage(), $updateAssoc->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Modification de la couleur effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return $e;
    }
}

/**
 * @param int $assocId
 *
 * @return bool
 */
function deleteColor(int $assocId):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        $assoc = \Vehicle\FinishManager::fetchAssocExternalColor($db, $assocId);
        if(is_a($assoc, '\Exception')){
            throw new \Exception('Impossible de trouver l\'association demandée.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //On supprime la jointure en base simplement
        $deleteAssoc = \Vehicle\FinishManager::removeExternalColor($db, $assocId);
        if(is_a($deleteAssoc, '\Exception')){
            throw new \Exception($deleteAssoc->getMessage(), $deleteAssoc->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Suppression de la couleur effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function createRim(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $finishId = (int)$data['finishId'];
        $name = (string)$data['name'];
        $size = (int)$data['size'];
        $type = (string)$data['type'];
        $postTaxesBuyingPrice = (float)$data['buyingPrice'];
        $currencyId = (int)$data['currencyId'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($finishId) || !isset($name) || empty($size) || empty($type) || !isset($postTaxesBuyingPrice) || !isset($currencyId)){
            throw new Exception('Un des champs requis n\'est pas présent.');
        }

        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception')){
            throw new \Exception('Impossible de trouver la devise demandée.');
        }

        $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
        if(is_a($finish, '\Exception')){
            throw new \Exception('Impossible de trouver la finition demandée.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, cohérence des données
        //Création de l'objet en local
        $rimModel = new \Vehicle\RimModel(0, $name, 0, $type, $size, $size);

        //On regarde si ce type de jante existe en base
        $searchRim = \Vehicle\RimModelManager::searchRim($db, $type, $size);
        //Si les jantes n'existent pas
        if(is_a($searchRim, '\Exception')){
            //On insère en base
            $insertRim = \Vehicle\RimModelManager::insertRim($db, $rimModel);
            if(is_a($insertRim, '\Exception')){
                throw new Exception($insertRim->getMessage(), $insertRim->getCode());
            }
            //Et on récupère son ID
            $rimModel->setRimId($insertRim->getRimId());
        }
        //si il existe
        else{
            $rimModel->setRimId($searchRim->getRimId());
        }

        //On regarde ensuite si ce modèle de jante existe
        $searchRimModel = \Vehicle\RimModelManager::searchRimModel($db, $name, $rimModel->getRimId());
        //Si ce modèle n'existe pas
        if(is_a($searchRimModel, '\Exception')){
            //Et on insère en base
            $insertRimModel = \Vehicle\RimModelManager::insertRimModel($db, $rimModel);
            if(is_a($insertRimModel, '\Exception')){
                throw new Exception($insertRimModel->getMessage(), $insertRimModel->getCode());
            }
            //Et on récupère son ID
            $rimModel->setRimId($insertRimModel->getId());
        }
        //si il existe
        else{
            $rimModel->setId($searchRimModel->getId());
        }

        //On regarde ensuite si cette finition possède ces jantes
        $finishHasRims = \Vehicle\FinishManager::doesFinishHaveThoseRims($db, $rimModel->getId(), $finishId);
        //Si cette finition n'a pas ces jantes actuellement
        if($finishHasRims){
            throw new Exception('Erreur, cette finition possède déjà ces jantes. Veuillez les éditer à la place.');
        }

        //On créé le prix de la couleur en local si il n'est pas null
        if($postTaxesBuyingPrice != 0){
            //on récupère le pays d'origine du véhicule via sa finition
            $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
            $country = $finish->getDealer()->getCountry();
            //Calcul du prix HT
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $pretaxBuyingPrice = \Prices\VatManager::convertToPretax($postTaxesBuyingPrice, $vat->getAmount());
            $price = new \Prices\Price(0, $pretaxBuyingPrice, 0.0, null, $country->getId(), null, $currencyId,
                                       0.0, 0.0, new DateTime());

            //On insère le prix en base
            $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
            if(is_a($insertPrice, '\Exception')){
                throw new Exception($insertPrice->getMessage(), $insertPrice->getCode());
            }
            $price = $insertPrice;
            $priceId = $price->getId();
        }
        else{
            $price = null;
            $priceId = null;
        }

        //Puis on insère les infos dans la table de jointures
        $insertRim = null;
        $insertRim = \Vehicle\FinishManager::addRims($db, $rimModel->getId(), $finishId, $priceId);
        if(is_a($insertRim, '\Exception')){
            throw new Exception($insertRim->getMessage(), $insertRim->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Création des jantes effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param array $data
 *
 * @return bool
 */
function modifyRim(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $assocId = (int)$data['id'];
        $name = (string)$data['name'];
        $size = (int)$data['size'];
        $type = (string)$data['type'];
        $postTaxesBuyingPrice = (float)$data['buyingPrice'];
        $currencyId = (int)$data['currencyId'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($assocId) || !isset($name) || empty($size) || empty($type) || !isset($postTaxesBuyingPrice) || !isset($currencyId)){
            throw new Exception('Un des champs requis n\'est pas présent.');
        }

        $assoc = \Vehicle\FinishManager::fetchAssocRim($db, $assocId);
        if(is_a($assoc, '\Exception')){
            throw new Exception('Impossible de trouver l\'association demandée');
        }

        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception')){
            throw new \Exception('Impossible de trouver la devise demandée.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, cohérence des données
        //Création de l'objet en local
        $rimModel = new \Vehicle\RimModel(0, $name, 0, $type, $size, $size);

        //On récupère la définition des jantes
        $searchRim = \Vehicle\RimModelManager::searchRim($db, $type, $size);
        //Si elles n'existent pas
        if(is_a($searchRim, '\Exception')){
            //On insère en base
            $insertRim = \Vehicle\RimModelManager::insertRim($db, $rimModel);
            if(is_a($insertRim, '\Exception')){
                throw new Exception($insertRim->getMessage(), $insertRim->getCode());
            }
            //Et on récupère son ID
            $rimModel->setRimId($insertRim->getRimId());
        }
        //si il existe
        else{
            $rimModel->setRimId($searchRim->getRimId());
        }

        //On regarde ensuite si ce modèle de jante existe
        $searchRimModel = \Vehicle\RimModelManager::searchRimModel($db, $name, $rimModel->getRimId());
        //Si ce modèle n'existe pas
        if(is_a($searchRimModel, '\Exception')){
            //Et on insère en base
            $insertRimModel = \Vehicle\RimModelManager::insertRimModel($db, $rimModel);
            if(is_a($insertRimModel, '\Exception')){
                throw new Exception($insertRimModel->getMessage(), $insertRimModel->getCode());
            }
            //Et on récupère son ID
            $rimModel->setRimId($insertRimModel->getId());
        }
        //si il existe
        else{
            $rimModel->setId($searchRimModel->getId());
        }

        //On créé le prix de la couleur en local si il n'est pas null
        if($postTaxesBuyingPrice != 0){
            //on récupère le pays d'origine du véhicule via sa finition
            /** @var \Vehicle\Finish $finish */
            $finish = $assoc['finish'];
            /** @var \Prices\Price $price */
            $price = $assoc['price'];
            $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finish->getId());
            $country = $finish->getDealer()->getCountry();
            //Calcul du prix HT
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $pretaxBuyingPrice = \Prices\VatManager::convertToPretax($postTaxesBuyingPrice, $vat->getAmount());
            //Si le prix a changé ou qu'il n'y en avait pas
            if($price == null || (round($pretaxBuyingPrice, 4) != round($price->getPretaxBuyingPrice(), 4))){
                $price = new \Prices\Price(0, $pretaxBuyingPrice, 0.0, null, $country->getId(), null, $currencyId,
                                           0.0, 0.0, new DateTime());

                //On insère le prix en base
                $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
                if(is_a($insertPrice, '\Exception')){
                    throw new Exception($insertPrice->getMessage(), $insertPrice->getCode());
                }

                $price = $insertPrice;
                $priceId = $price->getId();
            }
            //Si le prix est identique
            else{
                $priceId = $price->getId();
            }
        }
        //Si pas de prix entré
        else{
            $price = null;
            $priceId = null;
        }

        //On update ensuite l'association
        $updateAssoc = \Vehicle\FinishManager::updateRim($db, $assocId, $rimModel->getId(), $priceId);
        if(is_a($updateAssoc, '\Exception')){
            throw new Exception($updateAssoc->getMessage(), $updateAssoc->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Modification des jantes effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param int $assocId
 *
 * @return bool
 */
function deleteRims(int $assocId):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        $assoc = \Vehicle\FinishManager::fetchAssocRim($db, $assocId);
        if(is_a($assoc, '\Exception')){
            throw new \Exception('Impossible de trouver l\'association demandée.');
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //On supprime la jointure en base simplement
        $deleteAssoc = \Vehicle\FinishManager::removeRims($db, $assocId);
        if(is_a($deleteAssoc, '\Exception')){
            throw new \Exception($deleteAssoc->getMessage(), $deleteAssoc->getCode());
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Suppression des jantes effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param array $data
 *
 * @return bool
 * @throws Exception|\Prices\Currency
 * @throws Exception|\Prices\Price
 * @throws Exception|\Vehicle\ExternalColor
 * @throws Exception|\Vehicle\Finish
 * @throws Exception|\Vehicle\Pack
 * @throws Exception|\Vehicle\RimModel
 * @throws bool|Exception
 */
function createPack(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $finishId = (int)$data['finishId'];
        $name = (string)$data['name'];
        $equipmentsIdsArray = $data['equipment'];
        $colorId = (int)$data['color'];
        $rimsId = (int)$data['rim'];
        $postTaxesBuyingPrice = (float)$data['buyingPrice'];
        $currencyId = (int)$data['currencyId'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($finishId) || empty($name) || !isset($equipmentsIdsArray) || !isset($colorId) || !isset($rimsId) || !isset($postTaxesBuyingPrice) || !isset($currencyId))
            throw new Exception('Un des champs requis n\'est pas présent.');

        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception'))
            throw $currency;

        $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
        if(is_a($finish, '\Exception'))
            throw $finish;

        if(!\Vehicle\EquipmentManager::doTheyExist($db, $equipmentsIdsArray))
            throw new \Exception('Un ou plusieurs equipements de série choisis n\'existent pas.');
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //arrivée ici, Cohérence des données requises
        //On créé le pack en local
        $pack = new \Vehicle\Pack(0, $name, array(), $equipmentsIdsArray, null, $colorId, null, $rimsId, null);

        //On créé le prix de la couleur en local si il n'est pas null
        if($postTaxesBuyingPrice != 0){
            //on récupère le pays d'origine du véhicule via sa finition
            $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
            $country = $finish->getDealer()->getCountry();
            //Calcul du prix HT
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $pretaxBuyingPrice = \Prices\VatManager::convertToPretax($postTaxesBuyingPrice, $vat->getAmount());
            $price = new \Prices\Price(0, $pretaxBuyingPrice, 0.0, null, $country->getId(), null, $currencyId,
                                       0.0, 0.0, new DateTime());

            //On insère le prix en base
            $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
            if(is_a($insertPrice, '\Exception')){
                throw $insertPrice;
            }
            $price = $insertPrice;
            $priceId = $price->getId();
        }
        else{
            $price = null;
            $priceId = 0;
        }

        $pack->setPriceId($priceId);
        //Puis on insère le pack en base
        $insertPack = \Vehicle\PackManager::insertPack($db, $pack, $finishId);
        if(is_a($insertPack, '\Exception')){
            throw $insertPack;
        }

        //On gère ensuite les couleurs/Jantes/Equipements

        //Si le pack contient une couleur
        $color = null;
        if(!empty($colorId)){
            //On va tenter de la récupérer
            $color = \Vehicle\ExternalColorManager::fetchColor($db, $colorId);
            //Si elle n'existe pas
            if(is_a($color, '\Exception'))
                throw $color;

            //Puis on l'insère dans la table de jointures
            $insertColor = \Vehicle\PackManager::insertColor($db, $pack->getId(), $colorId);
            if(is_a($insertColor, '\Exception'))
                throw $insertColor;
        }

        //Si le pack contient des jantes
        $rims = null;
        if(!empty($rimsId)){
            //On va tenter de les récupérer
            $rims = \Vehicle\RimModelManager::fetchRimModel($db, $rimsId);
            //Si elles n'existent pas
            if(is_a($rims, '\Exception'))
                throw $rims;

            //Puis on les insère dans la table de jointures
            $insertRims = \Vehicle\PackManager::insertRims($db, $pack->getId(), $rimsId);
            if(is_a($insertRims, '\Exception'))
                throw $insertRims;
        }

        //Si il y a des équipements dans le pack
        if(!empty($equipmentsIdsArray)){
            if(!\Vehicle\EquipmentManager::doTheyExist($db, $equipmentsIdsArray))
                throw new Exception('Erreur, un des équipements choisi n\'existe pas.');

            //Puis on les insère dans la table de jointures
            $insertEquipments = \Vehicle\PackManager::addEquipments($db, $pack->getId(), $equipmentsIdsArray);
            if(is_a($insertEquipments, '\Exception'))
                throw $insertEquipments;
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Création du pack effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param array $data
 *
 * @return bool
 * @throws Exception|\Prices\Currency
 * @throws Exception|\Vehicle\Pack
 */
function modifyPack(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $packId = (int)$data['id'];
        $name = (string)$data['name'];
        $equipmentsIdsArray = $data['equipment'];
        $colorId = (int)$data['color'];
        $rimsId = (int)$data['rim'];
        $postTaxesBuyingPrice = (float)$data['buyingPrice'];
        $currencyId = (int)$data['currencyId'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($packId) || empty($name) || !isset($equipmentsIdsArray) || !isset($colorId) || !isset($rimsId) || !isset($postTaxesBuyingPrice) || !isset($currencyId))
            throw new Exception('Un des champs requis n\'est pas présent.');

        $pack = \Vehicle\PackManager::fetchPack($db, $packId);
        if(is_a($pack, '\Exception'))
            throw $pack;

        $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
        if(is_a($currency, '\Exception'))
            throw $currency;

        if(!\Vehicle\EquipmentManager::doTheyExist($db, $equipmentsIdsArray))
            throw new \Exception('Un ou plusieurs equipements de série choisis n\'existent pas.');
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, cohérence des données

        /**
         * Dans la mesure où c'est le manager qui va gérer les cas de duplication de jantes dans un pack, on va se contenter
         * ici d'insérer les jantes si on en a demandé, et de les supprimer si on en a pas mis. Quel que soit l'état de la base avant
         */
        //On supprime les anciennes jantes (même si il n'y en avait pas)
        $deleteRims = \Vehicle\PackManager::deleteRims($db, $pack->getId());
        if(is_a($deleteRims, '\Exception'))
            throw $deleteRims;
        //Si a demandé des jantes
        if(!empty($rimsId)){
            //On check l'existence des jantes
            $rims = \Vehicle\RimModelManager::fetchRimModel($db, $rimsId);
            //Si elles n'existent pas
            if(is_a($rims, '\Exception'))
                throw $rims;

            //Puis on les insère dans la table de jointures (Le manager gère les doublons)
            $insertRims = \Vehicle\PackManager::insertRims($db, $pack->getId(), $rimsId);
            if(is_a($insertRims, '\Exception'))
                throw $insertRims;
        }

        /**
         * Ici, on va appliquer le même fonctionnement pour la couleur
         */
        //On supprime simplement l'éventuelle couleur de ce pack
        $deleteColor = \Vehicle\PackManager::deleteColor($db, $pack->getId());
        if(is_a($deleteColor, '\Exception'))
            throw $deleteColor;
        //Si on a demandé une couleur
        if(!empty($colorId)){
            //On vérifie son existence
            $color = \Vehicle\ExternalColorManager::fetchColor($db, $colorId);
            //Si elle n'existe pas
            if(is_a($color, '\Exception'))
                throw $color;

            //Puis on insère la couleur dans la table de jointures
            $insertColor = \Vehicle\PackManager::insertColor($db, $pack->getId(), $colorId);
            if(is_a($insertColor, '\Exception'))
                throw $insertColor;
        }

        /**
         * Viennent maintenant les équipements. Il faut les classer en 2 catégories
         * 1 : Les équipements à rajouter au pack
         * 2 : Les équipements à supprimer du pack
         */
        $equipmentsToAdd = array();
        $equipmentsToRemove = array();

        $actualEquipments = \Vehicle\PackManager::fetchEquipments($db, $pack->getId());
        $actualEquipmentsIds = array();
        foreach($actualEquipments as $equipment){
            $actualEquipmentsIds[] = $equipment->getId();
        }

        //Parcourt du tableau d'équipements demandés
        foreach($equipmentsIdsArray as $equipmentId){
            //Si on ne retrouve pas cet équipement dans l'ancien tableau
            if(!in_array((int)$equipmentId, $actualEquipmentsIds)){
                //Il est à rajouter
                $equipmentsToAdd[] = (int)$equipmentId;
            }
        }
        //Parcourt du tableau d'équipements déjà présents
        foreach($actualEquipmentsIds as $equipmentId){
            //Si on ne retrouve pas cet équipement dans ceux demandés
            if(!in_array((int)$equipmentId, $equipmentsIdsArray)){
                //Il est à supprimer
                $equipmentsToRemove[] = (int)$equipmentId;
            }
        }

        //On supprime les équipements à supprimer
        if(!empty($equipmentsToRemove)){
            $deleteEquipments = \Vehicle\PackManager::deleteEquipments($db, $pack->getId(), $equipmentsToRemove);
            if(is_a($deleteEquipments, '\Exception')){
                throw $deleteEquipments;
            }
        }
        if(!empty($equipmentsToAdd)){
            //Et on ajoute ceux à rajouter
            $addEquipments = \Vehicle\PackManager::addEquipments($db, $pack->getId(), $equipmentsToAdd);
            if(is_a($addEquipments, '\Exception')){
                throw $addEquipments;
            }
        }

        //On créé le prix de la couleur en local si il n'est pas null
        if($postTaxesBuyingPrice != 0){
            //on récupère le pays d'origine du véhicule via sa finition
            $finishId = \Vehicle\FinishManager::whoGotThisPack($db, $pack->getId());
            $price = \Prices\PriceManager::fetchPrice($db, $pack->getPriceId());
            $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);
            $country = $finish->getDealer()->getCountry();
            //Calcul du prix HT
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $pretaxBuyingPrice = \Prices\VatManager::convertToPretax($postTaxesBuyingPrice, $vat->getAmount());
            //Si le prix a changé OU qu'il n'y en avait pas avant
            if(is_a($price, '\Exception') || (round($pretaxBuyingPrice, 4) != round($price->getPretaxBuyingPrice(), 4)) || $price->getCurrencyId() != $currencyId){
                $price = new \Prices\Price(0, $pretaxBuyingPrice, 0.0, null, $country->getId(), null, $currencyId,
                                           0.0, 0.0, new DateTime());

                //On insère le prix en base
                $insertPrice = \Prices\PriceManager::insertPrice($db, $price);
                if(is_a($insertPrice, '\Exception')){
                    throw new Exception($insertPrice->getMessage(), $insertPrice->getCode());
                }

                $price = $insertPrice;
                $priceId = $price->getId();
            }
            //Si le prix est identique
            else{
                $priceId = $price->getId();
            }
        }
        //Si pas de prix entré
        else{
            $price = null;
            $priceId = null;
        }

        //On hydrate ensuite l'objet en local
        $pack->setName($name);
        $pack->setPriceId($priceId);

        //Et on hydrate la base
        $hydratePack = \Vehicle\PackManager::hydratePack($db, $pack);
        if(is_a($hydratePack, '\Exception'))
            throw $hydratePack;

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Modification du pack effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}


/**
 * @param int $packId
 *
 * @return bool
 * @throws Exception|\Vehicle\Pack
 * @throws bool|Exception
 */
function deletePack(int $packId):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        $pack = \Vehicle\PackManager::fetchPack($db, $packId);
        if(is_a($pack, '\Exception'))
            throw $pack;
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //On supprime la jointure en base simplement
        $deletePack = \Vehicle\PackManager::deletePack($db, $packId);
        if(is_a($deletePack, '\Exception'))
            throw $deletePack;

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Suppression du pack effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param int   $vehicleId
 * @param array $files
 *
 * @return bool
 * @throws Exception|\Vehicle\Details
 */
function addImage(int $vehicleId, array $files):bool{
    try{
        $db = databaseConnection();
        $vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $vehicleId);
        $db = null;
        if(is_a($vehicle, '\Exception')){
            throw $vehicle;
        }

        $finish = $vehicle->getFinish();
        $finishName = $finish->getName();
        $model = $finish->getModel();
        $modelName = $model->getName();
        $brand = $model->getBrand();
        $brandName = $brand->getName();
        $bodywork = $vehicle->getBodywork();
        $bodyworkName = $bodywork->getName();
        $doorsAmount = $vehicle->getDoorsAmount();


        $files = $files['createVehicleImage'];

        $extension = $files['name'];
        $searchExt = explode('.', $extension);
        $ext = $searchExt[count($searchExt) - 1];

        $pathOnServer = $_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/'.$brandName.'/'.$modelName.'/'.$finishName;
        $fileName = $bodyworkName.'_'.$doorsAmount.'.'.$ext;
        $type = $files['type'];
        $tmp_name = $files['tmp_name'];
        $error = $files['error'];
        $size = (int)$files['size'];

        $fileToUpload = new FileToUpload($pathOnServer, $fileName, $type, $tmp_name, $error, $size);
        $uploadFile = FileToUploadManager::uploadFile($fileToUpload);
        if(is_a($uploadFile, 'Exception')){
            throw $uploadFile;
        }

        $_SESSION['returnAction'] = array(1, 'Upload du fichier effectué avec succés.');

        return true;
    }
    catch(Exception $e){
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());

        return false;
    }
}

/**
 * @param array $data
 *
 * @return bool
 * @throws Exception|\Offers\Offer
 * @throws Exception|\Vehicle\Details
 * @throws bool|Exception
 */
function createOffer(array $data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $vehicleId = (int)$data['vehicleId'];
        $equipmentsIds = isset($data['equipments']) ? $data['equipments'] : array();
        $packsIds = isset($data['packs']) ? $data['packs'] : array();
        $colorId = (int)$data['color'];
        $rimsId = (int)$data['rims'];
        $dealerMargin = (float)$data['dealerMargin'];
        /** @var \Users\SocietyClient|\Users\IndividualClient $client */
        $client = $_SESSION['selectedClient'];
        /** @var \Users\User $user */
        $user = $_SESSION['user'];
        $structure = $user->getStructure();
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($client))
            throw new Exception('Aucun client sélectionné.');

        if(empty($user))
            throw new Exception('Vous n\'etes bizarrement pas connecté. oO');

        if(empty($structure))
            throw new Exception('Votre structure n\'est pas valide...');

        if(empty($vehicleId) || !isset($equipmentsIds, $packsIds, $colorId, $rimsId, $dealerMargin))
            throw new Exception('Erreur lors de l\'envoi du formulaire.');

        //On récupère la définition complete du véhicule
        $vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $vehicleId);
        if(is_a($vehicle, '\Exception'))
            throw $vehicle;

        //Pour chaque équipement on va tester la possibilité de l'avoir dans cette finition
        if(!empty($equipmentsIds) && !\Vehicle\FinishManager::doesFinishHaveTheseOptions($db, $vehicle->getFinishId(), $equipmentsIds))
            throw new Exception('Erreur lors du choix des options.');

        //Pareil pour les packs
        if(!empty($packsIds) && !\Vehicle\FinishManager::doesFinishHaveThesePacks($db, $vehicle->getFinishId(), $packsIds))
            throw new Exception('Erreur lors du choix des packs.');

        //On vérifie la couleur
        if(!empty($colorId) && !\Vehicle\FinishManager::doesFinishHaveThisColor($db, $vehicle->getFinishId(), $colorId))
            throw new Exception('Erreur lors du choix de la couleur.');

        //Et les jantes
        if(!empty($rimsId) && !\Vehicle\FinishManager::doesFinishHaveThoseRims($db, $vehicle->getFinishId(), $rimsId))
            throw new Exception('Erreur lors du choix des jantes');
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        $structureId = $structure->getId();
        $userId = $user->getId();
        $clientId = $client->getId();
        $dealer = $user->getStructure();
        $dealerDepartment = $dealer->getDepartment();

        //Arrivée ici, les données sont cohérentes
        /***********RECUPERATION ET DEFINITION DES VARIABLES MANQUANTES***************/
        //On va créer le numéro de l'offre
        $offerNumber = \Offers\OfferManager::generateOfferNumber($db, $structureId, $userId, $clientId);
        $date = new DateTime();

        $vat = \Prices\VatManager::fetchFrenchVat($db);
        $country = \Prices\CountryManager::fetchCountry($db, $vehicle->getPrice()->getCountryId());
        $vehiclePrice = $vehicle->getPrice();
        $currency = $vehiclePrice->getCurrency();
        $freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $dealerDepartment);
        if($structure->getFreightCharges() === NULL){
            $freightChargesInFrance = $freightCharges->getAmount();
        }
        else{
            $freightChargesInFrance = $structure->getFreightCharges();
        }
        $pretaxBuyingPrice = $vehiclePrice->getPretaxBuyingPrice();
        $pretaxBuyingPriceInEuro = $pretaxBuyingPrice * $currency->getExchangeRate()->getRateToEuro();
        $pretaxTransportedPriceInEuro = $pretaxBuyingPriceInEuro + $country->getFreightCharges()->getAmount();
        $freightChargesToFrance = $country->getFreightCharges()->getAmount();
        $marginAmount = $pretaxBuyingPriceInEuro * $vehiclePrice->getMargin() / 100;
        $managementFees = $vehiclePrice->getManagementFees();
        if(!$structure->getIsPartner()){
            $managementFees = 0;
        }

        $packageProvision = $structure->getPackageProvision();

        $vatRate = $vat->getAmount();
        $finishId = $vehicle->getFinishId();
        /***********RECUPERATION ET DEFINITION DES VARIABLES MANQUANTES***************/

        //On va créer une offre en local
        $offer = new \Offers\Offer(0, $offerNumber, $date, $vehicleId, $pretaxTransportedPriceInEuro, $freightChargesToFrance,
                                   $marginAmount, $managementFees, $dealerMargin, $vatRate, $packageProvision,
                                   $freightChargesInFrance, $clientId, $userId, 1);

        //Puis on insère cette offre en base
        $insertOffer = \Offers\OfferManager::insertOffer($db, $offer);
        if(is_a($insertOffer, '\Exception')){
            throw $insertOffer;
        }
        $offer = $insertOffer;

        //On créé maintenant les options, packs, couleur et jantes
        if(!empty($equipmentsIds)){
            $options = array();
            foreach($equipmentsIds as $itemId){
                $price = \Vehicle\FinishManager::fetchOptionPrice($db, $finishId, $itemId);
                $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                $pretaxBuyingPriceInEuro = $price->getPretaxBuyingPrice() * $currency->getExchangeRate()->getRateToEuro();
                $options[] = new \Offers\Option($itemId, $offer->getId(), $pretaxBuyingPriceInEuro);
            }
            //Et on insère les options en base
            $insertOptions = \Offers\OfferManager::insertMultipleOptions($db, $options);
            if(is_a($insertOptions, '\Exception')){
                throw $insertOptions;
            }
        }
        if(!empty($packsIds)){
            $packs = array();
            foreach($packsIds as $itemId){
                $price = \Vehicle\FinishManager::fetchPackPrice($db, $finishId, $itemId);
                $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                $pretaxBuyingPriceInEuro = $price->getPretaxBuyingPrice() * $currency->getExchangeRate()->getRateToEuro();
                $packs[] = new \Offers\Pack($itemId, $offer->getId(), $pretaxBuyingPriceInEuro);
            }
            //Et on insère les options en base
            $insertPack = \Offers\OfferManager::insertMultiplePacks($db, $packs);
            if(is_a($insertPack, '\Exception')){
                throw $insertPack;
            }
        }
        if(!empty($colorId)){
            $price = \Vehicle\FinishManager::fetchColorPrice($db, $finishId, $colorId);
            if(is_a($price, '\Exception')){
                $pretaxBuyingPriceInEuro = 0.0;
            }
            else{
                $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                $pretaxBuyingPriceInEuro = $price->getPretaxBuyingPrice() * $currency->getExchangeRate()->getRateToEuro();
            }
            $color = new \Offers\Color($colorId, $offer->getId(), $pretaxBuyingPriceInEuro);
            //Et on insère les options en base
            $insertColor = \Offers\OfferManager::insertColor($db, $color);
            if(is_a($insertColor, '\Exception')){
                throw $insertColor;
            }
        }
        if(!empty($rimsId)){
            $price = \Vehicle\FinishManager::fetchRimsPrice($db, $finishId, $rimsId);
            if(is_a($price, '\Exception')){
                $pretaxBuyingPriceInEuro = 0.0;
            }
            else{
                $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                $pretaxBuyingPriceInEuro = $price->getPretaxBuyingPrice() * $currency->getExchangeRate()->getRateToEuro();
            }
            $rims = new \Offers\Rims($rimsId, $offer->getId(), $pretaxBuyingPriceInEuro);
            //Et on insère les options en base
            $insertRims = \Offers\OfferManager::insertRims($db, $rims);
            if(is_a($insertRims, '\Exception')){
                throw $insertRims;
            }
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Création de l\'offre effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param string $offerReference
 * @param array  $postData
 *
 * @return bool
 * @throws Exception|\Offers\Offer
 * @throws Exception|\Users\IndividualClient|\Users\SocietyClient
 * @throws bool|Exception
 */
function transformOfferToCommand(string $offerReference, array $postData):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /***************************RECUPERATION DE L'OFFRE**********************/
        $offer = \Offers\OfferManager::fetchOfferByReference($db, $offerReference);
        if(is_a($offer, '\Exception'))
            throw $offer;
        /** @var \Users\User $user */
        $user = $_SESSION['user'];
        //Récupération du client concerné
        $client = \Users\ClientManager::fetchClient($db, $offer->getClientId());
        if(is_a($client, '\Exception')){
            throw $client;
        }
        /***************************RECUPERATION DE L'OFFRE**********************/

        /***************************RECUPERATION DES DONNEES**********************/
        $invalidClientData = (empty($client->getLastName()) || empty($client->getFirstName()) || empty($client->getPostalAddress())
            || empty($client->getPostalCode()) || empty($client->getTown()));

        $externalColor = (string)$postData['externalColor'];
        $internalColor = (string)$postData['internalColor'];
        if($invalidClientData){
            $firstName = (string)$postData['firstName'];
            $lastName = (string)$postData['lastName'];
            $addressNumber = (int)$postData['addressNumber'];
            $extension = $postData['extension'];
            $streetType = (string)$postData['streetType'];
            $wording = (string)$postData['wording'];
            $postalCode = (string)$postData['postalCode'];
            $town = (string)$postData['town'];
        }
        /***************************RECUPERATION DES DONNEES**********************/

        /***************************VERIFICATION DES DONNEES**********************/
        if(empty($externalColor) || empty($internalColor)
            || ($invalidClientData
                && (empty($firstName) || empty($lastName)
                || empty($addressNumber) || empty($streetType) || empty($wording) || empty($postalCode) || empty($town)
                )
            )
        ){
            throw new Exception('Erreur, un ou plusieurs champs sont manquants.');
        }
        /***************************VERIFICATION DES DONNEES**********************/

        /***************************VERIFICATION DES DROITS**********************/
        //Si l'utilisateur n'est pas admin
        if(!$user->isAdmin()){
            //Si il est dirigeant de ce garage
            if($user->isOwner()){
                $userOffer = \Users\UserManager::fetchUser($db, $offer->getOwnerId());
                //Si la personne connectée n'est pas dirigeante de la structure concernée par l'offre
                if($user->getStructureId() != $userOffer->getStructureId()){
                    throw new Exception('Erreur, vous n\'avez pas les droits pour transformer cette offre.');
                }
            }
            //Si il ne l'est pas
            else{
                //Si la personne connectée n'est pas celle qui a créé cette offre
                if($user->getId() != $offer->getOwnerId()){
                    throw new Exception('Erreur, vous n\'avez pas les droits pour transformer cette offre.');
                }
            }
        }
        /***************************VERIFICATION DES DROITS**********************/

        //Arrivée ici, l'utilisateur est autorisé à transformer l'offre et données cohérentes

        //On Update le client si il était mal renseigné
        if($invalidClientData){
            $client->setFirstName($firstName);
            $client->setLastName($lastName);
            $client->setAddressNumber($addressNumber);
            $client->setAddressExtension($extension);
            $client->setStreetType($streetType);
            $client->setAddressWording($wording);
            $client->setPostalCode($postalCode);
            $client->setTown($town);

            //Puis on hydrate la base
            $hydrateClient = \Users\ClientManager::hydrateClient($db, $client);
            if(is_a($hydrateClient, '\Excpetion'))
                throw $hydrateClient;
        }

        if($offer->getState() > 2){
            throw new Exception('Impossible, l\'offre est déjà signée.');
        }
        if($offer->getState() == 0){
            throw new Exception('Impossible, l\'offre a été annulée.');
        }
        if($offer->getState() == 1){
            //Transformation de l'état de l'offre en local
            $offer->setState(2);

            //Update dans la BDD
            $hydrateOffer = \Offers\OfferManager::hydrateOfferState($db, $offer);
            if(is_a($hydrateOffer, '\Exception')){
                throw $hydrateOffer;
            }
        }

        //Puis on ajoute les couleurs renseignées
        $offer->setExternalColor($externalColor);
        $offer->setInternalColor($internalColor);

        $hydrateOffer = \Offers\OfferManager::addColorsToOffer($db, $offer);
        if(is_a($hydrateOffer, '\Exception')){
            throw $hydrateOffer;
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Transformation de l\'offre effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}

/**
 * @param $data
 *
 * @return bool
 * @throws Exception|\Vehicle\Equipment
 * @throws array|Exception
 */
function createEquipment($data):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /********************RECUPERATION DES DONNEES BRUTES**********************/
        $familyId = (int)$data['familyId'];
        $equipmentName = (string)$data['name'];
        /********************RECUPERATION DES DONNEES BRUTES**********************/

        /********************CHECK DE LA COHERENCE DES DONNEES**********************/
        if(empty($familyId) || empty($equipmentName)){
            throw new Exception('Erreur, un ou plusieurs champs requis ne sont pas présents.');
        }

        $family = \Vehicle\EquipmentManager::fetchFamily($db, $familyId);
        if(is_a($family, '\Exception')){
            throw $family;
        }
        /********************CHECK DE LA COHERENCE DES DONNEES**********************/

        //Arrivée ici, cohérence des données
        //On créé l'équipement en local
        $equipment = new \Vehicle\Equipment(0, $equipmentName, $family['name'], (int)$family['id'], 0, false);

        //Puis on l'insère en base
        $insertEquipment = \Vehicle\EquipmentManager::insertEquipment($db, $equipment);
        if(is_a($insertEquipment, '\Exception')){
            throw $insertEquipment;
        }

        $db->commit();
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Création de l\'équipement effectuée avec succès !');

        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
        $db = null;

        return false;
    }
}