<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 17/02/2016
 * Time: 09:13
 */

namespace Vehicle;


class RimModelManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return RimModel
     */
    public static function constructRimModel(\PDO $db, array $data):RimModel{
        $id = (int)$data['id'];
        $name = (string)$data['name'];
        $rimId = (int)$data['rim_id'];
        $rimType = (string)$data['type'];
        $frontDiameter = (int)$data['frontDiameter'];
        $backDiameter = (int)$data['backDiameter'];

        return new RimModel($id, $name, $rimId, $rimType, $frontDiameter, $backDiameter);
    }

    /**
     * @param \PDO $db
     * @param int  $modelId
     *
     * @return \Exception|RimModel
     */
    public static function fetchRimModel(\PDO $db, \int $modelId){
        try{
            $query = '  SELECT  vhcl_rimModel.*,
                                vhcl_rim.type,
                                vhcl_rim.frontDiameter,
                                vhcl_rim.backDiameter
                        FROM    vhcl_rimModel
                        INNER JOIN vhcl_rim
                          ON vhcl_rimModel.rim_id = vhcl_rim.id
                        WHERE vhcl_rimModel.id = :modelId
            ;';
            $binds = array(':modelId' => $modelId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver les jantes sélectionnées.');
            }

            return self::constructRimModel($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $rimId
     *
     * @return \Exception|RimModel
     */
    public static function fetchRim(\PDO $db, \int $rimId){
        try{
            $query = 'SELECT * FROM vhcl_rim WHERE id = :rimId;';
            $binds = array(':rimId' => $rimId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver les jantes sélectionnées.');
            }

            return new RimModel(0, '', (int)$results[0]['id'], (string)$results[0]['type'], (int)$results[0]['frontDiameter'], (int)$results[0]['backDiameter']);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|RimModel
     */
    public static function fetchLastCreatedRimModel(\PDO $db){
        try{
            $query = '  SELECT  vhcl_rimModel.*,
                                vhcl_rim.type,
                                vhcl_rim.frontDiameter,
                                vhcl_rim.backDiameter
                        FROM    vhcl_rimModel
                        INNER JOIN vhcl_rim
                          ON vhcl_rimModel.rim_id = vhcl_rim.id
                        ORDER BY vhcl_rimModel.id DESC LIMIT 1
            ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune jantes actuellement en base.');
            }

            return self::constructRimModel($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param RimModel $model
     *
     * @return \Exception|RimModel
     */
    public static function insertRimModel(\PDO $db, RimModel $model){
        try{
            $query = 'INSERT INTO vhcl_rimModel (name, rim_id) VALUES (:name, :rimId);';
            $binds = array(
                ':name' => $model->getName(),
                ':rimId' => $model->getRimId()
            );

            \executeInsert($db, $query, $binds);

            $model->setId((int)$db->lastInsertId());

            return $model;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param RimModel $model
     *
     * @return \Exception|RimModel
     */
    public static function insertRim(\PDO $db, RimModel $model){
        try{
            $query = 'INSERT INTO vhcl_rim (type, frontDiameter, backDiameter) VALUES(:type, :size, :size);';
            $binds = array(
                ':type' => $model->getRimType(),
                ':size' => $model->getFrontDiameter()
            );

            \executeInsert($db, $query, $binds);

            $model->setRimId((int)$db->lastInsertId());

            return $model;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $type
     * @param int    $size
     *
     * @return \Exception|RimModel
     */
    public static function searchRim(\PDO $db, \string $type, \int $size){
        try{
            $query = 'SELECT * FROM vhcl_rim WHERE type LIKE :type AND frontDiameter = :size AND backDiameter = :size;';
            $binds = array(
                ':type' => $type,
                ':size' => $size
            );

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun résultat de jantes selon vos critères.');
            }

            return new RimModel(0, '', (int)$results[0]['id'], (string)$results[0]['type'], (int)$results[0]['frontDiameter'], (int)$results[0]['backDiameter']);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $name
     * @param int    $rimId
     *
     * @return \Exception|RimModel
     */
    public static function searchRimModel(\PDO $db, \string $name, \int $rimId){
        try{
            $query = '  SELECT  vhcl_rimModel.*,
                                vhcl_rim.type,
                                vhcl_rim.frontDiameter,
                                vhcl_rim.backDiameter
                        FROM    vhcl_rimModel
                        INNER JOIN vhcl_rim
                          ON vhcl_rimModel.rim_id = vhcl_rim.id
                        WHERE name LIKE :name AND rim_id = :rimId;';
            $binds = array(':name' => $name, ':rimId' => $rimId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun résultat de modèle de jantes selon vos critères.');
            }

            return self::constructRimModel($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function hydrateRimModel(\PDO $db, RimModel $model){
        try{
            //TODO : Voir comment on gère la modification des jantes
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param RimModel $model
     *
     * @return bool|\Exception
     */
    public static function hydrateRim(\PDO $db, RimModel $model){
        try{
            $query = 'UPDATE vhcl_rim SET type = :type, frontDiameter = :frontDiameter, backDiameter = :backDiameter WHERE id = :rimId;';
            $binds = array(
                ':rimId'         => $model->getRimId(),
                ':type'          => $model->getRimType(),
                ':frontDiameter' => $model->getFrontDiameter(),
                ':backDiameter'  => $model->getBackDiameter()
            );

            \executeInsert($db, $query, $binds);

            return true;
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
     * @return \Exception|RimModel[]
     */
    public static function fetchRimModelsList(\PDO $db, \string $orderBy = 'vhcl_rimModel.id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = '  SELECT  vhcl_rimModel.*,
                                vhcl_rim.type,
                                vhcl_rim.frontDiameter,
                                vhcl_rim.backDiameter
                        FROM    vhcl_rimModel
                        INNER JOIN vhcl_rim
                          ON vhcl_rimModel.rim_id = vhcl_rim.id
                        ORDER BY '.$orderBy.' '.$way.'
                        LIMIT '.$limit.'
                        OFFSET '.$offset.'
            ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune jantes actuellement en base.');
            }

            $rimsList = array();
            foreach($results as $result){
                $rimsList[] = self::constructRimModel($db, $result);
            }

            return $rimsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}