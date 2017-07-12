<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 15/02/2016
 * Time: 09:45
 */

namespace Vehicle;


class EngineManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Engine
     */
    public static function constructEngine(\PDO $db, array $data):Engine{
        $id = empty($data['engine_id']) ? (int)$data['id'] : (int)$data['engine_id'];
        $name = empty($data['engineName']) ? (string)$data['name'] : (string)$data['engineName'];

        return new Engine($id, $name);
    }

    /**
     * @param \PDO $db
     * @param int  $engineId
     *
     * @return \Exception|Engine
     */
    public static function fetchEngine(\PDO $db, \int $engineId){
        try{
            $query = 'SELECT * FROM vhcl_engine WHERE id = :engineId';
            $binds = array(':engineId' => $engineId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la motorisation sélectionnée.');
            }

            return self::constructEngine($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function fetchEngineByName(\PDO $db, \string $engineName){
        try{
            $query = 'SELECT * FROM vhcl_engine WHERE name LIKE :engineName';
            $binds = array(':engineName' => $engineName);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la motorisation demandée.');
            }

            return self::constructEngine($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Engine
     */
    public static function fetchLastCreatedEngine(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_engine ORDER BY id DESC LIMIT 1';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune motorisation actuellement en base.');
            }

            return self::constructEngine($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Engine $engine
     *
     * @return \Exception|Engine
     */
    public static function insertEngine(\PDO $db, Engine $engine){
        try{
            $query = 'INSERT INTO vhcl_engine (name) VALUES (:name) ON DUPLICATE KEY UPDATE name = name;';
            $binds = array(':name' => $engine->getName());

            \executeInsert($db, $query, $binds);

            $engine->setId($db->lastInsertId());
            return $engine;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Engine $engine
     *
     * @return bool|\Exception
     */
    public static function hydrateEngine(\PDO $db, Engine $engine){
        try{
            $query = 'UPDATE vhcl_engine SET name = :name WHERE id = :engineId;';
            $binds = array(':name' => $engine->getName(), ':engineId' => $engine->getId());

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteEngine(\PDO $db, \int $engineId){
        //TODO : Voir comment on gère la suppression d'une motorisation
    }


}