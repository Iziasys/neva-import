<?php
//Calcul de l'offset pour la requête de recherche
$offset = ($pageNumber - 1) * $limit;

$db = databaseConnection();
$horsePowerList = \Prices\HorsepowerPriceManager::fetchHorsePowerList($db, $orderBy, $orderWay, $limit, $offset);
$nbMaxResults = \Prices\HorsepowerPriceManager::countHorsePower($db);
$fixAmountForRC = \Prices\HorsepowerPriceManager::fetchFixAmount($db);
$db = null;

/********************CALCUL DU NOMBRE DE PAGES****************/
$resPerPage = $limit;
$nbPages = ceil($nbMaxResults / $resPerPage);
/********************CALCUL DU NOMBRE DE PAGES****************/
?>

<div class="container">
    <h3 class="page-header text-lg-center">Visualisation des frais de carte grise</h3>
    <div class="row form-group">
        <label for="inputFixAmountForRC" class="col-lg-4 col-lg-offser-2 form-control-label">Frais fixes pour CG :</label>
        <div class="col-lg-2 input-group">
            <input type="text" id="inputFixAmountForRC" class="form-control" value="<?php echo $fixAmountForRC; ?>">
            <span class="input-group-addon">€</span>
        </div>
        <div class="col-lg-2">
            <input type="button" id="btnModifyFixAmountForRC" class="btn btn-primary" value="Modifier">
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th><a href="/tarifs-fiscaux/visualiser/departement/<?php echo $orderBy == 'department' && $orderWay == 'ASC' ? 'desc' : 'asc'; ?>">Département</a></th>
            <th><a href="/tarifs-fiscaux/visualiser/montant/<?php echo $orderBy == 'amount' && $orderWay == 'ASC' ? 'desc' : 'asc'; ?>">Montant</a></th>
            <th><a href="/tarifs-fiscaux/visualiser/date/<?php echo $orderBy == 'date' && $orderWay == 'ASC' ? 'desc' : 'asc'; ?>">Date de MàJ</a></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($horsePowerList as $horsePower):
            ?>
            <tr>
                <td><?php echo str_pad($horsePower->getDepartment(), 2, STR_PAD_LEFT, '0'); ?></td>
                <td><?php echo $horsePower->getAmount().' € / cv'; ?></td>
                <td><?php echo $horsePower->getRefreshDate()->format('d/m/Y H:i:s'); ?></td>
                <td>
                    <a href="/tarifs-fiscaux/modifier/<?php echo $horsePower->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                </td>
            </tr>
            <?php
        endforeach;
        ?>
        </tbody>
    </table>
    <nav>
        <ul class="pagination">
            <li class="page-item <?php echo $pageNumber == 1 ? 'disabled' : ''; ?>">
                <a href="/tarifs-fiscaux/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit; ?>/1" class="page-link" aria-label="First">
                    <span aria-hidden="true" class="fa fa-angle-double-left"></span>
                </a>
            </li>
            <li class="page-item <?php echo $pageNumber == 1 ? 'disabled' : ''; ?>">
                <a href="/tarifs-fiscaux/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit.'/'.($pageNumber - 1); ?>" class="page-link" aria-label="Previous">
                    <span aria-hidden="true" class="fa fa-angle-left"></span>
                </a>
            </li>
            <?php
            for($i = 1; $i <= $nbPages; $i++){
                ?>
                <li class="page-item <?php echo $pageNumber == $i ? 'active' : ''; ?>">
                    <a href="/tarifs-fiscaux/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit.'/'.$i; ?>" class="page-link"><?php echo $i; ?></a>
                </li>
                <?php
            }
            ?>
            <li class="page-item <?php echo $pageNumber == $nbPages ? 'disabled' : ''; ?>">
                <a href="/tarifs-fiscaux/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit.'/'.($pageNumber + 1); ?>" class="page-link" aria-label="Next">
                    <span aria-hidden="true" class="fa fa-angle-right"></span>
                </a>
            </li>
            <li class="page-item <?php echo $pageNumber == $nbPages ? 'disabled' : ''; ?>">
                <a href="/tarifs-fiscaux/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit.'/'.$nbPages; ?>" class="page-link" aria-label="Last">
                    <span aria-hidden="true" class="fa fa-angle-double-right"></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
