<?php
class FileToUpload{
    private $pathOnServer, $name, $type, $tmp_name, $error, $size;

    /*********************SETTERS & GETTERS******************************/
    /**
     * @param string $pathOnServer
     */
    public function setPathOnServer(\string $pathOnServer){
        $this->pathOnServer = (string)$pathOnServer;
    }

    /**
     * @return string
     */
    public function getPathOnServer():\string{
        return $this->pathOnServer;
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
     * @param string $type
     */
    public function setType(\string $type){
        $this->type = (string)$type;
    }

    /**
     * @return string
     */
    public function getType():\string{
        return $this->type;
    }

    /**
     * @param string $tmp_name
     */
    public function setTmp_name(\string $tmp_name){
        $this->tmp_name = (string)$tmp_name;
    }

    /**
     * @return string
     */
    public function getTmp_name():\string{
        return $this->tmp_name;
    }

    /**
     * @param string $error
     */
    public function setError(\string $error){
        $this->error = (string)$error;
    }

    /**
     * @return string
     */
    public function getError():\string{
        return $this->error;
    }

    /**
     * @param int $size
     */
    public function setSize(\int $size){
        $this->size = (int)$size;
    }

    /**
     * @return int
     */
    public function getSize():\int{
        return $this->size;
    }
    /*********************SETTERS & GETTERS******************************/

    /*********************CONSTRUCTEUR******************************/
    /**
     * FileToUpload constructor.
     *
     * @param string $pathOnServer
     * @param string $name
     * @param string $type
     * @param string $tmp_name
     * @param string $error
     * @param int    $size
     */
    public function __construct(\string $pathOnServer = '', \string $name = '', \string $type = '', \string $tmp_name = '',
                                \string $error = '', \int $size = 0){
        $this->setPathOnServer($pathOnServer);
        $this->setName($name);
        $this->setType($type);
        $this->setTmp_name($tmp_name);
        $this->setError($error);
        $this->setSize($size);
    }
    /*********************CONSTRUCTEUR******************************/
}