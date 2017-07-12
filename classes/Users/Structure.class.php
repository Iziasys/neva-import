<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 28/01/2016
 * Time: 08:25
 */

namespace Users;


class Structure
{
    private $id, $structureName, $address, $postalCode, $town, $phone, $mobile, $fax, $email, $isPartner,
        $acceptNewsLetter, $imageName, $societyDetails, $siret, $ape, $packageContent, $packageProvision, $isPrimary,
        $defaultMargin, $freightCharges, $defaultWarranty, $defaultFunding;

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
     * @param string $structureName
     */
    public function setStructureName(\string $structureName){
        $this->structureName = (string)$structureName;
    }

    /**
     * @return string
     */
    public function getStructureName():\string{
        return $this->structureName;
    }

    /**
     * @param string $address
     */
    public function setAddress(\string $address){
        $this->address = (string)$address;
    }

    /**
     * @return string
     */
    public function getAddress():\string{
        return $this->address;
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
        $this->mobile = (string)$mobile;
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
     * @param bool $isPartner
     */
    public function setIsPartner(\bool $isPartner){
        $this->isPartner = (bool)$isPartner;
    }

    /**
     * @return bool
     */
    public function getIsPartner():\bool{
        return $this->isPartner;
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

    /**
     * @param string $imageName
     */
    public function setImageName(\string $imageName){
        $this->imageName = (string)$imageName;
    }

    /**
     * @return string
     */
    public function getImageName():\string{
        return $this->imageName;
    }

    /**
     * @param string $societyDetails
     */
    public function setSocietyDetails(\string $societyDetails){
        $this->societyDetails = (string)$societyDetails;
    }

    /**
     * @return string
     */
    public function getSocietyDetails():\string{
        return $this->societyDetails;
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

    /**
     * @param string $ape
     */
    public function setApe(\string $ape){
        $this->ape = (string)$ape;
    }

    /**
     * @return string
     */
    public function getApe():\string{
        return $this->ape;
    }

    /**
     * @param string $packageContent
     */
    public function setPackageContent(\string $packageContent){
        $this->packageContent = (string)$packageContent;
    }

    /**
     * @return string
     */
    public function getPackageContent():\string{
        return $this->packageContent;
    }

    /**
     * @param float $packageProvision
     */
    public function setPackageProvision(\float $packageProvision){
        $this->packageProvision = (float)$packageProvision;
    }

    /**
     * @return float
     */
    public function getPackageProvision():\float{
        return $this->packageProvision;
    }

    /**
     * @param bool $isPrimary
     */
    public function setIsPrimary(\bool $isPrimary){
        $this->isPrimary = (bool)$isPrimary;
    }

    /**
     * @return bool
     */
    public function getIsPrimary():\bool{
        return $this->isPrimary;
    }

    /**
     * @param int $defaultMargin
     */
    public function setDefaultMargin(\int $defaultMargin){
        $this->defaultMargin = (int)$defaultMargin;
    }

    /**
     * @return int
     */
    public function getDefaultMargin():\int{
        return $this->defaultMargin;
    }

    /**
     * @param float|null $freightCharges
     */
    public function setFreightCharges($freightCharges){
        if($freightCharges === null){
            $this->freightCharges = null;
        }
        else{
            $this->freightCharges = (float)$freightCharges;
        }
    }

    /**
     * @return float|null
     */
    public function getFreightCharges(){
        return $this->freightCharges;
    }

    /**
     * @param string $defaultWarranty
     */
    public function setDefaultWarranty(\string $defaultWarranty){
        $this->defaultWarranty = $defaultWarranty;
    }

    /**
     * @return string
     */
    public function getDefaultWarranty():\string{
        return $this->defaultWarranty;
    }

    /**
     * @param string $defaultFunding
     */
    public function setDefaultFunding(\string $defaultFunding){
        $this->defaultFunding = $defaultFunding;
    }

    /**
     * @return string
     */
    public function getDefaultFunding():\string{
        return $this->defaultFunding;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Structure constructor.
     *
     * @param int        $id
     * @param string     $structureName
     * @param string     $address
     * @param string     $postalCode
     * @param string     $town
     * @param string     $phone
     * @param string     $mobile
     * @param string     $fax
     * @param string     $email
     * @param bool       $isPartner
     * @param bool       $acceptNewsLetter
     * @param string     $imageName
     * @param string     $societyDetails
     * @param string     $siret
     * @param string     $ape
     * @param string     $packageContent
     * @param float      $packageProvision
     * @param bool       $isPrimary
     * @param int        $defaultMargin
     * @param float|null $freightCharges
     * @param string     $defaultWarranty
     * @param string     $defaultFunding
     */
    public function __construct(\int $id = 0, \string $structureName = '', \string $address = '', \string $postalCode = '',
                                \string $town = '', \string $phone = '', \string $mobile = '', \string $fax = '',
                                \string $email = '', \bool $isPartner = false, \bool $acceptNewsLetter = false,
                                \string $imageName = '', \string $societyDetails = '', \string $siret = '',
                                \string $ape = '', \string $packageContent = '', \float $packageProvision = 0.0,
                                \bool $isPrimary = false, \int $defaultMargin = 0, \float $freightCharges = null,
                                \string $defaultWarranty = '', \string $defaultFunding = ''
    ){
        $this->setId($id);
        $this->setStructureName($structureName);
        $this->setAddress($address);
        $this->setPostalCode($postalCode);
        $this->setTown($town);
        $this->setPhone($phone);
        $this->setMobile($mobile);
        $this->setFax($fax);
        $this->setEmail($email);
        $this->setIsPartner($isPartner);
        $this->setAcceptNewsLetter($acceptNewsLetter);
        $this->setImageName($imageName);
        $this->setSocietyDetails($societyDetails);
        $this->setSiret($siret);
        $this->setApe($ape);
        $this->setPackageContent($packageContent);
        $this->setPackageProvision($packageProvision);
        $this->setIsPrimary($isPrimary);
        $this->setDefaultMargin($defaultMargin);
        $this->setFreightCharges($freightCharges);
        $this->setDefaultWarranty($defaultWarranty);
        $this->setDefaultFunding($defaultFunding);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return string
     */
    public function getDepartment(){
        $postalCode = str_pad($this->postalCode, 5, STR_PAD_LEFT);

        return substr($postalCode, 0, 2);
    }
}