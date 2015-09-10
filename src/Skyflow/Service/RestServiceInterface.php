<?php

/**
 * Interface for a REST service.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

use skyflow\Service\WebServiceInterface;

use GuzzleHttp\Message\ResponseInterface as HttpResponseInterface;

/**
 * Interface for a REST service.
 */
interface RestServiceInterface extends WebServiceInterface
{
    /**
     * Get the service url.
     *
     * The service url is the concatenation of the endpoint and version
     * (getEndpoint() and getVersion() are declared in ServiceInterface).
     *
     * @return string The service url.
     */
    public function getServiceUrl();

    /**
     * Get the request url for the provided url.
     *
     * @param  string $url        The provided url to append to the service url.
     * @param  array  $parameters Only for HTTP GET. The GET query parameters.
     * @return string             The request url.
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
    public function httpGet($url, $parameters = null, $headers = null);

    /**
     * Send a POST HTTP request to the endpoint/version/url with provided parameters.
     *
     * @param  string $url           The URL to append to endpoint/version.
     * @param  array  $parameters    The HTTP JSON body parameters.
     * @param  array  $headers       The HTTP headers as array name => value.
     * @return HttpResponseInterface The HTTP response.
     */
    public function httpPost($url, $parameters = null, $headers = null);

    /**
     * Send a PATCH HTTP request to the endpoint/version/url with provided parameters.
     *
     * @param  string $url           The URL to append to endpoint/version.
     * @param  array  $parameters    The HTTP JSON body parameters.
     * @param  array  $headers       The HTTP headers as array name => value.
     * @return HttpResponseInterface The HTTP response.
     */
    public function httpPatch($url, $parameters = null, $headers = null);

    /**
     * Send a DELETE HTTP request to the endpoint/version/url with provided parameters.
     *
     * @param  string $url           The URL to append to endpoint/version.
     * @param  array  $headers       The HTTP headers as array name => value.
     * @return HttpResponseInterface The HTTP response.
     */
    public function httpDelete($url, $headers = null);
}
