<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 18/02/2016
 * Time: 16:31
 */

namespace Vehicle;


class PackManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Pack
     */
    public static function constructPack(\PDO $db, array $data):Pack{
        $id = (int)$data['id'];
        $name = (string)$data['name'];
        $priceId = (int)$data['price_id'];

        return new Pack($id, $name, array(), array(), null, 0, null, 0, null, $priceId);
    }

    /**
     * @param \PDO $db
     * @param int  $packId
     *
     * @return \Exception|Pack
     */
    public static function fetchPack(\PDO $db, \int $packId){
        try{
            $query = 'SELECT * FROM vhcl_pack_finish WHERE id = :packId;';
            $binds = array(':packId' => $packId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le pack demandé.');
            }

            return self::constructPack($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Pack
     */
    public static function fetchLastCreatedPack(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_pack_finish ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun pack actuellement en base.');
            }

            return self::constructPack($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param Pack $pack
     * @param int  $finishId
     *
     * @return \Exception|Pack
     */
    public static function insertPack(\PDO $db, Pack $pack, \int $finishId){
        try{
            $query = 'INSERT INTO vhcl_pack_finish (name, finish_id, price_id) VALUES (:name, :finishId, :priceId);';
            $binds = array(
                ':name'     => $pack->getName(),
                ':finishId' => $finishId,
                ':priceId'  => $pack->getPriceId() == 0 ? null : $pack->getPriceId()
            );

            \executeInsert($db, $query, $binds);

            $pack->setId($db->lastInsertId());

            return $pack;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param Pack $pack
     *
     * @return bool|\Exception
     */
    public static function hydratePack(\PDO $db, Pack $pack){
        try{
            $query = 'UPDATE vhcl_pack_finish SET name = :name, price_id = :priceId WHERE id = :packId;';
            $binds = array(
                ':packId'   => $pack->getId(),
                ':name'     => $pack->getName(),
                ':priceId'  => $pack->getPriceId()
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
     * @param int  $packId
     *
     * @return bool|\Exception
     */
    public static function deletePack(\PDO $db, \int $packId){
        try{
            $query = 'DELETE FROM vhcl_pack_finish WHERE id = :packId;';
            $binds = array(':packId' => $packId);

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $packId
     * @param int  $colorId
     *
     * @return bool|\Exception
     */
    public static function insertColor(\PDO $db, \int $packId, \int $colorId){
        try{
            $query = 'INSERT IGNORE INTO vhcl_extColor_pack (externalColor_id, pack_id) VALUES (:colorId, :packId);';
            $binds = array(
                ':colorId' => $colorId,
                ':packId'  => $packId
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
     * @param int  $packId
     *
     * @return bool|\Exception
     */
    public static function deleteColor(\PDO $db, \int $packId){
        try{
            $query = 'DELETE FROM vhcl_extColor_pack WHERE pack_id = :packId;';
            $binds = array(
                ':packId' => $packId
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
     * @param int  $packId
     * @param int  $rimsId
     *
     * @return bool|\Exception
     */
    public static function insertRims(\PDO $db, \int $packId, \int $rimsId){
        try{
            //Le INSERT IGNORE sert à ne pas insérer de doublons (clef unique sur pack_id_rim_id)
            $query = 'INSERT IGNORE INTO vhcl_rim_pack (pack_id, rim_id) VALUES (:packId, :rimId);';
            $binds = array(
                ':packId' => $packId,
                ':rimId'  => $rimsId
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
     * @param int  $packId
     *
     * @return bool|\Exception
     */
    public static function deleteRims(\PDO $db, \int $packId){
        try{
            $query = 'DELETE FROM vhcl_rim_pack WHERE pack_id = :packId;';
            $binds = array(
                ':packId' => $packId
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param int   $packId
     * @param int[] $equipmentsIds
     *
     * @return bool|\Exception
     */
    public static function addEquipments(\PDO $db, \int $packId, array $equipmentsIds){
        try{
            $query = 'INSERT IGNORE INTO vhcl_equipment_pack (pack_id, equipment_id) VALUES ';
            $binds = array(':packId' => $packId);

            foreach($equipmentsIds as $key => $equipmentDetails){
                if($key > 0){
                    $query .= ', ';
                }
                $query .= '(:packId, :equipment'.$key.')';
                $binds[':equipment'.$key] = (int)$equipmentDetails;
            }

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param int   $packId
     * @param array $equipmentsIds
     *
     * @return bool|\Exception
     */
    public static function deleteEquipments(\PDO $db, \int $packId, array $equipmentsIds){
        try{
            $query = 'DELETE FROM vhcl_equipment_pack WHERE pack_id = :packId AND equipment_id IN (';
            $binds = array(':packId' => $packId);

            foreach($equipmentsIds as $key => $equipmentDetails){
                if($key > 0){
                    $query .= ', ';
                }
                $query .= ' :equipment'.$key.' ';
                $binds[':equipment'.$key] = (int)$equipmentDetails;
            }
            $query .= ')';

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $packId
     *
     * @return \Exception|Equipment[]
     */
    public static function fetchEquipments(\PDO $db, \int $packId){
        try{
            $query = 'SELECT equipment_id FROM vhcl_equipment_pack WHERE pack_id = :packId;';
            $binds = array(':packId' => $packId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Ce pack n\'a aucun équipement inclus.');
            }

            $equipmentsArray = array();
            foreach($results as $result){
                $equipmentsArray[] = EquipmentManager::fetchEquipment($db, (int)$result['equipment_id']);
            }

            return $equipmentsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $packId
     *
     * @return \Exception|ExternalColor
     */
    public static function fetchColor(\PDO $db, \int $packId){
        try{
            $query = 'SELECT externalColor_id FROM vhcl_extColor_pack WHERE pack_id = :packId;';
            $binds = array(':packId' => $packId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results))
                throw new \Exception('Ce pack n\'a aucune couleur.');

            return ExternalColorManager::fetchColor($db, (int)$results[0]['externalColor_id']);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $packId
     *
     * @return \Exception|RimModel
     */
    public static function fetchRim(\PDO $db, \int $packId){
        try{
            $query = 'SELECT rim_id FROM vhcl_rim_pack WHERE pack_id = :packId;';
            $binds = array(':packId' => $packId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results))
                throw new \Exception('Ce pack n\'a aucune jantes.');

            return RimModelManager::fetchRimModel($db, (int)$results[0]['rim_id']);
        }
        catch(\Exception $e){
            return $e;
        }
    }
}