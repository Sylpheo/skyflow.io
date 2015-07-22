<?php

namespace skyflow\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

class Users implements UserInterface
{
    /**
     * User id.
     * @var integer
     */
    private $id;

    /**
     * User name.
     * @var string
     */
    private $username;

    /**
     * User password.
     * @var string
     */
    private $password;

    /**
     * Salt that was originally used to encode the password.
     * @var string
     */
    private $salt;

    /**
     * Role.
     * Values : ROLE_USER or ROLE_ADMIN.
     * @var string
     */
    private $role;

    /**
     * ExactTarget clientid
     * @var string
     */
    private $clientid;

    /**
     * ExactTarget clientsecret
     * @var string
     */
    private $clientsecret;

    /**
     * skyflow token (auto generate)
     * @var string
     */
    private $skyflowtoken;

     /**
     * Wve client id
     * @var string
     */
    private $waveid;

     /**
     * Wave clientsecret
     *
     * @var string
     */
    private $wavesecret;

    /**
     * Wave login
     * @var string
     */
    private $wavelogin;

     /**
     * Wave password
     * @var string
     */
    private $wavepassword;


    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }

        public function getClientid() {
        return $this->clientid;
    }

    public function setClientid($clientid) {
        $this->clientid = $clientid;
    }


    public function getClientsecret() {
        return $this->clientsecret;
    }

    public function setClientsecret($clientsecret) {
        $this->clientsecret = $clientsecret;
    }

    public function getSkyflowtoken() {
        return $this->skyflowtoken;
    }

    public function setSkyflowtoken($skyflowtoken) {
        $this->skyflowtoken = $skyflowtoken;
    }

    public function getWaveid() {
        return $this->waveid;
    }

    public function setWaveid($waveid) {
        $this->waveid = $waveid;
    }

    public function getWavesecret() {
        return $this->wavesecret;
    }

    public function setWavesecret($wavesecret) {
        $this->wavesecret = $wavesecret;
    }

    public function getWavelogin() {
        return $this->wavelogin;
    }

    public function setWavelogin($wavelogin) {
        $this->wavelogin = $wavelogin;
    }

       public function getWavepassword() {
        return $this->wavepassword;
    }

    public function setWavepassword($wavepassword) {
        $this->wavepassword = $wavepassword;
    }


    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->getRole());
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        // Nothing to do here
    }
}