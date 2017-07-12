<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 20/01/2016
 * Time: 09:18
 */

namespace Vehicle;


use Prices\PriceManager;

class EquipmentManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Equipment|OptionalEquipment
     */
    public static function constructEquipment(\PDO $db, array $data):Equipment{
        $id = (int)$data['id'];
        $name = (string)$data['equipmentName'];
        $typeName = (string)$data['equipmentType'];
        $typeId = (int)$data['type_id'];
        $family = (int)$data['family'];
        $exclusivity = (bool)$data['exclusivity'];

        if(!empty($data['price_id']) && $data['price_id'] != null){
            $priceId = (int)$data['price_id'];
            $price = PriceManager::fetchPrice($db, $priceId);
            return new OptionalEquipment($id, $name, $typeName, $typeId, $family, $exclusivity, $price, $priceId);
        }
        else{
            return new Equipment($id, $name, $typeName, $typeId, $family, $exclusivity);
        }
    }

    /**
     * @param \PDO $db
     * @param int  $equipmentId
     *
     * @return \Exception|Equipment|OptionalEquipment
     */
    public static function fetchEquipment(\PDO $db, \int $equipmentId){
        try{
            $query = '  SELECT vhcl_equipment.*, vhcl_equipmentType.equipmentType
                        FROM vhcl_equipment
                        INNER JOIN vhcl_equipmentType
                        ON vhcl_equipment.type_id = vhcl_equipmentType.id
                        WHERE vhcl_equipment.id = :equipmentId
            ;';
            $binds = array(':equipmentId' => $equipmentId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'équipement demandé.');
            }

            return self::constructEquipment($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Equipment|OptionalEquipment
     */
    public static function fetchLastCreatedEquipment(\PDO $db){
        try{
            $query = '  SELECT vhcl_equipment.*, vhcl_equipmentType.equipmentType
                        FROM vhcl_equipment
                        INNER JOIN vhcl_equipmentType
                        ON vhcl_equipment.type_id = vhcl_equipmentType.id
                        ORDER BY vhcl_equipment.id DESC LIMIT 1
            ;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun équipement actuellement en base.');
            }

            return self::constructEquipment($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO      $db
     * @param Equipment $equipment
     *
     * @return \Exception|Equipment
     */
    public static function insertEquipment(\PDO $db, Equipment $equipment)
    {
        try{
            $query = 'INSERT INTO vhcl_equipment(equipmentName, type_id, family, exclusivity) VALUES(:equipmentName, :typeId, :family, :exclusivity);';
            $binds = array(
                ':equipmentName' => $equipment->getName(),
                ':typeId' => $equipment->getTypeId(),
                ':family' => $equipment->getFamily(),
                ':exclusivity' => $equipment->getExclusivity(),
            );

            \executeInsert($db, $query, $binds);

            $equipment->setId($db->lastInsertId());
            return $equipment;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO      $db
     * @param Equipment $equipment
     *
     * @return bool|\Exception
     */
    public static function hydrateEquipment(\PDO $db, Equipment $equipment)
    {
        try{
            $query = 'UPDATE vhcl_equipment SET equipmentName = :equipmentName, type_id = :typeId, family = :family, exclusivity = :exclusivity WHERE id = :equipmentId ;';
            $binds = array(
                ':equipmentId' => $equipment->getId(),
                ':equipmentName' => $equipment->getName(),
                ':typeId' => $equipment->getTypeId(),
                ':family' => $equipment->getFamily(),
                ':exclusivity' => $equipment->getExclusivity(),
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteEquipment(\int $equipmentId = 0)
    {
        //TODO : Voir comment on gère le delete d'un équipement (a cause des dépendances)
    }

    /**
     * @param \PDO $db
     *
     * @return string[]|\Exception
     */
    public static function fetchFamiliesList(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_equipmentType ORDER BY equipmentType ASC;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune famille d\'equipements actuellement en base');
            }

            $familiesList = array();
            foreach($results as $result){
                $familiesList[] = ['id' => (int)$result['id'], 'name' => (string)$result['equipmentType']];
            }

            return $familiesList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $familyId
     *
     * @return \Exception|Equipment[]
     */
    public static function fetchEquipmentsByFamily(\PDO $db, \int $familyId){
        try{
            $query = '  SELECT vhcl_equipment.*, vhcl_equipmentType.equipmentType
                        FROM vhcl_equipment
                        INNER JOIN vhcl_equipmentType
                          ON vhcl_equipment.type_id = vhcl_equipmentType.id
                        WHERE type_id = :familyId
                        ORDER BY equipmentName ASC;';
            $binds = array(':familyId' => $familyId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun équipement pour cette famille en base.');
            }

            $equipmentsArray = array();
            foreach($results as $result){
                $equipmentsArray[] = self::constructEquipment($db, $result);
            }

            return $equipmentsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param int[] $equipmentsIds
     *
     * @return bool|\Exception
     */
    public static function doTheyExist(\PDO $db, array $equipmentsIds){
        try{
            $query = 'SELECT count(*) as nbEquipments FROM vhcl_equipment WHERE id IN (';
            foreach($equipmentsIds as $key => $equipmentId){
                $equipmentId = (int)$equipmentId;
                if($key > 0){
                    $query .= ', ';
                }
                $query .= $equipmentId;
            }
            $query .= ');';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if((int)$results[0]['nbEquipments'] != count($equipmentsIds)){
                return false;
            }
            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO        $db
     * @param Equipment[] $unorderedEquipments
     *
     * @return array|\Exception array de la forme : [familyName1: [equipment, equipment,...], familyName2: [equipment1, equipment2,...]]
     */
    public static function orderEquipmentsByFamily(\PDO $db, array $unorderedEquipments){
        try{
            $familiesList = self::fetchFamiliesList($db);
            $orderedEquipments = array();
            foreach($familiesList as $family){
                $familyId = $family['id'];
                $familyName = $family['name'];
                foreach($unorderedEquipments as $equipment){
                    if($equipment->getTypeId() == $familyId){
                        $orderedEquipments[$familyName][] = $equipment;
                    }
                }
            }

            return $orderedEquipments;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $familyId
     *
     * @return array|\Exception
     */
    public static function fetchFamily(\PDO $db, \int $familyId){
        try{
            $query = 'SELECT * FROM vhcl_equipmentType WHERE id = :familyId;';
            $binds = array(':familyId' => $familyId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la famille demandée.');
            }

            return array('id' => $results[0]['id'], 'name' => $results[0]['equipmentType']);
        }
        catch(\Exception $e){
            return $e;
        }
    }
}