<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 01/02/2016
 * Time: 15:14
 */

namespace Users;


class RightsManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Rights
     */
    private static function constructRights(\PDO $db, array $data):Rights{
        $id = (int)$data['id'];
        $administrateVehicleOnDemand = (bool)$data['administrateVehicleOnDemand'];
        $viewVehicleOnDemand = (bool)$data['viewVehicleOnDemand'];
        $administrateVehicleOnStock = (bool)$data['administrateVehicleOnStock'];
        $viewVehicleOnStock = (bool)$data['viewVehicleOnStock'];
        $administrateCarousel = (bool)$data['administrateCarousel'];
        $createStructure = (bool)$data['createStructure'];
        $modifyStructure = (bool)$data['modifyStructure'];
        $createUser = (bool)$data['createUser'];
        $modifyUser = (bool)$data['modifyUser'];

        return new Rights($id, $administrateVehicleOnDemand, $viewVehicleOnDemand, $administrateVehicleOnStock,
            $viewVehicleOnStock, $administrateCarousel, $createStructure, $modifyStructure, $createUser, $modifyUser);
    }

    /**
     * @param \PDO  $db
     * @param int $rightsId
     *
     * @return \Exception|Rights
     */
    public static function fetchRights(\PDO $db, \int $rightsId){
        try{
            $query = 'SELECT * FROM usr_rights WHERE id = :rightsId ;';
            $binds = array(':rightsId' => $rightsId);

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while ($data = $q->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Impossible de trouver la table de droits demandée.');
            }

            return self::constructRights($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     *
     * @return \Exception|Rights
     */
    public static function fetchLastCreatedRights(\PDO $db){
        try{
            $query = 'SELECT * FROM usr_rights ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Il n\'y a actuellement aucune table de droits dans la base de données.');
            }

            return self::constructRights($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Rights $rights
     *
     * @return \Exception|Rights
     */
    public static function insertRights(\PDO $db, Rights $rights){
        try{
            $query = 'INSERT INTO usr_rights  (
                                                id,
                                                administrateVehicleOnDemand,
                                                viewVehicleOnDemand,
                                                administrateVehicleOnStock,
                                                viewVehicleOnStock,
                                                administrateCarousel,
                                                createStructure,
                                                modifyStructure,
                                                createUser,
                                                modifyUser
                                            )
                                    VALUES  (
                                                :rightsId,
                                                :administrateVehicleOnDemand,
                                                :viewVehicleOnDemand,
                                                :adminVehicleOnStock,
                                                :viewVehicleOnStock,
                                                :administrateCarousel,
                                                :createStructure,
                                                :modifyStructure,
                                                :createUser,
                                                :modifyUser
                                    )
            ;';
            $binds = array(
                ':rightsId' => $rights->getId(),
                ':administrateVehicleOnDemand' => $rights->getAdministrateVehicleOnDemand(),
                ':viewVehicleOnDemand' => $rights->getViewVehicleOnDemand(),
                ':adminVehicleOnStock' => $rights->getAdministrateVehicleOnStock(),
                ':viewVehicleOnStock' => $rights->getViewVehicleOnStock(),
                ':administrateCarousel' => $rights->getAdministrateCarousel(),
                ':createStructure' => $rights->getCreateStructure(),
                ':modifyStructure' => $rights->getModifyStructure(),
                ':createUser' => $rights->getCreateUser(),
                ':modifyUser' => $rights->getModifyUser()
            );

            $q = $db->prepare($query);
            $q->execute($binds);

            $rights->setId((int)$db->lastInsertId());

            return $rights;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Rights $rights
     *
     * @return bool|\Exception
     */
    public static function hydrateRights(\PDO $db, Rights $rights){
        try{
            $query = 'UPDATE usr_rights  SET    administrateVehicleOnDemand = :administrateVehicleOnDemand,
                                                viewVehicleOnDemand = :viewVehicleOnDemand,
                                                administrateVehicleOnStock = :adminVehicleOnStock,
                                                viewVehicleOnStock = :viewVehicleOnStock,
                                                administrateCarousel = :administrateCarousel,
                                                createStructure = :createStructure,
                                                modifyStructure = :modifyStructure,
                                                createUser = :createUser,
                                                modifyUser = :modifyUser
                                        WHERE id = :rightsId
            ;';
            $binds = array(
                ':rightsId' => $rights->getId(),
                ':administrateVehicleOnDemand' => $rights->getAdministrateVehicleOnDemand(),
                ':viewVehicleOnDemand' => $rights->getViewVehicleOnDemand(),
                ':adminVehicleOnStock' => $rights->getAdministrateVehicleOnStock(),
                ':viewVehicleOnStock' => $rights->getViewVehicleOnStock(),
                ':administrateCarousel' => $rights->getAdministrateCarousel(),
                ':createStructure' => $rights->getCreateStructure(),
                ':modifyStructure' => $rights->getModifyStructure(),
                ':createUser' => $rights->getCreateUser(),
                ':modifyUser' => $rights->getModifyUser()
            );

            $q = $db->prepare($query);
            $q->execute($binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteRights(\PDO $db, \int $rightsId){
        try{
            //TODO : Voir comment on gère la suppression d'une table de droits
        }
        catch(\Exception $e){
            return $e;
        }
    }
}