<?php

namespace exactSilex\Domain;

class Event 
{
    /**
     * Article id.
     *
     * @var integer
     */
    private $id;

    /**
     * Event event.
     *
     * @var string
     */
    private $event;

    /**
     * Event triggerSend.
     *
     * @var string
     */
    private $triggerSend;

    /**
    *
    * Event id_user
    * Users
    */
    private $idUsers;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEvent() {
        return $this->event;
    }

    public function setEvent($event) {
        $this->event = $event;
    }

    public function getTriggerSend() {
        return $this->triggerSend;
    }

    public function setTriggerSend($triggerSend) {
        $this->triggerSend = $triggerSend;
    }

    public function getIdUsers(){
        return $this->idUsers;
    }

    public function setIdUsers($idUsers){
        $this->idUsers = $idUsers;
    }
}