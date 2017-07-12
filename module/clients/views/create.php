<?php

/** @var \Users\User $user */
$user = $_SESSION['user'];

if($_GET['action'] == 'modify' && !empty($_GET['clientId'])){
    $clientId = (int)$_GET['clientId'];

    $mode = 'modify';

    $db = databaseConnection();
    $client = \Users\ClientManager::fetchClient($db, $clientId);
    $db = null;

    $isSociety = $client->getIsSociety();
    $societyName = $isSociety ? $client->getName() : '';
    $siren = $isSociety ? $client->getSiren() : '';
    $siret = $isSociety ? $client->getSiret() : '';
    $civility = $client->getCivility();
    $lastName = $client->getLastName();
    $firstName = $client->getFirstName();
    $phone = $client->getPhone();
    $mobile = $client->getMobile();
    $fax = $client->getFax();
    $email = $client->getEmail();
    $acceptOffers = $client->getAcceptNewsLetter() ? true : null;
    $addressNumber = $client->getAddressNumber();
    $addressExtension = $client->getAddressExtension();
    $streetType = $client->getStreetType();
    $addressWording = $client->getAddressWording();
    $postalCode = $client->getPostalCode();
    $town = $client->getTown();
}
else{
    $mode = 'create';

    $isSociety = false;
    $societyName = '';
    $siren = '';
    $siret = '';
    $civility = 'Mme';
    $lastName = '';
    $firstName = '';
    $phone = '';
    $mobile = '';
    $fax = '';
    $email = '';
    $acceptOffers = null;
    $addressNumber = '';
    $addressExtension = '';
    $streetType = '';
    $addressWording = '';
    $postalCode = '';
    $town = '';
}

?>

<div class="container">
    <h3 class="page-header">
        <?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> d'un client
    </h3>
    <br>
    <form action="/clients/creer" method="POST">
        <input type="hidden" name="<?php echo $mode; ?>Client[structureId]" value="<?php echo $user->getStructureId(); ?>">
        <input type="hidden" name="<?php echo $mode; ?>Client[clientId]" value="<?php echo $clientId; ?>">
        <div class="row">
            <div class="form-group">
                <div class="col-lg-12">
                    <label class="radio-inline">
                        <input type="radio" class="radio-for-isSociety" name="<?php echo $mode; ?>Client[isSociety]"
                               value="0" <?php echo !$isSociety ? 'checked' : ''; ?>> Client particulier
                    </label>
                    <label class="radio-inline">
                        <input type="radio" class="radio-for-isSociety" name="<?php echo $mode; ?>Client[isSociety]"
                               value="1" <?php echo $isSociety ? 'checked' : ''; ?>> Client société
                    </label>
                </div>
            </div>
        </div>
        <div class="row rowForSociety">
            <div class="form-group">
                <label for="inputClientName" class="col-lg-2 form-control-label">Nom de la société :</label>
                <div class="col-lg-10">
                    <input type="text" class="form-control" id="inputClientName"
                           name="<?php echo $mode; ?>Client[societyName]" value="<?php echo $societyName; ?>">
                </div>
            </div>
        </div>
        <div class="row rowForSociety">
            <div class="form-group">
                <label for="inputClientSiren" class="col-lg-2 form-control-label">Siren :</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="inputClientSiren"
                           name="<?php echo $mode; ?>Client[siren]" value="<?php echo $siren; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputClientSiret" class="col-lg-2 form-control-label">Siret :</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="inputClientSiret"
                           name="<?php echo $mode; ?>Client[siret]" value="<?php echo $siret; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label class="col-lg-2 form-control-label">Cvilité :</label>
                <div class="col-lg-2">
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Client[civility]"
                               value="Mme" <?php echo $civility == 'Mme' ? 'checked' : ''; ?>> Mme.
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Client[civility]"
                               value="M" <?php echo $civility == 'M' ? 'checked' : ''; ?>> M.
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="inputClientLastName" class="col-lg-2 form-control-label">Nom :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputClientLastName"
                           name="<?php echo $mode; ?>Client[lastName]" value="<?php echo $lastName; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputClientFirstName" class="col-lg-2 form-control-label">Prénom :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputClientFirstName"
                           name="<?php echo $mode; ?>Client[firstName]" value="<?php echo $firstName; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputClientPhone" class="col-lg-2 form-control-label">Téléphone :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputClientPhone"
                           name="<?php echo $mode; ?>Client[phone]" value="<?php echo $phone; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputClientMobile" class="col-lg-2 form-control-label">Portable :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputClientMobile"
                           name="<?php echo $mode; ?>Client[mobile]" value="<?php echo $mobile; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputClientFax" class="col-lg-2 form-control-label">Fax :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputClientFax"
                           name="<?php echo $mode; ?>Client[fax]" value="<?php echo $fax; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputClientMail" class="col-lg-2 form-control-label">E-mail :</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="inputClientMail"
                           name="<?php echo $mode; ?>Client[email]" value="<?php echo $email; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 form-control-label">Accepte les offres mail :</label>
                <div class="col-lg-2">
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Client[acceptOffers]" value="0" required> Non
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Client[acceptOffers]" value="1"
                            <?php echo $acceptOffers ? 'checked' : ''; ?> required> Oui
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputClientAddressNumber" class="col-lg-2 form-control-label">N° :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputClientAddressNumber"
                           name="<?php echo $mode; ?>Client[addressNumber]" value="<?php echo $addressNumber; ?>"
                           placeholder="15">
                </div>
            </div>
            <div class="form-group">
                <label for="inputClientAddressExtension" class="col-lg-2 form-control-label">Extension :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputClientAddressExtension"
                           name="<?php echo $mode; ?>Client[addressExtension]" value="<?php echo $addressExtension; ?>"
                           placeholder="Bis">
                </div>
            </div>
            <div class="form-group">
                <label for="inputClientStreetType" class="col-lg-2 form-control-label">Type de voie :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputClientStreetType"
                           name="<?php echo $mode; ?>Client[streetType]" value="<?php echo $streetType; ?>"
                           placeholder="Rue">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputClientAddressWording" class="col-lg-2 form-control-label">Libellé :</label>
                <div class="col-lg-10">
                    <input type="text" id="inputClientAddressWording" class="form-control"
                           name="<?php echo $mode; ?>Client[addressWording]" value="<?php echo $addressWording; ?>"
                           placeholder="Général de Gaulle">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputClientPostalCode" class="col-lg-2 form-control-label">Code Postal :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputClientPostalCode" class="form-control"
                           name="<?php echo $mode; ?>Client[postalCode]" value="<?php echo $postalCode; ?>"
                           placeholder="75000">
                </div>
            </div>
            <div class="form-group">
                <label for="inputClientTown" class="col-lg-2 form-control-label">Ville :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputClientTown" class="form-control"
                           name="<?php echo $mode; ?>Client[town]" value="<?php echo $town; ?>"
                           placeholder="Paris">
                </div>
            </div>
        </div>
        <br>
        <div class="form-group row">
            <div class="col-md-2 col-md-offset-8">
                <button type="submit" class="btn btn-primary btn-principalColor formManager"
                        data-formManager="submitInput">
                    <?php echo $mode == 'create' ? 'Ajouter' : 'Modifier'; ?> le client
                </button>
            </div>
        </div>
    </form>
</div>
