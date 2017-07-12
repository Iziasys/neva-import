<?php
$pageTitle = 'Offres';
$moreCss = array('jquery-ui.min', 'jquery-ui.structure.min', 'jquery-ui.theme.min');
$moreJs = array('formManager', 'jquery-ui.min', 'sortTable', 'offers_function');

if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();


}