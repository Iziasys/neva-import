<?php
$db = databaseConnection();
$countriesList = \Prices\CountryManager::fetchCountriesList($db);
$db = null;
?>

<div class="container">
    <h3 class="page-header">Visualisation des pays</h3>

    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Abbréviation</th>
            <th>Devise</th>
            <th>Taux TVA</th>
            <th>Frais de transport</th>
            <th>Dernière MàJ</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($countriesList as $country):
            ?>
            <tr>
                <td><?php echo $country->getName(); ?></td>
                <td><?php echo $country->getAbbreviation(); ?></td>
                <td><?php echo $country->getCurrency()->getCurrency().' ( '.$country->getCurrency()->getSymbol().' )'; ?></td>
                <td><?php echo $country->getVat()->getAmount().'%'; ?></td>
                <td><?php echo $country->getFreightCharges()->getAmount().' € HT'; ?></td>
                <td><?php echo $country->getFreightCharges()->getDate()->format('d/m/Y H:i:s'); ?></td>
                <td>
                    <a href="/pays/modifier/<?php echo $country->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                    <a href="/pays/supprimer/<?php echo $country->getId(); ?>" class="btn btn-danger-outline btn-sm fa fa-times"></a>
                </td>
            </tr>
            <?php
        endforeach;
        ?>
        </tbody>
    </table>
</div>
