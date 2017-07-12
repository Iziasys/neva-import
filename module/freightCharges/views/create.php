<?php
if($_GET['action'] == 'modify' && !empty($_GET['freightId'])){
    $freightId = (int)$_GET['freightId'];
    $db = databaseConnection();
    $freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightCharges($db, $freightId);
    $db = null;

    $departmentName = $freightCharges->getDepartmentName();
    $amount = $freightCharges->getAmount();

    $mode = 'modify';
}
else{
    $departmentName = '';
    $amount = '';

    $mode = 'create';
}
?>

<div class="container">
    <h3 class="page-header">
        <?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> des frais de transport <small>(Tout est indiqué au départ d'OBERENTZEN 68127)</small>
    </h3>
    <br>
    <form action="/transport/visualiser" method="POST">
        <?php
        if($mode == 'modify'):
            ?>
            <input type="hidden" name="<?php echo $mode; ?>FreightCharges[id]" value="<?php echo $freightId; ?>">
            <?php
        endif;
        ?>
        <div class="row">
            <div class="form-group">
                <label for="inputFreightChargesName" class="col-md-2 col-md-offset-1 form-control-label">Département* :</label>
                <div class="col-md-3">
                    <input type="text" id="inputFreightChargesName" name="<?php echo $mode; ?>FreightCharges[name]"
                           class="form-control formManager" data-formManager="required pureString" value="<?php echo $departmentName; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputFreightChargesAmount" class="col-md-2 form-control-label">Coût du transport* :</label>
                <div class="col-md-3 input-group">
                    <input type="text" id="inputFreightChargesAmount" name="<?php echo $mode; ?>FreightCharges[amount]"
                           class="form-control formManager" data-formManager="required float" value="<?php echo $amount; ?>" required>
                    <span class="input-group-addon">€ HT</span>
                </div>
            </div>
        </div>

        <br>
        <div class="form-group row">
            <div class="col-md-2 col-md-offset-8">
                <button type="submit" class="btn btn-primary btn-principalColor formManager"
                        data-formManager="submitInput">
                    <?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> les frais de transport
                </button>
            </div>
        </div>
    </form>
</div>
