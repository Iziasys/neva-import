<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 25/02/2016
 * Time: 10:09
 */

namespace Users;


class Client extends Person
{
    protected $isSociety, $ownerId, $addressNumber, $addressExtension, $streetType, $addressWording, $postalCode, $town, $insertionDate;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param bool $isSociety
     */
    public function setIsSociety(\bool $isSociety){
        $this->isSociety = (bool)$isSociety;
    }

    /**
     * @return bool
     */
    public function getIsSociety():\bool{
        return $this->isSociety;
    }

    /**
     * @param int $ownerId
     */
    public function setOwnerId(\int $ownerId){
        $this->ownerId = (int)$ownerId;
    }

    /**
     * @return int
     */
    public function getOwnerId():\int{
        return $this->ownerId;
    }

    /**
     * @param string $addressNumber
     */
    public function setAddressNumber(\string $addressNumber){
        $this->addressNumber = (string)$addressNumber;
    }

    /**
     * @return string
     */
    public function getAddressNumber():\string{
        return $this->addressNumber;
    }

    /**
     * @param string $addressExtension
     */
    public function setAddressExtension(\string $addressExtension){
        $this->addressExtension = (string)$addressExtension;
    }

    /**
     * @return string
     */
    public function getAddressExtension():\string{
        return $this->addressExtension;
    }

    /**
     * @param string $streetType
     */
    public function setStreetType(\string $streetType){
        $this->streetType = (string)$streetType;
    }

    /**
     * @return string
     */
    public function getStreetType():\string{
        return $this->streetType;
    }

    /**
     * @param string $addressWording
     */
    public function setAddressWording(\string $addressWording){
        $this->addressWording = (string)$addressWording;
    }

    /**
     * @return string
     */
    public function getAddressWording():\string{
        return $this->addressWording;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(\string $postalCode){
        $this->postalCode = (string)$postalCode;
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
        $this->town = (string)$town;
    }

    /**
     * @return string
     */
    public function getTown():\string{
        return $this->town;
    }

    /**
     * @param \DateTime $insertionDate
     */
    public function setInsertionDate(\DateTime $insertionDate){
        $this->insertionDate = $insertionDate;
    }

    /**
     * @return \DateTime
     */
    public function getInsertionDate():\DateTime{
        return $this->insertionDate;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Client constructor.
     *
     * @param int            $id
     * @param string         $email
     * @param string         $lastName
     * @param string         $firstName
     * @param string         $civility
     * @param string         $phone
     * @param string         $mobile
     * @param string         $fax
     * @param bool           $acceptNewsLetter
     * @param bool           $isSociety
     * @param int            $ownerId
     * @param int            $addressNumber
     * @param string         $addressExtension
     * @param string         $streetType
     * @param string         $addressWording
     * @param string         $postalCode
     * @param string         $town
     * @param \DateTime|null $insertionDate
     */
    public function __construct(\int $id = 0, \string $email = '', \string $lastName = '', \string $firstName = '',
                                \string $civility = '', \string $phone = '', \string $mobile = '', \string $fax = '',
                                \bool $acceptNewsLetter = false, \bool $isSociety = false, \int $ownerId = 0,
                                \int $addressNumber = 0, \string $addressExtension = '', \string $streetType = '',
                                \string $addressWording = '', \string $postalCode = '', \string $town = '',
                                \DateTime $insertionDate = null){
        parent::__construct($id, $email, $lastName, $firstName, $civility, $phone, $mobile, $fax, $acceptNewsLetter);
        $this->setIsSociety($isSociety);
        $this->setOwnerId($ownerId);
        $this->setAddressNumber($addressNumber);
        $this->setAddressExtension($addressExtension);
        $this->setStreetType($streetType);
        $this->setAddressWording($addressWording);
        $this->setPostalCode($postalCode);
        $this->setTown($town);
        $this->setInsertionDate(is_a($insertionDate, '\DateTime') ? $insertionDate : new \DateTime());
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return string
     */
    public function getPostalAddress():\string{
        $addNumber = $this->getAddressNumber() == 0 ? '' : $this->getAddressNumber();
        return $addNumber.''.$this->getAddressExtension().' '.$this->getStreetType().' '.$this->getAddressWording().' '.$this->getPostalCode().' '.$this->getTown();
    }

    /**
     * @return string
     */
    public function getStreetAddress():\string{
        $addNumber = $this->getAddressNumber() == 0 ? '' : $this->getAddressNumber();
        return $addNumber.''.$this->getAddressExtension().' '.$this->getStreetType().' '.$this->getAddressWording();
    }

    /**
     * @return string
     */
    public function getDepartment(){
        $postalCode = str_pad($this->postalCode, 5, STR_PAD_LEFT);

        return substr($postalCode, 0, 2);
    }
}