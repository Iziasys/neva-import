<?php

//Si l'utilisateur est connecté
if(is_connected()){
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/views/home.php');
}
//Sinon
else{
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/connectionForm.php');
}