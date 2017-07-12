<?php
if($_GET['action'] == 'modify' && !empty($_GET['currencyId'])){
    $currencyId = (int)$_GET['currencyId'];
    $db = databaseConnection();
    $currency = \Prices\CurrencyManager::fetchCurrency($db, $currencyId);
    $db = null;

    $name = $currency->getCurrency();
    $abbreviation = $currency->getAbbreviation();
    $symbol = $currency->getSymbol();
    $rateToEuro = $currency->getExchangeRate()->getRateToEuro();
    $rateFromEuro = 1 / $rateToEuro;

    $mode = 'modify';
}
else{
    $name = '';
    $abbreviation = '';
    $symbol = '';
    $rateFromEuro = '';
    $rateToEuro = '';

    $mode = 'create';
}
?>

<div class="container">
    <h3 class="page-header">
        <?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> d'une devise
    </h3>
    <br>
    <form action="/devises/visualiser" method="POST">
        <?php
        if($mode == 'modify'):
            ?>
            <input type="hidden" name="<?php echo $mode; ?>Currency[id]" value="<?php echo $currencyId; ?>">
            <?php
        endif;
        ?>
        <div class="row">
            <div class="form-group">
                <label for="inputCurrencyName" class="col-md-2 col-md-offset-1 form-control-label">Nom de la devise* :</label>
                <div class="col-md-3">
                    <input type="text" id="inputCurrencyName" name="<?php echo $mode; ?>Currency[name]"
                           class="form-control formManager" data-formManager="required pureString" value="<?php echo $name; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputCurrencyAbbreviation" class="col-md-2 form-control-label">Abbreviation* :</label>
                <div class="col-md-3">
                    <input type="text" id="inputCurrencyAbbreviation" name="<?php echo $mode; ?>Currency[abbreviation]"
                           class="form-control formManager" data-formManager="required pureString" value="<?php echo $abbreviation; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputCurrencySymbol" class="col-md-2 col-md-offset-1 form-control-label">Symbole* :</label>
                <div class="col-md-3">
                    <input type="text" id="inputCurrencySymbol" name="<?php echo $mode; ?>Currency[symbol]"
                           class="form-control formManager" data-formManager="required" value="<?php echo $symbol; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputCurrencyFromEuro" class="col-md-2 col-md-offset-1 form-control-label">1 € = </label>
                <div class="col-md-3 input-group">
                    <input type="text" id="inputCurrencyFromEuro" name="<?php echo $mode; ?>Currency[conversionFromEuro]"
                           class="form-control formManager" data-formManager="required float" value="<?php echo $rateFromEuro; ?>" required>
                    <span class="input-group-addon receiverCurrency"><?php echo $symbol; ?></span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputCurrencyToEuro" class="col-md-2 form-control-label">1 <span class="receiverCurrency"><?php echo $symbol; ?></span> = </label>
                <div class="col-md-3 input-group">
                    <input type="text" id="inputCurrencyToEuro" name="<?php echo $mode; ?>Currency[conversionToEuro]"
                           class="form-control formManager" data-formManager="required float" value="<?php echo $rateToEuro; ?>" required>
                    <span class="input-group-addon">€</span>
                </div>
            </div>
        </div>
        <br>
        <div class="form-group row">
            <div class="col-md-2 col-md-offset-8">
                <button type="submit" class="btn btn-primary btn-principalColor formManager"
                        data-formManager="submitInput">
                    <?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> la devise
                </button>
            </div>
        </div>
    </form>
</div>
