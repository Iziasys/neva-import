<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 23/03/2016
 * Time: 13:51
 */

namespace Users;


class Address
{
    protected $number, $extension, $streetType, $wording, $postalCode, $town;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param int $number
     */
    public function setNumber(\int $number){
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getNumber():\int{
        return $this->number;
    }

    /**
     * @param string $extension
     */
    public function setExtension(\string $extension){
        $this->extension = $extension;
    }

    /**
     * @return string
     */
    public function getExtension():\string{
        return $this->extension;
    }

    /**
     * @param string $streetType
     */
    public function setStreetType(\string $streetType){
        $this->streetType = $streetType;
    }

    /**
     * @return string
     */
    public function getStreetType():\string{
        return $this->streetType;
    }

    /**
     * @param string $wording
     */
    public function setWording(\string $wording){
        $this->wording = $wording;
    }

    /**
     * @return string
     */
    public function getWording():\string{
        return $this->wording;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(\string $postalCode){
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getPostalCode():\string{
        return $this->postalCode;
    }

    /**
     * @param string $town
     */
    public function setTown(\string $town){
        $this->town = $town;
    }

    /**
     * @return string
     */
    public function getTown():\string{
        return $this->town;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Address constructor.
     *
     * @param int    $number
     * @param string $extension
     * @param string $streetType
     * @param string $wording
     * @param string $postalCode
     * @param string $town
     */
    public function __construct(\int $number = 0, \string $extension = '', \string $streetType = '', \string $wording = '',
                                \string $postalCode = '', \string $town = ''){
        $this->setNumber($number);
        $this->setExtension($extension);
        $this->setStreetType($streetType);
        $this->setWording($wording);
        $this->setPostalCode($postalCode);
        $this->setTown($town);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return string
     */
    public function getPostalAddress():\string{
        $addNumber = $this->getNumber() == 0 ? '' : $this->getNumber();
        return $addNumber.''.$this->getNumber().' '.$this->getStreetType().' '.$this->getWording().' '.$this->getPostalCode().' '.$this->getTown();
    }

    /**
     * @return string
     */
    public function getStreetAddress():\string{
        $addNumber = $this->getNumber() == 0 ? '' : $this->getNumber();
        return $addNumber.''.$this->getExtension().' '.$this->getStreetType().' '.$this->getWording();
    }

    /**
     * @return string
     */
    public function getDepartment(){
        $postalCode = str_pad($this->postalCode, 5, STR_PAD_LEFT);

        return substr($postalCode, 0, 2);
    }
}