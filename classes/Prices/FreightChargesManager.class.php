<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 04/02/2016
 * Time: 16:56
 */

namespace Prices;


class FreightChargesManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return FreightCharges
     */
    private static function constructFreightCharges(\PDO $db, array $data):FreightCharges{
        $id = (int)$data['id'];
        $amount = (float)$data['amount'];
        $date = new \DateTime($data['date']);

        return new FreightCharges($id, $amount, $date);
    }

    /**
     * @param \PDO $db
     * @param int  $chargesId
     *
     * @return \Exception|FreightCharges
     */
    public static function fetchFreightCharges(\PDO $db, \int $chargesId){
        try{
            $query = 'SELECT * FROM prices_freightCharges WHERE id = :chargesId;';
            $binds = array(':chargesId' => $chargesId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver les frais de transport demandés.');
            }

            return self::constructFreightCharges($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|FreightCharges
     */
    public static function fetchLastCreatedFreightCharges(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_freightCharges ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun frais de transport actuellement en base.');
            }

            return self::constructFreightCharges($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO           $db
     * @param FreightCharges $charges
     * @param int            $countryId
     *
     * @return \Exception|FreightCharges
     */
    public static function insertFreightCharges(\PDO $db, FreightCharges $charges, \int $countryId){
        try{
            $query = 'INSERT INTO prices_freightCharges (amount, country_id, date)
                                                  VALUES(:amount, :countryId, :date)
            ;';
            $binds = array(
                ':amount'    => $charges->getAmount(),
                ':countryId' => $countryId,
                ':date'      => $charges->getDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            $charges->setId($db->lastInsertId());

            return $charges;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO           $db
     * @param FreightCharges $charges
     *
     * @return bool|\Exception
     */
    public static function hydrateFreightCharges(\PDO $db, FreightCharges $charges){
        try{
            $query = 'UPDATE prices_freightCharges SET amount = :amount, date = :date
                                                  WHERE id = :chargesId
            ;';
            $binds = array(
                ':amount'    => $charges->getAmount(),
                ':chargesId' => $charges->getId(),
                ':date'      => $charges->getDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteFreightCharges(\PDO $db, \int $chargesId){
        //TODO : Voir comment on gère la suppression de frais de transport
    }

    /**
     * @param \PDO $db
     * @param int  $countryId
     *
     * @return \Exception|FreightCharges
     */
    public static function fetchFreightChargesFromCountry(\PDO $db, \int $countryId){
        try{
            $query = 'SELECT * FROM prices_freightCharges WHERE country_id = :countryId ORDER BY date DESC LIMIT 1;';
            $binds = array(':countryId' => $countryId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver les frais de transport demandés.');
            }

            return self::constructFreightCharges($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
}