<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 08:49
 */

namespace Vehicle;


class Equipment
{
    protected $id, $name, $typeName, $typeId, $family, $exclusivity;

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
     * @param string $typeName
     */
    public function setTypeName(\string $typeName){
        $this->typeName = (string)$typeName;
    }

    /**
     * @return string
     */
    public function getTypeName():\string{
        return $this->typeName;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId(\int $typeId){
        $this->typeId = (int)$typeId;
    }

    /**
     * @return int
     */
    public function getTypeId():\int{
        return $this->typeId;
    }

    /**
     * @param string $family
     */
    public function setFamily(\string $family){
        $this->family = (string)$family;
    }

    /**
     * @return string
     */
    public function getFamily():\string{
        return $this->family;
    }

    /**
     * @param bool $exclusivity
     */
    public function setExclusivity(\bool $exclusivity){
        $this->exclusivity = (bool)$exclusivity;
    }

    /**
     * @return bool
     */
    public function getExclusivity():\bool{
        return $this->exclusivity;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Equipment constructor.
     *
     * @param int    $id
     * @param string $name
     * @param string $typeName
     * @param int    $typeId
     * @param string $family
     * @param bool   $exclusivity
     */
    public function __construct(\int $id = 0, \string $name = '', \string $typeName = '', \int $typeId = 0, \string $family = '', \bool $exclusivity = false){
        $this->setId($id);
        $this->setName($name);
        $this->setTypeName($typeName);
        $this->setTypeId($typeId);
        $this->setFamily($family);
        $this->setExclusivity($exclusivity);
    }
    /*******************CONSTRUCTOR*****************/
}