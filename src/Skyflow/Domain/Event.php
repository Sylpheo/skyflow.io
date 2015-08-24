<?php

namespace Skyflow\Domain;

class Event 
{
    /**
     * Event id
     * @var integer
     */
    private $id;

    /**
     * Event name
     * @var string
     */
    private $name;

    /**
     * Event flow.
     * @var string
     */
    private $description;

    /**
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

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getIdUsers(){
        return $this->idUsers;
    }

    public function setIdUsers($idUsers){
        $this->idUsers = $idUsers;
    }
}