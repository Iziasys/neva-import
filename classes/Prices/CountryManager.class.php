<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 04/02/2016
 * Time: 17:09
 */

namespace Prices;


class CountryManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Country
     */
    private static function constructCountry(\PDO $db, array $data):Country{
        $id = (int)$data['id'];
        $name = (string)$data['name'];
        $abbreviation = (string)$data['abbreviation'];
        $currencyId = (int)$data['currency_id'];
        $currency = CurrencyManager::fetchCurrency($db, $currencyId);
        if(!is_a($currency, '\Prices\Currency')){
            $currency = new Currency();
        }
        if($id != 0){
            $vat = VatManager::fetchVatFromCountry($db, $id);
            $vatId = $vat->getId();
            $freightCharges = FreightChargesManager::fetchFreightChargesFromCountry($db, $id);
            $freightChargesId = $freightCharges->getId();
        }
        else{
            $vat = new Vat();
            $vatId = 0;
            $freightCharges = new FreightCharges();
            $freightChargesId = 0;
        }

        return new Country($id, $name, $abbreviation, $currency, $currencyId, $vat, $vatId, $freightCharges, $freightChargesId);
    }

    /**
     * @param \PDO $db
     * @param int  $countryId
     *
     * @return \Exception|Country
     */
    public static function fetchCountry(\PDO $db, \int $countryId){
        try{
            $query = 'SELECT * FROM prices_country WHERE id = :countryId;';
            $binds = array(':countryId' => $countryId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le pays demandé.');
            }

            return self::constructCountry($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Country
     */
    public static function fetchLastCreatedCountry(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_country ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun pays actuellement en base.');
            }

            return self::constructCountry($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO    $db
     * @param Country $country
     *
     * @return \Exception|Country
     */
    public static function insertCountry(\PDO $db, Country $country){
        try{
            $query = 'INSERT INTO prices_country (name, abbreviation, currency_id)
                                          VALUES (:name, :abbreviation, :currencyId)
            ;';
            $binds = array(
                ':name' => $country->getName(),
                ':abbreviation' => $country->getAbbreviation(),
                ':currencyId' => $country->getCurrencyId()
            );

            \executeInsert($db, $query, $binds);

            $country->setId($db->lastInsertId());
            return $country;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO    $db
     * @param Country $country
     *
     * @return bool|\Exception
     */
    public static function hydrateCountry(\PDO $db, Country $country){
        try{
            $query = 'UPDATE prices_country SET name = :name, abbreviation = :abbreviation WHERE id = :countryId;';
            $binds = array(
                ':countryId' => $country->getId(),
                ':name' => $country->getName(),
                ':abbreviation' => $country->getAbbreviation()
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteCountry(\PDO $db, \int $countryId){
        //TODO : Voir comment on gère la suppression d'un pays
    }

    /**
     * @param \PDO   $db
     * @param string $orderBy
     * @param string $way
     * @param int    $limit
     * @param int    $offset
     *
     * @return Country[]|\Exception
     */
    public static function fetchCountriesList(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = 'SELECT * FROM prices_country ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun pays actuellement en base.');
            }

            $countries = array();
            foreach($results as $result){
                $countries[] = self::constructCountry($db, $result);
            }

            return $countries;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}