<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 04/03/2016
 * Time: 15:58
 */

namespace Vehicle;


class VehicleInStockManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return VehicleInStock
     */
    public static function constructVehicle(\PDO $db, array $data):VehicleInStock{
        $id = !empty($data['vehicle_id']) ? (int)$data['vehicle_id'] : (int)$data['id'];
        $brand = (string)$data['brand'];
        $model = (string)$data['model'];
        $finish = (string)$data['finish'];
        $engineSize = (float)$data['engineSize'];
        $engine = (string)$data['engine'];
        $dynamicalPower = (int)$data['dynamicalPower'];
        $modelDate = new \DateTime($data['modelDate']);
        $mileage = (int)$data['mileage'];
        $fuel = (string)$data['fuel'];
        $gearbox = (string)$data['gearbox'];
        $reference = (string)$data['reference'];
        $fiscalPower = (int)$data['fiscalPower'];
        $co2 = (int)$data['co2'];
        $bodywork = (string)$data['bodywork'];
        $transmission = (string)$data['transmission'];
        $usersAmount = (int)$data['usersAmount'];
        $externalColor = (string)$data['externalColor'];
        $hasAccident = (bool)$data['hasAccident'];
        $technicalInspection = (bool)$data['isTechnicalInspectionOk'];
        $maintenanceLog = (bool)$data['isMaintenanceLogOk'];
        $equipments = $data['equipments'];
        $equipments = explode(';', $equipments);
        $suppComments = (string)$data['suppComments'];
        $funding = (string)$data['funding'];
        $warranty = (string)$data['warranty'];
        $price = (float)$data['price'];
        $sellerMargin = (float)$data['sellerMargin'];
        $image1 = (string)$data['image1'];
        $image2 = (string)$data['image2'];
        $image3 = (string)$data['image3'];
        $image4 = (string)$data['image4'];
        $structureId = (int)$data['structure_id'];
        $depotSale = (bool)$data['depotSale'];
        $availabilityDate = new \DateTime($data['availabilityDate']);
        $sold = (bool)$data['sold'];
        $reserved = (bool)$data['reserved'];
        $reservedBy = (int)$data['reservedBy'];
        $insertionDate = new \DateTime($data['insertionDate']);
        $sellingStructure = (int)$data['sellingStructure'];
        $sellingDate = $data['sellingDate'] != null ? new \DateTime($data['sellingDate']) : null;
        $bonusPenalty = (float)$data['bonusPenalty'];
        $buyingPrice = $data['buyingPrice'] == 'null' ? null : (float)$data['buyingPrice'];
        $vatOnMargin = $data['vatOnMargin'] == 'null' ? null : (bool)$data['vatOnMargin'];
        $feesAmount = $data['feesAmount'] == 'null' ? null : (float)$data['feesAmount'];
        $feesDetails = $data['feesDetails'] == 'null' ? null : (string)$data['feesDetails'];

        return new VehicleInStock($id, $brand, $model, $finish, $engineSize, $engine, $dynamicalPower, $modelDate, $mileage, $fuel, $gearbox,
                                  $reference, $fiscalPower, $co2, $bodywork, $transmission, $usersAmount, $externalColor, $hasAccident,
                                  $technicalInspection, $maintenanceLog, $equipments, $suppComments, $funding, $warranty, $price, $sellerMargin,
                                  $image1, $image2, $image3, $image4, $structureId, $depotSale, $availabilityDate, $sold, $reserved, $reservedBy,
                                  $insertionDate, $sellingStructure, $sellingDate, $bonusPenalty, $buyingPrice, $vatOnMargin, $feesAmount, $feesDetails);
    }

    /**
     * @param \PDO $db
     * @param int  $vehicleId
     *
     * @return \Exception|VehicleInStock
     */
    public static function fetchVehicle(\PDO $db, \int $vehicleId){
        try{
            $query = 'SELECT * FROM vhcl_vehicleInStock WHERE id = :vehicleId;';
            $binds = array(':vehicleId' => $vehicleId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le véhicule demandé.');
            }

            return self::constructVehicle($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|VehicleInStock
     */
    public static function fetchLastCreatedVehicle(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_vehicleInStock ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun véhicule en base actuellement.');
            }

            return self::constructVehicle($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO           $db
     * @param VehicleInStock $vehicle
     *
     * @return \Exception|VehicleInStock
     */
    public static function insertVehicle(\PDO $db, VehicleInStock $vehicle){
        try{
            $query = '
            INSERT INTO vhcl_vehicleInStock (
                                              brand,
                                              model,
                                              finish,
                                              engineSize,
                                              engine,
                                              dynamicalPower,
                                              modelDate,
                                              mileage,
                                              fuel,
                                              gearbox,
                                              reference,
                                              fiscalPower,
                                              co2,
                                              bodywork,
                                              transmission,
                                              externalColor,
                                              usersAmount,
                                              hasAccident,
                                              isTechnicalInspectionOk,
                                              isMaintenanceLogOk,
                                              equipments,
                                              suppComments,
                                              funding,
                                              warranty,
                                              price,
                                              sellerMargin,
                                              image1,
                                              image2,
                                              image3,
                                              image4,
                                              structure_id,
                                              depotSale,
                                              availabilityDate,
                                              sold,
                                              reserved,
                                              reservedBy,
                                              insertionDate,
                                              sellingStructure,
                                              sellingDate,
                                              bonusPenalty,
                                              buyingPrice,
                                              vatOnMargin,
                                              feesAmount,
                                              feesDetails
                                          )
                                  VALUES  (
                                      :brand,
                                      :model,
                                      :finish,
                                      :engineSize,
                                      :engine,
                                      :dynamicalPower,
                                      :modelDate,
                                      :mileage,
                                      :fuel,
                                      :gearbox,
                                      :reference,
                                      :fiscalPower,
                                      :co2,
                                      :bodywork,
                                      :transmission,
                                      :externalColor,
                                      :usersAmount,
                                      :hasAccident,
                                      :isTechnicalInspectionOk,
                                      :isMaintenanceLogOk,
                                      :equipments,
                                      :suppComments,
                                      :funding,
                                      :warranty,
                                      :price,
                                      :sellerMargin,
                                      :image1,
                                      :image2,
                                      :image3,
                                      :image4,
                                      :structureId,
                                      :depotSale,
                                      :availabilityDate,
                                      :sold,
                                      :reserved,
                                      :reservedBy,
                                      :insertionDate,
                                      :sellingStructure,
                                      :sellingDate,
                                      :bonusPenalty,
                                      :buyingPrice,
                                      :vatOnMargin,
                                      :feesAmount,
                                      :feesDetails
                                  )
            ;';
            $binds = array(
                ':brand'                   => $vehicle->getBrand(),
                ':model'                   => $vehicle->getModel(),
                ':finish'                  => $vehicle->getFinish(),
                ':engineSize'              => $vehicle->getEngineSize(),
                ':engine'                  => $vehicle->getEngine(),
                ':dynamicalPower'          => $vehicle->getDynamicalPower(),
                ':modelDate'               => $vehicle->getModelDate()->format('Y-m-d'),
                ':mileage'                 => $vehicle->getMileage(),
                ':fuel'                    => $vehicle->getFuel(),
                ':gearbox'                 => $vehicle->getGearbox(),
                ':reference'               => $vehicle->getReference(),
                ':fiscalPower'             => $vehicle->getFiscalPower(),
                ':co2'                     => $vehicle->getCo2(),
                ':bodywork'                => $vehicle->getBodywork(),
                ':transmission'            => $vehicle->getTransmission(),
                ':externalColor'           => $vehicle->getExternalColor(),
                ':usersAmount'             => $vehicle->getUsersAmount(),
                ':hasAccident'             => $vehicle->getHasAccident(),
                ':isTechnicalInspectionOk' => $vehicle->getIsTechnicalInspectionOk(),
                ':isMaintenanceLogOk'      => $vehicle->getIsMaintenanceLogOk(),
                ':equipments'              => implode(';', $vehicle->getEquipments()),
                ':suppComments'            => $vehicle->getSuppComments(),
                ':funding'                 => $vehicle->getFunding(),
                ':warranty'                => $vehicle->getWarranty(),
                ':price'                   => $vehicle->getPrice(),
                ':sellerMargin'            => $vehicle->getSellerMargin(),
                ':image1'                  => $vehicle->getImage1(),
                ':image2'                  => $vehicle->getImage2(),
                ':image3'                  => $vehicle->getImage3(),
                ':image4'                  => $vehicle->getImage4(),
                ':structureId'             => $vehicle->getStructureId(),
                ':depotSale'               => $vehicle->getDepotSale(),
                ':availabilityDate'        => $vehicle->getAvailabilityDate()->format('Y-m-d H:i:s'),
                ':sold'                    => $vehicle->getSold(),
                ':reserved'                => $vehicle->getReserved(),
                ':reservedBy'              => ($vehicle->getReservedBy() > 0) ? $vehicle->getReservedBy() : null,
                ':insertionDate'           => $vehicle->getInsertionDate()->format('Y-m-d H:i:s'),
                ':sellingStructure'        => $vehicle->getSellingStructure(),
                ':sellingDate'             => $vehicle->getSellingDate()->format('Y-m-d H:i:s'),
                ':bonusPenalty'            => $vehicle->getBonusPenalty(),
                ':buyingPrice'             => $vehicle->getBuyingPrice(),
                ':vatOnMargin'             => $vehicle->getVatOnMargin(),
                ':feesAmount'              => $vehicle->getFeesAmount(),
                ':feesDetails'             => $vehicle->getFeesDetails()
            );

            \executeInsert($db, $query, $binds);

            $vehicle->setId($db->lastInsertId());

            return $vehicle;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO           $db
     * @param VehicleInStock $vehicle
     *
     * @return bool|\Exception
     */
    public static function hydrateVehicle(\PDO $db, VehicleInStock $vehicle){
        try{
            $query = '
              UPDATE vhcl_vehicleInStock SET  brand = :brand,
                                              model = :model,
                                              finish = :finish,
                                              engineSize = :engineSize,
                                              engine = :engine,
                                              dynamicalPower = :dynamicalPower,
                                              modelDate = :modelDate,
                                              mileage = :mileage,
                                              fuel = :fuel,
                                              gearbox = :gearbox,
                                              reference = :reference,
                                              fiscalPower = :fiscalPower,
                                              co2 = :co2,
                                              bodywork = :bodywork,
                                              transmission = :transmission,
                                              externalColor = :externalColor,
                                              usersAmount = :usersAmount,
                                              hasAccident = :hasAccident,
                                              isTechnicalInspectionOk = :isTechnicalInspectionOk,
                                              isMaintenanceLogOk = :isMaintenanceLogOk,
                                              equipments = :equipments,
                                              suppComments = :suppComments,
                                              funding = :funding,
                                              warranty = :warranty,
                                              price = :price,
                                              sellerMargin = :sellerMargin,
                                              image1 = :image1,
                                              image2 = :image2,
                                              image3 = :image3,
                                              image4 = :image4,
                                              structure_id = :structureId,
                                              depotSale = :depotSale,
                                              availabilityDate = :availabilityDate,
                                              sold = :sold,
                                              reserved = :reserved,
                                              reservedBy = :reservedBy,
                                              insertionDate = :insertionDate,
                                              sellingStructure = :sellingStructure,
                                              sellingDate = :sellingDate,
                                              bonusPenalty = :bonusPenalty,
                                              buyingPrice = :buyingPrice,
                                              vatOnMargin = :vatOnMargin,
                                              feesAmount = :feesAmount,
                                              feesDetails = :feesDetails
                                       WHERE id = :vehicleId
            ;';
            $binds = array(
                ':vehicleId'               => $vehicle->getId(),
                ':brand'                   => $vehicle->getBrand(),
                ':model'                   => $vehicle->getModel(),
                ':finish'                  => $vehicle->getFinish(),
                ':engineSize'              => $vehicle->getEngineSize(),
                ':engine'                  => $vehicle->getEngine(),
                ':dynamicalPower'          => $vehicle->getDynamicalPower(),
                ':modelDate'               => $vehicle->getModelDate()->format('Y-m-d'),
                ':mileage'                 => $vehicle->getMileage(),
                ':fuel'                    => $vehicle->getFuel(),
                ':gearbox'                 => $vehicle->getGearbox(),
                ':reference'               => $vehicle->getReference(),
                ':fiscalPower'             => $vehicle->getFiscalPower(),
                ':co2'                     => $vehicle->getCo2(),
                ':bodywork'                => $vehicle->getBodywork(),
                ':transmission'            => $vehicle->getTransmission(),
                ':externalColor'           => $vehicle->getExternalColor(),
                ':usersAmount'             => $vehicle->getUsersAmount(),
                ':hasAccident'             => $vehicle->getHasAccident(),
                ':isTechnicalInspectionOk' => $vehicle->getIsTechnicalInspectionOk(),
                ':isMaintenanceLogOk'      => $vehicle->getIsMaintenanceLogOk(),
                ':equipments'              => implode(';', $vehicle->getEquipments()),
                ':suppComments'            => $vehicle->getSuppComments(),
                ':funding'                 => $vehicle->getFunding(),
                ':warranty'                => $vehicle->getWarranty(),
                ':price'                   => $vehicle->getPrice(),
                ':sellerMargin'            => $vehicle->getSellerMargin(),
                ':image1'                  => $vehicle->getImage1(),
                ':image2'                  => $vehicle->getImage2(),
                ':image3'                  => $vehicle->getImage3(),
                ':image4'                  => $vehicle->getImage4(),
                ':structureId'             => $vehicle->getStructureId(),
                ':depotSale'               => $vehicle->getDepotSale(),
                ':availabilityDate'        => $vehicle->getAvailabilityDate()->format('Y-m-d H:i:s'),
                ':sold'                    => $vehicle->getSold(),
                ':reserved'                => $vehicle->getReserved(),
                ':reservedBy'              => ($vehicle->getReservedBy() > 0) ? $vehicle->getReservedBy() : null,
                ':insertionDate'           => $vehicle->getInsertionDate()->format('Y-m-d H:i:s'),
                ':sellingStructure'        => $vehicle->getSellingStructure(),
                ':sellingDate'             => $vehicle->getSellingDate()->format('Y-m-d H:i:s'),
                ':bonusPenalty'            => $vehicle->getBonusPenalty(),
                ':buyingPrice'             => $vehicle->getBuyingPrice(),
                ':vatOnMargin'             => $vehicle->getVatOnMargin(),
                ':feesAmount'              => $vehicle->getFeesAmount(),
                ':feesDetails'             => $vehicle->getFeesDetails()
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
     * @param int  $vehicleId
     *
     * @return bool|\Exception
     */
    public static function deleteVehicle(\PDO $db, \int $vehicleId){
        try{
            $query = 'DELETE FROM vhcl_vehicleInStock WHERE id = :vehicleId;';
            $binds = array(':vehicleId' => $vehicleId);

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param array|string|null $constraints
     * @param array|null $arrayOrder
     * @param int    $limit
     * @param int    $offset
     * @param string $specialConstraint
     *
     * @return \Exception|VehicleInStock[]
     */
    public static function fetchVehiclesList(\PDO $db, $constraints = null, array $arrayOrder = null, \int $limit = 50, \int $offset = 0, \string $specialConstraint = ''){
        try{
            $query = 'SELECT * FROM vhcl_vehicleInStock WHERE 1 ';
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
            if($specialConstraint != ''){
                $query .= $specialConstraint;
            }

            $query .=' ORDER BY ';

            if($arrayOrder == null){
                $query .= ' vhcl_vehicleInStock.id ASC ';
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

            $vehiclesList = array();
            foreach($results as $result){
                $vehiclesList[] = self::constructVehicle($db, $result);
            }

            return $vehiclesList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param VehicleInStock[] $vehiclesList
     *
     * @return array|\Exception
     */
    public static function orderVehiclesByState(array $vehiclesList):array{
        try{
            $arrivingVehicles = array();
            $vehiclesInStock = array();
            $reservedVehicles = array();
            $soldVehicles = array();

            foreach($vehiclesList as $vehicleInStock){
                if($vehicleInStock->getSold()){
                    array_push($soldVehicles, $vehicleInStock);
                }
                else if($vehicleInStock->getReserved()){
                    array_push($reservedVehicles, $vehicleInStock);
                }
                else if($vehicleInStock->getIsArriving()){
                    array_push($arrivingVehicles, $vehicleInStock);
                }
                else{
                    array_push($vehiclesInStock, $vehicleInStock);
                }
            }

            return array(
                'arriving' => $arrivingVehicles,
                'stock'    => $vehiclesInStock,
                'reserved' => $reservedVehicles,
                'sold'     => $soldVehicles
            );
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $brand
     * @param string $model
     * @param string $finish
     *
     * @return \Exception|VehicleInStock[]
     */
    public static function searchVehiclesList(\PDO $db, \string $brand, \string $model = '', \string $finish = ''){
        try{
            $query = 'SELECT * FROM vhcl_vehicleInStock WHERE sold = 0 AND UPPER(brand) LIKE UPPER(:brand) AND usersAmount = 0 ';
            $binds = array(':brand' => $brand);
            if($model != ''){
                $query .= ' AND UPPER(model) LIKE UPPER(:model) ';
                $binds[':model'] = $model;
            }
            if($finish != ''){
                $query .= ' AND UPPER(finish) LIKE UPPER(:finish) ';
                $binds[':finish'] = $finish;
            }

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun véhicule correspondant aux critères en base.');
            }

            $vehiclesList = array();
            foreach($results as $result){
                $vehiclesList[] = self::constructVehicle($db, $result);
            }
            return $vehiclesList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param int   $vehicleId
     * @param int   $structureId
     * @param float $margin
     *
     * @return bool|\Exception
     */
    public static function defineStructureMargin(\PDO $db, \int $vehicleId, \int $structureId, \float $margin){
        try{
            $query = 'INSERT INTO prices_marginOnStock  (vehicle_id, structure_id, margin)
                                                  VALUES(:vehicleId, :structureId, :margin)
                      ON DUPLICATE KEY UPDATE margin = :margin
            ;';
            $binds = array(
                ':vehicleId'   => $vehicleId,
                ':structureId' => $structureId,
                ':margin'      => $margin
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
     * @param int  $vehicleId
     * @param int  $structureId
     *
     * @return \Exception|float
     */
    public static function fetchStructureMargin(\PDO $db, \int $vehicleId, \int $structureId){
        try{
            $query = 'SELECT margin FROM prices_marginOnStock WHERE vehicle_id = :vehicleId AND structure_id = :structureId;';
            $binds = array(
                ':vehicleId'   => $vehicleId,
                ':structureId' => $structureId
            );

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la marge demandée.');
            }

            return (float)$results[0]['margin'];
        }
        catch(\Exception $e){
            return $e;
        }
    }
}