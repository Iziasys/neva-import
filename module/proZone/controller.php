<?php

//Si l'utilisateur est connecté
if(is_connected()):
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    if(!empty($_GET['action']) && !empty($_GET['category'])):
        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/'.$_GET['category'].'/'.$_GET['action'].'.php');
    else:
        myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/'.$askedModule.'/views/home.php');
    endif;
//Sinon
else:
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/home/connectionForm.php');
endif;