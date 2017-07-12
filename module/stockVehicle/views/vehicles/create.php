<?php
try{
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $structure = $user->getStructure();
    if($_GET['action'] == 'modify' && !empty($_GET['vehicleId'])){
        $vehicleId = (int)$_GET['vehicleId'];

        //Récupération des informations du véhicule en base
        $db = databaseConnection();
        $vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
        if(is_a($vehicle, '\Exception')){
            $db = null;
            throw $vehicle;
        }
        $db = null;

        $mode = 'modify';
    }
    else if($_GET['action'] == 'copyFromCommand' && !empty($_GET['vehicleId'])){
        $vehicleId = (int)$_GET['vehicleId'];

        $db = databaseConnection();
        $vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $vehicleId);
        $serialEquipments = \Vehicle\FinishManager::fetchSerialEquipments($db, $vehicle->getFinishId());
        $optionalEquipments = \Vehicle\FinishManager::fetchOptionalEquipments($db, $vehicle->getFinishId());
        $db = null;

        $mode = 'create';
    }
    else{
        if(empty($_GET['vehicleId'])){
            $vehicle = new \Vehicle\VehicleInStock();

            $vehicle->setHasAccident(false);
            $vehicle->setIsTechnicalInspectionOk(true);
            $vehicle->setIsMaintenanceLogOk(true);
            $vehicle->setFunding($structure->getDefaultFunding());
            $vehicle->setWarranty($structure->getDefaultWarranty());

            $mode = 'create';
        }
        else{
            $vehicleId = (int)$_GET['vehicleId'];

            //Récupération des informations du véhicule en base
            $db = databaseConnection();
            $vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
            $db = null;

            $mode = 'copy';
        }
    }

    if($_GET['action'] == 'copyFromCommand'){
        /** @var \Vehicle\Details $vehicle */
        $vehicle = $vehicle;

        $finishObject = $vehicle->getFinish();
        $finish = $finishObject->getName();
        $modelObject = $finishObject->getModel();
        $model = $modelObject->getName();
        $brandObject = $modelObject->getBrand();
        $brand = $brandObject->getName();
        $engineSize = $vehicle->getEngineSize();
        $engine = $vehicle->getEngine()->getName();
        $dynamicalPower = $vehicle->getDynamicalPower();
        $today = new DateTime();
        $modelYear = $today->format('m/Y');
        $mileage = '';
        $fuel = $vehicle->getFuel()->getName();
        $gearbox = $vehicle->getGearbox()->getName();
        $offerReference = '';
        $fiscalPower = $vehicle->getFiscalPower();
        $co2 = $vehicle->getCo2();
        $bodywork = $vehicle->getBodywork()->getName();
        $transmission = $vehicle->getTransmission()->getName();
        $usersAmount = '';
        $externalColor = '';
        $hasAccident = false;
        $isTechnicalInspectionOk = true;
        $isMaintenanceLogOk = true;
        $equipments = '';
        if(!empty($serialEquipments) && !is_a($serialEquipments, '\Exception')){
            asort($serialEquipments);
            foreach($serialEquipments as $eq){
                if(!empty($equipments))
                    $equipments .= ';';
                $equipments .= $eq->getName();
            }
        }
        $suppComments = '';
        $funding = $structure->getDefaultFunding();
        $warranty = $structure->getDefaultWarranty();
        $price = '';
        $sellerMargin = '';
        $depotSale = false;
        $availabilityDate = $today;
        $buyingPrice = '';
        $vatOnMargin = '';
        $feesAmount = '';
        $feesDetails = '';
        $image1 = '';
        $image2 = '';
        $image3 = '';
        $image4 = '';
    }
    else{
        $brand = $vehicle->getBrand();
        $model = $vehicle->getModel();
        $finish = $vehicle->getFinish();
        $engineSize = $vehicle->getEngineSize();
        $engine = $vehicle->getEngine();
        $dynamicalPower = $vehicle->getDynamicalPower();
        $modelYear = $vehicle->getModelDate()->format('m/Y');
        $mileage = $vehicle->getMileage();
        $fuel = $vehicle->getFuel();
        $gearbox = $vehicle->getGearbox();
        $offerReference = $vehicle->getReference();
        $fiscalPower = $vehicle->getFiscalPower();
        $co2 = $vehicle->getCo2();
        $bodywork = $vehicle->getBodywork();
        $transmission = $vehicle->getTransmission();
        $usersAmount = $vehicle->getUsersAmount();
        $externalColor = $vehicle->getExternalColor();
        $hasAccident = $vehicle->getHasAccident();
        $isTechnicalInspectionOk = $vehicle->getIsTechnicalInspectionOk();
        $isMaintenanceLogOk = $vehicle->getIsMaintenanceLogOk();
        $equipments = implode(';', $vehicle->getEquipmentsOrderedByName());
        $suppComments = $vehicle->getSuppComments();
        $funding = $vehicle->getFunding();
        $warranty = $vehicle->getWarranty();
        $price = $vehicle->getPrice();
        $sellerMargin = $vehicle->getSellerMargin();
        $depotSale = $vehicle->getDepotSale();
        $availabilityDate = $vehicle->getAvailabilityDate();
        $buyingPrice = $vehicle->getBuyingPrice() === null ? '' : $vehicle->getBuyingPrice();
        $vatOnMargin = $vehicle->getVatOnMargin() === null ? '' : $vehicle->getVatOnMargin();
        $feesAmount = $vehicle->getFeesAmount() === null ? '' : $vehicle->getFeesAmount();
        $feesDetails = $vehicle->getFeesDetails() === null ? '' : $vehicle->getFeesDetails();
        $image1 = $vehicle->getImage1();
        $image2 = $vehicle->getImage2();
        $image3 = $vehicle->getImage3();
        $image4 = $vehicle->getImage4();
    }

    ?>
    <div class="container">
        <h2 class="page-header text-lg-center">Création d'un véhicule</h2>
        <br>
        <form action="/stock-arrivage/vehicules/visualiser" method="POST" enctype="multipart/form-data">
            <?php
            if(!empty($vehicleId)){
                ?>
                <input type="hidden" name="<?php echo $mode; ?>Vehicle[vehicleId]" value="<?php echo $vehicleId; ?>">
                <?php
            }
            ?>
            <h4 class="page-header">Véhicule</h4>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleBrand" class="col-lg-2 form-control-label">Marque* :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleBrand" class="form-control formManager"
                               value="<?php echo $brand; ?>"
                               data-formManager="required" name="<?php echo $mode; ?>Vehicle[brand]"
                               placeholder="RENAULT" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleModel" class="col-lg-2 form-control-label">Modèle* :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleModel" class="form-control formManager"
                               value="<?php echo $model; ?>"
                               data-formManager="required" name="<?php echo $mode; ?>Vehicle[model]"
                               placeholder="Captur" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleFinish" class="col-lg-2 form-control-label">Finition :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleFinish" class="form-control formManager"
                               value="<?php echo $finish; ?>"
                               data-formManager="" name="<?php echo $mode; ?>Vehicle[finish]" placeholder="Zen">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleEngineSize" class="col-lg-2 form-control-label">Cylindrée* :</label>
                    <div class="col-lg-2 input-group">
                        <input type="text" id="inputVehicleEngineSize" class="form-control formManager"
                               value="<?php echo $engineSize; ?>"
                               data-formManager="required float" name="<?php echo $mode; ?>Vehicle[engineSize]"
                               placeholder="1.2" required>
                        <span class="input-group-addon">L</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleEngine" class="col-lg-2 form-control-label">Motorisation* :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleEngine" class="form-control formManager"
                               value="<?php echo $engine; ?>"
                               data-formManager="required" name="<?php echo $mode; ?>Vehicle[engine]"
                               placeholder="DCi" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleDynamicalPower" class="col-lg-2 form-control-label">Puissance Dyn.*
                        :</label>
                    <div class="col-lg-2 input-group">
                        <input type="text" id="inputVehicleDynamicalPower" class="form-control formManager"
                               value="<?php echo $dynamicalPower; ?>"
                               data-formManager="required integer" name="<?php echo $mode; ?>Vehicle[dynamicalPower]"
                               placeholder="130" required>
                        <span class="input-group-addon">ch</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleModelYear" class="col-lg-2 form-control-label">Année modèle* :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleModelYear" class="form-control formManager"
                               value="<?php echo $modelYear; ?>"
                               data-formManager="required" name="<?php echo $mode; ?>Vehicle[modelYear]"
                               placeholder="2014 ou 02/2015" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleMileage" class="col-lg-2 form-control-label">Kilométrage* :</label>
                    <div class="col-lg-2 input-group">
                        <input type="text" id="inputVehicleMileage" class="form-control formManager"
                               value="<?php echo $mileage; ?>"
                               data-formManager="required integer" name="<?php echo $mode; ?>Vehicle[mileage]"
                               placeholder="18900" required>
                        <span class="input-group-addon">km</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleFuel" class="col-lg-2 form-control-label">Type de carburant :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleFuel" class="form-control formManager"
                               value="<?php echo $fuel; ?>"
                               data-formManager="pureString" name="<?php echo $mode; ?>Vehicle[fuel]"
                               placeholder="Diesel">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleGearbox" class="col-lg-2 form-control-label">Boite de vitesse :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleGearbox" class="form-control formManager"
                               value="<?php echo $gearbox; ?>"
                               data-formManager="" name="<?php echo $mode; ?>Vehicle[gearbox]" placeholder="Manuelle">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleReference" class="col-lg-2 form-control-label">Référence de l'offre
                        :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleReference" class="form-control formManager"
                               value="<?php echo $offerReference; ?>"
                               data-formManager="" name="<?php echo $mode; ?>Vehicle[reference]"
                               placeholder="ma référence">
                    </div>
                </div>
            </div>
            <br><br>
            <h4 class="page-header">Informations supplémentaires</h4>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleFiscalPower" class="col-lg-2 form-control-label">Puissance Fisc. :</label>
                    <div class="col-lg-2 input-group">
                        <input type="text" id="inputVehicleFiscalPower" class="form-control formManager"
                               value="<?php echo $fiscalPower; ?>"
                               data-formManager="integer" name="<?php echo $mode; ?>Vehicle[fiscalPower]"
                               placeholder="6">
                        <span class="input-group-addon">cv</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleCo2" class="col-lg-2 form-control-label">Taux de Co2 :</label>
                    <div class="col-lg-2 input-group">
                        <input type="text" id="inputVehicleCo2" class="form-control formManager"
                               value="<?php echo $co2; ?>"
                               data-formManager="integer" name="<?php echo $mode; ?>Vehicle[co2]"
                               placeholder="98">
                        <span class="input-group-addon">g/km</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleBodywork" class="col-lg-2 form-control-label">Type de Carrosserie :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleBodywork" class="form-control formManager"
                               value="<?php echo $bodywork; ?>"
                               data-formManager="pureString" name="<?php echo $mode; ?>Vehicle[bodywork]"
                               placeholder="SUV">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleTransmission" class="col-lg-2 form-control-label">Transmission :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleTransmission" class="form-control formManager"
                               value="<?php echo $transmission; ?>"
                               data-formManager="" name="<?php echo $mode; ?>Vehicle[transmission]" placeholder="4x2">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleUsersAmount" class="col-lg-2 form-control-label">Nb de propriétaires
                        :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleUsersAmount" class="form-control formManager"
                               value="<?php echo $usersAmount; ?>"
                               data-formManager="integer" name="<?php echo $mode; ?>Vehicle[usersAmount]"
                               placeholder="1">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleExternalColor" class="col-lg-2 form-control-label">Couleur Ext. :</label>
                    <div class="col-lg-2">
                        <input type="text" id="inputVehicleExternalColor" class="form-control formManager"
                               value="<?php echo $externalColor; ?>"
                               data-formManager="pureString" name="<?php echo $mode; ?>Vehicle[externalColor]"
                               placeholder="Rouge">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-4 col-without-padding">
                    <label class="col-lg-12 form-control-label">Accident(s) antérieur(s) ?</label>
                    <div class="col-lg-6">
                        <label class="radio-inline">
                            <input type="radio" value="0" name="<?php echo $mode; ?>Vehicle[hasAccident]"
                                <?php echo !$hasAccident ? 'checked' : ''; ?> required> Non
                        </label>
                    </div>
                    <div class="col-lg-6">
                        <label class="radio-inline">
                            <input type="radio" value="1" name="<?php echo $mode; ?>Vehicle[hasAccident]"
                                <?php echo $hasAccident ? 'checked' : ''; ?> required> Oui
                        </label>
                    </div>
                </div>
                <div class="form-group col-lg-4 col-without-padding">
                    <label class="col-lg-12 form-control-label">Contrôle Technique :</label>
                    <div class="col-lg-6">
                        <label class="radio-inline">
                            <input type="radio" value="0" name="<?php echo $mode; ?>Vehicle[technicalInspection]"
                                <?php echo $isTechnicalInspectionOk ? 'checked' : ''; ?> required> 0 défauts
                        </label>
                    </div>
                    <div class="col-lg-6">
                        <label class="radio-inline">
                            <input type="radio" value="1" name="<?php echo $mode; ?>Vehicle[technicalInspection]"
                                <?php echo !$isTechnicalInspectionOk ? 'checked' : ''; ?> required> Problèmes
                        </label>
                    </div>
                </div>
                <div class="form-group col-lg-4 col-without-padding">
                    <label class="col-lg-12 form-control-label">Carnet d'entretien :</label>
                    <div class="col-lg-6">
                        <label class="radio-inline">
                            <input type="radio" value="0" name="<?php echo $mode; ?>Vehicle[maintenanceLog]"
                                <?php echo $isMaintenanceLogOk ? 'checked' : ''; ?> required> A jour
                        </label>
                    </div>
                    <div class="col-lg-6">
                        <label class="radio-inline">
                            <input type="radio" value="1" name="<?php echo $mode; ?>Vehicle[maintenanceLog]"
                                <?php echo !$isMaintenanceLogOk ? 'checked' : ''; ?> required> pas à jour
                        </label>
                    </div>
                </div>
            </div>
            <br><br>
            <h4 class="page-header">Equipements</h4>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleEquipments" class="col-lg-4 form-control-label">Principaux équipements
                        :</label>
                    <div class="col-lg-8">
                        <input type="text" class="form-control" id="inputVehicleEquipments"
                               data-formName="<?php echo $mode; ?>Vehicle"
                               value="<?php echo $equipments; ?>"
                               placeholder="Liste des équipements, séparés par un ';' (point-virgule)">
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="receiverVehicleEquipments" class="col-lg-8 col-lg-offset-4">

                </div>
            </div>
            <br><br>
            <h4 class="page-header">Autres Informations</h4>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleSuppComments" class="col-lg-4 form-control-label">Commentaires
                        supplémentaires :</label>
                    <div class="col-lg-8">
                    <textarea name="<?php echo $mode; ?>Vehicle[suppComments]" id="inputVehicleSuppComments"
                              style="width:100%;"
                              rows="3"><?php echo $suppComments; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleFunding" class="col-lg-4 form-control-label">Financement :</label>
                    <div class="col-lg-8">
                    <textarea name="<?php echo $mode; ?>Vehicle[funding]" id="inputVehicleFunding" style="width:100%;"
                              rows="3"><?php echo $funding; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleWarranty" class="col-lg-4 form-control-label">Garantie :</label>
                    <div class="col-lg-8">
                    <textarea name="<?php echo $mode; ?>Vehicle[warranty]" id="inputVehicleWarranty" style="width:100%;"
                              rows="3"><?php echo $warranty; ?></textarea>
                    </div>
                </div>
            </div>
            <br><br>
            <h4 class="page-header">Tarifs</h4>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehiclePrice" class="col-lg-3 form-control-label">Prix de vente* :</label>
                    <div class="col-lg-3 input-group">
                        <input type="text" id="inputVehiclePrice" class="form-control formManager"
                               value="<?php echo $price; ?>"
                               data-formManager="required float" name="<?php echo $mode; ?>Vehicle[price]" required>
                        <span class="input-group-addon">€ TTC</span>
                    </div>
                </div>
                <?php
                /*<div class="form-group">
                    <label for="inputVehicleSellerMargin" class="col-lg-3 form-control-label">Marge vendeur :</label>
                    <div class="col-lg-3 input-group">
                        <input type="text" id="inputVehicleSellerMargin" class="form-control formManager" value="<?php echo $sellerMargin; ?>"
                               data-formManager="required float" name="<?php echo $mode; ?>Vehicle[sellerMargin]" required>
                        <span class="input-group-addon">€ TTC</span>
                    </div>
                </div>*/
                ?>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleType" class="col-lg-2">Type de véhicule :</label>
                    <div class="col-lg-2">
                        <label class="radio-inline">
                            <input type="radio" value="0" name="<?php echo $mode; ?>Vehicle[depotSale]"
                                <?php echo !$depotSale ? 'checked' : ''; ?>> Véhicule sur parc
                        </label>
                    </div>
                    <div class="col-lg-2">
                        <label class="radio-inline">
                            <input type="radio" value="1" name="<?php echo $mode; ?>Vehicle[depotSale]"
                                <?php echo $depotSale ? 'checked' : ''; ?>> Dépôt Vente
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleAvailabilityDate" class="col-lg-3 form-control-label">Date de dispo.
                        :</label>
                    <div class="col-lg-3">
                        <input type="date" id="inputVehicleAvailabilityDate" class="form-control"
                               value="<?php echo $availabilityDate->format('Y-m-d'); ?>"
                               name="<?php echo $mode; ?>Vehicle[availabilityDate]" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleBuyingPrice" class="col-lg-3 form-control-label">Prix d'achat :</label>
                    <div class="col-lg-3 input-group">
                        <input type="text" class="form-control" name="<?php echo $mode; ?>Vehicle[buyingPrice]"
                               id="inputVehicleBuyingPrice" value="<?php echo $buyingPrice; ?>">
                        <span class="input-group-addon">€ TTC</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVatOnMargin" class="col-lg-2">TVA sur marge :</label>
                    <div class="col-lg-2">
                        <label class="radio-inline">
                            <input type="radio" value="1" name="<?php echo $mode; ?>Vehicle[vatOnMargin]"
                                <?php echo $vatOnMargin === true ? 'checked' : ''; ?>> Oui
                        </label>
                    </div>
                    <div class="col-lg-2">
                        <label class="radio-inline">
                            <input type="radio" value="0" name="<?php echo $mode; ?>Vehicle[vatOnMargin]"
                                <?php echo $vatOnMargin === false ? 'checked' : ''; ?>> Non
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleFeesAmount" class="col-lg-3 form-control-label">Frais de remise en état :</label>
                    <div class="col-lg-3 input-group">
                        <input type="text" class="form-control" id="inputVehicleFeesAmount"
                               name="<?php echo $mode; ?>Vehicle[feesAmount]" value="<?php echo $feesAmount; ?>">
                        <span class="input-group-addon">€</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleFeesDetails" class="col-lg-3 form-control-label">Détails de la remise en état :</label>
                    <div class="col-lg-3">
                        <textarea name="<?php echo $mode; ?>Vehicle[FeesDetails]" id="inputVehicleFeesDetails" style="width:100%;"
                              rows="3"><?php echo $feesDetails; ?></textarea>
                    </div>
                </div>
            </div>
            <br><br>
            <h4 class="page-header">Photos du Véhicule</h4>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleImage1" class="col-lg-2 form-control-label">Photo 1 :</label>
                    <div class="col-lg-4">
                        <label class="file">
                            <input type="file" id="inputVehicleImage1" name="<?php echo $mode; ?>Vehicle[image1]">
                            <span class="file-custom"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleImage2" class="col-lg-2 form-control-label">Photo 2 :</label>
                    <div class="col-lg-4">
                        <label class="file">
                            <input type="file" id="inputVehicleImage2" name="<?php echo $mode; ?>Vehicle[image2]">
                            <span class="file-custom"></span>
                        </label>
                    </div>
                </div>
            </div>
            <?php
            if($mode != 'copy'):
                ?>
                <div class="row">
                    <div class="col-lg-6">
                        <?php
                        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$image1)):
                            ?>
                            <img
                                src="<?php echo getAppPath().'/ressources/vehicleImages/VO/'.$image1; ?>"
                                alt="Photo 1" width="100%">
                            <?php
                        endif;
                        ?>
                    </div>
                    <div class="col-lg-6">
                        <?php
                        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$image2)):
                            ?>
                            <img
                                src="<?php echo getAppPath().'/ressources/vehicleImages/VO/'.$image2; ?>"
                                alt="Photo 2" width="100%">
                            <?php
                        endif;
                        ?>
                    </div>
                </div>
                <?php
            endif;
            ?>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleImage3" class="col-lg-2 form-control-label">Photo 3 :</label>
                    <div class="col-lg-4">
                        <label class="file">
                            <input type="file" id="inputVehicleImage3" name="<?php echo $mode; ?>Vehicle[image3]">
                            <span class="file-custom"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleImage4" class="col-lg-2 form-control-label">Photo 4 :</label>
                    <div class="col-lg-4">
                        <label class="file">
                            <input type="file" id="inputVehicleImage4" name="<?php echo $mode; ?>Vehicle[image4]">
                            <span class="file-custom"></span>
                        </label>
                    </div>
                </div>
            </div>
            <?php
            if($mode != 'copy'):
                ?>
                <div class="row">
                    <div class="col-lg-6">
                        <?php
                        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$image3)):
                            ?>
                            <img
                                src="<?php echo getAppPath().'/ressources/vehicleImages/VO/'.$image3; ?>"
                                alt="Photo 3" width="100%">
                            <?php
                        endif;
                        ?>
                    </div>
                    <div class="col-lg-6">
                        <?php
                        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$image4)):
                            ?>
                            <img
                                src="<?php echo getAppPath().'/ressources/vehicleImages/VO/'.$image4; ?>"
                                alt="Photo 4" width="100%">
                            <?php
                        endif;
                        ?>
                    </div>
                </div>
                <?php
            endif;
            ?>
            <br><br>
            <div class="form-group row">
                <div class="col-lg-2 col-lg-offset-8">
                    <button type="submit" class="btn btn-primary btn-principalColor formManager"
                            data-formManager="submitInput">
                        <?php
                        if($mode == 'create'){
                            echo 'Créer';
                        }
                        else if($mode == 'copy'){
                            echo 'Dupliquer';
                        }
                        else{
                            echo 'Modifier';
                        }
                        ?>
                        le véhicule
                    </button>
                </div>
            </div>
        </form>
    </div>
    <br><br><br>
    <?php
}
catch(Exception $e){
    msgReturn_push(array(0, $e->getMessage()));
}