<?php
$db = databaseConnection();
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structureId = $user->getStructureId();
$usersList = \Users\UserManager::fetchListOfUsers($db, $structureId);
$db = null;
?>

<div class="container">
    <h3 class="page-header">Visualisation des structures</h3>

    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Tél.</th>
            <th>Port.</th>
            <th>Email</th>
            <th>Dernière connexion</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach($usersList as $user):
        ?>
        <tr>
            <td><?php echo $user->getLastName().' '.$user->getFirstName(); ?></td>
            <td><?php echo $user->getPhone(); ?></td>
            <td><?php echo $user->getMobile(); ?></td>
            <td><?php echo $user->getEmail(); ?></td>
            <td><?php echo $user->getLastConnection()->format('d/m/Y H:i:s'); ?></td>
            <td>
                <a href="/conseillers/modifier/<?php echo $user->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                <a href="/conseillers/supprimer/<?php echo $user->getId(); ?>" class="btn btn-danger-outline btn-sm fa fa-times"></a>
            </td>
        </tr>
        <?php
        endforeach;
        ?>
        </tbody>
    </table>
</div>