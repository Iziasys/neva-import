<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 10:34
 */

namespace Vehicle;


use Prices\Price;

class OptionalColor extends ExternalColor
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
     * OptionalColor constructor.
     *
     * @param int    $id
     * @param bool   $biTone
     * @param string $name
     * @param string $details
     * @param Price|null   $price
     */
    public function __construct(\int $id = 0, \bool $biTone = false, \string $name = '', \string $details = '', Price $price = null){
        parent::__construct($id, $biTone, $name, $details);
        $this->setPrice(is_a($price, '\Prices\Price') ? $price : new Price());
    }
    /*******************CONSTRUCTOR*****************/
}