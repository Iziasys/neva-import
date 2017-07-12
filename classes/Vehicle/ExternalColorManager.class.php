<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 16/02/2016
 * Time: 09:48
 */

namespace Vehicle;


class ExternalColorManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return ExternalColor
     */
    public static function constructColor(\PDO $db, array $data):ExternalColor{
        $id = !empty($data['externalColor_id']) ? (int)$data['externalColor_id'] : (int)$data['id'];
        $biTone = (bool)$data['bitone'];
        $name = (string)$data['name'];
        $details = (string)$data['details'];

        return new ExternalColor($id, $biTone, $name, $details);
    }

    /**
     * @param \PDO $db
     * @param int  $colorId
     *
     * @return \Exception|ExternalColor
     */
    public static function fetchColor(\PDO $db, \int $colorId){
        try{
            $query = 'SELECT * FROM vhcl_externalColor WHERE id = :colorId;';
            $binds = array(':colorId' => $colorId);

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la couleur demandée.');
            }

            return self::constructColor($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO          $db
     * @param ExternalColor $color
     *
     * @return \Exception|ExternalColor
     */
    public static function fetchColorByInformation(\PDO $db, ExternalColor $color){
        try{
            $query = 'SELECT * FROM vhcl_externalColor WHERE bitone = :bitone AND name LIKE :name AND details LIKE :details ;';
            $binds = array(
                ':bitone'  => $color->getBiTone(),
                ':name'    => $color->getName(),
                ':details' => $color->getDetails()
            );

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Impossible de trouver la couleur demandée.');
            }

            return self::constructColor($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|ExternalColor
     */
    public static function fetchLastCreatedColor(\PDO $db){
        try{
            $query = 'SELECT * FROM vhcl_externalColor ORDER BY id DESC LIMIT 1';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune couleur actuellement en base.');
            }

            return self::constructColor($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO          $db
     * @param ExternalColor $color
     *
     * @return \Exception|ExternalColor
     */
    public static function insertColor(\PDO $db, ExternalColor $color){
        try{
            $query = 'INSERT INTO vhcl_externalColor (bitone, name, details) VALUES (:bitone, :name, :details)';
            $binds = array(
                ':bitone'  => $color->getBiTone(),
                ':name'    => $color->getName(),
                ':details' => $color->getDetails()
            );

            \executeInsert($db, $query, $binds);

            $color->setId($db->lastInsertId());

            return $color;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO          $db
     * @param ExternalColor $color
     *
     * @return bool|\Exception
     */
    public static function hydrateColor(\PDO $db, ExternalColor $color){
        try{
            $query = 'UPDATE vhcl_externalColor SET bitone = :bitone, name = :name, details = :details WHERE id = :colorId ;';
            $binds = array(
                ':colorId' => $color->getId(),
                ':bitone'  => $color->getBiTone(),
                ':name'    => $color->getName(),
                ':details' => $color->getDetails()
            );

            \executeInsert($db, $query, $binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteColor(\PDO $db, \int $colorId){
        try{
            //TODO : Voir comment on gère la suppression d'une couleur
            return true;
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
     * @return \Exception|ExternalColor[]
     */
    public static function fetchColorsList(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = 'SELECT * FROM vhcl_externalColor ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $results = \executeSelect($db, $query, $binds);

            if(empty($results)){
                throw new \Exception('Aucune couleur d\'extérieur actuellement en base.');
            }

            $colorsList = array();
            foreach($results as $result){
                $colorsList[] = self::constructColor($db, $result);
            }

            return $colorsList;
        }
        catch(\Exception $e){
            return $e;
        }
    }
}