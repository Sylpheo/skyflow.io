<?php

namespace Skyflow\Domain;

class Wave_request{

    /**
     * @var id
     */
    private $id;

    /**
     * @var request
     */
    private $request;

    /**
     * @var id_user
     */
    private $id_user;
    public function getId()
    {
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getRequest(){
        return $this->request;
    }

    public function setRequest($request){
        $this->request = $request;
    }

    public function getIdUser(){
        return $this->id_user;
    }

    public function setIdUser($id_user){
        $this->id_user = $id_user;
    }
}