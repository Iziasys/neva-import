<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

$vehicleId = (int)$_POST['vehicleId'];

if(empty($vehicleId)){
    echo 'Erreur lors de la sélection du véhicule.';
    die();
}

$db = databaseConnection();
$vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $vehicleId);
if(is_a($vehicle, '\Exception')){
    echo 'Erreur lors de la sélection du véhicule.';
    $db = null;
    die();
}
$country = \Prices\CountryManager::fetchCountry($db, $vehicle->getPrice()->getCountryId());
$vat = \Prices\VatManager::fetchVatFromCountry($db, $country->getId());
$db = null;

$finish = $vehicle->getFinish();
$model = $finish->getModel();
$brand = $model->getBrand();
$price = $vehicle->getPrice();
$currency = $price->getCurrency();
$dealer = $finish->getDealer();

$buyingPrice = $price->getPretaxBuyingPrice();
$changeRateToEuro = $currency->getExchangeRate()->getRateToEuro();
$freightChargesToFrance = $country->getFreightCharges()->getAmount();
$marginAmount = 0.0;
$marginPercentage = $price->getMargin();
$managementFees = $price->getManagementFees();
$vatAmount = $vat->getAmount();
$freightChargesInFrance = $dealerMargin = $packageProvision = $optionPrice = $registrationCardAmount = $bonusPenalty = 0;
$priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
                                         $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty);

$totalMargin = $priceDetails->getMarginAmount() + $priceDetails->getManagementFees();
?>
<style>
    .row.greyed{
        background:#dddddd;
    }
</style>
<h4 class="page-header">
    <?php echo $brand->getName().' '.$model->getName().' '.$finish->getName(); ?>
    <small>
        <?php echo $vehicle->getBodywork()->getName().' '.$vehicle->getDoorsAmount().' portes'; ?>
        - <?php echo $vehicle->getEngineSize().' '.$vehicle->getEngine()->getName().' '.$vehicle->getDynamicalPower(); ?>
        - <?php echo $vehicle->getTransmission()->getName().' Boite '.$vehicle->getGearbox()->getName(); ?>
    </small>
</h4>
<br><br>
<div id="priceDetails">
    <div class="row">
        <div class="col-lg-4">Pays d'origine</div>
        <div class="col-lg-3"><?php echo $country->getName().' ('.$country->getAbbreviation().')'; ?></div>
    </div>
    <div class="row">
        <div class="col-lg-4">Concessionnaire</div>
        <div class="col-lg-3"><?php echo $dealer->getName(); ?></div>
    </div>

    <hr>

    <div class="row">
        <div class="col-lg-4">Prix d'achat</div>
        <div class="col-lg-3"><?php echo number_format($priceDetails->getPretaxBuyingPrice(), 2, '.', ' ').' '.$currency->getSymbol(); ?> HT</div>
    </div>
    <div class="row greyed">
        <div class="col-lg-3 col-lg-offset-4 text-lg-right">* <?php echo $currency->getExchangeRate()->getRateToEuro(); ?></div>
        <div class="col-lg-5">
            Conversion vers Euro
            <small><i>(MaJ le <?php echo $currency->getExchangeRate()->getRateDate()->format('d/m/Y'); ?>)</i></small>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">Prix d'achat (en euros)</div>
        <div class="col-lg-3"><?php echo number_format($priceDetails->getPretaxBuyingPriceInEuro(), 2, '.', ' '); ?> € HT</div>
    </div>
    <div class="row greyed">
        <div class="col-lg-3 col-lg-offset-4 text-lg-right">+ <?php echo $country->getFreightCharges()->getAmount(); ?> € HT</div>
        <div class="col-lg-5">Frais de transport depuis le pays</div>
    </div>
    <div class="row">
        <div class="col-lg-4">Prix d'achat, transporté 68127</div>
        <div class="col-lg-3"><?php echo number_format($priceDetails->getPretaxTransportedPrice(), 2, '.', ' '); ?> € HT</div>
    </div>
    <div class="row greyed">
        <div class="col-lg-3 col-lg-offset-4 text-lg-right">+ <?php echo number_format($priceDetails->getMarginAmount(), 2, '.', ' '); ?> € HT</div>
        <div class="col-lg-5">
            Marge <?php echo $priceDetails->getMarginPercentage(); ?>% Avngrp
            <small><i>(sur le véhicule uniquement)</i></small>
        </div>
    </div>
    <div class="row greyed" title="Frais de gestion du véhicule, non applicable en B2B hors partenaires">
        <div class="col-lg-3 col-lg-offset-4 text-lg-right">+ <?php echo number_format($priceDetails->getManagementFees(), 2, '.', ' '); ?> € HT</div>
        <div class="col-lg-5">Frais de gestion</div>
    </div>
    <div class="row">
        <div class="col-lg-4">Prix de vente (au départ du 68127)</div>
        <div class="col-lg-3"><?php echo number_format($priceDetails->getPretaxDealerBuyingPrice(), 2, '.', ' '); ?> € HT</div>
    </div>
    <div class="row">
        <div class="col-lg-4">Prix public France</div>
        <div class="col-lg-3"><?php echo number_format(\Prices\VatManager::convertToPretax($price->getPostTaxesPublicPrice(), 20), 2, '.', ' '); ?> € HT</div>
    </div>

    <hr>

    <div class="row">
        <div class="col-lg-4">Marge totale :</div>
        <div class="col-lg-3"><?php echo number_format($totalMargin, 2, '.', ' '); ?> € HT</div>
    </div>

    <hr>

    <input type="hidden" id="inputVehicleId" value="<?php echo $vehicleId; ?>">
    <div class="row">
        <div class="form-group">
            <label for="inputModifyManagementFees" class="col-lg-2 form-control-label">Frais de gestion :</label>
            <div class="col-lg-2 input-group">
                <input type="text" id="inputModifyManagementFees" class="form-control" value="<?php echo $managementFees; ?>">
                <span class="input-group-addon">€ HT</span>
            </div>
            <div class="col-lg-2">
                <input type="button" id="btnModifyManagementFees" class="btn btn-primary" value="Modifier">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label for="inputModifyMargin" class="col-lg-2 form-control-label">Marge Avngrp :</label>
            <div class="col-lg-2 input-group">
                <input type="text" id="inputModifyMargin" class="form-control" value="<?php echo $price->getMargin(); ?>">
                <span class="input-group-addon">%</span>
            </div>
            <div class="col-lg-8 col-without-padding">
                <div class="col-lg-4">
                    <input type="button" id="modifyMarginForVehicle" class="btn btn-primary btnModifyMargin" value="Pour le véhicule">
                </div>
                <div class="col-lg-4">
                    <input type="button" id="modifyMarginForFinish" class="btn btn-primary btnModifyMargin" value="Pour la finition">
                </div>
                <div class="col-lg-4">
                    <input type="button" id="modifyMarginForModel" class="btn btn-primary btnModifyMargin" value="Pour le modèle">
                </div>
            </div>
        </div>
    </div>
</div>