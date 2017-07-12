<?php

//Si l'utilisateur est connecté
if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    if(!empty($_GET['action']) && !empty($_GET['category'])):
        switch($_GET['category']):
            case 'vehicles' :
                switch($_GET['action']):
                    /*******************CREATION*****************/
                    case 'create' :
                        if($user->getType() === 1):
                            myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/vehicles/create.php');
                        else:
                            echo 'Vous n\'avez pas les droits pour voir cette page.';
                        endif;
                        break;
                    /*******************CREATION*****************/
                    /*******************SUPPRESSION*****************/
                    case 'delete' :
                        if($user->getType() === 1) :
                            myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/vehicles/view.php');
                        else:
                            echo 'Vous n\'avez pas les droits pour voir cette page.';
                        endif;
                        break;
                    /*******************SUPPRESSION*****************/
                    /*******************MODIFICATION*****************/
                    case 'modify' :
                        if($user->getType() === 1 && !empty($_GET['vehicleId'])):
                            myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/vehicles/create.php');
                        else:
                            echo 'Vous n\'avez pas les droits pour voir cette page.';
                        endif;
                        break;
                    /*******************MODIFICATION*****************/
                    /*******************VISUALISATION*****************/
                    case 'view' :
                        if($user->getType() === 1):
                            myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/vehicles/view.php');
                        else:
                            echo 'Vous n\'avez pas les droits pour voir cette page.';
                        endif;
                        break;
                    /*******************VISUALISATION*****************/
                    /*******************COPIE DEPUIS VEHICULE EN COMMANDE*****************/
                    case 'copyFromCommand' :
                        if(!empty($_GET['vehicleId'])):
                            myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/vehicles/create.php');
                        else:
                            echo 'Erreur, aucun véhicule sélectionné';
                        endif;
                        break;
                    /*******************COPIE DEPUIS VEHICULE EN COMMANDE*****************/
                    /*******************MODE COPIER COLLER POUR LBC*****************/
                    case 'copyPaste' :
                        if(!empty($_GET['vehicleId'])):
                            myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/vehicles/copyPaste.php');
                        else:
                            echo 'Erreur, aucun véhicule sélectionné';
                        endif;
                        break;
                    /*******************MODE COPIER COLLER POUR LBC*****************/
                    default :
                        echo 'Erreur lors du choix de l\'action.';
                        break;
                endswitch;
                break;
            case 'catalog' :
                switch($_GET['action']):
                    /*******************VISUALISATION*****************/
                    case 'view' :
                        if(!empty($_GET['vehicleId'])):
                            myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/catalog/viewSingle.php');
                        else:
                            myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/catalog/view.php');
                        endif;
                        break;
                    /*******************VISUALISATION*****************/
                    default :
                        echo 'Erreur lors du choix de l\'action.';
                        break;
                endswitch;
                break;
            case 'offers' :
                switch($_GET['action']):
                    case 'create' :
                        if(empty($_POST['createOffer']['vehicleId']) || empty($_SESSION['selectedClient'])):
                            echo 'Erreur, champs mal renseignés.';
                        else:
                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/views/viewList.php', array('mode' => 'stock', 'state' => 1));
                        endif;
                        break;
                    case 'view' :
                        if(!empty($_GET['offerReference'])){
                            $offerReference = $_GET['offerReference'];

                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/offers/view.php', array('offerReference' => $offerReference));
                        }
                        else{
                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/views/viewList.php', array('mode' => 'stock', 'state' => 1));
                        }
                        break;
                    case 'transform' :
                        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/views/viewList.php', array('mode' => 'stock', 'state' => 2));
                        break;
                    default :
                        echo 'Erreur lors du choix de l\'action.';
                        break;
                endswitch;
                break;
            default :
                break;
        endswitch;
    else:
        myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/admin/homeAdmin.php');
    endif;
}
//Sinon
else{
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/home/connectionForm.php');
}