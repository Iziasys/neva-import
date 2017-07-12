<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 25/02/2016
 * Time: 10:23
 */

namespace Users;


class SocietyClient extends Client
{
    private $name, $siren, $siret;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param string $name
     */
    public function setName(\string $name){
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName():\string{
        return $this->name;
    }

    /**
     * @param string $siren
     */
    public function setSiren(\string $siren){
        $this->siren = (string)$siren;
    }

    /**
     * @return string
     */
    public function getSiren():\string{
        return $this->siren;
    }

    /**
     * @param string $siret
     */
    public function setSiret(\string $siret){
        $this->siret = (string)$siret;
    }

    /**
     * @return string
     */
    public function getSiret():\string{
        return $this->siret;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * SocietyClient constructor.
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
     * @param string         $name
     * @param string         $siren
     * @param string         $siret
     */
    public function __construct(\int $id = 0, \string $email = '', \string $lastName = '', \string $firstName = '',
                                \string $civility = '', \string $phone = '', \string $mobile = '', \string $fax = '',
                                \bool $acceptNewsLetter = false, \bool $isSociety = false, \int $ownerId = 0,
                                \int $addressNumber = 0, \string $addressExtension = '', \string $streetType = '',
                                \string $addressWording = '', \string $postalCode = '', \string $town = '',
                                \DateTime $insertionDate = null, \string $name = '', \string $siren = '', \string $siret = ''){
        parent::__construct($id, $email, $lastName, $firstName, $civility, $phone, $mobile, $fax, $acceptNewsLetter,
                            $isSociety, $ownerId, $addressNumber, $addressExtension, $streetType, $addressWording,
                            $postalCode, $town, $insertionDate);
        $this->setName($name);
        $this->setSiren($siren);
        $this->setSiret($siret);
    }
    /*******************CONSTRUCTOR*****************/
}