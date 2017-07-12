<?php

//Instanciation d'un objet PDO pour récupérer les données du formulaire
$db = databaseConnection();
$familiesList = \Vehicle\EquipmentManager::fetchFamiliesList($db);
$db = null;
?>

<div class="container">
    <h2 class="page-header">Création d'un équipement</h2>
    <br>
    <form action="/commande-vehicules/equipement/creer/" method="post">
        <div class="row">
            <div class="form-group">
                <label for="inputEquipmentFamily" class="col-lg-2 form-control-label">Famille d'équipement :</label>
                <div class="col-lg-10">
                    <select class="form-control" name="createEquipment[familyId]" id="inputEquipmentFamily">
                        <?php
                        foreach($familiesList as $family):
                            ?>
                            <option value="<?php echo $family['id']; ?>">
                                <?php echo $family['name']; ?>
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
                <label for="inputEquipmentName" class="col-lg-2 form-control-label">Equipement :</label>
                <div class="col-lg-10">
                    <input type="text" class="form-control formManager" id="inputEquipmentName" name="createEquipment[name]"
                           data-formManager="required" required>
                </div>
            </div>
        </div>
        <br><br>
        <div class="row">
            <div class="form-group">
                <div class="col-md-4 col-md-offset-8">
                    <button type="submit" class="btn btn-primary">Créer l'équipement</button>
                </div>
            </div>
        </div>
        <br><br><br><br><br><br><br>
    </form>
</div>