<?php

/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 18/03/2016
 * Time: 11:06
 */
class ProOfferPDF extends OfferPDF
{
    use ProOffer;

    protected $adminStructure;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param \Users\Structure $adminStructure
     */
    public function setAdminStructure(\Users\Structure $adminStructure){
        $this->adminStructure = $adminStructure;
    }

    /**
     * @return \Users\Structure
     */
    public function getAdminStructure():\Users\Structure{
        return $this->adminStructure;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    public function __construct($offerReference){
        parent::__construct($offerReference);

        $db = databaseConnection();
        $adminStructure = \Users\StructureManager::fetchPrimaryStructure($db);
        $db = null;

        $this->setAdminStructure($adminStructure);
    }
    /*******************CONSTRUCTOR*****************/

    public function Header(){
        if(!in_array($this->PageNo(), $this->getPagesToIgnoreHeader())){
            $structure = $this->getAdminStructure();
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(50, 0, utf8_decode($structure->getStructureName()));
            $this->Ln($this->regularLn);
            $this->SetFont('Arial', '', 12);
            $this->Cell(50, 0, utf8_decode($structure->getAddress()));
            $this->Ln($this->regularLn);
            $this->Cell(50, 0, utf8_decode($structure->getPostalCode().' '.$this->getStructure()->getTown()));
            $this->Ln($this->regularLn);
            $this->Cell(50, 0, utf8_decode('Tél : '.getPhoneNumber($structure->getPhone())));
            $this->Ln($this->largeLn);
            $this->Cell(50, 0, utf8_decode($structure->getEmail()));
            $this->Ln($this->regularLn);
            $this->setY(5);
            $this->Cell(115);
            if(is_file($_SERVER["DOCUMENT_ROOT"].getAppPath().'/theme/images/bani/'.$structure->getImageName())){
                $this->Image($_SERVER["DOCUMENT_ROOT"].getAppPath().'/theme/images/bani/'.$structure->getImageName(), 150, 0, 50, 50);
            }
            $this->SetY(50);
        }
    }

    //Pied de page
    public function Footer(){
        if(!in_array($this->PageNo(), $this->getPagesToIgnoreFooter())){
            $structure = $this->getStructure();
            $this->setY(-20);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'R');
            $this->Ln();
            $this->Cell(0, 0, $structure->getStructureName().' - '.$structure->getAddress().' - '.$structure->getPostalCode().' '.$structure->getTown(), 0, 0, 'C');
            $this->Ln(3);
            $this->Cell(0, 0, utf8_decode('Tél : '.getPhoneNumber($structure->getPhone()).' '.preg_replace_callback('/([€]+)/', function(){ return $_SESSION['euros']; }, $structure->getSocietyDetails()).' - SIRET '.$structure->getSiret().' APE '.$structure->getApe()), 0, 0, 'C');
        }
    }

    public function callPrintProProtagonistInformation(){
        $offerReference = $this->getOfferReference();
        $client = $this->getUser();
        $sellingStructure = $this->getAdminStructure();

        $this->printProProtagonistInformation($this, $offerReference, $sellingStructure, $client->getStructure());
    }

    public function callPrintProBlocVehicleWithImage(){
        $offer = $this->getOffer();
        $vehicle = $this->getVehicle();
        $lineDefVehicle = $this->getLineDefVehicle();

        $this->printProBlocVehicleWithImage($this, $offer, $vehicle, $lineDefVehicle);
    }

    public function callPrintProBlocRecapPrice(){
        $priceDetails = $this->getPriceDetails();

        $this->printProBlocRecapPrice($this, $priceDetails);
    }
}