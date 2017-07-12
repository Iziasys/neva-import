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
                if($rights->getCreateUser()):
                    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/create.php');
                else:
                    echo 'Vous n\'avez pas les droits pour voir cette page.';
                endif;
                break;
            /*******************CREATION*****************/
            /*******************MODIFICATION*****************/
            case 'modify' :
                if(($rights->getModifyUser() && $user->getId() == $_GET['userId']) || $rights->getCreateUser()):
                    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/create.php');
                else:
                    echo 'Vous n\'avez pas les droits nécessaires pour modifier cet utilisateur.';
                endif;
                break;
            /*******************MODIFICATION*****************/
            /*******************VISUALISATION*****************/
            case 'view' :
                myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/view.php');
                break;
            /*******************VISUALISATION*****************/
            default :
                echo 'Erreur lors du choix de l\'action.';
                break;
        }
    }
    else{
        myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/admin/homeAdmin.php');
    }
}
//Sinon
else{
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/home/connectionForm.php');
}