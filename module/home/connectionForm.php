<?php

?>
<h2 class="page-header text-xs-center">Bienvenue.<br>Avant de pouvoir utiliser cet outil, veuillez vous connecter avec les identifiants qui vous ont été fournis.</h2>


<div class="container">
    <div class="card card-inverse">
        <h3 class="card-header text-xs-center card-primary card-principalColor">Connexion</h3>
        <div class="card-block">
            <form method="post" action="/connexion">
                <div class="form-group row">
                    <label for="inputEmail" class="col-md-2 col-md-offset-2 form-control-label">Email</label>
                    <div class="col-md-6">
                        <input type="email" class="form-control formManager" id="inputEmail" data-formManager="required mail" name="connectionForm[email]" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-md-2 col-md-offset-2 form-control-label">Mot de passe</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control formManager" id="inputPassword" data-formManager="required" name="connectionForm[password]" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-2 col-md-offset-8">
                        <button type="submit" class="btn btn-primary btn-principalColor formManager" data-formManager="submitInput">Connexion</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>