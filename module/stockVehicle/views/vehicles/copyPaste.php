<?php
/**
 * Mode copier coller pour affichage rapide sur le bon coin
 * Simplement un textarea avec les informations préformatées (textarea pour modification avant C/C)
 */
try{
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $structure = $user->getStructure();
    if(empty($_GET['vehicleId'])){
        throw new Exception('Veuillez choisir un véhicule');
    }
    $vehicleId = (int)$_GET['vehicleId'];

    //Récupération des informations du véhicule en base
    $db = databaseConnection();
    $vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
    if(is_a($vehicle, '\Exception')){
        $db = null;
        throw $vehicle;
    }
    $db = null;

    ?>
    <div class="container-fluid">
        <h2 class="page-header text-lg-center">Mode Copier/Coller</h2>
        <div class="row">
            <div class="col-lg-6">
                <div class="col-lg-6">Marque :</div>
                <div class="col-lg-6"><b><?php echo $vehicle->getBrand(); ?></b></div>
            </div>
            <div class="col-lg-6">
                <div class="col-lg-6">Modèle :</div>
                <div class="col-lg-6"><b><?php echo $vehicle->getModel(); ?></b></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="col-lg-6">Année Modèle :</div>
                <div class="col-lg-6"><b><?php echo $vehicle->getModelDate()->format('m/Y'); ?></b></div>
            </div>
            <div class="col-lg-6">
                <div class="col-lg-6">Kilométrage :</div>
                <div class="col-lg-6"><b><?php echo number_format($vehicle->getMileage(), 0, '.', ' '); ?> km</b></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="col-lg-6">Carburant :</div>
                <div class="col-lg-6"><b><?php echo $vehicle->getFuel(); ?></b></div>
            </div>
            <div class="col-lg-6">
                <div class="col-lg-6">Boite de vitesse :</div>
                <div class="col-lg-6"><b><?php echo $vehicle->getGearbox(); ?></b></div>
            </div>
        </div>
        <div class="row">
            <label for="textareaVehicle" class="col-lg-12 form-control-label"><b>Texte de l'annonce :</b></label>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <textarea id="textareaVehicle" cols="150" rows="40"><?php
echo '
'.$structure->getStructureName().

($structure->getId() === 1 ? ' (réseau de professionnels de l\'automobile spécialisés dans les domaines de la réparations carrosserie, la vente de véhicules neufs ou d\'occasion et l\'entretien de véhicules pour les particuliers ainsi que pour les flottes professionnelles)' :
    ($structure->getId() === 3 ? ', agent Renault depuis 1975,' :
        '')).
' vous propose :

'.$vehicle->getBrand().' '.$vehicle->getModel().' '.$vehicle->getFinish().' '.$vehicle->getEngineSize().' '.$vehicle->getEngine().' '.$vehicle->getDynamicalPower().' ch

Mise en circulation : '.$vehicle->getModelDate()->format('m/Y').'
Kilométrage : '.number_format($vehicle->getMileage(), 0, '.', ' ').' km
Carburant : '.$vehicle->getFuel().'
Puissance réelle : '.$vehicle->getDynamicalPower().'ch
Puissance fiscale : '.$vehicle->getFiscalPower().'cv
Boîte de vitesse : '.$vehicle->getGearbox().'
Type : '.$vehicle->getBodywork().'
';
                    if($vehicle->getUsersAmount() == 0){
                        echo 'Véhicule neuf.';
                    }
                    else if($vehicle->getUsersAmount() == 1){
                        echo 'Véhicule de première main.';
                    }
                    else{
                        echo '';
                    }
                    echo '
Véhicule en parfait état de présentation (intérieur et extérieur).
'.($vehicle->getIsTechnicalInspectionOk() ? 'Contrôle technique OK.' : '').'
'.($vehicle->getIsMaintenanceLogOk() ? 'Carnet d\'entretien à jour.' : '').'
'.$vehicle->getSuppComments().'
Possibilités de reprise, nous consulter.
'.$vehicle->getFunding().'
'.$vehicle->getWarranty().'

Principaux équipements : ';
                    foreach($vehicle->getEquipmentsOrderedByName() as $value){
                        echo '
- '.trim($value);
                    }

echo 'Merci d\'indiquer la référence de l\'annonce : '.$vehicle->getReference().'

Merci par avance de nous contacter pour convenir d\'un rendez-vous.

D\'autres véhicules disponibles, neufs ou d\'occasion
Recherche personnalisée sur demande.

Pour plus d\'informations :
'.$structure->getStructureName().'
'.($structure->getId() === 1 ?
    '3 rue des Pommiers
68127 OBERENTZEN'
    : $structure->getAddress().'
'.$structure->getPostalCode().' '.$structure->getTown()).'
Tél : '.getPhoneNumber($structure->getPhone()).'
Mail : '.$structure->getEmail().'';
                    ?>
                </textarea>
            </div>
        </div>
    </div>
    <?php
}
catch(Exception $e){
    msgReturn_push(array(0, $e->getMessage()));
}