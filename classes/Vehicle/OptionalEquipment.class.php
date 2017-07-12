<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 09:04
 */

namespace Vehicle;
use \Prices\Price;

class OptionalEquipment extends Equipment
{
    private $price, $priceId;

    /*******************SETTERS & GETTERS*****************/
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
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * OptionalEquipment constructor.
     *
     * @param int    $id
     * @param string $name
     * @param string $typeName
     * @param int    $typeId
     * @param string $family
     * @param bool   $exclusivity
     * @param Price|null   $price
     * @param int    $priceId
     */
    public function __construct(\int $id = 0, \string $name = '', \string $typeName = '', \int $typeId = 0,
                                \string $family = '', \bool $exclusivity = false, Price $price = null, \int $priceId = 0){
        parent::__construct($id, $name, $typeName, $typeId, $family, $exclusivity);
        $this->setPrice(is_a($price, '\Prices\Price') ? $price : new Price());
    }
    /*******************CONSTRUCTOR*****************/
}