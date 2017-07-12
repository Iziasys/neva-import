<?php
$db = databaseConnection();
$currenciesList = \Prices\CurrencyManager::fetchCurrenciesList($db);
$db = null;
?>

<div class="container">
    <h3 class="page-header">Visualisation des devises</h3>

    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Abbréviation</th>
            <th>Symbole</th>
            <th>Taux de change</th>
            <th>Dernière MàJ</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($currenciesList as $currency):
            ?>
            <tr>
                <td><?php echo $currency->getCurrency(); ?></td>
                <td><?php echo $currency->getAbbreviation(); ?></td>
                <td><?php echo $currency->getSymbol(); ?></td>
                <td><?php echo '1'.$currency->getSymbol().' = '.$currency->getExchangeRate()->getRateToEuro().'€'; ?></td>
                <td><?php echo $currency->getExchangeRate()->getRateDate()->format('d/m/Y H:i:s'); ?></td>
                <td>
                    <a href="/devises/modifier/<?php echo $currency->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                    <a href="/devises/supprimer/<?php echo $currency->getId(); ?>" class="btn btn-danger-outline btn-sm fa fa-times"></a>
                </td>
            </tr>
            <?php
        endforeach;
        ?>
        </tbody>
    </table>
</div>
