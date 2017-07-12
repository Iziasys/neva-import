<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 16/03/2016
 * Time: 10:03
 */

namespace Offers;


class PrintableOffer
{
    private $type, $offerNumber, $offerDate, $seller, $client, $vehicleId, $brand, $model, $finish, $engineSize, $engine,
        $dynamicalPower, $price, $state;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param int $type
     */
    public function setType(\int $type){
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType():\int{
        return $this->type;
    }

    /**
     * @param string $offerNumber
     */
    public function setOfferNumber(\string $offerNumber){
        $this->offerNumber = $offerNumber;
    }

    /**
     * @return string
     */
    public function getOfferNumber():\string{
        return $this->offerNumber;
    }

    /**
     * @param \DateTime $offerDate
     */
    public function setOfferDate(\DateTime $offerDate){
        $this->offerDate = $offerDate;
    }

    /**
     * @return \DateTime
     */
    public function getOfferDate():\DateTime{
        return $this->offerDate;
    }

    /**
     * @param string $seller
     */
    public function setSeller(\string $seller){
        $this->seller = $seller;
    }

    /**
     * @return string
     */
    public function getSeller():\string{
        return $this->seller;
    }

    /**
     * @param string $client
     */
    public function setClient(\string $client){
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getClient():\string{
        return $this->client;
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
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * PrintableOffer constructor.
     *
     * @param int            $type
     * @param string         $offerNumber
     * @param \DateTime|null $offerDate
     * @param string         $seller
     * @param string         $client
     * @param int            $vehicleId
     * @param string         $brand
     * @param string         $model
     * @param string         $finish
     * @param float          $engineSize
     * @param string         $engine
     * @param int            $dynamicalPower
     * @param float          $price
     * @param int            $state
     */
    public function __construct(\int $type = 0, \string $offerNumber = '', \DateTime $offerDate = null,
                                \string $seller = '', \string $client = '', \int $vehicleId = 0, \string $brand = '',
                                \string $model = '', \string $finish = '', \float $engineSize = 0.0,
                                \string $engine = '', \int $dynamicalPower = 0, \float $price = 0.0, \int $state = 0
    ){
        $this->setType($type);
        $this->setOfferNumber($offerNumber);
        $this->setOfferDate(is_a($offerDate, '\DateTime') ? $offerDate : new \DateTime());
        $this->setSeller($seller);
        $this->setClient($client);
        $this->setVehicleId($vehicleId);
        $this->setBrand($brand);
        $this->setModel($model);
        $this->setFinish($finish);
        $this->setEngineSize($engineSize);
        $this->setEngine($engine);
        $this->setDynamicalPower($dynamicalPower);
        $this->setPrice($price);
        $this->setState($state);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return string
     */
    public function getOfferReference(){
        return $this->getOfferDate()->format('Ymd').$this->getOfferNumber();
    }
}