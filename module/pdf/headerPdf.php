<?php
include($_SERVER['DOCUMENT_ROOT'].'/conf.php');
if($GLOBALS['__SITE_MODE'] == 'development'){
    ini_set("display_errors", 1);
}

include($_SERVER["DOCUMENT_ROOT"].getAppPath().'/fonctions/fonctions.php');//Appel du fichier de fonctions principales
include($_SERVER["DOCUMENT_ROOT"].getAppPath().'/fonctions/loadLibraries.php');

spl_autoload_register('loadClass');//Chargement automatique des classes
session_start();//Démarrage de la session
$_SESSION['ROOT_PATH'] = getAppPath();

include($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/ressources/connexion.php');

header('Content-Type: text/html; charset=UTF-8;');