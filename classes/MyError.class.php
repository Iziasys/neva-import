<?php
class MyError{
    private $publicMessage, $errorMessage;

    /*******************SETTERS & GETTERS*****************/
    /**
     * @param string $publicMessage
     */
    public function setPublicMessage(string $publicMessage):void{
        $this->publicMessage = (string)$publicMessage;
    }

    /**
     * @return string
     */
    public function getPublicMessage():string{
        return $this->publicMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage):void{
        $this->errorMessage = (string)$errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorMessage():string{
        return $this->errorMessage;
    }
    /*******************SETTERS & GETTERS*****************/

    /*******************CONSTRUCTOR*****************/
    /**
     * MyError constructor.
     * @param string $publicMessage
     * @param string $errorMessage
     */
    public function __construct(string $publicMessage = '', string $errorMessage = ''){
        $this->setPublicMessage($publicMessage);
        $this->setErrorMessage($errorMessage);
    }
    /*******************CONSTRUCTOR*****************/

    /**
     * @return string Message d'erreur adapté au mode de l'application
     */
    public function getError():string{
        if($GLOBALS['__SITE_MODE'] == 'development'){
            return $this->getErrorMessage();
        }
        else{
            return $this->getPublicMessage();
        }
    }
}