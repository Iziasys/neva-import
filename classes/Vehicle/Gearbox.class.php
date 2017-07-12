<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 11:37
 */

namespace Vehicle;


class Gearbox
{
    private $id, $name;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param int $id
     */
    public function setId(\int $id){
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId():\int{
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName(\string $name){
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName():\string{
        return $this->name;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Bodywork constructor.
     *
     * @param int    $id
     * @param string $name
     */
    public function __construct(\int $id = 0, \string $name = ''){
        $this->setId($id);
        $this->setName($name);
    }
    /*******************CONSTRUCTOR*****************/
}