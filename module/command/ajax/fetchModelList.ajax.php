<?php
if(!isset($_SESSION)){
    include('./header_ajax.php');
}

$data = $_POST['args'];

//Split des différents arguments
$argList = preg_split('/---/', $data);

$arguments = array();
foreach($argList as $arg){
    $details = preg_split('/:/', $arg);
    $arguments[$details[0]] = $details[1];
}

$brandId = (int)$arguments['brandId'];
$modelName = (string)$arguments['modelName'];
$db = databaseConnection();
$modelList = \Vehicle\ModelManager::fetchModelListByBrand($db, $brandId, $modelName);
$db = null;
?>

<?php
foreach($modelList as $model):
?>
<button class="dropdown-item typeAhead-dropdown-item" data-typeAhead-value="<?php echo $model->getName(); ?>"
        type="button">
    <?php echo $model->getName(); ?>
</button>
<?php
endforeach;