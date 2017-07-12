<?php

/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 08/03/2016
 * Time: 14:46
 */
class OfferPDF extends PDF
{
    protected $offerReference, $offer, $client, $vehicle, $priceDetails;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param string $offerReference
     */
    public function setOfferReference(\string $offerReference){
        $this->offerReference = $offerReference;
    }

    /**
     * @return string
     */
    public function getOfferReference():\string{
        return $this->offerReference;
    }

    /**
     * @param \Offers\Offer $offer
     */
    public function setOffer(Offers\Offer $offer){
        $this->offer = $offer;
    }

    /**
     * @return \Offers\Offer
     */
    public function getOffer():\Offers\Offer{
        return $this->offer;
    }

    /**
     * @param \Users\Client $client
     */
    public function setClient(\Users\Client $client){
        $this->client = $client;
    }

    /**
     * @return \Users\Client
     */
    public function getClient():\Users\Client{
        return $this->client;
    }

    /**
     * @param \Vehicle\Details $vehicle
     */
    public function setVehicle(\Vehicle\Details $vehicle){
        $this->vehicle = $vehicle;
    }

    /**
     * @return \Vehicle\Details
     */
    public function getVehicle():\Vehicle\Details{
        return $this->vehicle;
    }

    /**
     * @param \Prices\PriceDetails $priceDetails
     */
    public function setPriceDetails(\Prices\PriceDetails $priceDetails){
        $this->priceDetails = $priceDetails;
    }

    /**
     * @return \Prices\PriceDetails
     */
    public function getPriceDetails():\Prices\PriceDetails{
        return $this->priceDetails;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * PDF constructor.
     *
     * @param string $offerReference
     */
    public function __construct(\string $offerReference)
    {
        $db = databaseConnection();
        $offer = \Offers\OfferManager::fetchOfferByReference($db, $offerReference);
        if(is_a($offer, '\Exception')){
            $db = null;
            echo $offer->getMessage();
            die();
        }
        $user = \Users\UserManager::fetchUser($db, $offer->getOwnerId());
        if(is_a($user, '\Exception')){
            $db = null;
            echo $user->getMessage();
            die();
        }
        $client = \Users\ClientManager::fetchClient($db, $offer->getClientId());
        if(is_a($client, '\Exception')){
            $db = null;
            echo $client->getMessage();
            die();
        }
        $vehicle = \Vehicle\DetailsManager::fetchCompleteDetails($db, $offer->getVehicleId());
        if(is_a($vehicle, '\Exception')){
            $db = null;
            echo $vehicle->getMessage();
            die();
        }

        $dealer = $structure = $user->getStructure();
        $dealerDepartment = $dealer->getDepartment();
        $clientDepartment = empty($client->getPostalCode()) ? $dealerDepartment : $client->getDepartment();
        $horsepowerPrice = \Prices\HorsepowerPriceManager::fetchPriceFromDepartment($db, $clientDepartment);
        $db = null;

        parent::__construct($structure, $user);

        $this->setOffer($offer);
        $this->setClient($client);
        $this->setVehicle($vehicle);

        //Définition du prix pour décentralisation de l'affichage
        $optionalEquipments = $offer->getOptions();
        $packs = $offer->getPacks();
        $color = $offer->getColor();
        $rims = $offer->getRims();

        $postTaxesOptionPrice = 0;
        if(!empty($optionalEquipments)){
            foreach($optionalEquipments as $equipment){
                $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($equipment->getPrice(), $offer->getVatRate()), 2);
            }
        }
        if(!empty($packs)){
            foreach($packs as $pack){
                $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($pack->getPrice(), $offer->getVatRate()), 2);
            }
        }
        $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($color->getPrice(), $offer->getVatRate()), 2);
        $postTaxesOptionPrice += round(\Prices\VatManager::convertToPostTaxes($rims->getPrice(), $offer->getVatRate()), 2);
        $vatAmount = $offer->getVatRate();
        $buyingPrice = $offer->getVehiclePrice() - $offer->getFreightChargesToFrance();
        $changeRateToEuro = 1;
        $freightChargesToFrance = $offer->getFreightChargesToFrance();
        $marginAmount = $offer->getMarginAmount();
        $marginPercentage = 0;
        $managementFees = $offer->getManagementFees();
        $freightChargesInFrance = $offer->getFreightChargesInFrance();
        $dealerMargin = $offer->getDealerMargin();
        $packageProvision = $structure->getPackageProvision();
        $optionPrice = \Prices\VatManager::convertToPretax($postTaxesOptionPrice, $vatAmount);
        $registrationCardAmount = $horsepowerPrice->getRegistrationCardAmount($vehicle->getFiscalPower());
        $bonusPenalty = 0;

        $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
                                                 $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty);

        $this->setPriceDetails($priceDetails);
        $this->setOfferReference($offerReference);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @param string        $offerReference
     */
    public function printProtagonistInformation(string $offerReference){
        $seller = $this->getUser();
        $sellerStructure = $this->getStructure();
        $client = $this->getClient();

        $sellerPhone = !empty($seller->getPhone()) ? $seller->getPhone() : $seller->getStructure()->getPhone();
        $sellerMail = !empty($seller->getEmail()) ? $seller->getEmail() : $seller->getStructure()->getEmail();
        $clientPhone = !empty($client->getMobile()) ? $client->getMobile() : $client->getPhone();

        $this->SetFont('Arial', 'U', 12);
        $this->Cell(120, 12, utf8_decode('Votre conseiller :'));
        $this->SetFont('Arial', '', 12);
        $this->Cell(80, 12, utf8_decode('Offre N°'.$this->getOfferReference()));
        if($sellerStructure->getIsPartner()){
            $this->Ln($this->regularLn);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(120, 12, utf8_decode($sellerStructure->getStructureName()));
            $this->SetFont('Arial', '', 12);

            $this->Ln($this->regularLn);
        }
        else{
            $this->Ln($this->largeLn);
        }
        $this->Cell(120, 12, utf8_decode($seller->getCivility().' '.$seller->getLastName().' '.$seller->getFirstName()));
        $this->Cell(80, 12, utf8_decode($client->getCivility().' '.$client->getLastName().' '.$client->getFirstName()));
        $this->Ln($this->regularLn);
        $this->Cell(120, 12, utf8_decode('Tél. : '.getPhoneNumber($sellerPhone)));
        $this->Cell(80, 12, utf8_decode($client->getStreetAddress()));
        $this->Ln($this->regularLn);
        $this->Cell(120, 12, '');
        $this->Cell(80, 12, utf8_decode($client->getPostalCode().' '.$client->getTown()));
        $this->Ln($this->regularLn);
        $this->Cell(120, 12,utf8_decode('Mail : '.$sellerMail));
        $this->Cell(80, 12, utf8_decode('Tél. : '.getPhoneNumber($clientPhone)));
    }

    public function printBlocVehicleWithImage(){
        $vehicle = $this->getVehicle();
        $db = databaseConnection();
        $colorItem = \Vehicle\ExternalColorManager::fetchColor($db, $this->getOffer()->getColor()->getItemId());
        $db = null;
        if(is_a($colorItem, '\Exception')){
            $color = 'De série';
        }
        else{
            $color = $colorItem->getName().' '.$colorItem->getDetails();
        }

        $lineDefVehicle = $this->getLineDefVehicle();

        $finish = $vehicle->getFinish();
        $finishName = $finish->getName();
        $model = $finish->getModel();
        $modelName = $model->getName();
        $brand = $model->getBrand();
        $brandName = $brand->getName();

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Votre véhicule'), 'B');
        $this->Ln($this->largeLn);
        $this->Ln($this->smallLn);
        $this->SetFont('Arial', '', 12);
        $this->WriteHTML($lineDefVehicle);
        $this->Ln($this->largeLn);
        $this->Cell(50, 12, utf8_decode('Carrosserie :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getBodywork()->getName()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Nb Portes/Places :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getDoorsAmount().' portes/ '.$vehicle->getSitsAmount().' places'));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Type :'));
        $this->Cell(50, 12, utf8_decode('VP / Véhicule Neuf (0 à 60 kms)'));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Couleur :'));
        $this->Cell(50, 12, utf8_decode($color));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Carburant :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getFuel()->getName()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Puissance Fiscale :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getFiscalPower().' CV'));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Boite vitesse :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getGearbox()->getName()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Disponibilité** :'));
        $this->Cell(50, 12, utf8_decode('Commande usine'));
        $this->SetY(125);
        $this->SetFont('Arial', '', 9);
        $this->Cell(185, 12, utf8_decode('Photo non contractuelle'), '', 0, 'R');
        $imagePath = '/ressources/vehicleImages/'.$brand->getName().'/'.$model->getName().'/'.$finish->getName().'/'.$vehicle->getBodywork()->getName().'_'.$vehicle->getDoorsAmount().'.png';
        if(!is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
            try{
                $imagePath = '/ressources/vehicleImages/'.$brand->getName().'/'.$model->getName().'/'.$finish->getName().'/'.$vehicle->getBodywork()->getName().'_'.$vehicle->getDoorsAmount().'.jpg';
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 135, 75, 0, 'JPG');
            }
            catch(Exception $e){
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 135, 75, 0, 'PNG');
            }
        }
        else{
            $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 135, 75, 0, 'PNG');
        }

        $this->SetY(185);
        $this->SetFont('Arial', '', 12);
        $this->Cell(185, 12, utf8_decode('Prix du véhicule (Hors option) : '.number_format($this->getPriceDetails()->getPostTaxesDealerSellingPrice(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->largeLn);
    }

    /**
     * @return string
     */
    public function getLineDefVehicle(){
        $vehicle = $this->getVehicle();
        $finish = $vehicle->getFinish();
        $finishName = $finish->getName();
        $model = $finish->getModel();
        $modelName = $model->getName();
        $brand = $model->getBrand();
        $brandName = $brand->getName();
        $engine = $vehicle->getEngine();
        $engineName = $engine->getName();

        return '<b>'.$brandName.' '.$modelName.' '.$vehicle->getEngineSize().' '.$engineName.' '.$vehicle->getDynamicalPower().' '.$finishName.'</b> ('.$vehicle->getCo2().'g Co2/km*)';
    }


    public function printBlocSerialEquipments(){
        $db = databaseConnection();
        $serialEquipments = \Vehicle\FinishManager::fetchSerialEquipments($db, $this->getVehicle()->getFinishId());
        $equipmentsList = \Vehicle\EquipmentManager::orderEquipmentsByFamily($db, $serialEquipments);
        $familiesList = \Vehicle\EquipmentManager::fetchFamiliesList($db);
        $db = null;

        $cellWidth = 28;
        $colWidth = 32;

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Equipements de série'), 'B');
        $this->Ln($this->largeLn);
        $this->Ln($this->smallLn);
        $this->SetFont('Arial', '', 12);
        //Pour imprimer les équipements de série en colonne, on va d'abord calculer l'index max
        $maxRows = 0;
        //Et on réordonne le tableau avec des clé numériques
        $numericKeysArray = array();
        foreach($familiesList as $family){
            if(!empty($equipmentsList[$family['name']])){
                $numericKeysArray[] = $equipmentsList[$family['name']];
                if(count($equipmentsList[$family['name']]) > $maxRows){
                    $maxRows = count($equipmentsList[$family['name']]);
                }
            }
        }

        $this->printHeaderEquipmentFamilies();
        //Puis on va parcourir le tableau élément par élément, et afficher l'équipement si il y en a un
        $this->SetFont('Arial', '', 9);
        $this->SetWidths(array($colWidth,$colWidth,$colWidth,$colWidth,$colWidth,$colWidth));
        for($i = 0; $i < $maxRows; $i++){
            $text = array();
            foreach($familiesList as $key => $family){
                if(!empty($equipmentsList[$family['name']][$i])){
                    /** @var \Vehicle\Equipment $equipment */
                    $equipment = $equipmentsList[$family['name']][$i];
                    $text[] = $equipment->getName();
                }
                else{
                    $text[] = '';
                }
            }
            $this->Row($text);
        }
    }

    public function printHeaderEquipmentFamilies(){
        $db = databaseConnection();
        $familiesList = \Vehicle\EquipmentManager::fetchFamiliesList($db);
        $db = null;

        $cellWidth = 24;
        $colWidth = 32;

        $this->SetFont('Arial', 'B', 10);
        $originY = $this->GetY();
        $originX = $this->GetX();
        foreach($familiesList as $key => $family){
            $this->SetY($originY);
            $this->SetX($originX + ($colWidth * $key));
            $this->MultiCell($cellWidth, 4, utf8_decode($family['name']));
        }
        $this->Ln(5);
    }

    public function printBlocOptionalEquipment(){
        $optionalEquipments = $this->getOffer()->getOptions();

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Equipements en option'), 'T');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 12);
        if(!is_a($optionalEquipments, '\Exception')){
            foreach($optionalEquipments as $equipment){
                /** @var \Offers\Option $equipment */
                $equipment = $equipment;
                $sellingPrice = \Prices\VatManager::convertToPostTaxes($equipment->getPrice(), $this->getOffer()->getVatRate());
                $db = databaseConnection();
                $equipmentInformation = \Vehicle\EquipmentManager::fetchEquipment($db, $equipment->getItemId());
                $db = null;

                $this->Cell(120, 12, utf8_decode($equipmentInformation->getName()));
                $this->Cell(65, 12, utf8_decode(round($sellingPrice, 2).' '.$this->chrEuro.' TTC'), '', 0, 'R');
                $this->Ln($this->regularLn);
            }
        }
    }

    public function printBlocPacks(){
        $packs = $this->getOffer()->getPacks();

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Packs'), 'T');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 12);
        if(!is_a($packs, '\Exception')){
            foreach($packs as $pack){
                /** @var \Offers\Pack $pack */
                $pack = $pack;
                $sellingPrice = \Prices\VatManager::convertToPostTaxes($pack->getPrice(), $this->getOffer()->getVatRate());
                $db = databaseConnection();
                $packInformation = \Vehicle\PackManager::fetchPack($db, $pack->getItemId());
                $equipmentsArray = \Vehicle\PackManager::fetchEquipments($db, $packInformation->getId());
                $color = \Vehicle\PackManager::fetchColor($db, $packInformation->getId());
                $rims = \Vehicle\PackManager::fetchRim($db, $packInformation->getId());
                $db = null;
                $equipmentsList = '';
                foreach($equipmentsArray as $key => $equipment){
                    if($key > 0){
                        $equipmentsList .= ', ';
                    }
                    $equipmentsList .= $equipment->getName();
                }
                $this->SetFont('Arial', 'B', 12);
                $packName = str_replace('&eacute;', 'é', $packInformation->getName());
                $this->Cell(120, 12, utf8_decode($packName));
                $this->SetFont('Arial', '', 12);
                $this->Cell(65, 12, utf8_decode(round($sellingPrice, 2).' '.$this->chrEuro.' TTC'), '', 0, 'R');
                $this->Ln($this->smallLn);
                if(!empty($equipmentsList)){
                    $this->Ln($this->regularLn);
                    $this->MultiCell(120, 5, utf8_decode($equipmentsList));
                }
                if(!is_a($color, '\Exception')){
                    $this->Ln($this->regularLn);
                    $this->Cell(120, 12, utf8_decode('Couleur : '.$color->getName().' '.$color->getDetails()));
                }
                if(!is_a($rims, '\Exception')){
                    $this->Ln($this->regularLn);
                    $this->Cell(120, 12, utf8_decode('Jantes : '.$rims->getName().' - '.$rims->getRimType().' '.$rims->getFrontDiameter().'"'));
                }
            }
        }
    }

    public function printBlocColor(){
        $color = $this->getOffer()->getColor();

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Couleur d\'extérieur'), 'T');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 12);

        if(!is_a($color, '\Exception')){
            $sellingPrice = \Prices\VatManager::convertToPostTaxes($color->getPrice(), $this->getOffer()->getVatRate());
            $db = databaseConnection();
            $colorInformation = \Vehicle\ExternalColorManager::fetchColor($db, $color->getItemId());
            $db = null;
            if(is_a($colorInformation, '\Exception')){
                $this->Cell(120, 12, utf8_decode('Couleur de série.'));
            }
            else{
                $this->Cell(120, 12, utf8_decode($colorInformation->getName().' '.$colorInformation->getDetails()));
                $this->Cell(65, 12, utf8_decode(number_format($sellingPrice, 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
            }
        }
    }

    public function printBlocRims(){
        $rims = $this->getOffer()->getRims();

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Jantes'), 'T');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 12);

        if(!is_a($rims, '\Exception')){
            $sellingPrice = \Prices\VatManager::convertToPostTaxes($rims->getPrice(), $this->getOffer()->getVatRate());
            $db = databaseConnection();
            $rimsInformation = \Vehicle\RimModelManager::fetchRimModel($db, $rims->getItemId());
            $db = null;
            if(is_a($rimsInformation, '\Exception')){
                $this->Cell(120, 12, utf8_decode('Jantes de série.'));
            }
            else{
                $this->Cell(120, 12, utf8_decode($rimsInformation->getRimType().' '.$rimsInformation->getFrontDiameter().' - '.$rimsInformation->getName()));
                $this->Cell(65, 12, utf8_decode(number_format($sellingPrice, 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
            }
        }
    }

    public function printBlocRecapPrice(){
        $priceDetails = $this->getPriceDetails();

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Total pour votre véhicule'), 'T');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 11);

        $leftPadding = 90;
        $wordingWidth = 65;
        $priceWidth = 30;
        $this->Cell($leftPadding, 12);
        $this->Cell($wordingWidth, 12, utf8_decode('Prix du véhicule (Hors option) :'));
        $this->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getPostTaxesDealerSellingPrice(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->regularLn);
        $this->Cell($leftPadding, 12);
        $this->Cell($wordingWidth, 12, utf8_decode('Total options :'));
        $this->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getPostTaxesOptionPrice(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->regularLn);
        $this->Cell($leftPadding, 12);
        $this->Cell($wordingWidth, 12, utf8_decode('Forfait de Mise à disposition :'));
        $this->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getPostTaxesPackageProvision(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->largeLn);
        $this->Cell($leftPadding, 12);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($wordingWidth, 8, utf8_decode('Montant total :'), 'TLB');
        $this->SetFont('Arial', '', 12);
        $this->Cell($priceWidth, 8, utf8_decode(number_format($priceDetails->getPostTaxesAllIncludedPrice(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), 'TRB', 0, 'R');
        $this->Ln($this->largeLn);
        $this->Cell($leftPadding, 12);
        $this->Cell($wordingWidth, 12, utf8_decode('Bonus/Malus*** :'));
        $this->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getBonusPenalty(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->regularLn);
        $this->Cell($leftPadding, 12);
        $this->Cell($wordingWidth, 12, utf8_decode('Carte Grise*** :'));
        $this->Cell($priceWidth, 12, utf8_decode(number_format($priceDetails->getRegistrationCardAmount(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->largeLn);
        $this->Cell($leftPadding, 12);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($wordingWidth, 8, utf8_decode('Total clé en mains**** :'), 'TLB');
        $this->SetFont('Arial', '', 12);
        $this->Cell($priceWidth, 8, utf8_decode(number_format($priceDetails->getPostTaxesKeyInHandPrice(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), 'TRB', 0, 'R');
        $this->Ln($this->largeLn);
        /*$this->Cell($leftPadding, 12);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($wordingWidth, 8, utf8_decode('Total clé en mains :'), 'TLB');
        $this->SetFont('Arial', '', 12);
        $this->Cell($priceWidth, 8, utf8_decode(number_format($priceDetails->getPostTaxesKeyInHandPrice(), 2, '.', ' ').' '.$this->chrEuro.' TTC***'), 'TRB', 0, 'R');
        $this->Ln($this->largeLn);*/

        $this->SetFont('Arial', 'I', 9);
        $this->Cell(185, 12, utf8_decode('*Taux donné à titre indicatif.'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('**Délai donné à titre indicatif.'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('***Sous réserve de modification des tarifs du cheval fiscal et du malus écologique en vigueur et de confirmation du taux de Co2.'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('****Sous réserve de disponibilité du véhicule.'));
        /*$this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('***Sous réserve de la disponibilité du véhicule.'));*/
    }

    public function printBlocRecapPackageContent(){
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Forfait de mise à disposition'), 'T');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 11);

        $this->MultiCell(185, 5, utf8_decode('Il comprend : '.$this->getStructure()->getPackageContent()));
        //$this->Cell(185, 12, utf8_decode('La carte grise définitive reste à la charge du client.'));
    }

    public function printBlocSignatures(){
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Fait à :'));
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', 'U', 12);
        $this->Cell(120, 12, utf8_decode('Date et signature du conseiller :'));
        $this->Cell(120, 12, utf8_decode('Date et signature du client :'));
    }
}