<?php

/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 16/03/2016
 * Time: 14:53
 */
class StockOfferPDF extends PDF
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
     * @param \Offers\StockOffer $offer
     */
    public function setOffer(Offers\StockOffer $offer){
        $this->offer = $offer;
    }

    /**
     * @return \Offers\StockOffer
     */
    public function getOffer():\Offers\StockOffer{
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
     * @param \Vehicle\VehicleInStock $vehicle
     */
    public function setVehicle(\Vehicle\VehicleInStock $vehicle){
        $this->vehicle = $vehicle;
    }

    /**
     * @return \Vehicle\VehicleInStock
     */
    public function getVehicle():\Vehicle\VehicleInStock{
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
        $offer = \Offers\StockOfferManager::fetchOfferByReference($db, $offerReference, true);
        if(is_a($offer, '\Exception')){
            $db = null;
            echo $offer->getMessage();
            die();
        }

        $user = $offer->getUser();
        $client = $offer->getClient();
        $vehicle = $offer->getVehicle();

        $dealer = $structure = $user->getStructure();
        $dealerDepartment = $dealer->getDepartment();
        $clientDepartment = empty($client->getPostalCode()) ? $dealerDepartment : $client->getDepartment();
        $horsepowerPrice = \Prices\HorsepowerPriceManager::fetchPriceFromDepartment($db, $clientDepartment);
        $vat = \Prices\VatManager::fetchFrenchVat($db);
        $db = null;

        parent::__construct($structure, $user);

        $this->setOffer($offer);
        $this->setClient($client);
        $this->setVehicle($vehicle);

        $vatAmount = $vat->getAmount();
        $buyingPrice = $offer->getVehiclePriceAmount();
        $changeRateToEuro = 1;
        $freightChargesToFrance = 0;
        $marginAmount = 0;
        $marginPercentage = 0;
        $managementFees = 0;
        $freightChargesInFrance = $offer->getFreightCharges();
        $dealerMargin = $offer->getDealerMargin();
        $packageProvision = $offer->getPackageProvision();
        $optionPrice = 0;
        $registrationCardAmount = $horsepowerPrice->getRegistrationCardAmount($vehicle->getFiscalPower());
        $bonusPenalty = 0;

        $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
                                                 $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty);

        $this->setPriceDetails($priceDetails);
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
        $this->Cell(80, 12, utf8_decode('Offre N°'.$offerReference));
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
        $this->Cell(80, 12, utf8_decode('Mail. : '.$client->getEmail()));
        $this->Ln($this->regularLn);
        $this->Cell(120, 12,utf8_decode('Tél. : '.getPhoneNumber($clientPhone)));
    }

    public function printBlocVehicleWithImage(){
        $vehicle = $this->getVehicle();
        $db = databaseConnection();
        $color = $vehicle->getExternalColor();

        $lineDefVehicle = $this->getLineDefVehicle();

        $finishName = $vehicle->getFinish();
        $modelName = $vehicle->getModel();
        $brandName = $vehicle->getBrand();

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Votre véhicule'), 'B');
        $this->Ln($this->largeLn);
        $this->Ln($this->smallLn);
        $this->SetFont('Arial', '', 12);
        $this->WriteHTML(utf8_decode($lineDefVehicle));
        $this->Ln($this->largeLn);
        $this->Cell(50, 12, utf8_decode('Carrosserie :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getBodywork()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Type :'));
        if($vehicle->isNewVehicle()){
            $this->Cell(50, 12, utf8_decode('VP / Véhicule Neuf'));
            $this->Ln($this->regularLn);
            $this->Cell(50, 12, '');
            $this->Cell(50, 12, utf8_decode('('.$vehicle->getMileage().'km)'));
        }
        else{
            $this->Cell(50, 12, utf8_decode('VP / Véhicule d\'occasion'));
            $this->Ln($this->regularLn);
            $this->Cell(50, 12, '');
            $this->Cell(50, 12, utf8_decode('('.$vehicle->getMileage().'km)'));
        }
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('DMC : '));
        $this->Cell(50, 12, utf8_decode($vehicle->getModelDate()->format('m/Y')));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Couleur :'));
        $this->Cell(50, 12, utf8_decode($color));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Carburant :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getFuel()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Puissance Fiscale :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getFiscalPower().' CV'));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Boite vitesse :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getGearbox()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Disponibilité** :'));
        if($vehicle->getIsArriving())
            $this->Cell(50, 12, utf8_decode('En arrivage'));
        else
            $this->Cell(50, 12, utf8_decode('Véhicule en stock'));
        $this->SetY(125);
        $this->SetFont('Arial', '', 9);
        $this->Cell(185, 12, utf8_decode('Photo non contractuelle'), '', 0, 'R');
        $imagePath = '/ressources/vehicleImages/VO/'.$vehicle->getImage1();
        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
            try{
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 140, 75, 0);
            }
            catch(Exception $e){
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 140, 75, 0, 'PNG');
            }
        }

        $this->SetY(195);
        $this->SetFont('Arial', '', 12);
        $this->Cell(185, 12, utf8_decode('Prix du véhicule (Hors option) : '.number_format($this->getPriceDetails()->getPostTaxesDealerSellingPrice(), 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
    }

    /**
     * @return string
     */
    public function getLineDefVehicle(){
        $vehicle = $this->getVehicle();
        $finishName = $vehicle->getFinish();
        $modelName = $vehicle->getModel();
        $brandName = $vehicle->getBrand();
        $engineName = $vehicle->getEngine();

        return '<b>'.$brandName.' '.$modelName.' '.$vehicle->getEngineSize().' '.$engineName.' '.$vehicle->getDynamicalPower().' '.$finishName.'</b> ('.$vehicle->getCo2().'g Co2/km*)';
    }

   public function printBlocEquipments(){
       $equipmentsList = implode(', ', $this->getVehicle()->getEquipmentsOrderedByName());
       $comments = $this->getVehicle()->getSuppComments();
       $this->SetFont('Arial', 'B', 12);
       $this->Cell(185, 8, utf8_decode('Principaux équipements'), 'B');
       $this->Ln($this->largeLn);
       $this->Ln($this->smallLn);
       $this->SetFont('Arial', '', 12);
       $this->MultiCell(185, 5, utf8_decode($equipmentsList));
       $this->Ln($this->largeLn);
       $this->MultiCell(185, 5, utf8_decode($comments));
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
        $this->Cell($wordingWidth, 12, utf8_decode('Prix du véhicule :'));
        $postTaxesDealerSellingPrice = round($priceDetails->getPostTaxesDealerSellingPrice());
        $this->Cell($priceWidth, 12, utf8_decode(number_format($postTaxesDealerSellingPrice, 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        //$this->Cell($priceWidth, 12, utf8_decode($priceDetails->getPostTaxesDealerSellingPrice().' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->regularLn);
        $this->Cell($leftPadding, 12);
        $this->Cell($wordingWidth, 12, utf8_decode('Forfait de Mise à disposition :'));
        $postTaxesPackageProvision = round($priceDetails->getPostTaxesPackageProvision());
        $this->Cell($priceWidth, 12, utf8_decode(number_format($postTaxesPackageProvision, 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->largeLn);
        $this->Cell($leftPadding, 12);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($wordingWidth, 8, utf8_decode('Montant total :'), 'TLB');
        $this->SetFont('Arial', '', 12);
        $this->Cell($priceWidth, 8, utf8_decode(number_format($postTaxesDealerSellingPrice + $postTaxesPackageProvision, 2, '.', ' ').' '.$this->chrEuro.' TTC'), 'TRB', 0, 'R');
        $this->Ln($this->largeLn);
        $this->Cell($leftPadding, 12);
        $this->Cell($wordingWidth, 12, utf8_decode('Bonus/Malus*** :'));
        $bonusPenalty = $priceDetails->getBonusPenalty();
        $this->Cell($priceWidth, 12, utf8_decode(number_format($bonusPenalty, 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->regularLn);
        $this->Cell($leftPadding, 12);
        $this->Cell($wordingWidth, 12, utf8_decode('Carte Grise*** :'));
        $registrationCardAmount = $priceDetails->getRegistrationCardAmount();
        $this->Cell($priceWidth, 12, utf8_decode(number_format($registrationCardAmount, 2, '.', ' ').' '.$this->chrEuro.' TTC'), '', 0, 'R');
        $this->Ln($this->largeLn);
        $this->Cell($leftPadding, 12);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($wordingWidth, 8, utf8_decode('Total clé en mains**** :'), 'TLB');
        $this->SetFont('Arial', '', 12);
        $this->Cell($priceWidth, 8, utf8_decode(number_format($postTaxesDealerSellingPrice + $postTaxesPackageProvision + $bonusPenalty + $registrationCardAmount, 2, '.', ' ').' '.$this->chrEuro.' TTC'), 'TRB', 0, 'R');
        $this->Ln($this->largeLn);
        /*$this->Cell($leftPadding, 12);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($wordingWidth, 8, utf8_decode('Total clé en mains :'), 'TLB');
        $this->SetFont('Arial', '', 12);
        $this->Cell($priceWidth, 8, utf8_decode(number_format($priceDetails->getPostTaxesKeyInHandPrice(), 2, '.', ' ').' '.$this->chrEuro.' TTC***'), 'TRB', 0, 'R');
        $this->Ln($this->largeLn);*/

        $this->SetFont('Arial', 'I', 8);
        $this->Cell(185, 12, utf8_decode('*Taux donné à titre indicatif.'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('**Délai donné à titre indicatif.'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('***Sous réserve de modification des tarifs du cheval fiscal et du malus écologique en vigueur et de confirmation du taux de Co2.'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('****Sous réserve de disponibilité du véhicule.'));
        $this->Ln($this->smallLn);
        $this->SetFont('Arial', '', 8);
        $this->Cell(185, 12, utf8_decode('Le règlement du véhicule est a effectuer, soit par chèque de banque à la livraison (nous vous prions de nous envoyer une copie 24 à 48h avant la date'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('de livraison), soit par virement (la somme devra figurer sur notre compte au moment de la livraison du véhicule).'));
        $this->Ln($this->smallLn);
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('Une caution de 10% du prix du véhicule TTC sera demandée à la signature du bon de commande du véhicule.'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('Cette caution ne sera pas encaissée sauf en cas de renonciation à la commande.'));
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('Cette caution sera restituée lors du paiement complet du véhicule.'));
        $this->Ln($this->smallLn);
        $this->Ln($this->smallLn);
        $this->Cell(185, 12, utf8_decode('CHEQUE DE CAUTION N°................................  BANQUE:..........................  MONTANT:................'));
    }

    public function printBlocRecapPackageContent(){
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Forfait de mise à disposition'), 'T');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 11);

        $this->MultiCell(185, 5, utf8_decode('Il comprend : '.$this->getStructure()->getPackageContent()));
        $this->Cell(185, 12, utf8_decode('La carte grise définitive reste à la charge du client.'));
    }

    public function printBlocSuppInfo(){
        $vehicle = $this->getVehicle();

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(185, 8, utf8_decode('Informations supplémentaires'), 'T');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 11);

        $this->MultiCell(185, 4, utf8_decode($vehicle->getSuppComments()));
        if(!$vehicle->getHasAccident()){
            $this->Cell(185, 8, utf8_decode('Jamais accidenté'));
            $this->Ln($this->regularLn);
        }
        if($vehicle->getIsTechnicalInspectionOk()){
            $this->Cell(185, 8, utf8_decode('Contrôle Technique OK'));
            $this->Ln($this->regularLn);
        }
        if($vehicle->getIsMaintenanceLogOk()){
            $this->Cell(185, 8, utf8_decode('Carnet d\'entretien à jour'));
            $this->Ln($this->regularLn);
        }
        $this->Cell(185, 8, utf8_decode('Garantie : '.$vehicle->getWarranty()));
        $this->Ln($this->regularLn);
        $this->Cell(185, 8, utf8_decode('Financement : '.$vehicle->getFunding()));
        $this->Ln($this->regularLn);
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