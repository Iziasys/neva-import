<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 15/02/2016
 * Time: 10:29
 */

namespace Vehicle;


use Prices\CurrencyManager;
use Prices\PriceManager;

class DetailsManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Details
     */
    private static function constructDetails(\PDO $db, array $data):Details{
        $id = (int)$data['id'];
        $dynamicalPower = (int)$data['dynamicalPower'];
        $fiscalPower = (int)$data['fiscalPower'];
        $engineSize = (float)$data['engineSize'];
        $co2 = (int)$data['co2'];
        $sitsAmount = (int)$data['sitsAmount'];
        $doorsAmount = (int)$data['doorsAmount'];
        $available = (bool)$data['available'];

        $finishId = (int)$data['finish_id'];
        $priceId = (int)$data['price_id'];

        $engine = EngineManager::constructEngine($db, $data);
        $transmission = TransmissionManager::constructTransmission($db, $data);
        $bodywork = BodyworkManager::constructBodywork($db, $data);
        $gearbox = GearboxManager::constructGearbox($db, $data);
        $fuel = FuelManager::constructFuel($db, $data);

        return new Details($id, $dynamicalPower, $fiscalPower, $engineSize, $co2, $sitsAmount, $doorsAmount, $engine,
                           $transmission, $bodywork, null, $finishId, $gearbox, $fuel, null, $priceId, $available);
    }

    /**
     * @param \PDO $db
     * @param int  $detailsId
     *
     * @return \Exception|Details
     */
    public static function fetchDetails(\PDO $db, \int $detailsId){
        try{
            $query = '  SELECT  vhcl_details.*,
                                vhcl_engine.name as engineName,
                                vhcl_transmission.name as transmissionName,
                                vhcl_bodywork.name as bodyworkName,
                                vhcl_gearbox.name as gearboxName,
                                vhcl_fuel.name as fuelName
                                FROM vhcl_details
                                INNER JOIN vhcl_fuel
                                  ON vhcl_details.fuel_id = vhcl_fuel.id
                                INNER JOIN vhcl_gearbox
                                  ON vhcl_details.gearbox_id = vhcl_gearbox.id
                                INNER JOIN vhcl_bodywork
                                  ON vhcl_details.bodywork_id = vhcl_bodywork.id
                                INNER JOIN vhcl_transmission
                                  ON vhcl_details.transmission_id = vhcl_transmission.id
                                INNER JOIN vhcl_engine
                                  ON vhcl_details.engine_id = vhcl_engine.id
                                WHERE vhcl_details.id = :detailsId
            ;';
            $binds = array(':detailsId' => $detailsId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le véhicule demandé.');
            }

            return self::constructDetails($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Details
     */
    public static function fetchLastCreatedDetails(\PDO $db){
        try{
            $query = '  SELECT  vhcl_details.*,
                                vhcl_engine.name as engineName,
                                vhcl_transmission.name as transmissionName,
                                vhcl_bodywork.name as bodyworkName,
                                vhcl_gearbox.name as gearboxName,
                                vhcl_fuel.name as fuelName
                                FROM vhcl_details
                                INNER JOIN vhcl_fuel
                                  ON vhcl_details.fuel_id = vhcl_fuel.id
                                INNER JOIN vhcl_gearbox
                                  ON vhcl_details.gearbox_id = vhcl_gearbox.id
                                INNER JOIN vhcl_bodywork
                                  ON vhcl_details.bodywork_id = vhcl_bodywork.id
                                INNER JOIN vhcl_transmission
                                  ON vhcl_details.transmission_id = vhcl_transmission.id
                                INNER JOIN vhcl_engine
                                  ON vhcl_details.engine_id = vhcl_engine.id
                                ORDER BY id DESC LIMIT 1
            ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun véhicule actuellement en base.');
            }

            return self::constructDetails($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO    $db
     * @param Details $details
     *
     * @return \Exception|Details
     */
    public static function insertDetails(\PDO $db, Details $details){
        try{
            $query = 'INSERT INTO vhcl_details  ( dynamicalPower,
                                                  fiscalPower,
                                                  engineSize,
                                                  co2,
                                                  sitsAmount,
                                                  doorsAmount,
                                                  engine_id,
                                                  transmission_id,
                                                  finish_id,
                                                  bodywork_id,
                                                  gearbox_id,
                                                  fuel_id,
                                                  price_id,
                                                  available
                                        )
                                        VALUES  ( :dynamicalPower,
                                                  :fiscalPower,
                                                  :engineSize,
                                                  :co2,
                                                  :sitsAmount,
                                                  :doorsAmount,
                                                  :engineId,
                                                  :transmissionId,
                                                  :finishId,
                                                  :bodyworkId,
                                                  :gearboxId,
                                                  :fuelId,
                                                  :priceId,
                                                  :available
                                        )
            ;';
            $binds = array(
                ':dynamicalPower' => $details->getDynamicalPower(),
                ':fiscalPower'    => $details->getFiscalPower(),
                ':engineSize'     => $details->getEngineSize(),
                ':co2'            => $details->getCo2(),
                ':sitsAmount'     => $details->getSitsAmount(),
                ':doorsAmount'    => $details->getDoorsAmount(),
                ':engineId'       => $details->getEngine()->getId(),
                ':transmissionId' => $details->getTransmission()->getId(),
                ':finishId'       => $details->getFinishId(),
                ':bodyworkId'     => $details->getBodywork()->getId(),
                ':gearboxId'      => $details->getGearbox()->getId(),
                ':fuelId'         => $details->getFuel()->getId(),
                ':priceId'        => $details->getPriceId(),
                ':available'      => $details->getAvailable()
            );

            \executeInsert($db, $query, $binds);

            $details->setId($db->lastInsertId());

            return $details;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO    $db
     * @param Details $details
     *
     * @return bool|\Exception
     */
    public static function hydrateDetails(\PDO $db, Details $details){
        try{
            $query = '  UPDATE vhcl_details
                        SET dynamicalPower = :dynamicalPower,
                            fiscalPower = :fiscalPower,
                            engineSize = :engineSize,
                            co2 = :co2,
                            sitsAmount = :sitsAmount,
                            doorsAmount = :doorsAmount,
                            engine_id = :engineId,
                            transmission_id = :transmissionId,
                            finish_id = :finishId,
                            bodywork_id = :bodyworkId,
                            gearbox_id = :gearboxId,
                            fuel_id = :fuelId,
                            price_id = :priceId,
                            available = :available
                        WHERE id = :detailsId
            ;';
            $binds = array(
                ':detailsId'      => $details->getId(),
                ':dynamicalPower' => $details->getDynamicalPower(),
                ':fiscalPower'    => $details->getFiscalPower(),
                ':engineSize'     => $details->getEngineSize(),
                ':co2'            => $details->getCo2(),
                ':sitsAmount'     => $details->getSitsAmount(),
                ':doorsAmount'    => $details->getDoorsAmount(),
                ':engineId'       => $details->getEngine()->getId(),
                ':transmissionId' => $details->getTransmission()->getId(),
                ':finishId'       => $details->getFinishId(),
                ':bodyworkId'     => $details->getBodywork()->getId(),
                ':gearboxId'      => $details->getGearbox()->getId(),
                ':fuelId'         => $details->getFuel()->getId(),
                ':priceId'        => $details->getPriceId(),
                ':available'      => $details->getAvailable()
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
     * @param int  $detailsId
     *
     * @return bool|\Exception
     */
    public static function deleteDetails(\PDO $db, \int $detailsId){
        try{
            $query = 'DELETE FROM vhcl_details WHERE id = :detailsId';
            $binds = array(':detailsId' => $detailsId);

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO       $db
     * @param bool       $complete
     * @param array|null $arrayOrder
     * @param int        $limit
     * @param int        $offset
     *
     * @return \Exception|Details[]
     */
    public static function fetchVehicleList(\PDO $db, \bool $complete = false, array $arrayOrder = null, \int $limit = 50, \int $offset = 0){
        try{
            if(!$complete){
                $query = '  SELECT  vhcl_details.*,
                                vhcl_engine.name as engineName,
                                vhcl_transmission.name as transmissionName,
                                vhcl_bodywork.name as bodyworkName,
                                vhcl_gearbox.name as gearboxName,
                                vhcl_fuel.name as fuelName
                                FROM vhcl_details
                                INNER JOIN vhcl_fuel
                                  ON vhcl_details.fuel_id = vhcl_fuel.id
                                INNER JOIN vhcl_gearbox
                                  ON vhcl_details.gearbox_id = vhcl_gearbox.id
                                INNER JOIN vhcl_bodywork
                                  ON vhcl_details.bodywork_id = vhcl_bodywork.id
                                INNER JOIN vhcl_transmission
                                  ON vhcl_details.transmission_id = vhcl_transmission.id
                                INNER JOIN vhcl_engine
                                  ON vhcl_details.engine_id = vhcl_engine.id
                                ORDER BY
                ';
            }
            else{
                $query = '  SELECT  vhcl_details.*,
                                vhcl_engine.name as engineName,
                                vhcl_transmission.name as transmissionName,
                                vhcl_bodywork.name as bodyworkName,
                                vhcl_gearbox.name as gearboxName,
                                vhcl_fuel.name as fuelName,
                                vhcl_finish.finishName,
                                vhcl_model.modelName,
                                vhcl_brand.brandName
                                FROM vhcl_details
                                INNER JOIN vhcl_fuel
                                  ON vhcl_details.fuel_id = vhcl_fuel.id
                                INNER JOIN vhcl_gearbox
                                  ON vhcl_details.gearbox_id = vhcl_gearbox.id
                                INNER JOIN vhcl_bodywork
                                  ON vhcl_details.bodywork_id = vhcl_bodywork.id
                                INNER JOIN vhcl_transmission
                                  ON vhcl_details.transmission_id = vhcl_transmission.id
                                INNER JOIN vhcl_engine
                                  ON vhcl_details.engine_id = vhcl_engine.id
                                INNER JOIN vhcl_finish
                                  ON vhcl_details.finish_id = vhcl_finish.id
                                INNER JOIN vhcl_model
                                  ON vhcl_finish.model_id = vhcl_model.id
                                INNER JOIN vhcl_brand
                                  ON vhcl_model.brand_id = vhcl_brand.id
                                ORDER BY
                ';
            }

            if($arrayOrder == null){
                $query .= ' vhcl_details.id ASC ';
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
                throw new \Exception('Aucun véhicule actuellement en base.');
            }

            $detailsList = array();
            foreach($results as $result){
                $details = self::constructDetails($db, $result);
                if($complete){
                    $details->setFinish(FinishManager::fetchCompleteFinish($db, $details->getFinishId()));
                    $price = PriceManager::fetchPrice($db, $details->getPriceId());
                    $price->setCurrency(CurrencyManager::fetchCurrency($db, $price->getCurrencyId()));
                    $details->setPrice($price);
                }
                $detailsList[] = $details;
            }

            return $detailsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO       $db
     * @param int        $finishId
     * @param array|null $arrayOrder
     * @param int        $limit
     * @param int        $offset
     *
     * @return \Exception|Details[]
     */
    public static function fetchVehicleListFromFinish(\PDO $db, \int $finishId, array $arrayOrder = null, \int $limit = 50, \int $offset = 0){
        try{

            $query = '  SELECT  vhcl_details.*,
                            vhcl_engine.name as engineName,
                            vhcl_transmission.name as transmissionName,
                            vhcl_bodywork.name as bodyworkName,
                            vhcl_gearbox.name as gearboxName,
                            vhcl_fuel.name as fuelName,
                            vhcl_finish.finishName,
                            vhcl_model.modelName,
                            vhcl_brand.brandName
                            FROM vhcl_details
                            INNER JOIN vhcl_fuel
                              ON vhcl_details.fuel_id = vhcl_fuel.id
                            INNER JOIN vhcl_gearbox
                              ON vhcl_details.gearbox_id = vhcl_gearbox.id
                            INNER JOIN vhcl_bodywork
                              ON vhcl_details.bodywork_id = vhcl_bodywork.id
                            INNER JOIN vhcl_transmission
                              ON vhcl_details.transmission_id = vhcl_transmission.id
                            INNER JOIN vhcl_engine
                              ON vhcl_details.engine_id = vhcl_engine.id
                            INNER JOIN vhcl_finish
                              ON vhcl_details.finish_id = vhcl_finish.id
                            INNER JOIN vhcl_model
                              ON vhcl_finish.model_id = vhcl_model.id
                            INNER JOIN vhcl_brand
                              ON vhcl_model.brand_id = vhcl_brand.id
                            WHERE vhcl_finish.id = :finishId
                            ORDER BY
            ';

            if($arrayOrder == null){
                $query .= ' vhcl_details.id ASC ';
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
            $binds = array(':finishId' => $finishId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun véhicule actuellement en base.');
            }

            $detailsList = array();
            foreach($results as $result){
                $details = self::constructDetails($db, $result);
                $details->setFinish(FinishManager::fetchCompleteFinish($db, $details->getFinishId()));
                $price = PriceManager::fetchPrice($db, $details->getPriceId());
                $price->setCurrency(CurrencyManager::fetchCurrency($db, $price->getCurrencyId()));
                $details->setPrice($price);
                $detailsList[] = $details;
            }

            return $detailsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO       $db
     * @param int        $modelId
     * @param array|null $arrayOrder
     * @param int        $limit
     * @param int        $offset
     *
     * @return \Exception|Details[]
     */
    public static function fetchVehicleListFromModel(\PDO $db, \int $modelId, array $arrayOrder = null, \int $limit = 50, \int $offset = 0){
        try{

            $query = '  SELECT  vhcl_details.*,
                            vhcl_engine.name as engineName,
                            vhcl_transmission.name as transmissionName,
                            vhcl_bodywork.name as bodyworkName,
                            vhcl_gearbox.name as gearboxName,
                            vhcl_fuel.name as fuelName,
                            vhcl_finish.finishName,
                            vhcl_model.modelName,
                            vhcl_brand.brandName
                            FROM vhcl_details
                            INNER JOIN vhcl_fuel
                              ON vhcl_details.fuel_id = vhcl_fuel.id
                            INNER JOIN vhcl_gearbox
                              ON vhcl_details.gearbox_id = vhcl_gearbox.id
                            INNER JOIN vhcl_bodywork
                              ON vhcl_details.bodywork_id = vhcl_bodywork.id
                            INNER JOIN vhcl_transmission
                              ON vhcl_details.transmission_id = vhcl_transmission.id
                            INNER JOIN vhcl_engine
                              ON vhcl_details.engine_id = vhcl_engine.id
                            INNER JOIN vhcl_finish
                              ON vhcl_details.finish_id = vhcl_finish.id
                            INNER JOIN vhcl_model
                              ON vhcl_finish.model_id = vhcl_model.id
                            INNER JOIN vhcl_brand
                              ON vhcl_model.brand_id = vhcl_brand.id
                            WHERE vhcl_model.id = :modelId
                            ORDER BY
            ';

            if($arrayOrder == null){
                $query .= ' vhcl_details.id ASC ';
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
            $binds = array(':modelId' => $modelId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun véhicule actuellement en base.');
            }

            $detailsList = array();
            foreach($results as $result){
                $details = self::constructDetails($db, $result);
                $details->setFinish(FinishManager::fetchCompleteFinish($db, $details->getFinishId()));
                $price = PriceManager::fetchPrice($db, $details->getPriceId());
                $price->setCurrency(CurrencyManager::fetchCurrency($db, $price->getCurrencyId()));
                $details->setPrice($price);
                $detailsList[] = $details;
            }

            return $detailsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO       $db
     * @param bool       $complete
     * @param array|string|null $constraints
     * @param array|null $arrayOrder
     * @param int        $limit
     * @param int        $offset
     *
     * @return \Exception|Details[]
     */
    public static function fetchActiveVehicleList(\PDO $db, \bool $complete = false, $constraints = null, array $arrayOrder = null, \int $limit = 50, \int $offset = 0){
        try{
            if(!$complete){
                $query = '  SELECT  vhcl_details.*,
                                vhcl_engine.name as engineName,
                                vhcl_transmission.name as transmissionName,
                                vhcl_bodywork.name as bodyworkName,
                                vhcl_gearbox.name as gearboxName,
                                vhcl_fuel.name as fuelName
                                FROM vhcl_details
                                INNER JOIN vhcl_fuel
                                  ON vhcl_details.fuel_id = vhcl_fuel.id
                                INNER JOIN vhcl_gearbox
                                  ON vhcl_details.gearbox_id = vhcl_gearbox.id
                                INNER JOIN vhcl_bodywork
                                  ON vhcl_details.bodywork_id = vhcl_bodywork.id
                                INNER JOIN vhcl_transmission
                                  ON vhcl_details.transmission_id = vhcl_transmission.id
                                INNER JOIN vhcl_engine
                                  ON vhcl_details.engine_id = vhcl_engine.id
                ';
            }
            else{
                $query = '  SELECT  vhcl_details.*,
                                vhcl_engine.name as engineName,
                                vhcl_transmission.name as transmissionName,
                                vhcl_bodywork.name as bodyworkName,
                                vhcl_gearbox.name as gearboxName,
                                vhcl_fuel.name as fuelName,
                                vhcl_finish.finishName,
                                vhcl_model.modelName,
                                vhcl_brand.brandName
                                FROM vhcl_details
                                INNER JOIN vhcl_fuel
                                  ON vhcl_details.fuel_id = vhcl_fuel.id
                                INNER JOIN vhcl_gearbox
                                  ON vhcl_details.gearbox_id = vhcl_gearbox.id
                                INNER JOIN vhcl_bodywork
                                  ON vhcl_details.bodywork_id = vhcl_bodywork.id
                                INNER JOIN vhcl_transmission
                                  ON vhcl_details.transmission_id = vhcl_transmission.id
                                INNER JOIN vhcl_engine
                                  ON vhcl_details.engine_id = vhcl_engine.id
                                INNER JOIN vhcl_finish
                                  ON vhcl_details.finish_id = vhcl_finish.id
                                INNER JOIN vhcl_model
                                  ON vhcl_finish.model_id = vhcl_model.id
                                INNER JOIN vhcl_brand
                                  ON vhcl_model.brand_id = vhcl_brand.id
                                WHERE vhcl_details.available = 1
                                AND vhcl_finish.available = 1
                ';
            }

            if($constraints != null){
                if(is_array($constraints)){
                    foreach($constraints as $constraint){
                        $query .= ' AND ';
                        $field = $constraint['field'];
                        $operator = $constraint['operator'];
                        $value = $constraint['value'];
                        $query .= ' '.$field.' '.$operator.' '.$value.' ';
                    }
                }
                else{
                    $query .= $constraints;
                }
            }

            $query .=' ORDER BY ';

            if($arrayOrder == null){
                $query .= ' vhcl_details.id ASC ';
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
                throw new \Exception('Aucun véhicule actuellement en base.');
            }

            $detailsList = array();
            foreach($results as $result){
                $details = self::constructDetails($db, $result);
                if($complete){
                    $details->setFinish(FinishManager::fetchCompleteFinish($db, $details->getFinishId()));
                    $price = PriceManager::fetchPrice($db, $details->getPriceId());
                    $price->setCurrency(CurrencyManager::fetchCurrency($db, $price->getCurrencyId()));
                    $details->setPrice($price);
                }
                $detailsList[] = $details;
            }

            return $detailsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param array|string|null $constraints
     *
     * @return \Exception|int
     */
    public static function countActiveVehicles(\PDO $db, $constraints = null){
        try{
            $query = '  SELECT COUNT(*) nbResults
                        FROM vhcl_details
                        INNER JOIN vhcl_fuel
                          ON vhcl_details.fuel_id = vhcl_fuel.id
                        INNER JOIN vhcl_gearbox
                          ON vhcl_details.gearbox_id = vhcl_gearbox.id
                        INNER JOIN vhcl_bodywork
                          ON vhcl_details.bodywork_id = vhcl_bodywork.id
                        INNER JOIN vhcl_transmission
                          ON vhcl_details.transmission_id = vhcl_transmission.id
                        INNER JOIN vhcl_engine
                          ON vhcl_details.engine_id = vhcl_engine.id
                        INNER JOIN vhcl_finish
                          ON vhcl_details.finish_id = vhcl_finish.id
                        INNER JOIN vhcl_model
                          ON vhcl_finish.model_id = vhcl_model.id
                        INNER JOIN vhcl_brand
                          ON vhcl_model.brand_id = vhcl_brand.id
                        WHERE vhcl_details.available = 1
                        AND vhcl_finish.available = 1
            ';
            if($constraints != null){
                if(is_array($constraints)){
                    foreach($constraints as $constraint){
                        $query .= ' AND ';
                        $field = $constraint['field'];
                        $operator = $constraint['operator'];
                        $value = $constraint['value'];
                        $query .= ' '.$field.' '.$operator.' '.$value.' ';
                    }
                }
                else{
                    $query .= $constraints;
                }
            }
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            return (int)$results[0]['nbResults'];
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $detailsId
     *
     * @return \Exception|Details
     */
    public static function fetchCompleteDetails(\PDO $db, \int $detailsId){
        try{
            $query = '  SELECT  vhcl_details.*,
                                vhcl_engine.name as engineName,
                                vhcl_transmission.name as transmissionName,
                                vhcl_bodywork.name as bodyworkName,
                                vhcl_gearbox.name as gearboxName,
                                vhcl_fuel.name as fuelName,
                                vhcl_finish.finishName,
                                vhcl_model.modelName,
                                vhcl_brand.brandName
                        FROM vhcl_details
                        INNER JOIN vhcl_fuel
                          ON vhcl_details.fuel_id = vhcl_fuel.id
                        INNER JOIN vhcl_gearbox
                          ON vhcl_details.gearbox_id = vhcl_gearbox.id
                        INNER JOIN vhcl_bodywork
                          ON vhcl_details.bodywork_id = vhcl_bodywork.id
                        INNER JOIN vhcl_transmission
                          ON vhcl_details.transmission_id = vhcl_transmission.id
                        INNER JOIN vhcl_engine
                          ON vhcl_details.engine_id = vhcl_engine.id
                        INNER JOIN vhcl_finish
                          ON vhcl_details.finish_id = vhcl_finish.id
                        INNER JOIN vhcl_model
                          ON vhcl_finish.model_id = vhcl_model.id
                        INNER JOIN vhcl_brand
                          ON vhcl_model.brand_id = vhcl_brand.id
                        WHERE vhcl_details.id = :detailsId
            ;';
            $binds = array(':detailsId' => $detailsId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le véhicule demandé.');
            }

            $details = self::constructDetails($db, $results[0]);
            $details->setFinish(FinishManager::fetchCompleteFinish($db, $details->getFinishId()));
            $price = PriceManager::fetchPrice($db, $details->getPriceId());
            $price->setCurrency(CurrencyManager::fetchCurrency($db, $price->getCurrencyId()));
            $details->setPrice($price);

            return $details;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * Retourne la liste de véhicules sur l'unicité suivante :
     * Marque/Modèle/Finition/Carrosserie/NbPortes
     *
     * @param \PDO $db
     *
     * @return \Exception|Details[]
     */
    public static function fetchUniqueVehicleTypeList(\PDO $db){
        try{
            $query = '  SELECT  vhcl_details.*,
                                vhcl_engine.name as engineName,
                                vhcl_transmission.name as transmissionName,
                                vhcl_bodywork.name as bodyworkName,
                                vhcl_gearbox.name as gearboxName,
                                vhcl_fuel.name as fuelName,
                                vhcl_finish.finishName,
                                vhcl_model.modelName,
                                vhcl_brand.brandName
                        FROM vhcl_details
                        INNER JOIN vhcl_fuel
                          ON vhcl_details.fuel_id = vhcl_fuel.id
                        INNER JOIN vhcl_gearbox
                          ON vhcl_details.gearbox_id = vhcl_gearbox.id
                        INNER JOIN vhcl_bodywork
                          ON vhcl_details.bodywork_id = vhcl_bodywork.id
                        INNER JOIN vhcl_transmission
                          ON vhcl_details.transmission_id = vhcl_transmission.id
                        INNER JOIN vhcl_engine
                          ON vhcl_details.engine_id = vhcl_engine.id
                        INNER JOIN vhcl_finish
                          ON vhcl_details.finish_id = vhcl_finish.id
                        INNER JOIN vhcl_model
                          ON vhcl_finish.model_id = vhcl_model.id
                        INNER JOIN vhcl_brand
                          ON vhcl_model.brand_id = vhcl_brand.id
                        GROUP BY vhcl_brand.id, vhcl_model.id, vhcl_finish.id, vhcl_bodywork.id, doorsAmount
            ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun véhicule actuellement en base.');
            }

            $detailsList = array();
            foreach($results as $result){
                $details = self::constructDetails($db, $result);
                $details->setFinish(FinishManager::fetchCompleteFinish($db, $details->getFinishId()));
                $price = PriceManager::fetchPrice($db, $details->getPriceId());
                $price->setCurrency(CurrencyManager::fetchCurrency($db, $price->getCurrencyId()));
                $details->setPrice($price);
                $detailsList[] = $details;
            }

            return $detailsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}