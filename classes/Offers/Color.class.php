<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 29/02/2016
 * Time: 12:29
 */

namespace Offers;


class Color extends ItemInOffer
{
    /*******************CONSTRUCTOR*****************/
    /**
     * Color constructor.
     *
     * @param int   $itemId
     * @param int   $offerId
     * @param float $optionPrice
     */
    public function __construct(\int $itemId = 0, \int $offerId = 0, \float $optionPrice = 0.0){
        parent::__construct($itemId, $offerId, $optionPrice);
    }
    /*******************CONSTRUCTOR*****************/
}