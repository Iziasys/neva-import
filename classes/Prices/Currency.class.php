<?php

/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 18/01/2016
 * Time: 15:30
 */
namespace Prices;
class Currency{
    private $id, $currency, $abbreviation, $symbol, $exchangeRate;

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
     * @param string $currency
     */
    public function setCurrency(\string $currency){
        $this->currency = (string)$currency;
    }

    /**
     * @return string
     */
    public function getCurrency():\string{
        return $this->currency;
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
     * @param string $symbol
     */
    public function setSymbol(\string $symbol){
        $this->symbol = (string)$symbol;
    }

    /**
     * @return string
     */
    public function getSymbol():\string{
        return $this->symbol;
    }

    /**
     * @param ExchangeRate $exchangeRate
     */
    public function setExchangeRate(ExchangeRate $exchangeRate){
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * @return ExchangeRate
     */
    public function getExchangeRate():ExchangeRate{
        return $this->exchangeRate;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Currency constructor.
     * @param int $id
     * @param string $currency
     * @param string $abbreviation
     * @param string $symbol
     * @param ExchangeRate|null $exchangeRate
     */
    public function __construct($id = 0, $currency = '', $abbreviation = '', $symbol = '', $exchangeRate = null){
        $this->setId($id);
        $this->setCurrency($currency);
        $this->setAbbreviation($abbreviation);
        $this->setSymbol($symbol);
        $this->setExchangeRate(is_a($exchangeRate, '\Prices\ExchangeRate') ? $exchangeRate : new ExchangeRate());
    }
    /*******************CONSTRUCTOR*****************/
}