<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 04/02/2016
 * Time: 11:40
 */

namespace Prices;


class ExchangeRateManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return ExchangeRate
     */
    private static function constructExchangeRate(\PDO $db, array $data):ExchangeRate{
        $id = (int)$data['id'];
        $rateToEuro = (float)$data['rateToEuro'];
        $rateDate = new \DateTime($data['rateDate']);

        return new ExchangeRate($id, $rateToEuro, $rateDate);
    }

    /**
     * @param \PDO $db
     * @param int  $rateId
     *
     * @return \Exception|ExchangeRate
     */
    public static function fetchExchangeRate(\PDO $db, \int $rateId){
        try{
            $query = 'SELECT * FROM prices_exchangeRate WHERE id = :rateId ;';
            $binds = array(':rateId' => $rateId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le taux de change demandé.');
            }

            return self::constructExchangeRate($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|ExchangeRate
     */
    public static function fetchLastCreatedExchangeRate(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_exchangeRate ORDER BY id DESC LIMIT 1 ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun taux de change actuellement en base.');
            }

            return self::constructExchangeRate($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO         $db
     * @param ExchangeRate $rate
     * @param int          $currencyId
     *
     * @return \Exception|ExchangeRate
     */
    public static function insertExchangeRate(\PDO $db, ExchangeRate $rate, \int $currencyId){
        try{
            $query = 'INSERT INTO prices_exchangeRate   ( currency_id,
                                                          rateToEuro,
                                                          rateDate
                                                        )
                                                VALUES  (
                                                          :currencyId,
                                                          :rateToEuro,
                                                          :rateDate
                                                )
            ;';
            $binds = array(
                ':currencyId' => $currencyId,
                ':rateToEuro' => $rate->getRateToEuro(),
                ':rateDate'   => $rate->getRateDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            $rate->setId($db->lastInsertId());
            return $rate;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO         $db
     * @param ExchangeRate $rate
     *
     * @return bool|\Exception
     */
    public static function hydrateExchangeRate(\PDO $db, ExchangeRate $rate){
        try{
            $query = 'UPDATE prices_exchangeRate SET    rateToEuro = :rateToEuro,
                                                        rateDate = :rateDate
                                                 WHERE id = :rateId
            ;';
            $binds = array(
                ':rateId' => $rate->getId(),
                ':rateToEuro' => $rate->getRateToEuro(),
                ':rateDate'   => $rate->getRateDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteExchangeRate(\PDO $db, \int $rateId){
        //TODO : Voir comment on gère la suppression d'un taux de change
    }

    /**
     * @param \PDO $db
     * @param int  $currencyId
     *
     * @return \Exception|ExchangeRate
     */
    public static function fetchExchangeRateFromCurrency(\PDO $db, \int $currencyId){
        try{
            $query = 'SELECT * FROM prices_exchangeRate WHERE currency_id = :currencyId ORDER BY rateDate DESC LIMIT 1;';
            $binds = array(':currencyId' => $currencyId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun taux de change actuellement en base pour cette devise.');
            }

            return self::constructExchangeRate($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
}