<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();
$pretaxDefaultMargin = $structure->getDefaultMargin();
$dealer = $structure;
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

$arrayConstraints = array(
    array(
        'field' => 'sold',
        'operator' => '=',
        'value' => 0
    )
);
$acceptNewVehicles = !empty($_POST['acceptNewVehicles']) && $_POST['acceptNewVehicles'] == 'false' ? false : true;
$acceptUsedVehicles = !empty($_POST['acceptUsedVehicles']) && $_POST['acceptUsedVehicles'] == 'false' ? false : true;

$specialConstraint = ' AND (depotSale = 0 OR (depotSale = 1 AND structure_id = '.$dealer->getId().')) ';
if(!$acceptNewVehicles){
    $specialConstraint .= ' AND mileage > 100 ';
}
if(!$acceptUsedVehicles){
    $specialConstraint .= ' AND mileage <= 100 ';
}
$arrayOrder = array(
    array(
        'orderBy' => 'insertionDate',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'brand',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'model',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'finish',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'engineSize',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'engine',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'dynamicalPower',
        'way' => 'ASC'
    ),
);
$arrayOrder = array(
    array(
        'orderBy' => 'price',
        'way' => 'ASC'
    )
);

$db = databaseConnection();
$vehiclesList = \Vehicle\VehicleInStockManager::fetchVehiclesList($db, $arrayConstraints, $arrayOrder, 1000, 0, $specialConstraint);
$vat = \Prices\VatManager::fetchFrenchVat($db);
$freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $dealerDepartment);
$db = null;

foreach($vehiclesList as $vehicleInStock):
    $db = databaseConnection();
    $structureMargin = \Vehicle\VehicleInStockManager::fetchStructureMargin($db, $vehicleInStock->getId(), $dealer->getId());
    $db = null;

    $buyingPrice = \Prices\VatManager::convertToPretax($vehicleInStock->getPrice(), $vat->getAmount());
    $changeRateToEuro = 1;
    $freightChargesToFrance = 0;
    $marginAmount = 0;
    $marginPercentage = 0;
    $managementFees = 0;
    //Si on utilise la base des transporteurs
    if($structure->getFreightCharges() === null){
        $freightChargesInFrance = $freightCharges->getAmount();
    }
    //Si on utilise le montant renseigné dans la structure
    else{
        $freightChargesInFrance = $structure->getFreightCharges();
    }
    $dealerMargin = is_a($structureMargin, '\Exception') ? $dealer->getDefaultMargin() : $structureMargin;
    $packageProvision = $structure->getPackageProvision();
    $optionPrice = 0.0;
    $vatAmount = $vat->getAmount();
    $registrationCardAmount = 0;
    $bonusPenalty = 0;
    $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount,
                                             $marginPercentage, $managementFees, $freightChargesInFrance, $dealerMargin,
                                             $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount,
                                             $bonusPenalty);
    ?>
    <a href="/stock-arrivage/catalogue/visualiser/<?php echo $vehicleInStock->getId(); ?>"
       class="col-lg-12 card card-block card-border-blue hoverable clickable link-black">
        <div class="col-lg-2 col-without-padding">
            <?php
            if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicleInStock->getImage1())):
                ?>
                <img src="<?php echo getAppPath().'/ressources/vehicleImages/VO/'.$vehicleInStock->getImage1(); ?>"
                     alt="Photo 1" width="100%">
                <?php
            endif;
            ?>
        </div>
        <div class="col-lg-10 text-small">
            <div class="row">
                <h4 class="col-lg-8">
                    <b><?php echo $vehicleInStock->getBrand().' '.$vehicleInStock->getModel().' '.$vehicleInStock->getFinish(); ?></b>
                    -
                    <?php echo $vehicleInStock->getEngineSize().' '.$vehicleInStock->getEngine().' '.$vehicleInStock->getDynamicalPower().'ch '.$vehicleInStock->getTransmission(); ?>
                </h4>
                <h3 class="col-lg-4 text-primary">
                    <?php echo number_format($priceDetails->getPostTaxesDealerSellingPrice(), 2, '.', ' '); ?> € TTC
                </h3>
            </div>
            <div class="row">
                <div class="col-lg-6 col-without-padding">
                    <div class="col-lg-4">
                        Type :
                    </div>
                    <div class="col-lg-8">
                        <b><?php echo $vehicleInStock->getBodywork(); ?></b>
                    </div>
                </div>
                <div class="col-lg-6 col-without-padding">
                    <div class="col-lg-4">
                        Couleur :
                    </div>
                    <div class="col-lg-8">
                        <b><?php echo $vehicleInStock->getExternalColor(); ?></b>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-without-padding">
                    <div class="col-lg-4">
                        Carburant :
                    </div>
                    <div class="col-lg-8">
                        <b><?php echo $vehicleInStock->getFuel(); ?></b>
                    </div>
                </div>
                <div class="col-lg-6 col-without-padding">
                    <div class="col-lg-4">
                        Co2 :
                    </div>
                    <div class="col-lg-8">
                        <b><?php echo $vehicleInStock->getCo2(); ?> g/Km</b>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-without-padding">
                    <div class="col-lg-4">
                        DMC :
                    </div>
                    <div class="col-lg-8">
                        <b><?php echo $vehicleInStock->getModelDate()->format('m/Y'); ?></b>
                    </div>
                </div>
                <div class="col-lg-6 col-without-padding">
                    <div class="col-lg-4">
                        Kilométrage :
                    </div>
                    <div class="col-lg-8">
                        <b><?php echo number_format($vehicleInStock->getMileage(), 0, '.', ' '); ?>km</b>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-without-padding">
                    <div class="col-lg-4">
                        Boite :
                    </div>
                    <div class="col-lg-8">
                        <b><?php echo $vehicleInStock->getGearbox(); ?></b>
                    </div>
                </div>
                <div class="col-lg-6 col-without-padding">
                    <div class="col-lg-4">
                        Référence :
                    </div>
                    <div class="col-lg-8">
                        <b><?php echo $vehicleInStock->getReference(); ?></b>
                    </div>
                </div>
            </div>
            <?php
            if($vehicleInStock->getReserved()):
                ?>
                <div class="row text-danger">
                    Véhicule optionné, vous ne pouvez pas éditer de bon de commande pour le moment
                </div>
                <?php
            endif;
            if($vehicleInStock->getIsArriving()):
                ?>
                <div class="row text-danger">
                    Véhicule en arrivage le <?php echo $vehicleInStock->getAvailabilityDate()->format('d-m-Y'); ?>
                </div>
                <?php
            endif;
            ?>
        </div>
    </a>
    <?php
endforeach;