<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();

$arrayOrder = array(
    array(
        'orderBy' => 'brand',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'model',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'finish',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'engineSize',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'engine',
        'way' => 'ASC'
    ),
    array(
        'orderBy' => 'dynamicalPower',
        'way' => 'ASC'
    ),
);

$db = databaseConnection();
$vehiclesList = \Vehicle\VehicleInStockManager::fetchVehiclesList($db, null, $arrayOrder, 1000);
$db = null;
$vehiclesListOrderByState = \Vehicle\VehicleInStockManager::orderVehiclesByState($vehiclesList);
?>
<h2 class="page-header text-lg-center">Visualisation des véhicules en stock et arrivage</h2>
<?php
foreach($vehiclesListOrderByState as $key => $vehiclesList):
    $title = '';
    switch($key):
        case 'arriving' :
            $title = 'En arrivage';
            break;
        case 'stock' :
            $title = 'En stock';
            break;
        case 'reserved' :
            $title = 'Optionnés';
            break;
        case 'sold' :
            $title = 'Vendus';
            break;
        default :
            break;
    endswitch;
    ?>
    <h4 class="text-primary"><?php echo $title; ?></h4>
    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Informations</th>
            <th style="width:20%;">Détails</th>
            <th style="width:14%;">Carrosserie</th>
            <th style="width:14%;">Prix de vente</th>
            <th style="width:20%;">Etat</th>
            <th style="width:14%;"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($vehiclesList as $vehicleInStock):
            /** @var \Vehicle\VehicleInStock $vehicleInStock */
            $vehicleInStock = $vehicleInStock;
            $vehicleState = '';
            $today = new DateTime();
            if($vehicleInStock->getIsArriving()):
                $vehicleState = 'En Arrivage : '.$vehicleInStock->getAvailabilityDate()->format('d/m/Y');
            else:
                if($vehicleInStock->getSold()):
                    $vehicleState = 'Vendu le '.$vehicleInStock->getSellingDate()->format('d/m/Y');
                else:
                    if($vehicleInStock->getReserved()):
                        $vehicleState = 'Optionné';
                    else:
                        $vehicleState = 'En Stock';
                    endif;
                endif;
            endif;
            ?>
            <tr data-vehicleId="<?php echo $vehicleInStock->getId(); ?>">
                <td><?php echo $vehicleInStock->getBrand().' '.$vehicleInStock->getModel().' '.$vehicleInStock->getFinish(); ?></td>
                <td><?php echo $vehicleInStock->getEngineSize().' '.$vehicleInStock->getEngine().' '.$vehicleInStock->getDynamicalPower().' '.$vehicleInStock->getTransmission(); ?></td>
                <td><?php echo $vehicleInStock->getBodywork().' - '.$vehicleInStock->getExternalColor(); ?></td>
                <td class="text-xs-right"><?php echo number_format($vehicleInStock->getPrice(), 2, '.', ' '); ?> € TTC</td>
                <td><?php echo $vehicleState; ?></td>
                <td>
                    <a href="/stock-arrivage/vehicules/modifier/<?php echo $vehicleInStock->getId(); ?>"
                       class="btn btn-primary-outline btn-sm fa fa-pencil" title="Modifier le véhicule"></a>
                    <a href="/stock-arrivage/vehicules/cloner/<?php echo $vehicleInStock->getId(); ?>"
                       class="btn btn-primary-outline btn-sm fa fa-copy" title="Dupliquer le véhicule"></a>
                    <a href="/stock-arrivage/vehicules/supprimer/<?php echo $vehicleInStock->getId(); ?>"
                       class="btn btn-danger-outline btn-sm fa fa-times" title="Supprimer le véhicule"></a>
                    <a href="/stock-arrivage/vehicules/copier-coller/<?php echo $vehicleInStock->getId(); ?>"
                       class="btn btn-primary-outline btn-sm fa fa-clipboard" title="Mode Copier/Coller pour LBC"></a>
                    <?php
                    if($key == 'reserved'):
                        ?>
                        <a href="/stock-arrivage/vehicules/visualiser" id="btnUnreserveVehicle"
                           class="btn btn-success-outline btn-sm fa fa-undo" title="Annuler la réservation"></a>
                        <a href="/stock-arrivage/vehicules/visualiser"
                           class="btn btn-success-outline btn-sm fa fa-check btnSellVehicle" title="Valider la vente"></a>
                        <?php
                    endif;
                    ?>
                </td>
            </tr>
            <?php
        endforeach;
        ?>
        </tbody>
    </table>
    <br>
    <?php
endforeach;