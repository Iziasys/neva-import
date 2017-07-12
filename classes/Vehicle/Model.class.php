<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stephane
 * Date: 18/01/2016
 * Time: 15:23
 */

namespace Vehicle;

class Model{
    private $id, $name, $brand, $brandId;

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
     * @param Brand $brand
     */
    public function setBrand(Brand $brand){
        $this->brand = $brand;
    }

    /**
     * @return Brand
     */
    public function getBrand():Brand{
        return $this->brand;
    }

    /**
     * @param int $brandId
     */
    public function setBrandId(\int $brandId){
        $this->brandId = (int)$brandId;
    }

    /**
     * @return int
     */
    public function getBrandId():\int{
        return $this->brandId;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Model constructor.
     *
     * @param int        $id
     * @param string     $name
     * @param Brand|null $brand
     * @param int        $brandId
     */
    public function __construct(\int $id = 0, \string $name = '', Brand $brand = null, \int $brandId = 0){
        $this->setId($id);
        $this->setName($name);
        $this->setBrand(is_a($brand, '\Vehicle\Brand') ? $brand : new Brand());
        $this->setBrandId($brandId);
    }
    /*******************CONSTRUCTOR*****************/
}