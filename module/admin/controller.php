<?php

//Si l'utilisateur est connecté
if(is_connected()){
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/homeAdmin.php');
}
//Sinon
else{
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/home/connectionForm.php');
}