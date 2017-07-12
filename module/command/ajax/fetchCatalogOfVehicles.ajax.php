<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

if(!empty($_POST)){
    $brandsIds = $_POST['brandsIds'];
    $modelsIds = $_POST['modelsIds'];
    $finishesIds = $_POST['finishesIds'];
    $bodyworkIds = $_POST['bodyworkIds'];
    $fuelsIds = $_POST['fuelsIds'];
    $gearboxesIds = $_POST['gearboxesIds'];
}

if(!empty($brandsIds)){
    $brandConstraint = array(
        'field'    => 'vhcl_brand.id',
        'operator' => 'IN',
        'value'    => '('.implode(',', $brandsIds).')'
    );
}
if(!empty($modelsIds)){
    $modelConstraint = array(
        'field'    => 'vhcl_model.id',
        'operator' => 'IN',
        'value'    => '('.implode(',', $modelsIds).')'
    );
}
if(!empty($finishesIds)){
    $finishConstraint = array(
        'field'    => 'vhcl_finish.id',
        'operator' => 'IN',
        'value'    => '('.implode(',', $finishesIds).')'
    );
}
if(!empty($bodyworkIds)){
    $bodyworkConstraint = array(
        'field'    => 'bodywork_id',
        'operator' => 'IN',
        'value'    => '('.implode(',', $bodyworkIds).')'
    );
}
if(!empty($fuelsIds)){
    $fuelConstraint = array(
        'field'    => 'fuel_id',
        'operator' => 'IN',
        'value'    => '('.implode(',', $fuelsIds).')'
    );
}
if(!empty($gearboxesIds)){
    $gearboxConstraint = array(
        'field'    => 'gearbox_id',
        'operator' => 'IN',
        'value'    => '('.implode(',', $gearboxesIds).')'
    );
}

//TODO : Gestion des données recues pour actualisation de la liste de résultats

$constraints = array();
if(!empty($brandConstraint))
    array_push($constraints, $brandConstraint);
if(!empty($modelConstraint))
    array_push($constraints, $modelConstraint);
if(!empty($finishConstraint))
    array_push($constraints, $finishConstraint);
if(!empty($bodyworkConstraint))
    array_push($constraints, $bodyworkConstraint);
if(!empty($fuelConstraint))
    array_push($constraints, $fuelConstraint);
if(!empty($gearboxConstraint))
    array_push($constraints, $gearboxConstraint);

$arrayOrder = array(
    array(
        'orderBy' => 'brandName',
        'way'     => 'ASC'
    ),
    array(
        'orderBy' => 'modelName',
        'way'     => 'ASC'
    ),
    array(
        'orderBy' => 'finishName',
        'way'     => 'ASC'
    ),
    array(
        'orderBy' => 'engineSize',
        'way'     => 'ASC'
    ),
    array(
        'orderBy' => 'engineName',
        'way'     => 'asc'
    ),
    array(
        'orderBy' => 'dynamicalPower',
        'way'     => 'asc'
    )
);

$db = databaseConnection();
$vehiclesList = \Vehicle\DetailsManager::fetchActiveVehicleList($db, true, $constraints, $arrayOrder);
$nbVehicles = \Vehicle\DetailsManager::countActiveVehicles($db, $constraints);
$db = null;

/** @var \Users\Structure $structure */
$structure = $_SESSION['user']->getStructure();
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
?>

<table class="table table-striped table-hover sortable col-lg-12">
    <thead>
    <tr>
        <th class="text-lg-center">Marque</th>
        <th class="text-lg-center">Modèle</th>
        <th class="text-lg-center">Carrosserie</th>
        <th class="text-lg-center">Motorisation</th>
        <th class="text-lg-center">Boite</th>
        <th class="text-lg-center">Finition</th>
        <th class="text-lg-center">Co2<br>(g/km)</th>
        <th class="text-lg-center">A partir de</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($vehiclesList as $vehicle):
        $finish = $vehicle->getFinish();
        $model = $finish->getModel();
        $brand = $model->getBrand();
        $price = $vehicle->getPrice();

        //Récupération en base des informations requises pour le calcul du prix
        $db = databaseConnection();
        $country = \Prices\CountryManager::fetchCountry($db, $price->getCountryId());
        $freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $dealerDepartment);
        $vat = \Prices\VatManager::fetchFrenchVat($db);
        $db = null;

        $vehiclePrice = $vehicle->getPrice();
        $currency = $vehiclePrice->getCurrency();

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
        $registrationCardAmount = 0;
        $bonusPenalty = 0;

        $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
                                                 $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty);
    ?>
        <tr class="clickable print-details-vehicle _blank" data-href="/commande-vehicules/catalogue/visualiser/<?php echo $vehicle->getId(); ?>" data-vehicleId="<?php echo $vehicle->getId(); ?>">
            <td class="text-lg-center"><?php echo $brand->getName(); ?></td>
            <td class="text-lg-center"><?php echo $model->getName(); ?></td>
            <td>
                <div class="col-lg-6 text-lg-center"><?php echo $vehicle->getBodywork()->getName(); ?></div>
                <div class="col-lg-6 text-lg-center">
                    <?php echo $vehicle->getDoorsAmount().' portes - '.$vehicle->getSitsAmount().' places'; ?>
                </div>
            </td>
            <td class="text-lg-center">
                <?php echo $vehicle->getEngineSize().' '.$vehicle->getEngine()->getName().' '.$vehicle->getDynamicalPower(); ?>
            </td>
            <td class="text-lg-center"><?php echo $vehicle->getGearbox()->getName(); ?></td>
            <td class="text-lg-center"><?php echo $finish->getName(); ?></td>
            <td class="text-lg-center"><?php echo $vehicle->getCo2(); ?></td>
            <td class="text-lg-center"><?php echo number_format($priceDetails->getPostTaxesAllIncludedPrice(), 2, '.', ' '); ?>€ TTC</td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>