<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 14:40
 */

namespace Vehicle;


class ModelManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Model
     */
    private static function constructModel(\PDO $db, array $data):Model{
        $id = (int)$data['id'];
        $name = (string)$data['modelName'];
        $brandId = (int)$data['brand_id'];
        $brand = new Brand();

        if(!empty($data['brandName'])){
            $brand->setId($brandId);
            $brand->setName((string)$data['brandName']);
        }

        return new Model($id, $name, $brand, $brandId);
    }

    /**
     * @param \PDO $db
     * @param int  $modelId
     * @param bool $minimal
     *
     * @return \Exception|Model
     */
    public static function fetchModel(\PDO $db, \int $modelId = 0, \bool $minimal = true){
        try{
            //Si on demande les informations minimum sur le model (a savoir id/nom/brandId)
            if($minimal){
                $query = '  SELECT  vhcl_model.id AS id,
                                    vhcl_model.modelName AS modelName,
                                    vhcl_model.brand_id AS brand_id
                            FROM vhcl_model
                            WHERE id = :modelId
                ;';
            }
            //Si on demande toutes les informations (incluant donc toutes celles de la marque)
            else{
                $query = '  SELECT  vhcl_model.id AS id,
                                    vhcl_model.modelName AS modelName,
                                    vhcl_model.brand_id AS brand_id,
                                    vhcl_brand.brandName AS brandName
                            FROM vhcl_model
                            INNER JOIN vhcl_brand
                              ON vhcl_model.brand_id = vhcl_brand.id
                            WHERE vhcl_model.id = :modelId
                ;';
            }
            $binds = array(':modelId' => $modelId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le modèle demandé.');
            }

            $model = self::constructModel($db, $results[0]);

            //Si demande de toutes les informations, on va chercher les détails de la finition (équipements, packs, jantes, couleurs, ...)
            if(!$minimal){

            }

            //Retour du modèle
            return $model;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param bool $minimal
     *
     * @return \Exception|Model
     */
    public static function fetchLastCreatedModel(\PDO $db, \bool $minimal = true){
        try{
            //Si on demande les informations minimum sur le model (a savoir id/nom/brandId)
            if($minimal){
                $query = '  SELECT  vhcl_model.id AS id,
                                    vhcl_model.modelName AS modelName,
                                    vhcl_model.brand_id AS brand_id
                            FROM vhcl_model
                            ORDER BY ID DESC LIMIT 1
                ;';
            }
            //Si on demande toutes les informations (incluant donc toutes celles de la marque)
            else{
                $query = '  SELECT  vhcl_model.id AS id,
                                    vhcl_model.modelName AS modelName,
                                    vhcl_model.brand_id AS brand_id,
                                    vhcl_brand.brandName AS brandName
                            FROM vhcl_model
                            INNER JOIN vhcl_brand
                              ON vhcl_model.brand_id = vhcl_brand.id
                            ORDER BY ID DESC LIMIT 1
                ;';
            }
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun modèle actuellement en base.');
            }

            $model = self::constructModel($db, $results[0]);

            //Retour du modèle
            return $model;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Model $model
     *
     * @return \Exception|Model
     */
    public static function insertModel(\PDO $db, Model $model){
        try{
            $query = 'INSERT INTO vhcl_model(modelName, brand_id) VALUES(:name, :brandId);';
            $binds = array(':name' => $model->getName(), ':brandId' => $model->getBrandId());

            \executeInsert($db, $query, $binds);

            $model->setId($db->lastInsertId());

            return $model;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Model $model
     *
     * @return bool|\Exception
     */
    public static function hydrateModel(\PDO $db, Model $model){
        try{
            $query = 'UPDATE vhcl_model SET modelName = :name WHERE brand_id = :brandId ;';
            $binds = array(':name' => $model->getName(), ':brandId' => $model->getBrandId());

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteModel(\int $modelId = 0)
    {
        try{
            //TODO : Voir comment on gère le delete d'un modele, est-ce qu'on bride si une finition existe avec ce modèle ? ou bien on delete aussi les finitions corresp ?
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $modelName
     * @param int    $brandId
     *
     * @return \Exception|Model
     */
    public static function fetchModelByName(\PDO $db, \string $modelName, \int $brandId = 0){
        try{
            if(empty($brandId)){
                $query = 'SELECT * FROM vhcl_model WHERE modelName LIKE :modelName ORDER BY id ASC LIMIT 1;';
                $binds = array(':modelName' => $modelName);
            }
            else{
                $query = 'SELECT * FROM vhcl_model WHERE modelName LIKE :modelName AND brand_id = :brandId ORDER BY id ASC LIMIT 1;';
                $binds = array(':modelName' => $modelName, 'brandId' => $brandId);
            }

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun modèle correspondant aux informations entrées n\'est présent en base.');
            }

            return self::constructModel($db, $results[0]);
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
     * @return \Exception|Model[]
     */
    public static function fetchModelList(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = 'SELECT * FROM vhcl_model ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun modèle actuellement en base.');
            }

            $modelsList = array();
            foreach($results as $result){
                $modelsList[] = self::constructModel($db, $result);
            }

            return $modelsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param int    $brandId
     * @param string $modelName
     *
     * @return \Exception|Model[]
     */
    public static function fetchModelListByBrand(\PDO $db, \int $brandId, \string $modelName = '%'){
        try{
            $query = 'SELECT * FROM vhcl_model WHERE brand_id = :brandId AND modelName LIKE :modelName ORDER BY modelName ASC;';
            $binds = array(
                ':brandId'   => $brandId,
                ':modelName' => $modelName.'%'
            );

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun modèle correspondant aux critères entrés.');
            }

            $modelsList = array();
            foreach($results as $result){
                $modelsList[] = self::constructModel($db, $result);
            }

            return $modelsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $brandName
     * @param string $mode
     *
     * @return string[]|\Exception
     */
    public static function fetchModelsListWhereAVehicleIsAvailable(\PDO $db, \string $brandName, \string $mode = 'all'){
        try{
            $modelsArray = array();

            if($mode != 'stock'){
                $query = '  SELECT vhcl_model.modelName
                            FROM vhcl_model
                            INNER JOIN vhcl_finish
                              ON vhcl_model.id = vhcl_finish.model_id
                            INNER JOIN vhcl_details
                              ON vhcl_finish.id = vhcl_details.finish_id
                            INNER JOIN vhcl_brand
                              ON vhcl_model.brand_id = vhcl_brand.id
                            WHERE vhcl_finish.available = 1
                            AND vhcl_details.available = 1
                            AND UPPER(vhcl_brand.brandName) = UPPER(:brandName)
                            GROUP BY modelName
                            ORDER BY modelName
                ;';
                $binds = array(':brandName' => $brandName);

                $results = \executeSelect($db, $query, $binds);

                foreach($results as $result){
                    $modelsArray[] = $result['modelName'];
                }
            }
            if($mode != 'command'){
                $query = 'SELECT model FROM vhcl_vehicleInStock WHERE sold = 0 AND UPPER(brand) = UPPER(:brandName) GROUP BY model ORDER BY model';
                $binds = array(':brandName' => $brandName);

                $results = \executeSelect($db, $query, $binds);

                foreach($results as $result){
                    if(!in_array($result['model'], $modelsArray)){
                        $modelsArray[] = $result['model'];
                    }
                }
            }


            if(empty($modelsArray)){
                throw new \Exception('Aucun modèle disponible actuellement.');
            }

            return $modelsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}