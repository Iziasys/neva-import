<?php

/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 08/03/2016
 * Time: 14:58
 */
class VehicleInStockPDF extends PDF
{
    protected $vehicle, $priceDetails;

    /*******************SETTERS & GETTERS*****************/
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
     * VehicleInStockPDF constructor.
     *
     * @param int                  $vehicleId
     */
    public function __construct(\int $vehicleId){
        /** @var \Users\User $user */
        $user = $_SESSION['user'];
        if(!is_a($user, '\Users\User')){
            $db = null;
            echo 'Vous devez être connecté.';
            die();
        }
        $structure = $user->getStructure();

        $db = databaseConnection();
        $vehicle = \Vehicle\VehicleInStockManager::fetchVehicle($db, $vehicleId);
        if(is_a($vehicle, '\Exception')){
            $db = null;
            echo $vehicle->getMessage();
            die();
        }

        $dealerDepartment = $structure->getDepartment();
        if(empty($_SESSION['selectedClient'])){
            $client = null;
            $clientDepartment = $dealerDepartment;
            $clientId = 0;
        }
        else{
            /** @var \Users\Client|null $client */
            $client = $_SESSION['selectedClient'];
            $clientDepartment = empty($client->getPostalCode()) ? $dealerDepartment : $client->getDepartment();
            $clientId = $client->getId();
        }
        $vat = \Prices\VatManager::fetchFrenchVat($db);
        $freightCharges = \Prices\FreightChargesInFranceManager::fetchFreightChargesByDepartment($db, $clientDepartment);
        $structureMargin = \Vehicle\VehicleInStockManager::fetchStructureMargin($db, $vehicleId, $structure->getId());
        $db = null;

        parent::__construct($structure, $user);

        $buyingPrice = \Prices\VatManager::convertToPretax($vehicle->getPrice(), $vat->getAmount());
        $changeRateToEuro = 1;
        $freightChargesToFrance = 0;
        $marginAmount = 0;
        $marginPercentage = 0;
        $managementFees = 0;
        if($structure->getFreightCharges() === null){
            $freightChargesInFrance = $freightCharges->getAmount();
        }
        else{
            $freightChargesInFrance = $structure->getFreightCharges();
        }
        $dealerMargin = is_a($structureMargin, '\Exception') ? $structure->getDefaultMargin() : $structureMargin;
        $packageProvision = $structure->getPackageProvision();
        $optionPrice = 0.0;
        $vatAmount = $vat->getAmount();
        $registrationCardAmount = 0;
        $bonusPenalty = 0;
        $priceDetails = new \Prices\PriceDetails($buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount,
                                                 $marginPercentage, $managementFees, $freightChargesInFrance, $dealerMargin,
                                                 $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount,
                                                 $bonusPenalty);

        $this->setVehicle($vehicle);
        $this->setPriceDetails($priceDetails);
    }
    /*******************CONSTRUCTOR*****************/

    public function printVehicleDetails(){
        $vehicle = $this->getVehicle();

        $this->SetFont('Arial', 'B', 16);
        $this->Cell(185, 12, utf8_decode($vehicle->getBrand().' '.$vehicle->getModel().' '.$vehicle->getFinish()
                                         .' - '.$vehicle->getEngineSize().' '.$vehicle->getEngine().' '.
                                         $vehicle->getDynamicalPower().'ch'), 'B');
        $this->Ln($this->largeLn);
        $this->SetFont('Arial', '', 12);
        $this->Cell(50, 12, utf8_decode('Année-Modèle :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getModelDate()->format('m/Y')));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Kilométrage :'));
        $this->Cell(50, 12, utf8_decode(number_format($vehicle->getMileage(), 0, '.', ' ')));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Carburant :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getFuel()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Boite de vitesse :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getGearbox()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Carrosserie :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getBodywork()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Puissance Fiscale :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getFiscalPower().' cv'));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Couleur :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getExternalColor()));
        $this->Ln($this->regularLn);
        $this->Cell(50, 12, utf8_decode('Co2 :'));
        $this->Cell(50, 12, utf8_decode($vehicle->getCo2().' g/Km'));
        $this->Ln($this->regularLn);
        /*$this->SetY(60);
        $this->SetFont('Arial', '', 9);
        $this->Cell(185, 12, utf8_decode('Photo non contractuelle'), '', 0, 'R');*/
        $imagePath = '/ressources/vehicleImages/VO/'.$vehicle->getImage1();
        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
            try{
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 65, 75, 0, 'JPG');
            }
            catch(Exception $e){
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 125, 65, 75, 0, 'PNG');
            }
        }
    }

    public function printVehicleEquipments(){
        $equipments = $this->getVehicle()->getEquipmentsOrderedByName();

        $this->SetFont('Arial', 'B', 16);
        $this->Cell(185, 12, utf8_decode('Principaux équipements :'), 'B');
        $this->SetFont('Arial', '', 12);
        $this->Ln($this->largeLn);
        $this->Ln($this->largeLn);

        $equipmentsList = implode(', ', $equipments);
        $this->MultiCell(185, 5, utf8_decode($equipmentsList));
    }

    public function printMoreInformation(){
        $vehicle = $this->getVehicle();
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(185, 12, utf8_decode('Informations supplémentaires :'), 'B');
        $this->SetFont('Arial', '', 12);
        $this->Ln($this->largeLn);
        $this->Ln($this->largeLn);
        if(!empty($vehicle->getSuppComments())){
            $this->MultiCell(185, 4, utf8_decode($vehicle->getSuppComments()));
        }
        if(!empty($vehicle->getFunding())){
            $this->MultiCell(185, 4, utf8_decode('Financement : '. $vehicle->getFunding()));
        }
        if(!empty($vehicle->getWarranty())){
            $this->MultiCell(185, 4, utf8_decode('Garantie : '. $vehicle->getWarranty()));
        }

        if(!$vehicle->getHasAccident()){
            $this->Cell(60, 4, utf8_decode('Véhicule jamais accidenté.'));
            $this->Ln($this->regularLn);
        }
        if($vehicle->getIsTechnicalInspectionOk()){
            $this->Cell(60, 4, utf8_decode('Contrôle technique OK.'));
            $this->Ln($this->regularLn);
        }
        if($vehicle->getIsMaintenanceLogOk()){
            $this->Cell(60, 4, utf8_decode('Carnet d\'entretien à jour.'));
            $this->Ln($this->regularLn);
        }
    }

    public function printVehiclePrice(){
        $vehiclePrice = $this->getPriceDetails();

        $this->SetFont('Arial', 'B', 45);
        $this->SetTextColor(2, 117, 216);
        $this->Cell(185, 22, utf8_decode(number_format($vehiclePrice->getPostTaxesDealerSellingPrice(), 0, '.', ' ').' '.$this->getChrEuro().' TTC'), '', 0, 'R');
        $this->SetTextColor(0, 0, 0);
    }

    public function printAllPictures(){
        $image1 = $this->getVehicle()->getImage1();
        $image2 = $this->getVehicle()->getImage2();
        $image3 = $this->getVehicle()->getImage3();
        $image4 = $this->getVehicle()->getImage4();

        $imagePath = '/ressources/vehicleImages/VO/'.$image1;
        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
            try{
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 10, 50, 92, 0, 'JPG');
            }
            catch(Exception $e){
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 10, 50, 92, 0, 'PNG');
            }
        }
        $imagePath = '/ressources/vehicleImages/VO/'.$image2;
        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
            try{
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 102, 50, 92, 0, 'JPG');
            }
            catch(Exception $e){
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 102, 50, 92, 0, 'PNG');
            }
        }
        $imagePath = '/ressources/vehicleImages/VO/'.$image3;
        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
            try{
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 10, 130, 92, 0, 'JPG');
            }
            catch(Exception $e){
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 10, 120, 92, 0, 'PNG');
            }
        }
        $imagePath = '/ressources/vehicleImages/VO/'.$image4;
        if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath)){
            try{
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 102, 130, 92, 0, 'JPG');
            }
            catch(Exception $e){
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().$imagePath, 102, 130, 92, 0, 'PNG');
            }
        }
        $this->SetY(230);
    }
}