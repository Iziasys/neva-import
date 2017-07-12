<?php

/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 18/01/2016
 * Time: 15:35
 */
namespace Prices;
class ExchangeRate{
    private $id, $rateToEuro, $rateDate;

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
     * @param float $rateToEuro
     */
    public function setRateToEuro(\float $rateToEuro){
        $this->rateToEuro = (float)$rateToEuro;
    }

    /**
     * @return float
     */
    public function getRateToEuro():\float{
        return $this->rateToEuro;
    }

    /**
     * @param \DateTime $rateDate
     */
    public function setRateDate(\DateTime $rateDate){
        $this->rateDate = $rateDate;
    }

    /**
     * @return \DateTime
     */
    public function getRateDate():\DateTime{
        return $this->rateDate;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * ExchangeRate constructor.
     *
     * @param int            $id
     * @param float          $rateToEuro
     * @param \DateTime|null $rateDate
     */
    public function __construct(\int $id = 0, \float $rateToEuro = 0.0, \DateTime $rateDate = null){
        $this->setId($id);
        $this->setRateToEuro($rateToEuro);
        $this->setRateDate(is_a($rateDate, '\DateTime') ? $rateDate : new \DateTime());
    }
    /*******************CONSTRUCTOR*****************/
}