<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 01/02/2016
 * Time: 15:04
 */

namespace Users;


class Rights
{
    private $id, $administrateVehicleOnDemand, $viewVehicleOnDemand, $administrateVehicleOnStock, $viewVehicleOnStock,
        $administrateCarousel, $createStructure, $modifyStructure, $createUser, $modifyUser;

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
     * @param bool $administrateVehicleOnDemand
     */
    public function setAdministrateVehicleOnDemand(\bool $administrateVehicleOnDemand){
        $this->administrateVehicleOnDemand = (bool)$administrateVehicleOnDemand;
    }

    /**
     * @return bool
     */
    public function getAdministrateVehicleOnDemand():\bool{
        return $this->administrateVehicleOnDemand;
    }

    /**
     * @param bool $viewVehicleOnDemand
     */
    public function setViewVehicleOnDemand(\bool $viewVehicleOnDemand){
        $this->viewVehicleOnDemand = (bool)$viewVehicleOnDemand;
    }

    /**
     * @return bool
     */
    public function getViewVehicleOnDemand():\bool{
        return $this->viewVehicleOnDemand;
    }

    /**
     * @param bool $administrateVehicleOnStock
     */
    public function setAdministrateVehicleOnStock(\bool $administrateVehicleOnStock){
        $this->administrateVehicleOnStock = (bool)$administrateVehicleOnStock;
    }

    /**
     * @return bool
     */
    public function getAdministrateVehicleOnStock():\bool{
        return $this->administrateVehicleOnStock;
    }

    /**
     * @param bool $viewVehicleOnStock
     */
    public function setViewVehicleOnStock(\bool $viewVehicleOnStock){
        $this->viewVehicleOnStock = (bool)$viewVehicleOnStock;
    }

    /**
     * @return bool
     */
    public function getViewVehicleOnStock():\bool{
        return $this->viewVehicleOnStock;
    }

    /**
     * @param bool $administrateCarousel
     */
    public function setAdministrateCarousel(\bool $administrateCarousel){
        $this->administrateCarousel = (bool)$administrateCarousel;
    }

    /**
     * @return bool
     */
    public function getAdministrateCarousel():\bool{
        return $this->administrateCarousel;
    }

    /**
     * @param bool $createStructure
     */
    public function setCreateStructure(\bool $createStructure){
        $this->createStructure = (bool)$createStructure;
    }

    /**
     * @return bool
     */
    public function getCreateStructure():\bool{
        return $this->createStructure;
    }

    /**
     * @param bool $modifyStructure
     */
    public function setModifyStructure(\bool $modifyStructure){
        $this->modifyStructure = (bool)$modifyStructure;
    }

    /**
     * @return bool
     */
    public function getModifyStructure():\bool{
        return $this->modifyStructure;
    }

    /**
     * @param bool $createUser
     */
    public function setCreateUser(\bool $createUser){
        $this->createUser = (bool)$createUser;
    }

    /**
     * @return bool
     */
    public function getCreateUser():\bool{
        return $this->createUser;
    }

    /**
     * @param bool $modifyUser
     */
    public function setModifyUser(\bool $modifyUser){
        $this->modifyUser = (bool)$modifyUser;
    }

    /**
     * @return bool
     */
    public function getModifyUser():\bool{
        return $this->modifyUser;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Rights constructor.
     *
     * @param int  $id
     * @param bool $administrateVehicleOnDemand
     * @param bool $viewVehicleOnDemand
     * @param bool $administrateVehicleOnStock
     * @param bool $viewVehicleOnStock
     * @param bool $administrateCarousel
     * @param bool $createStructure
     * @param bool $modifyStructure
     * @param bool $createUser
     * @param bool $modifyUser
     */
    public function __construct(\int $id = 0, \bool $administrateVehicleOnDemand = false, \bool $viewVehicleOnDemand = false,
                                \bool $administrateVehicleOnStock = false, \bool $viewVehicleOnStock = false,
                                \bool $administrateCarousel = false, \bool $createStructure = false,
                                \bool $modifyStructure = false, \bool $createUser = false, \bool $modifyUser = false)
    {
        $this->setId($id);
        $this->setAdministrateVehicleOnDemand($administrateVehicleOnDemand);
        $this->setViewVehicleOnDemand($viewVehicleOnDemand);
        $this->setAdministrateVehicleOnStock($administrateVehicleOnStock);
        $this->setViewVehicleOnStock($viewVehicleOnStock);
        $this->setAdministrateCarousel($administrateCarousel);
        $this->setCreateStructure($createStructure);
        $this->setModifyStructure($modifyStructure);
        $this->setCreateUser($createUser);
        $this->setModifyUser($modifyUser);
    }
    /*******************CONSTRUCTOR*****************/
}