<?php
$db = databaseConnection();
$currenciesList = \Prices\CurrencyManager::fetchCurrenciesList($db);
$db = null;

if($_GET['action'] == 'modify' && !empty($_GET['countryId'])){
    $countryId = (int)$_GET['countryId'];
    $db = databaseConnection();
    $country = \Prices\CountryManager::fetchCountry($db, $countryId);
    $db = null;

    $name = $country->getName();
    $abbreviation = $country->getAbbreviation();
    $currencyId = $country->getCurrencyId();
    $vat = $country->getVat()->getAmount();
    $freightCharges = $country->getFreightCharges()->getAmount();

    $mode = 'modify';
}
else{
    $name = '';
    $abbreviation = '';
    $currencyId = 0;
    $vat = '';
    $freightCharges = '';

    $mode = 'create';
}
?>

<div class="container">
    <h3 class="page-header">
        <?php echo $mode == 'create' ? 'Ajout' : 'Modification'; ?> d'un pays
    </h3>
    <br>
    <form action="/pays/visualiser" method="POST">
        <?php
        if($mode == 'modify'):
            ?>
            <input type="hidden" name="<?php echo $mode; ?>Country[id]" value="<?php echo $countryId; ?>">
            <?php
        endif;
        ?>
        <div class="row">
            <div class="form-group">
                <label for="inputCountryName" class="col-md-2 col-md-offset-1 form-control-label">Nom du pays* :</label>
                <div class="col-md-3">
                    <input type="text" id="inputCountryName" name="<?php echo $mode; ?>Country[name]"
                           class="form-control formManager" data-formManager="required pureString" value="<?php echo $name; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputCountryAbbreviation" class="col-md-2 form-control-label">Abbreviation* :</label>
                <div class="col-md-3">
                    <input type="text" id="inputCountryAbbreviation" name="<?php echo $mode; ?>Country[abbreviation]"
                           class="form-control formManager" data-formManager="required pureString" value="<?php echo $abbreviation; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="selectCountrySymbol" class="col-md-2 col-md-offset-1 form-control-label">Devise* :</label>
                <div class="col-md-3">
                    <select id="selectCountrySymbol" name="<?php echo $mode; ?>Country[currency]"
                           class="form-control">
                        <option value="0" <?php echo $currencyId == 0 ? 'selected' : ''; ?>>Choisissez une devise</option>
                        <?php
                        foreach($currenciesList as $currency):
                        ?>
                            <option value="<?php echo $currency->getId(); ?>"
                                <?php echo $currency->getId() == $currencyId ? 'selected' : ''; ?>>
                                <?php echo $currency->getCurrency().' ( '.$currency->getSymbol().' )'; ?>
                            </option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputCountryVat" class="col-md-2 col-md-offset-1 form-control-label">TVA en vigueur* :</label>
                <div class="col-md-3 input-group">
                    <input type="text" id="inputCountryVat" name="<?php echo $mode; ?>Country[vat]"
                           class="form-control formManager" data-formManager="required float" value="<?php echo $vat; ?>" required>
                    <span class="input-group-addon">%</span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputCountryFreightCharges" class="col-md-2 form-control-label">Coût du transport* :</label>
                <div class="col-md-3 input-group">
                    <input type="text" id="inputCountryFreightCharges" name="<?php echo $mode; ?>Country[freightCharges]"
                           class="form-control formManager" data-formManager="required float" value="<?php echo $freightCharges; ?>" required>
                    <span class="input-group-addon">€ HT</span>
                </div>
            </div>
        </div>
        <br>
        <div class="form-group row">
            <div class="col-md-2 col-md-offset-8">
                <button type="submit" class="btn btn-primary btn-principalColor formManager"
                        data-formManager="submitInput">
                    <?php echo $mode == 'create' ? 'Ajouter' : 'Modifier'; ?> le pays
                </button>
            </div>
        </div>
    </form>
</div>
