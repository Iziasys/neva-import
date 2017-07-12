<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 08/02/2016
 * Time: 08:32
 */

namespace Prices;


class FreightChargesInFrance
{
    private $id, $department, $departmentName, $amount, $date;

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
     * @param string $departmentName
     */
    public function setDepartmentName(\string $departmentName){
        $this->departmentName = (string)$departmentName;
    }

    /**
     * @return string
     */
    public function getDepartmentName():\string{
        return $this->departmentName;
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
     * FreightChargesInFrance constructor.
     *
     * @param int            $id
     * @param string            $department
     * @param string         $departmentName
     * @param float          $amount
     * @param \DateTime|null $date
     */
    public function __construct(\int $id = 0, \string $department = '', \string $departmentName = '', \float $amount = 0.0,
                                \DateTime $date = null){
        $this->setId($id);
        $this->setDepartment($department);
        $this->setDepartmentName($departmentName);
        $this->setAmount($amount);
        $this->setDate(is_a($date, '\DateTime') ? $date : new \DateTime());
    }
    /*******************CONSTRUCTOR*****************/
}