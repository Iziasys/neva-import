<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 12/02/2016
 * Time: 16:53
 */

namespace Vehicle;


class FuelManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Fuel
     */
    public static function constructFuel(\PDO $db, array $data):Fuel{
        $id = empty($data['fuel_id']) ? (int)$data['id'] : (int)$data['fuel_id'];
        $name = empty($data['fuelName']) ? (string)$data['name'] : (string)$data['fuelName'];

        return new Fuel($id, $name);
    }

    /**
     * @param \PDO $db
     * @param int  $fuelId
     *
     * @return \Exception|Fuel
     */
    public static function fetchFuel(\PDO $db, \int $fuelId){
        try{
            $query = 'SELECT * FROM vhcl_fuel WHERE id = :fuelId;';
            $binds = array(':fuelId' => $fuelId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le carburant demandé.');
            }

            return self::constructFuel($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Fuel
     */
    public static function fetchLastCreatedFuel(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_fuel ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun carburant actuellement en base.');
            }

            return self::constructFuel($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param Fuel $fuel
     *
     * @return \Exception|Fuel
     */
    public static function insertFuel(\PDO $db, Fuel $fuel){
        try{
            $query = 'INSERT INTO vhcl_fuel (name) VALUES (:name);';
            $binds = array(':name' => $fuel->getName());

            \executeInsert($db, $query, $binds);

            $fuel->setId($db->lastInsertId());
            return $fuel;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param Fuel $fuel
     *
     * @return bool|\Exception
     */
    public static function hydrateFuel(\PDO $db, Fuel $fuel){
        try{
            $query = 'UPDATE vhcl_fuel SET name = :name WHERE id = :fuelId ;';
            $binds = array(':name' => $fuel->getName(), ':fuelId' => $fuel->getId());

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteFuel(\PDO $db, \int $fuelId){
        //TODO : Voir comment on gère la suppression d'un carburant
    }

    /**
     * @param \PDO   $db
     * @param string $orderBy
     *
     * @return \Exception|Fuel[]
     */
    public static function fetchFuelList(\PDO $db, \string $orderBy = 'id'){
        try{
            $query = 'SELECT * FROM vhcl_fuel ORDER BY '.$orderBy.' ASC ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun carburant actuellement en base.');
            }

            $fuelArray = array();
            foreach($results as $result){
                $fuelArray[] =  self::constructFuel($db, $result);
            }
            return $fuelArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $fuelId
     *
     * @return bool|\Exception
     */
    public static function doesFuelExist(\PDO $db, \int $fuelId){
        try{
            $query = 'SELECT COUNT(*) FROM vhcl_fuel WHERE id = :fuelId;';
            $binds = array(':fuelId' => $fuelId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                return false;
            }
            else{
                return true;
            }
        }
        catch(\Exception $e){
            return $e;
        }
    }
}