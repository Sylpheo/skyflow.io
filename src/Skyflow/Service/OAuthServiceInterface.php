<?php

/**
 * Authentication Service Interface for use by addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Service;

interface OAuthServiceInterface
{
    /**
     * Authenticate to an OAuth2 application.
     *
     * Here we redirect to the OAuth2 application login page
     * for the user to authenticate to the application.
     *
     * Then the application redirects the user to the callback url.
     */
    public function authenticate();

    /**
     * Do the OAuth2 callback process.
     *
     * We use the "code" parameter provided by the OAuth2 application
     * to request an access token and refresh token.
     *
     * @param string $code The authorization code provided by the OAuth2 application.
     */
    public function callback($code);

    /**
     * Request and save a new access_token.
     *
     * Use the current user refresh token to request
     * a new access token from the OAuth2 application.
     */
    public function refresh();
}
