<?php
/**
 * Created by PhpStorm.
 * User: DECOCK Stéphane
 * Date: 19/01/2016
 * Time: 08:48
 */

namespace Vehicle;


use Prices\Dealer;

class Finish
{
    private $id, $name, $model, $modelId, $dealer, $dealerId, $active, $serialEquipmentsArray, $serialEquipmentsIdsArray,
        $optionalEquipmentsArray, $optionalEquipmentsIdsArray, $packsArray, $packsIdsArray, $serialRimModel,
        $serialRimModelId, $optionalRimsArray, $optionalRimsIdsArray, $serialColor, $serialColorId,
        $optionalColorsArray, $optionalColorsIdsArray;

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
     * @param Model $model
     */
    public function setModel(Model $model){
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel():Model{
        return $this->model;
    }

    /**
     * @param int $modelId
     */
    public function setModelId(\int $modelId){
        $this->modelId = (int)$modelId;
    }

    /**
     * @return int
     */
    public function getModelId():\int{
        return $this->modelId;
    }

    /**
     * @param Dealer $dealer
     */
    public function setDealer(Dealer $dealer){
        $this->dealer = $dealer;
    }

    /**
     * @return Dealer
     */
    public function getDealer():Dealer{
        return $this->dealer;
    }

    /**
     * @param int $dealerId
     */
    public function setDealerId(\int $dealerId){
        $this->dealerId = (int)$dealerId;
    }

    /**
     * @return int
     */
    public function getDealerId():\int{
        return $this->dealerId;
    }

    /**
     * @param bool $active
     */
    public function setActive(\bool $active){
        $this->active = (bool)$active;
    }

    /**
     * @return bool
     */
    public function getActive():\bool{
        return $this->active;
    }

    /**
     * @param Equipment[] $serialEquipmentsArray
     */
    public function setSerialEquipmentsArray(array $serialEquipmentsArray){
        foreach($serialEquipmentsArray as $item){
            if(!is_a($item, '\Vehicle\Equipment')){
                continue;
            }
            else{
                $this->serialEquipmentsArray[] = $item;
            }
        }
    }

    /**
     * @return Equipment[]
     */
    public function getSerialEquipmentsArray():array{
        return $this->serialEquipmentsArray;
    }

    /**
     * @param int[] $serialEquipmentsIdsArray
     */
    public function setSerialEquipmentsIdsArray(array $serialEquipmentsIdsArray){
        foreach($serialEquipmentsIdsArray as $item){
            $this->serialEquipmentsIdsArray[] = (int)$item;
        }
    }

    /**
     * @return int
     */
    public function getSerialEquipmentsIdsArray():array{
        return $this->serialEquipmentsIdsArray;
    }

    /**
     * @param OptionalEquipment[] $optionalEquipmentsArray
     */
    public function setOptionalEquipmentsArray(array $optionalEquipmentsArray){
        foreach($optionalEquipmentsArray as $item){
            if(!is_a($item, '\Vehicle\OptionalEquipment')){
                continue;
            }
            else{
                $this->optionalEquipmentsArray[] = $item;
            }
        }
    }

    /**
     * @return OptionalEquipment[]
     */
    public function getOptionalEquipmentsArray():array{
        return $this->optionalEquipmentsArray;
    }

    /**
     * @param int[] $optionalEquipmentsIdsArray
     */
    public function setOptionalEquipmentsIdsArray(array $optionalEquipmentsIdsArray){
        foreach($optionalEquipmentsIdsArray as $item){
            $this->optionalEquipmentsIdsArray[] = (int)$item;
        }
    }

    /**
     * @return int[]
     */
    public function getOptionalEquipmentsIdsArray():array{
        return $this->optionalEquipmentsIdsArray;
    }

    /**
     * @param Pack[] $packsArray
     */
    public function setPacksArray(array $packsArray){
        foreach($packsArray as $item){
            if(!is_a($item, '\Vehicle\Pack')){
                continue;
            }
            else{
                $this->packsArray[] = $item;
            }
        }
    }

    /**
     * @return Pack[]
     */
    public function getPacksArray():array{
        return $this->packsArray;
    }

    /**
     * @param int[] $packsIdsArray
     */
    public function setPacksIdsArray(array $packsIdsArray){
        foreach($packsIdsArray as $item){
            $this->packsIdsArray[] = (int)$item;
        }
    }

    /**
     * @return int[]
     */
    public function getPacksIdsArray():array{
        return $this->packsIdsArray;
    }

    /**
     * @param RimModel $serialRimModel
     */
    public function setSerialRimModel(RimModel $serialRimModel){
        $this->serialRimModel = $serialRimModel;
    }

    /**
     * @return RimModel
     */
    public function getSerialRimModel():RimModel{
        return $this->serialRimModel;
    }

    /**
     * @param int $serialRimModelId
     */
    public function setSerialRimModelId(\int $serialRimModelId){
        $this->serialRimModelId = (int)$serialRimModelId;
    }

    /**
     * @return int
     */
    public function getSerialRimModelId():\int{
        return $this->serialRimModelId;
    }

    /**
     * @param OptionalRim[] $optionalRimsArray
     */
    public function setOptionalRimsArray(array $optionalRimsArray){
        foreach($optionalRimsArray as $item){
            if(!is_a($item, '\Vehicle\OptionalRim')){
                continue;
            }
            else{
                $this->optionalRimsArray[] = $item;
            }
        }
    }

    /**
     * @return OptionalRim[]
     */
    public function getOptionalRimsArray():array{
        return $this->optionalRimsArray;
    }

    /**
     * @param int[] $optionalRimsIdsArray
     */
    public function setOptionalRimsIdsArray(array $optionalRimsIdsArray){
        foreach($optionalRimsIdsArray as $item){
            $this->optionalRimsIdsArray[] = (int)$item;
        }
    }

    /**
     * @return int[]
     */
    public function getOptionalRimsIdsArray():array{
        return $this->optionalRimsIdsArray;
    }

    /**
     * @param ExternalColor $serialColor
     */
    public function setSerialColor(ExternalColor $serialColor){
        $this->serialColor = $serialColor;
    }

    /**
     * @return ExternalColor
     */
    public function getSerialColor():ExternalColor{
        return $this->serialColor;
    }

    /**
     * @param int $serialColorId
     */
    public function setSerialColorId(\int $serialColorId){
        $this->serialColorId = (int)$serialColorId;
    }

    /**
     * @return int
     */
    public function getSerialColorId():\int{
        return $this->serialColorId;
    }

    /**
     * @param OptionalColor[] $optionalColorsArray
     */
    public function setOptionalColorsArray(array $optionalColorsArray){
        foreach($optionalColorsArray as $item){
            if(!is_a($item, '\Vehicle\OptionalColor')){
                continue;
            }
            else{
                $this->optionalColorsArray[] = $item;
            }
        }
    }

    /**
     * @return OptionalColor[]
     */
    public function getOptionalColorsArray():array{
        return $this->optionalColorsArray;
    }

    /**
     * @param int[] $optionalColorsIdsArray
     */
    public function setOptionalColorsIdsArray(array $optionalColorsIdsArray){
        foreach($optionalColorsIdsArray as $item){
            $this->optionalColorsIdsArray[] = (int)$item;
        }
    }

    /**
     * @return int[]
     */
    public function getOptionalColorsIdsArray():array{
        return $this->optionalColorsIdsArray;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * Finish constructor.
     *
     * @param int                 $id
     * @param string              $name
     * @param Model|null          $model
     * @param int                 $modelId
     * @param Dealer|null         $dealer
     * @param int                 $dealerId
     * @param bool                $active
     * @param Equipment[]         $serialEquipmentsArray
     * @param int[]               $serialEquipmentsIdsArray
     * @param OptionalEquipment[] $optionalEquipmentsArray
     * @param int[]               $optionalEquipmentsIdsArray
     * @param Pack[]              $packsArray
     * @param int[]               $packsIdsArray
     * @param RimModel|null       $serialRimModel
     * @param int                 $serialRimModelId
     * @param OptionalRim[]       $optionalRimsArray
     * @param int[]               $optionalRimsIdsArray
     * @param ExternalColor|null  $serialColor
     * @param int                 $serialColorId
     * @param OptionalColor[]     $optionalColorsArray
     * @param int[]               $optionalColorsIdsArray
     */
    public function __construct(\int $id= 0, \string $name = '', Model $model = null, \int $modelId = 0, Dealer $dealer = null,
                                \int $dealerId = 0, \bool $active = false,
                                array $serialEquipmentsArray = [], array $serialEquipmentsIdsArray = [], array $optionalEquipmentsArray = [],
                                array $optionalEquipmentsIdsArray = [], array $packsArray = [], array $packsIdsArray = [],
                                RimModel $serialRimModel = null, \int $serialRimModelId = 0, array $optionalRimsArray = [],
                                array $optionalRimsIdsArray = [], ExternalColor $serialColor = null, \int $serialColorId = 0,
                                array $optionalColorsArray = [], array $optionalColorsIdsArray = []){
        $this->setId($id);
        $this->setName($name);
        $this->setModel(is_a($model, '\Vehicle\Model') ? $model : new Model());
        $this->setModelId($modelId);
        $this->setDealer(is_a($dealer, '\Prices\Dealer') ? $dealer : new Dealer());
        $this->setDealerId($dealerId);
        $this->setActive($active);
        $this->setSerialEquipmentsArray($serialEquipmentsArray);
        $this->setSerialEquipmentsIdsArray($serialEquipmentsIdsArray);
        $this->setOptionalEquipmentsArray($optionalEquipmentsArray);
        $this->setOptionalEquipmentsIdsArray($optionalEquipmentsIdsArray);
        $this->setPacksArray($packsArray);
        $this->setPacksIdsArray($packsIdsArray);
        $this->setSerialRimModel(is_a($serialRimModel, '\Vehicle\RimModel') ? $serialRimModel : new RimModel());
        $this->setSerialRimModelId($serialRimModelId);
        $this->setOptionalRimsArray($optionalRimsArray);
        $this->setOptionalRimsIdsArray($optionalRimsIdsArray);
        $this->setSerialColor(is_a($serialColor, '\Vehicle\ExternalColor') ? $serialColor : new ExternalColor());
        $this->setSerialColorId($serialColorId);
        $this->setOptionalColorsArray($optionalColorsArray);
        $this->setOptionalColorsIdsArray($optionalColorsIdsArray);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * Désactive la finition
     */
    public function disableFinish(){
        $this->setActive(false);
    }

    /**
     * Active la finition
     */
    public function enableFinish(){
        $this->setActive(true);
    }
}