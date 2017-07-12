<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();
$dealer = $user->getStructure();
$dealerDepartment = $dealer->getDepartment();
if(empty($_SESSION['selectedClient'])){
    $client = null;
    $clientDepartment = $dealerDepartment;
    $clientId = 0;
}
else{
    /** @var \Users\Client|null $client */
    $client = $_SESSION['selectedClient'];
    $clientDepartment = empty($client->getPostalCode()) ? $dealerDepartment : $client->getDepartment();
    $clientId = $client->getId();
}

try{
    if(empty($_GET['brandName']) || empty($_GET['modelName'])){
        throw new Exception('Erreur lors de la récupération des critères...');
    }

    $brandName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['brandName']);
    $modelName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['modelName']);

    ?>
    <h2 class="page-header text-lg-center">Visualisation des véhicules disponibles</h2>
    <?php

    $brandName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['brandName']);
    $modelName = preg_replace_callback('([_]+)', function(){ return ' '; }, $_GET['modelName']);

    $db = databaseConnection();
    $vehicles = \Vehicle\VehicleInStockManager::searchVehiclesList($db, $brandName, $modelName);
    if(is_a($vehicles, '\Exception')){
        $db = null;
        throw new Exception('Désolé, aucun véhicule n\'est disponible actuellement...');
    }
    $db = null;

    /************************TRAITEMENT DE LA LISTE DE VEHICULES************************/
    /**
     * Le tableau des véhicules sera sous la forme suivante
     * $vehiclesGroupedByFinish = array
     * [
     *      finishName:
     *      [
     *          'sharedEquipments': string[],
     *          'vehicles':
     *          [
     *              'additionalEquipments': string[],
     *              'vehicleDetails': string,
     *              'price': float,
     *              'color': string
     *          ]
     *      ]
     * ]
     */
    $vehiclesGroupedByFinish = array();
    //Remplissage du tableau avec le premier tour
    foreach($vehicles as $key => $vehicleInStock){
        $finishName = ucfirst($vehicleInStock->getFinish());
        $actualSharedEquipments =
            empty($vehiclesGroupedByFinish[$finishName])
                ? array()
                : $vehiclesGroupedByFinish[$finishName]['sharedEquipments'];

        //Premier passage, je prends tous les équipements du véhicule
        if(empty($actualSharedEquipments)){
            $actualSharedEquipments = $vehicleInStock->getEquipments();
        }
        //Aux autres passages
        else{
            //On parcours tous les équipements actuellement partagés
            foreach($actualSharedEquipments as $key2 => $actualSharedEquipment){
                //Et on regarde si il est aussi dans les nouveaux
                if($actualSharedEquipment != null && !in_array($actualSharedEquipment, $vehicleInStock->getEquipments())){
                    //Si ce n'est pas le cas
                    $actualSharedEquipments[$key2] = null;
                }
            }
        }

        $vehiclesGroupedByFinish[$finishName]['sharedEquipments'] = $actualSharedEquipments;
    }
    //Puis on épure le tableau des null précédemment fixés
    foreach($vehicles as $vehicleInStock){
        $finishName = ucfirst($vehicleInStock->getFinish());
        foreach($vehiclesGroupedByFinish[$finishName]['sharedEquipments'] as $key => $value){
            if($value == null){
                unset($vehiclesGroupedByFinish[$finishName]['sharedEquipments'][$key]);
            }
        }
    }

    //Remplissage du détail des véhicules
    foreach($vehicles as $key => $vehicleInStock){
        $additionalEquipments = array();
        $finishName = ucfirst($vehicleInStock->getFinish());
        //Récupération des équipements additionels
        foreach($vehicleInStock->getEquipments() as $equipment){
            if(!in_array($equipment, $vehiclesGroupedByFinish[$finishName]['sharedEquipments'])){
                $additionalEquipments[] = $equipment;
            }
        }

        $vehicleDetails = '<b>'.$vehicleInStock->getEngineSize().' '.$vehicleInStock->getEngine().' '.$vehicleInStock->getDynamicalPower().'</b> Co2 = '.$vehicleInStock->getCo2().'g/km';
        $color = $vehicleInStock->getExternalColor();
        $price = $vehicleInStock->getPrice();

        $vehiclesGroupedByFinish[$finishName]['vehicles'][] = array(
            'additionalEquipments' => $additionalEquipments,
            'vehicleDetails'       => $vehicleDetails,
            'price'                => $price,
            'color'                => $color,
        );
    }
    /************************TRAITEMENT DE LA LISTE DE VEHICULES************************/

    ?>
    <div class="container">
        <br><br>
        <a href="/espace-pro/vehicules-demande/visualiser">Tous nos véhicules</a>
        -->
        <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $_GET['brandName']; ?>"><?php echo $brandName; ?></a>
        -->
        <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $_GET['brandName']; ?>/<?php echo $_GET['modelName']; ?>"><?php echo $modelName; ?></a>
        -->
        Stock & arrivage
    <?php
    foreach($vehiclesGroupedByFinish as $finishName => $finishInformation){
        ?>
        <br><br>
        <h4 class="page-header"><?php echo $brandName.' '.$modelName.' '.$finishName; ?></h4>
        <div class="col-lg-12">
            <?php echo implode(' + ', $finishInformation['sharedEquipments']); ?>
        </div>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th style="width: 18%;">Tarif</th>
                <th>Détails & Options</th>
                <th style="width: 15%;">Couleur</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($finishInformation['vehicles'] as $vehicle):
                ?>
                <tr>
                    <td class="text-danger"><b><?php echo number_format($vehicle['price'], 2, '.', ' ').' € TTC'; ?></b></td>
                    <td>
                        <?php echo $vehicle['vehicleDetails']; ?><br>
                        <?php echo implode(' + ', $vehicle['additionalEquipments']); ?>
                    </td>
                    <td><?php echo $vehicle['color']; ?></td>
                </tr>
                <?php
            endforeach;
            ?>
            </tbody>
        </table>
        <?php
    }
    ?>

    </div>
    <?php
}
catch(Exception $e){
    echo $e->getMessage();
}