<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 10:04
 */

namespace Vehicle;


class ExternalColor
{
    protected $id, $biTone, $name, $details;

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
     * @param bool $biTone
     */
    public function setBiTone(\bool $biTone){
        $this->biTone = (bool)$biTone;
    }

    /**
     * @return bool
     */
    public function getBiTone():\bool{
        return $this->biTone;
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

    /**
     * @param string $details
     */
    public function setDetails(\string $details){
        $this->details = (string)$details;
    }

    /**
     * @return string
     */
    public function getDetails():\string{
        return $this->details;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * ExternalColor constructor.
     *
     * @param int    $id
     * @param bool   $biTone
     * @param string $name
     * @param string $details
     */
    public function __construct(\int $id = 0, \bool $biTone = false, \string $name = '', \string $details = ''){
        $this->setId($id);
        $this->setBiTone($biTone);
        $this->setName($name);
        $this->setDetails($details);
    }
    /*******************CONSTRUCTOR*****************/
}