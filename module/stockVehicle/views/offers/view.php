<?php
try{
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $structure = $user->getStructure();

    $db = databaseConnection();
    $offer = \Offers\StockOfferManager::fetchOfferByReference($db, $offerReference, true);
    $vat = \Prices\VatManager::fetchFrenchVat($db);
    $db = null;

    if(is_a($offer, '\Exception')){
        throw $offer;
    }

    //Récupération des informations de l'offre pour affichage
    $vehicle = $offer->getVehicle();

    $image1 = $vehicle->getImage1();
    $image2 = $vehicle->getImage2();
    $image3 = $vehicle->getImage3();
    $image4 = $vehicle->getImage4();

    $hasImage1 = ($vehicle->getImage1() != '' && is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage1())) ? true : false;
    $hasImage2 = ($vehicle->getImage2() != '' && is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage2())) ? true : false;
    $hasImage3 = ($vehicle->getImage3() != '' && is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage3())) ? true : false;
    $hasImage4 = ($vehicle->getImage4() != '' && is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/ressources/vehicleImages/VO/'.$vehicle->getImage4())) ? true : false;


    $buyingPrice = $offer->getVehiclePriceAmount();
    $changeRateToEuro = 1;
    $freightChargesToFrance = 0;
    $marginAmount = 0;
    $marginPercentage = 0;
    $managementFees = 0;
    $freightChargesInFrance = $offer->getFreightCharges();
    $dealerMargin = $offer->getDealerMargin();
    $packageProvision = $offer->getPackageProvision();
    $optionPrice = 0.0;
    $vatAmount = $vat->getAmount();
    $registrationCardAmount = 0;
    $bonusPenalty = 0;
    $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount,
                                             $marginPercentage, $managementFees, $freightChargesInFrance, $dealerMargin,
                                             $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount,
                                             $bonusPenalty);

    $client = $offer->getClient();
    $offerStructure = $offer->getStructure();
    //Si le nom/prénom et adresse client ne sont pas renseignés, on affichera un modal pour le faire avant de valider l'offre en BDC
    $invalidClientData = (empty($client->getLastName()) || empty($client->getFirstName()) || empty($client->getPostalAddress())
        || empty($client->getPostalCode()) || empty($client->getTown()));

    ?>

    <?php
    //Si il manque des données au client, popup de remplissage des informations
    if($invalidClientData):
        ?>
        <!-- MODAL D'AJOUT DES INFORMATIONS -->
        <div class="modal fade add-information-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h2 class="modal-title" id="exampleModalLabel">Ajout des informations manquantes</h2>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <form action="/stock-arrivage/offre-de-prix/transformer/<?php echo $offerReference; ?>"
                                  method="POST">
                                <div class="row">
                                    <div class="form-group">
                                        <label for="inputClientFirstName" class="col-lg-3 form-control-label">Prénom du client :</label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" id="inputClientFirstName"
                                                   name="transformOffer[firstName]"
                                                   value="<?php echo $client->getFirstName(); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputClientLastName" class="col-lg-3 form-control-label">Nom du client :</label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" id="inputClientLastName"
                                                   name="transformOffer[lastName]"
                                                   value="<?php echo $client->getLastName(); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="inputClientAddressNumber" class="col-lg-2 form-control-label">N° :</label>
                                        <div class="col-lg-2">
                                            <input type="text" class="form-control" id="inputClientAddressNumber"
                                                   name="transformOffer[addressNumber]" placeholder="15"
                                                   value="<?php echo $client->getAddressNumber(); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputClientAddressExtension" class="col-lg-2 form-control-label">Extension :</label>
                                        <div class="col-lg-2">
                                            <input type="text" class="form-control" id="inputClientAddressExtension"
                                                   name="transformOffer[extension]" placeholder="Bis"
                                                   value="<?php echo $client->getAddressExtension(); ?>" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputClientStreetType" class="col-lg-2 form-control-label">Type de voie :</label>
                                        <div class="col-lg-2">
                                            <input type="text" class="form-control" id="inputClientStreetType"
                                                   name="transformOffer[streetType]" placeholder="Rue"
                                                   value="<?php echo $client->getStreetType(); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="inputClientAddressWording" class="col-lg-2 form-control-label">Libellé :</label>
                                        <div class="col-lg-10">
                                            <input type="text" class="form-control" id="inputClientAddressWording"
                                                   name="transformOffer[wording]" placeholder="Général de Gaulle"
                                                   value="<?php echo $client->getAddressWording(); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="inputClientPostalCode" class="col-lg-2 form-control-label">Code Postal :</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="inputClientPostalCode"
                                                   name="transformOffer[postalCode]" placeholder="75000"
                                                   value="<?php echo $client->getPostalCode(); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputClientTown" class="col-lg-2 form-control-label">Ville :</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="inputClientTown"
                                                   name="transformOffer[town]" placeholder="Paris"
                                                   value="<?php echo $client->getTown(); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-lg-4 col-lg-offset-5">
                                        <button type="submit" class="btn btn-primary btn-principalColor formManager">
                                            Valider
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL D'AJOUT DES INFORMATIONS -->
        <?php
    endif;
    ?>

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
        <h2 class="page-header text-lg-center">
            Offre de prix N°<?php echo $offer->getReference(); ?>
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
                foreach($vehicle->getEquipments() as $equipment):
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
        <div class="card card-block card-border-blue col-lg-5 col-lg-offset-2">
            <h4 class="card-title text-primary">Client :</h4>
            <div class="col-lg-12"><?php echo $client->getCivility().' '.$client->getLastName().' '.$client->getFirstName(); ?></div>
            <div class="col-lg-12"><?php echo $client->getStreetAddress(); ?></div>
            <div class="col-lg-12"><?php echo $client->getPostalCode().' '.$client->getTown(); ?></div>
            <div class="col-lg-12">Tél : <?php echo getPhoneNumber($client->getPhone()); ?></div>
        </div>
        <div class="row"></div>
        <div class="card card-block card-inverse card-primary card-border-white col-lg-5">
            <h4 class="card-title">Tarifs :</h4>
            <div class="col-lg-12 text-white">
                Prix de vente : <span class="vehiclePostTaxesPrice"><?php echo number_format($priceDetails->getPostTaxesAllIncludedPrice(), 2, '.', ' '); ?></span>
                € TTC
            </div>
        </div>
        <div class="row"></div>
        <br>
        <div class="row">
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <?php
                    if($offer->getState() == 1):
                        ?>
                        <a href="/stock-arrivage/offre-de-prix/imprimer/<?php echo $offerReference; ?>" target="_blank"
                           class="btn btn-primary fit-to-container">
                            Imprimer
                        </a>
                        <?php
                    endif;
                    ?>
                    <?php
                    if($offer->getState() == 2):
                        ?>
                        <a href="/stock-arrivage/bon-de-commande/imprimer/<?php echo $offerReference; ?>" target="_blank"
                           class="btn btn-primary fit-to-container">
                            Imprimer
                        </a>
                        <?php
                    endif;
                    ?>
                </div>
                <?php
                if(!$vehicle->getReserved()):
                    ?>
                    <div class="col-lg-5 col-lg-offset-1">
                        <a href="/stock-arrivage/offre-de-prix/transformer/<?php echo $offerReference; ?>"
                           class="btn btn-primary fit-to-container"
                            <?php if($invalidClientData):
                                echo 'data-toggle="modal" data-target=".add-information-modal-lg"';
                            endif; ?>>Transformer en Bon de Commande
                        </a>
                    </div>
                    <?php
                endif;
                ?>
            </div>
        </div>
        <?php
        if($offer->getState() == 2 && $vehicle->getReserved() && $vehicle->getReservedBy() == $structure->getId() && $vehicle->getSold() == 0):
            ?>
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-3">
                        <button class="btn btn-success fit-to-container">Valider la vente</button>
                    </div>
                </div>
            </div>
            <?php
        endif;
        ?>
    </div>
    <div class="row"></div>
    <br><br><br>
    <?php
}
catch(Exception $e){
    msgReturn_push(array(0, $e->getMessage()));
}

function printVehicleDetail(string $title, string $content){
    ?>
    <div class="col-lg-3 col-xl-2"><?php echo $title; ?> :</div>
    <div class="col-lg-3 col-xl-2"><b><?php echo $content; ?></b></div>
    <?php
}