<?php
//Calcul de l'offset pour la requête de recherche
$offset = ($pageNumber - 1) * $limit;

$db = databaseConnection();
$freightChargesList = \Prices\FreightChargesInFranceManager::fetchFreightChargesList($db, $orderBy, $orderWay, $limit, $offset);
$nbMaxResults = \Prices\FreightChargesInFranceManager::countFreightCharges($db);
$db = null;

/********************CALCUL DU NOMBRE DE PAGES****************/
$resPerPage = $limit;
$nbPages = ceil($nbMaxResults / $resPerPage);
/********************CALCUL DU NOMBRE DE PAGES****************/
?>

<div class="container">
    <h3 class="page-header">Visualisation des frais de transport intra-France <small>(Tout est indiqué au départ d'OBERENTZEN 68127)</small></h3>

    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th><a href="/transport/visualiser/departement/<?php echo $orderBy == 'department' && $orderWay == 'ASC' ? 'desc' : 'asc'; ?>">Département</a></th>
            <th><a href="/transport/visualiser/nom/<?php echo $orderBy == 'departmentName' && $orderWay == 'ASC' ? 'desc' : 'asc'; ?>">Nom</a></th>
            <th><a href="/transport/visualiser/montant/<?php echo $orderBy == 'amount' && $orderWay == 'ASC' ? 'desc' : 'asc'; ?>">Montant</a></th>
            <th><a href="/transport/visualiser/date/<?php echo $orderBy == 'date' && $orderWay == 'ASC' ? 'desc' : 'asc'; ?>">Date de MàJ</a></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($freightChargesList as $freightCharges):
            ?>
            <tr>
                <td><?php echo str_pad($freightCharges->getDepartment(), 2, STR_PAD_LEFT, '0'); ?></td>
                <td><?php echo $freightCharges->getDepartmentName(); ?></td>
                <td><?php echo $freightCharges->getAmount().' € HT'; ?></td>
                <td><?php echo $freightCharges->getDate()->format('d/m/Y H:i:s'); ?></td>
                <td>
                    <a href="/transport/modifier/<?php echo $freightCharges->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                    <a href="/transport/supprimer/<?php echo $freightCharges->getId(); ?>" class="btn btn-danger-outline btn-sm fa fa-times"></a>
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
                <a href="/transport/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit; ?>/1" class="page-link" aria-label="First">
                    <span aria-hidden="true" class="fa fa-angle-double-left"></span>
                </a>
            </li>
            <li class="page-item <?php echo $pageNumber == 1 ? 'disabled' : ''; ?>">
                <a href="/transport/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit.'/'.($pageNumber - 1); ?>" class="page-link" aria-label="Previous">
                    <span aria-hidden="true" class="fa fa-angle-left"></span>
                </a>
            </li>
            <?php
            for($i = 1; $i <= $nbPages; $i++){
                ?>
                <li class="page-item <?php echo $pageNumber == $i ? 'active' : ''; ?>">
                    <a href="/transport/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit.'/'.$i; ?>" class="page-link"><?php echo $i; ?></a>
                </li>
                <?php
            }
            ?>
            <li class="page-item <?php echo $pageNumber == $nbPages ? 'disabled' : ''; ?>">
                <a href="/transport/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit.'/'.($pageNumber + 1); ?>" class="page-link" aria-label="Next">
                    <span aria-hidden="true" class="fa fa-angle-right"></span>
                </a>
            </li>
            <li class="page-item <?php echo $pageNumber == $nbPages ? 'disabled' : ''; ?>">
                <a href="/transport/visualiser/<?php echo $_GET['orderBy'].'/'.$_GET['orderWay'].'/'.$limit.'/'.$nbPages; ?>" class="page-link" aria-label="Last">
                    <span aria-hidden="true" class="fa fa-angle-double-right"></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
