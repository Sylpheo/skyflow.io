<?php

/**
 * Class for an authenticated REST service.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use skyflow\Domain\OAuthUser;
use skyflow\OAuthAuthenticatedTrait;
use skyflow\Service\RestService;
use skyflow\Service\ServiceInterface;

class RestOAuthAuthenticatedService extends RestService
{
    use OAuthAuthenticatedTrait;

    /**
     * Rest service constructor.
     *
     * @param ServiceInterface      $parentService The parent service.
     * @param array                 $config        The service configuration.
     * @param HttpClientInterface   $httpClient    An HTTP Client.
     * @param OAuthUser             $user          The OAuth user.
     * @param OAuthServiceInterface $authService   The OAuth authentication service.
     */
    public function __construct(
        $parentService,
        $config,
        HttpClientInterface $httpClient,
        OAuthUser $user,
        OAuthServiceInterface $authService
    ) {
        parent::__construct($parentService, $config, $httpClient);

        // used from OAuthAuthenticatedTrait
        $this->setUser($user);
        $this->setAuthService($authService);
    }

    /**
     * {@inheritdoc} Access token is automatically refreshed if it has expired.
     */
    public function httpGet($url, $parameters, $headers = null)
    {
        try {
            return parent::httpGet($url, $parameters, $headers);
        } catch (\Exception $ex) {
            if ($ex->getCode() === 401) {
                if (isset($headers[0]['Authorization'])) {
                    $type = explode(' ', $headers['Authorization'], 2)[0];
                    $this->getAuthService()->refresh();
                    $headers['Authorization'] = $type
                        . ' '
                        . $this->getUser()->getAccessToken();
                    return parent::httpGet($url, $parameters, $headers);
                } else {
                    throw $ex;
                }
            }
        }
    }

    /**
     * {@inheritdoc} Access token is automatically refreshed if it has expired.
     */
    public function httpPost($url, $parameters, $headers = null)
    {
        try {
            return parent::httpPost($url, $parameters, $headers);
        } catch (\Exception $ex) {
            if ($ex->getCode() === 401) {
                if (isset($headers[0]['Authorization'])) {
                    $type = explode(' ', $headers['Authorization'], 2)[0];
                    $this->getAuthService()->refresh();
                    $headers['Authorization'] = $type
                        . ' '
                        . $this->getUser()->getAccessToken();
                    return parent::httpPost($url, $parameters, $headers);
                } else {
                    throw $ex;
                }
            }
        }
    }
}
