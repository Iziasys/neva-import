<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 29/02/2016
 * Time: 12:10
 */

namespace Offers;

class Offer
{
    private $id, $number, $creationDate, $vehicleId, $vehiclePrice, $freightChargesToFrance, $marginAmount, $managementFees, $dealerMargin,
        $vatRate, $packageProvision, $freightChargesInFrance, $clientId, $ownerId, $state, $options, $packs, $color, $rims,
        $externalColor, $internalColor;

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
     * @param string $number
     */
    public function setNumber(\string $number){
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber():\string{
        return $this->number;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate(\DateTime $creationDate){
        $this->creationDate = $creationDate;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate():\DateTime{
        return $this->creationDate;
    }

    /**
     * @param int $vehicleId
     */
    public function setVehicleId(\int $vehicleId){
        $this->vehicleId = $vehicleId;
    }

    /**
     * @return int
     */
    public function getVehicleId():\int{
        return $this->vehicleId;
    }

    /**
     * @param float $vehiclePrice
     */
    public function setVehiclePrice(\float $vehiclePrice){
        $this->vehiclePrice = $vehiclePrice;
    }

    /**
     * @return float
     */
    public function getVehiclePrice():\float{
        return $this->vehiclePrice;
    }

    /**
     * @param float $freightChargesToFrance
     */
    public function setFreightChargesToFrance(\float $freightChargesToFrance){
        $this->freightChargesToFrance = $freightChargesToFrance;
    }

    /**
     * @return float
     */
    public function getFreightChargesToFrance():\float{
        return $this->freightChargesToFrance;
    }

    /**
     * @param float $marginAmount
     */
    public function setMarginAmount(\float $marginAmount){
        $this->marginAmount = $marginAmount;
    }

    /**
     * @return float
     */
    public function getMarginAmount():\float{
        return $this->marginAmount;
    }

    /**
     * @param float $managementFees
     */
    public function setManagementFees(\float $managementFees){
        $this->managementFees = $managementFees;
    }

    /**
     * @return float
     */
    public function getManagementFees():\float{
        return $this->managementFees;
    }

    /**
     * @param float $dealerMargin
     */
    public function setDealerMargin(\float $dealerMargin){
        $this->dealerMargin = $dealerMargin;
    }

    /**
     * @return float
     */
    public function getDealerMargin():\float{
        return $this->dealerMargin;
    }

    /**
     * @param float $vatRate
     */
    public function setVatRate(\float $vatRate){
        $this->vatRate = $vatRate;
    }

    /**
     * @return float
     */
    public function getVatRate():\float{
        return $this->vatRate;
    }

    /**
     * @param float $packageProvision
     */
    public function setPackageProvision(\float $packageProvision){
        $this->packageProvision = $packageProvision;
    }

    /**
     * @return float
     */
    public function getPackageProvision():\float{
        return $this->packageProvision;
    }

    /**
     * @param float $freightChargesInFrance
     */
    public function setFreightChargesInFrance(\float $freightChargesInFrance){
        $this->freightChargesInFrance = $freightChargesInFrance;
    }

    /**
     * @return float
     */
    public function getFreightChargesInFrance():\float{
        return $this->freightChargesInFrance;
    }

    /**
     * @param int $clientId
     */
    public function setClientId(\int $clientId){
        $this->clientId = $clientId;
    }

    /**
     * @return int
     */
    public function getClientId():\int{
        return $this->clientId;
    }

    /**
     * @param int $ownerId
     */
    public function setOwnerId(\int $ownerId){
        $this->ownerId = $ownerId;
    }

    /**
     * @return int
     */
    public function getOwnerId():\int{
        return $this->ownerId;
    }

    /**
     * @param int $state
     */
    public function setState(\int $state){
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getState():\int{
        return $this->state;
    }

    /**
     * @param Option[] $options
     */
    public function setOptions(array $options){
        $tmp = array();
        foreach($options as $option){
            if(is_a($option, '\Offers\Option')){
                $tmp[] = $option;
            }
        }
        $this->options = $tmp;
    }

    /**
     * @return Option[]
     */
    public function getOptions():array{
        return $this->options;
    }

    /**
     * @param Pack[] $packs
     */
    public function setPacks(array $packs){
        $tmp = array();
        foreach($packs as $pack){
            if(is_a($pack, '\Offers\Pack')){
                $tmp[] = $pack;
            }
        }
        $this->packs = $tmp;
    }

    /**
     * @return Pack[]
     */
    public function getPacks():array{
        return $this->packs;
    }

    /**
     * @param Color $color
     */
    public function setColor(Color $color){
        $this->color = $color;
    }

    /**
     * @return Color
     */
    public function getColor():Color{
        return $this->color;
    }

    /**
     * @param Rims $rims
     */
    public function setRims(Rims $rims){
        $this->rims = $rims;
    }

    /**
     * @return Rims
     */
    public function getRims():Rims{
        return $this->rims;
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
     * @param string $internalColor
     */
    public function setInternalColor(\string $internalColor){
        $this->internalColor = $internalColor;
    }

    /**
     * @return string
     */
    public function getInternalColor():\string{
        return $this->internalColor;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Offer constructor.
     *
     * @param int            $id
     * @param string         $number
     * @param \DateTime|null $creationDate
     * @param int            $vehicleId
     * @param float          $vehiclePrice
     * @param float          $freightChargesToFrance
     * @param float          $marginAmount
     * @param float          $managementFees
     * @param float          $dealerMargin
     * @param float          $vatRate
     * @param float          $packageProvision
     * @param float          $freightChargesInFrance
     * @param int            $clientId
     * @param int            $ownerId
     * @param int            $state
     * @param Option[]|null  $options
     * @param Pack[]|null    $packs
     * @param Color|null     $color
     * @param Rims|null      $rims
     * @param string         $externalColor
     * @param string         $internalColor
     */
    public function __construct(\int $id = 0, \string $number = '', \DateTime $creationDate = null, \int $vehicleId = 0,
                                \float $vehiclePrice = 0.0, \float $freightChargesToFrance = 0.0, \float $marginAmount = 0.0,
                                \float $managementFees = 0.0, \float $dealerMargin = 0.0, \float $vatRate = 0.0,
                                \float $packageProvision = 0.0, \float $freightChargesInFrance = 0.0,
                                \int $clientId = 0, \int $ownerId = 0, \int $state = 0, array $options = array(),
                                array $packs = array(), Color $color = null, Rims $rims = null,
                                \string $externalColor = '', \string $internalColor = ''){
        $this->setId($id);
        $this->setNumber($number);
        $this->setCreationDate(is_a($creationDate, '\DateTime') ? $creationDate : new \DateTime());
        $this->setVehicleId($vehicleId);
        $this->setVehiclePrice($vehiclePrice);
        $this->setFreightChargesToFrance($freightChargesToFrance);
        $this->setMarginAmount($marginAmount);
        $this->setManagementFees($managementFees);
        $this->setDealerMargin($dealerMargin);
        $this->setVatRate($vatRate);
        $this->setPackageProvision($packageProvision);
        $this->setFreightChargesInFrance($freightChargesInFrance);
        $this->setClientId($clientId);
        $this->setOwnerId($ownerId);
        $this->setState($state);
        $this->setOptions($options);
        $this->setPacks($packs);
        $this->setColor(is_a($color, '\Offers\Color') ? $color : new Color());
        $this->setRims(is_a($color, '\Offers\Rims') ? $rims : new Rims());
        $this->setExternalColor($externalColor);
        $this->setInternalColor($internalColor);
    }
    /*******************CONSTRUCTOR*****************/
}