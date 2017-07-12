<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 01/02/2016
 * Time: 14:23
 */

namespace Users;


class StructureManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return Structure
     */
    public static function constructStructure(\PDO $db, array $data):Structure{
        $id = !empty($data['structure_id']) ? (int)$data['structure_id'] : (int)$data['id'];
        $name = (string)$data['structureName'];
        $address = (string)$data['address'];
        $postalCode = (string)$data['postalCode'];
        $town = (string)$data['town'];
        $phone = (string)$data['phone'];
        $mobile = (string)$data['mobile'];
        $fax = (string)$data['fax'];
        $email = (string)$data['email'];
        $isPartner = (bool)$data['isPartner'];
        $acceptNewsLetter = (bool)$data['acceptNewsLetter'];
        $imageName = (string)$data['imageName'];
        $societyDetails = (string)$data['societyDetails'];
        $siret = (string)$data['SIRET'];
        $ape = (string)$data['APE'];
        $packageContent = (string)$data['packageContent'];
        $packageProvision = (float)$data['packageProvision'];
        $isPrimary = (bool)$data['isPrimary'];
        $defaultMargin = (int)$data['defaultMargin'];
        //$freightCharges = (float)$data['freightCharges'] === $data['freightCharges'] ? (float)$data['freightCharges'] : null;
        $freightCharges = ($data['freightCharges'] === 'NULL') ? null : (float)$data['freightCharges'];
        $defaultWarranty = (string)$data['defaultWarranty'];
        $defaultFunding = (string)$data['defaultFunding'];

        return new Structure($id, $name, $address, $postalCode, $town, $phone, $mobile, $fax, $email, $isPartner,
                             $acceptNewsLetter, $imageName, $societyDetails, $siret, $ape, $packageContent,
                             $packageProvision, $isPrimary, $defaultMargin, $freightCharges, $defaultWarranty, $defaultFunding);
    }

    /**
     * @param \PDO  $db
     * @param int $structureId
     *
     * @return \Exception|Structure
     */
    public static function fetchStructure(\PDO $db, \int $structureId){
        try{
            $query = 'SELECT * FROM usr_structures WHERE id = :structureId ;';
            $binds = array(':structureId' => $structureId);

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Impossible de trouver la structure demandée.');
            }

            return self::constructStructure($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     *
     * @return \Exception|Structure
     */
    public static function fetchLastCreatedStructure(\PDO $db){
        try{
            $query = 'SELECT * FROM usr_structures ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Aucune structure actuellement en base.');
            }

            return self::constructStructure($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Structure $structure
     *
     * @return \Exception|Structure
     */
    public static function insertStructure(\PDO $db, Structure $structure){
        try{
            $query = 'INSERT INTO usr_structures (
                                                    structureName,
                                                    address,
                                                    postalCode,
                                                    town,
                                                    phone,
                                                    mobile,
                                                    fax,
                                                    email,
                                                    isPartner,
                                                    acceptNewsLetter,
                                                    imageName,
                                                    societyDetails,
                                                    SIRET,
                                                    APE,
                                                    packageContent,
                                                    packageProvision,
                                                    defaultMargin,
                                                    freightCharges,
                                                    defaultWarranty,
                                                    defaultFunding
                                                )
                                        VALUES  (
                                                    :structureName,
                                                    :address,
                                                    :postalCode,
                                                    :town,
                                                    :phone,
                                                    :mobile,
                                                    :fax,
                                                    :email,
                                                    :isPartner,
                                                    :acceptNewsLetter,
                                                    :imageName,
                                                    :societyDetails,
                                                    :siret,
                                                    :ape,
                                                    :packageContent,
                                                    :packageProvision,
                                                    :defaultMargin,
                                                    :freightCharges,
                                                    :defaultWarranty,
                                                    :defaultFunding
                                        )
            ;';
            $binds = array(
                ':structureName'    => $structure->getStructureName(),
                ':address'          => $structure->getAddress(),
                ':postalCode'       => $structure->getPostalCode(),
                ':town'             => $structure->getTown(),
                ':phone'            => $structure->getPhone(),
                ':mobile'           => $structure->getMobile(),
                ':fax'              => $structure->getFax(),
                ':email'            => $structure->getEmail(),
                ':isPartner'        => $structure->getIsPartner(),
                ':acceptNewsLetter' => $structure->getAcceptNewsLetter(),
                ':imageName'        => $structure->getImageName(),
                ':societyDetails'   => $structure->getSocietyDetails(),
                ':siret'            => $structure->getSiret(),
                ':ape'              => $structure->getApe(),
                ':packageContent'   => $structure->getPackageContent(),
                ':packageProvision' => $structure->getPackageProvision(),
                ':defaultMargin'    => $structure->getDefaultMargin(),
                ':freightCharges'   => $structure->getFreightCharges(),
                ':defaultWarranty'  => $structure->getDefaultWarranty(),
                ':defaultFunding'   => $structure->getDefaultFunding()
            );

            $q = $db->prepare($query);
            $q->execute($binds);

            $structure->setId((int)$db->lastInsertId());

            return $structure;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param Structure $structure
     *
     * @return bool|\Exception
     */
    public static function hydrateStructure(\PDO $db, Structure $structure){
        try{
            $query = 'UPDATE usr_structures SET     structureName = :structureName,
                                                    address = :address,
                                                    postalCode = :postalCode,
                                                    town = :town,
                                                    phone = :phone,
                                                    mobile = :mobile,
                                                    fax = :fax,
                                                    email = :email,
                                                    isPartner = :isPartner,
                                                    acceptNewsLetter = :acceptNewsLetter,
                                                    imageName = :imageName,
                                                    societyDetails = :societyDetails,
                                                    SIRET = :siret,
                                                    APE = :ape,
                                                    packageContent = :packageContent,
                                                    packageProvision = :packageProvision,
                                                    defaultMargin = :defaultMargin,
                                                    freightCharges = :freightCharges,
                                                    defaultWarranty = :defaultWarranty,
                                                    defaultFunding = :defaultFunding
                                            WHERE id = :structureId
            ;';
            $binds = array(
                ':structureId'      => $structure->getId(),
                ':structureName'    => $structure->getStructureName(),
                ':address'          => $structure->getAddress(),
                ':postalCode'       => $structure->getPostalCode(),
                ':town'             => $structure->getTown(),
                ':phone'            => $structure->getPhone(),
                ':mobile'           => $structure->getMobile(),
                ':fax'              => $structure->getFax(),
                ':email'            => $structure->getEmail(),
                ':isPartner'        => $structure->getIsPartner(),
                ':acceptNewsLetter' => $structure->getAcceptNewsLetter(),
                ':imageName'        => $structure->getImageName(),
                ':societyDetails'   => $structure->getSocietyDetails(),
                ':siret'            => $structure->getSiret(),
                ':ape'              => $structure->getApe(),
                ':packageContent'   => $structure->getPackageContent(),
                ':packageProvision' => $structure->getPackageProvision(),
                ':defaultMargin'    => $structure->getDefaultMargin(),
                ':freightCharges'   => $structure->getFreightCharges(),
                ':defaultWarranty'  => $structure->getDefaultWarranty(),
                ':defaultFunding'   => $structure->getDefaultFunding()
            );

            $q = $db->prepare($query);
            $q->execute($binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteStructure(\PDO $db, \int $structureId){
        try{
            //TODO : Voir comment on gère la suppression d'une structure
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
     * @return \Exception|Structure[]
     */
    public static function fetchListOfStructures(\PDO $db, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = 'SELECT * FROM usr_structures ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array();

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Aucune structure actuellement en base.');
            }

            $structures = array();
            foreach($results as $result){
                $structures[] = self::constructStructure($db, $result);
            }

            return $structures;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     *
     * @return \Exception|Structure
     */
    public static function fetchPrimaryStructure(\PDO $db){
        try{
            $query = 'SELECT * FROM usr_structures WHERE isPrimary = 1;';
            $binds = array();

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Impossible de trouver la structure demandée.');
            }

            return self::constructStructure($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
}