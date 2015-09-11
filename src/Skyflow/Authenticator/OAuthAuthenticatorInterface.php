<?php

/**
 * Interface for an OAuth authenticator.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Authenticator;

/**
 * Inteface for an OAuth authenticator.
 */
interface OAuthAuthenticatorInterface
{
    /**
     * Authenticate to the OAuth application.
     *
     * @param boolean $exit If using web server authentication flow, true to make
     *                      the application exit right now (for the redirect to happen).
     *                      False if you don't want to exit now. Defaults to true.
     */
    public function authenticate($exit);

    /**
     * Handle OAuth callback.
     *
     * When using web server authentication flow, after a successful
     * authenticate(), you have to set the $code and call this method to
     * request an OAuth access_token, refresh_token and such.
     */
    public function callback();

    /**
     * Refresh the OAuth access_token using the refresh_token.
     */
    public function refresh();
}
