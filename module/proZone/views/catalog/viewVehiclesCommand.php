<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();
$dealer = $user->getStructure();
$dealerDepartment = $dealer->getDepartment();
if(empty($_SESSION['selectedClient'])){
    $client = null;
    $clientDepartment = $dealerDepartment;
    $clientId = 0;
}
else{
    /** @var \Users\Client|null $client */
    $client = $_SESSION['selectedClient'];
    $clientDepartment = empty($client->getPostalCode()) ? $dealerDepartment : $client->getDepartment();
    $clientId = $client->getId();
}

try{
    if(empty($_GET['brandName']) || empty($_GET['modelName'])){
        throw new Exception('Erreur lors de la récupération des critères...');
    }

    $brandName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['brandName']);
    $modelName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['modelName']);

    ?>
    <h2 class="page-header text-lg-center">Visualisation des véhicules disponibles</h2>
    <div class="container">
    <br><br>
    <a href="/espace-pro/vehicules-demande/visualiser">Tous nos véhicules</a>
    -->
    <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $_GET['brandName']; ?>"><?php echo $brandName; ?></a>
    -->
    <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $_GET['brandName']; ?>/<?php echo $_GET['modelName']; ?>"><?php echo $modelName; ?></a>
    -->
    Commande VN
    </div>
    <?php

    $brandName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['brandName']);
    $modelName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['modelName']);

    $db = databaseConnection();
    $model = \Vehicle\ModelManager::fetchModelByName($db, $modelName);
    if(is_a($model, '\Exception')){
        $db = null;
        throw new Exception('Désolé, aucun véhicule n\'est disponible actuellement...');
    }
    $vehiclesList = \Vehicle\DetailsManager::fetchVehicleListFromModel($db, $model->getId(), array(array('orderBy' => 'finishName', 'way' => 'ASC')));
    $vat = \Prices\VatManager::fetchFrenchVat($db);
    $db = null;
    if(is_a($vehiclesList, '\Exception')):
        throw new Exception('Désolé, aucun véhicule n\'est disponible actuellement...');
    endif;

    /**
     * Tableau qui contiendra des véhicules à chaque index de finition
     * Tableau de la forme :
     * [finishName:
     *      [ vehicleId:
     *          [vehicle: \Vehicle\Details,
     *           price: \Price\PriceDetails
     *          ]
     *      ]
     * ]
     */
    $vehiclesArray = array();
    //Parcours de chaque véhicule
    foreach($vehiclesList as $vehicle):
        $vehicleFinish = $vehicle->getFinish();

        $vehiclePrice = $vehicle->getPrice();
        $currency = $vehiclePrice->getCurrency();

        $db = databaseConnection();
        $country = \Prices\CountryManager::fetchCountry($db, $vehiclePrice->getCountryId());
        $horsepowerPrice = \Prices\HorsepowerPriceManager::fetchPriceFromDepartment($db, $clientDepartment);
        $freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $dealerDepartment);
        $db = null;

        /*****************CALCUL DES TARIFS********************/
        //Définition du prix pour décentralisation de l'affichage
        $buyingPrice = $vehiclePrice->getPretaxBuyingPrice();
        $changeRateToEuro = $currency->getExchangeRate()->getRateToEuro();
        $freightChargesToFrance = $country->getFreightCharges()->getAmount();
        $marginAmount = 0.0;
        $marginPercentage = $vehiclePrice->getMargin();
        $managementFees = $vehiclePrice->getManagementFees();
        if(!$structure->getIsPartner()){
            $managementFees = 0;
        }
        if($structure->getFreightCharges() == 0){
            $freightChargesInFrance = $freightCharges->getAmount();
        }
        else{
            $freightChargesInFrance = $structure->getFreightCharges();
        }
        $dealerMargin = $structure->getDefaultMargin();
        $packageProvision = $structure->getPackageProvision();
        $optionPrice = 0.0;
        $vatAmount = $vat->getAmount();
        $registrationCardAmount = $horsepowerPrice->getRegistrationCardAmount($vehicle->getFiscalPower());
        $bonusPenalty = 0;

        $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
                                                 $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty);
        /*****************CALCUL DES TARIFS********************/

        $vehicleInformation = array(
            'vehicle' => $vehicle,
            'price'   => $priceDetails
        );

        $vehiclesArray[$vehicleFinish->getName()][] = $vehicleInformation;
    endforeach;

    ?>
    <div class="container">
        <?php
        $finishesSerialEquipments = array();
        foreach($vehiclesArray as $finishName => $vehiclesList):
            /** @var \Vehicle\Details $vehicle */
            $vehicle = $vehiclesList[0]['vehicle'];
            $finish = $vehicle->getFinish();

            $db = databaseConnection();
            $serialEquipments = \Vehicle\FinishManager::fetchSerialEquipments($db, $finish->getId());
            $db = null;

            $serialEquipmentsList = '';
            foreach($serialEquipments as $key => $equipment):
                if($key):
                    $serialEquipmentsList .= ' + ';
                endif;
                $serialEquipmentsList .= $equipment->getName();
                $finishesSerialEquipments[$finishName][] = $equipment->getId();
            endforeach;
            ?>
            <br><br>
            <h4 class="page-header"><?php echo $brandName.' '.$modelName.' '.$finishName; ?></h4>
            <div class="col-lg-12">
                <?php echo $serialEquipmentsList; ?>
            </div>
            <?php
            foreach($vehiclesList as $vehicleId => $vehicleInformation):
                /** @var \Vehicle\Details $vehicle */
                $vehicle = $vehicleInformation['vehicle'];
                /** @var \Prices\PriceDetails $priceDetails */
                $priceDetails = $vehicleInformation['price'];
                ?>
                    <div class="row">
                        <div class="col-lg-2 col-lg-offset-1 text-danger">
                            <b><?php echo number_format($priceDetails->getPostTaxesDealerBuyingPrice(), 2, '.', ' '); ?> € TTC</b>
                        </div>
                        <div class="col-lg-2">
                            <?php echo $vehicle->getEngineSize().' '.$vehicle->getEngine()->getName().' '.$vehicle->getDynamicalPower().' '.$vehicle->getTransmission()->getName(); ?>
                        </div>
                        <div class="col-lg-2">
                            Co2 = <?php echo $vehicle->getCo2(); ?> g/Km
                        </div>
                        <div class="col-lg-4">
                            <?php echo $vehicle->getDoorsAmount().' portes/'.$vehicle->getSitsAmount().' places -- Boîte '.$vehicle->getGearbox()->getName(); ?>
                        </div>
                    </div>
                <?php
            endforeach;
        endforeach;

        /**
         * Construction du tableau options
         * Sous la forme :
         * $finishesOptions =
         * [
         *      equipmentId:
         *      [
         *          'name': string
         *          finishName: string
         *      ]
         * ]
         * Et un second tableau sous la forme
         * $finishesOptionsIds =
         * [
         *      finishName: int[] equipmentsIds
         * ]
         */
        // TODO : Reflechir au format du tableau et le remplir !!!
        $finishesOptionsIds = array();
        $finishesOptions = array();

        $finishesPacksIds = array();
        $finishesPacks = array();

        $finishesColorsIds = array();
        $finishesColors = array();

        $finishesRimsIds = array();
        $finishesRims = array();

        foreach($vehiclesArray as $finishName => $vehiclesList):
            /** @var \Vehicle\Details $vehicle */
            $vehicle = $vehiclesList[0]['vehicle'];
            $finish = $vehicle->getFinish();

            //Récupération en base des données de la finition
            $db = databaseConnection();
            $options = \Vehicle\FinishManager::fetchOptionalEquipments($db, $finish->getId());
            $packs = \Vehicle\FinishManager::fetchPacks($db, $finish->getId());
            $colors = \Vehicle\FinishManager::fetchExternalColors($db, $finish->getId());
            $rims = \Vehicle\FinishManager::fetchRims($db, $finish->getId());
            $db = null;

            //On regarde les équipements que cette finition possède
            foreach($options as $optionTested):
                //Si le tableau des options ne contient pas encore cet item
                if(empty($finishesOptionsIds[$finishName]) || !in_array($optionTested->getId(), $finishesOptionsIds[$finishName])):
                    //On insère l'ID de l'équipement dans le tableau
                    $finishesOptionsIds[$finishName][] = $optionTested->getId();
                    //Et on créé l'item dans le tableau
                    $finishesOptions[$optionTested->getId()]['name'] = $optionTested->getName();
                endif;

                //Calcul de l'info à afficher dans la case
                $toPrint = '-';
                if(in_array($optionTested->getId(), $finishesSerialEquipments[$finishName])):
                    $toPrint = 'S';
                endif;
                $db = databaseConnection();
                $equipmentCurrency = \Prices\CurrencyManager::fetchCurrency($db, $optionTested->getPrice()->getCurrencyId());
                $db = null;
                $sellingPrice = $optionTested->getPrice()->getPretaxSellingPrice() * $equipmentCurrency->getExchangeRate()->getRateToEuro();
                $sellingPrice = \Prices\VatManager::convertToPostTaxes($sellingPrice, $vat->getAmount());

                if($sellingPrice == '0'):
                    $toPrint = 'Gratuit';
                else:
                    $toPrint = number_format($sellingPrice, 2, '.', ' ').' € TTC';
                endif;

                $finishesOptions[$optionTested->getId()][$finishName] = $toPrint;
            endforeach;

            //On regarde les packs que cette finition possède
            foreach($packs as $packTested):
                /** @var \Vehicle\Pack $pack */
                $pack = $packTested['pack'];
                /** @var \Prices\Price $price */
                $price = $packTested['price'];

                //Si le tableau des packs ne contient pas encore cet item
                if(empty($finishesPacksIds[$finishName]) || !in_array($pack->getId(), $finishesPacksIds[$finishName])):
                    //Récupération des informations du pack
                    $db = databaseConnection();
                    $equipmentsArray = \Vehicle\PackManager::fetchEquipments($db, $pack->getId());
                    $colorInPack = \Vehicle\PackManager::fetchColor($db, $pack->getId());
                    $rimsInPack = \Vehicle\PackManager::fetchRim($db, $pack->getId());
                    $db = null;
                    //Dressage de la liste des équipements/couleurs/jantes contenues dans le pack
                    $equipmentsList = '';
                    foreach($equipmentsArray as $key => $equipment):
                        if($key > 0):
                            $equipmentsList .= ' + ';
                        endif;
                        $equipmentsList .= $equipment->getName();
                    endforeach;
                    if(!is_a($colorInPack, '\Exception')):
                        $equipmentsList .= ' + '.$colorInPack->getName().' '.$colorInPack->getDetails();
                    endif;
                    if(!is_a($rimsInPack, '\Exception')):
                        $equipmentsList .= ' + '.$rimsInPack->getRimType().' '.$rimsInPack->getFrontDiameter().'" - '.$rimsInPack->getName();
                    endif;

                    //On insère l'ID de l'équipement dans le tableau
                    $finishesPacksIds[$finishName][] = $pack->getId();
                    //Et on créé l'item dans le tableau
                    $finishesPacks[$pack->getId()]['name'] = $pack->getName().' : '.$equipmentsList;
                endif;

                //Calcul de l'info à afficher dans la case
                $toPrint = '-';
                if($price == null):
                    $toPrint = 'Gratuit';
                else:
                    $db = databaseConnection();
                    $packCurrency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                    $db = null;
                    $sellingPrice = $price->getPretaxSellingPrice() * $packCurrency->getExchangeRate()->getRateToEuro();
                    $sellingPrice = \Prices\VatManager::convertToPostTaxes($sellingPrice, $vat->getAmount());

                    $toPrint = number_format($sellingPrice, 2, '.', ' ').' € TTC';
                endif;

                $finishesPacks[$pack->getId()][$finishName] = $toPrint;
            endforeach;

            //On regarde les couleurs que cette finition possède
            foreach($colors as $colorTested):
                /** @var \Vehicle\ExternalColor $color */
                $color = $colorTested['color'];
                /** @var \Prices\Price $price */
                $price = $colorTested['price'];

                //Si le tableau des options ne contient pas encore cet item
                if(empty($finishesColorsIds[$finishName]) || !in_array($color->getId(), $finishesColorsIds[$finishName])):
                    //On insère l'ID de la couleur dans le tableau
                    $finishesColorsIds[$finishName][] = $color->getId();
                    //Et on créé l'item dans le tableau
                    $finishesColors[$color->getId()]['name'] = $color->getName().' '.$color->getDetails();
                endif;

                //Calcul de l'info à afficher dans la case
                $toPrint = '-';
                if($price == null):
                    $toPrint = 'Gratuit';
                else:
                    if($price->getPretaxSellingPrice() == '0'):
                        $toPrint = 'Gratuit';
                    else:
                        $db = databaseConnection();
                        $colorCurrency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                        $db = null;
                        $sellingPrice = $price->getPretaxSellingPrice() * $colorCurrency->getExchangeRate()->getRateToEuro();
                        $sellingPrice = \Prices\VatManager::convertToPostTaxes($sellingPrice, $vat->getAmount());

                        $toPrint = number_format($sellingPrice, 2, '.', ' ').' € TTC';
                    endif;
                endif;

                $finishesColors[$color->getId()][$finishName] = $toPrint;
            endforeach;

            //On regarde les jantes que cette finition possède
            foreach($rims as $rimsTested):
                /** @var \Vehicle\RimModel $rim */
                $rim = $rimsTested['rim'];
                /** @var \Prices\Price $price */
                $price = $rimsTested['price'];

                //Si le tableau des options ne contient pas encore cet item
                if(empty($finishesRimsIds[$finishName]) || !in_array($rim->getId(), $finishesRimsIds[$finishName])):
                    //On insère l'ID de la couleur dans le tableau
                    $finishesRimsIds[$finishName][] = $rim->getId();
                    //Et on créé l'item dans le tableau
                    $finishesRims[$rim->getId()]['name'] = $rim->getRimType().' '.$rim->getFrontDiameter().'" - '.$rim->getName();
                endif;

                //Calcul de l'info à afficher dans la case
                $toPrint = '-';
                if($price == null):
                    $toPrint = 'Gratuit';
                else:
                    if($price->getPretaxSellingPrice() == '0'):
                        $toPrint = 'Gratuit';
                    else:
                        $db = databaseConnection();
                        $rimCurrency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                        $db = null;
                        $sellingPrice = $price->getPretaxSellingPrice() * $rimCurrency->getExchangeRate()->getRateToEuro();
                        $sellingPrice = \Prices\VatManager::convertToPostTaxes($sellingPrice, $vat->getAmount());

                        $toPrint = number_format($sellingPrice, 2, '.', ' ').' € TTC';
                    endif;
                endif;

                $finishesRims[$rim->getId()][$finishName] = $toPrint;
            endforeach;
        endforeach;
        ?>
        <br><br><br>
        <h4 class="page-header text-lg-center">Tableau des options</h4>
        <table class="table table-striped table-bordered text-small">
            <thead>
            <tr>
                <th>OPTIONS</th>
                <?php
                foreach($vehiclesArray as $finishName => $vehiclesList):
                    ?>
                    <th style="white-space: nowrap;"><?php echo $finishName; ?></th>
                    <?php
                endforeach;
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($finishesOptions as $information):
                $optionName = $information['name'];
                ?>
                <tr>
                    <td><?php echo $optionName; ?></td>
                    <?php
                    foreach($vehiclesArray as $finishName => $vehiclesList):
                        ?>
                        <td class="text-lg-center" style="white-space: nowrap;">
                            <?php
                            if(empty($information[$finishName])):
                                echo '-';
                            else:
                                echo $information[$finishName];
                            endif;
                            ?>
                        </td>
                        <?php
                    endforeach;
                    ?>
                </tr>
                <?php
            endforeach;
            foreach($finishesPacks as $information):
                $packName = $information['name'];
                ?>
                <tr>
                    <td><?php echo $packName; ?></td>
                    <?php
                    foreach($vehiclesArray as $finishName => $vehiclesList):
                        ?>
                        <td class="text-lg-center" style="white-space: nowrap;">
                            <?php
                            if(empty($information[$finishName])):
                                echo '-';
                            else:
                                echo $information[$finishName];
                            endif;
                            ?>
                        </td>
                        <?php
                    endforeach;
                    ?>
                </tr>
                <?php
            endforeach;
            foreach($finishesColors as $information):
                $colorName = $information['name'];
                ?>
                <tr>
                    <td><?php echo $colorName; ?></td>
                    <?php
                    foreach($vehiclesArray as $finishName => $vehiclesList):
                        ?>
                        <td class="text-lg-center" style="white-space: nowrap;">
                            <?php
                            if(empty($information[$finishName])):
                                echo '-';
                            else:
                                echo $information[$finishName];
                            endif;
                            ?>
                        </td>
                        <?php
                    endforeach;
                    ?>
                </tr>
                <?php
            endforeach;
            foreach($finishesRims as $information):
                $rimName = $information['name'];
                ?>
                <tr>
                    <td><?php echo $rimName; ?></td>
                    <?php
                    foreach($vehiclesArray as $finishName => $vehiclesList):
                        ?>
                        <td class="text-lg-center" style="white-space: nowrap;">
                            <?php
                            if(empty($information[$finishName])):
                                echo '-';
                            else:
                                echo $information[$finishName];
                            endif;
                            ?>
                        </td>
                        <?php
                    endforeach;
                    ?>
                </tr>
                <?php
            endforeach;
            ?>
            </tbody>
        </table>
        VEHICULE NEUF AVEC IMMATRICULATION ETRANGERE - DOCUMENTS SOUS 15 JOURS<br>
        CLIENT FINAL OBLIGATOIRE
    </div>
    <br><br>
    <?php
}
catch(Exception $e){
    echo $e->getMessage();
}