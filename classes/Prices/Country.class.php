<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 18/01/2016
 * Time: 16:26
 */

namespace Prices;


class Country{
    private $id, $name, $abbreviation, $currency, $currencyId, $vat, $vatId, $freightCharges, $freightChargesId;

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
     * @param string $abbreviation
     */
    public function setAbbreviation(\string $abbreviation){
        $this->abbreviation = (string)$abbreviation;
    }

    /**
     * @return string
     */
    public function getAbbreviation():\string{
        return $this->abbreviation;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency){
        $this->currency = $currency;
    }

    /**
     * @return Currency
     */
    public function getCurrency():Currency{
        return $this->currency;
    }

    /**
     * @param int $currencyId
     */
    public function setCurrencyId(\int $currencyId){
        $this->currencyId = (int)$currencyId;
    }

    /**
     * @return int
     */
    public function getCurrencyId():\int{
        return $this->currencyId;
    }

    /**
     * @param Vat $vat
     */
    public function setVat(Vat $vat){
        $this->vat = $vat;
    }

    /**
     * @return Vat
     */
    public function getVat():Vat{
        return $this->vat;
    }

    /**
     * @param int $vatId
     */
    public function setVatId(\int $vatId){
        $this->vatId = (int)$vatId;
    }

    /**
     * @return int
     */
    public function getVatId():\int{
        return $this->vatId;
    }

    /**
     * @param FreightCharges $freightCharges
     */
    public function setFreightCharges(FreightCharges $freightCharges){
        $this->freightCharges = $freightCharges;
    }

    /**
     * @return FreightCharges
     */
    public function getFreightCharges():FreightCharges{
        return $this->freightCharges;
    }

    /**
     * @param int $freightChargesId
     */
    public function setFreightChargesId(\int $freightChargesId){
        $this->freightChargesId = (int)$freightChargesId;
    }

    /**
     * @return int
     */
    public function getFreightChargesId():\int{
        return $this->freightChargesId;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Country constructor.
     *
     * @param int                 $id
     * @param string              $name
     * @param string              $abbreviation
     * @param Currency|null       $currency
     * @param int                 $currencyId
     * @param Vat|null            $vat
     * @param int                 $vatId
     * @param FreightCharges|null $freightCharges
     * @param int                 $freightChargesId
     */
    public function __construct(\int $id = 0, \string $name = '', \string $abbreviation = '', Currency $currency = null,
                                \int $currencyId = 0, Vat $vat = null, \int $vatId = 0,
                                FreightCharges $freightCharges = null, \int $freightChargesId = 0){
        $this->setId($id);
        $this->setName($name);
        $this->setAbbreviation($abbreviation);
        $this->setCurrency(is_a($currency, '\Prices\Currency') ? $currency : new Currency());
        $this->setCurrencyId($currencyId);
        $this->setVat(is_a($vat, '\Prices\Vat') ? $vat : new Vat());
        $this->setVatId($vatId);
        $this->setFreightCharges(is_a($freightCharges, '\Prices\FreightCharges') ? $freightCharges : new FreightCharges());
        $this->setFreightChargesId($freightChargesId);
    }
    /*******************CONSTRUCTOR*****************/
}