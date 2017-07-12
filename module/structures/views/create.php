<?php
try{
    if($_GET['action'] == 'modify' && !empty($_GET['structureId'])){
        $structureId = (int)$_GET['structureId'];
        $db = databaseConnection();
        $structure = \Users\StructureManager::fetchStructure($db, $structureId);
        if(is_a($structure, '\Exception')){
            throw $structure;
        }
        $firstUser = \Users\UserManager::fetchAdminOfStructure($db, $structureId);
        if(is_a($firstUser, '\Exception')){
            throw $firstUser;
        }
        $db = null;

        $name = $structure->getStructureName();
        $address = $structure->getAddress();
        $postalCode = $structure->getPostalCode();
        $town = $structure->getTown();
        $phone = $structure->getPhone();
        $fax = $structure->getFax();
        $email = $structure->getEmail();
        $siret = $structure->getSiret();
        $ape = $structure->getApe();
        $isPartner = $structure->getIsPartner();
        $acceptNewsLetter = $structure->getAcceptNewsLetter();
        $societyImage = '';
        $packageContent = $structure->getPackageContent();
        $packageProvision = $structure->getPackageProvision();
        $defaultMargin = $structure->getDefaultMargin();
        $freightCharges = $structure->getFreightCharges() === null ? '' : (int)$structure->getFreightCharges();
        $defaultWarranty = $structure->getDefaultWarranty();
        $defaultFunding = $structure->getDefaultFunding();
        $firstUserCivility = $firstUser->getCivility();
        $firstUserFirstName = $firstUser->getFirstName();
        $firstUserLastName = $firstUser->getLastName();
        $firstUserEmail = $firstUser->getEmail();
        $mode = 'modify';

        //Récupération des droits afin de voir si il peut modifier le partenaire ou pas
        /** @var \Users\User $user */
        $user = $_SESSION['user'];
        $rights = $user->getRights();
    }
    else{
        $name = '';
        $address = '';
        $postalCode = '';
        $town = '';
        $phone = '';
        $fax = '';
        $email = '';
        $siret = '';
        $ape = '';
        $isPartner = false;
        $acceptNewsLetter = false;
        $societyImage = '';
        $packageContent = 'L\'immatriculation provisoire, 1/3 du carburant et la préparation du véhicule ainsi que les démarches administratives du véhicule.';
        $packageProvision = '190';
        $defaultMargin = '400';
        $freightCharges = '';
        $defaultWarranty = '';
        $defaultFunding = '';
        $firstUserCivility = 'M';
        $firstUserFirstName = '';
        $firstUserLastName = '';
        $firstUserEmail = '';
        $mode = 'create';
    }
?>

<div class="container">
    <h3 class="page-header">
        <?php echo $mode == 'create' ? 'Création' : 'Modification'; ?> d'une structure
    </h3>
    <br>
    <form action="/structures/visualiser" method="POST" enctype="multipart/form-data">
        <?php
        if($mode == 'modify'):
        ?>
        <input type="hidden" name="<?php echo $mode; ?>Structure[id]" value="<?php echo $structureId; ?>">
        <?php
        endif;
        ?>
        <div class="row">
            <div class="form-group">
                <label for="inputStructureName" class="col-lg-2 form-control-label">Nom de la structure* :</label>
                <div class="col-lg-10">
                    <input type="text" id="inputStructureName" name="<?php echo $mode; ?>Structure[name]"
                           class="form-control formManager" data-formManager="required" value="<?php echo $name; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputStructureAddress" class="col-lg-2 form-control-label">Adresse* :</label>
                <div class="col-lg-10">
                    <input type="text" id="inputStructureName" name="<?php echo $mode; ?>Structure[address]"
                           class="form-control formManager" data-formManager="required" value="<?php echo $address; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputStructurePostalCode" class="col-lg-2 form-control-label">Code Postal* :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputStructurePostalCode" name="<?php echo $mode; ?>Structure[postalCode]"
                           class="form-control formManager" data-formManager="required integer length"
                           data-formManager-length="5" value="<?php echo $postalCode; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputStructureTown" class="col-lg-2 form-control-label">Ville* :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputStructureTown" name="<?php echo $mode; ?>Structure[town]"
                           class="form-control formManager" data-formManager="required" value="<?php echo $town; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputStructurePhone" class="col-lg-2 form-control-label">Tél.* :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputStructurePhone" name="<?php echo $mode; ?>Structure[phone]"
                           class="form-control formManager" data-formManager="required integer length"
                           data-formManager-length="10" value="<?php echo $phone; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputStructureFax" class="col-lg-2 form-control-label">Fax :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputStructureFax" name="<?php echo $mode; ?>Structure[fax]"
                           class="form-control formManager" data-formManager="optional integer length"
                           data-formManager-length="10" value="<?php echo $fax; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputStructureEmail" class="col-lg-2 form-control-label">Email* :</label>
                <div class="col-lg-10">
                    <input type="email" id="inputStructureEmail" name="<?php echo $mode; ?>Structure[email]"
                           class="form-control formManager" data-formManager="required mail" value="<?php echo $email; ?>" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputStructureSiret" class="col-lg-2 form-control-label">SIRET :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputStructureSiret" name="<?php echo $mode; ?>Structure[siret]"
                           class="form-control" value="<?php echo $siret; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputStructureApe" class="col-lg-2 form-control-label">APE :</label>
                <div class="col-lg-4">
                    <input type="text" id="inputStructureApe" name="<?php echo $mode; ?>Structure[ape]"
                           class="form-control" value="<?php echo $ape; ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label class="col-lg-2 form-control-label">Accepte les offres* :</label>
                <div class="col-lg-4">
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Structure[acceptNewsLetter]"
                               value="0" <?php echo $acceptNewsLetter ? '' : 'checked'; ?>>Non
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Structure[acceptNewsLetter]"
                               value="1" <?php echo $acceptNewsLetter ? 'checked' : ''; ?>>Oui
                    </label>
                </div>
            </div>
            <?php
            if(($mode == 'modify' && $rights->getCreateStructure()) || $mode == 'create'):
            ?>
            <div class="form-group">
                <label class="col-lg-2 form-control-label">Partenaire avngrp* :</label>
                <div class="col-lg-4">
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Structure[isPartner]"
                               value="0" <?php echo $isPartner ? '' : 'checked'; ?>>Non
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="<?php echo $mode; ?>Structure[isPartner]"
                               value="1" <?php echo $isPartner ? 'checked' : ''; ?>>Oui
                    </label>
                </div>
            </div>
            <?php
            endif;
            ?>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputStructureImage" class="col-lg-2 form-control-label">Image :</label>
                <div class="col-lg-4">
                    <label class="file">
                        <input type="file" id="inputVehicleImageNewImage" name="<?php echo $mode; ?>StructureImage">
                        <span class="file-custom"></span>
                    </label>
                </div>
                <label class="form-control-label col-lg-6">Vous devez choisir un fichier de type .png, .jpg, .jpeg ou .gif</label>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputStructurePackageContent" class="col-lg-2 form-control-label">Le forfait de mise à disposition comprend :</label>
                <div class="col-lg-4">
                    <textarea name="<?php echo $mode; ?>Structure[packageContent]" id="inputStructurePackageContent"
                              cols="30" rows="5"
                              class="form-control"><?php echo $packageContent; ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="inputStructurePackageProvision" class="col-lg-2 form-control-label">Montant du forfait* :</label>
                <div class="col-lg-4 input-group">
                    <input type="text" id="inputStructurePackageProvision" class="form-control formManager"
                           name="<?php echo $mode; ?>Structure[packageProvision]" data-formManager="required float"
                           value="<?php echo $packageProvision; ?>">
                    <span class="input-group-addon">€ HT</span>
                </div>
            </div>
            <div class="form-group">
                <label for="inputStructureDefaultMargin" class="col-lg-2 form-control-label">Commission par défaut* :</label>
                <div class="col-lg-4 input-group">
                    <input type="text" id="inputStructureDefaultMargin" class="form-control formManager"
                           name="<?php echo $mode; ?>Structure[defaultMargin]" value="<?php echo $defaultMargin; ?>"
                           data-formManager="required integer">
                    <span class="input-group-addon">€ HT</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <label for="inputStructureFreightCharges" class="col-lg-6 form-control-label">Frais de transport du 68127 vers votre ville :<br><small>Laissez vide si vous souhaitez utiliser notre base de transporteur</small></label>
                <div class="col-lg-6 input-group">
                    <input type="text" id="inputStructureFreightCharges" class="form-control formManager"
                           name="<?php echo $mode; ?>Structure[freightCharges]" value="<?php echo $freightCharges; ?>"
                           data-formManager="">
                    <span class="input-group-addon">€ HT</span>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="form-group">
                <label for="inputStructureWarranty" class="col-lg-2 form-control-label">Garantie par défaut :</label>
                <div class="col-lg-4">
                    <textarea name="<?php echo $mode; ?>Structure[warranty]" id="inputStructureWarranty"
                              cols="30" rows="5"
                              class="form-control"><?php echo $defaultWarranty; ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="inputStructureFunding" class="col-lg-2 form-control-label">Financement par défaut :</label>
                <div class="col-lg-4">
                    <textarea name="<?php echo $mode; ?>Structure[funding]" id="inputStructureFunding"
                              cols="30" rows="5"
                              class="form-control"><?php echo $defaultFunding; ?></textarea>
                </div>
            </div>
        </div>
        <br>
        <?php
        //if($mode == 'create'):
        ?>
            <div class="row">

                <div class="form-group">
                    <label for="inputStructureOwnerFirstName" class="col-lg-2 form-control-label">Prénom* :</label>
                    <div class="col-lg-2">
                        <input type="text" class="form-control formManager" id="inputStructureOwnerFirstName"
                               name="<?php echo $mode; ?>Structure[firstName]" data-formManager="required pureString"
                               value="<?php echo $firstUserFirstName; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputStructureOwnerLastName" class="col-lg-2 form-control-label">Nom* :</label>
                    <div class="col-lg-2">
                        <input type="text" class="form-control formManager" id="inputStructureOwnerLastName"
                               name="<?php echo $mode; ?>Structure[lastName]" data-formManager="required pureString"
                               value="<?php echo $firstUserLastName; ?>" required>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="form-group">
                    <label for="inputStructureOwnerEmail" class="col-lg-2 form-control-label">Email* :</label>
                    <div class="col-lg-2">
                        <input type="text" class="form-control formManager" id="inputStructureOwnerEmail"
                               name="<?php echo $mode; ?>Structure[ownerEmail]" data-formManager="required"
                               value="<?php echo $firstUserEmail; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputStructureOwnerPassword" class="col-lg-2 form-control-label">Mot de passe* :</label>
                    <div class="col-lg-2">
                        <input type="password" class="form-control formManager" id="inputStructureOwnerPassword"
                               name="<?php echo $mode; ?>Structure[password]"
                               <?php echo $mode == 'create' ? 'data-formManager="required" required' : ''; ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputStructureOwnerConfPass" class="col-lg-2 form-control-label">Répétez* :</label>
                    <div class="col-lg-2">
                        <input type="password" class="form-control formManager" id="inputStructureOwnerConfPass"
                               name="<?php echo $mode; ?>Structure[confPass]"
                                <?php echo $mode == 'create' ? 'data-formManager="required sameAs" data-formManager-sameAs="inputStructureOwnerPassword" required' : ''; ?>>
                    </div>
                </div>
            </div>
            <br>
        <?php
        //endif;
        ?>
        <br>
        <div class="form-group row">
            <div class="col-lg-2 col-lg-offset-8">
                <button type="submit" class="btn btn-primary btn-principalColor formManager"
                        data-formManager="submitInput">
                    <?php echo $mode == 'create' ? 'Créer' : 'Modifier'; ?> la structure
                </button>
            </div>
        </div>
        <br><br>
    </form>
</div>
<?php
}
catch(Exception $e){
    msgReturn_push(array(0, $e->getMessage()));
}