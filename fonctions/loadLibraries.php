<?php

/**
 * Charge la liste des fichiers JS demandés
 *
 * @param array $jsToLoad Sous la forme [file1, file2, file3, etc...]
 */
function loadJs(array $jsToLoad){
    $jsString = '';
    foreach($jsToLoad as $jsFile){
        $jsString .= '<script src="'.getAppPath().'/javascripts/'.$jsFile.'.js"></script>';
    }

    echo $jsString;
}

/**
 * Charge la liste des fichiers CSS demandés
 *
 * @param array $cssToLoad Sous la forme [file1, file2, file3, etc...]
 */
function loadCss(array $cssToLoad){
    $cssString = '';
    foreach($cssToLoad as $cssFile){
        $cssString .= '<link rel="stylesheet" href="'.getAppPath().'/theme/'.$cssFile.'.css">';
    }

    echo $cssString;
}
