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
    $db = null;

    if($_GET['action'] == 'modify' && !empty($_GET['colorId'])){
        $db = databaseConnection();
        $assoc = \Vehicle\FinishManager::fetchAssocExternalColor($db, (int)$_GET['colorId']);
        /** @var int $assocId */
        $assocId = (int)$assoc['assocId'];
        /** @var \Vehicle\Finish $finish */
        $finish = $assoc['finish'];
        /** @var \Vehicle\ExternalColor $color */
        $color = $assoc['color'];
        /** @var \Prices\Price|null $price */
        $price = $assoc['price'];
        if($price != null){
            $countryId = $price->getCountryId();
            $vat = \Prices\VatManager::fetchFrenchVat($db);
            $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
        }
        $db = null;

        $finishId = $finish->getId();

        $colorName = $color->getName();
        $colorDenomination = $color->getDetails();
        $biTone = $color->getBiTone();
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

        $colorName = '';
        $colorDenomination = '';
        $biTone = 0;
        $postTaxesBuyingPrice = '';
        $currencyId = 0;

        $mode = 'create';
    }

    ?>

    <div class="container">
        <h2 class="page-header"><?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> d'une couleur</h2>
        <br>
        <form action="/commande-vehicules/couleur/visualiser" method="post">
            <?php
            if($mode == 'modify'):
                ?>
                <input type="hidden" name="<?php echo $mode; ?>Color[id]" value="<?php echo $assocId; ?>">
                <?php
            endif;
            ?>
            <div class="row">
                <div class="form-group">
                    <label for="inputColorfinish" class="col-lg-2 form-control-label">Véhicule :</label>
                    <div class="col-lg-10">
                        <select class="form-control" name="<?php echo $mode; ?>Color[finishId]"
                                id="inputColorfinish" <?php echo $mode == 'modify' ? 'disabled' : ''; ?>>
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
            <h3 class="page-header">Informations</h3>
            <div class="row">
                <div class="form-group">
                    <label for="inputColorName" class="col-lg-2 form-control-label">Nom :</label>
                    <div class="col-lg-2">
                        <input type="text" class="form-control formManager" id="inputColorName"
                               name="<?php echo $mode; ?>Color[name]"
                               value="<?php echo $colorName; ?>" placeholder="Rouge" data-formManager="required"
                               required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputColorDenomination" class="col-lg-2 form-control-label">Dénomination :</label>
                    <div class="col-lg-2">
                        <input type="text" class="form-control" id="inputColorDenomination"
                               name="<?php echo $mode; ?>Color[denomination]" value="<?php echo $colorDenomination; ?>"
                               placeholder="Passion">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 form-control-label">Bi-ton :</label>
                    <div class="col-lg-2">
                        <label class="radio-inline">
                            <input type="radio"
                                   name="<?php echo $mode; ?>Color[bi-tone]" <?php echo $biTone ? 'checked' : ''; ?>
                                   value="1"> Oui
                        </label>
                        <label class="radio-inline">
                            <input type="radio"
                                   name="<?php echo $mode; ?>Color[bi-tone]" <?php echo !$biTone ? 'checked' : ''; ?>
                                   value="0"> Non
                        </label>
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
                               name="<?php echo $mode; ?>Color[buyingPrice]"
                               value="<?php echo $postTaxesBuyingPrice; ?>"
                               placeholder="500">
                    <span class="input-group-addon">
                        <select name="<?php echo $mode; ?>Color[currencyId]" id="inputColorCurrency">
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
                                class="btn btn-primary"><?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> la
                            couleur
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