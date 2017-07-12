<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 15/02/2016
 * Time: 08:26
 */

namespace Vehicle;


class GearboxManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Gearbox
     */
    public static function constructGearbox(\PDO $db, array $data):Gearbox{
        $id = empty($data['gearbox_id']) ? (int)$data['id'] : (int)$data['gearbox_id'];
        $name = empty($data['gearboxName']) ? (string)$data['name'] : (string)$data['gearboxName'];

        return new Gearbox($id, $name);
    }

    /**
     * @param \PDO $db
     * @param int  $gearboxId
     *
     * @return \Exception|Gearbox
     */
    public static function fetchGearbox(\PDO $db, \int $gearboxId){
        try{
            $query = 'SELECT * FROM vhcl_gearbox WHERE id = :gearboxId ;';
            $binds = array(':gearboxId' => $gearboxId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la boite de vitesse demandée.');
            }

            return self::constructGearbox($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Gearbox
     */
    public static function fetchLastCreatedGearbox(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_gearbox ORDER BY id DESC LIMIT 1 ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune boite de vitesse actuellement en base.');
            }

            return self::constructGearbox($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO    $db
     * @param Gearbox $gearbox
     *
     * @return \Exception|Gearbox
     */
    public static function insertGearbox(\PDO $db, Gearbox $gearbox){
        try{
            $query = 'INSERT INTO vhcl_gearbox (name) VALUES (:name);';
            $binds = array(':name' => $gearbox->getName());

            \executeInsert($db, $query, $binds);

            $gearbox->setId($db->lastInsertId());
            return $gearbox;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO    $db
     * @param Gearbox $gearbox
     *
     * @return bool|\Exception
     */
    public static function hydrateGearbox(\PDO $db, Gearbox $gearbox){
        try{
            $query = 'UPDATE vhcl_gearbox SET name = :name WHERE id = :gearboxId ;';
            $binds = array(':gearboxId' => $gearbox->getId(), ':name' => $gearbox->getName());

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteGearbox(\PDO $db, \int $gearboxId){
        //TODO : Voir comment on gère la suppression d'une boite de vitesse
    }

    /**
     * @param \PDO   $db
     * @param string $orderBy
     *
     * @return \Exception|Gearbox[]
     */
    public static function fetchGearboxList(\PDO $db, \string $orderBy = 'id'){
        try{
            $query = 'SELECT * FROM vhcl_gearbox ORDER BY '.$orderBy.' ASC ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune boite de vitesse actuellement en base.');
            }

            $gearboxArray = array();
            foreach($results as $result){
                $gearboxArray[] =  self::constructGearbox($db, $result);
            }
            return $gearboxArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $gearboxId
     *
     * @return bool|\Exception
     */
    public static function doesGearboxExist(\PDO $db, \int $gearboxId){
        try{
            $query = 'SELECT COUNT(*) FROM vhcl_gearbox WHERE id = :gearboxId;';
            $binds = array(':gearboxId' => $gearboxId);

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