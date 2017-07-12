<?php
if($_GET['action'] == 'modify' && !empty($_GET['powerId'])){
    $horsePowerId = (int)$_GET['powerId'];
    $db = databaseConnection();
    $horsePower = \Prices\HorsepowerPriceManager::fetchPrice($db, $horsePowerId);
    $db = null;

    $department = $horsePower->getDepartment();
    $amount = $horsePower->getAmount();

    $mode = 'modify';
}
else{
    $department = '';
    $amount = '';

    $mode = 'create';
}
?>

<div class="container">
    <h3 class="page-header">
        <?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> du Tarif Fiscal
    </h3>
    <br>
    <form action="/tarifs-fiscaux/visualiser" method="POST">
        <?php
        if($mode == 'modify'):
            ?>
            <input type="hidden" name="<?php echo $mode; ?>HorsePower[id]" value="<?php echo $horsePowerId; ?>">
            <?php
        endif;
        ?>
        <div class="row">
            <div class="form-group">
                <label for="inputHorsePowerName" class="col-lg-2 col-lg-offset-1 form-control-label">Département :</label>
                <div class="col-lg-3">
                    <input type="text" id="inputHorsePowerName" name="<?php echo $mode; ?>HorsePower[name]"
                           class="form-control" value="<?php echo $department; ?>" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="inputHorsePowerAmount" class="col-lg-2 form-control-label">Coût du cheval fiscal* :</label>
                <div class="col-lg-3 input-group">
                    <input type="text" id="inputHorsePowerAmount" name="<?php echo $mode; ?>HorsePower[amount]"
                           class="form-control formManager" data-formManager="required float" value="<?php echo $amount; ?>" required>
                    <span class="input-group-addon">€ HT</span>
                </div>
            </div>
        </div>

        <br>
        <div class="form-group row">
            <div class="col-lg-2 col-lg-offset-8">
                <button type="submit" class="btn btn-primary btn-principalColor formManager"
                        data-formManager="submitInput">
                    <?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> le tarif fiscal
                </button>
            </div>
        </div>
    </form>
</div>
