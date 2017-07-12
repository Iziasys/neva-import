<?php
//Récupération des offres disponibles
/** @var \Users\User $user */
$user = $_SESSION['user'];

$isOwner = $user->isOwner();
$isAdmin = $user->isAdmin();

$db = databaseConnection();
$offersList = \Offers\OfferManager::fetchOffersList($db, $user, $isOwner, $isAdmin);
$db = null;
?>
<h2 class="page-header text-lg-center">Liste des offres disponibles</h2>
<table class="table sortTable table-striped table-hover text-small">
    <thead>
    <tr>
        <th>N° Offre</th>
        <?php
        if($isAdmin):
            ?>
            <th>Distributeur</th>
            <?php
        endif;
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
    foreach($offersList as $offer):
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

        $offerReference = $offer->getCreationDate()->format('Ymd').$offer->getNumber();
        ?>
        <tr class="hoverable" data-href="/commande-vehicules/offre-de-prix/visualiser/<?php echo $offerReference; ?>">
            <td><?php echo $offerReference; ?></td>
            <?php
            if($isAdmin):
                ?>
                <td><?php echo $owner->getStructure()->getStructureName(); ?></td>
                <?php
            endif;
            if($isOwner):
                ?>
                <td><?php echo $owner->getCivility().' '.$owner->getLastName().' '.$owner->getFirstName(); ?></td>
                <?php
            endif;
            ?>
            <td><?php echo $client->getCivility().' '.$client->getLastName().' '.$client->getFirstName(); ?></td>
            <td><?php echo $brand->getName().' '.$model->getName().' '.$finish->getName().' - '.$vehicle->getEngineSize().' '.$vehicle->getEngine()->getName().' '.$vehicle->getDynamicalPower(); ?></td>
            <td><?php echo number_format($priceDetails->getPostTaxesAllIncludedPrice(), 2, '.', ' ').' € TTC'; ?></td>
            <td>
                <a href="/commande-vehicules/offre-de-prix/visualiser/<?php echo $offerReference; ?>" class="btn btn-primary-outline btn-sm fa fa-eye" title="Visualiser"></a>
                <a href="/commande-vehicules/offre-de-prix/imprimer/<?php echo $offerReference; ?>" class="btn btn-primary-outline btn-sm fa fa-print" title="Imprimer" target="_blank"></a>
                <a href="/commande-vehicules/catalogue/visualiser/<?php echo $vehicle->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil" title="Modifier"></a>
                <a href="/commande-vehicules/offre-de-prix/transformer/<?php echo $offerReference; ?>" class="btn btn-success-outline btn-sm fa fa-check" title="Transformer en BDC"></a>
            </td>
        </tr>
        <?php
    endforeach;
    ?>
    </tbody>
</table>
