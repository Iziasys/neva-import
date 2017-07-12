<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 04/03/2016
 * Time: 14:56
 */

namespace Vehicle;


class VehicleInStock
{
    private $id, $brand, $model, $finish, $engineSize, $engine, $dynamicalPower, $modelDate, $mileage, $fuel, $gearbox,
        $reference, $fiscalPower, $co2, $bodywork, $transmission, $usersAmount, $externalColor, $hasAccident,
        $isTechnicalInspectionOk, $isMaintenanceLogOk, $equipments, $suppComments, $funding, $warranty, $price, $sellerMargin,
        $image1, $image2, $image3, $image4, $structureId, $depotSale, $availabilityDate, $sold, $reserved, $reservedBy,
        $insertionDate, $sellingStructure, $sellingDate, $bonusPenalty, $buyingPrice, $vatOnMargin, $feesAmount, $feesDetails;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param int $id
     */
    public function setId(\int $id){
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId():\int{
        return $this->id;
    }

    /**
     * @param string $brand
     */
    public function setBrand(\string $brand){
        $this->brand = $brand;
    }

    /**
     * @return string
     */
    public function getBrand():\string{
        return $this->brand;
    }

    /**
     * @param string $model
     */
    public function setModel(\string $model){
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getModel():\string{
        return $this->model;
    }

    /**
     * @param string $finish
     */
    public function setFinish(\string $finish){
        $this->finish = $finish;
    }

    /**
     * @return string
     */
    public function getFinish():\string{
        return $this->finish;
    }

    /**
     * @param float $engineSize
     */
    public function setEngineSize(\float $engineSize){
        $this->engineSize = $engineSize;
    }

    /**
     * @return float
     */
    public function getEngineSize():\float{
        return $this->engineSize;
    }

    /**
     * @param string $engine
     */
    public function setEngine(\string $engine){
        $this->engine = $engine;
    }

    /**
     * @return string
     */
    public function getEngine():\string{
        return $this->engine;
    }

    /**
     * @param int $dynamicalPower
     */
    public function setDynamicalPower(\int $dynamicalPower){
        $this->dynamicalPower = $dynamicalPower;
    }

    /**
     * @return int
     */
    public function getDynamicalPower():\int{
        return $this->dynamicalPower;
    }

    /**
     * @param \DateTime $modelDate
     */
    public function setModelDate(\DateTime $modelDate){
        $this->modelDate = $modelDate;
    }

    /**
     * @return \DateTime
     */
    public function getModelDate():\DateTime{
        return $this->modelDate;
    }

    /**
     * @param int $mileage
     */
    public function setMileage(\int $mileage){
        $this->mileage = $mileage;
    }

    /**
     * @return int
     */
    public function getMileage():\int{
        return $this->mileage;
    }

    /**
     * @param string $fuel
     */
    public function setFuel(\string $fuel){
        $this->fuel = $fuel;
    }

    /**
     * @return string
     */
    public function getFuel():\string{
        return $this->fuel;
    }

    /**
     * @param string $gearbox
     */
    public function setGearbox(\string $gearbox){
        $this->gearbox = $gearbox;
    }

    /**
     * @return string
     */
    public function getGearbox():\string{
        return $this->gearbox;
    }

    /**
     * @param string $reference
     */
    public function setReference(\string $reference){
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getReference():\string{
        return $this->reference;
    }

    /**
     * @param int $fiscalPower
     */
    public function setFiscalPower(\int $fiscalPower){
        $this->fiscalPower = $fiscalPower;
    }

    /**
     * @return int
     */
    public function getFiscalPower():\int{
        return $this->fiscalPower;
    }

    /**
     * @param int $co2
     */
    public function setCo2(\int $co2){
        $this->co2 = $co2;
    }

    /**
     * @return int
     */
    public function getCo2():\int{
        return $this->co2;
    }

    /**
     * @param string $bodywork
     */
    public function setBodywork(\string $bodywork){
        $this->bodywork = $bodywork;
    }

    /**
     * @return string
     */
    public function getBodywork():\string{
        return $this->bodywork;
    }

    /**
     * @param string $transmission
     */
    public function setTransmission(\string $transmission){
        $this->transmission = $transmission;
    }

    /**
     * @return string
     */
    public function getTransmission():\string{
        return $this->transmission;
    }

    /**
     * @param string $externalColor
     */
    public function setExternalColor(\string $externalColor){
        $this->externalColor = $externalColor;
    }

    /**
     * @return string
     */
    public function getExternalColor():\string{
        return $this->externalColor;
    }

    /**
     * @param int $usersAmount
     */
    public function setUsersAmount(\int $usersAmount){
        $this->usersAmount = $usersAmount;
    }

    /**
     * @return int
     */
    public function getUsersAmount():\int{
        return $this->usersAmount;
    }

    /**
     * @param bool $hasAccident
     */
    public function setHasAccident(\bool $hasAccident){
        $this->hasAccident = $hasAccident;
    }

    /**
     * @return bool
     */
    public function getHasAccident():\bool{
        return $this->hasAccident;
    }

    /**
     * @param bool $isTechnicalInspectionOk
     */
    public function setIsTechnicalInspectionOk(\bool $isTechnicalInspectionOk){
        $this->isTechnicalInspectionOk = $isTechnicalInspectionOk;
    }

    /**
     * @return bool
     */
    public function getIsTechnicalInspectionOk():\bool{
        return $this->isTechnicalInspectionOk;
    }

    /**
     * @param bool $isMaintenanceLogOk
     */
    public function setIsMaintenanceLogOk(\bool $isMaintenanceLogOk){
        $this->isMaintenanceLogOk = $isMaintenanceLogOk;
    }

    /**
     * @return bool
     */
    public function getIsMaintenanceLogOk():\bool{
        return $this->isMaintenanceLogOk;
    }

    /**
     * @param string[] $equipments
     */
    public function setEquipments(array $equipments){
        $this->equipments = $equipments;
    }

    /**
     * @return string[]
     */
    public function getEquipments():array{
        return $this->equipments;
    }

    /**
     * @param string $suppComments
     */
    public function setSuppComments(\string $suppComments){
        $this->suppComments = $suppComments;
    }

    /**
     * @return string
     */
    public function getSuppComments():\string{
        return $this->suppComments;
    }

    /**
     * @param string $funding
     */
    public function setFunding(\string $funding){
        $this->funding = $funding;
    }

    /**
     * @return string
     */
    public function getFunding():\string{
        return $this->funding;
    }

    /**
     * @param string $warranty
     */
    public function setWarranty(\string $warranty){
        $this->warranty = $warranty;
    }

    /**
     * @return string
     */
    public function getWarranty():\string{
        return $this->warranty;
    }

    /**
     * @param float $price
     */
    public function setPrice(\float $price){
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice():\float{
        return $this->price;
    }

    /**
     * @param float $sellerMargin
     */
    public function setSellerMargin(\float $sellerMargin){
        $this->sellerMargin = $sellerMargin;
    }

    /**
     * @return float
     */
    public function getSellerMargin():\float{
        return $this->sellerMargin;
    }

    /**
     * @param string $image1
     */
    public function setImage1(\string $image1){
        $this->image1 = $image1;
    }

    /**
     * @return string
     */
    public function getImage1():\string{
        return $this->image1;
    }

    /**
     * @param string $image2
     */
    public function setImage2(\string $image2){
        $this->image2 = $image2;
    }

    /**
     * @return string
     */
    public function getImage2():\string{
        return $this->image2;
    }

    /**
     * @param string $image3
     */
    public function setImage3(\string $image3){
        $this->image3 = $image3;
    }

    /**
     * @return string
     */
    public function getImage3():\string{
        return $this->image3;
    }

    /**
     * @param string $image4
     */
    public function setImage4(\string $image4){
        $this->image4 = $image4;
    }

    /**
     * @return string
     */
    public function getImage4():\string{
        return $this->image4;
    }

    /**
     * @param int $structureId
     */
    public function setStructureId(\int $structureId){
        $this->structureId = $structureId;
    }

    /**
     * @return int
     */
    public function getStructureId():\int{
        return $this->structureId;
    }

    /**
     * @param bool $depotSale
     */
    public function setDepotSale(\bool $depotSale){
        $this->depotSale = $depotSale;
    }

    /**
     * @return bool
     */
    public function getDepotSale():\bool{
        return $this->depotSale;
    }

    /**
     * @param \DateTime $availabilityDate
     */
    public function setAvailabilityDate(\DateTime $availabilityDate){
        $this->availabilityDate = $availabilityDate;
    }

    /**
     * @return \DateTime
     */
    public function getAvailabilityDate():\DateTime{
        return $this->availabilityDate;
    }

    /**
     * @param bool $sold
     */
    public function setSold(\bool $sold){
        $this->sold = $sold;
    }

    /**
     * @return bool
     */
    public function getSold():\bool{
        return $this->sold;
    }

    /**
     * @param bool $reserved
     */
    public function setReserved(\bool $reserved){
        $this->reserved = $reserved;
    }

    /**
     * @return bool
     */
    public function getReserved():\bool{
        return $this->reserved;
    }

    /**
     * @param int $reservedBy
     */
    public function setReservedBy(\int $reservedBy){
        $this->reservedBy = $reservedBy;
    }

    /**
     * @return int
     */
    public function getReservedBy():\int{
        return $this->reservedBy;
    }

    /**
     * @param \DateTime $insertionDate
     */
    public function setInsertionDate(\DateTime $insertionDate){
        $this->insertionDate = $insertionDate;
    }

    /**
     * @return \DateTime
     */
    public function getInsertionDate():\DateTime{
        return $this->insertionDate;
    }

    /**
     * @param int $sellingStructure
     */
    public function setSellingStructure(\int $sellingStructure){
        $this->sellingStructure = $sellingStructure;
    }

    /**
     * @return int
     */
    public function getSellingStructure():\int{
        return $this->sellingStructure;
    }

    /**
     * @param \DateTime $sellingDate
     */
    public function setSellingDate(\DateTime $sellingDate){
        $this->sellingDate = $sellingDate;
    }

    /**
     * @return \DateTime
     */
    public function getSellingDate():\DateTime{
        return $this->sellingDate;
    }

    /**
     * @param float $bonusPenalty
     */
    public function setBonusPenalty(\float $bonusPenalty){
        $this->bonusPenalty = $bonusPenalty;
    }

    /**
     * @return float
     */
    public function getBonusPenalty():\float{
        return $this->bonusPenalty;
    }

    /**
     * @param float|null $buyingPrice
     */
    public function setBuyingPrice($buyingPrice){
        if($buyingPrice === null){
            $this->buyingPrice = null;
        }
        else{
            $this->buyingPrice = (float)$buyingPrice;
        }
    }

    /**
     * @return float|null
     */
    public function getBuyingPrice(){
        return $this->buyingPrice;
    }

    /**
     * @param bool|null $vatOnMargin
     */
    public function setVatOnMargin($vatOnMargin){
        if($vatOnMargin === null){
            $this->vatOnMargin = null;
        }
        else{
            $this->vatOnMargin = (bool)$vatOnMargin;
        }
    }

    /**
     * @return bool|null
     */
    public function getVatOnMargin(){
        return $this->vatOnMargin;
    }


    /**
     * @param float|null $feesAmount
     */
    public function setFeesAmount($feesAmount){
        if($feesAmount === null){
            $this->feesAmount = null;
        }
        else{
            $this->feesAmount = (float)$feesAmount;
        }
    }

    /***
     * @return float|null
     */
    public function getFeesAmount(){
        return $this->feesAmount;
    }

    /**
     * @param string|null $feesDetails
     */
    public function setFeesDetails($feesDetails){
        if($feesDetails === null){
            $this->feesDetails = null;
        }
        else{
            $this->feesDetails = (float)$feesDetails;
        }
    }

    /**
     * @return string|null
     */
    public function getFeesDetails(){
        return $this->feesDetails;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * VehicleInStock constructor.
     *
     * @param int            $id
     * @param string         $brand
     * @param string         $model
     * @param string         $finish
     * @param float          $engineSize
     * @param string         $engine
     * @param int            $dynamicalPower
     * @param \DateTime|null $modelDate
     * @param int            $mileage
     * @param string         $fuel
     * @param string         $gearbox
     * @param string         $reference
     * @param int            $fiscalPower
     * @param int            $co2
     * @param string         $bodywork
     * @param string         $transmission
     * @param int            $usersAmount
     * @param string         $externalColor
     * @param bool           $hasAccident
     * @param bool           $isTechnicalInspectionOk
     * @param bool           $isMaintenanceLogOk
     * @param string[]       $equipments
     * @param string         $suppComments
     * @param string         $funding
     * @param string         $warranty
     * @param float          $price
     * @param float          $sellerMargin
     * @param string         $image1
     * @param string         $image2
     * @param string         $image3
     * @param string         $image4
     * @param int            $structureId
     * @param bool           $depotSale
     * @param \DateTime|null $availabilityDate
     * @param bool           $sold
     * @param bool           $reserved
     * @param int            $reservedBy
     * @param \DateTime|null $insertionDate
     * @param int            $sellingStructure
     * @param \DateTime|null $sellingDate
     * @param float          $bonusPenalty
     * @param float|null     $buyingPrice
     * @param bool|null      $vatOnMargin
     * @param float|null     $feesAmount
     * @param string|null    $feesDetails
     */
    public function __construct(\int $id = 0, \string $brand = '', \string $model = '', \string $finish = '',
                                \float $engineSize = 0.0, \string $engine = '', \int $dynamicalPower = 0,
                                \DateTime $modelDate = null, \int $mileage = 0, \string $fuel = '', \string $gearbox = '',
                                \string $reference = '', \int $fiscalPower = 0, \int $co2 = 0, \string $bodywork = '',
                                \string $transmission = '', \int $usersAmount = 0, \string $externalColor = '', \bool $hasAccident = true,
                                \bool $isTechnicalInspectionOk = false, \bool $isMaintenanceLogOk = false, array $equipments = array(),
                                \string $suppComments = '', \string $funding = '', \string $warranty = '', \float $price = 0.0,
                                \float $sellerMargin = 0.0, \string $image1 = '', \string $image2 = '', \string $image3 = '',
                                \string $image4 = '', \int $structureId = 0, \bool $depotSale = false,
                                \DateTime $availabilityDate = null, \bool $sold = false, \bool $reserved = false, \int $reservedBy = 0,
                                \DateTime $insertionDate = null, \int $sellingStructure = 0, \DateTime $sellingDate = null,
                                \float $bonusPenalty = 0.0, \float $buyingPrice = null, \bool $vatOnMargin = null,
                                \float $feesAmount = null, \string $feesDetails = null)
    {
        $this->setId($id);
        $this->setBrand($brand);
        $this->setModel($model);
        $this->setFinish($finish);
        $this->setEngineSize($engineSize);
        $this->setEngine($engine);
        $this->setDynamicalPower($dynamicalPower);
        $this->setModelDate(is_a($modelDate, '\DateTime') ? $modelDate : new \DateTime());
        $this->setMileage($mileage);
        $this->setFuel($fuel);
        $this->setGearbox($gearbox);
        $this->setReference($reference);
        $this->setFiscalPower($fiscalPower);
        $this->setCo2($co2);
        $this->setBodywork($bodywork);
        $this->setTransmission($transmission);
        $this->setUsersAmount($usersAmount);
        $this->setExternalColor($externalColor);
        $this->setHasAccident($hasAccident);
        $this->setIsTechnicalInspectionOk($isTechnicalInspectionOk);
        $this->setIsMaintenanceLogOk($isMaintenanceLogOk);
        $this->setEquipments($equipments);
        $this->setSuppComments($suppComments);
        $this->setFunding($funding);
        $this->setWarranty($warranty);
        $this->setPrice($price);
        $this->setSellerMargin($sellerMargin);
        $this->setImage1($image1);
        $this->setImage2($image2);
        $this->setImage3($image3);
        $this->setImage4($image4);
        $this->setStructureId($structureId);
        $this->setDepotSale($depotSale);
        $this->setAvailabilityDate(is_a($availabilityDate, '\DateTime') ? $availabilityDate : new \DateTime());
        $this->setSold($sold);
        $this->setReserved($reserved);
        $this->setReservedBy($reservedBy);
        $this->setInsertionDate(is_a($insertionDate, '\DateTime') ? $insertionDate : new \DateTime());
        $this->setSellingStructure($sellingStructure);
        $this->setSellingDate(is_a($sellingDate, '\DateTime') ? $sellingDate : new \DateTime());
        $this->setBonusPenalty($bonusPenalty);
        $this->setBuyingPrice($buyingPrice);
        $this->setVatOnMargin($vatOnMargin);
        $this->setFeesAmount($feesAmount);
        $this->setFeesDetails($feesDetails);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return bool
     */
    public function getIsArriving(){
        $today = new \DateTime();

        return $this->getAvailabilityDate() > $today;
    }

    /**
     * @return bool
     */
    public function isNewVehicle(){
        if($this->getMileage() <= 100 && $this->getUsersAmount() < 1)
            return true;
        else
            return false;
    }

    /**
     * @return string[]
     */
    public function getEquipmentsOrderedByName(){
        $toReturn = $this->getEquipments();
        asort($toReturn);

        return $toReturn;
    }
}