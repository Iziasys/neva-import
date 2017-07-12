<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 08:40
 */

namespace Prices;


use Users\Address;
use Users\Person;

class Dealer
{
    private $id, $name, $country, $countryId, $address, $phone, $fax, $email, $contact, $comments, $acceptNewsLetter;

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
     * @param Country $country
     */
    public function setCountry(Country $country){
        $this->country = $country;
    }

    /**
     * @return Country
     */
    public function getCountry():Country{
        return $this->country;
    }

    /**
     * @param int $countryId
     */
    public function setCountryId(\int $countryId){
        $this->countryId = (int)$countryId;
    }

    /**
     * @return int
     */
    public function getCountryId():\int{
        return $this->countryId;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address){
        $this->address = $address;
    }

    /**
     * @return Address
     */
    public function getAddress():Address{
        return $this->address;
    }

    /**
     * @param string $phone
     */
    public function setPhone(\string $phone){
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone():\string{
        return $this->phone;
    }

    /**
     * @param string $fax
     */
    public function setFax(\string $fax){
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getFax():\string{
        return $this->fax;
    }

    /**
     * @param string $email
     */
    public function setEmail(\string $email){
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail():\string{
        return $this->email;
    }

    /**
     * @param Person $contact
     */
    public function setContact(Person $contact){
        $this->contact = $contact;
    }

    /**
     * @return Person
     */
    public function getContact():Person{
        return $this->contact;
    }

    /**
     * @param string $comments
     */
    public function setComments(\string $comments){
        $this->comments = $comments;
    }

    /**
     * @return string
     */
    public function getComments():\string{
        return $this->comments;
    }

    /**
     * @param bool $acceptNewsLetter
     */
    public function setAcceptNewsLetter(\bool $acceptNewsLetter){
        $this->acceptNewsLetter = $acceptNewsLetter;
    }

    /**
     * @return bool
     */
    public function getAcceptNewsLetter():\bool{
        return $this->acceptNewsLetter;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Dealer constructor.
     *
     * @param int          $id
     * @param string       $name
     * @param Country|null $country
     * @param int          $countryId
     * @param Address|null $address
     * @param string       $phone
     * @param string       $fax
     * @param string       $email
     * @param Person|null  $contact
     * @param string       $comments
     * @param bool         $acceptNewsLetter
     */
    public function __construct(\int $id = 0, \string $name = '', Country $country = null, \int $countryId = 0,
                                Address $address = null, \string $phone = '', \string $fax = '', \string $email = '',
                                Person $contact = null, \string $comments = '', \bool $acceptNewsLetter = false){
        $this->setId($id);
        $this->setName($name);
        $this->setCountry(is_a($country, '\Prices\Country') ? $country : new Country());
        $this->setCountryId($countryId);
        $this->setAddress(is_a($address, '\Users\Address') ? $address : new Address());
        $this->setPhone($phone);
        $this->setFax($fax);
        $this->setEmail($email);
        $this->setContact(is_a($contact, '\Users\Person') ? $contact : new Person());
        $this->setComments($comments);
        $this->setAcceptNewsLetter($acceptNewsLetter);
    }
    /*******************CONSTRUCTOR*****************/
}