<?php
/**
 * Ecran liste des offres disponibles
 */
if(!empty($mode))
    $mode = (string)$mode;
if(!empty($state))
    $state = (int)$state;

$title = '';
switch($state){
    case 1 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    case 2 :
        $title = 'Liste des Bons de Commande disponibles';
        break;
    case 3 :
        $title = 'Bons de Commande à uploader';
        break;
    case 4 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    case 5 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    case 6 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    case 7 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    case 8 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    case 9 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    case 10 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    case 11 :
        $title = 'Liste des Offres de Prix disponibles';
        break;
    default :
        break;
}
?>
<h2 class="page-header text-lg-center"><?php echo $title; ?></h2>
<div class="col-lg-10 col-lg-offset-1">
    <input type="hidden" id="askedState" value="<?php echo $state; ?>">
    <div class="row">
        <div class="col-lg-4">
            <label class="checkbox-inline">
                <input type="checkbox" id="acceptNewVehicles" <?php echo $mode != 'stock' ? 'checked' : ''; ?>> Véhicules Neufs
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" id="acceptUsedVehicles" <?php echo $mode != 'command' ? 'checked' : ''; ?>> Véhicules D'occasions
            </label>
        </div>
    </div>
</div>
<div class="row"></div>
<div id="receiverOffersList">
    <?php //myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/ajax/fetchOffers.ajax.php', array('mode' => $mode)); ?>
</div>
