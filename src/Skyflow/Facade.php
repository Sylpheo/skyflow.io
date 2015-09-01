<?php

/**
 * Abstract facade for use by the Skyflow addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow;

use Skyflow\Service\ServiceInterface;

/**
 * Abstract facade for use by the Skyflow addons.
 *
 * An addon facade eposes addon services and utility methods.
 */
class Facade
{
    /**
     * An array of the addon services exposed by the facade.
     *
     * @var ServiceInterface[]
     */
    private $services;

    /**
     * Facade constructor.
     *
     * @param ServiceInterface[] $services An array of the addon services exposed
     *                                     by the facade.
     */
    public function __construct($services)
    {
        $this->services = $services;
    }

    /**
     * Delegates calls to unknown facade methods to its registered services.
     *
     * @param  string $name      The name of the method to call.
     * @param  array  $arguments An array of the method arguments.
     */
    public function __call($name, $arguments)
    {
        foreach ($services as $service) {
            if (method_exists($service, $name)) {
                return call_user_func_array(array($service, $name), $arguments);
            }
        }
    }

    /**
     * Add a service to the facade.
     *
     * @param string           $name    The name of the service.
     * @param ServiceInterface $service The service.
     */
    public function addService($name, ServiceInterface $service)
    {
        $this->services[$name] = $service;
    }

    /**
     * Get a service from the facade.
     *
     * @param string $name The name of the service to get.
     */
    public function getService($name)
    {
        return $this->services[$name];
    }
}
