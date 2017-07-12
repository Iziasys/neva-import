<?php

//Instanciation d'un objet PDO pour récupérer les données du formulaire
$db = databaseConnection();
$brandList = \Vehicle\BrandManager::fetchBrandList($db, 'brandName');
$familiesList = \Vehicle\EquipmentManager::fetchFamiliesList($db);
foreach($familiesList as $key => $family){
    $familiesList[$key]['equipments'] = \Vehicle\EquipmentManager::fetchEquipmentsByFamily($db, (int)$family['id']);
}
$dealersList = \Prices\DealerManager::fetchDealersList($db, 'name');
$currenciesList = \Prices\CurrencyManager::fetchCurrenciesList($db, 'currency');
$db = null;

if($_GET['action'] == 'modify' && !empty($_GET['finishId'])){
    $db = databaseConnection();
    $finishId = (int)$_GET['finishId'];
    $finish = \Vehicle\FinishManager::fetchCompleteFinish($db, $finishId);

    $brandId = $finish->getModel()->getBrandId();
    $modelName = $finish->getModel()->getName();
    $finishName = $finish->getName();

    $serialEquipments = \Vehicle\FinishManager::fetchSerialEquipments($db, $finishId);
    $serialEquipmentsIds = array();
    foreach($serialEquipments as $equipment){
        $serialEquipmentsIds[] = $equipment->getId();
    }
    $optionalEquipments = \Vehicle\FinishManager::fetchOptionalEquipments($db, $finishId);
    $optionalEquipmentsIds = array();
    $optionalEquipmentsPrices = array();
    $vat = null;
    foreach($optionalEquipments as $equipment){
        $optionalEquipmentsIds[] = $equipment->getId();
        if($vat == null){
            $vat = \Prices\VatManager::fetchFrenchVat($db);
        }
        $optionalEquipmentsPrices[$equipment->getId()] = \Prices\VatManager::convertToPostTaxes($equipment->getPrice()->getPretaxBuyingPrice(), $vat->getAmount());
    }

    $dealerId = $finish->getDealerId();
    $currencyId = $optionalEquipments[0]->getPrice()->getCurrencyId();

    $db = null;
    $mode = 'modify';
}
else{
    $brandId = 0;
    $modelName = '';
    $finishName = '';
    $serialEquipmentsIds = array();
    $optionalEquipmentsIds = array();
    $optionalEquipmentsPrices = array();
    $dealerId = 0;
    $currencyId = 0;

    $mode = 'create';
}

?>

<div class="container">
    <h2 class="page-header">Création d'un véhicule générique</h2>
    <br>
    <form action="/commande-vehicules/vehicule-generique/visualiser" method="post">
        <?php
        if($mode == 'modify'):
            ?>
            <input type="hidden" name="<?php echo $mode; ?>GenericVehicle[id]" value="<?php echo $finishId; ?>">
            <?php
        endif;
        ?>
        <div class="row">
            <div class="form-group">
                <label for="inputVehicleBrand" class="col-md-1 form-control-label">Marque :</label>
                <div class="col-md-3">
                    <select class="form-control" name="<?php echo $mode; ?>GenericVehicle[brandId]" id="inputVehicleBrand" <?php echo $mode == 'modify' ? 'disabled' : ''; ?>>
                        <option value="0" <?php echo $brandId == 0 ? 'selected' : ''; ?>>Sélectionnez une marque</option>
                        <?php
                        foreach($brandList as $brand):
                            ?>
                            <option value="<?php echo $brand->getId(); ?>"
                                <?php echo $brandId == $brand->getId() ? 'selected' : ''; ?>>
                                <?php echo $brand->getName(); ?>
                            </option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputVehicleModel" class="col-md-1 form-control-label">Modèle :</label>
                <div class="col-md-3">
                    <input type="text" id="inputVehicleModel" name="<?php echo $mode; ?>GenericVehicle[model]"
                           class="form-control formManager typeAhead typeAhead-ajax" data-formManager="required"
                           data-typeAhead-ajax="command/fetchModelList" data-typeAhead-argument="modelName:inputVehicleModel brandId:inputVehicleBrand"
                           data-typeAhead-dropdown="modelDropdown" value="<?php echo $modelName; ?>" required
                        <?php echo $mode == 'modify' ? 'disabled' : ''; ?>>
                    <div id="modelDropdown" class="dropdown-menu typeAhead-dropdown" aria-labelledby="dropdownModels">

                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputVehicleFinish" class="col-md-1 form-control-label">Finition :</label>
                <div class="col-md-3">
                    <input type="text" id="inputVehicleFinish" name="<?php echo $mode; ?>GenericVehicle[finish]"
                           class="form-control formManager typeAhead typeAhead-ajax" data-formManager="required"
                           data-typeAhead-ajax="command/fetchFinishList" data-typeAhead-argument="finishName:inputVehicleFinish modelName:inputVehicleModel"
                           data-typeAhead-dropdown="finishDropdown" value="<?php echo $finishName; ?>" required>
                    <div id="finishDropdown" class="dropdown-menu typeAhead-dropdown" aria-labelledby="dropdownFinishes">

                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <h3 class="page-header">Equipements de série</h3>
        <div class="row">
            <div class="form-group">
                <div class="col-md-3">
                    <ul class="nav nav-stacked nav-pills nav-pills-lg" role="tablist">
                        <?php
                        foreach($familiesList as $key => $family):
                            $familyName = $family['name'];
                            $target = preg_replace_callback('/ /', function(){return '';}, $familyName);
                            ?>
                            <li class="nav-item">
                                <a href="#serial<?php echo $target; ?>" data-toggle="pill" role="tab" class="nav-link <?php echo ($key == 0) ? 'active' : ''; ?>"><?php echo $familyName; ?></a>
                            </li><br>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <?php
                        foreach($familiesList as $key => $family):
                            $familyName = $family['name'];
                            $target = preg_replace_callback('/(\s)/', function(){return '';}, $familyName);
                            ?>
                            <div class="tab-pane text-small <?php echo $key == 0 ? 'active' : ''; ?>" id="serial<?php echo $target; ?>" role="tabpanel">
                                <?php
                                foreach($family['equipments'] as $key2 => $equipment):
                                    /** @var \Vehicle\Equipment $equipment */
                                    $equipment = $equipment;
                                    if($key2 % 3 == 0):
                                        ?><div class="row"><?php
                                    endif;
                                    ?>
                                    <div class="col-md-4">
                                        <label class="hoverable fit-to-container">
                                            <input type="checkbox" name="<?php echo $mode; ?>GenericVehicle[serialEquipment][]"
                                                   value="<?php echo $equipment->getId(); ?>" <?php echo in_array($equipment->getId(), $serialEquipmentsIds) ? 'checked' : ''; ?>>
                                            <?php echo $equipment->getName(); ?>
                                        </label>
                                    </div>
                                    <?php
                                    if(($key2 + 1) % 3 == 0 || $key2 == count($family['equipments']) - 1):
                                        ?></div><?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                            <?php
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <h3 class="page-header">Equipements en option <small class="font-italic text-small">Prix TTC du pays du concessionnaire, avec la devise renseignée à la fin du formulaire</small></h3>
        <div class="row">
            <div class="form-group">
                <div class="col-md-3">
                    <ul class="nav nav-stacked nav-pills nav-pills-lg" role="tablist">
                        <?php
                        foreach($familiesList as $key => $family):
                            $familyName = $family['name'];
                            $target = preg_replace_callback('/ /', function(){return '';}, $familyName);
                            ?>
                            <li class="nav-item">
                                <a href="#optional<?php echo $target; ?>" data-toggle="pill" role="tab" class="nav-link <?php echo ($key == 0) ? 'active' : ''; ?>"><?php echo $familyName; ?></a>
                            </li><br>
                            <?php
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <?php
                        foreach($familiesList as $key => $family):
                            $familyName = $family['name'];
                            $target = preg_replace_callback('/(\s)/', function(){return '';}, $familyName);
                            ?>
                            <div class="tab-pane text-small <?php echo $key == 0 ? 'active' : ''; ?>" id="optional<?php echo $target; ?>" role="tabpanel">
                                <?php
                                foreach($family['equipments'] as $key2 => $equipment):
                                    /** @var \Vehicle\Equipment $equipment */
                                    $equipment = $equipment;
                                    if($key2 % 3 == 0):
                                        ?><div class="row"><?php
                                    endif;
                                    ?>
                                    <div class="col-md-4">
                                        <div class="col-md-9 col-without-padding">
                                            <label class="hoverable fit-to-container">
                                                <input type="checkbox" name="<?php echo $mode; ?>GenericVehicle[optionalEquipment][]"
                                                       value="<?php echo $equipment->getId(); ?>" <?php echo in_array($equipment->getId(), $optionalEquipmentsIds) ? 'checked' : ''; ?>>
                                                <?php echo $equipment->getName(); ?>
                                            </label>
                                        </div>
                                        <div class="col-md-3 col-without-padding input-group input-group-xs">
                                            <input type="text" class="form-control" name="<?php echo $mode; ?>GenericVehicle[optionalPrice][<?php echo $equipment->getId(); ?>]"
                                                   value="<?php echo in_array($equipment->getId(), $optionalEquipmentsIds) ? round($optionalEquipmentsPrices[$equipment->getId()], 2) : ''; ?>">
                                            <span class="input-group-addon">TTC</span>
                                        </div>
                                    </div>
                                    <?php
                                    if(($key2 + 1) % 3 == 0 || $key2 == count($family['equipments']) - 1):
                                        ?></div><?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                            <?php
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputVehicleDealer" class="col-md-2 form-control-label">Concessionnaire :</label>
                <div class="col-md-4">
                    <select class="form-control" name="<?php echo $mode; ?>GenericVehicle[dealerId]" id="inputVehicleDealer">
                        <option value="0" <?php echo $dealerId == 0 ? 'selected' : ''; ?>>Sélectionnez un concessionnaire</option>
                        <?php
                        foreach($dealersList as $dealer):
                            $db = databaseConnection();
                            $country = \Prices\CountryManager::fetchCountry($db, $dealer->getCountryId());
                            $db = null;
                            ?>
                            <option value="<?php echo $dealer->getId(); ?>" <?php echo $dealerId == $dealer->getId() ? 'selected' : ''; ?>>
                                <?php echo $dealer->getName().' ('.$country->getName().')'; ?>
                            </option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputVehicleCurrency" class="col-md-2 form-control-label">Devise utilisée :</label>
                <div class="col-md-4">
                    <select class="form-control" name="<?php echo $mode; ?>GenericVehicle[currencyId]" id="inputVehicleCurrency">
                        <option value="0" <?php echo $currencyId == 0 ? 'selected' : ''; ?>>Sélectionnez une devise</option>
                        <?php
                        foreach($currenciesList as $currency):
                            ?>
                            <option value="<?php echo $currency->getId(); ?>" <?php echo $currencyId == $currency->getId() ? 'selected' : ''; ?>>
                                <?php echo $currency->getCurrency().' ( '.$currency->getSymbol().' )'; ?>
                            </option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <br><br>
        <div class="row">
            <div class="form-group">
                <div class="col-md-4 col-md-offset-8">
                    <button type="submit" class="btn btn-primary"><?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> le véhicule générique</button>
                </div>
            </div>
        </div>
        <br><br><br><br><br><br><br>
    </form>
</div>