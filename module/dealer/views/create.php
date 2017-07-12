<?php
// TODO : Attention, le formulaire ici est clairement provisoire, il va manquer bcp d'informations, à retoucher dans le futur donc

$db = databaseConnection();
$countriesList = \Prices\CountryManager::fetchCountriesList($db);
$db = null;

if($_GET['action'] == 'modify' && !empty($_GET['dealerId'])){
    $dealerId = (int)$_GET['dealerId'];
    $db = databaseConnection();
    $dealer = \Prices\DealerManager::fetchDealer($db, $dealerId);
    $db = null;

    $mode = 'modify';
}
else{
    $dealer = new \Prices\Dealer();

    $mode = 'create';
}

$name = $dealer->getName();
$countryId = $dealer->getCountryId();
$address = $dealer->getAddress();
$addressNumber = !empty($address->getNumber()) ? $address->getNumber() : '';
$addressExtension = $address->getExtension();
$streetType = $address->getStreetType();
$addressWording = $address->getWording();
$postalCode = $address->getPostalCode();
$town = $address->getTown();
$societyPhone = $dealer->getPhone();
$societyFax = $dealer->getFax();
$societyMail = $dealer->getEmail();
$contact = $dealer->getContact();
$civility = $contact->getCivility();
$firstName = $contact->getFirstName();
$lastName = $contact->getLastName();
$phone = $contact->getPhone();
$mobile = $contact->getMobile();
$email = $contact->getEmail();
$comments = $dealer->getComments();
$acceptOffers = $dealer->getAcceptNewsLetter();

?>

<div class="container">
    <h3 class="page-header">
        <?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> d'un fournisseur
    </h3>
    <br>
    <form action="/fournisseurs/visualiser" method="POST">
        <?php
        if($mode == 'modify'):
            ?>
            <input type="hidden" name="<?php echo $mode; ?>Dealer[id]" value="<?php echo $dealerId; ?>">
            <?php
        endif;
        ?>
        <div class="row">
            <div class="form-group">
                <label for="inputDealerName" class="col-lg-2 form-control-label">Nom du fournisseur* :</label>
                <div class="col-lg-10">
                    <input type="text" id="inputDealerName" name="<?php echo $mode; ?>Dealer[name]"
                           class="form-control formManager" data-formManager="required" value="<?php echo $name; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="selectDealerSymbol" class="col-lg-2 form-control-label">Pays* :</label>
                <div class="col-lg-4">
                    <select id="selectDealerSymbol" name="<?php echo $mode; ?>Dealer[country]"
                           class="form-control">
                        <option value="0" <?php echo $countryId == 0 ? 'selected' : ''; ?>>Choisissez un pays</option>
                        <?php
                        foreach($countriesList as $country):
                        ?>
                            <option value="<?php echo $country->getId(); ?>"
                                <?php echo $country->getId() == $countryId ? 'selected' : ''; ?>>
                                <?php echo $country->getName().' ('.$country->getAbbreviation().')'; ?>
                            </option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputDealerAddressNumber" class="col-lg-2 form-control-label">N° :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputDealerAddressNumber"
                           name="<?php echo $mode; ?>Dealer[addressNumber]" value="<?php echo $addressNumber; ?>"
                           placeholder="15">
                </div>
            </div>
            <div class="form-group">
                <label for="inputDealerAddressExtension" class="col-lg-2 form-control-label">Extension :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputDealerAddressExtension"
                           name="<?php echo $mode; ?>Dealer[addressExtension]" value="<?php echo $addressExtension; ?>"
                           placeholder="Bis">
                </div>
            </div>
            <div class="form-group">
                <label for="inputDealerStreetType" class="col-lg-2 form-control-label">Type de voie :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputDealerStreetType"
                           name="<?php echo $mode; ?>Dealer[streetType]" value="<?php echo $streetType; ?>"
                           placeholder="Rue">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputDealerAddressWording" class="col-lg-2 form-control-label">Libellé :</label>
                <div class="col-lg-10">
                    <input type="text" id="inputDealerAddressWording" class="form-control"
                           name="<?php echo $mode; ?>Dealer[addressWording]" value="<?php echo $addressWording; ?>"
                           placeholder="Général de Gaulle">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputDealerPostalCode" class="col-lg-2 form-control-label">Code Postal :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputDealerPostalCode" class="form-control"
                           name="<?php echo $mode; ?>Dealer[postalCode]" value="<?php echo $postalCode; ?>"
                           placeholder="75000">
                </div>
            </div>
            <div class="form-group">
                <label for="inputDealerTown" class="col-lg-2 form-control-label">Ville :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputDealerTown" class="form-control"
                           name="<?php echo $mode; ?>Dealer[town]" value="<?php echo $town; ?>"
                           placeholder="Paris">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputDealerPhone" class="col-lg-2 form-control-label">Téléphone :</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="inputDealerPhone"
                           name="<?php echo $mode; ?>Dealer[societyPhone]" value="<?php echo $societyPhone; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputDealerFax" class="col-lg-2 form-control-label">Fax :</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="inputDealerFax"
                           name="<?php echo $mode; ?>Dealer[societyFax]" value="<?php echo $societyFax; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputDealerMail" class="col-lg-2 form-control-label">E-mail :</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="inputDealerMail"
                           name="<?php echo $mode; ?>Dealer[societyEmail]" value="<?php echo $societyMail; ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 form-control-label">Accepte les offres mail :</label>
                <div class="col-lg-2">
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Dealer[acceptOffers]" value="0" required> Non
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Dealer[acceptOffers]" value="1"
                            <?php echo $acceptOffers ? 'checked' : ''; ?> required> Oui
                    </label>
                </div>
            </div>
        </div>
        <br><br>

        <h4 class="page-header">Données du contact :</h4>
        <div class="row">
            <div class="form-group">
                <label class="col-lg-2 form-control-label">Civilité :</label>
                <div class="col-lg-2">
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Dealer[civility]"
                               value="Mme" <?php echo $civility == 'Mme' ? 'checked' : ''; ?>> Mme.
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Dealer[civility]"
                               value="M" <?php echo $civility == 'M' ? 'checked' : ''; ?>> M.
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="inputDealerLastName" class="col-lg-2 form-control-label">Nom :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputDealerLastName"
                           name="<?php echo $mode; ?>Dealer[lastName]" value="<?php echo $lastName; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputDealerFirstName" class="col-lg-2 form-control-label">Prénom :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputDealerFirstName"
                           name="<?php echo $mode; ?>Dealer[firstName]" value="<?php echo $firstName; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputDealerPhone" class="col-lg-2 form-control-label">Téléphone :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputDealerPhone"
                           name="<?php echo $mode; ?>Dealer[phone]" value="<?php echo $phone; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputDealerMobile" class="col-lg-2 form-control-label">Portable :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputDealerMobile"
                           name="<?php echo $mode; ?>Dealer[mobile]" value="<?php echo $mobile; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputDealerMail" class="col-lg-2 form-control-label">E-mail :</label>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id="inputDealerMail"
                           name="<?php echo $mode; ?>Dealer[email]" value="<?php echo $email; ?>">
                </div>
            </div>
        </div>

        <br><br>
        <div class="row">
            <div class="form-group">
                <label for="inputDealerComments" class="col-lg-2 form-control-label">Commentaires :</label>
                <div class="col-lg-10">
                    <textarea name="<?php echo $mode; ?>Dealer[comments]" id="inputDealerComments" cols="30" rows="7"
                              class="form-control"><?php echo $comments; ?></textarea>
                </div>
            </div>
        </div>

        <br><br>
        <div class="form-group row">
            <div class="col-lg-2 col-lg-offset-8">
                <button type="submit" class="btn btn-primary btn-principalColor formManager"
                        data-formManager="submitInput">
                    <?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> le fournisseur
                </button>
            </div>
        </div>
    </form>
</div>
