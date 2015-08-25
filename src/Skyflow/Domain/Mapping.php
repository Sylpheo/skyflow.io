<?php

/**
 * Mapping Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Domain;

/**
 * Mapping Domain Object class.
 */
class Mapping {
    /**
     * Flow id
     * @var integer
     */
    private $id;

    /**
     * @var Event event
     */
    private $event;

    /**
     *
     * @var Flow flow
     */
    private $flow;

    /**
     * Mapping idUser
     * @var
     */
    private $idUser;



    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getEvent(){
        return $this->event;
    }

    public function setEvent(Event $event){
        $this->event =$event;
    }

    public function getFlow(){
        return $this->flow;
    }

    public function setFlow(Flow $flow){
        $this->flow = $flow;
    }
    public function getIdUser(){
        return $this->idUser;
    }

    public function setIdUser($idUser){
        $this->idUser = $idUser;
    }
}