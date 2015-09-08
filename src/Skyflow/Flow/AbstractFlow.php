<?php

/**
 * Flow abstract class.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Flow;

use skyflow\FacadeInterface;
use skyflow\Flow\FlowInterface;
use skyflow\Service\ServiceInterface;

/**
 * Flow abstract class.
 *
 * Extend this class to create a custom flow.
 * Put your custom flow in directory "src/Skyflow/Flow" and namespace it under
 * skyflow\Flow.
 *
 * Do __NOT__ declare getter methods on this class. The syntax "getSomething" is used
 * to get an addon facade by its name.
 */
abstract class AbstractFlow implements FlowInterface
{
    /**
     * An array of the addons facades exposed by the flow.
     *
     * @var FacadeInterface[]
     */
    private $facades;

    /**
     * Flow constructor.
     *
     * @param FacadeInterface[] $facades An array of the addons facades exposed
     *                                   by the flow.
     */
    public function __construct(array $facades = null)
    {
        $this->facades = $facades;
    }

    /**
     * Allow to get a facade using the syntax "get" concatenated with the name
     * of the facade.
     *
     * Example $this->getSalesforce() returns the facade named "Salesforce".
     * The facade name first letter must be uppercase or this will not work.
     *
     * @param  string $name      The name of the method to call.
     * @param  array  $arguments An array of the method arguments.
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) === 'get') {
            $facadeName = substr($name, 3, strlen($name));

            if (isset($this->facades[$facadeName])) {
                return $this->facades[$facadeName];
            }
        }
    }

    /**
     * Get a service using dotted notation.
     *
     * For example $this->get('salesforce.data.sobjects');
     *
     * @param  string $path                     The path to the service.
     * @return FacadeInterface|ServiceInterface The requested service.
     */
    public function get($path)
    {
        $services = explode('.', $path);

        $requested = $this->getFacade(ucfirst($services[0]));

        for ($i = 1; $i < count($services); $i++) {
            $requested = $requested->getService($services[$i]);
        }

        return $requested;
    }

    /**
     * {@inheritdoc}
     */
    public function addFacade($name, FacadeInterface $facade)
    {
        $this->facades[$name] = $facade;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacade($name)
    {
        return $this->facades[$name];
    }

    /**
     * {@inheritdoc}
     */
    abstract public function event($requestJson);

    /**
     * {@inheritdoc}
     */
    abstract public function run();
}
