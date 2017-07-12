<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 11/02/2016
 * Time: 11:21
 */

namespace Prices;


class PriceManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Price
     */
    public static function constructPrice(\PDO $db, array $data):Price{
        $id = (!empty($data['price_id'])) ? (int)$data['price_id'] : (int)$data['id'];
        $pretaxBuyingPrice = (float)$data['pretaxBuyingPrice'];
        $postTaxesPublicPrice = (float)$data['postTaxesPublicPrice'];
        $countryId = (int)$data['country_id'];
        $currencyId = (int)$data['currency_id'];
        $margin = (float)$data['margin'];
        $maximumDiscount = (float)$data['maximumDiscount'];
        $priceDate = new \DateTime($data['priceDate']);
        $managementFees = (float)$data['managementFees'];

        return new Price($id, $pretaxBuyingPrice, $postTaxesPublicPrice, null, $countryId, null, $currencyId, $margin, $maximumDiscount, $priceDate, $managementFees);
    }

    /**
     * @param \PDO $db
     * @param int  $priceId
     *
     * @return \Exception|Price
     */
    public static function fetchPrice(\PDO $db, \int $priceId){
        try{
            $query = 'SELECT * FROM prices_price WHERE id = :priceId ;';
            $binds = array(':priceId' => $priceId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver le prix demandé.');
            }

            return self::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Price
     */
    public static function fetchLastCreatedPrice(\PDO $db){
        try{
            $query = 'SELECT * FROM prices_price ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucun prix actuellement en base.');
            }

            return self::constructPrice($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Price $price
     *
     * @return \Exception|Price
     */
    public static function insertPrice(\PDO $db, Price $price){
        try{
            $query = 'INSERT INTO prices_price (pretaxBuyingPrice, postTaxesPublicPrice, country_id, currency_id, margin, maximumDiscount, priceDate, managementFees)
                                        VALUES (:pretaxBuyingPrice, :postTaxesPublicPrice, :countryId, :currencyId, :margin, :maximumDiscount, :priceDate, :managementFees)
            ;';
            $binds = array(
                ':pretaxBuyingPrice' => $price->getPretaxBuyingPrice(),
                ':postTaxesPublicPrice' => $price->getPostTaxesPublicPrice(),
                ':countryId' => $price->getCountryId(),
                ':currencyId' => $price->getCurrencyId(),
                ':margin' => $price->getMargin(),
                ':maximumDiscount' => $price->getMaximumDiscount(),
                ':priceDate' => $price->getPriceDate()->format('Y-m-d H:i:d'),
                ':managementFees' => $price->getManagementFees()
            );

            \executeInsert($db, $query, $binds);

            $price->setId($db->lastInsertId());
            return $price;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Price $price
     *
     * @return bool|\Exception
     */
    public static function hydratePrice(\PDO $db, Price $price){
        try{
            $query = 'UPDATE prices_price SET   pretaxBuyingPrice = :pretaxBuyingPrice,
                                                postTaxesPublicPrice = :postTaxesPublicPrice,
                                                country_id = :countryId,
                                                currency_id = :currencyId,
                                                margin = :margin,
                                                maximumDiscount = :maximumDiscount,
                                                priceDate = :priceDate,
                                                managementFees = :managementFees
                                        WHERE   id = :priceId
            ;';
            $binds = array(
                ':priceId' => $price->getId(),
                ':pretaxBuyingPrice' => $price->getPretaxBuyingPrice(),
                ':postTaxesPublicPrice' => $price->getPostTaxesPublicPrice(),
                ':countryId' => $price->getCountryId(),
                ':currencyId' => $price->getCurrencyId(),
                ':margin' => $price->getMargin(),
                ':maximumDiscount' => $price->getMaximumDiscount(),
                ':priceDate' => $price->getPriceDate()->format('Y-m-d H:i:d'),
                ':managementFees' => $price->getManagementFees()
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deletePrice(\PDO $db, \int $priceId){
        //TODO : Voir comment on gère la suppression d'un prix
    }


}