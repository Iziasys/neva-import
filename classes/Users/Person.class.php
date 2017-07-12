<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 25/02/2016
 * Time: 09:58
 */

namespace Users;


class Person
{
    protected $id, $email, $lastName, $firstName, $civility, $phone, $mobile, $fax, $acceptNewsLetter;

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
     * @param string $email
     */
    public function setEmail(\string $email){
        $this->email = (string)$email;
    }

    /**
     * @return string
     */
    public function getEmail():\string{
        return $this->email;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(\string $lastName){
        $this->lastName = (string)$lastName;
    }

    /**
     * @return string
     */
    public function getLastName():\string{
        return $this->lastName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(\string $firstName){
        $this->firstName = (string)$firstName;
    }

    /**
     * @return string
     */
    public function getFirstName():\string{
        return $this->firstName;
    }

    /**
     * @param string $civility
     */
    public function setCivility(\string $civility){
        $this->civility = (string)$civility;
    }

    /**
     * @return string
     */
    public function getCivility():\string{
        return $this->civility;
    }

    /**
     * @param string $phone
     */
    public function setPhone(\string $phone){
        $this->phone = (string)$phone;
    }

    /**
     * @return string
     */
    public function getPhone():\string{
        return $this->phone;
    }

    /**
     * @param string $mobile
     */
    public function setMobile(\string $mobile){
        $this->mobile = (string) $mobile;
    }

    /**
     * @return string
     */
    public function getMobile():\string{
        return $this->mobile;
    }

    /**
     * @param string $fax
     */
    public function setFax(\string $fax){
        $this->fax = (string)$fax;
    }

    /**
     * @return string
     */
    public function getFax():\string{
        return $this->fax;
    }

    /**
     * @param bool $acceptNewsLetter
     */
    public function setAcceptNewsLetter(\bool $acceptNewsLetter){
        $this->acceptNewsLetter = (bool)$acceptNewsLetter;
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
     * Person constructor.
     *
     * @param int    $id
     * @param string $email
     * @param string $lastName
     * @param string $firstName
     * @param string $civility
     * @param string $phone
     * @param string $mobile
     * @param string $fax
     * @param bool   $acceptNewsLetter
     */
    public function __construct(\int $id = 0, \string $email = '', \string $lastName = '', \string $firstName = '',
                                \string $civility = '', \string $phone = '', \string $mobile = '', \string $fax = '',
                                \bool $acceptNewsLetter = false){
        $this->setId($id);
        $this->setEmail($email);
        $this->setLastName($lastName);
        $this->setFirstName($firstName);
        $this->setCivility($civility);
        $this->setPhone($phone);
        $this->setMobile($mobile);
        $this->setFax($fax);
        $this->setAcceptNewsLetter($acceptNewsLetter);
    }
    /*******************CONSTRUCTOR*****************/
}