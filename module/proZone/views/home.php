<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$structure = $user->getStructure();
?>
<div class="col-lg-3">
    <div class="card">
        <h4 class="card-header text-xs-center card-primary card-principalColor">Visualisation des véhicules à la demande</h4>
        <div class="card-block">
            <ul class="card-text">
                <li><a href="/espace-pro/vehicules-demande/visualiser">Visualiser les véhicules disponibles</a></li>
            </ul>
        </div>
    </div>
</div>