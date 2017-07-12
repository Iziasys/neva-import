<?php
$vehicleId = (int)$_GET['vehicleId'];
/** @var \Users\User $user */
$user = $_SESSION['user'];
/** @var \Users\Structure $dealer */
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

/*****************RECUPERATION DES DONNEES EN BASE********************/
$db = databaseConnection();
$vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $vehicleId);
$serialEquipments = \Vehicle\FinishManager::fetchSerialEquipments($db, $vehicle->getFinishId());
$serialEquipments = \Vehicle\EquipmentManager::orderEquipmentsByFamily($db, $serialEquipments);
$familiesList = \Vehicle\EquipmentManager::fetchFamiliesList($db);
$optionalEquipments = \Vehicle\FinishManager::fetchOptionalEquipments($db, $vehicle->getFinishId());
$packs = \Vehicle\FinishManager::fetchPacks($db, $vehicle->getFinishId());
$colorsAssoc = \Vehicle\FinishManager::fetchExternalColors($db, $vehicle->getFinishId());
$rimsAssoc = \Vehicle\FinishManager::fetchRims($db, $vehicle->getFinishId());
$vat = \Prices\VatManager::fetchFrenchVat($db);
$country = \Prices\CountryManager::fetchCountry($db, $vehicle->getPrice()->getCountryId());
$freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $dealerDepartment);
$horsepowerPrice = \Prices\HorsepowerPriceManager::fetchPriceFromDepartment($db, $clientDepartment);
$clients = \Users\ClientManager::fetchClientsList($db, $dealer->getId(), $dealer->getIsPrimary());
$db = null;
/*****************RECUPERATION DES DONNEES EN BASE********************/

$finish = $vehicle->getFinish();
$model = $finish->getModel();
$brand = $model->getBrand();
$vehiclePrice = $vehicle->getPrice();
$currency = $vehiclePrice->getCurrency();

/** @var \Users\Structure $structure */
$structure = $_SESSION['user']->getStructure();

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
if($structure->getFreightCharges() === null){
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
?>

<!-- MODAL DE CHOIX DU CLIENT -->
<div class="modal fade select-client-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title" id="exampleModalLabel">Choix du client</h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="col-lg-6">
                        <h4 class="page-header"><label for="inputSelectClient">Sélectionnez le dans la base :</label></h4>
                        <select id="inputSelectClient" class="form-control">
                            <option value="0">Sélectionnez un client</option>
                            <?php
                            foreach($clients as $cli):
                                if($cli->getIsSociety()):
                            ?>
                                <option value="<?php echo $cli->getId(); ?>"><?php echo $cli->getName(); ?></option>
                            <?php
                                else:
                                    ?>
                                    <option value="<?php echo $cli->getId(); ?>" <?php echo $cli->getId() == $clientId ? 'selected' : ''; ?>>
                                        <?php echo $cli->getFirstName().' '.$cli->getLastName(); ?>
                                    </option>
                                    <?php
                                endif;
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-6 col-with-left-border">
                        <h4 class="page-header">Ou créez le maintenant :<br>
                            <small>Vous le complèterez plus tard</small></h4>
                        <form action="#" id="formCreateClient" method="post">
                            <input type="hidden" id="createClient_structureId" value="<?php echo $user->getStructureId(); ?>">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <label class="radio-inline">
                                        <input type="radio" class="radio-for-isSociety" id="createClient_isSociety"
                                               name="createClientSociety" value="0" checked> Client particulier
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" class="radio-for-isSociety" id="createClient_isSociety"
                                               name="createClientSociety" value="1"> Client société
                                    </label>
                                </div>
                            </div>
                            <div class="rowForSociety">
                                <div class="form-group">
                                    <label for="createClient_societyName" class="form-control-label">Nom de la société :</label>
                                    <input type="text" class="form-control" id="createClient_societyName">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Cvilité :</label>
                                <label class="radio-inline">
                                    <input type="radio" id="createClient_civility" name="createClientCivility" value="Mme"> Mme.
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="createClient_civility" name="createClientCivility" value="M" checked> M.
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="createClient_lastName" class="form-control-label">Nom :</label>
                                <input type="text" class="form-control" id="createClient_lastName" required>
                            </div>
                            <div class="form-group">
                                <label for="createClient_firstName" class="form-control-label">Prénom :</label>
                                <input type="text" class="form-control" id="createClient_firstName">
                            </div>
                            <div class="form-group">
                                <label for="createClient_email" class="form-control-label">E-mail :</label>
                                <input type="text" class="form-control" id="createClient_email">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Accepte les offres mail :</label>
                                <label class="radio-inline">
                                    <input type="radio" id="createClient_acceptOffers" name="createClientAcceptOffers" value="0" required> Non
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="createClient_acceptOffers" name="createClientAcceptOffers" value="1" required> Oui
                                </label>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-4 col-lg-offset-5">
                        <button type="button" class="btn btn-primary btn-principalColor formManager" id="btnSelectClient">
                            Valider
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL DE CHOIX DU CLIENT -->

<h1 class="page-header text-lg-center">
    Fiche Technique<br>
    <small class="text-small">
        Dernière mise à jour le : <?php echo $vehiclePrice->getPriceDate()->format('d/m/Y'); ?>
    </small>
</h1>

<br>

<div id="detailsVehicle col-lg-12">
    <form action="/commande-vehicules/offre-de-prix/creer" method="post" id="formCreateOffer">
        <input type="hidden" name="createOffer[vehicleId]" value="<?php echo $vehicle->getId(); ?>">
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div id="block-vehicle-information" class="col-lg-12 card-group col-without-padding">
                    <div class="card card-without-padding card-without-margin card-without-border card-with-right-border" style="width:72%;padding:10px;">
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
                                <div class="col-lg-3"><b><?php echo $vehicle->getDoorsAmount().'portes / '.$vehicle->getSitsAmount().' places'; ?></b></div>
                                <div class="col-lg-3">Puissance Fiscale :</div>
                                <div class="col-lg-3"><b><?php echo $vehicle->getFiscalPower(); ?></b></div>
                            </div>
                            <!--
                            <div class="row">
                                <div class="col-lg-3">Prix du véhicule hors options :</div>
                                <div class="col-lg-9">
                                    <b><span class="vehiclePostTaxesPrice"><?php echo number_format($postTaxesVehiclePrice, 0, '.', ' '); ?></span> € TTC</b>
                                </div>
                            </div>-->
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
                        <div class="col-lg-2 text-lg-left">
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
        <br><br><hr><br><br>
        <div class="row">
            <div class="col-lg-5 col-lg-offset-1">
                <h3 class="page-header col-lg-12">Equipements optionnels</h3>
                <?php
                if(!is_a($optionalEquipments, '\Exception')):
                    foreach($optionalEquipments as $equipment):
                        /** @var \Vehicle\OptionalEquipment $equipment */
                        $equipment = $equipment;
                        $db = databaseConnection();
                        $currency = \Prices\CurrencyManager::fetchCurrency($db, $equipment->getPrice()->getCurrencyId());
                        $db = null;
                        $equipmentPrice = $equipment->getPrice();
                        $sellingPrice = $equipmentPrice->getPretaxSellingPrice() * $currency->getExchangeRate()->getRateToEuro();
                        $sellingPrice = \Prices\VatManager::convertToPostTaxes($sellingPrice, $vat->getAmount());
                        ?>
                        <label class="col-lg-12 striped hoverable">
                        <span class="col-lg-8">
                            <input type="checkbox" value="<?php echo $equipment->getId(); ?>" name="createOffer[equipments][]"
                                   class="cb-optional-equipments" data-price="<?php echo round($sellingPrice, 2); ?>">
                            <?php echo $equipment->getName(); ?>
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
                    foreach($packs as $packInformation):
                        /** @var \Vehicle\Pack $pack */
                        $pack = $packInformation['pack'];
                        /** @var \Prices\Price $packPrice */
                        $packPrice = $packInformation['price'];

                        if($packPrice != null){
                            $db = databaseConnection();
                            $currency = \Prices\CurrencyManager::fetchCurrency($db, $packPrice->getCurrencyId());
                            $db = null;
                        }

                        if($packPrice != null){
                            $sellingPrice = $packPrice->getPretaxSellingPrice() * $currency->getExchangeRate()->getRateToEuro();
                            $sellingPrice = \Prices\VatManager::convertToPostTaxes($sellingPrice, $vat->getAmount());
                        }
                        else{
                            $sellingPrice = 0;
                        }
                        $db = databaseConnection();
                        $equipmentsArray = \Vehicle\PackManager::fetchEquipments($db, $pack->getId());
                        $color = \Vehicle\PackManager::fetchColor($db, $pack->getId());
                        $rims = \Vehicle\PackManager::fetchRim($db, $pack->getId());
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
                            <input type="checkbox" value="<?php echo $pack->getId(); ?>" class="cb-packs" name="createOffer[packs][]"
                                   data-price="<?php echo round($sellingPrice, 2); ?> ">
                            <?php echo $pack->getName(); ?><br>
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
                        <select id="selectVehicleColor" class="form-control" name="createOffer[color]">
                            <?php
                            if(!is_a($colorsAssoc, '\Exception')):
                                foreach($colorsAssoc as $assoc):
                                    $assocId = (int)$assoc['assocId'];
                                    /** @var \Vehicle\ExternalColor $color */
                                    $color = $assoc['color'];
                                    /** @var \Prices\Price $price */
                                    $price = $assoc['price'];

                                    if($price->getId() != 0):
                                        $db = databaseConnection();
                                        $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                                        $db = null;

                                        $sellingPrice = $price->getPretaxSellingPrice() * $currency->getExchangeRate()->getRateToEuro();
                                        $sellingPrice = \Prices\VatManager::convertToPostTaxes($sellingPrice, $vat->getAmount());
                                        $printedPrice = number_format($sellingPrice, 2, '.', ' ').' € TTC';
                                    else:
                                        $sellingPrice = 0;
                                        $printedPrice = 'gratuit';
                                    endif;
                                    ?>
                                    <option value="<?php echo $color->getId(); ?>" data-price="<?php echo round($sellingPrice, 2); ?>">
                                        <?php echo $color->getName().' '.$color->getDetails(); ?> -
                                        <?php echo $printedPrice; ?>
                                    </option>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
            </div>
            <div class="col-lg-5">
                <h3 class="page-header col-lg-12"><label for="selectVehicleRims">Jantes</label></h3>
                <div class="col-lg-12">
                    <select id="selectVehicleRims" class="form-control" name="createOffer[rims]">
                        <?php
                        if(!is_a($rimsAssoc, '\Exception')):
                            foreach($rimsAssoc as $assocId => $assoc):
                                /** @var \Vehicle\RimModel $rims */
                                $rims = $assoc['rim'];
                                /** @var \Prices\Price $price */
                                $price = $assoc['price'];

                                if($price != null):
                                    $db = databaseConnection();
                                    $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
                                    $db = null;

                                    $sellingPrice = $price->getPretaxSellingPrice() * $currency->getExchangeRate()->getRateToEuro();
                                    $sellingPrice = \Prices\VatManager::convertToPostTaxes($sellingPrice, $vat->getAmount());
                                    $printedPrice = number_format($sellingPrice, 2, '.', ' ').' € TTC';
                                else:
                                    $sellingPrice = 0;
                                    $printedPrice = 'gratuites';
                                endif;
                                ?>
                                <option value="<?php echo $rims->getId(); ?>" data-price="<?php echo round($sellingPrice, 2); ?>">
                                    <?php echo $rims->getRimType().' '.$rims->getFrontDiameter().'" - '.$rims->getName(); ?> -
                                    <?php echo $printedPrice; ?>
                                </option>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <br><br><br>
        <div class="row">
            <div class="col-lg-5 col-lg-offset-1">
                <div class="col-lg-6">
                    <input type="button" class="btn btn-primary" id="bntCalc" value="Calcul">
                </div>
                <div class="col-lg-6">
                    <input type="button" class="btn btn-primary" id="btnDetails" value="Détails">
                </div>
                <div class="col-lg-12 not-displayed" id="block-calc">
                    <div class="card card-block">
                        <div class="card-title">Calcul du prix</div>
                        <div class="card-text">
                            <div id="receiverDefaultMargin" class="not-displayed"><?php echo $priceDetails->getDealerMargin(); ?></div>
                            <div id="receiverBasicPrice" class="not-displayed"><?php echo $priceDetails->getPretaxDealerBuyingPrice(); ?></div>
                            <input type="text" id="marginAmount" class="customized-jquery-ui-input" name="createOffer[dealerMargin]"
                                   data-vat="<?php echo $vat->getAmount(); ?>" readonly>
                            <div id="calcSlider"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 text-small">
                <div class="invisible detailsRow" style="display:none;">
                    <div class="col-lg-8">Transport depuis 68127 :</div>
                    <div class="col-lg-4 text-lg-right"><?php echo number_format($priceDetails->getPostTaxesFreightChargesInFrance(), 2, '.', ' '); ?> € TTC</div>
                </div>
                <div class="row striped">
                    <div class="col-lg-8">Prix du véhicule (hors options) :</div>
                    <div class="col-lg-4 text-lg-right">
                        <span class="vehiclePostTaxesPrice">
                            <?php echo number_format($priceDetails->getPostTaxesDealerSellingPrice(), 2, '.', ' '); ?>
                        </span> € TTC
                    </div>
                </div>
                <div class="row striped">
                    <div class="col-lg-8">Sous total options :</div>
                    <div class="col-lg-4 text-lg-right"><span id="receiverOptionPrice">0.00</span> € TTC</div>
                </div>
                <div class="row striped">
                    <div class="col-lg-8">Sous total pour le véhicule :</div>
                    <div class="col-lg-4 text-lg-right"><span class="receiverOptionedVehiclePrice"><?php echo number_format($priceDetails->getPostTaxesDealerSellingPrice(), 2, '.', ' '); ?></span> € TTC</div>
                </div>
                <div class="row striped">
                    <div class="col-lg-8">Forfait de mise à disposition :</div>
                    <div class="col-lg-4 text-lg-right"><span id="packageProvision"><?php echo number_format($priceDetails->getPostTaxesPackageProvision(), 2, '.', ' '); ?></span> € TTC</div>
                </div>
                <div class="row striped">
                    <div class="col-lg-8">Prix de vente :</div>
                    <div class="col-lg-4 text-lg-right"><span class="receiverSellingPrice"><?php echo number_format($priceDetails->getPostTaxesAllIncludedPrice(), 2, '.', ' '); ?></span> € TTC</div>
                </div>
                <div class="row striped">
                    <div class="col-lg-8">Carte grise dans le <?php echo $dealer->getDepartment(); ?> :*</div>
                    <div class="col-lg-4 text-lg-right"><?php echo number_format($priceDetails->getRegistrationCardAmount(), 2, '.', ' '); ?> €&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                </div>
            </div>
        </div>
        <br><br><br>
        <div class="row">
            <div class="col-lg-4 col-lg-offset-6">
                <input type="button" class="btn btn-primary" value="Créer une offre de prix" data-toggle="modal" data-target=".select-client-modal-lg">
            </div>
        </div>
    </form>
</div>

<br><br><br><br>