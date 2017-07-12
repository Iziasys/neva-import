<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 25/02/2016
 * Time: 10:37
 */

namespace Users;


class ClientManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return IndividualClient|SocietyClient
     */
    public static function constructClient(\PDO $db, array $data){
        $id = isset($data['client_id']) ? (int)$data['client_id'] : (int)$data['id'];
        $ownerId = (int)$data['owner_id'];
        $isSociety = (bool)$data['isSociety'];
        $societyName = (string)$data['societyName'];
        $siren = (string)$data['siren'];
        $siret = (string)$data['siret'];
        $addressNumber = (int)$data['addressNumber'];
        $addressExtension = (string)$data['addressExtension'];
        $streetType = (string)$data['streetType'];
        $addressWording = (string)$data['addressWording'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        $civility = (string)$data['civility'];
        $latName = (string)$data['lastName'];
        $firstName = (string)$data['firstName'];
        $phone = preg_replace_callback('#[^0-9]#', function(){return '';}, $data['phone']);
        $mobile = preg_replace_callback('#[^0-9]#', function(){return '';}, $data['mobile']);
        $fax = preg_replace_callback('#[^0-9]#', function(){return '';}, $data['fax']);
        $email = (string)$data['email'];
        $acceptOffers = (bool)$data['acceptOffers'];
        $insertionDate = new \DateTime($data['insertionDate']);

        if($isSociety){
            return new SocietyClient($id, $email, $latName, $firstName, $civility, $phone, $mobile, $fax, $acceptOffers,
                                     true, $ownerId, $addressNumber, $addressExtension, $streetType, $addressWording,
                                     $postalCode, $town, $insertionDate, $societyName, $siren, $siret);
        }
        else{
            return new IndividualClient($id, $email, $latName, $firstName, $civility, $phone, $mobile, $fax, $acceptOffers,
                                        false, $ownerId, $addressNumber, $addressExtension, $streetType, $addressWording,
                                        $postalCode, $town, $insertionDate);
        }
    }

    /**
     * @param \PDO $db
     * @param int  $clientId
     *
     * @return \Exception|IndividualClient|SocietyClient
     */
    public static function fetchClient(\PDO $db, \int $clientId){
        try{
            $query = 'SELECT * FROM usr_clients WHERE id = :clientId;';
            $binds = array(':clientId' => $clientId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le client demandé.');
            }

            return self::constructClient($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|IndividualClient|SocietyClient
     */
    public static function fetchLastCreatedClient(\PDO $db){
        try{
            $query = 'SELECT * FROM usr_clients ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun client actuellement en base.');
            }

            return self::constructClient($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Client $client
     *
     * @return \Exception|IndividualClient|SocietyClient
     */
    public static function insertClient(\PDO $db, Client $client){
        try{
            $query = 'INSERT INTO usr_clients   (
                                                    isSociety,
                                                    owner_id,
                                                    societyName,
                                                    siren,
                                                    siret,
                                                    addressNumber,
                                                    addressExtension,
                                                    streetType,
                                                    addressWording,
                                                    postalCode,
                                                    town,
                                                    civility,
                                                    lastName,
                                                    firstName,
                                                    phone,
                                                    mobile,
                                                    fax,
                                                    email,
                                                    acceptOffers,
                                                    insertionDate
                                                )
                                     VALUES     (
                                                    :isSociety,
                                                    :ownerId,
                                                    :societyName,
                                                    :siren,
                                                    :siret,
                                                    :addressNumber,
                                                    :addressExtension,
                                                    :streetType,
                                                    :addressWording,
                                                    :postalCode,
                                                    :town,
                                                    :civility,
                                                    :lastName,
                                                    :firstName,
                                                    :phone,
                                                    :mobile,
                                                    :fax,
                                                    :email,
                                                    :acceptOffers,
                                                    :insertionDate
                                     )
            ;';
            $binds = array(
                ':isSociety'        => $client->getIsSociety(),
                ':ownerId'         => $client->getOwnerId(),
                ':societyName'      => is_a($client, '\Users\SocietyClient') ? $client->getName() : null,
                ':siren'            => is_a($client, '\Users\SocietyClient') ? $client->getSiren() : null,
                ':siret'            => is_a($client, '\Users\SocietyClient') ? $client->getSiret() : null,
                ':addressNumber'    => $client->getAddressNumber(),
                ':addressExtension' => $client->getAddressExtension(),
                ':streetType'       => $client->getStreetType(),
                ':addressWording'   => $client->getAddressWording(),
                ':postalCode'       => $client->getPostalCode(),
                ':town'             => $client->getTown(),
                ':civility'         => $client->getCivility(),
                ':lastName'         => $client->getLastName(),
                ':firstName'        => $client->getFirstName(),
                ':phone'            => $client->getPhone(),
                ':mobile'           => $client->getMobile(),
                ':fax'              => $client->getFax(),
                ':email'            => $client->getEmail(),
                ':acceptOffers'     => $client->getAcceptNewsLetter(),
                ':insertionDate'    => $client->getInsertionDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            $client->setId($db->lastInsertId());

            return $client;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Client $client
     *
     * @return bool|\Exception
     */
    public static function hydrateClient(\PDO $db, Client $client){
        try{
            $query = 'UPDATE usr_clients SET
                                                isSociety = :isSociety,
                                                owner_id = :ownerId,
                                                societyName = :societyName,
                                                siren = :siren,
                                                siret = :siret,
                                                addressNumber = :addressNumber,
                                                addressExtension = :addressExtension,
                                                streetType = :streetType,
                                                addressWording = :addressWording,
                                                postalCode = :postalCode,
                                                town = :town,
                                                civility = :civility,
                                                lastName = :lastName,
                                                firstName = :firstName,
                                                phone = :phone,
                                                mobile = :mobile,
                                                fax = :fax,
                                                email = :email,
                                                acceptOffers = :acceptOffers,
                                                insertionDate = :insertionDate
                                        WHERE id = :clientId

            ;';
            $binds = array(
                ':clientId'         => $client->getId(),
                ':isSociety'        => $client->getIsSociety(),
                ':ownerId'         => $client->getOwnerId(),
                ':societyName'      => is_a($client, '\Users\SocietyClient') ? $client->getName() : null,
                ':siren'            => is_a($client, '\Users\SocietyClient') ? $client->getSiren() : null,
                ':siret'            => is_a($client, '\Users\SocietyClient') ? $client->getSiret() : null,
                ':addressNumber'    => $client->getAddressNumber(),
                ':addressExtension' => $client->getAddressExtension(),
                ':streetType'       => $client->getStreetType(),
                ':addressWording'   => $client->getAddressWording(),
                ':postalCode'       => $client->getPostalCode(),
                ':town'             => $client->getTown(),
                ':civility'         => $client->getCivility(),
                ':lastName'         => $client->getLastName(),
                ':firstName'        => $client->getFirstName(),
                ':phone'            => $client->getPhone(),
                ':mobile'           => $client->getMobile(),
                ':fax'              => $client->getFax(),
                ':email'            => $client->getEmail(),
                ':acceptOffers'     => $client->getAcceptNewsLetter(),
                ':insertionDate'    => $client->getInsertionDate()->format('Y-m-d H:i:s')
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteClient(\PDO $db, \int $clientId){
        //TODO : Voir comment on gère la suppression d'un client
    }

    /**
     * @param \PDO $db
     * @param int  $structureId
     * @param bool $isPrimary
     * @param bool $societies
     *
     * @return \Exception|IndividualClient[]|SocietyClient[]
     */
    public static function fetchClientsList(\PDO $db, \int $structureId, \bool $isPrimary, \bool $societies = null){
        try{
            $query = 'SELECT * FROM usr_clients WHERE 1 ';
            if($societies !== null){
                if($societies)
                    $query .= ' AND isSociety = 1 ';
                else
                    $query .= ' AND isSociety = 0 ';
            }

            $query .= ' AND owner_id = :structureId ';
            $binds = array(':structureId' => $structureId);

            if($isPrimary){
                $query .= ' ORDER BY isSociety, societyName, firstName, lastName ASC;';
            }
            else{
                $query .= ' ORDER BY isSociety, societyName, firstName, lastName ASC;';
            }

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun client actuellement en base.');
            }

            $clientsArray = array();
            foreach($results as $result){
                $clientsArray[] = self::constructClient($db, $result);
            }

            return $clientsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}