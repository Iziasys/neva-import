<?php
$db = databaseConnection();
$arrayOrder = array(
    array('orderBy' => 'brandName', 'way' => 'ASC'),
    array('orderBy' => 'modelName', 'way' => 'ASC'),
    array('orderBy' => 'finishName', 'way' => 'ASC'),
    array('orderBy' => 'engineSize', 'way' => 'ASC'),
    array('orderBy' => 'vhcl_engine.name', 'way' => 'ASC'),
    array('orderBy' => 'dynamicalPower', 'way' => 'ASC'),
);
$vehiclesList = \Vehicle\DetailsManager::fetchVehicleList($db, true, $arrayOrder, 1000);
$db = null;
?>

<!-- MODAL DE CONFIRMATION DE SUPPRESSION DE VEHICULE -->
<div class="modal fade confirm-delete-vehicle-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title" id="exampleModalLabel">Êtes-vous sur de vouloir supprimer ce véhicule ?</h2>
            </div>
            <div class="modal-body">
                <input type="hidden" id="inputFinishId" value="0">
                <div class="container-fluid">
                    <div class="col-lg-4 col-lg-offset-2">
                        <button type="button" class="btn btn-primary btn-principal-color" data-dismiss="modal" >
                            Non
                        </button>
                    </div>
                    <div class="col-lg-4 col-lg-offset-1">
                        <button type="button" class="btn btn-danger" id="btnConfirmDeleteVehicle">
                            Oui
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL DE CONFIRMATION DE SUPPRESSION DE VEHICULE -->

<div class="container-fluid">
    <h3 class="page-header text-lg-center">Visualisation des véhicules</h3>

    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Informations</th>
            <th>Details</th>
            <th>Carrosserie</th>
            <th>Boite</th>
            <th>Co2</th>
            <th>Prix achat HT</th>
            <th>Concessionnaire</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(!is_a($vehiclesList, '\Exception')):
            foreach($vehiclesList as $vehicle):
                $finish = $vehicle->getFinish();
                $model = $finish->getModel();
                $brand = $model->getBrand();
                $dealer = $finish->getDealer();
                ?>
                <tr>
                    <td><?php echo $brand->getName().'<br>'.$model->getName().' '.$finish->getName(); ?></td>
                    <td><?php echo $vehicle->getEngineSize().' '.$vehicle->getEngine()->getName().' '.$vehicle->getDynamicalPower().'<br>'.$vehicle->getTransmission()->getName(); ?></td>
                    <td><?php echo $vehicle->getBodywork()->getName().'<br>'.$vehicle->getDoorsAmount().'portes - '.$vehicle->getSitsAmount().' places'; ?></td>
                    <td><?php echo $vehicle->getGearbox()->getName(); ?></td>
                    <td><?php echo $vehicle->getCo2(); ?> g/km</td>
                    <td><?php echo $vehicle->getPrice()->getPretaxBuyingPrice().' '.$vehicle->getPrice()->getCurrency()->getSymbol(); ?></td>
                    <td><?php echo $dealer->getName().' ('.$dealer->getCountry()->getAbbreviation().')'; ?></td>
                    <td>
                        <a href="/commande-vehicules/vehicule/modifier/<?php echo $vehicle->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil" title="Modifier"></a>
                        <form action="/commande-vehicules/vehicule/visualiser" method="post" style="display:inline">
                            <?php
                            if($vehicle->getAvailable()):
                                ?>
                                <input type="hidden" name="disableVehicle[id]" value="<?php echo $vehicle->getId(); ?>">
                                <button class="btn btn-warning-outline btn-sm fa fa-times" title="Désactiver"></button>
                                <?php
                            else:
                                ?>
                                <input type="hidden" name="enableVehicle[id]" value="<?php echo $vehicle->getId(); ?>">
                                <button class="btn btn-success-outline btn-sm fa fa-check" title="Activer"></button>
                                <?php
                            endif;
                            ?>
                        </form>
                        <a href="#"
                           class="btn btn-danger-outline btn-sm fa fa-trash btnAskDeleteVehicle" title="Supprimer"
                           data-toggle="modal" data-target=".confirm-delete-vehicle-modal-lg" data-finishId="<?php echo $vehicle->getId(); ?>"></a>
                        <a href="/stock-arrivage/vehicules/copier-depuis-commande/<?php echo $vehicle->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-copy" title="Copier en Stock"></a>
                    </td>
                </tr>
                <?php
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</div>
