<?php
$db = databaseConnection();
$arrayOrder = array(
    array('orderBy' => 'brandName', 'way' => 'ASC'),
    array('orderBy' => 'modelName', 'way' => 'ASC'),
    array('orderBy' => 'finishName', 'way' => 'ASC')
);
$finishesList = \Vehicle\FinishManager::fetchFinishList($db, true, $arrayOrder, 1000);
$db = null;
?>

<!-- MODAL DE CONFIRMATION DE SUPPRESSION DE VEHICULE -->
<div class="modal fade confirm-delete-finish-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title" id="exampleModalLabel">Êtes-vous sur de vouloir supprimer cette finition ?</h2>
            </div>
            <div class="modal-body">
                <input type="hidden" id="inputFinishId" value="0">
                <div class="container-fluid">
                    <div class="col-lg-4 col-lg-offset-2">
                        <button class="btn btn-primary btn-principal-color" data-dismiss="modal" type="button">
                            Non
                        </button>
                    </div>
                    <div class="col-lg-4 col-lg-offset-1">
                        <button type="button" class="btn btn-danger" id="btnConfirmDeleteFinish">
                            Oui
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODAL DE CONFIRMATION DE SUPPRESSION DE VEHICULE -->

<div class="container">
    <h3 class="page-header">Visualisation des finitions</h3>

    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Dénomination</th>
            <th>Concessionnaire</th>
            <th>Pays</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(!is_a($finishesList, '\Exception')):
            foreach($finishesList as $finish):
                $model = $finish->getModel();
                $brand = $model->getBrand();
                $dealer = $finish->getDealer();
                $country = $dealer->getCountry();
                ?>
                <tr>
                    <td><?php echo $brand->getName().' '.$model->getName().' '.$finish->getName(); ?></td>
                    <td><?php echo $dealer->getName(); ?></td>
                    <td><?php echo $country->getName(); ?></td>
                    <td>
                        <a href="/commande-vehicules/vehicule-generique/modifier/<?php echo $finish->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                        <form action="/commande-vehicules/vehicule-generique/visualiser" method="post" style="display:inline">
                            <?php
                            if($finish->getActive()):
                                ?>
                                <input type="hidden" name="disableFinish[id]" value="<?php echo $finish->getId(); ?>">
                                <button class="btn btn-warning-outline btn-sm fa fa-times"></button>
                                <?php
                            else:
                                ?>
                                <input type="hidden" name="enableFinish[id]" value="<?php echo $finish->getId(); ?>">
                                <button class="btn btn-success-outline btn-sm fa fa-check"></button>
                                <?php
                            endif;
                            ?>
                        </form>
                        <a href="/commande-vehicules/vehicule-generique/supprimer/<?php echo $finish->getId(); ?>"
                           class="btn btn-danger-outline btn-sm fa fa-trash btnAskDeleteVehicle" data-finishId="<?php echo $finish->getId(); ?>"
                           data-toggle="modal" data-target=".confirm-delete-finish-modal-lg"></a>
                    </td>
                </tr>
                <?php
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</div>
