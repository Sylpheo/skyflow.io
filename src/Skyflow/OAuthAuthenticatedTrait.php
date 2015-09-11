<?php

/**
 * Trait for classes that need to be authenticated to an OAuth provider.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow;

use Skyflow\Domain\OAuthUser;
use Skyflow\Service\OAuthServiceInterface;

trait OAuthAuthenticatedTrait
{
    /**
     * The OAuth user.
     *
     * @var OAuthUser
     */
    private $user;

    /**
     * The OAuth authentication service in case we need to refresh the access_token.
     *
     * @var OAuthServiceInterface
     */
    private $authService;

    /**
     * Set the OAuth user.
     *
     * This method is private because we don't want child classes to change the
     * OAuth user.
     *
     * @param OAuthUser $user The OAuth user.
     */
    private function setUser($user)
    {
        $this->user = $user;
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
     * Set the OAuth authentication service.
     *
     * This method is private because we don't want child classes to change the
     * OAuth authentication service.
     *
     * @param OAuthServiceInterface $authService The OAuth authentication service.
     */
    private function setAuthService($authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get the OAuth authentication service.
     *
     * @return OAuthServiceInterface The OAuth authentication service.
     */
    protected function getAuthService()
    {
        return $this->authService;
    }
}
