<?php
/** @var \Users\User $user */
$user = $_SESSION['user'];
$rights = $user->getRights();
$structure = $user->getStructure();
?>
<div class="col-lg-3">
    <div class="card">
        <h4 class="card-header text-xs-center card-primary card-principalColor">Gestion des structures et utilisateurs</h4>
        <div class="card-block">
            <ul class="card-text">
                <?php
                if($rights->getModifyStructure()):
                    ?>
                    <li><a href="/structures/modifier/<?php echo $structure->getId(); ?>">Modifier ma structure</a></li>
                    <?php
                endif;
                if($rights->getCreateStructure()):
                    ?>
                    <li><a href="/structures/creer">Créer une structure</a></li>
                    <li><a href="/structures/visualiser">Visualiser les structures</a></li>
                    <?php
                endif;
                if($rights->getCreateUser()):
                    ?>
                    <li><a href="/conseillers/creer">Créer un conseiller</a></li>
                    <?php
                endif;
                ?>
                    <li><a href="/conseillers/visualiser">Visualiser mes conseillers</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-3">
    <div class="card">
        <h4 class="card-header text-xs-center card-primary card-principalColor">Gestion des clients</h4>
        <div class="card-block">
            <ul class="card-text">
                <li><a href="/clients/creer">Créer un client</a></li>
                <li><a href="/clients/visualiser/societes">Visualiser mes sociétés clientes</a></li>
                <li><a href="/clients/visualiser/particuliers">Visualiser mes clients particuliers</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="col-lg-3">
    <?php if($user->getType() === 1): ?>
        <div class="card">
            <h4 class="card-header text-xs-center card-primary card-principalColor">Gestion des véhicules<br>en stock et arrivages</h4>
            <div class="card-block">
                <ul class="card-text">
                    <li><a href="/stock-arrivage/vehicules/creer">Création d'un véhicule</a></li>
                    <li><a href="/stock-arrivage/vehicules/visualiser">Visualisation des véhicules</a></li>
                </ul>
            </div>
        </div>
        <div class="card">
            <h4 class="card-header text-xs-center card-primary card-principalColor">Tarification</h4>
            <div class="card-block">
                <ul class="card-text">
                    <li><a href="/tarification/visualiser">Visualiser prix de vente et marges</a></li>
                </ul>
            </div>
        </div>
        <?php
    endif;
    ?>
</div>
<div class="col-lg-3">
    <?php
    if($user->getType() === 1):
        ?>
        <div class="card">
            <h4 class="card-header text-xs-center card-primary card-principalColor">Gestion des Fournisseurs et Transposteurs</h4>
            <div class="card-block">
                <ul class="card-text">
                    <li><a href="/devises/creer">Ajouter une devise</a></li>
                    <li><a href="/devises/visualiser"">Visualiser les devises</a></li>
                    <li><a href="/pays/creer">Ajouter un pays</a></li>
                    <li><a href="/pays/visualiser">Visualiser les pays</a></li>
                    <li><a href="/fournisseurs/creer">Créer un Fournisseur</a></li>
                    <li><a href="/fournisseurs/visualiser">Visualiser les Fournisseurs</a></li>
                    <li><a href="/transport/visualiser">Frais de transport intra-France</a></li>
                    <li><a href="/tarifs-fiscaux/visualiser">Tarifs Fiscaux en vigueur</a></li>
                </ul>
            </div>
        </div>
        <?php
    endif;
    ?>
    <?php
    if($user->getType() === 1):
        ?>
        <div class="card">
            <h4 class="card-header text-xs-center card-primary card-principalColor">Gestion des véhicules<br>à la demande</h4>
            <div class="card-block">
                <ul class="card-text">
                    <li><a href="/commande-vehicules/equipement/creer">Créer un équipement</a></li>
                    <li><a href="/commande-vehicules/vehicule-generique/creer">Créer un véhicule générique</a></li>
                    <li><a href="/commande-vehicules/vehicule-generique/visualiser">Visualiser les véhicules génériques</a></li>
                    <li><a href="/commande-vehicules/vehicule/creer">Créer un véhicule</a></li>
                    <li><a href="/commande-vehicules/vehicule/visualiser">Visualiser les véhicules</a></li>
                    <li><a href="/commande-vehicules/couleur/creer">Créer une couleur</a></li>
                    <li><a href="/commande-vehicules/couleur/visualiser">Visualiser les couleurs</a></li>
                    <li><a href="/commande-vehicules/jantes/creer">Créer des jantes</a></li>
                    <li><a href="/commande-vehicules/jantes/visualiser">Visualiser les jantes</a></li>
                    <li><a href="/commande-vehicules/pack/creer">Créer un pack</a></li>
                    <li><a href="/commande-vehicules/pack/visualiser">Visualiser les packs</a></li>
                    <li><a href="/commande-vehicules/images/ajouter">Ajouter une photo de véhicule</a></li>
                </ul>
            </div>
        </div>
        <?php
    endif;
    ?>
</div>