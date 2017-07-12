<?php
$db = databaseConnection();
$dealersList = \Prices\DealerManager::fetchDealersList($db);
$db = null;
?>

<div class="container">
    <h3 class="page-header">Visualisation des concessionnaires</h3>

    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Pays</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($dealersList as $dealer):
            $db = databaseConnection();
            $country = \Prices\CountryManager::fetchCountry($db, $dealer->getCountryId());
            $db = null;
            ?>
            <tr>
                <td><?php echo $dealer->getName(); ?></td>
                <td><?php echo $country->getName().' ('.$country->getAbbreviation().')'; ?></td>
                <td>
                    <a href="/fournisseurs/modifier/<?php echo $dealer->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                    <a href="/fournisseurs/supprimer/<?php echo $dealer->getId(); ?>" class="btn btn-danger-outline btn-sm fa fa-times"></a>
                </td>
            </tr>
            <?php
        endforeach;
        ?>
        </tbody>
    </table>
</div>
