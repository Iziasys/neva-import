<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 14:58
 */

namespace Vehicle;


use Prices\Country;
use Prices\Dealer;
use Prices\Price;
use Prices\PriceManager;

class FinishManager
{
    /************************GESTION DE LA FINITION EN SOI***********************/
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Finish
     */
    public static function constructFinish(\PDO $db, array $data):Finish{
        $id = (int)$data['id'];
        $name = (string)$data['finishName'];
        $modelId = (int)$data['model_id'];
        $dealerId = (int)$data['dealer_id'];
        $active = (bool)$data['available'];
        $model = new Model();
        $dealer = new Dealer();

        if(!empty($data['modelName'])){
            $model->setId($modelId);
            $model->setName((string)$data['modelName']);
        }
        if(!empty($data['dealerName'])){
            $dealer->setId($dealerId);
            $dealer->setName((string)$data['dealerName']);
        }

        return new Finish($id, $name, $model, $modelId, $dealer, $dealerId, $active);
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     * @param bool $minimal
     *
     * @return \Exception|Finish
     */
    public static function fetchFinish(\PDO $db, \int $finishId, \bool $minimal = true){
        try{
            //Si demande des informations minimales sur la finition
            //A savoir : id, name, modelId, dealerId
            if($minimal){
                $query = 'SELECT * FROM vhcl_finish WHERE id = :finishId ;';
            }
            else{

            }

            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la finition demandée.');
            }

            $finish = self::constructFinish($db, $results[0]);

            if($minimal){

            }
            else{

            }

            //Retour du modèle
            return $finish;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     *
     * @return \Exception|Finish
     */
    public static function fetchCompleteFinish(\PDO $db, \int $finishId){
        try{
            $query = '  SELECT  vhcl_finish.id as finish_id,
                                prices_dealer.name as dealerName,
                                prices_country.name as countryName,
                                vhcl_finish.*,
                                vhcl_model.*,
                                vhcl_brand.*,
                                prices_dealer.*,
                                prices_country.*
                        FROM vhcl_finish
                        INNER JOIN vhcl_model
                          ON vhcl_finish.model_id = vhcl_model.id
                        INNER JOIN vhcl_brand
                          ON vhcl_model.brand_id = vhcl_brand.id
                        INNER JOIN prices_dealer
                          ON vhcl_finish.dealer_id = prices_dealer.id
                        INNER JOIN prices_country
                          ON prices_dealer.country_id = prices_country.id
                        WHERE vhcl_finish.id = :finishId
            ';
            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la finition demandée.');
            }

            $result = $results[0];

            $brand = new Brand((int)$result['brand_id'], (string)$result['brandName']);
            $model = new Model((int)$result['model_id'], (string)$result['modelName'], $brand, $brand->getId());
            $country = new Country((int)$result['country_id'], (string)$result['countryName'], (string)$result['abbreviation']);
            $dealer = new Dealer((int)$result['dealer_id'], (string)$result['dealerName'], $country, (int)$result['country_id']);
            $finish = new Finish((int)$result['finish_id'], $result['finishName'], $model, $model->getId(), $dealer, $dealer->getId(), (bool)$result['available']);


            return $finish;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Finish
     */
    public static function fetchLastCreatedFinish(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_finish ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune finition actuellement en base.');
            }

            $finish = self::constructFinish($db, $results[0]);

            return $finish;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Finish $finish
     *
     * @return \Exception|Finish
     */
    public static function insertFinish(\PDO $db, Finish $finish){
        try{
            $query = 'INSERT INTO vhcl_finish (finishName, model_id, dealer_id, available)
                                      VALUES  (:name, :modelId, :dealerId, :active)
            ;';
            $binds = array(
                ':name'     => $finish->getName(),
                ':modelId'  => $finish->getModelId(),
                ':dealerId' => $finish->getDealerId(),
                ':active'   => $finish->getActive()
            );

            \executeInsert($db, $query, $binds);

            $finish->setId($db->lastInsertId());
            return $finish;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Finish $finish
     *
     * @return bool|\Exception
     */
    public static function hydrateFinish(\PDO $db, Finish $finish){
        try{
            $query = 'UPDATE vhcl_finish  SET   finishName = :name,
                                                model_id = :modelId,
                                                dealer_id = :dealerId,
                                                available = :active
                                          WHERE id = :finishId
            ;';
            $binds = array(
                ':finishId' => $finish->getId(),
                ':name'     => $finish->getName(),
                ':modelId'  => $finish->getModelId(),
                ':dealerId' => $finish->getDealerId(),
                ':active'   => $finish->getActive()
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
     * @param int  $finishId
     *
     * @return bool|\Exception
     */
    public static function deleteFinish(\PDO $db, \int $finishId){
        try{
            $query = 'DELETE FROM vhcl_finish WHERE id = :finishId;';
            $binds = array(':finishId' => $finishId);

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $finishName
     * @param int    $modelId
     *
     * @return \Exception|Finish
     */
    public static function fetchFinishByName(\PDO $db, \string $finishName, \int $modelId = 0){
        try{
            if(empty($modelId)){
                $query = 'SELECT * FROM vhcl_finish WHERE finishName LIKE :finishName ORDER BY model_id ASC LIMIT 1;';
                $binds = array(':finishName' => $finishName);
            }
            else{
                $query = 'SELECT * FROM vhcl_finish WHERE finishName LIKE :finishName AND model_id = :modelId ORDER BY id ASC LIMIT 1;';
                $binds = array(':finishName' => $finishName, ':modelId' => $modelId);
            }

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune finition correspondant aux critères entrés n\'est présente en base.');
            }

            return self::constructFinish($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param bool   $complete
     * @param array  $arrayOrder Sous la forme ['orderBy' : string, 'way' : string]
     * @param int    $limit
     * @param int    $offset
     *
     * @return \Exception|Finish[]
     */
    public static function fetchFinishList(\PDO $db, \bool $complete = false, array $arrayOrder = null, \int $limit = 50, \int $offset = 0){
        try{
            if(!$complete){
                $query = 'SELECT * FROM vhcl_finish ORDER BY ';
            }
            else{
                $query = '  SELECT  vhcl_finish.id as finish_id,
                                    prices_dealer.name as dealerName,
                                    prices_country.name as countryName,
                                    vhcl_finish.*,
                                    vhcl_model.*,
                                    vhcl_brand.*,
                                    prices_dealer.*,
                                    prices_country.*
                            FROM vhcl_finish
                            INNER JOIN vhcl_model
                              ON vhcl_finish.model_id = vhcl_model.id
                            INNER JOIN vhcl_brand
                              ON vhcl_model.brand_id = vhcl_brand.id
                            INNER JOIN prices_dealer
                              ON vhcl_finish.dealer_id = prices_dealer.id
                            INNER JOIN prices_country
                              ON prices_dealer.country_id = prices_country.id
                            ORDER BY
                ';
            }
            if($arrayOrder == null){
                $query .= ' vhcl_finish.id ASC ';
            }
            else{
                foreach($arrayOrder as $key => $clause){
                    if($key > 0){
                        $query .= ', ';
                    }
                    $orderBy = $clause['orderBy'];
                    $way = $clause['way'];
                    $query .= $orderBy.' '.$way.' ';
                }
            }
            $query .= ' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune finition actuellement en base.');
            }

            $finishesList = array();
            foreach($results as $result){
                if($complete){
                    $brand = new Brand((int)$result['brand_id'], (string)$result['brandName']);
                    $model = new Model((int)$result['model_id'], (string)$result['modelName'], $brand, $brand->getId());
                    $country = new Country((int)$result['country_id'], (string)$result['countryName'], (string)$result['abbreviation']);
                    $dealer = new Dealer((int)$result['dealer_id'], (string)$result['dealerName'], $country, (int)$result['country_id']);
                    $modelId = $model->getId();
                    $dealerId = $dealer->getId();
                    $finishId = (int)$result['finish_id'];
                }
                else{
                    $model = $country = $dealer = null;
                    $modelId = (int)$result['model_id'];
                    $dealerId = (int)$result['dealer_id'];
                    $finishId = (int)$result['id'];
                }
                $finishesList[] = new Finish($finishId, $result['finishName'], $model, $modelId, $dealer, $dealerId, (bool)$result['available']);
            }

            return $finishesList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param int    $modelId
     * @param string $finishName
     *
     * @return \Exception|Finish[]
     */
    public static function fetchFinishListByModel(\PDO $db, \int $modelId, \string $finishName = '%'){
        try{
            $query = 'SELECT * FROM vhcl_finish WHERE model_id = :modelId AND finishName LIKE :finishName ORDER BY finishName ASC;';
            $binds = array(
                ':modelId'    => $modelId,
                ':finishName' => $finishName.'%'
            );

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune finition correspondant aux critères entrés.');
            }

            $finishesList = array();
            foreach($results as $result){
                $finishesList[] = self::constructFinish($db, $result);
            }

            return $finishesList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $modelName
     * @param string $finishName
     *
     * @return \Exception|Finish[]
     */
    public static function fetchFinishListByModelName(\PDO $db, \string $modelName, \string $finishName = '%'){
        try{
            $query = '  SELECT vhcl_finish.*
                        FROM vhcl_finish
                        INNER JOIN vhcl_model
                          ON vhcl_finish.model_id = vhcl_model.id
                        WHERE UPPER(vhcl_model.modelName) LIKE UPPER(:modelName)
                        AND UPPER(vhcl_finish.finishName) LIKE UPPER(:finishName)
                        ORDER BY vhcl_finish.finishName ASC
            ;';
            $binds = array(
                ':modelName'  => $modelName,
                ':finishName' => $finishName.'%'
            );

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune finition correspondant aux critères entrés.');
            }

            $finishesList = array();
            foreach($results as $result){
                $finishesList[] = self::constructFinish($db, $result);
            }

            return $finishesList;
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /************************GESTION DE LA FINITION EN SOI***********************/

    /************************GESTION DES EQUIPEMENTS***********************/
    /**
     * @param \PDO  $db
     * @param int   $finishId
     * @param array $equipmentsDetails Sous la forme : [["equipment": int, "price": int\null]]
     *
     * @return bool|\Exception
     */
    public static function addEquipments(\PDO $db, \int $finishId, array $equipmentsDetails){
        try{
            $query = 'INSERT INTO vhcl_equipment_finish (finish_id, equipment_id, price_id) VALUES ';
            $binds = array(':finishId'    => $finishId);

            foreach($equipmentsDetails as $key => $equipmentDetails){
                if($key > 0){
                    $query .= ', ';
                }
                $query .= '(:finishId, :equipment'.$key.', :price'.$key.')';
                $binds[':equipment'.$key] = $equipmentDetails['equipment'];
                $binds[':price'.$key] = $equipmentDetails['price'];
            }

            $query .= ' ON DUPLICATE KEY UPDATE finish_id = finish_id ';

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param int   $finishId
     * @param int[] $equipmentsIds
     *
     * @return bool|\Exception
     */
    public static function removeEquipments(\PDO $db, \int $finishId, array $equipmentsIds){
        try{
            $query = 'DELETE FROM vhcl_equipment_finish WHERE finish_id = :finishId AND equipment_id IN (';
            $binds = array(':finishId' => $finishId);
            foreach($equipmentsIds as $key => $equipmentId){
                if($key > 0){
                    $query .= ', ';
                }
                $query .= ':equipment'.$key;
                $binds[':equipment'.$key] = (int)$equipmentId;
            }
            $query .= ');';

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param int   $finishId
     * @param array $equipmentsDetails
     *
     * @return bool|\Exception
     */
    public static function updateEquipments(\PDO $db, \int $finishId, array $equipmentsDetails){
        try{
            $query = 'INSERT INTO vhcl_equipment_finish (finish_id, equipment_id, price_id) VALUES ';
            $binds = array(':finishId'    => $finishId);

            foreach($equipmentsDetails as $key => $equipmentDetails){
                if($key > 0){
                    $query .= ', ';
                }
                $query .= '(:finishId, :equipment'.$key.', :price'.$key.')';
                $binds[':equipment'.$key] = $equipmentDetails['equipment'];
                $binds[':price'.$key] = $equipmentDetails['price'];
            }
            $query .= ' ON DUPLICATE KEY UPDATE price_id = VALUES(price_id)';

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     *
     * @return \Exception|Equipment[]
     */
    public static function fetchSerialEquipments(\PDO $db, \int $finishId){
        try{
            $query = '  SELECT vhcl_equipment.*, vhcl_equipmentType.equipmentType
                        FROM vhcl_equipment
                        INNER JOIN vhcl_equipmentType
                          ON vhcl_equipment.type_id = vhcl_equipmentType.id
                        INNER JOIN vhcl_equipment_finish
                          ON vhcl_equipment.id = vhcl_equipment_finish.equipment_id
                        WHERE finish_id = :finishId
                        AND price_id IS NULL ;';
            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Cette finition n\'a aucun équipement de série.');
            }

            $equipmentsArray = array();
            foreach($results as $result){
                $equipmentsArray[] = EquipmentManager::constructEquipment($db, $result);
            }
            return $equipmentsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     *
     * @return \Exception|OptionalEquipment[]
     */
    public static function fetchOptionalEquipments(\PDO $db, \int $finishId){
        try{
            $query = '  SELECT vhcl_equipment.*, vhcl_equipmentType.equipmentType, vhcl_equipment_finish.price_id
                        FROM vhcl_equipment
                        INNER JOIN vhcl_equipmentType
                          ON vhcl_equipment.type_id = vhcl_equipmentType.id
                        INNER JOIN vhcl_equipment_finish
                          ON vhcl_equipment.id = vhcl_equipment_finish.equipment_id
                        WHERE finish_id = :finishId
                        AND price_id IS NOT NULL ;';
            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Cette finition n\'a aucun équipement de série.');
            }

            $equipmentsArray = array();
            foreach($results as $result){
                $equipmentsArray[] = EquipmentManager::constructEquipment($db, $result);
            }
            return $equipmentsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param int   $finishId
     * @param array $equipmentsIds
     *
     * @return bool|\Exception
     */
    public static function doesFinishHaveTheseOptions(\PDO $db, \int $finishId, array $equipmentsIds){
        try{
            $query = 'SELECT COUNT(*) AS nbResults FROM vhcl_equipment_finish WHERE finish_id = :finishId AND equipment_id IN (';
            $binds = array(':finishId' => $finishId);
            foreach($equipmentsIds as $key => $equipmentId){
                $equipmentId = (int)$equipmentId;
                if($key > 0){
                    $query .= ', ';
                }
                $query .= ':equipment'.$key;
                $binds[':equipment'.$key] = $equipmentId;
            }

            $results = \executeSelect($db, $query, $binds);

            if(count($equipmentsIds) != (int)$results[0])
                return false;

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     * @param int  $equipmentId
     *
     * @return \Exception|Price
     */
    public static function fetchOptionPrice(\PDO $db, \int $finishId, \int $equipmentId){
        try{
            $query = '  SELECT prices_price.*
                        FROM prices_price
                        INNER JOIN vhcl_equipment_finish
                        ON prices_price.id = vhcl_equipment_finish.price_id
                        WHERE vhcl_equipment_finish.finish_id = :finishId
                        AND vhcl_equipment_finish.equipment_id = :equipmentId
            ;';
            $binds = array(':finishId' => $finishId, ':equipmentId' => $equipmentId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le prix demandé.');
            }

            return PriceManager::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /************************GESTION DES EQUIPEMENTS***********************/

    /************************GESTION DES COULEURS***********************/
    /**
     * @param \PDO $db
     * @param int  $finishId
     * @param int  $colorId
     *
     * @return bool|\Exception
     */
    public static function doesFinishHaveThisColor(\PDO $db, \int $finishId, \int $colorId){
        try{
            $query = 'SELECT COUNT(*) as nbColor FROM vhcl_extColor_finish WHERE finish_id = :finishId AND externalColor_id = :colorId ;';
            $binds = array(
                ':finishId' => $finishId,
                ':colorId'  => $colorId
            );

            $results = \executeSelect($db, $query, $binds);

            if((int)$results[0]['nbColor'] > 0){
                return true;
            }
            else{
                return false;
            }
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param int      $finishId
     * @param int      $colorId
     * @param int|null $priceId
     *
     * @return bool|\Exception
     */
    public static function addExternalColor(\PDO $db, \int $finishId, \int $colorId, \int $priceId = null){
        try{
            $query = 'INSERT INTO vhcl_extColor_finish (finish_id, externalColor_id, price_id) VALUES (:finishId, :colorId, :priceId);';
            $binds = array(
                ':finishId' => $finishId,
                ':colorId'  => $colorId,
                ':priceId'  => $priceId
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
     * @param int    $finishId
     * @param string $mode
     *
     * @return array|\Exception
     */
    public static function fetchExternalColor(\PDO $db, \int $finishId, \string $mode = 'all'){
        try{
            $query = 'SELECT * FROM vhcl_extColor_finish WHERE finish_id = :finishId ';
            if($mode == 'serial'){
                $query .= ' AND price_id IS NULL ';
            }
            else if($mode == 'optional'){
                $query .= ' AND price_id IS NOT NULL ';
            }
            $query .= ' ORDER BY id ASC;';
            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Cette finition n\'a aucune couleur disponible.');
            }

            $colorsArray = array();
            foreach($results as $result){
                $color = ExternalColorManager::fetchColor($db, (int)$result['externalColor_id']);
                if($result['price_id'] == null){
                    $price = null;
                }
                else{
                    $price = PriceManager::fetchPrice($db, (int)$result['price_id']);
                }
                $colorsArray[(int)$result['id']] = array('color' => $color, 'price' => $price);
            }

            return $colorsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $assocId
     *
     * @return array|\Exception array de la forme : [assocId: int, finish: Finish, color: ExternalColor, price: Price|null]
     */
    public static function fetchAssocExternalColor(\PDO $db, \int $assocId){
        try{
            $query = 'SELECT * FROM vhcl_extColor_finish WHERE id = :assocId;';
            $binds = array(':assocId' => $assocId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'association demandée.');
            }

            $result = $results[0];

            $finish = self::fetchFinish($db, (int)$result['finish_id']);
            $color = ExternalColorManager::fetchColor($db, (int)$result['externalColor_id']);
            if($result['price_id'] == null){
                $price = null;
            }
            else{
                $price = PriceManager::fetchPrice($db, (int)$result['price_id']);
            }

            return array('assocId' => $assocId, 'finish' => $finish, 'color' => $color, 'price' => $price);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param int      $assocId
     * @param int      $colorId
     * @param int|null $priceId
     *
     * @return bool|\Exception
     */
    public static function updateExternalColor(\PDO $db, \int $assocId, \int $colorId, \int $priceId = null){
        try{
            $query = 'UPDATE vhcl_extColor_finish SET externalColor_id = :colorId, price_id = :priceId WHERE id = :assocId ;';
            $binds = array(
                ':assocId' => $assocId,
                ':colorId' => $colorId,
                ':priceId' => $priceId
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
     * @param int  $assocId
     *
     * @return bool|\Exception
     */
    public static function removeExternalColor(\PDO $db, \int $assocId){
        try{
            $query = 'DELETE FROM vhcl_extColor_finish WHERE id = :assocId;';
            $binds = array(':assocId' => $assocId);

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     *
     * @return array|\Exception     Tableau sous la forme [index: [color: ExternalColor, price: Price|null], index: [color: ExternalColor, price: Price|null]]
     */
    public static function fetchExternalColors(\PDO $db, \int $finishId){
        try{
            $query = '  SELECT vhcl_extColor_finish.id AS assocId, vhcl_extColor_finish.*, vhcl_externalColor.*, prices_price.*
                        FROM vhcl_extColor_finish
                        INNER JOIN vhcl_externalColor
                          ON vhcl_extColor_finish.externalColor_id = vhcl_externalColor.id
                        LEFT JOIN prices_price
                          ON vhcl_extColor_finish.price_id = prices_price.id
                        WHERE finish_id = :finishId
            ;';
            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Cette finition n\'a aucune couleur.');
            }

            $colorsArray = array();
            foreach($results as $result){
                //Si c'est une couleur en option payante
                if($result['price_id'] != null){
                    $price = PriceManager::constructPrice($db, $result);
                }
                //Si c'est une couleur de série ou gratuite
                else{
                    $price = new Price();
                }
                $color = ExternalColorManager::constructColor($db, $result);

                $colorsArray[] = array('assocId' => (int)$result['assocId'], 'color' => $color, 'price' => $price);
            }

            return $colorsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     * @param int  $colorId
     *
     * @return \Exception|Price
     */
    public static function fetchColorPrice(\PDO $db, \int $finishId, \int $colorId){
        try{
            $query = '  SELECT prices_price.*
                        FROM prices_price
                        INNER JOIN vhcl_extColor_finish
                        ON prices_price.id = vhcl_extColor_finish.price_id
                        WHERE vhcl_extColor_finish.finish_id = :finishId
                        AND vhcl_extColor_finish.externalColor_id = :colorId
            ;';
            $binds = array(':finishId' => $finishId, ':colorId' => $colorId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le prix demandé.');
            }

            return PriceManager::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /************************GESTION DES COULEURS***********************/

    /************************GESTION DES JANTES***********************/
    /**
     * @param \PDO $db
     * @param int  $rimId
     * @param int  $finishId
     *
     * @return bool|\Exception
     */
    public static function doesFinishHaveThoseRims(\PDO $db, \int $finishId, \int $rimId){
        try{
            $query = 'SELECT COUNT(*) AS nbResults FROM vhcl_rim_finish WHERE finish_id = :finishId AND rimModel_id = :rimId;';
            $binds = array(
                ':finishId' => $finishId,
                ':rimId'    => $rimId
            );

            $results = \executeSelect($db, $query, $binds);

            if((int)$results[0]['nbResults'] > 0){
                return true;
            }
            else{
                return false;
            }
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param int      $rimId
     * @param int      $finishId
     * @param int|null $priceId
     *
     * @return bool|\Exception
     */
    public static function addRims(\PDO $db, \int $rimId, \int $finishId, \int $priceId = null){
        try{
            $query = 'INSERT INTO vhcl_rim_finish (finish_id, rimModel_id, price_id) VALUES (:finishId, :rimId, :priceId);';
            $binds = array(
                ':finishId' => $finishId,
                ':rimId'    => $rimId,
                ':priceId'  => $priceId
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
     * @param int    $finishId
     * @param string $mode
     *
     * @return \Exception|RimModel[]
     */
    public static function fetchRims(\PDO $db, \int $finishId, \string $mode = 'all'){
        try{
            $query = 'SELECT * FROM vhcl_rim_finish WHERE finish_id = :finishId ';
            if($mode == 'serial'){
                $query .= ' AND price_id IS NULL ';
            }
            else if($mode == 'optional'){
                $query .= ' AND price_id IS NOT NULL ';
            }
            $query .= ' ORDER BY id ASC;';
            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Cette finition n\'a pas de jantes disponibles.');
            }

            $rimsArray = array();
            foreach($results as $result){
                $rim = RimModelManager::fetchRimModel($db, (int)$result['rimModel_id']);
                if($result['price_id'] == null){
                    $price = null;
                }
                else{
                    $price = PriceManager::fetchPrice($db, (int)$result['price_id']);
                }
                $rimsArray[(int)$result['id']] = array('rim' => $rim, 'price' => $price);
            }

            return $rimsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $assocId
     *
     * @return array|\Exception array de la forme : [assocId: int, finish: Finish, rim: RimModel, price: Price|null]
     */
    public static function fetchAssocRim(\PDO $db, \int $assocId){
        try{
            $query = 'SELECT * FROM vhcl_rim_finish WHERE id = :assocId;';
            $binds = array(':assocId' => $assocId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'association demandée.');
            }

            $result = $results[0];

            $finish = self::fetchFinish($db, (int)$result['finish_id']);
            $rimModel = RimModelManager::fetchRimModel($db, (int)$result['rimModel_id']);
            if($result['price_id'] == null){
                $price = null;
            }
            else{
                $price = PriceManager::fetchPrice($db, (int)$result['price_id']);
            }

            return array('assocId' => $assocId, 'finish' => $finish, 'rim' => $rimModel, 'price' => $price);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO     $db
     * @param int      $assocId
     * @param int      $rimId
     * @param int|null $priceId
     *
     * @return bool|\Exception
     */
    public static function updateRim(\PDO $db, \int $assocId, \int $rimId, \int $priceId = null){
        try{
            $query = 'UPDATE vhcl_rim_finish SET rimModel_id = :rimId, price_id = :priceId WHERE id = :assocId ;';
            $binds = array(
                ':assocId' => $assocId,
                ':rimId'   => $rimId,
                ':priceId' => $priceId
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
     * @param int  $assocId
     *
     * @return bool|\Exception
     */
    public static function removeRims(\PDO $db, \int $assocId){
        try{
            $query = 'DELETE FROM vhcl_rim_finish WHERE id = :assocId;';
            $binds = array(':assocId' => $assocId);

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     * @param int  $rimsId
     *
     * @return \Exception|Price
     */
    public static function fetchRimsPrice(\PDO $db, \int $finishId, \int $rimsId){
        try{
            $query = '  SELECT prices_price.*
                        FROM prices_price
                        INNER JOIN vhcl_rim_finish
                        ON prices_price.id = vhcl_rim_finish.price_id
                        WHERE vhcl_rim_finish.finish_id = :finishId
                        AND vhcl_rim_finish.rimModel_id = :rimsId
            ;';
            $binds = array(':finishId' => $finishId, ':rimsId' => $rimsId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le prix demandé.');
            }

            return PriceManager::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /************************GESTION DES JANTES***********************/

    /************************GESTION DES PACKS***********************/
    /**
     * @param \PDO $db
     * @param int  $finishId
     *
     * @return array|\Exception array de la forme [pack: Pack, 'price': Price]
     */
    public static function fetchPacks(\PDO $db, \int $finishId){
        try{
            $query = 'SELECT * FROM vhcl_pack_finish WHERE finish_id = :finishId;';
            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results))
                throw new \Exception('Cette finition n\'a actuellement aucun pack attribué.');

            $packsArray = array();
            foreach($results as $result){
                $pack = new Pack((int)$result['id'], (string)$result['name']);
                if($result['price_id'] == null){
                    $price = null;
                }
                else{
                    $price = PriceManager::fetchPrice($db, (int)$result['price_id']);
                }

                $packsArray[] = array(
                    'pack'  => $pack,
                    'price' => $price
                );
            }

            return $packsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $packId
     *
     * @return \Exception|int
     */
    public static function whoGotThisPack(\PDO $db, \int $packId){
        try{
            $query = 'SELECT finish_id FROM vhcl_pack_finish WHERE id = :packId;';
            $binds = array(':packId' => $packId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results))
                throw new \Exception('Ce pack n\'est associé à aucune finition.');

            return (int)$results[0]['finish_id'];
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param int   $finishId
     * @param int[] $packsIds
     *
     * @return bool|\Exception
     */
    public static function doesFinishHaveThesePacks(\PDO $db, \int $finishId, array $packsIds){
        try{
            $query = 'SELECT COUNT(*) AS nbResults FROM vhcl_pack_finish WHERE finish_id = :finishId AND id IN (';
            $binds = array(':finishId' => $finishId);
            foreach($packsIds as $key => $packId){
                $packId = (int)$packId;
                if($key > 0){
                    $query .= ', ';
                }
                $query .= ':pack'.$key;
                $binds[':pack'.$key] = $packId;
            }

            $results = \executeSelect($db, $query, $binds);

            if(count($packsIds) != (int)$results[0])
                return false;

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $finishId
     * @param int  $packId
     *
     * @return \Exception|Price
     */
    public static function fetchPackPrice(\PDO $db, \int $finishId, \int $packId){
        try{
            $query = '  SELECT prices_price.*
                        FROM prices_price
                        INNER JOIN vhcl_pack_finish
                        ON prices_price.id = vhcl_pack_finish.price_id
                        WHERE vhcl_pack_finish.finish_id = :finishId
                        AND vhcl_pack_finish.id = :packId
            ;';
            $binds = array(':finishId' => $finishId, ':packId' => $packId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le prix demandé.');
            }

            return PriceManager::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /************************GESTION DES PACKS***********************/
}