<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 25/02/2016
 * Time: 10:32
 */

namespace Users;


class IndividualClient extends Client
{
    /*******************SETTERS & GETTERS*****************/
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * IndividualClient constructor.
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
        parent::__construct($id, $email, $lastName, $firstName, $civility, $phone, $mobile, $fax, $acceptNewsLetter,
                            $isSociety, $ownerId, $addressNumber, $addressExtension, $streetType, $addressWording,
                            $postalCode, $town, $insertionDate);
    }
    /*******************CONSTRUCTOR*****************/
}