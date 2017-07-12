<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 08/02/2016
 * Time: 08:36
 */

namespace Prices;


class FreightChargesInFranceManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return FreightChargesInFrance
     */
    private static function constructFreightCharges(\PDO $db, array $data):FreightChargesInFrance{
        $id = (int)$data['id'];
        $department = (string)$data['department'];
        $departmentName = (string)$data['departmentName'];
        $amount = (float)$data['amount'];
        $date = new \DateTime($data['date']);

        return new FreightChargesInFrance($id, $department, $departmentName, $amount, $date);
    }

    /**
     * @param \PDO $db
     * @param int  $chargesId
     *
     * @return \Exception|FreightChargesInFrance
     */
    public static function fetchFreightCharges(\PDO $db, \int $chargesId){
        try{
            $query = 'SELECT * FROM prices_freightChargesInFrance WHERE id = :chargesId;';
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
     * @return \Exception|FreightChargesInFrance
     */
    public static function fetchLastCreatedFreightCharges(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_freightChargesInFrance ORDER BY id DESC LIMIT 1;';
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
     * @param \PDO                   $db
     * @param FreightChargesInFrance $charges
     *
     * @return \Exception|FreightChargesInFrance
     */
    public static function insertFreightCharges(\PDO $db, FreightChargesInFrance $charges){
        try{
            $query = 'INSERT INTO prices_freightChargesInFrance (department, departmentName, amount, date)
                                                          VALUES(:department, :departmentName, :amount, :date)
            ;';
            $binds = array(
                ':department'     => $charges->getDepartment(),
                ':departmentName' => $charges->getDepartmentName(),
                ':amount'         => $charges->getAmount(),
                ':date'           => $charges->getDate()->format('Y-m-d H:i:s')
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
     * @param \PDO                   $db
     * @param FreightChargesInFrance $charges
     *
     * @return bool|\Exception
     */
    public static function hydrateFreightCharges(\PDO $db, FreightChargesInFrance $charges){
        try{
            $query = 'UPDATE prices_freightChargesInFrance SET  department = :department,
                                                                departmentName = :departmentName,
                                                                amount = :amount,
                                                                date = :date
                                                  WHERE id = :chargesId
            ;';
            $binds = array(
                ':department'     => $charges->getDepartment(),
                ':departmentName' => $charges->getDepartmentName(),
                ':amount'         => $charges->getAmount(),
                ':chargesId'      => $charges->getId(),
                ':date'           => $charges->getDate()->format('Y-m-d H:i:s')
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
     *
     * @return \Exception|int
     */
    public static function countFreightCharges(\PDO $db){
        try{
            $query = 'SELECT COUNT(*) as nbRes FROM prices_freightChargesInFrance;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun frais de transport actuellement en base.');
            }

            return (int)$results[0]['nbRes'];
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
     * @return FreightChargesInFrance[]|\Exception
     */
    public static function fetchFreightChargesList(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 30, \int $offset = 0){
        try{
            $query = 'SELECT * FROM prices_freightChargesInFrance ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun frais de transport actuellement en base.');
            }

            $freightChargesList = array();
            foreach($results as $result){
                $freightChargesList[] = self::constructFreightCharges($db, $result);
            }
            return $freightChargesList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $department
     *
     * @return \Exception|FreightChargesInFrance
     */
    public static function fetchFreightChargesByDepartment(\PDO $db, \int $department){
        try{
            $query = 'SELECT * FROM prices_freightChargesInFrance WHERE department = :department;';
            $binds = array(':department' => $department);

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