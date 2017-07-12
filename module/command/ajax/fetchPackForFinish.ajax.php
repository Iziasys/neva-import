<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

$finishId = (int)$_POST['finishId'];

$db = databaseConnection();
$packsList = \Vehicle\FinishManager::fetchPacks($db, $finishId);

if(!is_a($packsList, '\Exception')):
    foreach($packsList as $key => $packInformation):
        /** @var \Vehicle\Pack $pack */
        $pack = $packInformation['pack'];
        /** @var \Prices\Price $price */
        $price = $packInformation['price'];
        if($price != null)
            $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
        ?>
        <tr>
            <td><?php echo $pack->getName(); ?></td>
            <?php if($price == null): ?>
            <td>Gratuit</td>
            <?php else: ?>
            <td><?php echo round($price->getPretaxBuyingPrice(), 2).' '.$currency->getSymbol(); ?></td>
            <?php endif; ?>
            <td>
                <a href="/commande-vehicules/pack/modifier/<?php echo $pack->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                <a href="/commande-vehicules/pack/supprimer/<?php echo $pack->getId(); ?>" class="btn btn-danger-outline btn-sm fa fa-trash"></a>
            </td>
        </tr>
    <?php
    endforeach;
endif;
$db = null;