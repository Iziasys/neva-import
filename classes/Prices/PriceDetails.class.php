<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 01/03/2016
 * Time: 11:06
 */

namespace Prices;


class PriceDetails
{
    private $buyingPrice, $changeRateToEuro, $freightChargesToFrance, $marginAmount, $marginPercentage, $managementFees,
        $freightChargesInFrance, $dealerMargin, $packageProvision, $optionPrice, $vatAmount, $registrationCardAmount, $bonusPenalty;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param float $buyingPrice
     */
    public function setBuyingPrice(\float $buyingPrice){
        $this->buyingPrice = $buyingPrice;
    }

    /**
     * @return float
     */
    public function getBuyingPrice():\float{
        return $this->buyingPrice;
    }

    /**
     * @param float $changeRateToEuro
     */
    public function setChangeRateToEuro(\float $changeRateToEuro){
        $this->changeRateToEuro = $changeRateToEuro;
    }

    /**
     * @return float
     */
    public function getChangeRateToEuro():\float{
        return $this->changeRateToEuro;
    }

    /**
     * @param float $freightChargesToFrance
     */
    public function setFreightChargesToFrance(\float $freightChargesToFrance){
        $this->freightChargesToFrance = $freightChargesToFrance;
    }

    /**
     * @return float
     */
    public function getFreightChargesToFrance():\float{
        return $this->freightChargesToFrance;
    }

    /**
     * @param float $marginAmount
     */
    public function setMarginAmount(\float $marginAmount){
        $this->marginAmount = $marginAmount;
    }

    /**
     * @return float
     */
    public function getMarginAmount():\float{
        return $this->marginAmount;
    }

    /**
     * @param float $marginPercentage
     */
    public function setMarginPercentage(\float $marginPercentage){
        $this->marginPercentage = $marginPercentage;
    }

    /**
     * @return float
     */
    public function getMarginPercentage():\float{
        return $this->marginPercentage;
    }

    /**
     * @param float $managementFees
     */
    public function setManagementFees(\float $managementFees){
        $this->managementFees = $managementFees;
    }

    /**
     * @return float
     */
    public function getManagementFees():\float{
        return $this->managementFees;
    }

    /**
     * @param float $freightChargesInFrance
     */
    public function setFreightChargesInFrance(\float $freightChargesInFrance){
        $this->freightChargesInFrance = $freightChargesInFrance;
    }

    /**
     * @return float
     */
    public function getFreightChargesInFrance():\float{
        return $this->freightChargesInFrance;
    }

    /**
     * @param float $dealerMargin
     */
    public function setDealerMargin(\float $dealerMargin){
        $this->dealerMargin = $dealerMargin;
    }

    /**
     * @return float
     */
    public function getDealerMargin():\float{
        return $this->dealerMargin;
    }

    /**
     * @param float $packageProvision
     */
    public function setPackageProvision(\float $packageProvision){
        $this->packageProvision = $packageProvision;
    }

    /**
     * @return float
     */
    public function getPackageProvision():\float{
        return $this->packageProvision;
    }

    /**
     * @param float $optionPrice
     */
    public function setOptionPrice(\float $optionPrice){
        $this->optionPrice = $optionPrice;
    }

    /**
     * @return float
     */
    public function getOptionPrice():\float{
        return $this->optionPrice;
    }

    /**
     * @param float $vatAmount
     */
    public function setVatAmount(\float $vatAmount){
        $this->vatAmount = $vatAmount;
    }

    /**
     * @return float
     */
    public function getVatAmount():\float{
        return $this->vatAmount;
    }

    /**
     * @param float $registrationCardAmount
     */
    public function setRegistrationCardAmount(\float $registrationCardAmount){
        $this->registrationCardAmount = $registrationCardAmount;
    }

    /**
     * @return float
     */
    public function getRegistrationCardAmount():\float{
        return $this->registrationCardAmount;
    }

    /**
     * @param float $bonusPenalty
     */
    public function setBonusPenalty(\float $bonusPenalty){
        $this->bonusPenalty = $bonusPenalty;
    }

    /**
     * @return float
     */
    public function getBonusPenalty():\float{
        return $this->bonusPenalty;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * PriceDetails constructor.
     *
     * @param float $buyingPrice
     * @param float $changeRateToEuro
     * @param float $freightChargesToFrance
     * @param float $marginAmount
     * @param float $marginPercentage
     * @param float $managementFees
     * @param float $freightChargesInFrance
     * @param float $dealerMargin
     * @param float $packageProvision
     * @param float $optionPrice
     * @param float $vatAmount
     * @param float $registrationCardAmount
     * @param float $bonusPenalty
     */
    public function __construct(\float $buyingPrice = 0.0, \float $changeRateToEuro = 0.0,
                                \float $freightChargesToFrance = 0.0, \float $marginAmount = 0.0, \float $marginPercentage = 0.0,
                                \float $managementFees = 0.0, \float $freightChargesInFrance = 0.0,
                                \float $dealerMargin = 0.0, \float $packageProvision = 0.0, \float $optionPrice = 0.0,
                                \float $vatAmount = 0.0, \float $registrationCardAmount = 0.0, \float $bonusPenalty = 0.0
    ){
        $this->setBuyingPrice($buyingPrice);
        $this->setChangeRateToEuro($changeRateToEuro);
        $this->setFreightChargesToFrance($freightChargesToFrance);
        if(empty($marginAmount)){
            $this->setMarginAmount($this->getPretaxBuyingPriceInEuro() * $marginPercentage / 100);
        }
        else{
            $this->setMarginAmount($marginAmount);
        }
        $this->setMarginPercentage($marginPercentage);
        $this->setManagementFees($managementFees);
        $this->setFreightChargesInFrance($freightChargesInFrance);
        $this->setDealerMargin($dealerMargin);
        $this->setOptionPrice($optionPrice);
        $this->setVatAmount($vatAmount);
        $this->setPackageProvision($packageProvision);
        $this->setRegistrationCardAmount($registrationCardAmount);
        $this->setBonusPenalty($bonusPenalty);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return float
     */
    public function getPretaxBuyingPrice():\float{
        return $this->buyingPrice;
    }

    /**
     * @return float
     */
    public function getPostTaxesBuyingPrice():\float{
        return VatManager::convertToPostTaxes($this->getPretaxBuyingPrice(), $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPretaxBuyingPriceInEuro():\float{
        return $this->getPretaxBuyingPrice()
                * $this->changeRateToEuro;
    }

    /**
     * @return float
     */
    public function getPostTaxesBuyingPriceInEuro():\float{
        return VatManager::convertToPostTaxes($this->getPretaxBuyingPrice()
                                              * $this->changeRateToEuro, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPretaxTransportedPrice():\float{
        return $this->getPretaxBuyingPriceInEuro()
                + $this->freightChargesToFrance;
    }

    /**
     * @return float
     */
    public function getPostTaxesTransportedPrice():\float{
        return VatManager::convertToPostTaxes($this->getPretaxBuyingPriceInEuro()
                                              + $this->freightChargesToFrance, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPretaxDealerBuyingPrice():\float{
        if(empty($this->marginAmount)){
            $this->setMarginAmount($this->getPretaxBuyingPriceInEuro() * $this->marginPercentage / 100);
        }
        return $this->getPretaxTransportedPrice()
                + $this->marginAmount
                + $this->managementFees
                + $this->freightChargesInFrance;
    }

    /**
     * @return float
     */
    public function getPostTaxesDealerBuyingPrice():\float{
        if(empty($this->marginAmount)){
            $this->setMarginAmount($this->getPretaxBuyingPriceInEuro() * $this->marginPercentage / 100);
        }
        return VatManager::convertToPostTaxes($this->getPretaxTransportedPrice()
                                              + $this->marginAmount
                                              + $this->managementFees
                                              + $this->freightChargesInFrance, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPretaxDealerSellingPrice():\float{
        return $this->getPretaxDealerBuyingPrice()
                + $this->dealerMargin;
    }

    /**
     * @return float
     */
    public function getPostTaxesDealerSellingPrice():\float{
        return VatManager::convertToPostTaxes($this->getPretaxDealerBuyingPrice()
                                                   + $this->dealerMargin, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPretaxClientBuyingPrice():\float{
        return $this->getPretaxDealerBuyingPrice()
                    + $this->dealerMargin
                    + $this->optionPrice;
    }

    /**
     * @return float
     */
    public function getPostTaxesClientBuyingPrice():\float{
        return VatManager::convertToPostTaxes($this->getPretaxDealerBuyingPrice()
                                                   + $this->dealerMargin
                                                   + $this->optionPrice, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPretaxAllIncludedPrice():\float{
        return $this->getPretaxClientBuyingPrice()
                + $this->packageProvision;
    }

    /**
     * @return float
     */
    public function getPostTaxesAllIncludedPrice():\float{
        return VatManager::convertToPostTaxes($this->getPretaxClientBuyingPrice()
                                              + $this->packageProvision, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPretaxKeyInHandPrice():\float{
        return $this->getPretaxAllIncludedPrice()
                + $this->registrationCardAmount
                + $this->bonusPenalty;
    }

    /**
     * @return float
     */
    public function getPostTaxesKeyInHandPrice():\float{
        return VatManager::convertToPostTaxes($this->getPretaxAllIncludedPrice(), $this->vatAmount) + $this->registrationCardAmount + $this->bonusPenalty;
    }

    /**
     * @return float
     */
    public function getPostTaxesMarginAmount():\float{
        return VatManager::convertToPostTaxes($this->marginAmount, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPostTaxesManagementFees():\float{
        return VatManager::convertToPostTaxes($this->managementFees, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPostTaxesDealerMargin():\float{
        return VatManager::convertToPostTaxes($this->dealerMargin, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPostTaxesOptionPrice():\float{
        return VatManager::convertToPostTaxes($this->optionPrice, $this->vatAmount);
    }

    /**
     * @return float
     */
    public function getPostTaxesPackageProvision():\float{
        return VatManager::convertToPostTaxes($this->packageProvision, $this->vatAmount);
    }

    public function getPostTaxesFreightChargesInFrance():\float{
        return VatManager::convertToPostTaxes($this->freightChargesInFrance, $this->vatAmount);
    }
}