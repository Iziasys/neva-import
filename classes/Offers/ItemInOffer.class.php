<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 29/02/2016
 * Time: 12:26
 */

namespace Offers;


class ItemInOffer
{
    private $itemId, $offerId, $price;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param int $itemId
     */
    public function setItemId(\int $itemId){
        $this->itemId = $itemId;
    }

    /**
     * @return int
     */
    public function getItemId():\int{
        return $this->itemId;
    }

    /**
     * @param int $offerId
     */
    public function setOfferId(\int $offerId){
        $this->offerId = $offerId;
    }

    /**
     * @return int
     */
    public function getOfferId():\int{
        return $this->offerId;
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
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * ItemInOffer constructor.
     *
     * @param int   $itemId
     * @param int   $offerId
     * @param float $price
     */
    public function __construct(\int $itemId = 0, \int $offerId = 0, \float $price = 0.0){
        $this->setItemId($itemId);
        $this->setOfferId($offerId);
        $this->setPrice($price);
    }
    /*******************CONSTRUCTOR*****************/
}