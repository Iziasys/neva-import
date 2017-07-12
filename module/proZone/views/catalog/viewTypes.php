<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();

try{
    if(empty($_GET['brandName']) || empty($_GET['modelName'])){
        throw new Exception('Erreur lors de la récupération des critères...');
    }

    $brandName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['brandName']);
    $modelName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['modelName']);

    /* Récupération des véhicules existants en base*/
    $db = databaseConnection();
    //On récupère d'abord les véhicules neufs
    $model = \Vehicle\ModelManager::fetchModelByName($db, $modelName);
    $vehiclesList = null;
    //Si il y a un model correspondant à la recherche
    if(!is_a($model, '\Exception')){
        //On récupère ses finitions
        $vehiclesList = \Vehicle\DetailsManager::fetchVehicleListFromModel($db, $model->getId(), array(array('orderBy' => 'finishName', 'way' => 'ASC')));
        $vat = \Prices\VatManager::fetchFrenchVat($db);
        //Si aucun véhicule correspondant, on fixe à null
        if(is_a($vehiclesList, '\Exception'))
            $vehiclesList = null;
    }

    //On récupère ensuite les véhicules en stock et arrivage correspondants aux critères
    $stockAndArriving = \Vehicle\VehicleInStockManager::searchVehiclesList($db, $brandName, $modelName);
    if(is_a($stockAndArriving, '\Exception'))
        $stockAndArriving = null;

    $db = null;

    ?>
    <h2 class="page-header text-lg-center">Visualisation des véhicules disponibles</h2>
    <br><br>
    <div class="container">
        <a href="/espace-pro/vehicules-demande/visualiser">Tous nos véhicules</a>
        -->
        <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $_GET['brandName']; ?>"><?php echo $brandName; ?></a>
        -->
        <?php echo $modelName; ?>
        <br><br>
    <?php

    if($stockAndArriving == null && $vehiclesList == null):
        ?>
        Désolé, aucun véhicule n'est disponible actuellement...
        <?php
    else:
        ?>
            <div class="col-lg-4 col-lg-offset-2 text-lg-center">
        <?php
        if($stockAndArriving != null):
            ?>
                <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $_GET['brandName'].'/'.$_GET['modelName']; ?>/stock-arrivage">Stock & Arrivages</a>
            <?php
        endif;
        ?>
            </div>
            <div class="col-lg-4 text-lg-center">
        <?php
        if($vehiclesList != null):
            ?>
                <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $_GET['brandName'].'/'.$_GET['modelName']; ?>/commande">Commandes VN</a>
            <?php
        endif;
        ?>
            </div>
        <?php
    endif;
    ?>
    </div>
    <?php
}
catch(Exception $e){
    echo $e->getMessage();
}