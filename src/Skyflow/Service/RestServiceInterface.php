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
