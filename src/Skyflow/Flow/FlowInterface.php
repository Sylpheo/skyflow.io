<?php

/**
 * Interface for a Skyflow Flow.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Skyflow\Flow;

interface FlowInterface
{
    /**
     * Flow execution when run via an event (HTTP POST request with JSON content).
     *
     * @param Symfony\Component\HttpFoundation\Request The JSON request handled by the application.
     * @return mixed
     */
    public function event($requestJson);

    /**
     * Flow execution when run via heroku scheduler.
     */
    public function run();
}
