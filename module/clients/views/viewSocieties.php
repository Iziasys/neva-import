<?php

/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();

$db = databaseConnection();
$clientsList = \Users\ClientManager::fetchClientsList($db, $structure->getId(), $structure->getIsPrimary(), true);
$db = null;
?>

<div class="container">
    <h3 class="page-header">Visualisation des clients</h3>

    <table class="table table-striped table-hover sortable">
        <thead>
        <tr>
            <th>Société</th>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Accepte les offres</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(!is_a($clientsList, '\Exception')):
            foreach($clientsList as $client):
                ?>
                <tr>
                    <td><?php echo $client->getName(); ?></td>
                    <td><?php echo $client->getCivility().'. '.$client->getLastName().' '.$client->getFirstName(); ?></td>
                    <td><?php echo $client->getPostalAddress(); ?></td>
                    <td><?php echo getPhoneNumber($client->getPhone()); ?></td>
                    <td><?php echo $client->getEmail(); ?></td>
                    <td><?php echo $client->getAcceptNewsLetter() ? 'Oui' : 'Non'; ?></td>
                    <td>
                        <a href="/clients/modifier/<?php echo $client->getId(); ?>" class="btn btn-primary-outline btn-sm fa fa-pencil"></a>
                        <a href="/clients/supprimer/<?php echo $client->getId(); ?>" class="btn btn-danger-outline btn-sm fa fa-times"></a>
                    </td>
                </tr>
                <?php
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</div>
