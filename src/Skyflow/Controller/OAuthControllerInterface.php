<?php

/**
 * Interface for OAuth controllers that handle authentication actions.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Controller;

/**
 * Interface for OAuth controllers that handle authentication actions.
 */
interface OAuthControllerInterface
{
    /**
     * Authenticate against the OAuth2 application.
     */
    public function authenticateAction();

    /**
     * Handle authentication callback.
     */
    public function callbackAction();
}
