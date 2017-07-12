<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();
$pretaxDefaultMargin = $structure->getDefaultMargin();
$dealer = $structure;
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
$vehicleId = (int)$_GET['vehicleId'];

$db = databaseConnection();
$vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
if(is_a($vehicle, '\Exception')){
    msgReturn_push(array(0, $vehicle->getMessage()));
}
$offerStructure = \Users\StructureManager::fetchStructure($db, $vehicle->getStructureId());
if(is_a($offerStructure, '\Exception')){
    msgReturn_push(array(0, $offerStructure->getMessage()));
}
$vat = \Prices\VatManager::fetchFrenchVat($db);
$freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $clientDepartment);
$structureMargin = \Vehicle\VehicleInStockManager::fetchStructureMargin($db, $vehicleId, $structure->getId());
$clients = \Users\ClientManager::fetchClientsList($db, $dealer->getId(), $dealer->getIsPrimary());
$db = null;

$image1 = $vehicle->getImage1();
$image2 = $vehicle->getImage2();
$image3 = $vehicle->getImage3();
$image4 = $vehicle->getImage4();

$hasImage1 = ($vehicle->getImage1() != '' && is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage1())) ? true : false;
$hasImage2 = ($vehicle->getImage2() != '' && is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage2())) ? true : false;
$hasImage3 = ($vehicle->getImage3() != '' && is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage3())) ? true : false;
$hasImage4 = ($vehicle->getImage4() != '' && is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage4())) ? true : false;


$buyingPrice = \Prices\VatManager::convertToPretax($vehicle->getPrice(), $vat->getAmount());
$changeRateToEuro = 1;
$freightChargesToFrance = 0;
$marginAmount = 0;
$marginPercentage = 0;
$managementFees = 0;
if($structure->getFreightCharges() === null){
    $freightChargesInFrance = $freightCharges->getAmount();
}
else{
    $freightChargesInFrance = $structure->getFreightCharges();
}
$dealerMargin = is_a($structureMargin, '\Exception') ? $structure->getDefaultMargin() : $structureMargin;
$packageProvision = $structure->getPackageProvision();
$optionPrice = 0.0;
$vatAmount = $vat->getAmount();
$registrationCardAmount = 0;
$bonusPenalty = 0;
$priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount,
                                         $marginPercentage, $managementFees, $freightChargesInFrance, $dealerMargin,
                                         $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount,
                                         $bonusPenalty);


?>
    <!-- MODAL DE CHOIX DU CLIENT -->
    <div class="modal fade select-client-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h2 class="modal-title" id="exampleModalLabel">Choix du client</h2>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="col-lg-6">
                            <h4 class="page-header"><label for="inputSelectClient">Sélectionnez le dans la base :</label></h4>
                            <select id="inputSelectClient" class="form-control">
                                <option value="0">Sélectionnez un client</option>
                                <?php
                                foreach($clients as $cli):
                                    if($cli->getIsSociety()):
                                        ?>
                                        <option value="<?php echo $cli->getId(); ?>"><?php echo $cli->getName(); ?></option>
                                        <?php
                                    else:
                                        ?>
                                        <option value="<?php echo $cli->getId(); ?>" <?php echo $cli->getId() == $clientId ? 'selected' : ''; ?>>
                                            <?php echo $cli->getFirstName().' '.$cli->getLastName(); ?>
                                        </option>
                                        <?php
                                    endif;
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-6 col-with-left-border">
                            <h4 class="page-header">Ou créez le maintenant :<br>
                                <small>Vous le complèterez plus tard</small></h4>
                            <form action="#" id="formCreateClient" method="post">
                                <input type="hidden" id="createClient_structureId" value="<?php echo $user->getStructureId(); ?>">
                                <div class="form-group">
                                    <div class="col-lg-12">
                                        <label class="radio-inline">
                                            <input type="radio" class="radio-for-isSociety" id="createClient_isSociety"
                                                   name="createClientSociety" value="0" checked> Client particulier
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" class="radio-for-isSociety" id="createClient_isSociety"
                                                   name="createClientSociety" value="1"> Client société
                                        </label>
                                    </div>
                                </div>
                                <div class="rowForSociety">
                                    <div class="form-group">
                                        <label for="createClient_societyName" class="form-control-label">Nom de la société :</label>
                                        <input type="text" class="form-control" id="createClient_societyName">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Cvilité :</label>
                                    <label class="radio-inline">
                                        <input type="radio" id="createClient_civility" name="createClientCivility" value="Mme"> Mme.
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" id="createClient_civility" name="createClientCivility" value="M" checked> M.
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label for="createClient_lastName" class="form-control-label">Nom :</label>
                                    <input type="text" class="form-control" id="createClient_lastName" required>
                                </div>
                                <div class="form-group">
                                    <label for="createClient_firstName" class="form-control-label">Prénom :</label>
                                    <input type="text" class="form-control" id="createClient_firstName">
                                </div>
                                <div class="form-group">
                                    <label for="createClient_email" class="form-control-label">E-mail :</label>
                                    <input type="text" class="form-control" id="createClient_email">
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Accepte les offres mail :</label>
                                    <label class="radio-inline">
                                        <input type="radio" id="createClient_acceptOffers" name="createClientAcceptOffers" value="0" required> Non
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" id="createClient_acceptOffers" name="createClientAcceptOffers" value="1" required> Oui
                                    </label>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-4 col-lg-offset-5">
                            <button type="button" class="btn btn-primary btn-principalColor formManager" id="btnSelectClient">
                                Valider
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL DE CHOIX DU CLIENT -->
<h2 class="page-header text-lg-center">
    <?php echo $vehicle->getBrand().' '.$vehicle->getModel().' '.$vehicle->getFinish(); ?>
</h2>
    <!-- CAROUSEL DES PHOTOS DU VEHICULE-->
    <div id="carousel-vehicle-images" class="carousel slide col-lg-8 col-lg-offset-2" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php
            if($hasImage1):
                echo '<li data-target="#carousel-vehicle-images" data-slide-to="0" class="active"></li>';
            endif;
            if($hasImage2):
                echo '<li data-target="#carousel-vehicle-images" data-slide-to="1"></li>';
            endif;
            if($hasImage3):
                echo '<li data-target="#carousel-vehicle-images" data-slide-to="2"></li>';
            endif;
            if($hasImage4):
                echo '<li data-target="#carousel-vehicle-images" data-slide-to="3"></li>';
            endif;
            ?>
        </ol>
        <div class="carousel-inner" role="listbox">
            <?php
            if($hasImage1):
                echo '  <div class="carousel-item active">
                        <img data-src="'.getAppPath().'/ressources/vehicleImages/VO/'.$image1.'"
                        src="'.getAppPath().'/ressources/vehicleImages/VO/'.$image1.'"
                        alt="Photo 1" class="img-responsive center-block">
                    </div>
            ';
            endif;
            if($hasImage2):
                echo '  <div class="carousel-item">
                        <img data-src="'.getAppPath().'/ressources/vehicleImages/VO/'.$image2.'"
                        src="'.getAppPath().'/ressources/vehicleImages/VO/'.$image2.'"
                        alt="Photo 2" class="img-responsive center-block">
                    </div>
            ';
            endif;
            if($hasImage3):
                echo '  <div class="carousel-item">
                        <img data-src="'.getAppPath().'/ressources/vehicleImages/VO/'.$image3.'"
                        src="'.getAppPath().'/ressources/vehicleImages/VO/'.$image3.'"
                        alt="Photo 3" class="img-responsive center-block">
                    </div>
            ';
            endif;
            if($hasImage4):
                echo '  <div class="carousel-item">
                        <img data-src="'.getAppPath().'/ressources/vehicleImages/VO/'.$image4.'"
                        src="'.getAppPath().'/ressources/vehicleImages/VO/'.$image4.'"
                        alt="Photo 4" class="img-responsive center-block">
                    </div>
            ';
            endif;
            ?>
        </div>
        <a class="left carousel-control" href="#carousel-vehicle-images" role="button" data-slide="prev">
            <span class="icon-prev" aria-hidden="true"></span>
            <span class="sr-only">Précédent</span>
        </a>
        <a class="right carousel-control" href="#carousel-vehicle-images" role="button" data-slide="next">
            <span class="icon-next" aria-hidden="true"></span>
            <span class="sr-only">Suivant</span>
        </a>
    </div>
    <!-- CAROUSEL DES PHOTOS DU VEHICULE-->

<div class="row"></div>
<br><br>
<div class="container" style="margin-top: 20px;">
    <h2 class="page-header text-lg-center text-danger">
        Etat du véhicule :
        <?php
        if($vehicle->getReserved()):
            echo 'Optionné';
        else:
            if($vehicle->getIsArriving()):
                echo 'En arrivage';
            else:
                echo 'En stock';
            endif;
        endif;
        ?>
    </h2>
    <div class="card card-block card-border-blue col-lg-12">
        <h4 class="card-title text-primary">Information sur le véhicule :</h4>
        <div class="card-text">
            <?php
            printVehicleDetail('Marque', $vehicle->getBrand());
            printVehicleDetail('Modèle', $vehicle->getModel());
            if(!empty($vehicle->getFinish())):
                printVehicleDetail('Finition', $vehicle->getFinish());
            endif;
            printVehicleDetail('Cylindree', $vehicle->getEngineSize());
            printVehicleDetail('Motorisation', $vehicle->getEngine());
            printVehicleDetail('Puissance Dyn.', $vehicle->getDynamicalPower().' ch');
            printVehicleDetail('Année Modèle', $vehicle->getModelDate()->format('m/Y'));
            printVehicleDetail('Kilométrage', $vehicle->getMileage());
            if(!empty($vehicle->getFuel())):
                printVehicleDetail('Carburant', $vehicle->getFuel());
            endif;
            printVehicleDetail('Boite de vitesse', $vehicle->getGearbox());
            if(!empty($vehicle->getFiscalPower())):
                printVehicleDetail('Puissance fisc', $vehicle->getFiscalPower().' cv');
            endif;
            if(!empty($vehicle->getCo2())):
                printVehicleDetail('Co2', $vehicle->getCo2().' g/Km');
            endif;
            if(!empty($vehicle->getBodywork())):
                printVehicleDetail('Carrosserie', $vehicle->getBodywork());
            endif;
            if(!empty($vehicle->getTransmission())):
                printVehicleDetail('Transmission', $vehicle->getTransmission());
            endif;
            if(!empty($vehicle->getExternalColor())):
                printVehicleDetail('Couleur extérieur', $vehicle->getExternalColor());
            endif;
            if(!empty($vehicle->getReference())):
                printVehicleDetail('Réf. de l\'offre', $vehicle->getReference());
            endif;
            ?>
        </div>
    </div>
    <br>
    <div class="card card-block card-border-blue col-lg-12">
        <h4 class="card-title text-primary">Principaux équipements :</h4>
        <div id="blocEquipments">
            <?php
            foreach($vehicle->getEquipmentsOrderedByName() as $equipment):
                ?>
                <div class="col-lg-3" style="margin-bottom:5px;"><?php echo $equipment; ?></div>
                <?php
            endforeach;
            ?>
        </div>
    </div>
    <br>
    <div class="card card-block card-border-blue col-lg-12">
        <h4 class="card-title text-primary">Informations supplémentaires :</h4>
        <?php
        if(!empty($vehicle->getUsersAmount())):
            echo '<div class="col-lg-12"><b>Nombre de propriétaires : </b>'.$vehicle->getUsersAmount().'</div>';
        endif;
        if(!empty($vehicle->getSuppComments())):
            echo '<div class="col-lg-12">'.$vehicle->getSuppComments().'</div>';
        endif;
        if(!$vehicle->getHasAccident()):
            echo '<div class="col-lg-4"><b>Véhicule jamais accidenté</b></div>';
        endif;
        if($vehicle->getIsMaintenanceLogOk()):
            echo '<div class="col-lg-4"><b>Carnet d\'entretien :</b> à jour</div>';
        endif;
        if($vehicle->getIsTechnicalInspectionOk()):
            echo '<div class="col-lg-4"><b>Contrôle technique : </b>OK</div>';
        endif;
        if(!empty($vehicle->getFunding())):
            echo '<div class="col-lg-12"><b>Financement : </b>'.$vehicle->getFunding().'</div>';
        endif;
        if(!empty($vehicle->getWarranty())):
            echo '<div class="col-lg-12"><b>Garantie : </b>'.$vehicle->getWarranty().'</div>';
        endif;
        ?>
    </div>
    <br>
    <div class="card card-block card-border-blue col-lg-5">
        <h4 class="card-title text-primary">Contact :</h4>
        <div class="col-lg-12"><?php echo $offerStructure->getStructureName(); ?></div>
        <div class="col-lg-12"><?php echo $offerStructure->getAddress(); ?></div>
        <div class="col-lg-12"><?php echo $offerStructure->getPostalCode().' '.$offerStructure->getTown(); ?></div>
        <div class="col-lg-12">Tél : <?php echo getPhoneNumber($offerStructure->getPhone()); ?></div>
    </div>
    <div class="card card-block card-inverse card-primary card-border-white col-lg-5 col-lg-offset-2">
        <h4 class="card-title">Tarifs :</h4>
        <div class="col-lg-12 text-white">
            Prix de vente : <span class="vehiclePostTaxesPrice"><?php echo number_format($priceDetails->getPostTaxesDealerSellingPrice(), 2, '.', ' '); ?></span>
            € TTC
        </div>
    </div>
    <div class="row"></div>
    <br><br>
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-3">
                <button class="btn btn-primary fit-to-container" id="bntCalc">Calcul</button>
            </div>
            <div class="col-lg-6 not-displayed" id="block-calc">
                <div class="card card-block">
                    <div class="card-title">Calcul du prix</div>
                    <div class="card-text">
                        <div id="receiverDefaultMargin" class="not-displayed"><?php echo $priceDetails->getDealerMargin(); ?></div>
                        <div id="receiverBasicPrice" class="not-displayed"><?php echo $priceDetails->getPretaxDealerBuyingPrice(); ?></div>
                        <div id="receiverPackageProvision" class="not-displayed">0</div>
                        <input type="text" id="marginAmount" class="customized-jquery-ui-input" name="createOffer[dealerMargin]"
                               data-vat="<?php echo $vat->getAmount(); ?>" data-vehicleId="<?php echo $vehicleId; ?>" readonly>
                        <div id="calcSlider"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-3">
                <a href="/stock-arrivage/catalogue/imprimer/<?php echo $vehicle->getId(); ?>" target="_blank"
                   class="btn btn-primary fit-to-container">
                    Imprimer
                </a>
            </div>
            <div class="col-lg-3 col-lg-offset-1">
                <form action="/stock-arrivage/offre-de-prix/creer" id="formCreateOffer" method="POST">
                    <input type="hidden" name="createOffer[vehicleId]" value="<?php echo $vehicleId; ?>">
                </form>
                <button class="btn btn-primary fit-to-container"
                        data-toggle="modal" data-target=".select-client-modal-lg">
                    Créer une Offre de Prix
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row"></div>
<br><br><br>
<?php
function printVehicleDetail(string $title, string $content){
    ?>
    <div class="col-lg-3 col-xl-2"><?php echo $title; ?> :</div>
    <div class="col-lg-3 col-xl-2"><b><?php echo $content; ?></b></div>
    <?php
}