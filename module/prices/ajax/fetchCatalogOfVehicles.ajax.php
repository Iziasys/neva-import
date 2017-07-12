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
$vehiclesList = \Vehicle\DetailsManager::fetchActiveVehicleList($db, true, $constraints, $arrayOrder, 1000);
$nbVehicles = \Vehicle\DetailsManager::countActiveVehicles($db, $constraints);
$db = null;
?>

<table class="table table-striped table-hover sortable col-lg-12">
    <thead>
    <tr>
        <th class="text-lg-center">Marque</th>
        <th class="text-lg-center">Modèle</th>
        <th class="text-lg-center">Motorisation</th>
        <th class="text-lg-center">Finition</th>
        <th class="text-lg-center">Boite</th>
        <th class="text-lg-center">Prix d'achat transporté<br>(HT)</th>
        <th class="text-lg-center">Marge</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($vehiclesList as $vehicle):
        $finish = $vehicle->getFinish();
        $model = $finish->getModel();
        $brand = $model->getBrand();
        $price = $vehicle->getPrice();
        $db = databaseConnection();
        $freightCharges = \Prices\FreightChargesManager::fetchFreightChargesFromCountry($db, $price->getCountryId());
        $db = null;
        $currency = $price->getCurrency();
        $rateToEuro = $currency->getExchangeRate()->getRateToEuro();
        $freightChargesAmount = $freightCharges->getAmount();
        $pretaxBuyingPrice = $price->getPretaxBuyingPrice();
        $pretaxBuyingPriceInEuro = $pretaxBuyingPrice * $rateToEuro;
        ?>
        <tr class="hoverable clickable tr-togle-price-details-modal" data-toggle="modal" data-target=".prices-details-modal-lg" data-vehicleId="<?php echo $vehicle->getId(); ?>">
            <td class="text-lg-center"><?php echo $brand->getName(); ?></td>
            <td class="text-lg-center">
                <?php echo $model->getName(); ?><br>
                <?php echo $vehicle->getBodywork()->getName().' '.$vehicle->getDoorsAmount().' portes'; ?>
            </td>
            <td class="text-lg-center">
                <?php echo $vehicle->getEngineSize().' '.$vehicle->getEngine()->getName().' '.$vehicle->getDynamicalPower(); ?><br>
                <?php echo $vehicle->getTransmission()->getName(); ?>
            </td>
            <td class="text-lg-center"><?php echo $finish->getName(); ?></td>
            <td class="text-lg-center"><?php echo $vehicle->getGearbox()->getName(); ?></td>
            <td class="text-lg-center"><?php echo number_format($pretaxBuyingPriceInEuro, 0, '.', ' ').'€'; ?></td>
            <td class="text-lg-center"><?php echo $price->getMargin(); ?>%</td>
        </tr>
        <?php
    endforeach;
    ?>
    </tbody>
</table>