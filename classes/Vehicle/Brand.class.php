<?php

/**
 * Created by:  DECOCK Stephane
 * Date: 18/01/2016
 * Time: 15:19
 */

namespace Vehicle;

class Brand{
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
     * Brand constructor.
     * @param int $id
     * @param string $name
     */
    public function __construct(\int $id = 0, \string $name = ''){
        $this->setId($id);
        $this->setName($name);
    }
    /*******************CONSTRUCTOR*****************/
}