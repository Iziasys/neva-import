<?php
if($_GET['action'] == 'modify' && !empty($_GET['userId'])){
    $userId = (int)$_GET['userId'];
    $db = databaseConnection();
    $user = \Users\UserManager::fetchUser($db, $userId);
    $db = null;

    $lastName = $user->getLastName();
    $firstName = $user->getFirstName();
    $phone = $user->getPhone();
    $mobile = $user->getMobile();
    $email = $user->getEmail();
    $acceptNewsLetter = $user->getAcceptNewsLetter();
    $mode = 'modify';
}
else{
    /** @var \Users\User $user */
    $user = $_SESSION['user'];
    $structure = $user->getStructure();
    $lastName = '';
    $firstName = '';
    $phone = $structure->getPhone();
    $mobile = '';
    $email = $structure->getEmail();
    $fax = '';
    $acceptNewsLetter = $structure->getAcceptNewsLetter();
    $mode = 'create';
}
?>

<div class="container">
    <h3 class="page-header">
        <?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> d'un utilisateur
    </h3>
    <br>
    <form action="/conseillers/visualiser" method="POST">
    <?php
        if($mode == 'modify'):
        ?>
        <input type="hidden" name="<?php echo $mode; ?>User[id]" value="<?php echo $userId; ?>">
        <?php
        endif;
        ?>
        <div class="row">
            <div class="form-group">
                <label for="inputUserLastName" class="col-md-2 col-md-offset-1 form-control-label">Nom* :</label>
                <div class="col-md-3">
                    <input type="text" id="inputUserLastName" name="<?php echo $mode; ?>User[lastName]"
                           class="form-control formManager" data-formManager="required pureString" value="<?php echo $lastName; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputUserFirstName" class="col-md-2 form-control-label">Prénom* :</label>
                <div class="col-md-3">
                    <input type="text" id="inputUserFirstName" name="<?php echo $mode; ?>User[firstName]"
                           class="form-control formManager" data-formManager="required pureString" value="<?php echo $firstName; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputUserPassword" class="col-md-2 col-md-offset-1 form-control-label">
                    <?php echo $mode == 'create' ? 'Mot de passe*' : 'Changer mdp'; ?> :
                </label>
                <div class="col-md-3">
                    <input type="password" id="inputUserPassword" name="<?php echo $mode; ?>User[password]"
                           class="form-control formManager"
                           data-formManager="<?php echo $mode == 'create' ? 'required' : ''; ?>" <?php echo $mode == 'create' ? 'required' : ''; ?>>
                </div>
            </div>
            <div class="form-group">
                <label for="inputUserConfPass" class="col-md-2 form-control-label">
                    Confirmez<?php echo $mode == 'create' ? '*' : ''; ?> :
                </label>
                <div class="col-md-3">
                    <input type="password" id="inputUserConfPass" name="<?php echo $mode; ?>User[confPass]"
                           class="form-control formManager" data-formManager="sameAs"
                           data-formManager-sameAs="inputUserPassword" <?php echo $mode == 'create' ? 'required' : ''; ?>>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputUserPhone" class="col-md-2 col-md-offset-1 form-control-label">Tél. :</label>
                <div class="col-md-3">
                    <input type="text" id="inputUserPhone" name="<?php echo $mode; ?>User[phone]"
                           class="form-control formManager" data-formManager="integer length"
                           data-formManager-length="10" value="<?php echo $phone; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputUserMobile" class="col-md-2 form-control-label">Port. :</label>
                <div class="col-md-3">
                    <input type="text" id="inputUserMobile" name="<?php echo $mode; ?>User[mobile]"
                           class="form-control formManager" data-formManager="optional integer length"
                           data-formManager-length="10" value="<?php echo $mobile; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputUserEmail" class="col-md-2 col-md-offset-1 form-control-label">Email* :</label>
                <div class="col-md-3">
                    <input type="email" id="inputUserEmail" name="<?php echo $mode; ?>User[email]"
                           class="form-control formManager" data-formManager="required mail"
                           value="<?php echo $email; ?>" <?php echo $mode == 'create' ? 'required' : ''; ?>>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label class="col-md-2 col-md-offset-1 form-control-label">Accepte les offres* :</label>
                <div class="col-md-3">
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>User[acceptNewsLetter]"
                               value="0" <?php echo $acceptNewsLetter ? '' : 'checked'; ?>>Non
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>User[acceptNewsLetter]"
                               value="1" <?php echo $acceptNewsLetter ? 'checked' : ''; ?>>Oui
                    </label>
                </div>
            </div>
        </div>
        <br>
        <div class="form-group row">
            <div class="col-md-2 col-md-offset-8">
                <button type="submit" class="btn btn-primary btn-principalColor formManager"
                        data-formManager="submitInput">
                    <?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> l'utilisateur
                </button>
            </div>
        </div>
    </form>
</div>
