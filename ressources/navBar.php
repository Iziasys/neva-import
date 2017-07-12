<?php
if(is_connected()):
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
?>
<nav class="navbar-dark bg-inverse my-navbar">
    <div id="block-logo-receiver">
        <div id="logo-receiver">
            <img src="/theme/images/bani/<?php echo $user->getStructure()->getImageName(); ?>" >
        </div>
    </div>
    <hr>
    <ul class="nav">
        <li class="nav-item <?php echo $askedModule == 'home' ? 'active' : ''; ?>">
            <a href="/" class="nav-link">Accueil <span class="sr-only">(actuel)</span></a>
        </li>
        <li class="nav-item <?php echo $askedModule == 'vehicleOnDemand' ? 'active' : ''; ?>">
            <a href="/commande-vehicules/catalogue" class="nav-link">Commande de véhicule</a>
        </li>
        <li class="nav-item <?php echo $askedModule == 'vehicleOnStock' ? 'active' : ''; ?>">
            <a href="/stock-arrivage/catalogue/visualiser" class="nav-link">Stock et arrivage</a>
        </li>
        <li class="nav-item <?php echo $askedModule == 'proZone' ? 'active' : ''; ?>">
            <a href="/espace-pro" class="nav-link">Mon espace Pro</a>
        </li>
        <li class="nav-item <?php echo $askedModule == 'admin' ? 'active' : ''; ?>">
            <a href="/administration" class="nav-link">Administration</a>
        </li>
    </ul>
    <ul class="nav nav-to-bottom">
        <li class="nav-item pull-bottom">
            <a href="/deconnexion" class="nav-link">Déconnexion</a>
        </li>
    </ul>
</nav>
<?php
endif;