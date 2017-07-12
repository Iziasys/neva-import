<?php

//Si l'utilisateur est connecté
if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();


    if(!empty($_GET['action'])){
        switch($_GET['action']){
            /*******************CREATION*****************/
            case 'create' :
                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/create.php');
                break;
            /*******************CREATION*****************/
            /*******************MODIFICATION*****************/
            case 'modify' :
                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/create.php');
                break;
            /*******************MODIFICATION*****************/
            /*******************VISUALISATION*****************/
            case 'view' :
                if(!empty($_GET['type'])){
                    switch($_GET['type']){
                        case 'societies' :
                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/viewSocieties.php');
                            break;
                        case 'individuals' :
                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/viewIndividuals.php');
                            break;
                        default :
                            myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                            break;
                    }
                }
                else{
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
                }
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
//Sinon
else{
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/home/connectionForm.php');
}