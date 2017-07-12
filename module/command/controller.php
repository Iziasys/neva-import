<?php

//Si l'utilisateur est connecté
if(is_connected()){
    if(!empty($_GET['category'])){
        switch($_GET['category']){
            case 'genericVehicle' :
                if($user->getType() === 1){
                    if(!empty($_GET['action'])){
                        switch($_GET['action']){
                            /*******************CREATION*****************/
                            case 'create' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/genericVehicle/create.php');
                                break;
                            /*******************CREATION*****************/
                            /*******************MODIFICATION*****************/
                            case 'modify' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/genericVehicle/create.php');
                                break;
                            /*******************MODIFICATION*****************/
                            case 'delete' :
                            /*******************VISUALISATION*****************/
                            case 'view' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/genericVehicle/view.php');
                                break;
                            /*******************VISUALISATION*****************/
                            default :
                                echo 'Erreur lors du choix de l\'action.';
                                break;
                        }
                    }
                    else{
                        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                    }
                }
                break;
            case 'vehicle' :
                if($user->getType() === 1){
                    if(!empty($_GET['action'])){
                        switch($_GET['action']){
                            /*******************CREATION*****************/
                            case 'create' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/vehicle/create.php');
                                break;
                            /*******************CREATION*****************/
                            /*******************MODIFICATION*****************/
                            case 'modify' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/vehicle/create.php');
                                break;
                            /*******************MODIFICATION*****************/
                            /*******************SUPPRESSION*****************/
                            case 'delete' :
                            /*******************SUPPRESSION*****************/
                            /*******************VISUALISATION*****************/
                            case 'view' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/vehicle/view.php');
                                break;
                            /*******************VISUALISATION*****************/
                            default :
                                echo 'Erreur lors du choix de l\'action.';
                                break;
                        }
                    }
                    else{
                        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                    }
                }
                break;
            case 'equipment' :
                if($user->getType() === 1){
                    if(!empty($_GET['action'])){
                        switch($_GET['action']){
                            /*******************CREATION*****************/
                            case 'create' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/equipment/create.php');
                                break;
                            /*******************CREATION*****************/
                            default :
                                echo 'Erreur lors du choix de l\'action.';
                                break;
                        }
                    }
                    else{
                        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                    }
                }
                break;
            case 'color' :
                if($user->getType() === 1){
                    if(!empty($_GET['action'])){
                        switch($_GET['action']){
                            /*******************CREATION*****************/
                            case 'create' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/color/create.php');
                                break;
                            /*******************CREATION*****************/
                            /*******************MODIFICATION*****************/
                            case 'modify' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/color/create.php');
                                break;
                            /*******************MODIFICATION*****************/
                            /*******************VISUALISATION*****************/
                            case 'view' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/color/view.php');
                                break;
                            /*******************VISUALISATION*****************/
                            /*******************SUPPRESSION*****************/
                            //La suppression rammène sur la visualisation, c'est le fichier defVar.php qui a une execution différente
                            case 'delete' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/color/view.php');
                                break;
                            default :
                                echo 'Erreur lors du choix de l\'action.';
                                break;
                        }
                    }
                    else{
                        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                    }
                }
                break;
            case 'rim' :
                if($user->getType() === 1){
                    if(!empty($_GET['action'])){
                        switch($_GET['action']){
                            /*******************CREATION*****************/
                            case 'create' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/rim/create.php');
                                break;
                            /*******************CREATION*****************/
                            /*******************MODIFICATION*****************/
                            case 'modify' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/rim/create.php');
                                break;
                            /*******************MODIFICATION*****************/
                            /*******************VISUALISATION*****************/
                            case 'view' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/rim/view.php');
                                break;
                            /*******************VISUALISATION*****************/
                            /*******************SUPPRESSION*****************/
                            //La suppression rammène sur la visualisation, c'est le fichier defVar.php qui a une execution différente
                            case 'delete' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/rim/view.php');
                                break;
                            default :
                                echo 'Erreur lors du choix de l\'action.';
                                break;
                        }
                    }
                    else{
                        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                    }
                }
                break;
            case 'pack' :
                if($user->getType() === 1){
                    if(!empty($_GET['action'])){
                        switch($_GET['action']){
                            /*******************CREATION*****************/
                            case 'create' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/pack/create.php');
                                break;
                            /*******************CREATION*****************/
                            /*******************MODIFICATION*****************/
                            case 'modify' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/pack/create.php');
                                break;
                            /*******************MODIFICATION*****************/
                            /*******************VISUALISATION*****************/
                            case 'view' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/pack/view.php');
                                break;
                            /*******************VISUALISATION*****************/
                            /*******************SUPPRESSION*****************/
                            //La suppression rammène sur la visualisation, c'est le fichier defVar.php qui a une execution différente
                            case 'delete' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/pack/view.php');
                                break;
                            default :
                                echo 'Erreur lors du choix de l\'action.';
                                break;
                        }
                    }
                    else{
                        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                    }
                }
                break;
            case 'catalog' :
                if(empty($_GET['action'])){
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/catalog/home.php');
                }
                else{
                    switch($_GET['action']){
                        case 'view' :
                            if(empty($_GET['vehicleId'])){
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/catalog/home.php');
                            }
                            else{
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/catalog/viewVehicle.php');
                            }
                            break;
                        default :
                            echo 'Erreur lors du choix de l\'action';
                            break;
                    }
                }
                break;
            case 'offers' :
                if(empty($_GET['action'])){
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/catalog/home.php');
                }
                else{
                    switch($_GET['action']){
                        case 'create' :
                            $db = databaseConnection();
                            $lastCreatedOffer = \Offers\OfferManager::fetchLastCreatedOffer($db);
                            $db = null;

                            $offerNumber = $lastCreatedOffer->getNumber();
                            $offerDate = $lastCreatedOffer->getCreationDate()->format('Ymd');
                            $offerReference = $offerDate.$offerNumber;

                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/offers/view.php', array('offerReference' => $offerReference));
                            break;
                        case 'view' :
                            if(!empty($_GET['offerReference'])){
                                $offerReference = $_GET['offerReference'];

                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/offers/view.php', array('offerReference' => $offerReference));
                            }
                            else{
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/views/viewList.php', array('mode' => 'command', 'state' => 1));
                            }
                            break;
                        case 'print' :

                            break;
                        /*case 'cancel' :
                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/views/viewList.php', array('mode' => 'command', 'state' => 1));
                            break;*/
                        case 'transform' :
                            //Ici, le fichier DefVar.php se chargera de transformer l'ODP en BDC, ce fichier ne fait que servir la vue
                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/views/viewList.php', array('mode' => 'command', 'state' => 2));
                            break;
                        default :
                            echo 'Erreur lors du choix de l\'action';
                            break;
                    }
                }
                break;
            case 'orderForms' :
                if(empty($_GET['action'])){
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/catalog/home.php');
                }
                else{
                    switch($_GET['action']){
                        case 'view' :
                            if(!empty($_GET['offerReference'])){
                                $offerReference = $_GET['offerReference'];

                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/orderForms/view.php', array('offerReference' => $offerReference));
                            }
                            else{
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/views/viewList.php', array('mode' => 'command', 'state' => 2));
                            }
                            break;
                    }
                }
                break;
            case 'images' :
                if($user->getType() === 1){
                    if(!empty($_GET['action'])){
                        switch($_GET['action']){
                            /*******************CREATION*****************/
                            case 'create' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/images/create.php');
                                break;
                            /*******************CREATION*****************/
                            /*******************MODIFICATION*****************/
                            case 'modify' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/images/create.php');
                                break;
                            /*******************MODIFICATION*****************/
                            /*******************VISUALISATION*****************/
                            case 'view' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/images/view.php');
                                break;
                            /*******************VISUALISATION*****************/
                            /*******************SUPPRESSION*****************/
                            //La suppression rammène sur la visualisation, c'est le fichier defVar.php qui a une execution différente
                            case 'delete' :
                                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/images/view.php');
                                break;
                            default :
                                echo 'Erreur lors du choix de l\'action.';
                                break;
                        }
                    }
                    else{
                        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                    }
                }
                break;
            default : break;
        }
    }
}
//Sinon
else{
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/home/connectionForm.php');
}