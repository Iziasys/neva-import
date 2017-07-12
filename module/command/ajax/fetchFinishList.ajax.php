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

$modelName = (string)$arguments['modelName'];
$finishName = (string)$arguments['finishName'];
$db = databaseConnection();
$finishList = \Vehicle\FinishManager::fetchFinishListByModelName($db, $modelName, $finishName);
$db = null;
?>

<?php
foreach($finishList as $finish):
?>
<button class="dropdown-item typeAhead-dropdown-item" data-typeAhead-value="<?php echo $finish->getName(); ?>"
        type="button">
    <?php echo $finish->getName(); ?>
</button>
<?php
endforeach;