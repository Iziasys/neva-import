<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

$brandIds = $_POST['brandIds'];
$db = databaseConnection();
if(!empty($brandIds)){
    $modelList = array();
    foreach($brandIds as $brandId){
        $brandId = (int)$brandId;
        $tmp = \Vehicle\ModelManager::fetchModelListByBrand($db, $brandId);
        foreach($tmp as $item){
            $modelList[] = $item;
        }
    }
}
else{
    $modelList = \Vehicle\ModelManager::fetchModelList($db, 'modelName', 'ASC');
}
$db = null;
?>

<?php
foreach($modelList as $model):
    ?>
    <option value="<?php echo $model->getId(); ?>"><?php echo $model->getName(); ?></option>
    <?php
endforeach;