<?php

/**
 * Interface for a Skyflow Flow.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Flow;

use Skyflow\FacadeInterface;

/**
 * Interface for a Skyflow Flow.
 *
 * Functionalities are exposed to the flow via Facades that are added via the
 * addFacade($name, $facade) method.
 */
interface FlowInterface
{
    /**
     * Add a facade to the flow.
     *
     * @param string          $name   The name of the facade.
     * @param FacadeInterface $facade The facade.
     */
    public function addFacade($name, FacadeInterface $facade);

    /**
     * Get a facade from the flow.
     *
     * @param string $name The name of the facade to get.
     * @return FacadeInterface The corresponding facade.
     */
    public function getFacade($name);

    /**
     * Flow execution when run via an event (HTTP POST request with JSON content).
     *
     * @param Symfony\Component\HttpFoundation\Request The JSON request handled
     *                                                 by the application.
     * @return mixed
     */
    public function event($requestJson);

    /**
     * Flow execution when run via heroku scheduler.
     */
    public function run();
}
