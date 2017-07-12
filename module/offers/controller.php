<?php

//Si l'utilisateur est connecté
if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    if(empty($_GET['category'])){
        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/offers/views/viewList.php', array('mode' => 'all', 'state' => $_GET['state']));
    }
    else{
        switch($_GET['category']){
            case '' :

                break;
            default :
                break;
        }
    }
}
//Sinon
else{
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/home/connectionForm.php');
}