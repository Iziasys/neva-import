<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 04/02/2016
 * Time: 11:38
 */

namespace Prices;


class CurrencyManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Currency
     */
    private static function constructCurrency(\PDO $db, array $data):Currency{
        $id = (int)$data['id'];
        $currency = (string)$data['currency'];
        $abbreviation = (string)$data['abbreviation'];
        $symbol = (string)$data['symbol'];
        $exchangeRate = ExchangeRateManager::fetchExchangeRateFromCurrency($db, $id);

        return new Currency($id, $currency, $abbreviation, $symbol, $exchangeRate);
    }

    /**
     * @param \PDO $db
     * @param int  $currencyId
     *
     * @return \Exception|Currency
     */
    public static function fetchCurrency(\PDO $db, \int $currencyId){
        try{
            $query = 'SELECT * FROM prices_currency WHERE id = :currencyId;';
            $binds = array(':currencyId' => $currencyId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la devise demandée.');
            }

            return self::constructCurrency($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Currency
     */
    public static function fetchLastCreatedCurrency(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_currency ORDER BY id DESC LIMIT 1';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune devise actuellement en base.');
            }

            return self::constructCurrency($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param Currency $currency
     *
     * @return \Exception|Currency
     */
    public static function insertCurrency(\PDO $db, Currency $currency){
        try{
            //Vérification préalable de doublon
            if(self::isDuplicated($db, $currency)){
                throw new \Exception('Erreur, devise déjà existante en base.');
            }
            $query = 'INSERT INTO prices_currency   (currency, abbreviation, symbol)
                                    VALUES          (:currency, :abbreviation, :symbol)
            ;';
            $binds = array(
                ':currency'     => $currency->getCurrency(),
                ':abbreviation' => $currency->getAbbreviation(),
                ':symbol'       => $currency->getSymbol()
            );

            \executeInsert($db, $query, $binds);

            $currency->setId((int)$db->lastInsertId());
            return $currency;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param Currency $currency
     *
     * @return bool|\Exception
     */
    public static function hydrateCurrency(\PDO $db, Currency $currency){
        try{
            $query = 'UPDATE prices_currency SET currency = :currency, abbreviation = :abbreviation, symbol = :symbol
                                             WHERE id = :currencyId
            ;';
            $binds = array(
                ':currencyId'   => $currency->getId(),
                ':currency'     => $currency->getCurrency(),
                ':abbreviation' => $currency->getAbbreviation(),
                ':symbol'       => $currency->getSymbol()
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteCurrency(\PDO $db, \int $currencyId){
        //TODO : Voir comment on gère la suppression d'une devise
    }

    /**
     * @param \PDO     $db
     * @param Currency $currency
     *
     * @return bool|\Exception
     */
    private static function isDuplicated(\PDO $db, Currency $currency){
        try{
            $query = 'SELECT * FROM prices_currency WHERE   currency LIKE :currency
                                                    OR      abbreviation LIKE :abbreviation
                                                    OR      symbol LIKE :symbol
            ;';
            $binds = array(
                ':currency'     => $currency->getCurrency(),
                ':abbreviation' => $currency->getAbbreviation(),
                ':symbol'       => $currency->getSymbol()
            );

            $results = \executeSelect($db, $query, $binds);

            if(!empty($results)){
                return true;
            }
            else{
                return false;
            }
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $orderBy
     * @param string $way
     * @param int    $limit
     * @param int    $offset
     *
     * @return Currency[]|\Exception
     */
    public static function fetchCurrenciesList(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = 'SELECT * FROM prices_currency ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune devise actuellement en base.');
            }

            $currencies = array();
            foreach($results as $result){
                $currencies[] = self::constructCurrency($db, $result);
            }

            return $currencies;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}