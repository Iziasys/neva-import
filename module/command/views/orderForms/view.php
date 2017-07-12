<?php
/*****************RECUPERATION DES DONNEES EN BASE********************/
$db = databaseConnection();
$offer = \Offers\OfferManager::fetchOfferByReference($db, $offerReference);
if(is_a($offer, '\Exception')){
    $db = null;
    msgReturn_push(array(0, 'Erreur, cette offre n\'existe pas'));
}
else{
    $vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $offer->getVehicleId());
    $user = \Users\UserManager::fetchUser($db, $offer->getOwnerId());
    $dealer = \Users\StructureManager::fetchStructure($db, $user->getStructureId());
    $dealerDepartment = $dealer->getDepartment();
    $client = \Users\ClientManager::fetchClient($db, $offer->getClientId());
    $clientDepartment = empty($client->getPostalCode()) ? $dealerDepartment : $client->getDepartment();
    $clientId = $client->getId();

    $serialEquipments = \Vehicle\FinishManager::fetchSerialEquipments($db, $vehicle->getFinishId());
    $serialEquipments = \Vehicle\EquipmentManager::orderEquipmentsByFamily($db, $serialEquipments);
    $familiesList = \Vehicle\EquipmentManager::fetchFamiliesList($db);
    $freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $dealerDepartment);
    $horsepowerPrice = \Prices\HorsepowerPriceManager::fetchPriceFromDepartment($db, $clientDepartment);
    $db = null;
    /*****************RECUPERATION DES DONNEES EN BASE********************/

    $finish = $vehicle->getFinish();
    $model = $finish->getModel();
    $brand = $model->getBrand();

    $optionalEquipments = $offer->getOptions();
    $packs = $offer->getPacks();
    $color = $offer->getColor();
    $rims = $offer->getRims();

    $postTaxesOptionPrice = 0;
    if(!empty($optionalEquipments)){
        foreach($optionalEquipments as $equipment){
            $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($equipment->getPrice(), $offer->getVatRate()), 2);
        }
    }
    if(!empty($packs)){
        foreach($packs as $pack){
            $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($pack->getPrice(), $offer->getVatRate()), 2);
        }
    }
    $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($color->getPrice(), $offer->getVatRate()), 2);
    $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($rims->getPrice(), $offer->getVatRate()), 2);

    /** @var \Users\Structure $structure */
    $structure = $_SESSION['user']->getStructure();

    /*****************CALCUL DES TARIFS********************/
    //Définition du prix pour décentralisation de l'affichage
    $vatAmount = $offer->getVatRate();
    $buyingPrice = $offer->getVehiclePrice() - $offer->getFreightChargesToFrance();
    $changeRateToEuro = 1;
    $freightChargesToFrance = $offer->getFreightChargesToFrance();
    $marginAmount = $offer->getMarginAmount();
    $marginPercentage = 0;
    $managementFees = $offer->getManagementFees();
    $freightChargesInFrance = $offer->getFreightChargesInFrance();
    $dealerMargin = $offer->getDealerMargin();
    $packageProvision = $structure->getPackageProvision();
    $optionPrice = \Prices\VatManager::convertToPretax($postTaxesOptionPrice, $vatAmount);
    $registrationCardAmount = $horsepowerPrice->getRegistrationCardAmount($vehicle->getFiscalPower());
    $bonusPenalty = 0;

    $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
                                             $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty);
    /*****************CALCUL DES TARIFS********************/
    ?>
    <h1 class="page-header text-lg-center">
        Bon de Commande N°<?php echo $offerReference; ?><br>
        <small class="text-small">
            En date du : <?php echo $offer->getCreationDate()->format('d/m/Y'); ?>
        </small>
    </h1>

    <br>

    <div id="detailsVehicle col-lg-12">
        <form action="/commande-vehicules/offre-de-prix/creer" method="post" id="formCreateOffer">
            <input type="hidden" name="createOffer[vehicleId]" value="<?php echo $vehicle->getId(); ?>">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div id="block-vehicle-information" class="col-lg-12 card-group col-without-padding">
                        <div
                            class="card card-without-padding card-without-margin card-without-border card-with-right-border"
                            style="width:72%;padding:10px;">
                            <h3 id="block-vehicle-information-title" class="col-lg-12">
                                <?php echo $brand->getName().' '.$model->getName(); ?><br>
                                <small><?php echo $finish->getName().' - '.$vehicle->getBodywork()->getName(); ?></small>
                            </h3>
                            <div id="block-vehicle-information-content" class="col-lg-12 text-small">
                                <div class="row">
                                    <div class="col-lg-3">Marque :</div>
                                    <div class="col-lg-3"><b><?php echo $brand->getName(); ?></b></div>
                                    <div class="col-lg-3">Modèle :</div>
                                    <div class="col-lg-3"><b><?php echo $model->getName(); ?></b></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">Finition :</div>
                                    <div class="col-lg-3"><b><?php echo $finish->getName(); ?></b></div>
                                    <div class="col-lg-3">Carrosserie :</div>
                                    <div class="col-lg-3"><b><?php echo $vehicle->getBodywork()->getName(); ?></b></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">Cylindrée :</div>
                                    <div class="col-lg-3"><b><?php echo $vehicle->getEngineSize(); ?></b></div>
                                    <div class="col-lg-3">Motorisation :</div>
                                    <div class="col-lg-3"><b><?php echo $vehicle->getEngine()->getName(); ?></b></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">Puissance Dynamique :</div>
                                    <div class="col-lg-3"><b><?php echo $vehicle->getDynamicalPower(); ?></b></div>
                                    <div class="col-lg-3">Carburant :</div>
                                    <div class="col-lg-3"><b><?php echo $vehicle->getFuel()->getName(); ?></b></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">Boite de vitesse :</div>
                                    <div class="col-lg-3"><b><?php echo $vehicle->getGearbox()->getName(); ?></b></div>
                                    <div class="col-lg-3">Taux de Co2 :</div>
                                    <div class="col-lg-3"><b><?php echo $vehicle->getCo2(); ?></b></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3">Portes/Places :</div>
                                    <div class="col-lg-3">
                                        <b><?php echo $vehicle->getDoorsAmount().'portes / '.$vehicle->getSitsAmount().' places'; ?></b>
                                    </div>
                                    <div class="col-lg-3">Puissance Fiscale :</div>
                                    <div class="col-lg-3"><b><?php echo $vehicle->getFiscalPower(); ?></b></div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-without-margin card-without-border">
                            <?php
                            $imagePath = '/ressources/vehicleImages/'.$brand->getName().'/'.$model->getName().'/'.$finish->getName().'/'.$vehicle->getBodywork()->getName().'_'.$vehicle->getDoorsAmount().'.png';
                            if(!is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
                                $imagePath = '/ressources/vehicleImages/'.$brand->getName().'/'.$model->getName().'/'.$finish->getName().'/'.$vehicle->getBodywork()->getName().'_'.$vehicle->getDoorsAmount().'.jpg';
                            }
                            ?>
                            <img
                                src="<?php echo $imagePath; ?>"
                                width="100%"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <br><br><br>
            <div class="row">
                <div class="col-lg-12 ">
                    <h3 class="page-header col-lg-5 col-lg-offset-1">Equipement de série</h3>
                    <div class="col-lg-12">
                        <?php
                        foreach($familiesList as $key => $family):
                            ?>
                            <div class="col-lg-2 <?php echo $key == 0 ? 'col-lg-offset-1' : ''; ?> text-lg-left">
                                <?php
                                echo '<b class="text-primaryColor text-uppercase">'.$family['name'].'</b>';
                                if(!empty($serialEquipments[$family['name']])):
                                    foreach($serialEquipments[$family['name']] as $equipment):
                                        /** @var \Vehicle\Equipment $equipment */
                                        $equipment = $equipment;
                                        ?>
                                        <div class="row text-lg-left text-small">
                                            <div class="col-lg-12">
                                                <?php echo $equipment->getName(); ?>
                                            </div>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                            <?php
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
            <br><br>
            <hr>
            <br><br>
            <div class="row">
                <div class="col-lg-5 col-lg-offset-1">
                    <h3 class="page-header col-lg-12">Equipements optionnels</h3>
                    <?php
                    if(!is_a($optionalEquipments, '\Exception')):
                        foreach($optionalEquipments as $equipment):
                            /** @var \Offers\Option $equipment */
                            $equipment = $equipment;
                            $sellingPrice = \Prices\VatManager::convertToPostTaxes($equipment->getPrice(), $offer->getVatRate());
                            $db = databaseConnection();
                            $equipmentInformation = \Vehicle\EquipmentManager::fetchEquipment($db, $equipment->getItemId());
                            $db = null;
                            ?>
                            <label class="col-lg-12 striped">
                        <span class="col-lg-8">
                            <?php echo $equipmentInformation->getName(); ?>
                        </span>
                        <span class="col-lg-4 text-lg-right">
                            <?php echo round($sellingPrice, 2).' € TTC'; ?>
                        </span>
                            </label>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <div class="col-lg-5">
                    <h3 class="page-header col-lg-12">Packs</h3>
                    <?php
                    if(!is_a($packs, '\Exception')):
                        foreach($packs as $pack):
                            /** @var \Offers\Pack $pack */
                            $pack = $pack;
                            $sellingPrice = \Prices\VatManager::convertToPostTaxes($pack->getPrice(), $offer->getVatRate());
                            $db = databaseConnection();
                            $packInformation = \Vehicle\PackManager::fetchPack($db, $pack->getItemId());
                            $equipmentsArray = \Vehicle\PackManager::fetchEquipments($db, $packInformation->getId());
                            $color = \Vehicle\PackManager::fetchColor($db, $packInformation->getId());
                            $rims = \Vehicle\PackManager::fetchRim($db, $packInformation->getId());
                            $db = null;
                            $equipmentsList = '';
                            foreach($equipmentsArray as $key => $equipment):
                                if($key > 0):
                                    $equipmentsList .= ', ';
                                endif;
                                $equipmentsList .= $equipment->getName();
                            endforeach;
                            ?>
                            <label class="col-lg-12 striped hoverable small-line-height">
                        <span class="col-lg-8">
                            <?php echo $packInformation->getName(); ?><br>
                            <span class="text-very-small small-line-height">
                                <?php echo $equipmentsList; ?>
                                <?php
                                if(!is_a($color, '\Exception')):
                                    echo '<br>Couleur : '.$color->getName().' '.$color->getDetails();
                                endif;
                                if(!is_a($rims, '\Exception')):
                                    echo '<br>Jantes : '.$rims->getName().' - '.$rims->getRimType().' '.$rims->getFrontDiameter().'"';
                                endif;
                                ?>
                            </span>
                        </span>
                        <span class="col-lg-4 text-lg-right">
                            <?php echo round($sellingPrice, 2).' € TTC'; ?>
                        </span>
                            </label>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-5 col-lg-offset-1">
                    <h3 class="page-header col-lg-12"><label for="selectVehicleColor">Peinture</label></h3>
                    <div class="col-lg-12">
                        <?php
                        $color = $offer->getColor();
                        if(!is_a($color, '\Exception')):
                            $sellingPrice = \Prices\VatManager::convertToPostTaxes($color->getPrice(), $offer->getVatRate());
                            $db = databaseConnection();
                            $colorInformation = \Vehicle\ExternalColorManager::fetchColor($db, $color->getItemId());
                            $db = null;
                            if(is_a($colorInformation, '\Exception')):
                                echo 'Couleur de série';
                            else:
                                echo $colorInformation->getName().' '.$colorInformation->getDetails().' - '.number_format($sellingPrice, 2, '.', ' ').' € TTC';
                            endif;
                        endif;
                        ?>
                    </div>

                </div>
                <div class="col-lg-5">
                    <h3 class="page-header col-lg-12"><label for="selectVehicleRims">Jantes</label></h3>
                    <div class="col-lg-12">
                        <?php
                        $rims = $offer->getRims();
                        if(!is_a($rims, '\Exception')):
                            $sellingPrice = \Prices\VatManager::convertToPostTaxes($rims->getPrice(), $offer->getVatRate());
                            $db = databaseConnection();
                            $rimsInformation = \Vehicle\RimModelManager::fetchRimModel($db, $rims->getItemId());
                            $db = null;
                            if(is_a($rimsInformation, '\Exception')):
                                echo 'Jantes de série';
                            else:
                                echo $rimsInformation->getRimType().' '.$rimsInformation->getFrontDiameter().'" - '.$rimsInformation->getName().' - '.number_format($sellingPrice, 2, '.', ' ').' € TTC';
                            endif;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            <br><br><br>
            <div class="row">
                <div class="col-lg-5 col-lg-offset-1">
                    <div class="col-lg-6">
                        <a href="/commande-vehicules/bons-de-commande/imprimer/<?php echo $offerReference; ?>" type="button" class="btn btn-primary" target="_blank">Imprimer</a>
                    </div>
                    <div class="col-lg-6">
                        <input type="button" class="btn btn-primary" id="btnDetails" value="Détails">
                    </div>
                </div>
                <div class="col-lg-5 text-small">
                    <div class="invisible detailsRow" style="display:none;">
                        <div class="col-lg-8">Transport depuis 68127 :</div>
                        <div class="col-lg-4 text-lg-right"><?php echo number_format($priceDetails->getPostTaxesFreightChargesInFrance(), 2, '.', ' '); ?>
                            € TTC</div>
                    </div>
                    <div class="row striped">
                        <div class="col-lg-8">Prix du véhicule (hors options) :</div>
                        <div class="col-lg-4 text-lg-right">
                            <?php echo number_format($priceDetails->getPostTaxesDealerSellingPrice(), 2, '.', ' '); ?> € TTC
                        </div>
                    </div>
                    <div class="row striped">
                        <div class="col-lg-8">Sous total options :</div>
                        <div
                            class="col-lg-4 text-lg-right"><?php echo number_format($priceDetails->getPostTaxesOptionPrice(), 2, '.', ' '); ?>
                            € TTC
                        </div>
                    </div>
                    <div class="row striped">
                        <div class="col-lg-8">Sous total pour le véhicule :</div>
                        <div
                            class="col-lg-4 text-lg-right"><?php echo number_format($priceDetails->getPostTaxesClientBuyingPrice(), 2, '.', ' '); ?>
                            € TTC
                        </div>
                    </div>
                    <div class="row striped">
                        <div class="col-lg-8">Forfait de mise à disposition :</div>
                        <div
                            class="col-lg-4 text-lg-right"><?php echo number_format($priceDetails->getPostTaxesPackageProvision(), 2, '.', ' '); ?>
                            € TTC
                        </div>
                    </div>
                    <div class="row striped">
                        <div class="col-lg-8">Prix de vente :</div>
                        <div
                            class="col-lg-4 text-lg-right"><?php echo number_format($priceDetails->getPostTaxesAllIncludedPrice(), 2, '.', ' '); ?>
                            € TTC
                        </div>
                    </div>
                    <div class="row striped">
                        <div class="col-lg-8">Carte grise dans le <?php echo $dealer->getDepartment(); ?> :*</div>
                        <div class="col-lg-4 text-lg-right"><?php echo $priceDetails->getRegistrationCardAmount(); ?> €&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <br><br><br><br>
    <?php
}