<?php

try{
    //Instanciation d'un objet PDO pour récupérer les données du formulaire
    $db = databaseConnection();
    $orderArray = array(
        array('orderBy' => 'brandName', 'way' => 'ASC'),
        array('orderBy' => 'modelName', 'way' => 'ASC'),
        array('orderBy' => 'finishName', 'way' => 'ASC')
    );
    $finishesList = \Vehicle\FinishManager::fetchFinishList($db, true, $orderArray);
    $currenciesList = \Prices\CurrencyManager::fetchCurrenciesList($db, 'currency');
    $familiesList = \Vehicle\EquipmentManager::fetchFamiliesList($db);
    foreach($familiesList as $key => $family){
        $familiesList[$key]['equipments'] = \Vehicle\EquipmentManager::fetchEquipmentsByFamily($db, (int)$family['id']);
    }
    $colorsList = \Vehicle\ExternalColorManager::fetchColorsList($db, 'name', 'ASC');
    $rimsList = \Vehicle\RimModelManager::fetchRimModelsList($db, 'name', 'ASC');
    $db = null;

    if($_GET['action'] == 'modify' && !empty($_GET['packId'])){
        $packId = (int)$_GET['packId'];
        $db = databaseConnection();
        $pack = \Vehicle\PackManager::fetchPack($db, $packId);
        $finishId = \Vehicle\FinishManager::whoGotThisPack($db, $packId);
        $equipments = \Vehicle\PackManager::fetchEquipments($db, $packId);
        $color = \Vehicle\PackManager::fetchColor($db, $packId);
        $rim = \Vehicle\PackManager::fetchRim($db, $packId);
        $price = null;
        if($pack->getPriceId() != 0){
            $price = \Prices\PriceManager::fetchPrice($db, $pack->getPriceId());
            $countryId = $price->getCountryId();
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
        }
        $db = null;

        $packName = $pack->getName();

        $equipmentsIds = array();
        if(!is_a($equipments, '\Exception')){
            foreach($equipments as $equipment){
                $equipmentsIds[] = $equipment->getId();
            }
        }
        $colorId = 0;
        if(!is_a($color, '\Exception')){
            $colorId = $color->getId();
        }
        $rimId = 0;
        if(!is_a($rim, '\Exception')){
            $rimId = $rim->getId();
        }

        if($price == null){
            $postTaxesBuyingPrice = 0;
            $currencyId = 0;
        }
        else{
            $pretaxBuyingPrice = $price->getPretaxBuyingPrice();
            $postTaxesBuyingPrice = round(\Prices\VatManager::convertToPostTaxes($pretaxBuyingPrice, $vat->getAmount()), 2);
            $currencyId = $currency->getId();
        }

        $mode = 'modify';
    }
    else{
        $db = databaseConnection();
        $lastCreatedFinish = \Vehicle\FinishManager::fetchLastCreatedFinish($db);
        if(is_a($lastCreatedFinish, '\Exception')){
            $db = null;
            throw $lastCreatedFinish;
        }
        $db = null;

        $finishId = $lastCreatedFinish->getId();

        $packName = '';

        $equipmentsIds = array();
        $colorId = 0;
        $rimId = 0;
        $postTaxesBuyingPrice = '';
        $currencyId = 0;

        $mode = 'create';
    }

    ?>

    <div class="container">
        <h2 class="page-header"><?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> d'un pack</h2>
        <br>
        <form action="/commande-vehicules/pack/visualiser" method="post">
            <?php
            if($mode == 'modify'):
                ?>
                <input type="hidden" name="<?php echo $mode; ?>Pack[id]" value="<?php echo $packId; ?>">
                <?php
            endif;
            ?>
            <div class="row">
                <div class="form-group">
                    <label for="inputPackfinish" class="col-lg-2 form-control-label">Véhicule :</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="<?php echo $mode; ?>Pack[finishId]"
                                id="inputPackfinish" <?php echo $mode == 'modify' ? 'disabled' : ''; ?>>
                            <option value="0" <?php echo $finishId == 0 ? 'selected' : ''; ?>>Sélectionnez un véhicule
                            </option>
                            <?php
                            foreach($finishesList as $finish):
                                ?>
                                <option value="<?php echo $finish->getId(); ?>"
                                    <?php echo $finishId == $finish->getId() ? 'selected' : ''; ?>>
                                    <?php echo $finish->getModel()->getBrand()->getName().' '.$finish->getModel()->getName().' '.$finish->getName(); ?>
                                    -
                                    <?php echo $finish->getDealer()->getName().' ('.$finish->getDealer()->getCountry()->getName().')'; ?>
                                </option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <br><br>
            <h3 class="page-header">Détails du pack</h3>
            <div class="row">
                <div class="form-group">
                    <label for="inputPackName" class="col-lg-2 form-control-label">Nom :</label>
                    <div class="col-lg-4">
                        <input type="text" id="inputPackName" class="form-control formManager"
                               name="<?php echo $mode; ?>Pack[name]"
                               data-formManager="required string" placeholder="visibilité"
                               value="<?php echo $packName; ?>" required>
                    </div>
                </div>
            </div>
            <br><br>
            <h3 class="page-header">Equipements inclus</h3>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <ul class="nav nav-stacked nav-pills nav-pills-lg" role="tablist">
                            <?php
                            foreach($familiesList as $key => $family):
                                $familyName = $family['name'];
                                $target = preg_replace_callback('/ /', function(){ return ''; }, $familyName);
                                ?>
                                <li class="nav-item">
                                    <a href="#serial<?php echo $target; ?>" data-toggle="pill" role="tab"
                                       class="nav-link <?php echo ($key == 0) ? 'active' : ''; ?>"><?php echo $familyName; ?></a>
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
                                $target = preg_replace_callback('/(\s)/', function(){ return ''; }, $familyName);
                                ?>
                                <div class="tab-pane text-small <?php echo $key == 0 ? 'active' : ''; ?>"
                                     id="serial<?php echo $target; ?>" role="tabpanel">
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
                                                <input type="checkbox" name="<?php echo $mode; ?>Pack[equipment][]"
                                                       value="<?php echo $equipment->getId(); ?>" <?php echo in_array($equipment->getId(), $equipmentsIds) ? 'checked' : ''; ?>>
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
            <h3 class="page-header">Couleur et jantes</h3>
            <div class="row">
                <div class="form-group">
                    <label for="inputPackColor" class="col-lg-2 form-control-label">Couleur :</label>
                    <div class="col-lg-4">
                        <select name="<?php echo $mode; ?>Pack[color]" id="inputPackColor" class="form-control">
                            <option value="0" <?php echo $colorId == 0 ? 'selected' : ''; ?>>Aucune</option>
                            <?php
                            foreach($colorsList as $color):
                                ?>
                                <option
                                    value="<?php echo $color->getId(); ?>" <?php echo $colorId == $color->getId() ? 'selected' : ''; ?>>
                                    <?php echo $color->getName().' '.$color->getDetails(); ?>
                                </option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPackRims" class="col-lg-2 form-control-label">Jantes :</label>
                    <div class="col-lg-4">
                        <select name="<?php echo $mode; ?>Pack[rim]" id="inputPackRims" class="form-control">
                            <option value="0" <?php echo $rimId == 0 ? 'selected' : ''; ?>>Aucune</option>
                            <?php
                            foreach($rimsList as $rimModel):
                                ?>
                                <option
                                    value="<?php echo $rimModel->getId(); ?>" <?php echo $rimId == $rimModel->getId() ? 'selected' : ''; ?>>
                                    <?php echo $rimModel->getName().' - '.$rimModel->getRimType().' '.$rimModel->getFrontDiameter().'"'; ?>
                                </option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <br><br>
            <h3 class="page-header">Tarification</h3>
            <div class="row">
                <div class="form-group">
                    <label for="inputColorPrice" class="col-lg-2 form-control-label">Prix d'achat :</label>
                    <div class="col-lg-3 input-group">
                        <input type="text" class="form-control formManager" id="inputColorPrice"
                               data-formManager="required float"
                               name="<?php echo $mode; ?>Pack[buyingPrice]" value="<?php echo $postTaxesBuyingPrice; ?>"
                               placeholder="500">
                    <span class="input-group-addon">
                        <select name="<?php echo $mode; ?>Pack[currencyId]" id="inputColorCurrency">
                            <?php
                            foreach($currenciesList as $currency):
                                ?>
                                <option
                                    value="<?php echo $currency->getId(); ?>" <?php echo $currencyId == $currency->getId() ? 'selected' : ''; ?>>
                                    <?php echo $currency->getSymbol(); ?>
                                </option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </span>
                        <span class="input-group-addon">TTC</span>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-4 col-md-offset-8">
                        <button type="submit"
                                class="btn btn-primary"><?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> le pack
                        </button>
                    </div>
                </div>
            </div>
            <br><br><br><br><br><br><br>
        </form>
    </div>
    <?php
}
catch(Exception $e){
    msgReturn_push(array(0, $e->getMessage()));
}