<?php

/**
 * Service for Wave authentication.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Service;

use skyflow\Authenticator\AuthenticatorInterface;
use skyflow\DAO\UsersDAO;
use skyflow\Domain\Users;

/**
 * Service for Wave authentication.
 */
class AuthService
{
    /**
     * The Wave authenticator.
     *
     * @var AuthenticatorInterface
     */
    protected $authenticator;

    /**
     * The logged-in skyflow user.
     *
     * @var User
     */
    protected $user;

    /**
     * The DAO object for User.
     *
     * @var UserDAO
     */
    protected $userDAO;

    /**
     * Wave AuthService constructor.
     *
     * @param AuthenticatorInterface $authenticator The Wave authenticator.
     * @param Users                  $user          The current logged-in user.
     * @param UsersDAO               $userDAO       The DAO object for User.
     */
    public function __construct(
        AuthenticatorInterface $authenticator,
        Users $user,
        UsersDAO $userDAO
    ) {
        $this->authenticator = $authenticator;
        $this->user = $user;
        $this->userDAO = $userDAO;
    }

    /**
     * Authenticate to Wave.
     *
     * Here we redirect to the Salesforce login page
     * for the user to authenticate to Salesforce.
     *
     * Then Salesforce redirects the user to the callback url.
     */
    public function authenticate()
    {
        $this->authenticator->clientId = $this->user->getWaveClientId();
        $this->authenticator->clientSecret = $this->user->getWaveClientSecret();
        $this->authenticator->loginUrl = $this->user->getWaveSandbox() ? 'https://test.salesforce.com' : 'https://login.salesforce.com';
        $this->authenticator->authenticate();
    }

    /**
     * Do the Wave OAuth2 callback process.
     *
     * We use the "code" parameter provided by Salesforce
     * to request an access token and refresh token. These
     * tokens are then saved to the current logged-in user.
     *
     * @param string $code The authorization code provided by Salesforce.
     */
    public function callback($code)
    {
        $this->authenticator->code = $code;
        $this->authenticator->callback();

        $this->user->setWaveInstanceUrl($this->authenticator->instance_url);
        $this->user->setWaveAccessToken($this->authenticator->access_token);
        $this->user->setWaveRefreshToken($this->authenticator->refresh_token);
        $this->userDAO->save($this->user);
    }

    /**
     * Request and save a new access_token.
     *
     * Use the current user Wave refresh token to request
     * a new access token from Salesforce.
     */
    public function refresh()
    {
        $this->authenticator->refresh();
    }
}
