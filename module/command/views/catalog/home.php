<?php
$db = databaseConnection();
$brandsList = \Vehicle\BrandManager::fetchBrandList($db, 'brandName', 'ASC');
$modelsList = \Vehicle\ModelManager::fetchModelList($db, 'modelName', 'ASC');
$arrayOrder = array(
    array(
        'orderBy' => 'finishName',
        'way' => 'ASC'
    )
);
$finishesList = \Vehicle\FinishManager::fetchFinishList($db, false, $arrayOrder);
$bodyworkList = \Vehicle\BodyworkManager::fetchBodyworkList($db, 'name');
$fuelsList = \Vehicle\FuelManager::fetchFuelList($db, 'name');
$gearboxesList = \Vehicle\GearboxManager::fetchGearboxList($db, 'name');
$db = null;
?>
<h1 class="page-header text-lg-center">
    Recherche d'un véhicule dans le catalogue
</h1>
<br><br>
<div class="row loadWhenReady">
    <div class="col-lg-4">
        <label for="inputSelectBrandForCatalog" class="col-lg-4 form-control-label">Marque :</label>
        <div class="col-lg-8 multiselect-button">
            <select id="inputSelectBrandForCatalog" class="form-control" multiple="multiple">
                <?php
                foreach($brandsList as $brand):
                ?>
                    <option value="<?php echo $brand->getId(); ?>"><?php echo $brand->getName(); ?></option>
                <?php
                endforeach;
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-4">
        <label for="inputSelectModelForCatalog" class="col-lg-4 form-control-label">Modèle :</label>
        <div class="col-lg-8 multiselect-button">
            <select id="inputSelectModelForCatalog" class="form-control" multiple="multiple">
                <?php
                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/command/ajax/fetchModelListForCatalog.ajax.php');
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-4">
        <label for="inputSelectFinishForCatalog" class="col-lg-4 form-control-label">Finition :</label>
        <div class="col-lg-8 multiselect-button">
            <select id="inputSelectFinishForCatalog" class="form-control" multiple="multiple">
                <?php
                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/command/ajax/fetchFinishListForCatalog.ajax.php');
                ?>
            </select>
        </div>
    </div>
</div>
<br>
<div class="row loadWhenReady">
    <div class="col-lg-4">
        <label for="inputSelectBodyworkForCatalog" class="col-lg-4 form-control-label">Carrosserie :</label>
        <div class="col-lg-8 multiselect-button">
            <select id="inputSelectBodyworkForCatalog" class="form-control" multiple="multiple">
                <?php
                foreach($bodyworkList as $bodywork):
                ?>
                    <option value="<?php echo $bodywork->getId(); ?>"><?php echo $bodywork->getName(); ?></option>
                <?php
                endforeach;
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-4">
        <label for="inputSelectFuelForCatalog" class="col-lg-4 form-control-label">Carburant :</label>
        <div class="col-lg-8 multiselect-button">
            <select id="inputSelectFuelForCatalog" class="form-control" multiple="multiple">
                <?php
                foreach($fuelsList as $fuel):
                    ?>
                    <option value="<?php echo $fuel->getId(); ?>"><?php echo $fuel->getName(); ?></option>
                    <?php
                endforeach;
                ?>
            </select>
        </div>
    </div>
    <div class="col-lg-4">
        <label for="inputSelectGearboxForCatalog" class="col-lg-4 form-control-label">Boite de vitesse :</label>
        <div class="col-lg-8 multiselect-button">
            <select id="inputSelectGearboxForCatalog" class="form-control" multiple="multiple">
                <?php
                foreach($gearboxesList as $gearbox):
                    ?>
                    <option value="<?php echo $gearbox->getId(); ?>"><?php echo $gearbox->getName(); ?></option>
                    <?php
                endforeach;
                ?>
            </select>
        </div>
    </div>
</div>
<br>
<?php
/*
?>
<div class="row loadWhenReady">
    <div class="col-lg-12">
        <label for="inputSelectPriceRange" class="col-lg-2 form-control-label">Tranche de prix :</label>
        <div class="col-lg-10">
            <input type="text" id="amount" class="customized-jquery-ui-input" readonly>
            <div id="inputSelectPriceRange"></div>
        </div>
    </div>
</div>
*/ ?>

<br><br><br>

<h2 class="page-header text-lg-center">Liste des véhicules disponibles</h2>
<div id="vehicles-catalog">
    <?php
    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/command/ajax/fetchCatalogOfVehicles.ajax.php');
    ?>
</div>