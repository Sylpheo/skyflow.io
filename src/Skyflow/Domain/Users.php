<?php

/**
 * Users Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Users Domain Object class.
 */
class Users implements UserInterface {
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

    /**
     * @var access_token
     */
    private $access_token_salesforce;

    /**
     * @var refresh_token
     */
    private $refresh_token_salesforce;

    /**
     * @var instance_url
     */
    private $instance_url_salesforce;

    private $salesforce_id;

    private $salesforce_secret;


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

    public function getAccessTokenSalesforce(){
        return $this->access_token_salesforce;
    }

    public function setAccessTokenSalesforce($access_token_salesforce){
        $this->access_token_salesforce = $access_token_salesforce;
    }

    public function getRefreshTokenSalesforce(){
        return $this->refresh_token_salesforce;
    }

    public function setRefreshTokenSalesforce($refresh_token_salesforce){
        $this->refresh_token_salesforce = $refresh_token_salesforce;
    }

    public function getInstanceUrlSalesforce(){
        return $this->instance_url_salesforce;
    }

    public function setInstanceUrlSalesforce($instance_url_salesforce){
        $this->instance_url_salesforce = $instance_url_salesforce;
    }

    public function setSalesforceId($salesforce_id){
        $this->salesforce_id = $salesforce_id;
    }

    public function getSalesforceId(){
        return $this->salesforce_id;
    }

    public function getSalesforceSecret(){
        return $this->salesforce_secret;
    }

    public function setSalesforceSecret($salesforce_secret){
        $this->salesforce_secret = $salesforce_secret;
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