<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 18/01/2016
 * Time: 16:05
 */

namespace Prices;


class Vat{
    private $id, $amount, $vatDate;

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
     * @param float $amount
     */
    public function setAmount(\float $amount){
        $this->amount = (float)$amount;
    }

    /**
     * @return float
     */
    public function getAmount():\float{
        return $this->amount;
    }

    /**
     * @param \DateTime $vatDate
     */
    public function setVatDate(\DateTime $vatDate){
        $this->vatDate = $vatDate;
    }

    /**
     * @return \DateTime
     */
    public function getVatDate():\DateTime{
        return $this->vatDate;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Vat constructor.
     * @param int $id
     * @param float $amount
     * @param \DateTime|null $vatDate
     */
    public function __construct(\int $id = 0, \float $amount = 0.0, \DateTime $vatDate = null){
        $this->setId($id);
        $this->setAmount($amount);
        $this->setVatDate(is_a($vatDate, '\DateTime') ? $vatDate : new \DateTime());
    }
    /*******************CONSTRUCTOR*****************/
}