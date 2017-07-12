<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 12/02/2016
 * Time: 16:37
 */

namespace Vehicle;


class BodyworkManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Bodywork
     */
    public static function constructBodywork(\PDO $db, array $data):Bodywork{
        $id = empty($data['bodywork_id']) ? (int)$data['id'] : (int)$data['bodywork_id'];
        $name = empty($data['bodyworkName']) ? (string)$data['name'] : (string)$data['bodyworkName'];

        return new Bodywork($id, $name);
    }

    /**
     * @param \PDO $db
     * @param int  $bodyworkId
     *
     * @return \Exception|Bodywork
     */
    public static function fetchBodywork(\PDO $db, \int $bodyworkId){
        try{
            $query = 'SELECT * FROM vhcl_bodywork WHERE id = :bodyworkId';
            $binds = array(':bodyworkId' => $bodyworkId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la carrosserie demandée.');
            }

            return self::constructBodywork($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Bodywork
     */
    public static function fetchLastCreatedBodywork(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_bodywork ORDER BY id DESC LIMIT 1';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune carrosserie actuellement en base.');
            }

            return self::constructBodywork($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param Bodywork $bodywork
     *
     * @return \Exception|Bodywork
     */
    public static function insertBodywork(\PDO $db, Bodywork $bodywork){
        try{
            $query = 'INSERT INTO vhcl_bodywork (name) VALUES(:name);';
            $binds = array(':name' => $bodywork->getName());

            \executeInsert($db, $query, $binds);

            $bodywork->setId($db->lastInsertId());
            return $bodywork;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param Bodywork $bodywork
     *
     * @return bool|\Exception
     */
    public static function hydrateBodywork(\PDO $db, Bodywork $bodywork){
        try{
            $query = 'UPDATE vhcl_brand SET name = :name WHERE id = :id';
            $binds = array(':name' => $bodywork->getName(), ':id' => $bodywork->getId());

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteBodywork(\PDO $db, \int $bodyworkId){
        //TODO : Voir comment on gère la suppression d'une carrosserie
    }

    /**
     * @param \PDO   $db
     * @param string $orderBy
     *
     * @return \Exception|Bodywork[]
     */
    public static function fetchBodyworkList(\PDO $db, \string $orderBy = 'id'){
        try{
            $query = 'SELECT * FROM vhcl_bodywork ORDER BY '.$orderBy.' ASC ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune carrosserie actuellement en base.');
            }

            $bodyworkArray = array();
            foreach($results as $result){
                $bodyworkArray[] =  self::constructBodywork($db, $result);
            }
            return $bodyworkArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $bodyworkId
     *
     * @return bool|\Exception
     */
    public static function doesBodyworkExist(\PDO $db, \int $bodyworkId){
        try{
            $query = 'SELECT COUNT(*) FROM vhcl_bodywork WHERE id = :bodyworkId;';
            $binds = array(':bodyworkId' => $bodyworkId);

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