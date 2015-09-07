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
     * Authorize HTTP headers with OAuth.
     *
     * @param  array|null $headers The HTTP headers.
     * @param  boolean    $refresh Whether to force refresh of existing
     *                             access_token or not
     * @return Authorized headers.
     */
    protected function authorize($headers, $refresh = false)
    {
        if (!isset($headers)) {
            $headers = array();
        }

        if (!isset($headers['Authorization'])) {
            $headers['Authorization'] = 'Bearer ' . $this->getUser()->getAccessToken();
        } elseif ($refresh) {
            $type = explode(' ', $headers['Authorization'], 2)[0];
            $headers['Authorization'] = $type
                        . ' '
                        . $this->getUser()->getAccessToken();
        }

        return $headers;
    }

    /**
     * {@inheritdoc} HTTP request is automatically authorized with OAuth.
     * Access token is automatically refreshed if it has expired.
     */
    public function httpGet($url, $parameters = null, $headers = null)
    {
        try {
            $authorizedHeaders = $this->authorize($headers);

            return parent::httpGet($url, $parameters, $authorizedHeaders);
        } catch (\Exception $ex) {
            if ($ex->getCode() === 401) {
                $this->getAuthService()->refresh();
                $authorizedHeaders = $this->authorize($headers, true);

                return parent::httpGet($url, $parameters, $authorizedHeaders);
            } else {
                throw $ex;
            }
        }
    }

    /**
     * {@inheritdoc} HTTP request is automatically authorized with OAuth.
     * Access token is automatically refreshed if it has expired.
     */
    public function httpPost($url, $parameters = null, $headers = null)
    {
        try {
            $authorizedHeaders = $this->authorize($headers);

            return parent::httpPost($url, $parameters, $authorizedHeaders);
        } catch (\Exception $ex) {
            if ($ex->getCode() === 401) {
                $this->getAuthService()->refresh();
                $authorizedHeaders = $this->authorize($headers, true);

                return parent::httpPost($url, $parameters, $authorizedHeaders);
            } else {
                throw $ex;
            }
        }
    }
}
