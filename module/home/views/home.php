<?php
try{
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $structure = $user->getStructure();

    $isAdmin = $user->isAdmin();
    $isOwner = $user->isOwner();

    $db = databaseConnection();
    //Gestion des Offres de prix
    $validPriceOffers = \Offers\OfferManager::countOffers($db, 1, $user, $isOwner, $isAdmin)
        + \Offers\StockOfferManager::countOffers($db, 1, $structure->getId(), ($user->isOwner() ? 0 : $user->getId()), $isAdmin);

    //Gestion des BDC à signer par le client
    $validOrderForms = \Offers\OfferManager::countOffers($db, 2, $user, $isOwner, $isAdmin)
        + \Offers\StockOfferManager::countOffers($db, 2, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);

    //Gestion des BDC à uploader
    $orderFormsToUpload = \Offers\OfferManager::countOffers($db, 3, $user, $isOwner, $isAdmin)
        + \Offers\StockOfferManager::countOffers($db, 3, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);

    //Gestion des BDC en attente de validation
    $orderFormsWaitingForValidation = \Offers\OfferManager::countOffers($db, 4, $user, $isOwner, $isAdmin)
        + \Offers\StockOfferManager::countOffers($db, 4, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);
    if($isAdmin){
        $providerOrders = \Offers\OfferManager::countOffers($db, 5, $user, $isOwner, $isAdmin)
            + \Offers\StockOfferManager::countOffers($db, 5, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);;
        $factoryOrders = \Offers\OfferManager::countOffers($db, 6, $user, $isOwner, $isAdmin)
            + \Offers\StockOfferManager::countOffers($db, 6, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);;
        $comingFromFactory = \Offers\OfferManager::countOffers($db, 7, $user, $isOwner, $isAdmin)
            + \Offers\StockOfferManager::countOffers($db, 7, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);;
        $comingFromProvider = \Offers\OfferManager::countOffers($db, 8, $user, $isOwner, $isAdmin)
            + \Offers\StockOfferManager::countOffers($db, 8, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);;
    }
    $parkedVehicle = \Offers\OfferManager::countOffers($db, 9, $user, $isOwner, $isAdmin)
        + \Offers\StockOfferManager::countOffers($db, 9, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);;
    $goingToDealer = \Offers\OfferManager::countOffers($db, 10, $user, $isOwner, $isAdmin)
        + \Offers\StockOfferManager::countOffers($db, 10, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);;
    $deliveredVehicle = \Offers\OfferManager::countOffers($db, 11, $user, $isOwner, $isAdmin)
        + \Offers\StockOfferManager::countOffers($db, 11, $structure->getId(), $user->isOwner() ? 0 : $user->getId(), $isAdmin);;
    $db = null;

    ?>

    <h2 class="page-header text-lg-center">Tableau de bord</h2>
    <div class="container">
        <div class="col-lg-12">
            <ul class="list-group">
                <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/offre-de-prix">
                    <span class="text-medium label label-primary label-pill pull-xs-right">
                        <?php echo $validPriceOffers; ?>
                    </span>
                    Offres de Prix en cours de validité
                </li>
                <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/bon-de-commande">
                    <span class="text-medium label label-primary label-pill pull-xs-right">
                        <?php echo $validOrderForms; ?>
                    </span>
                    Bons de Commande en cours de validité
                </li>
                <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/bon-a-uploader">
                    <span class="text-medium label label-primary label-pill pull-xs-right">
                        <?php echo $orderFormsToUpload; ?>
                    </span>
                    Bons de Commande à uploader
                </li>
                <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/bon-a-valider">
                    <span class="text-medium label label-primary label-pill pull-xs-right">
                        <?php echo $orderFormsWaitingForValidation; ?>
                    </span>
                    Bons de Commande en attente de validation
                </li>
                <?php
                if($isAdmin):
                    ?>
                    <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/commandes-fournisseur">
                        <span class="text-medium label label-primary label-pill pull-xs-right">
                            <?php echo $providerOrders; ?>
                        </span>
                        Commandes fournisseur passées
                    </li>
                    <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/commandes-usine">
                        <span class="text-medium label label-primary label-pill pull-xs-right">
                            <?php echo $factoryOrders; ?>
                        </span>
                        Commandes usine passées
                    </li>
                    <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/arrivages-usine">
                        <span class="text-medium label label-primary label-pill pull-xs-right">
                            <?php echo $comingFromFactory; ?>
                        </span>
                        Arrivage depuis usine
                    </li>
                    <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/arrivages-fournisseur">
                        <span class="text-medium label label-primary label-pill pull-xs-right">
                            <?php echo $comingFromProvider; ?>
                        </span>
                        Arrivage depuis fournisseur
                    </li>
                    <?php
                endif;
                ?>
                <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/vehicules-en-stock">
                    <span class="text-medium label label-primary label-pill pull-xs-right">
                        <?php echo $parkedVehicle; ?>
                    </span>
                    Véhicules en stock
                </li>
                <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/vehicules-en-arrivage">
                    <span class="text-medium label label-primary label-pill pull-xs-right">
                        <?php echo $goingToDealer; ?>
                    </span>
                    Véhicules en arrivage
                </li>
                <li class="list-group-item hoverable-info hoverable-inverse clickable board-item" data-href="/vehicules-livres">
                    <span class="text-medium label label-primary label-pill pull-xs-right">
                        <?php echo $deliveredVehicle; ?>
                    </span>
                    Véhicules livrés
                </li>
            </ul>
        </div>
    </div>
    <?php
}
catch(Exception $e){
    msgReturn_push(array(0, $e->getMessage()));
}