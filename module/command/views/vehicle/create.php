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
    $bodyworkList = \Vehicle\BodyworkManager::fetchBodyworkList($db, 'name');
    $fuelsList = \Vehicle\FuelManager::fetchFuelList($db, 'name');
    $gearboxesList = \Vehicle\GearboxManager::fetchGearboxList($db, 'name');
    $currenciesList = \Prices\CurrencyManager::fetchCurrenciesList($db, 'currency');
    $db = null;

    if($_GET['action'] == 'modify' && !empty($_GET['vehicleId'])){
        $detailsId = (int)$_GET['vehicleId'];

        $db = databaseConnection();
        $details = \Vehicle\DetailsManager::fetchDetails($db, $detailsId);
        $price = \Prices\PriceManager::fetchPrice($db, $details->getPriceId());
        $countryId = $price->getCountryId();
        $vat = \Prices\VatManager::fetchVatFromCountry($db, $countryId);
        $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
        $pretaxBuyingPrice = $price->getPretaxBuyingPrice();
        $db = null;

        $finishId = $details->getFinishId();
        $engineSize = $details->getEngineSize();
        $engine = $details->getEngine()->getName();
        $dynamicalPower = $details->getDynamicalPower();
        $co2 = $details->getCo2();
        $fiscalPower = $details->getFiscalPower();
        $transmission = $details->getTransmission()->getName();
        $doorsAmount = $details->getDoorsAmount();
        $sitsAmount = $details->getSitsAmount();
        $gearboxId = $details->getGearbox()->getId();
        $bodyworkId = $details->getBodywork()->getId();
        $fuelId = $details->getFuel()->getId();
        $postTaxesBuyingPrice = round(\Prices\VatManager::convertToPostTaxes($pretaxBuyingPrice, $vat->getAmount()), 2);
        $currencyId = $currency->getId();
        $publicPrice = $price->getPostTaxesPublicPrice();
        $maximumDiscount = $price->getMaximumDiscount();

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
        $engineSize = '';
        $engine = '';
        $dynamicalPower = '';
        $co2 = '';
        $fiscalPower = '';
        $transmission = '';
        $doorsAmount = '';
        $sitsAmount = '';
        $gearboxId = 0;
        $bodyworkId = '';
        $fuelId = 0;
        $postTaxesBuyingPrice = '';
        $currencyId = 0;
        $publicPrice = '';
        $maximumDiscount = '';

        $mode = 'create';
    }

    ?>

    <div class="container">
        <h2 class="page-header">Création d'un véhicule</h2>
        <br>
        <form action="/commande-vehicules/vehicule/visualiser" method="post">
            <?php
            if($mode == 'modify'):
                ?>
                <input type="hidden" name="<?php echo $mode; ?>Vehicle[id]" value="<?php echo $detailsId; ?>">
                <?php
            endif;
            ?>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehiclefinish" class="col-lg-2 form-control-label">Véhicule :</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="<?php echo $mode; ?>Vehicle[finishId]"
                                id="inputVehiclefinish" <?php echo $mode == 'modify' ? 'disabled' : ''; ?>>
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
            <h3 class="page-header">Caractéristiques</h3>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleEngineSize" class="col-lg-2 form-control-label">Cylindrée :</label>
                    <div class="col-lg-2 input-group">
                        <input id="inputVehicleEngineSize" type="text" class="form-control formManager"
                               data-formManager="required float"
                               name="<?php echo $mode; ?>Vehicle[engineSize]" placeholder="1.6"
                               value="<?php echo $engineSize; ?>" required>
                        <span class="input-group-addon">L</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleEngine" class="col-lg-2 form-control-label">Motorisation :</label>
                    <div class="col-lg-2">
                        <input id="inputVehicleEngine" type="text" class="form-control formManager"
                               data-formManager="required"
                               name="<?php echo $mode; ?>Vehicle[engine]" placeholder="HDi"
                               value="<?php echo $engine; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleDynamicalPower" class="col-lg-2 form-control-label">Puiss. Dynamique
                        :</label>
                    <div class="col-lg-2 input-group">
                        <input id="inputVehicleDynamicalPower" type="text" class="form-control formManager"
                               data-formManager="required int"
                               name="<?php echo $mode; ?>Vehicle[dynamicalPower]" placeholder="130"
                               value="<?php echo $dynamicalPower; ?>" required>
                        <span class="input-group-addon">ch</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleCo2" class="col-lg-2 form-control-label">Co2 :</label>
                    <div class="col-lg-2 input-group">
                        <input id="inputVehicleCo2" type="text" class="form-control formManager"
                               data-formManager="required int"
                               name="<?php echo $mode; ?>Vehicle[co2]" placeholder="98" value="<?php echo $co2; ?>"
                               required>
                        <span class="input-group-addon">g/km</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleFiscalPower" class="col-lg-2 form-control-label">Puiss. Fiscale :</label>
                    <div class="col-lg-2 input-group">
                        <input id="inputVehicleFiscalPower" type="text" class="form-control formManager"
                               data-formManager="required int"
                               name="<?php echo $mode; ?>Vehicle[fiscalPower]" placeholder="6"
                               value="<?php echo $fiscalPower; ?>" required>
                        <span class="input-group-addon">cv</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleTransmission" class="col-lg-2 form-control-label">Transmission :</label>
                    <div class="col-lg-2">
                        <input id="inputVehicleTransmission" type="text" class="form-control formManager"
                               data-formManager="required"
                               name="<?php echo $mode; ?>Vehicle[transmission]" placeholder="4x2"
                               value="<?php echo $transmission; ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehicleDoorsAmount" class="col-lg-2 form-control-label">Nb. Portes :</label>
                    <div class="col-lg-2">
                        <input id="inputVehicleDoorsAmount" type="text" class="form-control formManager"
                               data-formManager="required int"
                               name="<?php echo $mode; ?>Vehicle[doorsAmount]" placeholder="5"
                               value="<?php echo $doorsAmount; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleSitsAmount" class="col-lg-2 form-control-label">Nb. Places :</label>
                    <div class="col-lg-2">
                        <input id="inputVehicleSitsAmount" type="text" class="form-control formManager"
                               data-formManager="required int"
                               name="<?php echo $mode; ?>Vehicle[sitsAmount]" placeholder="5"
                               value="<?php echo $sitsAmount; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleGearbox" class="col-lg-2 form-control-label">Boite de vitesse :</label>
                    <div class="col-lg-2">
                        <select name="<?php echo $mode; ?>Vehicle[gearbox]" id="inputVehicleGearbox"
                                class="form-control">
                            <option value="0" <?php echo $gearboxId == 0 ? 'selected' : ''; ?>>Sélectionnez une boite de
                                vitesse
                            </option>
                            <?php
                            foreach($gearboxesList as $gearbox):
                                ?>
                                <option value="<?php echo $gearbox->getId(); ?>"
                                    <?php echo $gearboxId == $gearbox->getId() ? 'selected' : ''; ?>>
                                    <?php echo $gearbox->getName(); ?>
                                </option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <br><br>
            <h3 class="page-header">Carrosserie</h3>
            <div class="row">
                <?php
                foreach($bodyworkList as $bodywork):
                    ?>
                    <div class="col-md-3">
                        <label class="radio-inline">
                            <input type="radio" name="<?php echo $mode; ?>Vehicle[bodywork]"
                                   value="<?php echo $bodywork->getId(); ?>"
                                <?php echo $bodyworkId == $bodywork->getId() ? 'checked' : ''; ?>>
                            <?php echo $bodywork->getName(); ?>
                        </label>
                    </div>
                    <?php
                endforeach;
                ?>
            </div>
            <br><br>
            <h3 class="page-header">Carburant</h3>
            <div class="row">
                <?php
                foreach($fuelsList as $fuel):
                    ?>
                    <div class="col-md-3">
                        <label class="radio-inline">
                            <input type="radio" name="<?php echo $mode; ?>Vehicle[fuel]"
                                   value="<?php echo $fuel->getId(); ?>"
                                <?php echo $fuelId == $fuel->getId() ? 'checked' : ''; ?>>
                            <?php echo $fuel->getName(); ?>
                        </label>
                    </div>
                    <?php
                endforeach;
                ?>
            </div>
            <br><br>
            <h3 class="page-header">Tarification</h3>
            <div class="row">
                <div class="form-group">
                    <label for="inputVehiclePrice" class="col-lg-2 form-control-label">Prix d'achat :</label>
                    <div class="col-lg-3 input-group">
                        <input type="text" class="form-control formManager" id="inputVehiclePrice"
                               data-formManager="required float"
                               name="<?php echo $mode; ?>Vehicle[buyingPrice]"
                               value="<?php echo $postTaxesBuyingPrice; ?>"
                               placeholder="12800">
                    <span class="input-group-addon">
                        <select name="<?php echo $mode; ?>Vehicle[currencyId]" id="inputVehicleCurrency">
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
                <div class="form-group">
                    <label for="inputVehiclePublicPrice" class="col-lg-2 form-control-label">Prix public France
                        :</label>
                    <div class="col-lg-2 input-group col-without-padding">
                        <input type="text" class="form-control formManager fit-to-container"
                               id="inputVehiclePublicPrice"
                               data-formManager="required float"
                               name="<?php echo $mode; ?>Vehicle[publicPrice]" value="<?php echo $publicPrice; ?>"
                               placeholder="15000">
                        <span class="input-group-addon">€ TTC</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputVehicleMaximumDiscount" class="col-lg-2 form-control-label">Remise max France
                        :</label>
                    <div class="col-lg-1 input-group col-without-padding">
                        <input type="text" class="form-control fit-to-container formManager"
                               id="inputVehicleMaximumDiscount"
                               data-formManager="required float"
                               name="<?php echo $mode; ?>Vehicle[maximumDiscount]"
                               value="<?php echo $maximumDiscount; ?>"
                               placeholder="12">
                        <span class="input-group-addon min-padding">%</span>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-4 col-md-offset-8">
                        <button type="submit"
                                class="btn btn-primary"><?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> le
                            véhicule
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