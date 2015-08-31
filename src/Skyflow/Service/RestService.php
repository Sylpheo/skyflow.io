<?php

/**
 * REST service class for Skyflow services.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use skyflow\Service\AbstractService;
use skyflow\Service\RestServiceInterface;

/**
 * REST service class for Skyflow services.
 */
class RestService extends AbstractService implements RestServiceInterface
{
    /**
     * HTTP Client.
     *
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * RestService constructor.
     *
     * @param HttpClientInterface $httpClient The HTTP client.
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get the HttpClient.
     *
     * @return HttClientInterface The HTTP client.
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }
    
    /**
     * {@inheritdoc}
     */
    public function httpGet($url, $parameters, $headers = null)
    {
        $requestUrl = $this->getEndpoint();
        $requestUrl .= '/';
        $requestUrl .= empty($this->getVersion()) ? "" : $this->getVersion() . "/";
        $requestUrl .= ltrim($url, '/');

        if (is_array($parameters) && !empty($parameters)) {
            $requestUrl .= "?";

            foreach ($parameters as $name => $value) {
                $requestUrl .= $name . '=' . urlencode($value) . '&';
            }

            $requestUrl = rtrim($requestUrl, '&');
        }

        $request = $this->getHttpClient()->createRequest('GET', $requestUrl);

        if (is_array($headers)) {
            $request->setHeaders($headers);
        }

        return $this->getHttpClient()->send($request);
    }

    /**
     * {@inheritdoc}
     * @todo Implement this method.
     */
    public function httpPost($url, $parameters, $headers = null)
    {
        
    }
}
