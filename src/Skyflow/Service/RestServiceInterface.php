<?php

/**
 * Interface for a REST service.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

use skyflow\Service\ServiceInterface;

use GuzzleHttp\Message\ResponseInterface as HttpResponseInterface;

interface RestServiceInterface extends ServiceInterface
{
    /**
     * Get the service url.
     *
     * The service url is the concatenation of the endpoint and version.
     *
     * @return string The service url.
     */
    public function getServiceUrl();

    /**
     * Get the request url for the provided url.
     *
     * @param  string $url             The provided url to append to the service url.
     * @param  array  $queryParameters Only for HTTP GET. The GET query parameters.
     * @return string                  The request url.
     */
    public function getRequestUrl($url, $parameters = null);
    
    /**
     * Send a GET HTTP request to the endpoint/version/url with provided parameters.
     *
     * @param  string $url           The URL to append to endpoint/version.
     * @param  array  $parameters    The HTTP query parameters as array name => value.
     * @param  array  $headers       The HTTP headers as array name => value.
     * @return HttpResponseInterface The HTTP response.
     */
    public function httpGet($url, $parameters, $headers = null);

    /**
     * Send a POST HTTP request to the endpoint/version/url with provided parameters.
     *
     * @param  string $url           The URL to append to endpoint/version.
     * @param  array  $parameters    The HTTP JSON body parameters.
     * @param  array  $headers       The HTTP headers as array name => value.
     * @return HttpResponseInterface The HTTP response.
     */
    public function httpPost($url, $parameters, $headers = null);
}
