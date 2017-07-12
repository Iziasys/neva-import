<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 10:11
 */

namespace Vehicle;


class RimModel
{
    protected $id, $name, $rimId, $rimType, $frontDiameter, $backDiameter;

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
     * @param string $name
     */
    public function setName(\string $name){
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName():\string{
        return $this->name;
    }

    /**
     * @param int $rimId
     */
    public function setRimId(\int $rimId){
        $this->rimId = (int)$rimId;
    }

    /**
     * @return int
     */
    public function getRimId():\int{
        return $this->rimId;
    }

    /**
     * @param string $rimType
     */
    public function setRimType(\string $rimType){
        $this->rimType = (string)$rimType;
    }

    /**
     * @return string
     */
    public function getRimType():\string{
        return $this->rimType;
    }

    /**
     * @param int $frontDiameter
     */
    public function setFrontDiameter(\int $frontDiameter){
        $this->frontDiameter = (int)$frontDiameter;
    }

    /**
     * @return int
     */
    public function getFrontDiameter():\int{
        return $this->frontDiameter;
    }

    /**
     * @param int $backDiameter
     */
    public function setBackDiameter(\int $backDiameter){
        $this->backDiameter = (int)$backDiameter;
    }

    /**
     * @return int
     */
    public function getBackDiameter():\int{
        return $this->backDiameter;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * RimModel constructor.
     *
     * @param int    $id
     * @param string $name
     * @param int    $rimId
     * @param string $rimType
     * @param int    $frontDiameter
     * @param int    $backDiameter
     */
    public function __construct(\int $id = 0, \string $name = '', \int $rimId = 0, \string $rimType = '',
                                \int $frontDiameter = 0, \int $backDiameter = 0){
        $this->setId($id);
        $this->setName($name);
        $this->setRimId($rimId);
        $this->setRimType($rimType);
        $this->setFrontDiameter($frontDiameter);
        $this->setBackDiameter($backDiameter);
    }
    /*******************CONSTRUCTOR*****************/
}