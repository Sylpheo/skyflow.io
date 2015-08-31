<?php

/**
 * Abstract service class for the Skyflow addon services.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

use skyflow\Service\ServiceInterface;

/**
 * Abstract service class for the Skyflow addon services.
 *
 * This class is abstract because it has no service methods. Child classes must
 * define service methods.
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * The service endpoint.
     *
     * @var string
     */
    private $endpoint;

    /**
     * The service version.
     *
     * @var string
     */
    private $version;

    /**
     * Set the service endpoint.
     *
     * @param string $endpoint The service endpoint.
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = rtrim($endpoint, '/');
    }

    /**
     * Get the service endpoint.
     *
     * @return string The service endpoint.
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Set the service version.
     *
     * @param string $version The service version.
     */
    public function setVersion($version)
    {
        $this->version = ltrim(rtrim($version, '/'), '/');
    }

    /**
     * Get the service version.
     *
     * @return string The service version.
     */
    public function getVersion()
    {
        return $this->version;
    }
}
