<?php

namespace skyflow\Domain;

class Association
{
    /**
     * Flow id
     * @var integer
     */
    private $id;

    /**
     * Association idEvent
     * @var
     */
    private $idEvent;

    /**
     * Association idFlow
     * @var
     */
    private $idFlow;

    /**
     * Association idUser
     * @var
     */
    private $idUser;



    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

   public function getIdEvent(){
       return $this->idEvent;
   }

    public function setIdEvent($idEvent){
        $this->idEvent = $idEvent;
    }

    public function getIdFlow(){
        return $this->idFlow;
    }

    public function setIdFlow($idFlow){
        $this->idFlow = $idFlow;
    }
    public function getIdUser(){
        return $this->idUser;
    }

    public function setIdUser($idUser){
        $this->idUser = $idUser;
    }
}