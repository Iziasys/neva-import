<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 22/01/2016
 * Time: 11:54
 */

namespace Users;


class User extends Person
{
    private $password, $type, $structure, $structureId, $lastConnection, $rights, $rightsId;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param string $password
     */
    public function setPassword(\string $password){
        $this->password = (string)$password;
    }

    /**
     * @return string
     */
    public function getPassword():\string{
        return $this->password;
    }

    /**
     * @param int $type
     */
    public function setType(\int $type){
        $this->type = (int)$type;
    }

    /**
     * @return int
     */
    public function getType():\int{
        return $this->type;
    }

    /**
     * @param Structure $structure
     */
    public function setStructure(Structure $structure){
        $this->structure = $structure;
    }

    /**
     * @return Structure
     */
    public function getStructure():Structure{
        return $this->structure;
    }

    /**
     * @param int $structureId
     */
    public function setStructureId(\int $structureId) {
        $this->structureId = (int)$structureId;
    }

    /**
     * @return int
     */
    public function getStructureId():\int {
        return $this->structureId;
    }

    /**
     * @param \DateTime $lastConnection
     */
    public function setLastConnection(\DateTime $lastConnection) {
        $this->lastConnection = $lastConnection;
    }

    /**
     * @return \DateTime
     */
    public function getLastConnection():\DateTime {
        return $this->lastConnection;
    }

    /**
     * @param Rights $rights
     */
    public function setRights(Rights $rights) {
        $this->rights = $rights;
    }

    /**
     * @return Rights
     */
    public function getRights():Rights {
        return $this->rights;
    }

    /**
     * @param int $rightsId
     */
    public function setRightsId(\int $rightsId) {
        $this->rightsId = (int)$rightsId;
    }

    /**
     * @return int
     */
    public function getRightsId():\int {
        return $this->rightsId;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * User constructor.
     *
     * @param int            $id
     * @param string         $email
     * @param string         $password
     * @param int            $type
     * @param Structure|null $structure
     * @param int            $structureId
     * @param string         $firstName
     * @param string         $lastName
     * @param string         $phone
     * @param string         $mobile
     * @param \DateTime|null $lastConnection
     * @param bool           $acceptNewsLetter
     * @param Rights|null    $rights
     * @param int            $rightsId
     * @param string         $civility
     * @param string         $fax
     */
    public function __construct(\int $id = 0, \string $email = '', \string $password = '', \int $type = 0,
                                Structure $structure = null, \int $structureId = 0, \string $firstName = '',
                                \string $lastName = '', \string $phone = '', \string $mobile = '',
                                \DateTime $lastConnection = null, \bool $acceptNewsLetter = false,
                                Rights $rights = null, \int $rightsId = 0, \string $civility = '', \string $fax = '') {
        parent::__construct($id, $email, $lastName, $firstName, $civility, $phone, $mobile, $fax, $acceptNewsLetter);
        $this->setPassword($password);
        $this->setType($type);
        $this->setStructure(is_a($structure, '\Users\Structure') ? $structure : new Structure());
        $this->setStructureId($structureId);
        $this->setLastConnection(is_a($lastConnection, '\DateTime') ? $lastConnection : new \DateTime());
        $this->setRights(is_a($rights, '\Users\Rights') ? $rights : new Rights());
        $this->setRightsId($rightsId);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return bool
     */
    public function isOwner(){
        return $this->getType() <= 2 ? true : false;
    }

    /**
     * @return bool
     */
    public function isAdmin(){
        return $this->getType() == 1 ? true : false;
    }
}