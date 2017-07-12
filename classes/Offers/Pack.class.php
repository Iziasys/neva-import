<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 29/02/2016
 * Time: 12:26
 */

namespace Offers;


class Pack extends ItemInOffer
{
    /*******************CONSTRUCTOR*****************/
    /**
     * Pack constructor.
     *
     * @param int   $itemId
     * @param int   $offerId
     * @param float $optionPrice
     */
    public function __construct($itemId = 0, $offerId = 0, $optionPrice = 0.0){
        parent::__construct($itemId, $offerId, $optionPrice);
    }
    /*******************CONSTRUCTOR*****************/
}