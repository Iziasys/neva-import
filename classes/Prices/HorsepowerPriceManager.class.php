<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 29/02/2016
 * Time: 09:17
 */

namespace Prices;


class HorsepowerPriceManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return HorsepowerPrice
     */
    public static function constructPrice(\PDO $db, array $data):HorsepowerPrice{
        $id = !empty($data['registrationCard_id']) ? (int)$data['registrationCard_id'] : (int)$data['id'];
        $department = (string)$data['department'];
        $amount = !empty($data['registrationCard_amount']) ? (float)$data['registrationCard_amount'] : (float)$data['amount'];
        $refreshDate = !empty($data['registrationCard_date']) ?
            new \DateTime($data['registrationCard_date']) :
            new \DateTime($data['refreshDate']);

        return new HorsepowerPrice($id, $department, $amount, $refreshDate);
    }

    /**
     * @param \PDO $db
     * @param int  $priceId
     *
     * @return \Exception|HorsepowerPrice
     */
    public static function fetchPrice(\PDO $db, \int $priceId){
        try{
            $query = 'SELECT * FROM prices_horsepower WHERE id = :priceId;';
            $binds = array(':priceId' => $priceId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le tarif fiscal demandé.');
            }

            return self::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|HorsepowerPrice
     */
    public static function fetchLastCreatedPrice(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_horsepower ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun tarif fiscal actuellement en base.');
            }

            return self::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO            $db
     * @param HorsepowerPrice $price
     *
     * @return \Exception|HorsepowerPrice
     */
    public static function insertPrice(\PDO $db, HorsepowerPrice $price){
        try{
            $query = 'INSERT INTO prices_horsepower (department, amount, refreshDate) VALUES(:department, :amount, :refreshDate);';
            $binds = array(
                ':department'  => $price->getDepartment(),
                ':amount'      => $price->getAmount(),
                ':refreshDate' => $price->getRefreshDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            $price->setId($db->lastInsertId());

            return $price;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO            $db
     * @param HorsepowerPrice $price
     *
     * @return bool|\Exception
     */
    public static function hydratePrice(\PDO $db, HorsepowerPrice $price){
        try{
            $query = 'UPDATE prices_horsepower SET department = :department, amount = :amount, refreshDate = :refreshDate WHERE id = :priceId;';
            $binds = array(
                ':priceId'     => $price->getId(),
                ':department'  => $price->getDepartment(),
                ':amount'      => $price->getAmount(),
                ':refreshDate' => $price->getRefreshDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|int
     */
    public static function countHorsePower(\PDO $db){
        try{
            $query = 'SELECT COUNT(*) as nbRes FROM prices_horsepower;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun tarif fiscal actuellement en base.');
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
     * @return \Exception|HorsepowerPrice[]
     */
    public static function fetchHorsePowerList(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 30, \int $offset = 0){
        try{
            $query = 'SELECT * FROM prices_horsepower ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun tarif fiscal actuellement en base.');
            }

            $freightChargesList = array();
            foreach($results as $result){
                $freightChargesList[] = self::constructPrice($db, $result);
            }
            return $freightChargesList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    //TODO : fetchPriceFromDepartment()
    public static function fetchPriceFromDepartment(\PDO $db, \string $department){
        try{
            $query = 'SELECT * FROM prices_horsepower WHERE department = :department;';
            $binds = array(':department' => $department);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le tarif fiscal demandé.');
            }

            return self::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|float
     */
    public static function fetchFixAmount(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_fixAmountRC ORDER BY refreshDate DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun frais fixes actuellement en base.');
            }

            return (float)$results[0]['amount'];
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param float $amount
     *
     * @return bool|\Exception
     */
    public static function insertFixAmount(\PDO $db, \float $amount){
        try{
            $today = new \DateTime();
            $query = 'INSERT INTO prices_fixAmountRC (amount, refreshDate) VALUES (:amount, :refreshDate);';
            $binds = array(
                ':amount'      => $amount,
                ':refreshDate' => $today->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}