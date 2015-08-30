<?php

/**
 * OAuth authentication service for use by the Skyflow addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

use skyflow\Authenticator\OAuthAuthenticatorInterface;
use skyflow\Domain\OAuthUser;
use skyflow\DAO\OAuthUserDAO;
use skyflow\Service\OAuthServiceInterface;

/**
 * OAuth authentication service for use by the Skyflow addons.
 *
 * This is used by the OAuth controllers. This class is responsible for storing
 * the OAuth tokens in the OAuth user after successful authentication via
 * the OAuth authenticator.
 *
 * This class is not abstract because it can be used "as is" for OAuth providers
 * that just requires a client_id and a client_secret to authenticate a user, an
 * just return an access_token and refresh_token.
 *
 * Note that this class is not suitable for the Salesforce provider, which needs
 * to know if we are authenticating to a sandbox or a production organization. Also
 * Salesforce authenticate response includes an instance_url which is not handled
 * here.
 */
class OAuthService implements OAuthServiceInterface
{
    /**
     * The OAuth authenticator.
     *
     * @var OAuthAuthenticatorInterface
     */
    private $oauth;
    
    /**
     * The OAuth user.
     *
     * @var OAuthUser
     */
    private $user;

    /**
     * The OAuth user DAO object.
     *
     * @var OAuthUserDAO
     */
    private $userDAO;

    /**
     * OAuth authentication service constructor.
     *
     * @param OAuthAuthenticatorInterface $oauth   The OAuth authenticator.
     * @param OAuthUser                   $user    The OAuth user.
     * @param OAuthUserDAO                $userDAO The DAO object for the user.
     */
    public function __construct(
        OAuthAuthenticatorInterface $oauth,
        OAuthUser $user,
        OAuthUserDAO $userDAO
    ) {
        $this->oauth = $oauth;
        $this->user = $user;
        $this->userDAO = $userDAO;
    }

    /**
     * Get the OAuth authenticator.
     *
     * @return OAuthAuthenticator The OAuth authenticator.
     */
    protected function getOAuth()
    {
        return $this->oauth;
    }

    /**
     * Get the OAuth user.
     *
     * @return OAuthUser The OAuth user.
     */
    protected function getUser()
    {
        return $this->user;
    }

    /**
     * Get the OAuth user DAO object.
     *
     * @return OAuthUserDAO The OAuth user DAO object.
     */
    protected function getUserDAO()
    {
        return $this->userDAO;
    }

    /**
     * {@inherit}
     */
    public function authenticate()
    {
        $this->getOAuth()->clientId = $this->getUser()->getClientId();
        $this->getOAuth()->clientSecret = $this->getUser()->getClientSecret();
        $this->getOAuth()->authenticate();
    }

    /**
     * {@inheritdoc}
     */
    public function callback($code)
    {
        $this->getOAuth()->code = $code;
        $this->getOAuth()->callback();

        $this->getUser()->setAccessToken($this->getOAuth()->access_token);
        $this->getUser()->setRefreshToken($this->getOAuth()->refresh_token);
        $this->getUserDAO()->save($this->getUser());
    }

    /**
     * {@inheritdoc}
     */
    public function refresh()
    {
        $this->getOAuth()->refresh();
        $this->getUser()->setAccessToken($this->getOAuth()->access_token);
        $this->getUserDAO()->save($this->getUser());
    }
}
