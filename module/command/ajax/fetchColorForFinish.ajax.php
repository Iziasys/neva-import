<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

$finishId = (int)$_POST['finishId'];

$db = databaseConnection();
$colorsList = \Vehicle\FinishManager::fetchExternalColor($db, $finishId);

if(!is_a($colorsList, '\Exception')):
    foreach($colorsList as $key => $colorInformation):
        /** @var \Vehicle\ExternalColor $color */
        $color = $colorInformation['color'];
        /** @var \Prices\Price $price */
        $price = $colorInformation['price'];
        if($price != null)
            $currency = \Prices\CurrencyManager::fetchCurrency($db, $price->getCurrencyId());
        ?>
        <tr>
            <td><?php echo $color->getName(); ?></td>
            <td><?php echo $color->getDetails(); ?></td>
            <td><?php echo $color->getBiTone() ? 'Oui' : 'Non'; ?></td>
            <?php if($price == null): ?>
            <td>De série</td>
            <?php else: ?>
            <td><?php echo round($price->getPretaxBuyingPrice(), 2).' '.$currency->getSymbol(); ?></td>
            <?php endif; ?>
            <td>
                <a href="/commande-vehicules/couleur/modifier/<?php echo $key; ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                <a href="/commande-vehicules/couleur/supprimer/<?php echo $key; ?>" class="btn btn-danger-outline btn-sm fa fa-trash"></a>
            </td>
        </tr>
    <?php
    endforeach;
endif;
$db = null;