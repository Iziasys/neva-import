<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();
if(empty($_GET['brandName'])){

}
else{
    $brandName = preg_replace_callback('([_]+)', function(){return ' ';}, $_GET['brandName']);

    $db = databaseConnection();
    $modelsList = \Vehicle\ModelManager::fetchModelsListWhereAVehicleIsAvailable($db, $brandName);
    $db = null;
    ?>
    <h2 class="page-header text-lg-center">Visualisation des véhicules disponibles</h2>
    <div class="container">
        <br><br>
        <a href="/espace-pro/vehicules-demande/visualiser">Tous nos véhicules</a>
        -->
        <?php echo $brandName; ?>
        <br><br>
        <?php
        if(is_a($modelsList, '\Exception')):
            ?>
            Désolé, aucun véhicule n'est disponible actuellement...
            <?php
        else:
            foreach($modelsList as $key => $model):
                $modelNameInUrl = preg_replace_callback('([\s]+)', function(){return '_';}, $model);
                ?>
                <div class="col-lg-2">
                    <a href="/espace-pro/vehicules-demande/visualiser/<?php echo $brandName.'/'.$modelNameInUrl; ?>"><?php echo $model; ?></a>
                </div>
                <?php
            endforeach;
        endif;
        ?>
    </div>
    <?php
}
