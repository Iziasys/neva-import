<?php
/**
 * Formulaire d'ajout d'une image pour un véhicule
 * Une image sera liée à un véhicule par son chemin
 * Qui sera sous la forme :
 * Marque/Modele/Finition/Carrosserie_nbPortes.png
 * Exemple :
 * Renault/Clio IV/Zen/Citadine_3.png
 * Renault/Clio IV/Zen/Break_5.png
 */

//Instanciation d'un objet PDO pour récupérer les données du formulaire
$db = databaseConnection();
//On récupère tous les véhicules uniques (unicité donc sur marque/modele/finition/carrosserie/nbPortes)
$vehiclesList = \Vehicle\DetailsManager::fetchUniqueVehicleTypeList($db);
$lastCreatedVehicle = \Vehicle\DetailsManager::fetchLastCreatedDetails($db);
$db = null;

$vehicleId = $lastCreatedVehicle->getId();

$mode = 'create';

?>

<div class="container">
    <h2 class="page-header"><?php echo $mode == 'create' ? 'Ajout' : 'Modification'; ?> de l'image du véhicule</h2>
    <br>
    <form action="/commande-vehicules/images/visualiser" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="form-group">
                <label for="inputVehicleImageVehicle" class="col-lg-2">Véhicule :</label>
                <div class="col-lg-10">
                    <select class="form-control" name="<?php echo $mode; ?>VehicleImage[vehicleId]" id="inputVehicleImageVehicle" <?php echo $mode == 'modify' ? 'disabled' : ''; ?>>
                        <?php
                        foreach($vehiclesList as $vehicle):
                            $finish = $vehicle->getFinish();
                            $model = $finish->getModel();
                            $brand = $model->getBrand();
                            ?>
                            <option value="<?php echo $vehicle->getId(); ?>"
                                <?php echo $vehicleId == $vehicle->getId() ? 'selected' : ''; ?>>
                                <?php echo $brand->getName().' '.$model->getName().' '.$finish->getName(); ?> -
                                <?php echo $vehicle->getBodywork()->getName().' - '.$vehicle->getDoorsAmount().' portes'; ?>
                            </option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputVehicleImageActualImage" class="col-lg-2">Image actuelle :</label>
                <div class="col-lg-10" id="receiverActualVehicleImage">

                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputVehicleImageNewImage" class="col-lg-2 form-control-label">Nouvelle image :</label>
                <div class="col-lg-4">
                    <label class="file">
                        <input type="file" id="inputVehicleImageNewImage" name="<?php echo $mode; ?>VehicleImage">
                        <span class="file-custom"></span>
                    </label>
                </div>
                <label class="form-control-label col-lg-6">Vous devez choisir un fichier de type .png</label>
            </div>
        </div>
        <br><br>
        <div class="row">
            <div class="form-group">
                <div class="col-md-4 col-md-offset-8">
                    <button type="submit" class="btn btn-primary"><?php echo $mode == 'create' ? 'Ajouter' : 'Modifier'; ?> l'image</button>
                </div>
            </div>
        </div>
        <br><br><br><br><br><br><br>
    </form>
</div>