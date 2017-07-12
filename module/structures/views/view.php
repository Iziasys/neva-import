<?php
$db = databaseConnection();
$structuresList = \Users\StructureManager::fetchListOfStructures($db);
$db = null;
?>

<div class="container-fluid">
    <h3 class="page-header">Visualisation des structures</h3>

    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Tel.</th>
            <th>Email</th>
            <th>Partenaire</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($structuresList as $structure):
        ?>
        <tr>
            <td><?php echo $structure->getStructureName(); ?></td>
            <td><?php echo $structure->getAddress().' '.$structure->getPostalCode().' '.$structure->getTown(); ?></td>
            <td><?php echo $structure->getPhone(); ?></td>
            <td><?php echo $structure->getEmail(); ?></td>
            <td><?php echo $structure->getIsPartner() ? 'Oui' : 'Non'; ?></td>
            <td>
                <a href="/structures/modifier/<?php echo $structure->getId(); ?>"
                   class="btn btn-primary-outline btn-sm fa fa-pencil" title="Modifier la structure"></a>
                <a href="/structures/supprimer/<?php echo $structure->getId(); ?>"
                   class="btn btn-danger-outline btn-sm fa fa-times" title="Supprimer la structure"></a>

            </td>
        </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
</div>