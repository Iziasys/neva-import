<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

$modelIds = $_POST['modelIds'];
$db = databaseConnection();
if(!empty($modelIds)){
    $finishList = array();
    foreach($modelIds as $modelId){
        $modelId = (int)$modelId;
        $tmp = \Vehicle\FinishManager::fetchFinishListByModel($db, $modelId);
        foreach($tmp as $item){
            $finishList[] = $item;
        }
    }
}
else{
    $arrayOrder = array(
        array(
            'orderBy' => 'finishName',
            'way' => 'ASC'
        )
    );
    $finishList = \Vehicle\FinishManager::fetchFinishList($db, false, $arrayOrder);
}
$db = null;
?>

<?php
foreach($finishList as $finish):
    ?>
    <option value="<?php echo $finish->getId(); ?>"><?php echo $finish->getName(); ?></option>
    <?php
endforeach;