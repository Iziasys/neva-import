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
                if($user->getType() === 1):
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/create.php');
                else:
                    echo 'Vous n\'avez pas les droits pour voir cette page.';
                endif;
                break;
            /*******************CREATION*****************/
            /*******************MODIFICATION*****************/
            case 'modify' :
                if($user->getType() === 1):
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/create.php');
                else:
                    echo 'Vous n\'avez pas les droits nécessaires pour modifier ce concessionnaire.';
                endif;
                break;
            /*******************MODIFICATION*****************/
            /*******************VISUALISATION*****************/
            case 'view' :
                if($user->getType() === 1) :
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/view.php');
                else:
                    echo 'Vous n\'avez pas les droits pour voir cette page.';
                endif;
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