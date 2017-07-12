<?php

//Si l'utilisateur est connecté
if(is_connected()){
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $rights = $user->getRights();
    $structure = $user->getStructure();

    if(!empty($_GET['action'])){
        switch($_GET['action']){
            /*******************CREATION*****************/
            case 'create' :
                if($user->getType() === 1):
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/create.php');
                else:
                    echo 'Vous n\'avez pas les droits pour voir cette page.';
                endif;
                break;
            /*******************CREATION*****************/
            /*******************MODIFICATION*****************/
            case 'modify' :
                if($user->getType() === 1):
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/create.php');
                else:
                    echo 'Vous n\'avez pas les droits nécessaires pour modifier ce pays.';
                endif;
                break;
            /*******************MODIFICATION*****************/
            /*******************VISUALISATION*****************/
            case 'view' :
                if($user->getType() === 1) :
                    if(empty($_GET['orderBy'])):
                        $_GET['orderBy'] = 'id';
                        $orderBy = 'id';
                    else:
                        switch($_GET['orderBy']):
                            case 'id' :
                                $orderBy = 'id';
                                break;
                            case 'departement' :
                                $orderBy = 'department';
                                break;
                            case 'nom' :
                                $orderBy = 'departmentName';
                                break;
                            case 'montant' :
                                $orderBy = 'amount';
                                break;
                            case 'date' :
                                $orderBy = 'date';
                                break;
                            default :
                                $orderBy = 'id';
                                break;
                        endswitch;
                    endif;
                    if(empty($_GET['orderWay'])):
                        $_GET['orderWay'] = 'asc';
                        $orderWay = 'ASC';
                    else:
                        switch($_GET['orderWay']):
                            case 'asc' :
                                $orderWay = 'ASC';
                                break;
                            case 'desc' :
                                $orderWay = 'DESC';
                                break;
                            default :
                                $orderWay = 'ASC';
                                break;
                        endswitch;
                    endif;
                    if(empty($_GET['limit'])):
                        $limit = 30;
                    else:
                        $limit = (int)$_GET['limit'];
                    endif;
                    if(empty($_GET['pageNumber'])):
                        $pageNumber = 1;
                    else:
                        $pageNumber = (int)$_GET['pageNumber'];
                    endif;
                    $args = array(
                        'orderBy'    => $orderBy,
                        'orderWay'   => $orderWay,
                        'limit'      => $limit,
                        'pageNumber' => $pageNumber
                    );
                    myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/'.$askedModule.'/views/view.php', $args);
                else:
                    echo 'Vous n\'avez pas les droits pour voir cette page.';
                endif;
                break;
            /*******************VISUALISATION*****************/
            default :
                echo 'Erreur lors du choix de l\'action.';
                break;
        }
    }
    else{
        myInclude($_SERVER["DOCUMENT_ROOT"].getAppPath().'/module/admin/homeAdmin.php');
    }
}
//Sinon
else{
    myInclude($_SERVER["DOCUMENT_ROOT"] . getAppPath() . '/module/home/connectionForm.php');
}