<?php
$pageTitle = 'Espace Professionnel';
$moreJs = array('formManager');


if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();


}