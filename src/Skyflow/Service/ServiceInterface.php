<?php

/**
 * Service interface for the Skyflow addon services.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Service;

interface ServiceInterface
{
    /**
     * Set the service endpoint.
     *
     * @param string $endpoint The service endpoint.
     */
    public function setEndpoint($endpoint);

    /**
     * Get the service endpoint.
     *
     * @return string The service endpoint.
     */
    public function getEndpoint();
    
    /**
     * Set the service version.
     *
     * @param string $version The service version.
     */
    public function setVersion($version);

    /**
     * Get the service version.
     *
     * @return string The service version.
     */
    public function getVersion();
}
