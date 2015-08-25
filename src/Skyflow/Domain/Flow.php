<?php

/**
 * Flow Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Domain;

/**
 * Flow Domain Object class.
 */
class Flow {
    /**
     * Flow id
     * @var integer
     */
    private $id;

    /**
     * Flow name
     * @var varchar
     */
    private $name;

    /**
     * Flow class
     * @var varchar
     */
    private $class;

    /**
     * Flow documentation
     * @var varchar
     */
    private $documentation;

    private $idUser;



    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getClass(){
        return $this->class;
    }

    public function setClass($class){
        $this->class = $class;
    }

    public function getDocumentation(){
        return $this->documentation;
    }

    public function setDocumentation($documentation){
        $this->documentation = $documentation;
    }

    public function getIdUser(){
        return $this->idUser;
    }

    public function setIdUser($idUser){
        $this->idUser = $idUser;
    }
}