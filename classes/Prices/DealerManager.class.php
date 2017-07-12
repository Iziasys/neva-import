<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 05/02/2016
 * Time: 14:55
 */

namespace Prices;


use Users\Address;
use Users\Person;

class DealerManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Dealer
     */
    private static function constructDealer(\PDO $db, array $data):Dealer{
        //Information : Ici quand on demande de construire l'objet, on ne cherchera pas toutes les informations sur son pays.
        //              Il faudra donc les définir a la main avec la méthode setCountry() si on en a besoin
        //Raison :      Si on charge toutes les informations de tous les objets, les liaisons étant nombreuses, on aura une surcharge réelle
        $id = (int)$data['id'];
        $name = (string)$data['name'];
        $countryId = (int)$data['country_id'];
        $country = null;
        $addressNumber = (int)$data['addressNumber'];
        $addressExtension = (string)$data['addressExtension'];
        $streetType = (string)$data['streetType'];
        $wording = (string)$data['wording'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        $address = new Address($addressNumber, $addressExtension, $streetType, $wording, $postalCode, $town);
        $phone = (string)$data['phone'];
        $fax = (string)$data['fax'];
        $email = (string)$data['email'];
        $acceptNewsLetter = (bool)$data['acceptNewsLetter'];
        $civility = (string)$data['contactCivility'];
        $firstName = (string)$data['contactFirstName'];
        $lastName = (string)$data['contactLastName'];
        $contactPhone = (string)$data['contactPhone'];
        $contactMobile = (string)$data['contactMobile'];
        $contactEmail = (string)$data['contactEmail'];
        $contact = new Person(0, $contactEmail, $lastName, $firstName, $civility, $contactPhone, $contactMobile, '', $acceptNewsLetter);
        $comments = (string)$data['comments'];

        return new Dealer($id, $name, $country, $countryId, $address, $phone, $fax, $email, $contact, $comments, $acceptNewsLetter);
    }

    /**
     * @param \PDO $db
     * @param int  $dealerId
     *
     * @return \Exception|Dealer
     */
    public static function fetchDealer(\PDO $db, \int $dealerId){
        try{
            $query = 'SELECT * FROM prices_dealer WHERE id = :dealerId;';
            $binds = array(':dealerId' => $dealerId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le consessionnaire demandé.');
            }

            return self::constructDealer($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Dealer
     */
    public static function fetchLastCreatedDealer(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_dealer ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun concessionnaire actuellement en base.');
            }

            return self::constructDealer($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Dealer $dealer
     *
     * @return \Exception|Dealer
     */
    public static function insertDealer(\PDO $db, Dealer $dealer){
        try{
            $query = '
                INSERT INTO prices_dealer (
                    name,
                    country_id,
                    addressNumber,
                    addressExtension,
                    streetType,
                    wording,
                    postalCode,
                    town,
                    phone,
                    fax,
                    email,
                    contactCivility,
                    contactFirstName,
                    contactLastName,
                    contactPhone,
                    contactMobile,
                    contactEmail,
                    comments,
                    acceptNewsLetter
                )
                VALUES (
                    :name,
                    :countryId,
                    :addressNumber,
                    :addressExtension,
                    :streetType,
                    :wording,
                    :postalCode,
                    :town,
                    :phone,
                    :fax,
                    :email,
                    :contactCivility,
                    :contactFirstName,
                    :contactLastName,
                    :contactPhone,
                    :contactMobile,
                    :contactEmail,
                    :comments,
                    :acceptNewsLetter
                )
            ;';
            $binds = array(
                ':name'             => $dealer->getName(),
                ':countryId'        => $dealer->getCountryId(),
                ':addressNumber'    => $dealer->getAddress()->getNumber(),
                ':addressExtension' => $dealer->getAddress()->getExtension(),
                ':streetType'       => $dealer->getAddress()->getStreetType(),
                ':wording'          => $dealer->getAddress()->getWording(),
                ':postalCode'       => $dealer->getAddress()->getPostalCode(),
                ':town'             => $dealer->getAddress()->getTown(),
                ':phone'            => $dealer->getPhone(),
                ':fax'              => $dealer->getFax(),
                ':email'            => $dealer->getEmail(),
                ':contactCivility'  => $dealer->getContact()->getCivility(),
                ':contactFirstName' => $dealer->getContact()->getFirstName(),
                ':contactLastName'  => $dealer->getContact()->getLastName(),
                ':contactPhone'     => $dealer->getContact()->getPhone(),
                ':contactMobile'    => $dealer->getContact()->getMobile(),
                ':contactEmail'     => $dealer->getContact()->getEmail(),
                ':comments'         => $dealer->getComments(),
                ':acceptNewsLetter' => $dealer->getAcceptNewsLetter()
            );

            executeInsert($db, $query, $binds);

            $dealer->setId($db->lastInsertId());

            return $dealer;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param Dealer $dealer
     *
     * @return bool|\Exception
     */
    public static function hydrateDealer(\PDO $db, Dealer $dealer){
        try{
            $query = '
              UPDATE prices_dealer
              SET name = :name,
                  country_id = :countryId,
                  addressNumber = :addressNumber,
                  addressExtension = :addressExtension,
                  streetType = :streetType,
                  wording = :wording,
                  postalCode = :postalCode,
                  town = :town,
                  phone = :phone,
                  fax = :fax,
                  email = :email,
                  contactCivility = :contactCivility,
                  contactFirstName = :contactFirstName,
                  contactLastName = :contactLastName,
                  contactPhone = :contactPhone,
                  contactMobile = :contactMobile,
                  contactEmail = :contactEmail,
                  comments = :comments,
                  acceptNewsLetter = :acceptNewsLetter
              WHERE id = :dealerId
            ;';
            $binds = array(
                ':dealerId'         => $dealer->getId(),
                ':name'             => $dealer->getName(),
                ':countryId'        => $dealer->getCountryId(),
                ':addressNumber'    => $dealer->getAddress()->getNumber(),
                ':addressExtension' => $dealer->getAddress()->getExtension(),
                ':streetType'       => $dealer->getAddress()->getStreetType(),
                ':wording'          => $dealer->getAddress()->getWording(),
                ':postalCode'       => $dealer->getAddress()->getPostalCode(),
                ':town'             => $dealer->getAddress()->getTown(),
                ':phone'            => $dealer->getPhone(),
                ':fax'              => $dealer->getFax(),
                ':email'            => $dealer->getEmail(),
                ':contactCivility'  => $dealer->getContact()->getCivility(),
                ':contactFirstName' => $dealer->getContact()->getFirstName(),
                ':contactLastName'  => $dealer->getContact()->getLastName(),
                ':contactPhone'     => $dealer->getContact()->getPhone(),
                ':contactMobile'    => $dealer->getContact()->getMobile(),
                ':contactEmail'     => $dealer->getContact()->getEmail(),
                ':comments'         => $dealer->getComments(),
                ':acceptNewsLetter' => $dealer->getAcceptNewsLetter()
            );

            executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteDealer(\PDO $db, \int $dealerId){
        //TODO : Voir comment on gère la suppression d'un concessionnaire
    }

    /**
     * @param \PDO   $db
     * @param string $orderBy
     * @param string $way
     * @param int    $limit
     * @param int    $offset
     *
     * @return Dealer[]|\Exception
     */
    public static function fetchDealersList(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = 'SELECT * FROM prices_dealer ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun concessionnaire actuellement en base.');
            }

            $dealers = array();
            foreach($results as $result){
                $dealers[] = self::constructDealer($db, $result);
            }

            return $dealers;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}