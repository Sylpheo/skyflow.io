<?php

/**
 * REST service class for Skyflow services.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Service;

use GuzzleHttp\ClientInterface as HttpClientInterface;

use Skyflow\Service\AbstractWebService;
use Skyflow\Service\RestServiceInterface;
use Skyflow\Service\ServiceInterface;

/**
 * REST service class for Skyflow services.
 */
class RestService extends AbstractWebService implements RestServiceInterface
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
     * @param ServiceInterface    $parentService The parent service.
     * @param array               $config        The optional service configuration:
     *                                           provider, endpoint, version, extension.
     * @param HttpClientInterface $httpClient    The HTTP client.
     */
    public function __construct(
        $parentService,
        $config,
        HttpClientInterface $httpClient
    ) {
        parent::__construct($parentService, $config);
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
    public function getServiceUrl()
    {
        return $this->getEndpoint()
            . (empty($this->getVersion()) ? "" : '/' . $this->getVersion());
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestUrl($url, $parameters = null)
    {
        $requestUrl = $this->getServiceUrl();
        $requestUrl .= $this->getExtension();

        if (!empty($url)) {
            $requestUrl = $requestUrl . '/' . ltrim($url, '/');
        }

        if (is_array($parameters) && !empty($parameters)) {
            $requestUrl .= "?";

            foreach ($parameters as $name => $value) {
                $requestUrl .= $name . '=' . urlencode($value) . '&';
            }

            $requestUrl = rtrim($requestUrl, '&');
        }

        return $requestUrl;
    }
    
    /**
     * {@inheritdoc}
     */
    public function httpGet($url, $parameters = null, $headers = null)
    {
        $requestUrl = $this->getRequestUrl($url, $parameters);

        $request = $this->getHttpClient()->createRequest(
            'GET',
            $this->getRequestUrl($url, $parameters)
        );

        if (is_array($headers)) {
            $request->setHeaders($headers);
        }

        return $this->getHttpClient()->send($request);
    }

    /**
     * {@inheritdoc}. The default content type is "application/json".
     */
    public function httpPost($url, $parameters = null, $headers = null)
    {
        $request = $this->getHttpClient()->createRequest(
            'POST',
            $this->getRequestUrl($url),
            is_array($parameters) ? array('json' => $parameters) : array('body' => $parameters)
        );

        if (is_array($headers)) {
            $request->setHeaders($headers);
        }

        $contentType = $request->getHeader('Content-Type');
        if ($contentType === null || $contentType === '') {
            $request->setHeader('Content-Type', 'application/json');
        }

        return $this->getHttpClient()->send($request);
    }

    /**
     * {@inheritdoc}. The default content type is "application/json".
     */
    public function httpPatch($url, $parameters = null, $headers = null)
    {
        $request = $this->getHttpClient()->createRequest(
            'PATCH',
            $this->getRequestUrl($url),
            is_array($parameters) ? array('json' => $parameters) : array('body' => $parameters)
        );

        if (is_array($headers)) {
            $request->setHeaders($headers);
        }

        $contentType = $request->getHeader('Content-Type');
        if ($contentType === null || $contentType === '') {
            $request->setHeader('Content-Type', 'application/json');
        }

        return $this->getHttpClient()->send($request);
    }

    /**
     * {@inheritdoc}
     */
    public function httpDelete($url, $headers = null)
    {
        $request = $this->getHttpClient()->createRequest(
            'DELETE',
            $this->getRequestUrl($url)
        );

        if (is_array($headers)) {
            $request->setHeaders($headers);
        }

        return $this->getHttpClient()->send($request);
    }
}
