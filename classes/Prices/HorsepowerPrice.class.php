<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 29/02/2016
 * Time: 09:13
 */

namespace Prices;


class HorsepowerPrice
{
    private $id, $department, $amount, $refreshDate;

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
     * @param string $department
     */
    public function setDepartment(\string $department){
        $this->department = (string)$department;
    }

    /**
     * @return string
     */
    public function getDepartment():\string{
        return $this->department;
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
     * @param \DateTime $refreshDate
     */
    public function setRefreshDate(\DateTime $refreshDate){
        $this->refreshDate = $refreshDate;
    }

    /**
     * @return \DateTime
     */
    public function getRefreshDate():\DateTime{
        return $this->refreshDate;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * HorsepowerPrice constructor.
     *
     * @param int            $id
     * @param string         $department
     * @param float          $amount
     * @param \DateTime|null $refreshDate
     */
    public function __construct(\int $id = 0, \string $department = '', \float $amount = 0.0, \DateTime $refreshDate = null){
        $this->setId($id);
        $this->setDepartment($department);
        $this->setAmount($amount);
        $this->setRefreshDate(is_a($refreshDate, '\DateTime') ? $refreshDate : new \DateTime());
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @param int $fiscalPower
     *
     * @return float
     */
    public function getRegistrationCardAmount(\int $fiscalPower){
        $db = databaseConnection();
        $fixAmount = HorsepowerPriceManager::fetchFixAmount($db);
        $db = null;
        return round($fixAmount + ($fiscalPower * $this->getAmount()), 2);
    }
}