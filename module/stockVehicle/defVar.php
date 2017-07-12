<?php
$pageTitle = 'Stock et Arrivages';
$moreCss = array('jquery-ui.min', 'jquery-ui.structure.min', 'jquery-ui.theme.min');
$moreJs = array('formManager', 'jquery-ui.min');
if(!empty($_GET['category']) && $_GET['category'] == 'offers' && !empty($_GET['action'])
    && ($_GET['action'] == 'view' || $_GET['action'] == 'create') || $_GET['action'] == 'transform')
    $moreJs[] = 'offers_function';


if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    if(!empty($_POST['createVehicle']))
        createVehicle($_POST['createVehicle'], $_FILES['createVehicle']);
    if(!empty($_POST['modifyVehicle']))
        modifyVehicle($_POST['modifyVehicle'], $_FILES['modifyVehicle']);
    if(!empty($_POST['copyVehicle']))
        createVehicle($_POST['copyVehicle'], $_FILES['copyVehicle']);
    if(!empty($_POST['createOffer']['vehicleId']))
        createOffer((int)$_POST['createOffer']['vehicleId']);

    if(!empty($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['vehicleId']))
        deleteVehicle((int)$_GET['vehicleId']);

    if(!empty($_GET['category'])
        && $_GET['category'] == 'offers'
        && !empty($_GET['action'])
        && $_GET['action'] == 'transform'
        && !empty($_GET['offerReference'])
    ){
        $post = empty($_POST['transformOffer']) ? array() : $_POST['transformOffer'];
        transformOfferToCommand($_GET['offerReference'], $post);
    }
}

/**
 * @param array $data
 * @param array $files
 *
 * @return bool
 * @throws Exception|\Vehicle\VehicleInStock
 * @throws array|Exception
 */
function createVehicle(array $data, array $files):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /**********************RECUPERATION DES DONNEES BRUTES************************/
        $brand = (string)$data['brand'];
        $model = (string)$data['model'];
        $finish = (string)$data['finish'];
        $engineSize = (float)$data['engineSize'];
        $engine = (string)$data['engine'];
        $dynamicalPower = (int)$data['dynamicalPower'];
        $modelYear = (string)$data['modelYear'];
        $mileage = (int)$data['mileage'];
        $fuel = (string)$data['fuel'];
        $gearbox = (string)$data['gearbox'];
        $reference = (string)$data['reference'];
        $fiscalPower = (int)$data['fiscalPower'];
        $co2 = (int)$data['co2'];
        $bodywork = (string)$data['bodywork'];
        $transmission = (string)$data['transmission'];
        $usersAmount = (int)$data['usersAmount'];
        $externalColor = (string)$data['externalColor'];
        $hasAccident = (bool)$data['hasAccident'];
        $technicalInspection = (bool)$data['technicalInspection'];
        $maintenanceLog = (bool)$data['maintenanceLog'];
        $equipments = $data['equipments'];
        $suppComments = (string)$data['suppComments'];
        $funding = (string)$data['funding'];
        $warranty = (string)$data['warranty'];
        $price = (float)$data['price'];
        //$sellerMargin = (float)$data['sellerMargin'];
        $buyingPrice = isset($data['buyingPrice']) ? (float)$data['buyingPrice'] : null;
        $vatOnMargin = isset($data['vatOnMargin']) ? (bool)$data['vatOnMargin'] : null;
        $feesAmount = isset($data['feesAmount']) ? (float)$data['feesAmount'] : null;
        $feesDetails = isset($data['feesDetails']) ? (string)$data['feesDetails'] : null;
        $depotSale = (bool)$data['depotSale'];
        $availabilityDate = new DateTime($data['availabilityDate']);
        $images = $files;
        /**********************RECUPERATION DES DONNEES BRUTES************************/

        /**********************CHECK DE LA COHERENCE DES DONNEES************************/
        if(empty($brand) || empty($model) || empty($engineSize) || empty($dynamicalPower)
            || empty($modelYear) || empty($mileage) || empty($price)
            || !isset($finish, $fuel, $gearbox, $reference, $fiscalPower, $co2, $bodywork, $transmission, $usersAmount,
                $externalColor, $hasAccident, $technicalInspection, $maintenanceLog, $equipments, $suppComments,
                $funding, $warranty, $images)
        ){
            throw new Exception('Erreur, un ou plusieurs champs sont manquants.');
        }
        if(!preg_match('#[0-9]{1,2}\/[0-9]{4}#', $modelYear) || !preg_match('#([0-9]{4})#', $modelYear)){
            throw new Exception('L\'année modèle est mal rensignée.');
        }
        /**********************CHECK DE LA COHERENCE DES DONNEES************************/

        //Arrivée ici, données cohérentes
        /** @var \Users\User $user */
        $user = $_SESSION['user'];
        $structureId = $user->getStructureId();

        $insertionDate = new DateTime();

        //Création de la date de MEC
        if(preg_match('#[0-9]{1,2}\/[0-9]{4}#', $modelYear)){
            $information = explode('/', $modelYear);
            $modelDate = new DateTime($information[1].'-'.$information[0].'-01');
        }
        else{
            $modelDate = new DateTime($modelYear.'01-01');
        }
        $isTechnicalInspectionOk = !$technicalInspection;
        $isMaintenanceLogOk = !$maintenanceLog;

        /***************UPLOAD DES IMAGES DU VEHICULE****************/
        $image1 = '';
        $image2 = '';
        $image3 = '';
        $image4 = '';
        if(!empty($images['name']['image1']) ){
            $info = array(
                'name'     => $images['name']['image1'],
                'type'     => $images['type']['image1'],
                'tmp_name' => $images['tmp_name']['image1'],
                'error'    => $images['error']['image1'],
                'size'     => (int)$images['size']['image1']
            );
            $fileName = $insertionDate->format('YmdHis').'_1';

            $image1 = uploadVehicleImage($fileName, $info);
        }
        if(!empty($images['name']['image2'])){
            $info = array(
                'name'     => $images['name']['image2'],
                'type'     => $images['type']['image2'],
                'tmp_name' => $images['tmp_name']['image2'],
                'error'    => $images['error']['image2'],
                'size'     => (int)$images['size']['image2']
            );
            $fileName = $insertionDate->format('YmdHis').'_2';

            $image2 = uploadVehicleImage($fileName, $info);
        }
        if(!empty($images['name']['image3'])){
            $info = array(
                'name'     => $images['name']['image3'],
                'type'     => $images['type']['image3'],
                'tmp_name' => $images['tmp_name']['image3'],
                'error'    => $images['error']['image3'],
                'size'     => (int)$images['size']['image3']
            );
            $fileName = $insertionDate->format('YmdHis').'_3';

            $image3 = uploadVehicleImage($fileName, $info);
        }
        if(!empty($images['name']['image4'])){
            $info = array(
                'name'     => $images['name']['image4'],
                'type'     => $images['type']['image4'],
                'tmp_name' => $images['tmp_name']['image4'],
                'error'    => $images['error']['image4'],
                'size'     => (int)$images['size']['image4']
            );
            $fileName = $insertionDate->format('YmdHis').'_4';

            $image4 = uploadVehicleImage($fileName, $info);
        }
        /***************UPLOAD DES IMAGES DU VEHICULE****************/

        //Trim des équipements
        $tmpArray = array();
        foreach($equipments as $equipment){
            $tmpArray[] = trim($equipment);
        }
        $equipments = $tmpArray;

        //Instanciation en local
        $vehicleInStock = new \Vehicle\VehicleInStock(0, $brand, $model, $finish, $engineSize, $engine, $dynamicalPower, $modelDate, $mileage, $fuel, $gearbox,
                                                      $reference, $fiscalPower, $co2, $bodywork, $transmission, $usersAmount, $externalColor, $hasAccident,
                                                      $isTechnicalInspectionOk, $isMaintenanceLogOk, $equipments, $suppComments, $funding, $warranty, $price, 0,
                                                      $image1, $image2, $image3, $image4, $structureId, $depotSale, $availabilityDate, false, false, 0, $insertionDate,
                                                      0, null, 0, $buyingPrice, $vatOnMargin, $feesAmount, $feesDetails);

        $insertVehicle = \Vehicle\VehicleInStockManager::insertVehicle($db, $vehicleInStock);

        if(is_a($insertVehicle, '\Exception')){
            throw $insertVehicle;
        }

        //On valide la transaction
        $db->commit();
        //Fermeture de la connexion
        $db = null;
        $_SESSION['returnAction'] = array(1, 'Création du véhicule effectuée avec succès !');
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
 * @throws Exception|\Vehicle\VehicleInStock
 * @throws array|Exception
 * @throws bool|Exception
 */
function modifyVehicle(array $data, array $files):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /**********************RECUPERATION DES DONNEES BRUTES************************/
        $vehicleId = (int)$data['vehicleId'];
        $brand = (string)$data['brand'];
        $model = (string)$data['model'];
        $finish = (string)$data['finish'];
        $engineSize = (float)$data['engineSize'];
        $engine = (string)$data['engine'];
        $dynamicalPower = (int)$data['dynamicalPower'];
        $modelYear = (string)$data['modelYear'];
        $mileage = (int)$data['mileage'];
        $fuel = (string)$data['fuel'];
        $gearbox = (string)$data['gearbox'];
        $reference = (string)$data['reference'];
        $fiscalPower = (int)$data['fiscalPower'];
        $co2 = (int)$data['co2'];
        $bodywork = (string)$data['bodywork'];
        $transmission = (string)$data['transmission'];
        $usersAmount = (int)$data['usersAmount'];
        $externalColor = (string)$data['externalColor'];
        $hasAccident = (bool)$data['hasAccident'];
        $technicalInspection = (bool)$data['technicalInspection'];
        $maintenanceLog = (bool)$data['maintenanceLog'];
        $equipments = $data['equipments'];
        $suppComments = (string)$data['suppComments'];
        $funding = (string)$data['funding'];
        $warranty = (string)$data['warranty'];
        $price = (float)$data['price'];
        //$sellerMargin = (float)$data['sellerMargin'];
        $buyingPrice = isset($data['buyingPrice']) ? (float)$data['buyingPrice'] : null;
        $vatOnMargin = isset($data['vatOnMargin']) ? (bool)$data['vatOnMargin'] : null;
        $feesAmount = isset($data['feesAmount']) ? (float)$data['feesAmount'] : null;
        $feesDetails = isset($data['feesDetails']) ? (string)$data['feesDetails'] : null;
        $depotSale = (bool)$data['depotSale'];
        $availabilityDate = new DateTime($data['availabilityDate']);
        $images = $files;
        /**********************RECUPERATION DES DONNEES BRUTES************************/

        /**********************CHECK DE LA COHERENCE DES DONNEES************************/
        if(empty($vehicleId) || empty($brand) || empty($model) || empty($engineSize)
            || empty($dynamicalPower) || empty($modelYear) || empty($mileage) || empty($price)
            || !isset($finish, $fuel, $gearbox, $reference, $fiscalPower, $co2, $bodywork, $transmission, $usersAmount,
                $externalColor, $hasAccident, $technicalInspection, $maintenanceLog, $equipments, $suppComments,
                $funding, $warranty, $images)
        ){
            throw new Exception('Erreur, un ou plusieurs champs sont manquants.');
        }

        $vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
        if(is_a($vehicle, '\Exception')){
            throw $vehicle;
        }

        if(!preg_match('#[0-9]{1,2}\/[0-9]{4}#', $modelYear) || !preg_match('#([0-9]{4})#', $modelYear)){
            throw new Exception('L\'année modèle est mal rensignée.');
        }
        /**********************CHECK DE LA COHERENCE DES DONNEES************************/

        $insertionDate = $vehicle->getInsertionDate();

        /***************UPLOAD DES IMAGES DU VEHICULE****************/
        $image1 = $vehicle->getImage1();
        $image2 = $vehicle->getImage2();
        $image3 = $vehicle->getImage3();
        $image4 = $vehicle->getImage4();
        if(!empty($images['name']['image1']) ){
            $info = array(
                'name'     => $images['name']['image1'],
                'type'     => $images['type']['image1'],
                'tmp_name' => $images['tmp_name']['image1'],
                'error'    => $images['error']['image1'],
                'size'     => (int)$images['size']['image1']
            );
            $fileName = $insertionDate->format('YmdHis').'_1';

            $image1 = uploadVehicleImage($fileName, $info);
        }
        if(!empty($images['name']['image2'])){
            $info = array(
                'name'     => $images['name']['image2'],
                'type'     => $images['type']['image2'],
                'tmp_name' => $images['tmp_name']['image2'],
                'error'    => $images['error']['image2'],
                'size'     => (int)$images['size']['image2']
            );
            $fileName = $insertionDate->format('YmdHis').'_2';

            $image2 = uploadVehicleImage($fileName, $info);
        }
        if(!empty($images['name']['image3'])){
            $info = array(
                'name'     => $images['name']['image3'],
                'type'     => $images['type']['image3'],
                'tmp_name' => $images['tmp_name']['image3'],
                'error'    => $images['error']['image3'],
                'size'     => (int)$images['size']['image3']
            );
            $fileName = $insertionDate->format('YmdHis').'_3';

            $image3 = uploadVehicleImage($fileName, $info);
        }
        if(!empty($images['name']['image4'])){
            $info = array(
                'name'     => $images['name']['image4'],
                'type'     => $images['type']['image4'],
                'tmp_name' => $images['tmp_name']['image4'],
                'error'    => $images['error']['image4'],
                'size'     => (int)$images['size']['image4']
            );
            $fileName = $insertionDate->format('YmdHis').'_4';

            $image4 = uploadVehicleImage($fileName, $info);
        }
        /***************UPLOAD DES IMAGES DU VEHICULE****************/

        //Trim des équipements
        $tmpArray = array();
        foreach($equipments as $equipment){
            $tmpArray[] = trim($equipment);
        }
        $equipments = $tmpArray;

        //Création de la date de MEC
        if(preg_match('#[0-9]{1,2}\/[0-9]{4}#', $modelYear)){
            $information = explode('/', $modelYear);
            $modelDate = new DateTime($information[1].'-'.$information[0].'-01');
        }
        else{
            $modelDate = new DateTime($modelYear.'01-01');
        }
        $isTechnicalInspectionOk = !$technicalInspection;
        $isMaintenanceLogOk = !$maintenanceLog;

        //Puis on hydrate en local le véhicule
        $vehicle->setBrand($brand);
        $vehicle->setModel($model);
        $vehicle->setFinish($finish);
        $vehicle->setEngineSize($engineSize);
        $vehicle->setEngine($engine);
        $vehicle->setDynamicalPower($dynamicalPower);
        $vehicle->setModelDate($modelDate);
        $vehicle->setMileage($mileage);
        $vehicle->setFuel($fuel);
        $vehicle->setGearbox($gearbox);
        $vehicle->setReference($reference);
        $vehicle->setFiscalPower($fiscalPower);
        $vehicle->setCo2($co2);
        $vehicle->setBodywork($bodywork);
        $vehicle->setTransmission($transmission);
        $vehicle->setExternalColor($externalColor);
        $vehicle->setUsersAmount($usersAmount);
        $vehicle->setHasAccident($hasAccident);
        $vehicle->setIsTechnicalInspectionOk($isTechnicalInspectionOk);
        $vehicle->setIsMaintenanceLogOk($isMaintenanceLogOk);
        $vehicle->setEquipments($equipments);
        $vehicle->setSuppComments($suppComments);
        $vehicle->setFunding($funding);
        $vehicle->setWarranty($warranty);
        $vehicle->setPrice($price);
        $vehicle->setSellerMargin(0);
        $vehicle->setImage1($image1);
        $vehicle->setImage2($image2);
        $vehicle->setImage3($image3);
        $vehicle->setImage4($image4);
        $vehicle->setDepotSale($depotSale);
        $vehicle->setAvailabilityDate($availabilityDate);
        $vehicle->setBuyingPrice($buyingPrice);
        $vehicle->setVatOnMargin($vatOnMargin);
        $vehicle->setFeesAmount($feesAmount);
        $vehicle->setFeesDetails($feesDetails);

        //et on hydrate la base
        $hydrateVehicle = \Vehicle\VehicleInStockManager::hydrateVehicle($db, $vehicle);
        if(is_a($hydrateVehicle, '\Exception')){
            throw $hydrateVehicle;
        }

        //On valide la transaction
        $db->commit();
        //Fermeture de la connexion
        $db = null;
        $_SESSION['returnAction'] = array(1, 'Modification du véhicule effectuée avec succès !');
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
 * @param string $fileName
 * @param array  $imageInfo
 *
 * @return string
 * @throws array|Exception
 */
function uploadVehicleImage(string $fileName, array $imageInfo){
    $searchExt = explode('.', $imageInfo['name']);
    $ext = $searchExt[count($searchExt) - 1];
    $fileName = $fileName.'.'.$ext;

    $pathOnServer = $_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO';

    $fileToUpload = new FileToUpload($pathOnServer, $fileName, $imageInfo['type'], $imageInfo['tmp_name'], $imageInfo['error'], (int)$imageInfo['size']);
    $uploadFile = FileToUploadManager::uploadFile($fileToUpload);
    if(is_a($uploadFile, 'Exception')){
        throw $uploadFile;
    }
    return $fileName;
}

/**
 * @param int $vehicleId
 *
 * @return bool
 * @throws Exception|\Vehicle\VehicleInStock
 */
function deleteVehicle(int $vehicleId){
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        $vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
        if(is_a($vehicle, '\Exception')){
            throw $vehicle;
        }

        $deleteVehicle = \Vehicle\VehicleInStockManager::deleteVehicle($db, $vehicleId);
        if(is_a($deleteVehicle, '\Exception')){
            throw new Exception('Impossible de supprimer le véhicule sélectionné.');
        }

        //On valide la transaction
        $db->commit();
        //Fermeture de la connexion
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
 * @param int $vehicleId
 *
 * @return bool
 * @throws Exception|\Offers\StockOffer
 * @throws Exception|\Vehicle\VehicleInStock
 * @throws Exception|string
 */
function createOffer(int $vehicleId){
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /**********************RECUPERATION DES DONNEES BRUTES************************/
        /** @var null|\Users\Client $client */
        $client = empty($_SESSION['selectedClient']) ? null : $_SESSION['selectedClient'];
        $vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
        /**********************RECUPERATION DES DONNEES BRUTES************************/

        /**********************CHECK DE LA COHERENCE DES DONNEES************************/
        if(is_a($vehicle, '\Exception')){
            throw $vehicle;
        }
        if($client == null || is_a($client, '\Exception')){
            throw new Exception('Erreur lors de la sélection du client.');
        }
        /**********************CHECK DE LA COHERENCE DES DONNEES************************/

        //Arrivée ici, données cohérentes

        //Récupération des données manquantes pour la création de l'offre
        $vat = \Prices\VatManager::fetchFrenchVat($db);
        /** @var \Users\User $user */
        $user = $_SESSION['user'];
        $dealer = $user->getStructure();
        $dealerDepartment = $dealer->getDepartment();
        /** @var \Users\Client|null $client */
        $clientDepartment = empty($client->getPostalCode()) ? $dealerDepartment : $client->getDepartment();
        $vehiclePrice = \Prices\VatManager::convertToPretax($vehicle->getPrice(), $vat->getAmount());
        $marginInDatabase = \Vehicle\VehicleInStockManager::fetchStructureMargin($db, $vehicleId, $dealer->getId());
        $dealerMargin = is_a($marginInDatabase, '\Exception') ? $dealer->getDefaultMargin() : $marginInDatabase;
        if($dealer->getFreightCharges() === null){
            $freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $clientDepartment);
            $freightChargesAmount = $freightCharges->getAmount();
        }
        else{
            $freightChargesAmount = 0;
        }

        //On créé le numéro de l'offre
        $offerNumber = \Offers\StockOfferManager::generateOfferNumber($db, $dealer->getId(), $user->getId(), $client->getId());
        if(is_a($offerNumber, '\Exception')){
            throw $offerNumber;
        }

        //On créé l'offre en local
        $offer = new \Offers\StockOffer(0, $offerNumber, $vehicleId, $vehicle, $vehiclePrice, $dealerMargin,
                                        $freightChargesAmount, $dealer->getPackageProvision(), $dealer->getId(),
                                        $dealer, $user->getId(), $user, $client->getId(), $client, new DateTime(), 1);

        //Puis on l'insère en base
        $insertOffer = \Offers\StockOfferManager::insertOffer($db, $offer);
        if(is_a($insertOffer, '\Exception')){
            throw $insertOffer;
        }

        //On valide la transaction
        $db->commit();
        //Fermeture de la connexion
        $db = null;
        $_SESSION['returnAction'] = array(1, 'Création de l\'offre effectuée avec succès !');
        return true;
    }
    catch(Exception $e){
        $db->rollBack();
        $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage());
        $db = null;
        return false;
    }
}

function transformOfferToCommand(string $offerReference, array $postData):bool{
    $db = databaseConnection();
    try{
        $db->beginTransaction();

        /***************************RECUPERATION DE L'OFFRE**********************/
        $offer = \Offers\StockOfferManager::fetchOfferByReference($db, $offerReference, true);
        if(is_a($offer, '\Exception'))
            throw $offer;
        /** @var \Users\User $user */
        $user = $_SESSION['user'];
        //Récupération du client concerné
        $client = $offer->getClient();
        /***************************RECUPERATION DE L'OFFRE**********************/

        /***************************RECUPERATION DES DONNEES**********************/
        $invalidClientData = (empty($client->getLastName()) || empty($client->getFirstName()) || empty($client->getPostalAddress())
            || empty($client->getPostalCode()) || empty($client->getTown()));

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
        if($invalidClientData
            && (empty($firstName) || empty($lastName) || empty($addressNumber) || empty($streetType)
            || empty($wording) || empty($postalCode) || empty($town))
        ){
            throw new Exception('Erreur, un ou plusieurs champs sont manquants.');
        }
        /***************************VERIFICATION DES DONNEES**********************/

        /***************************VERIFICATION DES DROITS**********************/
        //Si l'utilisateur n'est pas admin
        if(!$user->isAdmin()){
            //Si il est dirigeant de ce garage
            if($user->isOwner()){
                $userOffer = $offer->getUser();
                //Si la personne connectée n'est pas dirigeante de la structure concernée par l'offre
                if($user->getStructureId() != $userOffer->getStructureId()){
                    throw new Exception('Erreur, vous n\'avez pas les droits pour transformer cette offre.');
                }
            }
            //Si il ne l'est pas
            else{
                //Si la personne connectée n'est pas celle qui a créé cette offre
                if($user->getId() != $offer->getUserId()){
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
            $hydrateOffer = \Offers\StockOfferManager::hydrateOfferState($db, $offer);
            if(is_a($hydrateOffer, '\Exception')){
                throw $hydrateOffer;
            }
        }

        //On réserve ensuite le véhicule choisi pour éviter un deuxieme BDC sur ce vhcl
        $vehicle = $offer->getVehicle();
        $vehicle->setReserved(true);
        $vehicle->setReservedBy($offer->getStructureId());

        $hydrateVehicle = \Vehicle\VehicleInStockManager::hydrateVehicle($db, $vehicle);
        if(is_a($hydrateVehicle, '\Exception')){
            throw $hydrateVehicle;
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