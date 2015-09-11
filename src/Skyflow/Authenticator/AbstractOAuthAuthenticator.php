<?php

/**
 * Abstract OAuth authenticator for use by the Skyflow addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Authenticator;

use Skyflow\Authenticator\OAuthAuthenticatorInterface;
use GuzzleHttp\ClientInterface as HttpClientInterface;

/**
 * Abstract OAuth authenticator for use by the Skyflow addons.
 *
 * The OAuth authenticator is responsible for handling OAuth handshakes using
 * an HTTPClient. It then stores the results (acess_token, refresh_token...) in
 * its own properties. The authenticator does not know about the OAuth user.
 * It's the responsibility of the OAuth service to store the credentials to the
 * OAuth user.
 */
abstract class AbstractOAuthAuthenticator implements OAuthAuthenticatorInterface
{
    /**
     * The HTTP client to be used by this authenticator.
     *
     * @var HttpClientInterface
     */
    private $httpClient;

    /**
     * Authenticator constructor.
     *
     * @param array $parameters An associative array of URL parameters/response
     *                          in snake_case format.
     *                          For example: "grant_type", "client_id"...
     */
    public function __construct($parameters = null)
    {
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $property = $this->denormalize($key);

                if (property_exists($this, $property)) {
                    $this->$property = $value;
                }
            }
        }
    }

    /**
     * Set the HTTP client to be used by this authenticator.
     *
     * @param HttpClientInterface $HttpClientInterface The HTTP client.
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get the HTTP client to be used by this authenticator.
     *
     * @return HttpClientInterface The HTTP client.
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function authenticate($exit);

    /**
     * {@inheritdoc}
     */
    abstract public function callback();

    /**
     * {@inheritdoc}
     */
    abstract public function refresh();

    /**
     * Normalize from camelCase to snake_case for the request.
     *
     * @param  string $str The string to normalize.
     * @return string      The normalized string from camelCase to snake_case.
     * @todo Refactor this. Duplicate of :
     *       Skyflow\DAO\AbstractDAO:normalize($str)
     */
    protected function normalize($str)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $str)), '_');
    }

    /**
     * Denormalize from snake_case to camelCase for storage in authenticator attributes.
     *
     * @param  string $str The string to denormalize.
     * @return string      The denormalized string from snake_case to camelCase.
     */
    protected function denormalize($str)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }

    /**
     * Check the presence of some parameters and throw an Exception with
     * provided values if there is one missing.
     *
     * @param  string[]   $parameters Array of parameters to check in camel_case format.
     * @throws \Exception If at least one of the parameters is missing.
     */
    protected function checkParameters($parameters)
    {
        foreach ($parameters as $parameter) {
            $property = $this->denormalize($parameter);

            if (empty($this->$property)) {
                $error = "Missing required URL parameter for OAuth." .
                    " Required parameters supplied are :";

                foreach ($parameters as $param) {
                    $prop = $this->denormalize($param);
                    $error .= ' ' . $param . ': ' . (
                        is_null($this->$prop) ? 'null' : $this->$prop
                    ) . ',';
                }

                $error = ltrim($error, ',');
                throw new \Exception($error);
            }
        }
    }
}
