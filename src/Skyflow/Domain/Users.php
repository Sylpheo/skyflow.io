<?php

/**
 * Users Domain Object class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Domain;

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
     * skyflow token (auto generate)
     * @var string
     */
    private $skyflowtoken;

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
     * Wave connected application client id.
     *
     * @var string
     */
    private $waveClientId;

    /**
     * Wave connected application client secret.
     *
     * @var string
     */
    private $waveClientSecret;

    /**
     * Wave is sandbox.
     *
     * @var boolean
     */
    private $waveSandbox;

    /**
     * Wave access token.
     *
     * @var string
     */
    private $waveAccessToken;

    /**
     * Wave refresh token.
     *
     * @var string
     */
    private $waveRefreshToken;

    /**
     * Wave Salesforce instance url.
     *
     * @var string
     */
    private $waveInstanceUrl;

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

    /**
     * True if using a Salesforce sandbox.
     * False if it is a production.
     *
     * @var boolean
     */
    private $salesforce_sandbox;

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

    public function getSkyflowtoken() {
        return $this->skyflowtoken;
    }

    public function setSkyflowtoken($skyflowtoken) {
        $this->skyflowtoken = $skyflowtoken;
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

    /**
     * Get Wave connected application client id.
     *
     * @return string Wave client id.
     */
    public function getWaveClientId() {
        return $this->waveClientId;
    }

    /**
     * Set Wave connected application client id.
     *
     * @param string $waveClientId Wave client id.
     */
    public function setWaveClientId($waveClientId) {
        $this->waveClientId = $waveClientId;
    }

    /**
     * Get Wave connected application client secret.
     *
     * @return string Wave client secret.
     */
    public function getWaveClientSecret() {
        return $this->waveClientSecret;
    }

    /**
     * Set Wave connected application client secret.
     *
     * @param string $waveClientSecret The Wave client secret.
     */
    public function setWaveClientSecret($waveClientSecret) {
        $this->waveClientSecret = $waveClientSecret;
    }

    /**
     * Get if wave is on a Salesforce sandbox.
     *
     * @return boolean True if wave is on a Salesforce sandbox or false.
     */
    public function getWaveSandbox() {
        return $this->waveSandbox;
    }

    /**
     * Set if wave is on a Salesforce sandbox.
     *
     * @param boolean $waveSandbox Boolean indicating if Wave
     *                             is on a Salesforce sandbox.
     */
    public function setWaveSandbox($waveSandbox) {
        $this->waveSandbox = $waveSandbox;
    }

    /**
     * Get wave access token.
     *
     * @return string Wave access token.
     */
    public function getWaveAccessToken() {
        return $this->waveAccessToken;
    }

    /**
     * Set wave access token.
     *
     * @param string $waveAccessToken Wave access token.
     */
    public function setWaveAccessToken($waveAccessToken) {
        $this->waveAccessToken = $waveAccessToken;
    }

    /**
     * Get wave refresh token.
     *
     * @return string Wave refresh token.
     */
    public function getWaveRefreshToken() {
        return $this->waveRefreshToken;
    }

    /**
     * Set wave refresh token.
     *
     * @param string $waveRefreshToken Wave refresh token.
     */
    public function setWaveRefreshToken($waveRefreshToken) {
        $this->waveRefreshToken = $waveRefreshToken;
    }

    /**
     * Get wave instance url.
     *
     * @return string Wave instance url.
     */
    public function getWaveInstanceUrl() {
        return $this->waveInstanceUrl;
    }

    /**
     * Set wave instance url.
     *
     * @param string $waveInstanceUrl Wave instance url.
     */
    public function setWaveInstanceUrl($waveInstanceUrl) {
        $this->waveInstanceUrl = $waveInstanceUrl;
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
     * Set if connected Salesforce is a sandbox.
     *
     * @param boolean $salesforce_sandbox True if using a sandbox, or false.
     */
    public function setSalesforceSandbox($salesforce_sandbox) {
        $this->salesforce_sandbox = $salesforce_sandbox;
    }

    /**
     * Get if connected Salesforce is a sandbox.
     *
     * @return boolean True if using a sandbox, or false.
     */
    public function getSalesforceSandbox() {
        return $this->salesforce_sandbox;
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