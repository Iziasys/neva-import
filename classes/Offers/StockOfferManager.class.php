<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 15/03/2016
 * Time: 08:58
 */

namespace Offers;


use Users\Client;
use Users\ClientManager;
use Users\Structure;
use Users\StructureManager;
use Users\User;
use Users\UserManager;
use Vehicle\VehicleInStock;
use Vehicle\VehicleInStockManager;

class StockOfferManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return StockOffer
     */
    public static function constructOffer(\PDO $db, array $data):StockOffer{
        $id = !empty($data['offer_id']) ? (int)$data['offer_id'] : (int)$data['id'];
        $number = (string)$data['number'];
        $vehicleId = (int)$data['vehicle_id'];
        $vehiclePriceAmount = (float)$data['vehiclePrice'];
        $dealerMargin = (float)$data['dealerMargin'];
        $freightCharges = (float)$data['freightCharges'];
        $packageProvision = (float)$data['packageProvision'];
        $structureId = (int)$data['structure_id'];
        $userId = (int)$data['user_id'];
        $clientId = (int)$data['client_id'];
        $creationDate = new \DateTime($data['creationDate']);
        $state = (int)$data['state'];

        $vehicle = new VehicleInStock();
        if(!empty($data['brand'])){
            $vehicle = VehicleInStockManager::constructVehicle($db, $data);
        }
        $structure = new Structure();
        if(!empty($data['structureName'])){
            $structure = StructureManager::fetchStructure($db, (int)$data['structure_id']);
        }
        $user = new User();
        if(!empty($data['rights_id'])){
            $user = UserManager::fetchUser($db, (int)$data['user_id']);
        }
        $client = new Client();
        if(!empty($data['insertionDate'])){
            $client = ClientManager::fetchClient($db, $clientId);
        }

        return new StockOffer($id, $number, $vehicleId, $vehicle, $vehiclePriceAmount, $dealerMargin, $freightCharges,
                              $packageProvision, $structureId, $structure, $userId, $user, $clientId, $client, $creationDate, $state);
    }

    /**
     * @param \PDO $db
     * @param int  $offerId
     * @param bool $complete
     *
     * @return \Exception|StockOffer
     */
    public static function fetchOffer(\PDO $db, \int $offerId, \bool $complete = false){
        try{
            if($complete){
                $query = '  SELECT  offers_stockOffer.id AS offer_id,
                                    offers_stockOffer.*,
                                    usr_users.*,
                                    usr_structures.*,
                                    vhcl_vehicleInStock.*,
                                    usr_clients.*
                            FROM offers_stockOffer
                            INNER JOIN vhcl_vehicleInStock
                              ON offers_stockOffer.vehicle_id = vhcl_vehicleInStock.id
                            INNER JOIN usr_structures
                              ON offers_stockOffer.structure_id = usr_structures.id
                            INNER JOIN usr_users
                              ON offers_stockOffer.user_id = usr_users.id
                            INNER JOIN usr_clients
                              ON offers_stockOffer.client_id = usr_clients.id
                            WHERE offers_stockOffer.id = :offerId
                ;';
            }
            else{
                $query = '  SELECT  offers_stockOffer.*
                            FROM offers_stockOffer
                            WHERE id = :offerId
                ;';
            }
            $binds = array(':offerId' => $offerId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'offre demandée.');
            }

            return self::constructOffer($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param bool $complete
     *
     * @return \Exception|StockOffer
     */
    public static function fetchLastCreatedOffer(\PDO $db, \bool $complete = false){
        try{
            if($complete){
                $query = '  SELECT  offers_stockOffer.id AS offer_id,
                                    offers_stockOffer.*,
                                    usr_users.*,
                                    usr_structures.*,
                                    vhcl_vehicleInStock.*,
                                    usr_clients.*
                            FROM offers_stockOffer
                            INNER JOIN vhcl_vehicleInStock
                              ON offers_stockOffer.vehicle_id = vhcl_vehicleInStock.id
                            INNER JOIN usr_structures
                              ON offers_stockOffer.structure_id = usr_structures.id
                            INNER JOIN usr_users
                              ON offers_stockOffer.user_id = usr_users.id
                            INNER JOIN usr_clients
                              ON offers_stockOffer.client_id = usr_clients.id
                            ORDER BY creationDate DESC LIMIT 1
                ;';
            }
            else{
                $query = '  SELECT  offers_stockOffer.*
                            FROM offers_stockOffer
                            ORDER BY creationDate DESC LIMIT 1
                ;';
            }
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune offre actuellement en base.');
            }

            return self::constructOffer($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO       $db
     * @param StockOffer $offer
     *
     * @return \Exception|StockOffer
     */
    public static function insertOffer(\PDO $db, StockOffer $offer){
        try{
            $query = 'INSERT INTO offers_stockOffer ( vehicle_id,
                                                      number,
                                                      vehiclePrice,
                                                      dealerMargin,
                                                      freightCharges,
                                                      packageProvision,
                                                      structure_id,
                                                      user_id,
                                                      client_id,
                                                      creationDate,
                                                      state
                                                  )
                                            VALUES (
                                                      :vehicleId,
                                                      :number,
                                                      :vehiclePrice,
                                                      :dealerMargin,
                                                      :freightCharges,
                                                      :packageProvision,
                                                      :structureId,
                                                      :userId,
                                                      :clientId,
                                                      :creationDate,
                                                      :state
                                            )
            ;';
            $binds = array(
                ':vehicleId'        => $offer->getVehicleId(),
                ':number'           => $offer->getNumber(),
                ':vehiclePrice'     => $offer->getVehiclePriceAmount(),
                ':dealerMargin'     => $offer->getDealerMargin(),
                ':freightCharges'   => $offer->getFreightCharges(),
                ':packageProvision' => $offer->getPackageProvision(),
                ':structureId'      => $offer->getStructureId(),
                ':userId'           => $offer->getUserId(),
                ':clientId'         => $offer->getClientId(),
                ':creationDate'     => $offer->getCreationDate()->format('Y-m-d H:i:s'),
                ':state'            => $offer->getState()
            );

            \executeInsert($db, $query, $binds);

            $offer->setId($db->lastInsertId());

            return $offer;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function hydrateOffer(\PDO $db, StockOffer $offer){
        try{
            //TODO : Normalement il n'y a aucune raison de modifier les données en base d'une offre, fonction donc à laisser vide
            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteOffer(\PDO $db, \int $offerId){
        try{
            //TODO : Normalement, pareil pour la suppression, aucune raison de l'effectuer, donc à coder si besoin
            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $askedState
     * @param int  $structureId
     * @param int  $userId
     * @param bool $complete
     *
     * @return \Exception|StockOffer[]
     */
    public static function fetchOffersList(\PDO $db, \int $askedState, \int $structureId, \int $userId = 0, \bool $complete = false){
        try{
            if($complete){
                $query = '  SELECT  offers_stockOffer.id AS offer_id,
                                    offers_stockOffer.*,
                                    usr_users.*,
                                    usr_structures.*,
                                    vhcl_vehicleInStock.*,
                                    usr_clients.*
                            FROM offers_stockOffer
                            INNER JOIN vhcl_vehicleInStock
                              ON offers_stockOffer.vehicle_id = vhcl_vehicleInStock.id
                            INNER JOIN usr_structures
                              ON offers_stockOffer.structure_id = usr_structures.id
                            INNER JOIN usr_users
                              ON offers_stockOffer.user_id = usr_users.id
                            INNER JOIN usr_clients
                              ON offers_stockOffer.client_id = usr_clients.id
                ';
            }
            else{
                $query = '  SELECT  offers_stockOffer.*
                            FROM offers_stockOffer
                ';
            }
            $query .= ' WHERE state = :askedState AND offers_stockOffer.structure_id = :structureId ';
            $binds = array(
                ':structureId' => $structureId,
                ':askedState'  => $askedState
            );
            if($userId != 0){
                $query .= ' AND offers_stockOffer.user_id = :userId ';
                $binds[':userId'] = $userId;
            }
            $query .= ' ORDER BY creationDate ASC;';

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune offre actuellement en base.');
            }

            $offersArray = array();
            foreach($results as $result){
                $offersArray[] = self::constructOffer($db, $result);
            }

            return $offersArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $state
     * @param int  $structureId
     * @param int  $userId
     * @param bool $complete
     *
     * @return \Exception|int
     */
    public static function countOffers(\PDO $db, \int $state, \int $structureId, \int $userId = 0, \bool $complete = false){
        try{
            if($complete){
                $query = '  SELECT COUNT(*) as nbResult
                            FROM offers_stockOffer
                            INNER JOIN vhcl_vehicleInStock
                              ON offers_stockOffer.vehicle_id = vhcl_vehicleInStock.id
                            INNER JOIN usr_structures
                              ON offers_stockOffer.structure_id = usr_structures.id
                            INNER JOIN usr_users
                              ON offers_stockOffer.user_id = usr_users.id
                            INNER JOIN usr_clients
                              ON offers_stockOffer.client_id = usr_clients.id
                ';
            }
            else{
                $query = '  SELECT COUNT(*) as nbResult
                            FROM offers_stockOffer
                ';
            }
            $query .= ' WHERE state = :state AND offers_stockOffer.structure_id = :structureId ';
            $binds = array(
                ':structureId' => $structureId,
                ':state'       => $state
            );
            if($userId != 0){
                $query .= ' AND offers_stockOffer.user_id = :userId ';
                $binds[':userId'] = $userId;
            }
            $query .= ' ORDER BY creationDate ASC;';

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                return 0;
            }

            return (int)$results[0]['nbResult'];
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $structureId
     * @param int  $userId
     * @param int  $clientId
     *
     * @return \Exception|string
     */
    public static function generateOfferNumber(\PDO $db, \int $structureId, \int $userId, \int $clientId){
        try{
            /****************RECUPERATION DU NUMERO CLIENT***************/
            //On va récupérer le numéro de client pour cette structure
            $query = 'SELECT id FROM usr_clients WHERE owner_id = :structureId ORDER BY id ASC;';
            $binds = array(':structureId' => $structureId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results))
                $clientNumber = 1;
            else
                for($i = 1; $i <= count($results); $i++)
                    if($clientId == $results[$i - 1]['id']){
                        $clientNumber = $i;
                        break;
                    }
            if(empty($clientNumber))
                throw new \Exception('Impossible de générer le numéro client...');
            $clientNumber = str_pad($clientNumber, 5, STR_PAD_LEFT, '0');
            /****************RECUPERATION DU NUMERO CLIENT***************/

            /****************RECUPERATION DU NUMERO CONSEILLER***************/
            $query = 'SELECT id FROM usr_users WHERE structure_id = :structureId ORDER BY id ASC;';
            $binds = array(':structureId' => $structureId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results))
                $userNumber = 1;
            else
                for($i = 1; $i <= count($results); $i++)
                    if($userId == $results[$i - 1]['id']){
                        $userNumber = $i;
                        break;
                    }
            if(empty($userNumber))
                throw new \Exception('Impossible de générer le numéro conseiller...');
            $userNumber = str_pad($userNumber, 2, STR_PAD_LEFT, '0');
            /****************RECUPERATION DU NUMERO CONSEILLER***************/

            /****************RECUPERATION DU NUMERO STRUCTURE***************/
            //Le numéro de la structure est simplement son Identifiant
            $structureNumber = $structureId;
            $structureNumber = str_pad($structureNumber, 5, STR_PAD_LEFT, '0');
            /****************RECUPERATION DU NUMERO STRUCTURE***************/

            /****************RECUPERATION DU NUMERO D'OFFRE***************/
            //On compte toutes les offres faites AJD par ce conseiller pour ce client
            $query = '  SELECT COUNT(*) AS nbOffers
                        FROM offers_stockOffer
                        WHERE user_id = :userId
                        AND client_id = :clientId
                        AND DATE(creationDate) = DATE(NOW())
            ;';
            $binds = array(
                ':userId'      => $userId,
                ':clientId'    => $clientId
            );

            $results = \executeSelect($db, $query, $binds);

            $offerNumber = (int)$results[0]['nbOffers'] + 1;
            $offerNumber = str_pad($offerNumber, 2, STR_PAD_LEFT, '0');
            /****************RECUPERATION DU NUMERO D'OFFRE***************/

            return $structureNumber.$userNumber.$clientNumber.$offerNumber;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $reference
     * @param bool   $complete
     *
     * @return \Exception|StockOffer
     */
    public static function fetchOfferByReference(\PDO $db, \string $reference, \bool $complete = false){
        try{
            $year = substr($reference, 0, 4);
            $month = substr($reference, 4, 2);
            $day = substr($reference, 6, 2);
            $date = new \DateTime($year.'-'.$month.'-'.$day);
            $number = substr($reference, 8);

            if($complete){
                $query = '  SELECT  offers_stockOffer.id AS offer_id,
                                    offers_stockOffer.*,
                                    usr_users.*,
                                    usr_structures.*,
                                    vhcl_vehicleInStock.*,
                                    usr_clients.*
                            FROM offers_stockOffer
                            INNER JOIN vhcl_vehicleInStock
                              ON offers_stockOffer.vehicle_id = vhcl_vehicleInStock.id
                            INNER JOIN usr_structures
                              ON offers_stockOffer.structure_id = usr_structures.id
                            INNER JOIN usr_users
                              ON offers_stockOffer.user_id = usr_users.id
                            INNER JOIN usr_clients
                              ON offers_stockOffer.client_id = usr_clients.id
                            WHERE DATE(offers_stockOffer.creationDate) = DATE(:offerDate)
                            AND offers_stockOffer.number = :offerNumber
                ;';
            }
            else{
                $query = '  SELECT  offers_stockOffer.*
                            FROM offers_stockOffer
                            WHERE DATE(creationDate) = DATE(:offerDate)
                            AND number = :offerNumber
                ;';
            }
            $binds = array(
                ':offerDate'   => $date->format('Y-m-d'),
                ':offerNumber' => $number
            );

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'offre demandée.');
            }

            return self::constructOffer($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO       $db
     * @param StockOffer $offer
     *
     * @return bool|\Exception
     */
    public static function hydrateOfferState(\PDO $db, StockOffer $offer){
        try{
            $query = 'UPDATE offers_stockOffer SET state = :state WHERE id = :offerId;';
            $binds = array(
                ':state'   => $offer->getState(),
                ':offerId' => $offer->getId()
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}