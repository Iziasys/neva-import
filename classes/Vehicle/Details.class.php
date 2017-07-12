<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 11:38
 */

namespace Vehicle;


use Prices\Price;

class Details
{
    private $id, $dynamicalPower, $fiscalPower, $engineSize, $co2, $sitsAmount, $doorsAmount, $engine, $transmission,
        $bodywork, $finish, $finishId, $gearbox, $fuel, $price, $priceId, $available;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param int $id
     */
    public function setId(\int $id){
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId():\int{
        return $this->id;
    }

    /**
     * @param int $dynamicalPower
     */
    public function setDynamicalPower(\int $dynamicalPower){
        $this->dynamicalPower = (int)$dynamicalPower;
    }

    /**
     * @return int
     */
    public function getDynamicalPower():\int{
        return $this->dynamicalPower;
    }

    /**
     * @param int $fiscalPower
     */
    public function setFiscalPower(\int $fiscalPower){
        $this->fiscalPower = (int)$fiscalPower;
    }

    /**
     * @return int
     */
    public function getFiscalPower():\int{
        return $this->fiscalPower;
    }

    /**
     * @param float $engineSize
     */
    public function setEngineSize(\float $engineSize){
        $this->engineSize = (float)$engineSize;
    }

    /**
     * @return float
     */
    public function getEngineSize():\float{
        return $this->engineSize;
    }

    /**
     * @param int $co2
     */
    public function setCo2(\int $co2){
        $this->co2 = (int)$co2;
    }

    /**
     * @return int
     */
    public function getCo2():\int{
        return $this->co2;
    }

    /**
     * @param int $sitsAmount
     */
    public function setSitsAmount(\int $sitsAmount){
        $this->sitsAmount = (int)$sitsAmount;
    }

    /**
     * @return int
     */
    public function getSitsAmount():\int{
        return $this->sitsAmount;
    }

    /**
     * @param int $doorsAmount
     */
    public function setDoorsAmount(\int $doorsAmount){
        $this->doorsAmount = (int)$doorsAmount;
    }

    /**
     * @return int
     */
    public function getDoorsAmount():\int{
        return $this->doorsAmount;
    }

    /**
     * @param Engine $engine
     */
    public function setEngine(Engine $engine){
        $this->engine = $engine;
    }

    /**
     * @return Engine
     */
    public function getEngine():Engine{
        return $this->engine;
    }

    /**
     * @param Transmission $transmission
     */
    public function setTransmission(Transmission $transmission){
        $this->transmission = $transmission;
    }

    /**
     * @return Transmission
     */
    public function getTransmission():Transmission{
        return $this->transmission;
    }

    /**
     * @param Bodywork $bodywork
     */
    public function setBodywork(Bodywork $bodywork){
        $this->bodywork = $bodywork;
    }

    /**
     * @return Bodywork
     */
    public function getBodywork():Bodywork{
        return $this->bodywork;
    }

    /**
     * @param Finish $finish
     */
    public function setFinish(Finish $finish){
        $this->finish = $finish;
    }

    /**
     * @return Finish
     */
    public function getFinish():Finish{
        return $this->finish;
    }

    /**
     * @param int $finishId
     */
    public function setFinishId(\int $finishId){
        $this->finishId = (int)$finishId;
    }

    /**
     * @return int
     */
    public function getFinishId():\int{
        return $this->finishId;
    }

    /**
     * @param Gearbox $gearbox
     */
    public function setGearbox(Gearbox $gearbox){
        $this->gearbox = $gearbox;
    }

    /**
     * @return Gearbox
     */
    public function getGearbox():Gearbox{
        return $this->gearbox;
    }

    /**
     * @param Fuel $fuel
     */
    public function setFuel(Fuel $fuel){
        $this->fuel = $fuel;
    }

    /**
     * @return Fuel
     */
    public function getFuel():Fuel{
        return $this->fuel;
    }

    /**
     * @param Price $price
     */
    public function setPrice(Price $price){
        $this->price = $price;
    }

    /**
     * @return Price
     */
    public function getPrice():Price{
        return $this->price;
    }

    /**
     * @param int $priceId
     */
    public function setPriceId(\int $priceId){
        $this->priceId = (int)$priceId;
    }

    /**
     * @return int
     */
    public function getPriceId():\int{
        return $this->priceId;
    }

    /**
     * @param bool $available
     */
    public function setAvailable(\bool $available){
        $this->available = (bool)$available;
    }

    /**
     * @return bool
     */
    public function getAvailable():\bool{
        return $this->available;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Details constructor.
     *
     * @param int               $id
     * @param int               $dynamicalPower
     * @param int               $fiscalPower
     * @param float             $engineSize
     * @param int               $co2
     * @param int               $sitsAmount
     * @param int               $doorsAmount
     * @param Engine|null       $engine
     * @param Transmission|null $transmission
     * @param Bodywork|null     $bodywork
     * @param Finish|null       $finish
     * @param int               $finishId
     * @param Gearbox|null      $gearbox
     * @param Fuel|null         $fuel
     * @param Price|null        $price
     * @param int               $priceId
     * @param bool              $available
     */
    public function __construct(\int $id = 0, \int $dynamicalPower = 0, \int $fiscalPower = 0, \float $engineSize = 0.0,
                                \int$co2 = 0, \int $sitsAmount = 0, \int $doorsAmount = 0, Engine $engine = null,
                                Transmission $transmission = null, Bodywork $bodywork = null, Finish $finish = null,
                                \int $finishId = 0, Gearbox $gearbox = null, Fuel $fuel = null, Price $price = null,
                                \int $priceId = 0, \bool $available){
        $this->setId($id);
        $this->setDynamicalPower($dynamicalPower);
        $this->setFiscalPower($fiscalPower);
        $this->setEngineSize($engineSize);
        $this->setCo2($co2);
        $this->setSitsAmount($sitsAmount);
        $this->setDoorsAmount($doorsAmount);
        $this->setEngine(is_a($engine, '\Vehicle\Engine') ? $engine : new Engine());
        $this->setTransmission(is_a($transmission, '\Vehicle\Transmission') ? $transmission : new Transmission());
        $this->setBodywork(is_a($bodywork, '\Vehicle\Bodywork') ? $bodywork : new Bodywork());
        $this->setFinish(is_a($finish, '\Vehicle\Finish') ? $finish : new Finish());
        $this->setFinishId($finishId);
        $this->setGearbox(is_a($gearbox, '\Vehicle\Gearbox') ? $gearbox : new Gearbox());
        $this->setFuel(is_a($fuel, '\Vehicle\Fuel') ? $fuel : new Fuel());
        $this->setPrice(is_a($price, '\Prices\Price') ? $price : new Price());
        $this->setPriceId($priceId);
        $this->setAvailable($available);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * Désactive le véhicule
     */
    public function disableVehicle(){
        $this->setAvailable(false);
    }

    /**
     * Active le véhicule
     */
    public function enableVehicle(){
        $this->setAvailable(true);
    }
}