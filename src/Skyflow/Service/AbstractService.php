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
 * define the service methods.
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * The service provider name.
     *
     * @var string
     */
    private $provider;

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
     * {@inheritdoc}
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get the name of the service provider.
     *
     * @return string The name of the service provider.
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = rtrim($endpoint, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = ltrim(rtrim($version, '/'), '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }
}
