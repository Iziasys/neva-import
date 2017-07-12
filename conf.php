<?php
/**
 * @return string
 */
function getAppPath(){
    return '';
}

$GLOBALS['__SITE_MODE'] = 'development';

/*****************POUR LA CONNEXION A LA BDD*****************/
function getHost(){return 'localhost';}
function getPrefDb(){return 'bdd_tool_neva';}
function getUsr(){return 'dev_write';}
function getPwd(){return 'avngrp#14';}
/*****************POUR LA CONNEXION A LA BDD*****************/

/*****************POUR LA DUREE DES OFFRES******************/
$GLOBALS['__VALIDITY_DURATION'] = 10;
/*****************POUR LA DUREE DES OFFRES******************/