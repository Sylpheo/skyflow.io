<?php

/**
 * Wave Request Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Domain;

/**
 * Wave Request Domain Object class.
 */
class WaveRequest
{
    /**
     * This id of the request.
     *
     * @var integer
     */
    private $id;

    /**
     * The request string.
     *
     * @var string
     */
    private $request;

    /**
     * The id of the User who owns the request.
     *
     * @var integer
     */
    private $userId;

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

    public function getUserId(){
        return $this->userId;
    }

    public function setUserId($userId){
        $this->userId = $userId;
    }
}
