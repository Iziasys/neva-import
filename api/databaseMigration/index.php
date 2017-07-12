<?php
include $_SERVER['DOCUMENT_ROOT'].'/conf.php';
if($GLOBALS['__SITE_MODE'] == 'development'){
    ini_set("display_errors", 1);
}

include $_SERVER["DOCUMENT_ROOT"].getAppPath().'/fonctions/fonctions.php';//Appel du fichier de fonctions principales
include $_SERVER["DOCUMENT_ROOT"].getAppPath().'/fonctions/loadLibraries.php';
spl_autoload_register('loadClass');//Chargement automatique des classes
session_start();//Démarrage de la session
$_SESSION['ROOT_PATH'] = getAppPath();
include $_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/ressources/connexion.php';
$newDb = databaseConnection();
$oldDb = new PDO('mysql:host='.getHost().';dbname=bdd_old_neva', ''.getUsr().'', ''.getPwd().'', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
try{
    $newDb->beginTransaction();

    /**********************GESTION DES CLIENTS*********************/
    /*$query_fetch_old_clients = '
        SELECT *
        FROM Client
        WHERE garage_idGarage IN (1, 7)
    ;';
    $binds = array();
    $old_clients = \executeSelect($oldDb, $query_fetch_old_clients, $binds);

    $clientsArray = array();
    foreach($old_clients as $old_client){
        $email = (string)$old_client['mailClient'];
        $lastName = (string)$old_client['nomClient'];
        $firstName = (string)$old_client['prenomClient'];
        $civility = 'Mr';
        $phone = (string)$old_client['telephoneClient'];
        $mobile = '';
        $fax = '';
        $acceptNewsLetter = true;
        $isSociety = false;
        $ownerId = $old_client['garage_idGarage'] == 7 ? 3 : 1;
        $addressNumber = 0;
        $addressExtension = '';
        $streetType = '';
        $addressWording = (string)$old_client['adresseClient'];
        $postalCode = (string)$old_client['codePostalClient'];
        $town = (string)$old_client['villeClient'];
        $insertionDate = new DateTime();

        $client = new \Users\IndividualClient(0, $email, $lastName, $firstName, $civility, $phone, $mobile, $fax,
                                              $acceptNewsLetter, $isSociety, $ownerId, $addressNumber, $addressExtension,
                                              $streetType, $addressWording, $postalCode, $town, $insertionDate);

        $clientsArray[] = $client;
    }
    /*foreach($clientsArray as $client){
        $insertClient = \Users\ClientManager::insertClient($newDb, $client);

        if(is_a($insertClient, '\Exception')){
            throw $insertClient;
        }
    }*/
    //INSERTION CLIENT OK
    echo 'InsertionClientOK<br>';
    /**********************GESTION DES CLIENTS*********************/

    /**********************GESTION DES VEHICULES EN STOCK ET ARRIVAGE*********************/
    //Récupération des véhicules en stock actuellement
    /*$query_fetch_old_vehicles_stock = '
        SELECT *
        FROM VehiculeOccasion
        WHERE garage_idGarage IN (1, 7)
    ;';
    $binds = array();
    $old_vehicles_stock = \executeSelect($oldDb, $query_fetch_old_vehicles_stock, $binds);

    $stockVehiclesArray = array();
    //Ensuite, pour chaque véhicule, on en créé un objet dans la nouvelle application
    foreach($old_vehicles_stock as $item){
        $brand = mb_strtoupper(restoreAccents((string)$item['marque']));
        $model = restoreAccents((string)$item['modele']);
        $finish = restoreAccents((string)$item['finition']);
        $engineSize = (float)$item['cylindree'];
        $engine = (string)$item['motorisation'];
        $dynamicalPower = (int)$item['puissanceDin'];
        if(preg_match('#[0-9]{1,2}\/[0-9]{4}#', $item['anneeModele'])){
            $information = explode('/', $item['anneeModele']);
            $modelDate = new DateTime($information[1].'-'.$information[0].'-01');
        }
        else{
            $modelDate = new DateTime((int)$item['anneeModele'].'-01-01');
        }
        $mileage = (int)$item['kilometrage'];
        $fuel = (string)$item['carburant'];
        $gearbox = (string)$item['boiteVitesse'];
        $reference = (string)$item['reference'];
        $fiscalPower = (int)$item['puissanceFisc'];
        $co2 = (int)$item['co2'];
        $bodywork = (string)$item['carrosserie'];
        $transmission = (string)$item['transmission'];
        $usersAmount = (int)$item['nbProprietaires'];
        $externalColor = (string)$item['couleurExt'];
        $hasAccident = (bool)$item['accidentAnterieur'];
        $isTechnicalInspectionOk = (bool)$item['controleTechnique'];
        $isMaintenanceLogOk = (bool)$item['carnetEntretien'];
        $equipmentsList = (string)$item['listeEquip'];
        $equipments = explode(';', $equipmentsList);
        $suppComments = (string)$item['commentaires'];
        $funding = (string)$item['financement'];
        $warranty = (string)$item['garantie'];
        $price = (float)$item['prix'];
        $sellerMargin = 0.0;
        $image1 = (string)$item['image1'];
        $image2 = (string)$item['image2'];
        $image3 = (string)$item['image3'];
        $image4 = (string)$item['image4'];
        $structureId = $item['garage_idGarage'] == 7 ? 3 : 1;
        $depotSale = (bool)$item['depotVente'];
        $availabilityDate = new DateTime($item['dateDisponibilite']);
        $sold = (bool)$item['vendu'];
        $reserved = false;
        $reservedBy = 0;
        $insertionDate = new DateTime($item['dateInsertion']);
        $sellingStructure = (int)$item['garageVendeur'];
        $sellingDate = ($item['dateVente'] == null ? new DateTime() : new DateTime($item['dateVente']));
        $bonusPenalty = (float)$item['tauxBonusMalus'];

        $vehicleInStock = new \Vehicle\VehicleInStock(0, $brand, $model, $finish, $engineSize, $engine, $dynamicalPower, $modelDate, $mileage, $fuel, $gearbox,
                                                      $reference, $fiscalPower, $co2, $bodywork, $transmission, $usersAmount, $externalColor, $hasAccident,
                                                      $isTechnicalInspectionOk, $isMaintenanceLogOk, $equipments, $suppComments, $funding, $warranty, $price, $sellerMargin,
                                                      $image1, $image2, $image3, $image4, $structureId, $depotSale, $availabilityDate, $sold, $reserved, $reservedBy,
                                                      $insertionDate, $sellingStructure, $sellingDate, $bonusPenalty);
        $stockVehiclesArray[] = $vehicleInStock;
    }
    //Et on insère dans la nouvelle base
    /*foreach($stockVehiclesArray as $vehicleInStock){
        $insertVehicle = \Vehicle\VehicleInStockManager::insertVehicle($newDb, $vehicleInStock);
        if(is_a($insertVehicle, '\Exception')){
            throw $insertVehicle;
        }
    }*/
    //INSERTION VEHICLE EN STOCK OK
    echo 'InsertionVehicleStockOK<br>';
    /**********************GESTION DES VEHICULES EN STOCK ET ARRIVAGE*********************/

    /**********************GESTION DES VEHICULES EN COMMANDE*********************/
    //D'abord on récupère les couleurs extérieures existantes en base
    /*$query_fetch_old_colors = '
        SELECT * FROM CouleurExt;
    ';
    $binds = array();

    $oldColors = \executeSelect($oldDb, $query_fetch_old_colors, $binds);

    $query_insert_new_colors = 'INSERT INTO vhcl_externalColor (id, bitone, name, details) VALUES';
    $binds = array();
    foreach($oldColors as $key => $oldColor){
        if($key)
            $query_insert_new_colors .= ',';
        $query_insert_new_colors .= '(:id'.$key.', :bitone'.$key.', :name'.$key.', :details'.$key.')';
        $binds[':id'.$key] = (int)$oldColor['idCouleurExt'];
        $binds[':bitone'.$key] = (bool)$oldColor['bitonCouleurExt'];
        $binds[':name'.$key] = (string)$oldColor['nomCouleurExt'];
        $binds[':details'.$key] = (string)$oldColor['denominationCouleurExt'];
    }
    echo $query_insert_new_colors;
    echo '<br>';
    print_r($binds);
    \executeInsert($newDb, $query_insert_new_colors, $binds);*/

    //Et les jantes
    /*$query_fetch_old_rims = '
        SELECT * FROM Jante;
    ';
    $binds = array();
    $oldRims = \executeSelect($oldDb, $query_fetch_old_rims, $binds);
    $query_insert_new_rims = 'INSERT INTO vhcl_rim (id, type, frontDiameter, backDiameter) VALUES';
    $binds = array();
    foreach($oldRims as $key => $oldRim){
        if($key)
            $query_insert_new_rims .= ',';
        $query_insert_new_rims .= '(:id'.$key.', :type'.$key.', :frontDiameter'.$key.', :backDiameter'.$key.')';
        $binds[':id'.$key] = (int)$oldRim['idJante'];
        $binds[':type'.$key] = (string)$oldRim['typeJante'];
        $binds[':frontDiameter'.$key] = (int)$oldRim['diametreJanteAv'];
        $binds[':backDiameter'.$key] = (int)$oldRim['diametreJanteAr'];
    }
    \executeInsert($newDb, $query_insert_new_rims, $binds);*/
    //Et les modèles de jantes
    /*$query_fetch_old_rimModels = '
        SELECT * FROM ModeleJante;
    ';
    $binds = array();
    $oldRimsModels = \executeSelect($oldDb, $query_fetch_old_rimModels, $binds);
    $query_insert_new_rimsModels = 'INSERT INTO vhcl_rimModel (id, name, rim_id) VALUES';
    $binds = array();
    foreach($oldRimsModels as $key => $oldRim){
        if($key)
            $query_insert_new_rimsModels .= ',';
        $query_insert_new_rimsModels .= '(:id'.$key.', :name'.$key.', :rimId'.$key.')';
        $binds[':id'.$key] = (int)$oldRim['idModeleJante'];
        $binds[':name'.$key] = (string)$oldRim['nomModeleJante'];
        $binds[':rimId'.$key] = (int)$oldRim['jante_idJante'];
    }
    \executeInsert($newDb, $query_insert_new_rimsModels, $binds);*/

    /*$query_fetch_old_vehicles_command = '
        SELECT * FROM Finition
        INNER JOIN Modele
        ON Finition.modele_idModele = Modele.idModele
        INNER JOIN Marque
        ON Modele.marque_idMarque = Marque.idMarque
    ';
    $binds = array();
    $old_vehicles_command = \executeSelect($oldDb, $query_fetch_old_vehicles_command, $binds);

    $commandVehiclesArray = array();
    foreach($old_vehicles_command as $vehicleData){
        var_dump($vehicleData);
        echo '<br><br>';

        $brandName = (string)$vehicleData['nomMarque'];
        $brand = \Vehicle\BrandManager::fetchBrandByName($newDb, $brandName);
        $modelName = (string)$vehicleData['nomModele'];
        $model = new \Vehicle\Model(0, $modelName, $brand, $brand->getId());

        $equipmentsToAdd = array();

        $finishId = (int)$vehicleData['idFinition'];
        $finishName = (string)$vehicleData['nomFinition'];
        $active = true;

        //INSERTION DES MODELES DE VHCL EN BASE
        $modelId = (int)$vehicleData['idModele'];
        $brandId = (int)$vehicleData['idMarque'];
        $model = new \Vehicle\Model($modelId, $modelName, null, $brandId);
        $queryInsertModel = '
            INSERT INTO vhcl_model (id, modelName, brand_id) VALUES (:id, :modelName, :brandId) ON DUPLICATE KEY UPDATE id = id ;
        ';
        $binds = array(
            ':id'        => $model->getId(),
            ':modelName' => $model->getName(),
            ':brandId'   => $model->getBrandId()
        );
        //\executeInsert($newDb, $queryInsertModel, $binds);
        echo 'InsertionModelsOK<br>';

        $finish = new \Vehicle\Finish($finishId, $finishName, $model, $model->getId(), null, 1, $active);
        $queryInsertFinish = '
            INSERT INTO vhcl_finish (id, finishName, model_id, dealer_id, available) VALUE (:id, :finishName, :modelId, :dealerId, :available);
        ';
        $binds = array(
            ':id'         => $finish->getId(),
            ':finishName' => $finish->getName(),
            ':modelId'    => $finish->getModelId(),
            ':dealerId'   => $finish->getDealerId(),
            ':available'  => $finish->getActive()
        );
        //\executeInsert($newDb, $queryInsertFinish, $binds);
        echo 'InsertionFinishesOK<br>';

        //EQUIPEMENTS DE SERIE
        $serialEquipmentsIdsList = $vehicleData['equipementsInclus'];
        $serialEquipmentsIdsArray = explode(',', $serialEquipmentsIdsList);
        $serialEquipmentsArray = array();
        foreach($serialEquipmentsIdsArray as $id){
            $id = (int)$id;
            $serialEquipmentsArray[] = \Vehicle\EquipmentManager::fetchEquipment($newDb, $id);
            $equipmentsToAdd[] = array(
                'equipment' => $id,
                'price'     => null
            );
        }

        //EQUIPEMENTS EN OPTION
        $optionalEquipmentsIdsList = $vehicleData['equipementsOptio'];
        $optionalEquipmentsIdsArray = explode(',', $optionalEquipmentsIdsList);
        $optionalEquipmentsArray = array();
        foreach($optionalEquipmentsIdsArray as $id){
            $id = (int)$id;

            if(empty($id))
                continue;

            //On récupère l'équipement optionnel dans la base
            $queryFetchOptionalEquipment = '
                SELECT * FROM PrixEquipement WHERE equipement_idEquipement = :equipmentId AND finition_idFinition = :finishId;
            ';
            $binds = array(
                ':equipmentId' => $id,
                ':finishId'    => $finishId
            );

            $optionalEquipments = \executeSelect($oldDb, $queryFetchOptionalEquipment, $binds);
            $optionalEquipments = $optionalEquipments[0];

            $currencyId = ($optionalEquipments['devise_idDevise'] == '2' ? 7 : 6);

            $equipmentPrice = new \Prices\Price(0, (float)$optionalEquipments['montantPrixEquipement'], 0.0, null, 1, null, $currencyId, 0.0, 0.0, null);
            //On insère le prix de l'équipement
            $insertEquipmentPrice = \Prices\PriceManager::insertPrice($newDb, $equipmentPrice);
            if(is_a($insertEquipmentPrice, '\Exception')){
                throw $insertEquipmentPrice;
            }
            echo 'InsertionPrixEquipementsOptionOK<br>';

            //Et ajout dans le tableau des équipements à insérer
            $equipmentsToAdd[] = array(
                'equipment' => $id,
                'price'     => $equipmentPrice->getId()
            );
        }

        $insertEquipments = \Vehicle\FinishManager::addEquipments($newDb, $finishId, $equipmentsToAdd);
        if(is_a($insertEquipments, '\Exception')){
            throw $insertEquipments;
        }
        echo 'InsertionEquipementsOK<br>';

        //PACKS
        $packsArray = array();

        $queryFetchOldPacks = '
            SELECT *
            FROM Pack
            WHERE finition_idFinition = :finishId
        ';
        $binds = array(':finishId' => $finishId);
        $oldPacks = \executeSelect($oldDb, $queryFetchOldPacks, $binds);
        if(!is_a($oldPacks, '\Exception')){
            foreach($oldPacks as $oldPack){
                $packName = (string)$oldPack['nomPack'];

                $currencyId = ($oldPack['devise_idDevise'] == '2' ? 7 : 6);

                $packPrice = new \Prices\Price(0, (float)$oldPack['prixPack'], 0.0, null, 1, null, $currencyId, 0.0, 0.0, null);
                //On insère le prix du pack
                $insertPackPrice = \Prices\PriceManager::insertPrice($newDb, $packPrice);
                if(is_a($insertPackPrice, '\Exception')){
                    throw $insertPackPrice;
                }
                echo 'InsertionPackPriceOK<br>';
                $pack = new \Vehicle\Pack(0, $packName, array(), array(), null, 0, null, 0, null, $packPrice->getId());
                //On insère le pack
                $insertPack = \Vehicle\PackManager::insertPack($newDb, $pack, $finishId);
                if(is_a($insertPack, '\Exception')){
                    throw $insertPack;
                }
                $pack = $insertPack;
                echo 'InsertionPackOK<br>';

                //Puis on s'occupe des équipements/couleurs/jantes du pack
                $includedEquipmentsArray = explode(',', $oldPack['listeEquipements']);
                //Insertion des équipements
                $insertEquipmentsInPack = \Vehicle\PackManager::addEquipments($newDb, $pack->getId(), $includedEquipmentsArray);
                if(is_a($insertEquipmentsInPack, '\Exception')){
                    throw $insertEquipmentsInPack;
                }
                echo 'InsertionEquipmentInPackOK<br>';

                $rimsId = (int)$oldPack['jante_idModeleJante'];
                if(!empty($rimsId)){
                    $insertRimsInPack = \Vehicle\PackManager::insertRims($newDb, $pack->getId(), $rimsId);
                    if(is_a($insertRimsInPack, '\Exception')){
                        throw $insertRimsInPack;
                    }
                    echo 'InsertionRimsInPackOK<br>';
                }

                $colorId = (int)$oldPack['couleurExt_idCouleurExt'];
                if(!empty($colorId)){
                    $insertColorInPack = \Vehicle\PackManager::insertColor($newDb, $pack->getId(), $colorId);
                    if(is_a($insertColorInPack, '\Exception')){
                        throw $insertColorInPack;
                    }
                    echo 'InsertionColorInPackOK<br>';
                }
            }
        }

        //JANTES DE SERIE
        $serialRimModel = null;
        $serialRimModelId = (int)$vehicleData['janteSerieFinition'];
        if(!empty($serialRimModelId)){
            $addRims = \Vehicle\FinishManager::addRims($newDb, $serialRimModelId, $finishId, null);
            if(is_a($addRims, '\Exception')){
                throw $addRims;
            }
            echo 'InsertionSerialRimsOK<br>';
        }

        //JANTES EN OPTION
        $optionalRimsArray = array();
        $optionalRimsIdsArray = explode(',', $vehicleData['janteOptionFinition']);
        foreach($optionalRimsIdsArray as $rims){
            if($rims == '0')
                continue;

            $queryFetchRimsPrice = 'SELECT * FROM PrixJante WHERE modeleJante_idModeleJante = :rimsId AND finition_idFinition = :finishId;';
            $binds = array(
                ':rimsId'   => (int)$rims,
                ':finishId' => $finishId
            );
            $rimsPrice = \executeSelect($oldDb, $queryFetchRimsPrice, $binds);

            $currencyId = ($rimsPrice[0]['devise_idDevise'] == '2' ? 7 : 6);
            $price = new \Prices\Price(0, (float)$rimsPrice[0]['montantPrixJante'], 0, null, 1, null, $currencyId);
            $insertRimsPrice = \Prices\PriceManager::insertPrice($newDb, $price);
            if(is_a($insertRimsPrice, '\Exception')){
                throw $insertRimsPrice;
            }
            echo 'InsertionRimsPriceOK<br>';
            $addRims = \Vehicle\FinishManager::addRims($newDb, (int)$rims, $finishId, $price->getId());
            if(is_a($addRims, '\Exception')){
                throw $addRims;
            }
        }
        echo 'InsertionOptionalRimsOK<br>';

        //COULEUR DE SERIE
        $serialColor = null;
        $serialColorId = (int)$vehicleData['couleurExtSerieFinition'];
        if(!empty($serialColorId)){
            $addColor = \Vehicle\FinishManager::addExternalColor($newDb, $finishId, $serialColorId, null);
            if(is_a($addColor, '\Exception')){
                throw $addColor;
            }
            echo 'InsertionCouleurSerieOK<br>';
        }

        //COULEURS EN OPTION
        $optionalColorsArray = array();
        $optionalColorsIdsArray = explode(',', $vehicleData['couleurExtOptionFinition']);
        foreach($optionalColorsIdsArray as $color){
            if($color == '0')
                continue;

            $queryFetchColorPrice = 'SELECT * FROM PrixCouleurExt WHERE couleurExt_idCouleurExt = :colorId AND finition_idFinition = :finishId;';
            $binds = array(
                ':colorId'  => (int)$color,
                ':finishId' => $finishId
            );
            $colorPrice = \executeSelect($oldDb, $queryFetchColorPrice, $binds);

            $currencyId = ($colorPrice[0]['devise_idDevise'] == '2' ? 7 : 6);
            $price = new \Prices\Price(0, (float)$colorPrice[0]['montantPrixCouleurExt'], 0, null, 1, null, $currencyId);
            $insertColorPrice = \Prices\PriceManager::insertPrice($newDb, $price);
            if(is_a($insertColorPrice, '\Exception')){
                throw $insertColorPrice;
            }
            echo 'InsertionColorPriceOK<br>';
            $addColor = \Vehicle\FinishManager::addExternalColor($newDb, $finishId, (int)$color, $price->getId());
            if(is_a($addColor, '\Exception')){
                throw $addColor;
            }
        }
        echo 'InsertionCouleurOptioOK<br>';

        //var_dump($finish);
        echo '<br><br><br>';
    }*/
    echo 'InsertionFinitionsOK<br>';

    $query_fetch_old_vehicles = '
        SELECT *
        FROM DetailsVehicule
        INNER JOIN TypeTransmission
          ON DetailsVehicule.typeTransmission_idTypeTransmission = TypeTransmission.idTypeTransmission
        INNER JOIN TypeMotorisation
          ON DetailsVehicule.typeMotorisation_idTypeMotorisation = TypeMotorisation.idTypeMotorisation
        INNER JOIN Carrosserie
          ON DetailsVehicule.carrosserie_idCarrosserie = Carrosserie.idCarrosserie
        INNER JOIN Carburant
          ON DetailsVehicule.carburant_idCarburant = Carburant.idCarburant
        INNER JOIN BoiteVitesse
          ON DetailsVehicule.boiteVitesse_idBoiteVitesse = BoiteVitesse.idBoiteVitesse
        INNER JOIN Prix
          ON DetailsVehicule.prixHT_idPrixHT = Prix.idPrix
    ;';
    $binds = array();

    $oldVehicles = \executeSelect($oldDb, $query_fetch_old_vehicles, $binds);

    foreach($oldVehicles as $oldVehicle){
        var_dump($oldVehicle);

        echo '<br><br>';

        $id = 0;
        $dynamicalPower = (int)$oldVehicle['puissanceDin'];
        $fiscalPower = (int)$oldVehicle['puissanceFisc'];
        $engineSize = (float)$oldVehicle['typeCylindree'];
        $co2 = (int)$oldVehicle['co2'];
        $sitsAmount = (int)$oldVehicle['nbPlaces'];
        $doorsAmount = (int)$oldVehicle['nbPortes'];
        $engine = new \Vehicle\Engine((int)$oldVehicle['idTypeMotorisation'], (string)$oldVehicle['nomTypeMotorisation']);
        //Insertion de l'engine
        $insertEngine = \Vehicle\EngineManager::insertEngine($newDb, $engine);
        if(is_a($insertEngine, '\Exception')){
            throw $insertEngine;
        }
        $transmission = new \Vehicle\Transmission((int)$oldVehicle['idTypeTransmission'], (string)$oldVehicle['nomTypeTransmission']);
        //Insertion de la transmission
        $insertTransmission = \Vehicle\TransmissionManager::insertTransmission($newDb, $transmission);
        if(is_a($insertTransmission, '\Exception')){
            throw $insertTransmission;
        }
        $bodywork = new \Vehicle\Bodywork((int)$oldVehicle['idCarrosserie'], (string)$oldVehicle['nomCarrosserie']);
        $finish = null;
        $finishId = (int)$oldVehicle['finition_idFinition'];
        $gearbox = new \Vehicle\Gearbox((int)$oldVehicle['idBoiteVitesse'], (string)$oldVehicle['nomBoiteVitesse']);
        $fuel = new \Vehicle\Fuel((int)$oldVehicle['idCarburant'], (string)$oldVehicle['nomCarburant']);

        $pretaxBuyingPrice = (float)$oldVehicle['montantPrixHT'];
        $postTaxesPublicPrice = (float)$oldVehicle['montantPrixPublic'];
        $country = null;
        $countryId = 1;
        if($oldVehicle['idPaysOrigine'] == '21')
            $countryId = 4;
        else if($oldVehicle['idPaysOrigine'] == '6')
            $countryId = 3;
        else
            $countryId = 1;
        $currency = null;
        $currencyId = ($oldVehicle['devise_idDevise'] == '2' ? 7 : 6);
        $margin = (float)$oldVehicle['montantMarge'];
        $maximumDiscount = (float)$oldVehicle['remiseMaxFr'];
        $priceDate = new DateTime();
        $managementFees = 250;
        $price = new \Prices\Price(0, $pretaxBuyingPrice, $postTaxesPublicPrice, $country, $countryId, $currency,
                                   $currencyId, $margin, $maximumDiscount, $priceDate, $managementFees);
        $insertPrice = \Prices\PriceManager::insertPrice($newDb, $price);
        if(is_a($insertPrice, '\Exception')){
            throw $insertPrice;
        }
        echo 'InsertionVehiclePriceOK<br>';
        $price = $insertPrice;
        $priceId = $price->getId();
        $available = true;

        $vehicle = new \Vehicle\Details(0, $dynamicalPower, $fiscalPower, $engineSize, $co2, $sitsAmount, $doorsAmount, $engine, $transmission,
                                        $bodywork, $finish, $finishId, $gearbox, $fuel, $price, $priceId, $available);
        $insertVehicle = \Vehicle\DetailsManager::insertDetails($newDb, $vehicle);
        if(is_a($insertVehicle, '\Exception')){
            throw $insertVehicle;
        }
        echo 'InsertionVehicleOK<br>';
    }
    /**********************GESTION DES VEHICULES EN COMMANDE*********************/


    $newDb->commit();
    $newDb = null;
}
catch(Exception $e){
    $newDb->rollBack();
    $newDb = null;
    echo 'Erreur : '.$e->getMessage().' File : '.$e->getFile().' - Line : '.$e->getLine();
}

function restoreAccents(string $origin){
    $tmp = $origin;
    $tmp = preg_replace_callback('#&eacute;#', function(){return 'é';}, $tmp);
    $tmp = preg_replace_callback('#&egrave;#', function(){return 'è';}, $tmp);
    $tmp = preg_replace_callback('#&ecirc;#', function(){return 'ê';}, $tmp);
    $tmp = preg_replace_callback('#&euml;#', function(){return 'ë';}, $tmp);
    $tmp = preg_replace_callback('#&icirc;#', function(){return 'î';}, $tmp);
    $tmp = preg_replace_callback('#&iuml;#', function(){return 'ï';}, $tmp);
    $tmp = preg_replace_callback('#&ocirc;#', function(){return 'ô';}, $tmp);
    $tmp = preg_replace_callback('#&ouml;#', function(){return 'ö';}, $tmp);

    return $tmp;
}
