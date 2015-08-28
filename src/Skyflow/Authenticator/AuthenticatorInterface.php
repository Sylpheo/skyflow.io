<?php

/**
 * Interface for Authenticators.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Authenticator;

/**
 * Inteface for Authenticators.
 */
interface AuthenticatorInterface
{
    public function authenticate();
    public function callback();
    public function refresh();
}
