<?php

//Si l'utilisateur est connecté
if(is_connected()){
    //Si une action est définie
    if(!empty($_GET['action'])){
        switch($_GET['action']){
            case 'view' :
                myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/view.php');
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