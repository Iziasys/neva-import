<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 04/02/2016
 * Time: 16:44
 */

namespace Prices;


class VatManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Vat
     */
    private static function constructVat(\PDO $db, array $data):Vat{
        $id = (int)$data['id'];
        $amount = (float)$data['amount'];
        $vatDate = new \DateTime($data['vatDate']);

        return new Vat($id, $amount, $vatDate);
    }

    /**
     * @param \PDO $db
     * @param int  $vatId
     *
     * @return \Exception|Vat
     */
    public static function fetchVat(\PDO $db, \int $vatId){
        try{
            $query = 'SELECT * FROM prices_vat WHERE id = :vatId;';
            $binds = array(':vatId' => $vatId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la TVA demandée.');
            }

            return self::constructVat($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Vat
     */
    public static function fetchLastCreatedVat(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_vat ORDER BY id DESC LIMIT 1';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune TVA actuellement en base.');
            }

            return self::constructVat($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param Vat  $vat
     * @param int  $countryId
     *
     * @return \Exception|Vat
     */
    public static function insertVat(\PDO $db, Vat $vat, \int $countryId){
        try{
            $query = 'INSERT INTO prices_vat (country_id, amount, vatDate)
                                    VALUES   (:countryId, :amount, :vatDate)
            ;';
            $binds = array(
                ':countryId' => $countryId,
                ':amount'    => $vat->getAmount(),
                ':vatDate'   => $vat->getVatDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            $vat->setId($db->lastInsertId());
            return $vat;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param Vat  $vat
     *
     * @return bool|\Exception
     */
    public static function hydrateVat(\PDO $db, Vat $vat){
        try{
            $query = 'UPDATE prices_vat SET amount = :amount, vatDate = :vatDate WHERE id = :vatId;';
            $binds = array(
                ':vatId'   => $vat->getId(),
                ':amount'  => $vat->getAmount(),
                ':vatDate' => $vat->getVatDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteVat(\PDO $db, \int $vatId){
        //TODO : Voir comment on gère la suppression d'une TVA
    }

    /**
     * @param \PDO $db
     * @param int  $countryId
     *
     * @return \Exception|Vat
     */
    public static function fetchVatFromCountry(\PDO $db, \int $countryId){
        try{
            $query = 'SELECT * FROM prices_vat WHERE country_id = :countryId ORDER BY vatDate DESC LIMIT 1;';
            $binds = array(':countryId' => $countryId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la TVA demandée.');
            }

            return self::constructVat($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param float $price
     * @param float $vatAmount
     *
     * @return \Exception|float
     */
    public static function convertToPretax(\float $price, \float $vatAmount){
        try{
            return $price / (1 + ($vatAmount / 100));
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param float $price
     * @param float $vatAmount
     *
     * @return \Exception|float
     */
    public static function convertToPostTaxes(\float $price, \float $vatAmount){
        try{
            return $price * (1 + ($vatAmount / 100));
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Vat
     */
    public static function fetchFrenchVat(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_vat WHERE country_id = 1 ORDER BY vatDate DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la TVA demandée.');
            }

            return self::constructVat($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
}