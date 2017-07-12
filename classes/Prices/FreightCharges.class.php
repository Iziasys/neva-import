<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 18/01/2016
 * Time: 16:00
 */

namespace Prices;


class FreightCharges{
    private $id, $amount, $date;

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
    public function getAmount():\float
    {
        return $this->amount;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date){
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate():\DateTime{
        return $this->date;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * FreightCharges constructor.
     *
     * @param int            $id
     * @param float          $amount
     * @param \DateTime|null $date
     */
    public function __construct(\int $id = 0, \float $amount = 0.0, \DateTime $date = null){
        $this->setId($id);
        $this->setAmount($amount);
        $this->setDate(is_a($date, '\DateTime') ? $date : new \DateTime());
    }
    /*******************CONSTRUCTOR*****************/
}