<?php
/********************************************************************
 *						INDEX PRINCIPAL DU SITE						*
 *	ALPHA_NEVA      		INDEX.PHP						    	*
 *	CREATION LE : 									11/01/2016		*
 *	AUTEUR :										DECOCK Stéphane	*
 ********************************************************************/


include($_SERVER['DOCUMENT_ROOT'].'/conf.php');
if($GLOBALS['__SITE_MODE'] == 'development'){
    ini_set("display_errors", 1);
}

include($_SERVER["DOCUMENT_ROOT"].getAppPath().'/fonctions/fonctions.php');//Appel du fichier de fonctions principales
include($_SERVER["DOCUMENT_ROOT"].getAppPath().'/fonctions/loadLibraries.php');

spl_autoload_register('loadClass');//Chargement automatique des classes
session_start();//Démarrage de la session
$_SESSION['ROOT_PATH'] = getAppPath();

/*****************EN TETE DE CACHE*******************/
header('Expires: FRI, 30 Jan 2014 10:40:00 GMT');
header('Last-Modified: THU, 28 Jan 2016 08:00:00 GMT');
header('Cache-Control: max-age=1, must-revalidate');
/*****************EN TETE DE CACHE*******************/

include($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/ressources/connexion.php');

$askedModule = empty($_GET['page']) ? 'home' : $_GET['page'];

if(isset($_GET['logout'])){
    $_SESSION['connected'] = 0;
    $_SESSION['user'] = null;
    session_destroy();
}
if(!empty($_POST['connectionForm'])){
    $data = $_POST['connectionForm'];

    $db = databaseConnection();
    $user = \Users\UserManager::tryToConnect($db, $data['email'], $data['password']);
    $db = null;

    //Si login incorrect
    if(is_a($user, 'Exception')){
        $_SESSION['returnAction'] = array(0, $user->getMessage());
        //myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/connectionForm.php');
    }
    //Si login correct
    else{
        //Actualisation de la dernière connexion en base
        $db = databaseConnection();
        $user->setLastConnection(new DateTime());
        $hydrateUser = \Users\UserManager::hydrateUser($db, $user);
        $db = null;

        $_SESSION['returnAction'] = array(1, 'Connexion réussie !');
        //Stockage dans les variables de session
        $_SESSION['connected'] = true;
        $_SESSION['user'] = $user;

    }
}
/*************************INCLUSION DES FICHIERS RELATIFS AU MODULE DEMANDE***********************************/
if(empty($_GET['page'])){//Si on ne demande pas de page spécifique
    $askedModule = 'home';//On charge le module d'accueil
    include($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/module/home/defVar.php');//Et sa définition de variables
}
else{//Si une page est demandée
    $askedModule = $_GET['page'];//On charge son module
    if(is_file($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/defVar.php')){
        include($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/defVar.php');//Et sa définition de variables
    }
}

$stylesheet = '';
$js = '';

//Inclusion des CSS
if(is_file($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/theme/theme.css')){
    $stylesheet = '<link rel="stylesheet" type="text/css" href="'.$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/theme/theme.css">';
}

//Inclusion des responsives
if(is_file($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/theme/responsive.css')){
    $stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/theme/responsive.css">';
}

//Inclusion des JS
if(is_file($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/javascripts/fonctions.js')){
    $js = '<script type="text/javascript" src="'.$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/javascripts/fonctions.js"></script>';
}
//Inclusion des fonctions PHP
if(is_file($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/fonctions/fonctions.php')){
    include($_SERVER["DOCUMENT_ROOT"].$_SESSION['ROOT_PATH'].'/module/'.$askedModule.'/fonctions/fonctions.php');
}
/*************************INCLUSION DES FICHIERS RELATIFS AU MODULE DEMANDE***********************************/
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Neva-Import - <?php echo $pageTitle; ?></title>
    <link rel="icon" type="image/png" href="/favicon.png" />
    <link rel="stylesheet" href="<?php echo getAppPath(); ?>/theme/bootstrap.css">
    <link rel="stylesheet" href="<?php echo getAppPath(); ?>/theme/theme.css">
    <link rel="stylesheet" href="<?php echo getAppPath(); ?>/theme/font-awesome.css">
    <?php echo $stylesheet; ?>
    <?php if(!empty($moreCss))loadCss($moreCss); ?>
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . getAppPath() . '/ressources/navBar.php'); ?>
<div id="alertsReceiver">
    <?php
    if(!empty($_SESSION['returnAction'])){
        msgReturn_push($_SESSION['returnAction']);
        unset($_SESSION['returnAction']);
    }
    ?>
</div>
<div id="content" class="container-fluid">
    <?php include($_SERVER['DOCUMENT_ROOT'] . getAppPath() . '/ressources/headBar.php'); ?>
    <?php
    if(is_file($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/controller.php'))://Si le module demandé existe
        include($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/' . $askedModule . '/controller.php');
    //On l'appel
    else://Sinon
        ?><div id="blocRetourMessage" class="erreur">Accès impossible, Erreur d'accès</div><?php
    endif;
    ?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="<?php echo getAppPath(); ?>/javascripts/bootstrap.js"></script>
<script src="<?php echo getAppPath(); ?>/javascripts/fonctions.js"></script>
<?php echo $js; ?>
<?php if(!empty($moreJs))loadJs($moreJs); ?>
</body>
</html>