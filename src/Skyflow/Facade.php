<?php

/**
 * Abstract facade for use by the Skyflow addons.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow;

use Skyflow\FacadeInterface;
use Skyflow\Service\ServiceInterface;

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
     * Example $this->getSalesforce() returns the service named "salesforce".
     * $this->getSObjects() returns the service names "sobjects" (all lowercase).
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
            $serviceName = strtolower(substr($name, 3, strlen($name)));

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
     * Get a service using dotted notation.
     *
     * For example $this->get('salesforce.data.sobjects');
     *
     * @param string $path The path to the service.
     * @return ServiceInterface The requested service.
     */
    public function get($path)
    {
        $services = explode('.', $path);

        $requested = $this->getService($services[0]);

        for ($i = 1; $i < count($services); $i++) {
            $requested = $requested->getService($services[$i]);
        }

        return $requested;
    }

    /**
     * {@inheritdoc}
     */
    public function addService($name, ServiceInterface $service)
    {
        $this->services[strtolower($name)] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function getService($name)
    {
        return $this->services[$name];
    }
}
