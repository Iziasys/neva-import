<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();

$db = databaseConnection();
$brandsList = \Vehicle\BrandManager::fetchBrandListWhereAVehicleIsAvailable($db);
$db = null;
?>
<h2 class="page-header text-lg-center">Visualisation des véhicules disponibles</h2>
<div class="container">
    <br><br>
    Tous nos véhicules
    <br><br>
    <?php
    if(is_a($brandsList, '\Exception')):
        ?>
        Désolé, aucun véhicule n'est disponible actuellement...
        <?php
    else:
        foreach($brandsList as $key => $brand):
            $brandNameInUrl = preg_replace_callback('([\s]+)', function(){return '_';}, $brand);
            ?>
            <div class="col-lg-2">
                <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $brandNameInUrl; ?>"><?php echo $brand; ?></a>
            </div>
            <?php
        endforeach;
    endif;
    ?>
</div>
