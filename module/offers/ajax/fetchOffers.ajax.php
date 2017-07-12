<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

$acceptNewVehicles = $_POST['acceptNewVehicles'] == 'true' ? true : false;
$acceptUsedVehicles = $_POST['acceptUsedVehicles'] == 'true' ? true : false;

$mode = $acceptNewVehicles ? ($acceptUsedVehicles ? 'all' : 'command') : ($acceptUsedVehicles ? 'stock' : 'none');
if(empty($_POST['askedState']))
    die();
$askedState = (int)$_POST['askedState'];

/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();

$isOwner = $user->isOwner();
$isAdmin = $user->isAdmin();
if(($askedState >= 5 && $askedState <= 8) && !$isAdmin){
    echo 'Impossible de voir ces états d\'offres.';
    die();
}

$db = databaseConnection();
$commandList = array();
if($mode == 'command' || $mode == 'all')
    $commandList = \Offers\OfferManager::fetchOffersList($db, $askedState, $user, $isOwner, false);
$stockList = array();
if($mode == 'stock' || $mode == 'all')
    $stockList = \Offers\StockOfferManager::fetchOffersList($db, $askedState, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), true);
$vat = \Prices\VatManager::fetchFrenchVat($db);
$db = null;

$printableList = array();
if(!empty($commandList) && !is_a($commandList, '\Exception')){
    foreach($commandList as $offer){
        $db = databaseConnection();
        $owner = \Users\UserManager::fetchUser($db, $offer->getOwnerId());
        $client = \Users\ClientManager::fetchClient($db, $offer->getClientId());
        $vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $offer->getVehicleId());
        $db = null;

        $dealer = $structure = $owner->getStructure();

        $finish = $vehicle->getFinish();
        $model = $finish->getModel();
        $brand = $model->getBrand();

        //Définition du prix pour décentralisation de l'affichage
        $optionalEquipments = $offer->getOptions();
        $packs = $offer->getPacks();
        $color = $offer->getColor();
        $rims = $offer->getRims();

        $postTaxesOptionPrice = 0;
        if(!empty($optionalEquipments)){
            foreach($optionalEquipments as $equipment){
                $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($equipment->getPrice(), $offer->getVatRate()), 2);
            }
        }
        if(!empty($packs)){
            foreach($packs as $pack){
                $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($pack->getPrice(), $offer->getVatRate()), 2);
            }
        }
        $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($color->getPrice(), $offer->getVatRate()), 2);
        $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($rims->getPrice(), $offer->getVatRate()), 2);
        $vatAmount = $offer->getVatRate();
        $buyingPrice = $offer->getVehiclePrice() - $offer->getFreightChargesToFrance();
        $changeRateToEuro = 1;
        $freightChargesToFrance = $offer->getFreightChargesToFrance();
        $marginAmount = $offer->getMarginAmount();
        $marginPercentage = 0;
        $managementFees = $offer->getManagementFees();
        $freightChargesInFrance = $offer->getFreightChargesInFrance();
        $dealerMargin = $offer->getDealerMargin();
        $packageProvision = $structure->getPackageProvision();
        $optionPrice = \Prices\VatManager::convertToPretax($postTaxesOptionPrice, $vatAmount);
        $registrationCardAmount = 0;
        $bonusPenalty = 0;

        $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
                                                 $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty);

        $offerNumber = $offer->getNumber();
        $offerDate = $offer->getCreationDate();
        $ownerName = $owner->getCivility().' '.$owner->getLastName().' '.$owner->getFirstName();
        $clientName = $client->getCivility().' '.$client->getLastName().' '.$client->getFirstName();
        $vehicleId = $offer->getVehicleId();
        $brandName = $brand->getName();
        $modelName = $model->getName();
        $finishName = $finish->getName();
        $engineSize = $vehicle->getEngineSize();
        $engine = $vehicle->getEngine()->getName();
        $dynamicalPower = $vehicle->getDynamicalPower();
        $price = $priceDetails->getPostTaxesAllIncludedPrice();
        $state = $offer->getState();
        $printableList[] = new \Offers\PrintableOffer(1, $offerNumber, $offerDate, $ownerName, $clientName, $vehicleId,
                                                      $brandName, $modelName, $finishName, $engineSize, $engine,
                                                      $dynamicalPower, $price, $state);
    }
}
if(!empty($stockList) && !is_a($stockList, '\Exception')){
    foreach($stockList as $offer){
        $owner = $offer->getUser();
        $client = $offer->getClient();
        $vehicle = $offer->getVehicle();

        $dealer = $structure = $owner->getStructure();

        $finish = $vehicle->getFinish();
        $model = $vehicle->getModel();
        $brand = $vehicle->getBrand();

        $vatAmount = $vat->getAmount();
        $buyingPrice = $offer->getVehiclePriceAmount();
        $changeRateToEuro = 1;
        $freightChargesToFrance = 0;
        $marginAmount = 0;
        $marginPercentage = 0;
        $managementFees = 0;
        $freightChargesInFrance = $offer->getFreightCharges();
        $dealerMargin = $offer->getDealerMargin();
        $packageProvision = $offer->getPackageProvision();
        $optionPrice = 0;
        $registrationCardAmount = 0;
        $bonusPenalty = 0;

        $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
                                                 $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty);

        $offerNumber = $offer->getNumber();
        $offerDate = $offer->getCreationDate();
        $ownerName = $owner->getCivility().' '.$owner->getLastName().' '.$owner->getFirstName();
        $clientName = $client->getCivility().' '.$client->getLastName().' '.$client->getFirstName();
        $vehicleId = $offer->getVehicleId();
        $brandName = $brand;
        $modelName = $model;
        $finishName = $finish;
        $engineSize = $vehicle->getEngineSize();
        $engine = $vehicle->getEngine();
        $dynamicalPower = $vehicle->getDynamicalPower();
        $price = $priceDetails->getPostTaxesAllIncludedPrice();
        $state = $offer->getState();
        $printableList[] = new \Offers\PrintableOffer(2, $offerNumber, $offerDate, $ownerName, $clientName, $vehicleId,
                                                      $brandName, $modelName, $finishName, $engineSize, $engine,
                                                      $dynamicalPower, $price, $state);
    }
}
if(empty($printableList)){
    echo 'Aucun résultat correspondant à votre recherche.';
}
?>
<table class="table sortTable table-striped table-hover text-small">
    <thead>
    <tr>
        <th>Type</th>
        <th>N° Offre</th>
        <?php
        if($isOwner):
            ?>
            <th>Conseiller</th>
            <?php
        endif;
        ?>
        <th>Client</th>
        <th>Véhicule</th>
        <th>Prix</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($printableList as $offer):
        /** @var \Offers\PrintableOffer $offer */
        $offer = $offer;
        ?>
        <tr class="hoverable" data-href="/commande-vehicules/offre-de-prix/visualiser/<?php echo $offer->getOfferReference(); ?>">
            <td><?php echo $offer->getType() == 1 ? 'Commande' : 'Stock/Arrivage'; ?></td>
            <td><?php echo $offer->getOfferReference(); ?></td>
            <?php
            if($isOwner):
                ?>
                <td><?php echo $offer->getSeller(); ?></td>
                <?php
            endif;
            ?>
            <td><?php echo $offer->getClient(); ?></td>
            <td>
                <?php echo $offer->getBrand().' '.$offer->getModel().' '.$offer->getFinish().' - '.$offer->getEngineSize().' '.$offer->getEngine().' '.$offer->getDynamicalPower(); ?>
            </td>
            <td><?php echo number_format($offer->getPrice(), 2, '.', ' ').' € TTC'; ?></td>
            <td>
                <?php
                if($offer->getType() == 1):
                    switch($offer->getState()):
                        case 1 :
                            ?>
                            <a href="/commande-vehicules/offre-de-prix/visualiser/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-eye" title="Visualiser"></a>
                            <a href="/commande-vehicules/offre-de-prix/imprimer/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-print" title="Imprimer" target="_blank"></a>
                            <a href="/commande-vehicules/catalogue/visualiser/<?php echo $offer->getVehicleId(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-pencil" title="Modifier"></a>
                            <?php /*    Ici, on enlève le bouton de transformation d'offre car cela nécessite de renseigner
                                        des infos (couleur/client) et les rechercher en base serait trop lourd si bcp d'offres

                            <a href="/commande-vehicules/offre-de-prix/transformer/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-success-outline btn-sm fa fa-check" title="Transformer en BDC"></a>
                            */ ?>
                            <a href="#" data-offerReference="<?php echo $offer->getOfferReference(); ?>"
                               class="btn-cancel-command-offer btn btn-danger-outline btn-sm fa fa-close" title="Annuler l'offre"></a>
                            <?php
                            break;
                        case 2 :
                            ?>
                            <a href="/commande-vehicules/bons-de-commande/visualiser/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-eye" title="Visualiser"></a>
                            <a href="/commande-vehicules/bons-de-commande/imprimer/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-print" title="Imprimer" target="_blank"></a>
                            <a href="/commande-vehicules/catalogue/visualiser/<?php echo $offer->getVehicleId(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-car" title="Visualiser le véhicule"></a>
                            <a href="#" data-offerReference="<?php echo $offer->getOfferReference(); ?>"
                               class="btn-validate-command-orderForm btn btn-success-outline btn-sm fa fa-check" title="Valider la signature"></a>
                            <a href="#" data-offerReference="<?php echo $offer->getOfferReference(); ?>"
                               class="btn-cancel-command-offer btn btn-danger-outline btn-sm fa fa-close" title="Annuler le Bon de Commande"></a>
                            <?php
                            break;
                        case 3 :
                            ?>
                            <a href="/commande-vehicules/bons-de-commande/visualiser/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-eye" title="Visualiser"></a>
                            <a href="/bon-a-uploader/imprimer/1<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-print" title="Imprimer" target="_blank"></a>
                            <?php
                            break;
                        case 4 :

                            break;
                        case 5 :

                            break;
                        case 6 :

                            break;
                        case 7 :

                            break;
                        case 8 :

                            break;
                        case 9 :

                            break;
                        case 10 :

                            break;
                        case 11 :

                            break;
                        default :
                            break;
                    endswitch;
                    ?>
                <?php
                else:
                    switch($offer->getState()):
                        case 1 :
                            ?>
                            <a href="/stock-arrivage/offre-de-prix/visualiser/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-eye" title="Visualiser"></a>
                            <a href="/stock-arrivage/offre-de-prix/imprimer/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-print" title="Imprimer" target="_blank"></a>
                            <a href="/stock-arrivage/catalogue/visualiser/<?php echo $offer->getVehicleId(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-pencil" title="Modifier"></a>
                            <!--<a href="#" data-offerReference="<?php echo $offer->getOfferReference(); ?>"
                               class="btn-cancel-stock-offer btn btn-danger-outline btn-sm fa fa-close" title="Annuler l'offre"></a>-->
                            <?php
                            break;
                        case 2 :
                            ?>
                            <a href="/stock-arrivage/offre-de-prix/visualiser/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-eye" title="Visualiser"></a>
                            <a href="/stock-arrivage/bon-de-commande/imprimer/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-print" title="Imprimer" target="_blank"></a>
                            <a href="/stock-arrivage/catalogue/visualiser/<?php echo $offer->getVehicleId(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-car" title="Visualiser le véhicule"></a>
                            <a href="#" data-offerReference="<?php echo $offer->getOfferReference(); ?>"
                               class="btn-validate-stock-orderForm btn btn-success-outline btn-sm fa fa-check" title="Valider la signature"></a>
                            <a href="#" data-offerReference="<?php echo $offer->getOfferReference(); ?>"
                               class="btn-cancel-stock-offer btn btn-danger-outline btn-sm fa fa-close" title="Annuler le Bon de Commande"></a>
                            <?php
                            break;
                        case 3 :
                            ?>
                            <a href="/commande-vehicules/bons-de-commande/visualiser/<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-eye" title="Visualiser"></a>
                            <a href="/bon-a-uploader/imprimer/1<?php echo $offer->getOfferReference(); ?>"
                               class="btn btn-primary-outline btn-sm fa fa-print" title="Imprimer" target="_blank"></a>
                            <?php
                            break;
                        case 4 :

                            break;
                        case 5 :

                            break;
                        case 6 :

                            break;
                        case 7 :

                            break;
                        case 8 :

                            break;
                        case 9 :

                            break;
                        case 10 :

                            break;
                        case 11 :

                            break;
                        default :
                            break;
                    endswitch;
                endif;
                ?>
            </td>
        </tr>
        <?php
    endforeach;
    ?>
    </tbody>
</table>
