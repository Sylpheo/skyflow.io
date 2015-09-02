<?php

/**
 * Interface for Skyflow addons facades.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow;

use skyflow\Service\ServiceInterface;

/**
 * Interface for Skyflow addons facades.
 *
 * A facade exposes services to flows.
 */
interface FacadeInterface
{
    /**
     * Add a service to the facade.
     *
     * @param string           $name    The name of the service.
     * @param ServiceInterface $service The service.
     */
    public function addService($name, ServiceInterface $service);

    /**
     * Get a service from the facade.
     *
     * @param string $name The name of the service to get.
     * @return ServiceInterface The corresponding service.
     */
    public function getService($name);
}
