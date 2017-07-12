<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 15/02/2016
 * Time: 09:53
 */

namespace Vehicle;


class TransmissionManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Transmission
     */
    public static function constructTransmission(\PDO $db, array $data):Transmission{
        $id = empty($data['transmission_id']) ? (int)$data['id'] : (int)$data['transmission_id'];
        $name = empty($data['transmissionName']) ? (string)$data['name'] : (string)$data['transmissionName'];

        return new Transmission($id, $name);
    }

    /**
     * @param \PDO $db
     * @param int  $transmissionId
     *
     * @return \Exception|Transmission
     */
    public static function fetchTransmission(\PDO $db, \int $transmissionId){
        try{
            $query = 'SELECT * FROM vhcl_transmission WHERE id = :transmissionId;';
            $binds = array(':transmissionId' => $transmissionId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la transmission sélectionnée.');
            }

            return self::constructTransmission($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $transmissionName
     *
     * @return \Exception|Transmission
     */
    public static function fetchTransmissionByName(\PDO $db, \string $transmissionName){
        try{
            $query = 'SELECT * FROM vhcl_transmission WHERE name LIKE :transmissionName;';
            $binds = array(':transmissionName' => $transmissionName);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la transmission demandée.');
            }

            return self::constructTransmission($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Transmission
     */
    public static function fetchLastCreatedTransmission(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_transmission ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune transmission actuellement en base.');
            }

            return self::constructTransmission($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO         $db
     * @param Transmission $transmission
     *
     * @return \Exception|Transmission
     */
    public static function insertTransmission(\PDO $db, Transmission $transmission){
        try{
            $query = 'INSERT INTO vhcl_transmission (name) VALUES (:name) ON DUPLICATE KEY UPDATE name = name;';
            $binds = array(':name' => $transmission->getName());

            \executeInsert($db, $query, $binds);

            $transmission->setId($db->lastInsertId());
            return $transmission;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO         $db
     * @param Transmission $transmission
     *
     * @return bool|\Exception
     */
    public static function hydrateTransmission(\PDO $db, Transmission $transmission){
        try{
            $query = 'UPDATE vhcl_transmission SET name = :name WHERE id = :transmissionId;';
            $binds = array(':transmissionId' => $transmission->getId(), ':name' => $transmission->getName());

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteTransmission(\PDO $db, \int $transmissionId){
        //TODO : Voir comment on gère la suppression d'une transmission
    }
}