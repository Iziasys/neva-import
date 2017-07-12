<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 10:49
 */

namespace Vehicle;

use Prices\Price;

class OptionalRim extends RimModel
{
    private $price;

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
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * OptionalRim constructor.
     *
     * @param int    $id
     * @param string $name
     * @param int    $rimId
     * @param string $rimType
     * @param int    $frontDiameter
     * @param int    $backDiameter
     * @param Price|null   $price
     */
    public function __construct(\int $id = 0, \string $name = '', \int $rimId = 0, \string $rimType = '',
                                \int $frontDiameter = 0, \int $backDiameter = 0, Price $price = null){
        parent::__construct($id, $name, $rimId, $rimType, $frontDiameter, $backDiameter);
        $this->setPrice(is_a($price, '\Prices\Price') ? $price : new Price());
    }
    /*******************CONSTRUCTOR*****************/
}