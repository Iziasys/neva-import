<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 15/03/2016
 * Time: 08:43
 */

namespace Offers;


use Users\Client;
use Users\Structure;
use Users\User;
use Vehicle\VehicleInStock;

class StockOffer
{
    private $id, $number, $vehicleId, $vehicle, $vehiclePriceAmount, $dealerMargin, $freightCharges, $packageProvision,
        $structureId, $structure, $userId, $user, $clientId, $client, $creationDate, $state;

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
     * @param VehicleInStock $vehicle
     */
    public function setVehicle(VehicleInStock $vehicle){
        $this->vehicle = $vehicle;
    }

    /**
     * @return VehicleInStock
     */
    public function getVehicle():VehicleInStock{
        return $this->vehicle;
    }

    /**
     * @param float $vehiclePriceAmount
     */
    public function setVehiclePriceAmount(\float $vehiclePriceAmount){
        $this->vehiclePriceAmount = $vehiclePriceAmount;
    }

    /**
     * @return float
     */
    public function getVehiclePriceAmount():\float{
        return $this->vehiclePriceAmount;
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
     * @param float $freightCharges
     */
    public function setFreightCharges(\float $freightCharges){
        $this->freightCharges = $freightCharges;
    }

    /**
     * @return float
     */
    public function getFreightCharges():\float{
        return $this->freightCharges;
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
     * @param Structure $structure
     */
    public function setStructure(Structure $structure){
        $this->structure = $structure;
    }

    /**
     * @return Structure
     */
    public function getStructure():Structure{
        return $this->structure;
    }

    /**
     * @param int $userId
     */
    public function setUserId(\int $userId){
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserId():\int{
        return $this->userId;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user){
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser():User{
        return $this->user;
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
     * @param Client $client
     */
    public function setClient(Client $client){
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient():Client{
        return $this->client;
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
     * StockOffer constructor.
     *
     * @param int                 $id
     * @param string              $number
     * @param int                 $vehicleId
     * @param VehicleInStock|null $vehicle
     * @param float               $vehiclePriceAmount
     * @param float               $dealerMargin
     * @param float               $freightCharges
     * @param float               $packageProvision
     * @param int                 $structureId
     * @param Structure|null      $structure
     * @param int                 $userId
     * @param User|null           $user
     * @param int                 $clientId
     * @param Client|null         $client
     * @param \DateTime|null      $creationDate
     * @param int                 $state
     */
    public function __construct(\int $id = 0, \string $number = '', \int $vehicleId = 0, VehicleInStock $vehicle = null,
                                \float $vehiclePriceAmount = 0.0, \float $dealerMargin = 0.0, \float $freightCharges = 0.0,
                                \float $packageProvision = 0.0, \int $structureId = 0, Structure $structure = null,
                                \int $userId = 0, User $user = null, \int $clientId = 0, Client $client = null,
                                \DateTime $creationDate = null, \int $state = 0){
        $this->setId($id);
        $this->setNumber($number);
        $this->setVehicleId($vehicleId);
        $this->setVehicle(is_a($vehicle, '\Vehicle\VehicleInStock') ? $vehicle : new VehicleInStock());
        $this->setVehiclePriceAmount($vehiclePriceAmount);
        $this->setDealerMargin($dealerMargin);
        $this->setFreightCharges($freightCharges);
        $this->setPackageProvision($packageProvision);
        $this->setStructureId($structureId);
        $this->setStructure(is_a($structure, '\Users\Structure') ? $structure : new Structure());
        $this->setUserId($userId);
        $this->setUser(is_a($user, '\Users\User') ? $user : new User());
        $this->setClientId($clientId);
        $this->setClient(is_a($client, '\Users\Client') ? $client : new Client());
        $this->setCreationDate(is_a($creationDate, '\DateTime') ? $creationDate : new \DateTime());
        $this->setState($state);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return string
     */
    public function getReference(){
        if(empty($this->getNumber()))
            return '';
        else
            return $this->getCreationDate()->format('Ymd').$this->getNumber();
    }

}