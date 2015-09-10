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
     * Send some HTTP request.
     *
     * This is here to avoid code duplication.
     *
     * @param  string $method     The HTTP method.
     * @param  string $url        The URL to append to endpoint/version.
     * @param  array $parameters  The HTTP query parameters as array name => value.
     * @param  array $headers     The HTTP headers as array name => value.
     * @return HttpResponseInterface The HTTP response.
     */
    protected function httpRequest($method, $url, $parameters, $headers)
    {
        $method = ucfirst(strtolower($method));

        try {
            $authorizedHeaders = $this->authorize($headers);

            if ($method === 'Delete') {
                // No $parameters for DELETE
                return call_user_func_array(
                    array('parent', 'http' . $method),
                    array($url, $authorizedHeaders)
                );
            } else {
                return call_user_func_array(
                    array('parent', 'http' . $method),
                    array($url, $parameters, $authorizedHeaders)
                );
            }
        } catch (\Exception $ex) {
            if ($ex->getCode() === 401) {
                $this->getAuthService()->refresh();
                $authorizedHeaders = $this->authorize($headers, true);

                if ($method === 'Delete') {
                    // No $parameters for DELETE
                    return call_user_func_array(
                        array('parent', 'http' . $method),
                        array($url, $authorizedHeaders)
                    );
                } else {
                    return call_user_func_array(
                        array('parent', 'http' . $method),
                        array($url, $parameters, $authorizedHeaders)
                    );
                }
            } else {
                throw $ex;
            }
        }
    }

    /**
     * {@inheritdoc} HTTP request is automatically authorized with OAuth.
     * Access token is automatically refreshed if it has expired.
     */
    public function httpGet($url, $parameters = null, $headers = null)
    {
        return $this->httpRequest('GET', $url, $parameters, $headers);
    }

    /**
     * {@inheritdoc} HTTP request is automatically authorized with OAuth.
     * Access token is automatically refreshed if it has expired.
     */
    public function httpPost($url, $parameters = null, $headers = null)
    {
        return $this->httpRequest('POST', $url, $parameters, $headers);
    }

    /**
     * {@inheritdoc} HTTP request is automatically authorized with OAuth.
     * Access token is automatically refreshed if it has expired.
     */
    public function httpPatch($url, $parameters = null, $headers = null)
    {
        return $this->httpRequest('PATCH', $url, $parameters, $headers);
    }

    /**
     * {@inheritdoc} HTTP request is automatically authorized with OAuth.
     * Access token is automatically refreshed if it has expired.
     */
    public function httpDelete($url, $headers = null)
    {
        return $this->httpRequest('DELETE', $url, null, $headers);
    }
}
