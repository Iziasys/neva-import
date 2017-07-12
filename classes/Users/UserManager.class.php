<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 22/01/2016
 * Time: 12:12
 */

namespace Users;


class UserManager
{
    /**
     * @param \PDO  $db
     * @param array $data
     *
     * @return User
     */
    public static function constructUser(\PDO $db, array $data):User{
        $id = !empty($data['user_id']) ? (int)$data['user_id'] : (int)$data['id'];
        $email = (string)$data['email'];
        $password = (string)$data['password'];
        $type = (int)$data['type'];
        $structureId = (int)$data['structure_id'];
        $structure = StructureManager::fetchStructure($db, $structureId);
        $firstName = (string)$data['firstname'];
        $lastName = (string)$data['lastname'];
        $phone = (string)$data['phone'];
        $mobile = (string)$data['mobile'];
        $lastConnection = new \DateTime($data['lastConnection']);
        $acceptNewsLetter = (bool)$data['acceptNewsLetter'];
        $rightsId = (int)$data['rights_id'];
        $rights = RightsManager::fetchRights($db, $rightsId);

        return new User($id, $email, $password, $type, $structure, $structureId, $firstName, $lastName, $phone,
                        $mobile, $lastConnection, $acceptNewsLetter, $rights, $rightsId);
    }

    /**
     * @param \PDO  $db
     * @param int $userId
     *
     * @return \Exception|User
     */
    public static function fetchUser(\PDO $db, \int $userId){
        try{
            $query = 'SELECT * FROM usr_users WHERE id = :userId ;';
            $binds = array(':userId' => $userId);

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'utilisateur demandé.');
            }

            return self::constructUser($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     *
     * @return \Exception|User
     */
    public static function fetchLastCreatedUser(\PDO $db){
        try{
            $query = 'SELECT * FROM usr_users ORDER BY id DESC LIMIT 1;';
            $binds = array();

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Aucun utilisateur actuellement en base.');
            }

            return self::constructUser($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param User $user
     *
     * @return \Exception|User
     */
    public static function insertUser(\PDO $db, User $user){
        try{
            $query = 'INSERT INTO usr_users(  email,
                                              password,
                                              type,
                                              firstname,
                                              lastname,
                                              structure_id,
                                              phone,
                                              mobile,
                                              lastConnection,
                                              acceptNewsLetter,
                                              rights_id
                                            )
                                    VALUES(   :email,
                                              :password,
                                              :type,
                                              :firstName,
                                              :lastName,
                                              :structureId,
                                              :phone,
                                              :mobile,
                                              :lastConnection,
                                              :acceptNewsLetter,
                                              :rightsId
                                    )
            ;';
            $binds = array(
                ':email'            => $user->getEmail(),
                ':password'         => $user->getPassword(),
                ':type'             => $user->getType(),
                ':firstName'        => $user->getFirstName(),
                ':lastName'         => $user->getLastName(),
                ':structureId'      => $user->getStructureId(),
                ':phone'            => $user->getPhone(),
                ':mobile'           => $user->getMobile(),
                ':lastConnection'   => $user->getLastConnection()->format('Y-m-d H:i:s'),
                ':acceptNewsLetter' => $user->getAcceptNewsLetter(),
                ':rightsId'         => $user->getRightsId()
            );

            $q = $db->prepare($query);
            $q->execute($binds);

            $user->setId((int)$db->lastInsertId());

            return $user;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO  $db
     * @param User $user
     *
     * @return bool|\Exception
     */
    public static function hydrateUser(\PDO $db, User $user){
        try{
            $query = 'UPDATE usr_users
                      SET email = :email,
                          password = :password,
                          type = :type,
                          firstname = :firstName,
                          lastname = :lastName,
                          structure_id = :structureId,
                          phone = :phone,
                          mobile = :mobile,
                          lastConnection = :lastConnection,
                          acceptNewsLetter = :acceptNewsLetter
                      WHERE id = :userId
            ;';
            $binds = array(
                ':userId'           => $user->getId(),
                ':email'            => $user->getEmail(),
                ':password'         => $user->getPassword(),
                ':type'             => $user->getType(),
                ':firstName'        => $user->getFirstName(),
                ':lastName'         => $user->getLastName(),
                ':structureId'      => $user->getStructureId(),
                ':phone'            => $user->getPhone(),
                ':mobile'           => $user->getMobile(),
                ':lastConnection'   => $user->getLastConnection()->format('Y-m-d H:i:s'),
                ':acceptNewsLetter' => $user->getAcceptNewsLetter()
            );

            $q = $db->prepare($query);
            $q->execute($binds);

            return true;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    public static function deleteUser(\PDO $db, \int $userId){
        //TODO : Gestion de la suppression d'un utilisateur
    }

    /**
     * @param \PDO   $db
     * @param string $email
     * @param string $rawPassword
     *
     * @return \Exception|User
     */
    public static function tryToConnect(\PDO $db, \string $email, \string $rawPassword){
        try{
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                throw new \Exception('L\'email entré n\'est pas au bon format.');
            }
            //Sélection de l\'utilisateur correspondant à l\'email entré
            $query = 'SELECT * FROM usr_users WHERE email LIKE :email ;';
            $binds = array(':email' => $email);

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            //Si aucun résultat, erreur sur le login (en l'occurence l'email)
            if(empty($results)){
                throw new \Exception('L\'email entré est incorrect.');
            }

            //On vérifie ensuite la correspondance du mot de pass entré
            if(\password_verify($rawPassword, $results[0]['password'])){
                //Et on retourne l'utilisateur qui vient de se connecter
                return self::constructUser($db, $results[0]);
            }
            //Si pas de correspondance
            else{
                throw new \Exception('Le mot de passe entré ne correspond pas.');
            }
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO   $db
     * @param int    $structureId
     * @param string $orderBy
     * @param string $way
     * @param int    $limit
     * @param int    $offset
     *
     * @return array|\Exception
     */
    public static function fetchListOfUsers(\PDO $db, \int $structureId, \string $orderBy = 'id', \string $way = 'ASC', \int $limit = 50, \int $offset = 0){
        try{
            $query = 'SELECT * FROM usr_users WHERE structure_id = :structureId ORDER BY '.$orderBy.' '.$way.' LIMIT '.$limit.' OFFSET '.$offset.';';
            $binds = array(':structureId' => $structureId);

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Aucun utilisateur pour cette structure actuellement en base.');
            }

            $users = array();
            foreach($results as $result){
                $users[] = self::constructUser($db, $result);
            }

            return $users;
        }
        catch(\Exception $e){
            return $e;
        }
    }

    /**
     * @param \PDO $db
     * @param int  $structureId
     *
     * @return \Exception|User
     */
    public static function fetchAdminOfStructure(\PDO $db, \int $structureId){
        try{
            $query = 'SELECT * FROM usr_users WHERE structure_id = :structureId AND (type = 1 OR type = 2) ORDER BY id ASC LIMIT 1;';
            $binds = array(':structureId' => $structureId);

            $q = $db->prepare($query);
            $q->execute($binds);

            $results = array();
            while($data = $q->fetch(\PDO::FETCH_ASSOC)){
                $results[] = $data;
            }

            if(empty($results)){
                throw new \Exception('Impossible de trouver l\'utilisateur demandé.');
            }

            return self::constructUser($db, $results[0]);
        }
        catch(\Exception $e){
            return $e;
        }
    }
}