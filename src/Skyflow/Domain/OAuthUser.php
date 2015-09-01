<?php

/**
 * Class for an OAuth User.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Domain;

use skyflow\Domain\AbstractModel;

/**
 * Class for an OAuth User.
 */
class OAuthUser extends AbstractModel
{
    /**
     * OAuth application client_id used by the user.
     *
     * @var string
     */
    protected $clientId;

    /**
     * OAuth application client_secret used by the user.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * User access_token to authenticate to OAuth application.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * User refresh_token to request a new access_token.
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'user';
    }

    /**
     * Get the OAuth user client_id.
     *
     * @return string The OAuth user client_id.
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set the OAuth user client_id.
     *
     * @param string $clientId The OAuth user client_id.
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * Get the OAuth user client_secret.
     *
     * @return string The OAuth user client_secret.
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Set the OAuth user client_secret.
     *
     * @param string $clientSecret The OAuth user client_secret.
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get the addon user access_token.
     *
     * @return string The addon user access_token.
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the OAuth user access_token.
     *
     * @param string $accessToken The OAuth user access_token.
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Get the OAuth user refresh_token.
     *
     * @return string The OAuth user refresh_token.
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Set the OAuth user refresh_token.
     *
     * @param string $refreshToken The OAuth user refresh_token.
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }
}
