<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 29/02/2016
 * Time: 12:36
 */

namespace Offers;


use Users\User;

class OfferManager
{
    /***********************GESTION DES OPTIONS***********************/
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Option
     */
    public static function constructOption(\PDO $db, array $data):Option{
        $itemId = (int)$data['equipment_id'];
        $offerId = (int)$data['offer_id'];
        $price = (float)$data['optionPrice'];

        return new Option($itemId, $offerId, $price);
    }

    /**
     * @param \PDO $db
     * @param int  $offerId
     *
     * @return \Exception|Option[]
     */
    public static function fetchOptionsInOffer(\PDO $db, \int $offerId){
        try{
            $query = 'SELECT * FROM offers_options WHERE offer_id = :offerId;';
            $binds = array(':offerId' => $offerId);

            $results = \executeSelect($db, $query, $binds);

            $optionsArray = array();
            if(!empty($results)){
                foreach($results as $result){
                    $optionsArray[] = self::constructOption($db, $result);
                }
            }

            return $optionsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Option[] $options
     *
     * @return bool|\Exception
     */
    public static function insertMultipleOptions(\PDO $db, array $options){
        try{
            $query = 'INSERT INTO offers_options (equipment_id, offer_id, optionPrice) VALUES ';
            $binds = array();
            foreach($options as $key => $option){
                if(!is_a($option, '\Offers\Option')){
                    throw new \Exception('Erreur, une des option donnée n\'est pas valide...');
                }
                else{
                    if($key > 0){
                        $query .= ', ';
                    }
                    $query .= '(:item'.$key.', :offer'.$key.', :price'.$key.')';
                    $binds['item'.$key] = $option->getItemId();
                    $binds['offer'.$key] = $option->getOfferId();
                    $binds['price'.$key] = $option->getPrice();
                }
            }
            $query .= ';';

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /***********************GESTION DES OPTIONS***********************/

    /***********************GESTION DES PACKS***********************/
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Pack
     */
    public static function constructPack(\PDO $db, array $data):Pack{
        $itemId = (int)$data['pack_id'];
        $offerId = (int)$data['offer_id'];
        $price = (float)$data['packPrice'];

        return new Pack($itemId, $offerId, $price);
    }

    /**
     * @param \PDO $db
     * @param int  $offerId
     *
     * @return \Exception|Pack[]
     */
    public static function fetchPacksInOffer(\PDO $db, \int $offerId){
        try{
            $query = 'SELECT * FROM offers_packs WHERE offer_id = :offerId;';
            $binds = array(':offerId' => $offerId);

            $results = \executeSelect($db, $query, $binds);

            $packsArray = array();
            if(!empty($results)){
                foreach($results as $result){
                    $packsArray[] = self::constructPack($db, $result);
                }
            }

            return $packsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Pack[] $packs
     *
     * @return bool|\Exception
     */
    public static function insertMultiplePacks(\PDO $db, array $packs){
        try{
            $query = 'INSERT INTO offers_packs (pack_id, offer_id, packPrice) VALUES ';
            $binds = array();
            foreach($packs as $key => $pack){
                if(!is_a($pack, '\Offers\Pack')){
                    throw new \Exception('Erreur, un des packs donné n\'est pas valide...');
                }
                else{
                    if($key > 0){
                        $query .= ', ';
                    }
                    $query .= '(:item'.$key.', :offer'.$key.', :price'.$key.')';
                    $binds['item'.$key] = $pack->getItemId();
                    $binds['offer'.$key] = $pack->getOfferId();
                    $binds['price'.$key] = $pack->getPrice();
                }
            }
            $query .= ';';

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /***********************GESTION DES PACKS***********************/

    /***********************GESTION DES COULEURS***********************/
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Color
     */
    public static function constructColor(\PDO $db, array $data):Color{
        $itemId = (int)$data['color_id'];
        $offerId = (int)$data['offer_id'];
        $price = (float)$data['colorPrice'];

        return new Color($itemId, $offerId, $price);
    }

    /**
     * @param \PDO $db
     * @param int  $offerId
     *
     * @return \Exception|null|Color
     */
    public static function fetchColorInOffer(\PDO $db, \int $offerId){
        try{
            $query = 'SELECT * FROM offers_colors WHERE offer_id = :offerId;';
            $binds = array(':offerId' => $offerId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                return null;
            }

            return self::constructColor($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Color $color
     *
     * @return bool|\Exception
     */
    public static function insertColor(\PDO $db, Color $color){
        try{
            $query = 'INSERT INTO offers_colors (color_id, offer_id, colorPrice) VALUES (:colorId, :offerId, :colorPrice)';
            $binds = array(
                ':colorId'    => $color->getItemId(),
                ':offerId'    => $color->getOfferId(),
                ':colorPrice' => $color->getPrice()
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /***********************GESTION DES COULEURS***********************/

    /***********************GESTION DES JANTES***********************/
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Rims
     */
    public static function constructRims(\PDO $db, array $data):Rims{
        $itemId = (int)$data['rim_id'];
        $offerId = (int)$data['offer_id'];
        $price = (float)$data['rimsPrice'];

        return new Rims($itemId, $offerId, $price);
    }

    /**
     * @param \PDO $db
     * @param int  $offerId
     *
     * @return \Exception|null|Rims
     */
    public static function fetchRimsInOffer(\PDO $db, \int $offerId){
        try{
            $query = 'SELECT * FROM offers_rims WHERE offer_id = :offerId;';
            $binds = array(':offerId' => $offerId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                return null;
            }

            return self::constructRims($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param Rims $rims
     *
     * @return bool|\Exception
     */
    public static function insertRims(\PDO $db, Rims $rims){
        try{
            $query = 'INSERT INTO offers_rims (rim_id, offer_id, rimsPrice) VALUES (:rimsId, :offerId, :rimsPrice)';
            $binds = array(
                ':rimsId'    => $rims->getItemId(),
                ':offerId'   => $rims->getOfferId(),
                ':rimsPrice' => $rims->getPrice()
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }
    /***********************GESTION DES JANTES***********************/

    /***********************GESTION DES OFFRES***********************/
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Offer
     */
    public static function constructOffer(\PDO $db, array $data):Offer{
        $id = (int)$data['id'];
        $number = (string)$data['number'];
        $creationDate = new \DateTime($data['creationDate']);
        $vehicleId = (int)$data['vehicle_id'];
        $vehiclePrice = (float)$data['vehiclePrice'];
        $freightChargesToFrance = (float)$data['freightChargesToFrance'];
        $marginAmount = (float)$data['marginAmount'];
        $managementFees = (float)$data['managementFees'];
        $dealerMargin = (float)$data['dealerMargin'];
        $vatRate = (float)$data['vatRate'];
        $packageProvision = (float)$data['packageProvision'];
        $freightChargesInFrance = (float)$data['freightChargesInFrance'];
        $clientId = (int)$data['client_id'];
        $ownerId = (int)$data['owner_id'];
        $state = (int)$data['state'];
        $externalColor = (string)$data['vehicleExternalColor'];
        $internalColor = (string)$data['vehicleInternalColor'];

        return new Offer($id, $number, $creationDate, $vehicleId, $vehiclePrice, $freightChargesToFrance, $marginAmount,
                         $managementFees, $dealerMargin, $vatRate, $packageProvision, $freightChargesInFrance,
                         $clientId, $ownerId, $state, array(), array(), null, null, $externalColor, $internalColor);
    }

    /**
     * @param \PDO $db
     * @param int  $offerId
     *
     * @return \Exception|Offer
     */
    public static function fetchOffer(\PDO $db, \int $offerId){
        try{
            $query = 'SELECT * FROM offers_offer WHERE id = :offerId;';
            $binds = array(':offerId' => $offerId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'offre demandée.');
            }

            $options = self::fetchOptionsInOffer($db, $offerId);
            $packs = self::fetchPacksInOffer($db, $offerId);
            $color = self::fetchColorInOffer($db, $offerId);
            $rims = self::fetchRimsInOffer($db, $offerId);

            $offer = self::constructOffer($db, $results[0]);
            $offer->setOptions($options);
            $offer->setPacks($packs);
            if(is_a($color, '\Offers\Color'))
                $offer->setColor($color);
            if(is_a($rims, '\Offers\Rims'))
                $offer->setRims($rims);

            return $offer;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $reference
     *
     * @return \Exception|Offer
     */
    public static function fetchOfferByReference(\PDO $db, \string $reference){
        try{
            $year = substr($reference, 0, 4);
            $month = substr($reference, 4, 2);
            $day = substr($reference, 6, 2);
            $date = new \DateTime($year.'-'.$month.'-'.$day);
            $number = substr($reference, 8);

            $query = 'SELECT * FROM offers_offer WHERE DATE(creationDate) = :date AND number = :number;';
            $binds = array(':date' => $date->format('Y-m-d'), ':number' => $number);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'offre demandée.');
            }

            $offer = self::constructOffer($db, $results[0]);
            $offerId = $offer->getId();

            $options = self::fetchOptionsInOffer($db, $offerId);
            $packs = self::fetchPacksInOffer($db, $offerId);
            $color = self::fetchColorInOffer($db, $offerId);
            $rims = self::fetchRimsInOffer($db, $offerId);

            $offer->setOptions($options);
            $offer->setPacks($packs);
            if(is_a($color, '\Offers\Color'))
                $offer->setColor($color);
            if(is_a($rims, '\Offers\Rims'))
                $offer->setRims($rims);

            return $offer;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Offer
     */
    public static function fetchLastCreatedOffer(\PDO $db){
        try{
            $query = 'SELECT * FROM offers_offer ORDER BY id DESC LIMIT 1';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune offre actuellement en base.');
            }

            $offer = self::constructOffer($db, $results[0]);
            $offerId = $offer->getId();

            $options = self::fetchOptionsInOffer($db, $offerId);
            $packs = self::fetchPacksInOffer($db, $offerId);
            $color = self::fetchColorInOffer($db, $offerId);
            $rims = self::fetchRimsInOffer($db, $offerId);

            $offer->setOptions($options);
            $offer->setPacks($packs);
            if(is_a($color, '\Offers\Color'))
                $offer->setColor($color);
            if(is_a($rims, '\Offers\Rims'))
                $offer->setRims($rims);

            return $offer;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Offer $offer
     *
     * @return \Exception|Offer
     */
    public static function insertOffer(\PDO $db, Offer $offer){
        try{
            $query = 'INSERT INTO offers_offer (
                                                  number,
                                                  creationDate,
                                                  vehicle_id,
                                                  vehiclePrice,
                                                  vehicleExternalColor,
                                                  vehicleInternalColor,
                                                  freightChargesToFrance,
                                                  marginAmount,
                                                  managementFees,
                                                  dealerMargin,
                                                  vatRate,
                                                  packageProvision,
                                                  freightChargesInFrance,
                                                  client_id,
                                                  owner_id,
                                                  state
                                              )
                                       VALUES (
                                                  :number,
                                                  :creationDate,
                                                  :vehicleId,
                                                  :vehiclePrice,
                                                  :externalColor,
                                                  :internalColor,
                                                  :freightChargesToFrance,
                                                  :marginAmount,
                                                  :managementFees,
                                                  :dealerMargin,
                                                  :vatRate,
                                                  :packageProvision,
                                                  :freightChargesInFrance,
                                                  :clientId,
                                                  :ownerId,
                                                  :state
                                       )
            ;';
            $binds = array(
                ':number'                 => $offer->getNumber(),
                ':creationDate'           => $offer->getCreationDate()->format('Y-m-d H:i:s'),
                ':vehicleId'              => $offer->getVehicleId(),
                ':vehiclePrice'           => $offer->getVehiclePrice(),
                ':externalColor'          => $offer->getExternalColor(),
                ':internalColor'          => $offer->getInternalColor(),
                ':freightChargesToFrance' => $offer->getFreightChargesToFrance(),
                ':marginAmount'           => $offer->getMarginAmount(),
                ':managementFees'         => $offer->getManagementFees(),
                ':dealerMargin'           => $offer->getDealerMargin(),
                ':vatRate'                => $offer->getVatRate(),
                ':packageProvision'       => $offer->getPackageProvision(),
                ':freightChargesInFrance' => $offer->getFreightChargesInFrance(),
                ':clientId'               => $offer->getClientId(),
                ':ownerId'                => $offer->getOwnerId(),
                ':state'                  => $offer->getState()
            );

            \executeInsert($db, $query, $binds);

            $offer->setId($db->lastInsertId());

            return $offer;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Offer $offer
     *
     * @return bool|\Exception
     */
    public static function hydrateOfferState(\PDO $db, Offer $offer){
        try{
            $query = 'UPDATE offers_offer SET state = :state WHERE id = :offerId;';
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

    /**
     * @param \PDO  $db
     * @param Offer $offer
     *
     * @return bool|\Exception
     */
    public static function addColorsToOffer(\PDO $db, Offer $offer){
        try{
            $query = 'UPDATE offers_offer SET vehicleExternalColor = :externalColor, vehicleInternalColor = :internalColor WHERE id = :offerId;';
            $binds = array(
                ':offerId'       => $offer->getId(),
                ':externalColor' => $offer->getExternalColor(),
                ':internalColor' => $offer->getInternalColor()
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
     * @param int  $askedState
     * @param User $owner
     * @param bool $isOwner
     * @param bool $isAdmin
     *
     * @return array|\Exception|Offer[]
     */
    public static function fetchOffersList(\PDO $db, \int $askedState, User $owner, \bool $isOwner = false, \bool $isAdmin = false){
        try{
            //Récupération des offres
            $binds = array(
                ':validity'   => $GLOBALS['__VALIDITY_DURATION'],
                ':askedState' => $askedState
            );
            //Si l'utilisateur est admin, on récupère toutes les offres disponibles
            if($isAdmin){
                $query = 'SELECT * FROM offers_offer WHERE DATE(creationDate) BETWEEN DATE_SUB(NOW(), INTERVAL :validity DAY) AND NOW() AND state = :askedState;';
            }
            //Si il est dirigeant de sa structure, toutes celles de sa structure
            else if($isOwner){
                $query = '  SELECT offers_offer.*
                            FROM offers_offer
                            INNER JOIN usr_users
                            ON offers_offer.owner_id = usr_users.id
                            WHERE creationDate
                              BETWEEN DATE_SUB(NOW(), INTERVAL :validity DAY)
                              AND NOW()
                            AND usr_users.structure_id = :structureId
                            AND state = :askedState
                ;';
                $binds['structureId'] = $owner->getStructureId();
            }
            //Si il est simplement vendeur, juste les offres qu'il a produit
            else{
                $query = '  SELECT *
                            FROM offers_offer
                            WHERE creationDate
                              BETWEEN DATE_SUB(NOW(), INTERVAL :validity DAY)
                              AND NOW()
                            AND owner_id = :ownerId
                            AND state = :askedState
                ;';
                $binds['ownerId'] = $owner->getId();
            }

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune offre trouvée pour ce conseiller.');
            }

            $offersList = array();
            foreach($results as $result){
                $offersList[] = self::constructOffer($db, $result);
            }

            return $offersList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $state
     * @param User $owner
     * @param bool $isOwner
     * @param bool $isAdmin
     *
     * @return \Exception|int
     */
    public static function countOffers(\PDO $db, \int $state, User $owner, \bool $isOwner = false, \bool $isAdmin = false){
        try{
            //Récupération des offres
            $binds = array(
                ':validity' => $GLOBALS['__VALIDITY_DURATION'],
                ':state'    => $state
            );
            //Si l'utilisateur est admin, on récupère toutes les offres disponibles
            if($isAdmin){
                $query = 'SELECT COUNT(*) AS nbResult FROM offers_offer WHERE DATE(creationDate) BETWEEN DATE_SUB(NOW(), INTERVAL :validity DAY) AND NOW() AND state = :state;';
            }
            //Si il est dirigeant de sa structure, toutes celles de sa structure
            else if($isOwner){
                $query = '  SELECT COUNT(*) AS nbResult
                            FROM offers_offer
                            INNER JOIN usr_users
                            ON offers_offer.owner_id = usr_users.id
                            WHERE creationDate
                              BETWEEN DATE_SUB(NOW(), INTERVAL :validity DAY)
                              AND NOW()
                            AND usr_users.structure_id = :structureId
                            AND state = :state
                ;';
                $binds['structureId'] = $owner->getStructureId();
            }
            //Si il est simplement vendeur, juste les offres qu'il a produit
            else{
                $query = '  SELECT COUNT(*) AS nbResult
                            FROM offers_offer
                            WHERE creationDate
                              BETWEEN DATE_SUB(NOW(), INTERVAL :validity DAY)
                              AND NOW()
                            AND owner_id = :ownerId
                            AND state = :state
                ;';
                $binds['ownerId'] = $owner->getId();
            }

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
    /***********************GESTION DES OFFRES***********************/

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
                        FROM offers_offer
                        WHERE owner_id = :userId
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
}