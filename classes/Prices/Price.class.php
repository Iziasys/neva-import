<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 09:12
 */

namespace Prices;


class Price
{
    private $id, $pretaxBuyingPrice, $postTaxesPublicPrice, $country, $countryId, $currency, $currencyId, $margin, $maximumDiscount, $priceDate, $managementFees;

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
     * @param float $pretaxBuyingPrice
     */
    public function setPretaxBuyingPrice(\float $pretaxBuyingPrice){
        $this->pretaxBuyingPrice = (float)$pretaxBuyingPrice;
    }

    /**
     * @return float
     */
    public function getPretaxBuyingPrice():\float{
        return $this->pretaxBuyingPrice;
    }

    /**
     * @param float $postTaxesPublicPrice
     */
    public function setPostTaxesPublicPrice(\float $postTaxesPublicPrice){
        $this->postTaxesPublicPrice = (float)$postTaxesPublicPrice;
    }

    /**
     * @return float
     */
    public function getPostTaxesPublicPrice():\float{
        return $this->postTaxesPublicPrice;
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
     * @param float $margin
     */
    public function setMargin(\float $margin){
        $this->margin = (float)$margin;
    }

    /**
     * @return float
     */
    public function getMargin():\float{
        return $this->margin;
    }

    /**
     * @param float $maximumDiscount
     */
    public function setMaximumDiscount(\float $maximumDiscount){
        $this->maximumDiscount = (float)$maximumDiscount;
    }

    /**
     * @return float
     */
    public function getMaximumDiscount():\float{
        return $this->maximumDiscount;
    }

    /**
     * @param \DateTime $priceDate
     */
    public function setPriceDate(\DateTime $priceDate){
        $this->priceDate = $priceDate;
    }

    /**
     * @return \DateTime
     */
    public function getPriceDate():\DateTime{
        return $this->priceDate;
    }

    /**
     * @param float $managementFees
     */
    public function setManagementFees(\float $managementFees){
        $this->managementFees = (float)$managementFees;
    }

    /**
     * @return float
     */
    public function getManagementFees():\float{
        return $this->managementFees;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Price constructor.
     *
     * @param int            $id
     * @param float          $pretaxBuyingPrice
     * @param float          $postTaxPublicPrice
     * @param Country|null   $country
     * @param int            $countryId
     * @param Currency|null  $currency
     * @param int            $currencyId
     * @param float          $margin
     * @param float          $maximumDiscount
     * @param \DateTime|null $priceDate
     * @param float          $managementFees
     */
    public function __construct(\int $id = 0, \float $pretaxBuyingPrice = 0.0, \float $postTaxPublicPrice = 0.0,
                                Country $country = null, \int $countryId = 0, Currency $currency = null, \int $currencyId = 0,
                                \float $margin = 0.0, \float $maximumDiscount = 0.0, \DateTime $priceDate = null,
                                \float $managementFees = 0.0)
    {
        $this->setId($id);
        $this->setPretaxBuyingPrice($pretaxBuyingPrice);
        $this->setPostTaxesPublicPrice($postTaxPublicPrice);
        $this->setCountry(is_a($country, '\Prices\Country') ? $country : new Country());
        $this->setCountryId($countryId);
        $this->setCurrency(is_a($currency, '\Prices\Currency') ? $currency : new Currency());
        $this->setCurrencyId($currencyId);
        $this->setMargin($margin);
        $this->setMaximumDiscount($maximumDiscount);
        $this->setPriceDate(is_a($priceDate, '\DateTime') ? $priceDate : new \DateTime());
        $this->setManagementFees($managementFees);
    }
    /*******************CONSTRUCTOR*****************/

    public function getPretaxSellingPrice(){
        return $this->getPretaxBuyingPrice() * (1 + $this->getMargin() / 100) + $this->getManagementFees();
    }
}