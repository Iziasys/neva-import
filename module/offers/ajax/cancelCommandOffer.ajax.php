<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}
$db = databaseConnection();
try{
    if(empty($_POST['offerReference'])){
        throw new Exception('Post non défini');
    }
    $offerReference = $_POST['offerReference'];
    $db->beginTransaction();

    /***************************RECUPERATION DE L'OFFRE**********************/
    $offer = \Offers\OfferManager::fetchOfferByReference($db, $offerReference);
    if(is_a($offer, '\Exception'))
        throw $offer;
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    /***************************RECUPERATION DE L'OFFRE**********************/

    /***************************VERIFICATION DES DROITS**********************/
    //Si l'utilisateur n'est pas admin
    if(!$user->isAdmin()){
        //Si il est dirigeant de ce garage
        if($user->isOwner()){
            $userOffer = \Users\UserManager::fetchUser($db, $offer->getOwnerId());
            //Si la personne connectée n'est pas dirigeante de la structure concernée par l'offre
            if($user->getStructureId() != $userOffer->getStructureId()){
                throw new Exception('Erreur, vous n\'avez pas les droits pour annuler cette offre.');
            }
        }
        //Si il ne l'est pas
        else{
            //Si la personne connectée n'est pas celle qui a créé cette offre
            if($user->getId() != $offer->getOwnerId()){
                throw new Exception('Erreur, vous n\'avez pas les droits pour annuler cette offre.');
            }
        }
    }
    /***************************VERIFICATION DES DROITS**********************/

    $offer->setState(0);

    //Update dans la BDD
    $hydrateOffer = \Offers\OfferManager::hydrateOfferState($db, $offer);
    if(is_a($hydrateOffer, '\Exception')){
        throw $hydrateOffer;
    }

    $db->commit();
    $db = null;

    $_SESSION['returnAction'] = array(1, 'Annulation de l\'offre effectuée avec succès !');

    return true;
}
catch(Exception $e){
    $db->rollBack();
    $_SESSION['returnAction'] = array(0, 'Erreur N°'.$e->getCode().' : '.$e->getMessage().' '.$e->getFile().' '.$e->getLine());
    $db = null;

    return false;
}