<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 13:06
 */

namespace Vehicle;


abstract class BrandManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Brand
     */
    private static function constructBrand(\PDO $db, array $data):Brand{
        $id = (int)$data['id'];
        $name = (string)$data['brandName'];

        return new Brand($id, $name);
    }

    /**
     * @param \PDO $db
     * @param int  $brandId
     *
     * @return \Exception|Brand
     */
    public static function fetchBrand(\PDO $db, \int $brandId = 0){
        try{
            $query = 'SELECT vhcl_brand.id AS id, vhcl_brand.brandName AS brandName FROM vhcl_brand WHERE id = :brandId';
            $binds = array(':brandId' => $brandId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la marque demandée.');
            }

            return self::constructBrand($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Brand
     */
    public static function fetchLastCreatedBrand(\PDO $db){
        try{
            $query = 'SELECT vhcl_brand.id AS id, vhcl_brand.brandName AS brandName FROM vhcl_brand ORDER BY ID DESC LIMIT 1;';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Il n\'y a actuellement aucune marque de véhicule en base.');
            }

            return self::constructBrand($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Brand $brand
     *
     * @return \Exception|Brand
     */
    public static function insertBrand(\PDO $db, Brand $brand){
        try{
            $query = 'INSERT INTO vhcl_brand(brandName) VALUES(:name);';
            $binds = array(':name' => $brand->getName());

            \executeInsert($db, $query, $binds);

            $brand->setId($db->lastInsertId());
            return $brand;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Brand $brand
     *
     * @return bool|\Exception
     */
    public static function hydrateBrand(\PDO $db, Brand $brand){
        try{
            $query = 'UPDATE vhcl_brand SET brandName = :name WHERE id = :id ;';
            $binds = array(':id' => $brand->getId(), ':name' => $brand->getName());

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteBrand(\PDO $db, \int $brandId = 0)
    {
        try{
            //TODO : Voir comment on gère le delete d'une marque. Le permet-on ou non ?:
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $orderBy
     * @param string $way
     * @param int    $limit
     * @param int    $offset
     *
     * @return \Exception|Brand[]
     */
    public static function fetchBrandList(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = 'SELECT * FROM vhcl_brand ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune marque actuellement en base.');
            }

            $brandList = array();
            foreach($results as $result){
                $brandList[] = self::constructBrand($db, $result);
            }

            return $brandList;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO    $db
     * @param \string $mode
     *
     * @return \Exception|string[]
     */
    public static function fetchBrandListWhereAVehicleIsAvailable(\PDO $db, \string $mode = 'all'){
        try{
            $brandsArray = array();

            if($mode != 'stock'){
                $query = '  SELECT vhcl_brand.*
                            FROM vhcl_brand
                            INNER JOIN vhcl_model
                              ON vhcl_brand.id = vhcl_model.brand_id
                            INNER JOIN vhcl_finish
                              ON vhcl_model.id = vhcl_finish.model_id
                            INNER JOIN vhcl_details
                              ON vhcl_finish.id = vhcl_details.finish_id
                            WHERE vhcl_finish.available = 1
                            AND vhcl_details.available = 1
                            GROUP BY brandName
                            ORDER BY brandName
                ;';
                $binds = array();

                $results = \executeSelect($db, $query, $binds);

                foreach($results as $result){
                    $brandsArray[] = $result['brandName'];
                }
            }
            if($mode != 'command'){
                $query = 'SELECT brand FROM vhcl_vehicleInStock WHERE sold = 0 GROUP BY brand ORDER BY brand';
                $binds = array();

                $results = \executeSelect($db, $query, $binds);

                foreach($results as $result){
                    if(!in_array($result['brand'], $brandsArray)){
                        $brandsArray[] = $result['brand'];
                    }
                }
            }

            if(empty($brandsArray)){
                throw new \Exception('Aucune marque disponible à la vente actuellement.');
            }

            return $brandsArray;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param string $brandName
     *
     * @return \Exception|Brand
     */
    public static function fetchBrandByName(\PDO $db, \string $brandName){
        try{
            $query = 'SELECT vhcl_brand.id AS id, vhcl_brand.brandName AS brandName FROM vhcl_brand WHERE brandName = :brandName';
            $binds = array(':brandName' => $brandName);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la marque demandée.');
            }

            return self::constructBrand($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
}