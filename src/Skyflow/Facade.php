<?php

/**
 * Abstract facade for use by the Skyflow addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow;

use skyflow\FacadeInterface;
use skyflow\Service\ServiceInterface;

/**
 * Abstract facade for use by the Skyflow addons.
 *
 * An addon facade eposes addon services and utility methods.
 */
class Facade implements FacadeInterface
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
    public function __construct($services = null)
    {
        $this->services = $services;
    }

    /**
     * Call magic method.
     *
     * Allow to get a service using the syntax "get" concatenated with the name
     * of the service.
     *
     * Example $this->getSalesforce() returns the facade named "Salesforce".
     * The facade name first letter must be uppercase or this will not work.
     *
     * Else if the method requested does not start with "get", delegate the call
     * to the services.
     *
     * @param  string $name      The name of the method to call.
     * @param  array  $arguments An array of the method arguments.
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) === 'get') {
            $serviceName = lcfirst(substr($name, 3, strlen($name)));

            if (isset($this->services[$serviceName])) {
                return $this->services[$serviceName];
            }
        } else {
            foreach ($this->getServices() as $service) {
                if (method_exists($service, $name)) {
                    return call_user_func_array(array($service, $name), $arguments);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addService($name, ServiceInterface $service)
    {
        $this->services[$name] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getService($name)
    {
        return $this->services[$name];
    }
}
