<?php

/**
 * Class for an authenticated REST service.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use skyflow\Domain\OAuthUser;
use skyflow\Service\RestService;
use skyflow\OAuthAuthenticatedTrait;

class RestOAuthAuthenticatedService extends RestService
{
    use OAuthAuthenticatedTrait;

    /**
     * Rest service constructor.
     *
     * @param HttpClientInterface   $httpClient  An HTTP Client.
     * @param OAuthUser             $user        The OAuth user.
     * @param OAuthServiceInterface $authService The OAuth authentication service.
     */
    public function __construct(
        HttpClientInterface $httpClient,
        OAuthUser $user,
        OAuthServiceInterface $authService
    ) {
        parent::__construct($httpClient);

        // used from OAuthAuthenticatedTrait
        $this->setUser($user);
        $this->setAuthService($authService);
    }
}
