<?php
$db = databaseConnection();
$arrayOrder = array(
    array('orderBy' => 'brandName', 'way' => 'ASC'),
    array('orderBy' => 'modelName', 'way' => 'ASC'),
    array('orderBy' => 'finishName', 'way' => 'ASC'),
    array('orderBy' => 'engineSize', 'way' => 'ASC'),
    array('orderBy' => 'vhcl_engine.name', 'way' => 'ASC'),
    array('orderBy' => 'dynamicalPower', 'way' => 'ASC'),
);
$vehiclesList = \Vehicle\DetailsManager::fetchVehicleList($db, true, $arrayOrder);
$arrayOrder = array(
    array('orderBy' => 'brandName', 'way' => 'ASC'),
    array('orderBy' => 'modelName', 'way' => 'ASC'),
    array('orderBy' => 'finishName', 'way' => 'ASC')
);
$finishesList = \Vehicle\FinishManager::fetchFinishList($db, true, $arrayOrder);
$db = null;
?>

<div class="container">
    <h3 class="page-header">Visualisation des jantes disponibles</h3>
    <div class="col-lg-12">
        <label for="selectFinishForRim" class="col-lg-2 form-control-label">Finition concernée :</label>
        <div class="col-lg-10">
            <select id="selectFinishForRim" class="form-control">
                <option value="0">Sélectionnez une finition</option>
                <?php
                foreach($finishesList as $finish):
                    $model = $finish->getModel();
                    $brand = $model->getBrand();
                    $dealer = $finish->getDealer();
                    $country = $dealer->getCountry();
                ?>
                    <option value="<?php echo $finish->getId(); ?>">
                        <?php echo $brand->getName().' '.$model->getName().' '.$finish->getName().' - '.$dealer->getName().' ('.$country->getName().')'; ?>
                    </option>
                <?php
                endforeach;
                ?>
            </select>
        </div>
    </div>
    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Matière</th>
            <th>Taille</th>
            <th>Prix HT</th>
            <th></th>
        </tr>
        </thead>
        <tbody id="receiverRimInformation">

        </tbody>
    </table>
</div>
