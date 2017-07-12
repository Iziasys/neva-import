<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 09:34
 */

namespace Vehicle;
use \Prices\Price;


class Pack
{
    private $id, $name, $equipmentsArray, $equipmentsIdsArray, $color, $colorId, $rimModel, $rimModelId, $price, $priceId;

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

    /**
     * @param Equipment[] $equipmentsArray
     */
    public function setEquipmentsArray(array $equipmentsArray){
        foreach($equipmentsArray as $item){
            if(!is_a($item, '\Vehicle\Equipment')){
                continue;
            }
            else{
                $this->equipmentsArray[] = $item;
            }
        }
    }

    /**
     * @return Equipment[]
     */
    public function getEquipmentsArray():array{
        return $this->equipmentsArray;
    }

    /**
     * @param int[] $equipmentsIdsArray
     */
    public function setEquipmentsIdsArray(array $equipmentsIdsArray){
        foreach($equipmentsIdsArray as $item){
            $this->equipmentsIdsArray[] = (int)$item;
        }
    }

    /**
     * @return int[]
     */
    public function getEquipmentsIdsArray():array{
        return $this->equipmentsIdsArray;
    }

    /**
     * @param ExternalColor $color
     */
    public function setColor(ExternalColor $color){
        $this->color = $color;
    }

    /**
     * @return ExternalColor
     */
    public function getColor():ExternalColor{
        return $this->color;
    }

    /**
     * @param int $colorId
     */
    public function setColorId(\int $colorId){
        $this->colorId = (int)$colorId;
    }

    /**
     * @return int
     */
    public function getColorId():\int{
        return $this->colorId;
    }

    /**
     * @param RimModel $rimModel
     */
    public function setRimModel(RimModel $rimModel){
        $this->rimModel = $rimModel;
    }

    /**
     * @return RimModel
     */
    public function getRimModel():RimModel{
        return $this->rimModel;
    }

    /**
     * @param int $rimModelId
     */
    public function setRimModelId(\int $rimModelId){
        $this->rimModelId = (int)$rimModelId;
    }

    /**
     * @return int
     */
    public function getRimModelId():\int{
        return $this->rimModelId;
    }

    /**
     * @param Price $price
     */
    public function setPrice(Price $price){
        $this->price = $price;
    }

    /**
     * @return Price
     */
    public function getPrice():Price{
        return $this->price;
    }

    /**
     * @param int $priceId
     */
    public function setPriceId(\int $priceId){
        $this->priceId = (int)$priceId;
    }

    /**
     * @return int
     */
    public function getPriceId():\int{
        return $this->priceId;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Pack constructor.
     *
     * @param int                $id
     * @param string             $name
     * @param Equipment[]        $equipmentsArray
     * @param int[]              $equipmentsIdsArray
     * @param ExternalColor|null $color
     * @param int                $colorId
     * @param RimModel|null      $rimModel
     * @param int                $rimModelId
     * @param Price|null         $price
     * @param int                $priceId
     */
    public function __construct(\int $id = 0, \string $name = '', array $equipmentsArray = [],
                                array $equipmentsIdsArray = [], ExternalColor $color = null, \int $colorId = 0,
                                RimModel $rimModel = null, \int $rimModelId = 0, Price $price = null, \int $priceId = 0){
        $this->setId($id);
        $this->setName($name);
        $this->setEquipmentsArray($equipmentsArray);
        $this->setEquipmentsIdsArray($equipmentsIdsArray);
        $this->setColor(is_a($color, '\Vehicle\ExternalColor') ? $color : new ExternalColor());
        $this->setColorId($colorId);
        $this->setRimModel(is_a($rimModel, '\Vehicle\RimModel') ? $rimModel : new RimModel());
        $this->setRimModelId($rimModelId);
        $this->setPrice(is_a($price, '\Prices\Price') ? $price : new Price());
        $this->setPriceId($priceId);
    }
    /*******************CONSTRUCTOR*****************/
}