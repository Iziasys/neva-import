<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

$finishId = (int)$_POST['finishId'];

$db = databaseConnection();
$rimsList = \Vehicle\FinishManager::fetchRims($db, $finishId);

if(!is_a($rimsList, '\Exception')):
    foreach($rimsList as $key => $rimInformation):
        /** @var \Vehicle\RimModel $rim */
        $rim = $rimInformation['rim'];
        /** @var \Prices\Price $price */
        $price = $rimInformation['price'];
        if($price != null)
            $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
        ?>
        <tr>
            <td><?php echo $rim->getName(); ?></td>
            <td><?php echo $rim->getRimType(); ?></td>
            <td><?php echo $rim->getFrontDiameter(); ?>"</td>
            <?php if($price == null): ?>
            <td>De série</td>
            <?php else: ?>
            <td><?php echo round($price->getPretaxBuyingPrice(), 2).' '.$currency->getSymbol(); ?></td>
            <?php endif; ?>
            <td>
                <a href="/commande-vehicules/jantes/modifier/<?php echo $key; ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                <a href="/commande-vehicules/jantes/supprimer/<?php echo $key; ?>" class="btn btn-danger-outline btn-sm fa fa-trash"></a>
            </td>
        </tr>
    <?php
    endforeach;
endif;
$db = null;