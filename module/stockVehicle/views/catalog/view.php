<?php
?>
<div class="container-fluid">
    <h2 class="page-header text-lg-center">Visualisation des véhicules en stock</h2>
    <div class="col-lg-4 col-lg-offset-4">
        <label class="checkbox-inline">
            <input type="checkbox" id="acceptNewVehicles" checked> Véhicules Neufs
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" id="acceptUsedVehicles" checked> Véhicules D'occasions
        </label>
    </div>
    <br><br>
    <div id="receiverStockCatalog">
        <?php include $_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/stockVehicle/ajax/fetchCatalogOfVehicles.ajax.php'; ?>
    </div>
</div>